<?php
/**
 * Template Name: Aplet koszyk
 *
 */

/*
* Koszyk nie korzysta wc_cart, jest customowy
* System przewiduje
*
*/
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_order.php' );
 ?>






<?php
	get_header();
?>




<? $activeOrder = new gaad_order( gaad_ajax::get_active_order() ); ?>

<script type="text/javascript">
	var order_items = <?php echo json_encode( $activeOrder->get_items() ); ?>;
	var order_totals = <?php echo json_encode( $activeOrder->get_totals() ); ?>;
	var order_payment = {};
	var cart_sett = <?php echo json_encode( 
		array( 
			'order_id' => gaad_ajax::get_active_order(),
			'disable_upload' => false,
			'disable_trash_item' => false,
			'disable_trash_item_file' => false,
			'currency_symbol' => 'zł',
			'disable_payment_panel' => true,
			'advanced_upload' => false,
			'submit' => array(
				'text' => 'Przejdź do płatności', 
			), 
		) ); ?>; 
</script>


<div class="container active-order">
	<div class="row">
		
		<div class="col-lg-12" style="position: unset; ">
			<h1>Twój koszyk</h1>
			
			<div id="wawa_cart"> </div>
			
		</div>
		
	</div>
</div>

	
		
	
<?php
	get_footer();
?>
