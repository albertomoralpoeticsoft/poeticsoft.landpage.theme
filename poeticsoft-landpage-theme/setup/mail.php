<?php

add_action(
  'phpmailer_init', 
  function($phpmailer) {

    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.ionos.es';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 465;
    $phpmailer->Username = 'info@agustinamate.com';
    $phpmailer->Password = 'Agustin150668&';
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->From = 'info@agustinamate.com';
    $phpmailer->FromName = 'AVE Audit™';    
    $phpmailer->isHTML(true);
  }
);

add_action(
  'wp_mail_failed',
  function ($wp_error) {

    theme_log('wp_mail_failed');
    theme_log(json_encode($wp_error));
  } ,
  10, 
  1 
);