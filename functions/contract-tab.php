<?php

add_filter('um_account_page_default_tabs_hook', 'my_custom_tab_in_um', 100 );
function my_custom_tab_in_um( $tabs ) {
	$tabs[800]['mytab']['icon'] = 'um-faicon-pencil';
	$tabs[800]['mytab']['title'] = 'Contrato';
	$tabs[800]['mytab']['custom'] = true;
	return $tabs;
}
	
/* make our new tab hookable */

add_action('um_account_tab__mytab', 'um_account_tab__mytab');
function um_account_tab__mytab( $info ) {
	global $ultimatemember;
	extract( $info );

	$output = $ultimatemember->account->get_tab_output('mytab');
	if ( $output ) { echo $output; }
}

/* Finally we add some content in the tab */

add_filter('um_account_content_hook_mytab', 'um_account_content_hook_mytab');
function um_account_content_hook_mytab( $output ){
	ob_start();
	?>
		
	<div class="um-field">
		
		<?php echo do_shortcode('[elfsight_pdf_embed id="1"]'); ?>
	
	</div>		
		
	<?php
		
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;
}