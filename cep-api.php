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
add_action('init', 'mes', 10);
function mes(){
	
	add_action( 'woocommerce_cart_calculate_fees','shipping_method_discount', 20, 1 );
	function shipping_method_discount( $cart_object ) {
  
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
  
		// HERE Define your targeted shipping method ID
		$payment_method = 'cod';


		$firstDayUTS = mktime (0, 0, 0, date("m"), 1, date("Y"));
		$lastDayUTS = mktime (0, 0, 0, date("m"), date('t'), date("Y"));

		$firstDay = date("Y-m-d", $firstDayUTS);
		$lastDay = date("Y-m-d", $lastDayUTS);


		$orders = wc_get_orders( array(
			'limit' => -1,
			'customer_id' => get_current_user_id(),
			'date_paid' => $firstDay .'...' . $lastDay,
		) );

		$total = 0;
		foreach ( $orders as $order){
			$total += $order->get_total();
		}
  
		// The percent to apply
		if($total <= 1800){
			$percent = 15;
		} elseif ( ($total > 1800) && ($total <= 5000) ){
			$percent = 20;
		} elseif($total > 5000){
			$percent = 25;
		}

		//echo $total;
		  
		$cart_total = $cart_object->subtotal_ex_tax;
		$chosen_payment_method = WC()->session->get('chosen_payment_method');
  
		if( $payment_method == $chosen_payment_method ){
			$label_text = __( "Desconto mensal" );
			// Calculation
			$discount = number_format(($cart_total / 100) * $percent, 2);
			// Add the discount
			$cart_object->add_fee( $label_text, -$discount, false );
		}
	}
  
	add_action( 'woocommerce_review_order_before_payment', 'refresh_payment_methods' );
	function refresh_payment_methods(){
		// jQuery code
		?>
		<script type="text/javascript">
			(function($){
				$( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
					$('body').trigger('update_checkout');
				});
			})(jQuery);
		</script>
		<?php
	}

}



// Quantidade Minima

add_action( 'woocommerce_check_cart_items', 'spyr_set_min_total' );
function spyr_set_min_total() {
    if( is_cart() || is_checkout() ) {
		
		global $woocommerce;	
		
 
        $minimum_cart_qty = 20;
		
		$qtd = WC()->cart->cart_contents_count;

         
        if( $qtd < $minimum_cart_qty ) {
			
			$saldo = $minimum_cart_qty - $qtd;
			$singular = '<br />Falta <strong>%s</strong> produto para atingir a quantidade mínima.</div>';
			$plural = '<br />Faltam <strong>%s</strong> produtos para atingir a quantidade mínima.</div>';
			if ( $saldo > 1 || $saldo < 1 ) {
				$msg = $plural;
			} else {
				$msg = $singular;
			}


			if ( $qtd != 0 ) {
					wc_add_notice( sprintf( '<div style="font-size: 15px;"><strong>O Pedido deve ter uma quantidade mínima de %s produtos.</strong>'
						.'<br />A quantidade total do seu pedido agora é de <strong>%s</strong>.'
						.$msg,
						$minimum_cart_qty,
						$qtd,
						$saldo ),
					'error' );
			}
        } elseif( ($qtd % 10 ) > 0 ){

			$sobra = $qtd % 10;
			$add = 10 - $sobra;

			if( $sobra > 1 ){
				$msgsobra = 'produtos';
			} else {
				$msgsobra = 'produto';
			}

			if( $add > 1 ){
				$msgadd = 'produtos';
			} else {
				$msgadd = 'produto';
			}

			wc_add_notice( sprintf('Para garantirmos a qualidade das nossas entregas e o perfeito estado dos nosso produtos, trabalhamos com embalagens a partir de 10 produtos. Para concluir seu pedido, basta adicionar %s ' . $msgadd . ' ao seu carrinho ou remover %s %s.', $add ,$sobra, $msgsobra), 'error');
		}
    }
}