<?php

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