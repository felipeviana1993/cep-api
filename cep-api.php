<?php
/*
Plugin Name: La Vie Funcionalidades
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You shouldnt be here' );
}


// Incluir arquivos JS necessarios
add_action('wp_enqueue_scripts','api_integrate_init');


function api_integrate_init() {    
    //wp_enqueue_script( 'bootstrap-js', plugins_url() . '/api-integrate/js/bootstrap.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'mask', plugins_url() . '/cep-api/js/jquery.mask.min.js', array(), '1.0.0', true );
	wp_enqueue_script( 'functionalities', plugins_url() . '/cep-api/js/functionalities.js', array(), '1.0.0', true );
	wp_enqueue_script( 'alerts', plugins_url() . '/cep-api/js/bootstrap-notify.js', array(), '1.0.0', true );
    
}


// hide price for logged user
function your_login_function()
{
    if ( is_user_logged_in() == false ) {
        function wpdocs_register_plugin_styles() {
            wp_register_style( 'my-plugin', plugins_url( '/cep-api/css/lavie-func.css' ) );
            wp_enqueue_style( 'my-plugin' );
        }
        // Register style sheet.
        add_action( 'wp_enqueue_scripts', 'wpdocs_register_plugin_styles' );
    }   
}
add_action('init', 'your_login_function');


// Custom fields profile tab
add_action('um_after_account_general', 'showUMExtraFields', 100);

function showUMExtraFields() {
  $id = um_user('ID');
  $output = '';
  $names = array('nasc_user', 'cpf_user');

  $fields = array(); 
  foreach( $names as $name )
    $fields[ $name ] = UM()->builtin()->get_specific_field( $name );
  $fields = apply_filters('um_account_secure_fields', $fields, $id);
  foreach( $fields as $key => $data )
    $output .= UM()->fields()->edit_field( $key, $data );

    

  echo $output;
}

add_action('um_account_pre_update_profile', 'getUMFormData', 100);

function getUMFormData(){
  $id = um_user('ID');
  $names = array('nasc_user', 'cpf_user');

  foreach( $names as $name )
    update_user_meta( $id, $name, $_POST[$name] );
}


function apply_dicount($total){
	
	
}


//Verificar total mensal
require_once('functions/desc-month.php');

// Quantidade Minima no carrinho
require_once('functions/min-cart.php');


/*
// Verifica quantidade de pedidos nao finalizados
function orders_received(){

	$statuses = ['processing','pending'];
	
	$orders = wc_get_orders( array(
		'limit' => -1,
		'status' => $statuses,
		'customer_id' => get_current_user_id(),
	) );

	$total = 0;
	foreach ( $orders as $order){
		$total += $order->get_total();
	}

	return $total;
}

// Altera status do pedido
add_action( 'template_redirect', 'woo_alter_orders_status' );
function woo_alter_orders_status() {
	
	global $wp;
	$order_id = $wp->query_vars['order-received'];

	$total = orders_received();

	

	if ( is_checkout() && !empty( $order_id ) ) {
		//wp_redirect( 'http://localhost:8888/woocommerce/custom-thank-you/' );
		//exit;
	}
}

*/

// Tab Contrato no perfil
require_once('functions/contract-tab.php');

// Custom field users
require_once('functions/user-fields.php');