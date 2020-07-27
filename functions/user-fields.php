<?php 
/**
 * Back end registration
 */

add_action( 'user_new_form', 'crf_admin_registration_form' );
function crf_admin_registration_form( $operation ) {
	if ( 'add-new-user' !== $operation ) {
		// $operation may also be 'add-existing-user'
		return;
	}

	$year = ! empty( $_POST['year_of_birth'] ) ? intval( $_POST['year_of_birth'] ) : '';

	?>
	<h3><?php esc_html_e( 'Personal Information', 'crf' ); ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="year_of_birth"><?php esc_html_e( 'Year of birth', 'crf' ); ?></label> <span class="description"><?php esc_html_e( '(required)', 'crf' ); ?></span></th>
			<td>
				<input type="number"
			       min="1900"
			       max="2017"
			       step="1"
			       id="year_of_birth"
			       name="year_of_birth"
			       value="<?php echo esc_attr( $year ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
	</table>
	
	<?php
}

add_action( 'user_profile_update_errors', 'crf_user_profile_update_errors', 10, 3 );
function crf_user_profile_update_errors( $errors, $update, $user ) {
	if ( empty( $_POST['year_of_birth'] ) ) {
		$errors->add( 'year_of_birth_error', __( '<strong>ERROR</strong>: Please enter your year of birth.', 'crf' ) );
	}

	if ( ! empty( $_POST['year_of_birth'] ) && intval( $_POST['year_of_birth'] ) < 0 ) {
		$errors->add( 'year_of_birth_error', __( '<strong>ERROR</strong>: You must be born after 1900.', 'crf' ) );
	}
}

add_action( 'edit_user_created_user', 'crf_user_register' );


/**
 * Back end display
 */

add_action( 'show_user_profile', 'crf_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'crf_show_extra_profile_fields' );

function crf_show_extra_profile_fields( $user ) {
	$limiteTotal = get_the_author_meta( 'year_of_birth', $user->ID );
	
	$orders = wc_get_orders( array(
		'limit' => -1,
		'customer_id' => $user->ID,
	) );

	$compras = 0;
	foreach ( $orders as $order){
		$compras += $order->get_total();
	}

	$limiteDispo = $limiteTotal - $compras;
	?>
	<h3><?php esc_html_e( 'Pagamentos', 'crf' ); ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="year_of_birth"><?php esc_html_e( 'Limite Geral', 'crf' ); ?></label></th>
			<td>
				<input type="number" id="year_of_birth" name="year_of_birth" value="<?php echo esc_attr( $limiteTotal ); ?>" class="regular-text" />
			</td>
		</tr>
		
		<tr>
			<th>Limite Disponível</th>
			<td>
				<input type="number" disabled="disabled" value="<?php echo $limiteDispo; ?>" />
			</td>
		</tr>
	</table>
	<hr>
	<h3>Últimos Pedidos</h3>
	<style>
		table.last-orders, .last-orders th, .last-orders td{
			border: 1px solid #666;
		}
		table.last-orders th, table.last-orders td{
			padding: 10px; /* Apply cell padding */
		}
	</style>
	<?php
		$pedidos = wc_get_orders( array(
			'limit' => 5,
			'customer_id' => $_GET['user_id'],
		) );

		function return_status($status){
			if($status == 'processing'){
				return "<span style='background: #3cc1ab; padding: 3px 5px; color: #FFFFFF;'>Pedido Recebido</span>";
			} elseif($status == 'completed'){
				return "<span style='background: #7acf58; padding: 3px 5px; color: #FFFFFF;'>Concluído</span>";
			} else {
				return $status;
			}
		}


		echo "<table border='0' cellspacing='0' cellpadding='0' class='last-orders'>
				<tr>
					<th>Data</th>
					<th>Status</th>
					<th>Total</th>
				</tr>
				";
			foreach ( $pedidos as $pedido){
				
				echo "<tr>";
					list($date,$hour) = explode(" ", $pedido->order_date);

					$arr = explode('-', $date);
					$newDate = $arr[2].'/'.$arr[1].'/'.$arr[0];

					echo "<td>" . $newDate.' '.$hour . "</td>";
					echo "<td>" . return_status($pedido->get_status()) . "</td>";
					echo "<td>" . $pedido->get_total() . "</td>";
				echo "</tr>";
			}
		echo "<table>";

	?>
	<br><hr>
	<p>Clique para exibir.</p> 
	<a class="btn" onclick="myFunction()">Clique aqui</a> 
	<p id="demo"></p> 
	<script> 
		function myFunction() { 
			var x; 
			var idade = prompt("Digite sua idade:"); 
				if (idade!=null) { 
					x="Idade: " + idade + " anos."; 
					document.getElementById("demo").innerHTML=x; 
				} 
		}
	</script>
	<?php
}

add_action( 'personal_options_update', 'crf_update_profile_fields' );
add_action( 'edit_user_profile_update', 'crf_update_profile_fields' );

function crf_update_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	if ( ! empty( $_POST['year_of_birth'] ) && ( $_POST['year_of_birth'] ) >= 0 ) {
		update_user_meta( $user_id, 'year_of_birth', ( $_POST['year_of_birth'] ) );
	}
}
