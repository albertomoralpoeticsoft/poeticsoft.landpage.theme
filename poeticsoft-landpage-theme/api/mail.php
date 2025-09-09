<?php

function poeticsoft_landpage_mailmessage( WP_REST_Request $req ){
    
  $res = new WP_REST_Response();

  try {

    $templateslug = $req->get_param('dia');
    $name = $req->get_param('name');
    $test = $req->get_param('test');
    $mailtemplate = get_page_by_path(
      $templateslug, 
      OBJECT, 
      'mailtemplate' 
    );
    $subject = $mailtemplate->post_title;
    $content = $mailtemplate->post_content;
    $csscontentfile = get_stylesheet_directory() . '/api/main.css';
    $csscontentfileurl = get_stylesheet_directory_uri() . '/api/main.css';
    $csscontent = file_get_contents($csscontentfile);
    $message = str_replace('[Nombre]', $name, $content);
    $contenthtml = '<html>
      <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" 
              content="width=device-width, 
                      user-scalable=no, 
                      initial-scale=1, 
                      maximum-scale=5"
        > 
        <base href="' . site_url() . '">' .
          (
            $test == 'si' ?
            '<link rel="stylesheet" href="' . $csscontentfileurl . '?cache=' . strval(rand(1111, 9999)) . '" media="all" />'
            :
            '<style type="text/css">' .
              $csscontent . 
            '</style>'
          ) .
      '</head>
      <body>
        <div class="message">' 
          . $message . 
        '</div>' .
      '</body>
    </html>';

    $contentdom = new DOMDocument('1.0');
    $contentdom->substituteEntities = false;
    libxml_use_internal_errors(true);
    $contentdom->loadHTML($contenthtml);
    libxml_use_internal_errors(false);
    $contentdomx = new DOMXPath($contentdom);      

    $items = $contentdomx->query('//comment()');
    foreach ($items as $item) {
      $item->parentNode->removeChild($item);
    }

    $result = $contentdom->saveHTML(); 
    
    if($test == "si") {   

      $writehtml = file_put_contents(
        dirname(__FILE__) . '/mail.html',
        $result
      );

      $mailsent = wp_mail(
        'poeticsoft@gmail.com',
        $subject,
        $result
      );
    }

    $res->set_data([
      'subject' => $subject,
      'body' => $result,      
      // 'mailsent' => $mailsent
    ]);

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
        'methods'  => 'POST',
        'callback' => 'poeticsoft_landpage_mailmessage',
        'permission_callback' => '__return_true'
      )
    );
  }
);