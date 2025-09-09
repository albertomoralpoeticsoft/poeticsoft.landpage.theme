<?php

add_action(
  'admin_menu',
  function () {    

    add_menu_page(
      'Poeticsoft Landpage',
      'Poeticsoft',
      'manage_options',
      'poeticsoft-landpage',
      '__return_null',
      'dashicons-images-alt2',
      25
    );
  }
);

add_action(
  'init',
  function () {

    register_post_type(
      'mailtemplate',
      [
        'public' => true,
        'show_ui' => true, 
        'show_in_menu' => 'poeticsoft-landpage',
        'menu_position' => 3,
        'labels' => array(
          'name' => __('Mail templates'),
          'singular_name' => __('Mail template')
        ),
        'supports' => array(
          'title',
          'editor',
          'thumbnail',
          'revisions',
          'excerpt',
        ),
        'show_in_rest' => true
      ]
    ); 
  },
  30
);

add_filter(
  'allowed_block_types_all',
  function ($allowed_blocks, $context) {
    
    if(
      isset($context->post)
      && 
      $context->post->post_type == 'mailtemplate'
    ) {
      
      $allowed_blocks = array();

      $allowed_blocks[] = 'core/heading';
      $allowed_blocks[] = 'core/paragraph';
      $allowed_blocks[] = 'core/list';
      $allowed_blocks[] = 'core/table';
      $allowed_blocks[] = 'core/image';
      $allowed_blocks[] = 'core/separator';
      $allowed_blocks[] = 'core/spacer';
      $allowed_blocks[] = 'core/shortcode';
      $allowed_blocks[] = 'core/buttons';
    }
  
    return $allowed_blocks;
  },
  10,
  2
);


