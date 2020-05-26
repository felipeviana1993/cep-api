<?php
/*
Plugin Name: CEP API
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You shouldnt be here' );
}


// Incluir arquivos JS necessarios
add_action('wp_enqueue_scripts','api_integrate_init');

function api_integrate_init() {        
    wp_enqueue_script( 'mask', plugins_url() . '/cep-api/js/jquery.mask.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'functionalities', plugins_url() . '/cep-api/js/functionalities.js', array(), '1.0.0', true );
}