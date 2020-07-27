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

$limiteDispo = '';

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
    
    if($limiteTotal > 0){
        $limiteDispo += $limiteTotal - $compras;
    } else {
        $limiteDispo += 0;
    }


	
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
				<input id="limituser" type="number" disabled="disabled" value="" />
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
        if($pedidos){
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
        } else {
            echo "<tr><td colspan='3'>Não há nenhum pedido para este usuário.</td></tr>";
        }
		echo "<table>";

	?>
	<br><hr>
	<h3>Entradas de pagemento</h3>
	<a class="button" id="entrada">Adicionar Entrada</a> 

	<p id="demo" style="display: none;"><img src="https://laviegourmetemcasa.com.br/wp-content/themes/vegan/images/loading.gif"></p> 
    <br><br>
    <table border='0' cellspacing='0' cellpadding='0' class='last-orders'>
        <tr>
            <th>Data</th>
            <th>Valor</th>
        </tr>

        <?php
            global $wpdb;
            $totalEntradas = 0;
            $results = $wpdb->get_results( "SELECT * FROM lgc_history WHERE id_user = {$_GET['user_id']} ORDER BY data_entrada DESC", OBJECT );

            if($results){
                    
                    foreach ( $results as $result){

                        list($date,$hour) = explode(" ", $result->data_entrada);
					    $arr = explode('-', $date);
                        $newDate = $arr[2].'/'.$arr[1].'/'.$arr[0];
                        
                        echo "<tr>";
                        echo "<td>{$newDate} {$hour}</td>";
                        echo "<td>{$result->valor_entrada}</td>";
                        echo "</tr>";

                        $totalEntradas += $result->valor_entrada;
                    }
                
            } else {
                echo "<tr><td colspan='2'>Não há nenhuma entradas para este usuário.</td></tr>";
            }

            $geral = $limiteDispo + $totalEntradas;
           
            
        ?>

        

    </table>
    
    <script> 
    jQuery(document).ready(function($) {

        $('#limituser').val("<?php echo $geral;?>");
        
        $('#entrada').click(function() {
            myFunction();
            event.preventDefault();
        });

        function myFunction() { 
			var x; 
            var valor = prompt("Valor da entrada:"); 

            var user_id = <?php echo $_GET['user_id']; ?>;
            
            if (valor!=null) { 
                //x="valor: " + valor + " anos."; 
                //document.getElementById("demo").innerHTML=x;

                $.ajax({
                    url: "/wp-content/plugins/cep-api/entrada.php",
                    type: 'post',
                    data: {
                        id_user: user_id,
                        entrada: valor,
                    },
                    beforeSend: function() {
                        $("#demo").show();
                        //alert("Foi");
                    }
                })
                
                .done(function(msg) {
                    msg = $.parseJSON(msg);
                    $("#demo").hide();
                    //alert(msg);
                    console.log(msg);

                    if (msg == 'sucesso') {
                        console.log(msg);
                        alert('Entrada criada com sucesso!')
                        location.reload();
                    } else {                        
                        console.log(msg['error']);
                        alert('[01]Ocorreu um erro:' + msg);
                    }


                })
                .fail(function(jqXHR, textStatus, msg) {
                    $("#demo").hide();
                    console.log(msg['error']);
                        alert('[02]Ocorreu um erro:' + msg);
                });
                
            } 
        }
    });
	</script>
    <br><br><hr>
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
