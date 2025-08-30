<?php

// https://fullsiteediting.com/lessons/theme-json-layout-and-spacing-options/

add_filter('xmlrpc_enabled', '__return_false');
add_filter('login_display_language_dropdown', '__return_false');

add_action( 
  'after_setup_theme', 
  function () {       
    
    remove_action('wp_footer', 'the_block_template_skip_link');
  } 
);

add_action(
  'wp_head', 
  function() {

    echo '<meta 
      name="viewport" 
      content="width=device-width, 
                user-scalable=no, 
                initial-scale=1, 
                maximum-scale=5"
    >';
  },
  2
);