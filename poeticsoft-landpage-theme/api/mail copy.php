<?php

function poeticsoft_landpage_mailmessage( WP_REST_Request $req ){
    
  $res = new WP_REST_Response();

  try {

    $templateslug = $req->get_param('dia');

    $AWSUrl = get_option('playmotiv_cloud_settings_aws_url', '');
    $AWSToken = get_option('playmotiv_cloud_settings_aws_token', '');
    $callurl = $AWSUrl . '/admin/games/' . $awsgameid;
    $callargs = array(
      'headers' => array(
        'x-api-key' => $AWSToken
      )
    );
    $game = wp_remote_get(
      $callurl,
      $callargs
    );  

    if (is_wp_error($game)) {

      $errors = $game->get_error_messages();
      throw new Exception(join(' - ', $errors), 500);
    } 

    $gamedata = json_decode($game['body']);  

    if(property_exists($gamedata, 'message')) {

      throw new Exception('Error from AWS: ' . $gamedata->message, 500);
    }

    $gamedata = $gamedata->game;
    $contentid = $gamedata->wpId;
    $gametitle = $gamedata->title; 
    $storyambient = json_decode($gamedata->storyAmbients);
    $story = $storyambient[0];
    $ambient = $storyambient[1];
    $content = get_post($contentid);

    if(
      !$content || 
      $content->post_type != 'content' || 
      get_post_status($contentid) == 'trash'
    )
      throw new Exception('Error from WP: Game Definition Content (' . $contentid . ') not found', 404); 
  
    $messagetemplateid = $req->get_param('messagetemplateid');
    $messagetemplate = get_post($messagetemplateid);
    $messagetitle = $messagetemplate->post_title;
    $featuredimageurl = get_the_post_thumbnail_url($messagetemplateid, 'post-thumbnail');

    if(
      !$messagetemplate || 
      $messagetemplate->post_type != 'messagetemplate' ||
       get_post_status($messagetemplateid) == 'trash'
    )
      throw new Exception('Error from WP: Message Template not found', 404);

    
    $sitename = get_bloginfo('name');
    $custom_logo_id = get_theme_mod('custom_logo');
    $clientlogo = wp_get_attachment_image_src($custom_logo_id, array(240, 86))[0];
    $gameinsignia = get_the_post_thumbnail_url($contentid,'post-thumbnail');
    $subject = $messagetemplate->post_title;
    
    $subject = preg_replace('/[\n\r]/', '', $subject);
    $subject = str_replace('&nbsp;', ' ', $subject);     
    $subject = str_replace('{game.title}', $gametitle, $subject);

    $content = apply_filters( 'the_content', $messagetemplate->post_content);
    $content = preg_replace('/[\n\r]/', '', $content); 
    $content = str_replace('{game.title}', $gametitle, $content);  

    $content = str_replace('{game.thumbnail}', 
      '<img src="' . $gameinsignia . '" width="120px"/>',
      $content
    );
    

    $mailcssfilepath = ABSPATH . 'wp-content/plugins/playmotiv-cloud-player-v4/story/' . $story . '/ambient/' . $ambient . '/mailtest.css';

    $exists = file_exists($mailcssfilepath);    
  
    $csscode = file_get_contents($mailcssfilepath);
    $body = '<!DOCTYPE html>
    <html lang="es">
      <head>
        <meta charset="UTF-8">
        <title>MAIL</title>
        <meta name="viewport" 
              content="width=device-width, 
                       user-scalable=yes,
                       initial-scale=1, 
                       maximum-scale=5">
        <style type="text/css">' .
          $csscode .
        '</style>
      </head>
      <body>
        <table 
          class="Mail" 
          cellspacing="0" 
          cellpadding="0"
          align="center"
        >';

    if($featuredimageurl) {

      $body .= '<tr>
            <td 
              class="MailTitle"                
              height="0"
            >' . 
              $messagetitle .
            '</td>
          </tr>          
          <tr>
            <td class="FeaturedImage">
              <img 
                src="' . $featuredimageurl . '"
                width="100%"
                style="
                  width: 100%;
                  height: auto;
                  display: block;
                "
              />
            </td>
          </tr>';
    }

    $body .= '<tr>
            <td height="0">
              <table 
                class="Identity" 
                cellspacing="0" 
                cellpadding="8"
              >
                <tr>
                  <td class="IdentityLogoWrapper" height="0">
                    <img class="IdentityLogo"
                          width="120"
                          src="' . $clientlogo . '"/>
                  </td>
                  <td
                    class="IdentityName"
                    height="0"
                  >' .
                    $sitename .
                  '</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td height="0">
              <table 
                class="GameTitleInsignia" 
                cellspacing="0" 
                cellpadding="8"
                width="100%"
              >
                <td  
                  class="GameTitle"
                  height="0"
                >' .
                  $gametitle .
                '</td>
                <td 
                  class="Insignia"
                  height="0"
                  align="right"
                >
                  <img class="GameInsignia"
                       width="120"
                       src="' . $gameinsignia . '" />
                </td>
              </table>
            </td>
          </tr>
          <tr>
            <td class="Content">' .
              $content .
            '</td>
          </tr>
        </table>
      </body>
    </html>';

    $messagetemplatedata->subject = $subject . ' ' . $story . ' ' . $ambient . ' ' . ($exists ? 'SI' : 'NO');
    $messagetemplatedata->body = $body;

    $res->set_data($messagetemplatedata);

    $mailtest = $req->get_param('mailtest');

    if($mailtest) {     

      // core_log($mailtest); 

      add_action(
        'wp_mail_failed',
        function ($wp_error) {

          core_log($wp_error);

          error_log('wp_mail_failed');
          error_log(json_encode($wp_error));
        } ,
        10, 
        1 
      );

      add_filter(
        'wp_mail_content_type',
        function() {

          return "text/html";
        } 
      );

      $mailsent = wp_mail(
        $mailtest,
        $subject,
        $body
      );

      $writehtml = file_put_contents(
        dirname(__FILE__) . '/mail.html',
        $body
      );

      /* /wp-content/plugins/playmotiv-cloud-core-v4/api/messagetemplates/mail.html

      core_log('----------------------------------------'); 
      core_log($mailsent ? 'sent' : 'not sent'); 
      core_log($writehtml ? 'html ok' : 'html ko'); 
      */
    } 

  } catch (Exception $e) {
    
    $res->set_status($e->getCode());
    $res->set_data($e->getMessage());
  }

  return $res;
}

add_action(
  'rest_api_init',
  function () {

    register_rest_route(
      'poeticsoft/landpage',
      'mailmessage',
      array(
        'methods'  => 'GET',
        'callback' => 'poeticsoft_landpage_mailmessage',
        'permission_callback' => '__return_true'
      )
    );
  }
);