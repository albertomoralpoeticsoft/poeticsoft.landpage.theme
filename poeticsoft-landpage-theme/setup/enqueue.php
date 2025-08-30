<?php

add_action( 
	'admin_enqueue_scripts', 
	function () {

    $url = get_stylesheet_directory_uri();

    wp_enqueue_script(
      'poeticsoft-landpage-theme-admin', 
      $url . '/admin/main.js',
      [], 
      filemtime(get_stylesheet_directory() . '/admin/main.js'),
      true
    );

    wp_enqueue_style( 
      'poeticsoft-landpage-theme-admin',
      $url . '/admin/main.css', 
      [], 
      filemtime(get_stylesheet_directory() . '/admin/main.css'),
      'all' 
    );
	}, 
	15 
);

add_action( 
	'wp_enqueue_scripts', 
	function () {

    if(isset($_GET['app'])) { // ?app=local

      $url = 'http://localhost:8090'; 

    } else {

      $url = get_stylesheet_directory_uri();
    }

    wp_enqueue_script(
      'poeticsoft-landpage-theme-theme', 
      $url . '/theme/main.js',
      [
        
      ], 
      filemtime(get_stylesheet_directory() . '/theme/main.js'),
      true
    );

    wp_enqueue_style( 
      'poeticsoft-landpage-theme-theme',
      $url . '/theme/main.css', 
      [], 
      filemtime(get_stylesheet_directory() . '/theme/main.css'),
      'all' 
    );

    wp_enqueue_style( 
      'poeticsoft-landpage-theme-theme-mailchimp',
      '//cdn-images.mailchimp.com/embedcode/classic-061523.css',
      [], 
      null
    );
	}, 
	15 
);