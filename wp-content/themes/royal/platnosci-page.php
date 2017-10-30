<?php
/**
 * Template Name: Aplet zamówienie, płatność
 *
 */

/*
* Koszyk nie korzysta wc_cart, jest customowy
* System przewiduje
*
*/
ob_start();
session_start();
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_order.php' );
$activeOrder = new gaad_order( get_user_meta( get_current_user_id(), 'active-order-id', true ) ); 

?>
 
 
 
	


<?php  /* //próby z paypalem
$pack = array(
	"actionType"=>"PAY",
	  "currencyCode"=>"PLN",
	  "receiverList"=>array(
		"receiver"=>array(
		  array(
			"amount"=>"0.02",
			"email"=>"wojtek@c-p.com.pl"
		  )
		)
	  ),
	  "returnUrl"=>"http://wawaprint.pl/u/zamowienie/platnosci/",
	  "cancelUrl"=>"http://wawaprint.pl/u/zamowienie/platnosci/",
	  "requestEnvelope"=>array(
		"errorLanguage"=>"pl_PL",
		"detailLevel"=>"ReturnAll"
	  )
);

$curl_headers = array(
	"X-PAYPAL-SECURITY-USERID: wojtek_api1.c-p.com.pl",
	"X-PAYPAL-SECURITY-PASSWORD: T93WRVSWM8VRLUNZ",
	"X-PAYPAL-SECURITY-SIGNATURE: AFcWxV21C7fd0v3bYYYRCpSSRl31ApZEn7EM0uPsH0IEDkXeVeDL39IQ",
	"X-PAYPAL-REQUEST-DATA-FORMAT: JSON",
	"X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
	"X-PAYPAL-APPLICATION-ID: APP-80W284485P519543T"
);

	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://svcs.sandbox.paypal.com/AdaptivePayments/Pay');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pack) );
	curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers );
	$res = json_decode( curl_exec( $ch ), true );
	$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey='.$res[ "payKey" ];
	echo '<pre>'; echo var_dump($url ); echo '</pre>';
	*/
?>



<?php
	get_header();
	$db = new gaad_db();
?>


<script type="text/javascript">
	var order_items = <?php echo json_encode( $activeOrder->get_items() ); ?>;
	var order_totals = <?php echo json_encode( $activeOrder->get_totals() ); ?>;
	var order_payment = <?php echo json_encode( $activeOrder->get_payment_status_data() ); ?>;
	var cart_sett = <?php echo json_encode( 
		array( 
			'order_id' => $activeOrder->post->ID,
			'disable_upload' => true,
			'disable_trash_item' => true,
			'disable_trash_item_file' => true,
			'currency_symbol' => 'zł',
			'verify_payment' => isset($_GET[$db->payments['p24']['p24_url_return_var']]) ? true : false, 
			'disable_payment_panel' => false,
			'submit' => array(
				'text' => 'Zapłać', 
				'action' => 'pay', 
			), 
		) ); ?>; 
</script>







<div class="container active-order">
	<div class="row">
		
		<div class="col-lg-12" style="position: unset; ">
			<h1>Twóje zamówienie (oczekiwanie na płatność)</h1>
			<div id="wawa_cart"></div>			
			
		</div>
		
	</div>
</div>
	
	
<?php
	get_footer();
?>
