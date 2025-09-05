<?php

add_action(
  'admin_init', 
  function () {
    
    if (defined('DOING_AJAX') && DOING_AJAX) {

      $accion = isset($_REQUEST['action']) ? sanitize_text_field($_REQUEST['action']) : '';
      if('form-block-submit' == $accion) { 
        
        $data = [
          'name' => $_REQUEST['nombre'],
          'email' => $_REQUEST['email'],
          'empresa' => $_REQUEST['empresa'],
          'status' => 'Nuevo'
        ];

        $body = json_encode($data);

        $response = wp_remote_post(
          'https://n8n.poeticsoft.com/webhook/ave-audit-lp-form', [
            'method'    => 'POST',
            'body'      => $body,
            'timeout'   => 20,
            'headers'   => [
              'Content-Type' => 'application/json',
            ],
        ]);

        $response = wp_remote_post(
          'https://n8n.poeticsoft.com/webhook-test/ave-audit-lp-form', [
            'method'    => 'POST',
            'body'      => $body,
            'timeout'   => 20,
            'headers'   => [
              'Content-Type' => 'application/json',
            ],
        ]);
      
        // theme_log($response);
      }
    }
  }
);