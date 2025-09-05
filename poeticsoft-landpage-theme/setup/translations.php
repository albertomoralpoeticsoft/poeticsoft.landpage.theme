<?php

add_filter( 
  'gettext', 
  function($translated_text, $text, $domain) {

    if ( $text === 'Required fields are marked with %s' ) {
      
      return 'Los campos requeridos están marcados con %s';
    }

    if ( $text === 'This email address is invalid.' ) {
      
      return 'Email incorrecto';
    }

    if ( $text === 'This field must not be empty.' ) {
      
      return 'El campo no puede estar vacío.';
    }

    if ( $text === 'This field must be checked.' ) {
      
      return 'Tienes que aceptar la política de privacidad.';
    }

    if ( $text === 'The form has been submitted successfully.' ) {
      
      return 'Hemos recibido tu solicitud, muchas gracias por tu interés.';
    }

    return $translated_text;

  }, 
  10, 
  3 
);