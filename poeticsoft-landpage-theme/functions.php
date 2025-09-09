<?php

function theme_log($display) { 

  $text = is_string($display) ? $display : json_encode($display, JSON_PRETTY_PRINT);

  file_put_contents(
    WP_CONTENT_DIR . '/theme_log.txt',
    $text . PHP_EOL,
    FILE_APPEND
  );
}

require_once(dirname(__FILE__) . '/setup/main.php');
require_once(dirname(__FILE__) . '/api/main.php');