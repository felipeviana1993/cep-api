<?php

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