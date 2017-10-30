<?php
/**
 * Template Name: Aplet zamówienie
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




<? $pendingOrders = gaad_ajax::get_pending_orders(); ?>



<?php 
	
	$cart_sett = array( 
		'order_id' => -1, 
		'disable_upload' => true,
		'disable_trash_item' => true,
		'disable_trash_item_file' => true,
		'currency_symbol' => 'zł',
		'submit' => array(
			'text' => 'Akceptuję zamówienie', 
		), 
	);
		
	$multiple_carts = array();
	$max = count( $pendingOrders );
	for( $i=0; $i<$max; $i++ ){
		$order_id = $pendingOrders[ $i ]->ID;		
		$order = new gaad_order( $order_id );	
		$this_cart_sett = array_merge( $cart_sett, array( 'order_id' => $order_id ) );
		$multiple_carts[ ] = array(
			'order_id' => $order_id, 
			'var_name' => 'gaad_cart_' . $order_id, 
			'cart_sett' => $this_cart_sett, 
			'order_items' => $order->get_items(), 
			'order_totals' => $order->get_totals()
		);
	}
	 
	//echo '<pre>'; echo var_dump($pendingOrders); echo '</pre>';
?>
<script type="text/javascript">
	window['multiple_carts'] = <?php echo json_encode( $multiple_carts ); ?>;
</script>






<div class="container active-order">
	<div class="row">
		
		<div class="col-lg-12" style="position: unset; ">
			<h1>Twóje zamówienie (oczekiwanie na płatność)</h1>
			
			<?php 
				$max = count( $multiple_carts );
				for( $i=0; $i<$max; $i++ ){					
					echo '<div id="'. $multiple_carts[ $i ][ 'var_name' ] .'"></div>';
				}
			?>
			
		</div>
		
	</div>
</div>
	
	
<?php
	get_footer();
?>
