<?php 



add_filter( 'woocommerce_hidden_order_itemmeta', 'hide_order_item_meta_fields' );
 
function hide_order_item_meta_fields( $fields ) {  
  $fields[] = 'pa_max_files';
  $fields[] = 'ship_days';
  $fields[] = 'ship_date';
  
  
return $fields;
}








require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_order.php' );





function wawa_mime_types($mime_types){
   
    $mime_types['psd'] 	= 'image/vnd.adobe.photoshop'; //Adding photoshop files
    $mime_types['ai'] 	= 'application/postscript'; //Adding photoshop files
    $mime_types['cdr'] 	= 'application/cdr'; //Adding photoshop files
    $mime_types['pdf'] 	= 'application/pdf'; //Adding photoshop files
	
    return $mime_types;
}
add_filter('upload_mimes', 'wawa_mime_types', 1, 1);






/*
* Funkcja dopisuje konfigurację pola wyboru parametru wariacji z tablicy podanej jako ostatni argument
*/
function update_input_settings( $input_settings, $index, $data_array ){
	/*
	* Sprawdzanie czy index jest tablicą, tworzenie pustej jeżeli nie jest
	*/
	$input_settings[ $index ] = is_array( $input_settings[ $index ]  ) ? $input_settings[ $index ]  : array();
	// łączenie danych z tablicy pod indeksem z danymi dostarczonymi w argeumncie $data_array
	$input_settings[ $index ] = array_merge( $input_settings[ $index ], $data_array, 
                                            array( 'validation' => array( 
                                              'status' => true, 
                                              'errorMsg' => null      
                                            ), )	);		
	
	return $input_settings;
}






/**
* Funkcja dodaje filtr ustawiający tablicę input_settings zaiwerającą ustawienia poszczególnych pól formularza ustawiającego atrybuty wariacji produktu
* By filtr został dołączony musi posiadać odpowiednią nazwę: gaad_product_input_settings_filter__[calc_class]
* calc_class jest nazwą kalkulatora ustaioną w adminie
*
* @return void
*/
function gaad_set_product_input_settings (  ) {
	global $post;
	$calc_class = gaad_ajax::gaad_get_calc_class( $post->ID );	
	
	if(  class_exists( 'gaad_input_settings_filter' ) && method_exists( 'gaad_input_settings_filter', 'gaad_product_input_settings_filter__'.$calc_class ) ){
		add_filter( 'product_input_settings', 'gaad_input_settings_filter::gaad_product_input_settings_filter__'.$calc_class );
		return;
	}	
	
	if( function_exists( 'gaad_product_input_settings_filter__'.$calc_class ) ){
		add_filter( 'product_input_settings', 'gaad_product_input_settings_filter__'.$calc_class );
	}
	
}
add_action( 'wp', 'gaad_set_product_input_settings', 10 );


function gaad_check_payment(){


	$db = new gaad_db();
	$var_name = $db->payments['p24'][ 'return_var_name' ];
	$user_id = get_current_user_id();
	
	if( is_page( 'platnosci' ) && isset( $_GET[$var_name ] ) ){
		$res_data = explode( ',', $_GET[ $var_name ]);
		
		$pending_orders = gaad_ajax::get_pending_orders();
		
		if( is_array( $pending_orders ) && !empty( $pending_orders ) ){
			$max = count( $pending_orders );
			for( $i=0; $i<$max; $i++){
				$order_id = $p_order->ID;
				
				$p_order = $pending_orders[ $i ];
				$p24_session_id = get_post_meta( $order_id, 'p24_session_id', true);
				$p24_sign = get_post_meta( $order_id, 'p24_sign', true);
				
				if( $res_data[0] == $p24_session_id && $res_data[1] == $p24_sign ){
					$order = new gaad_order( $order_id ); 
					$p24_merchant_id = $db->payments['p24']['p24_merchant_id'];
					/*delete_user_meta( $user_id, 'active-order-id', $order_id );
					$status =  $order->update_status( 'processing' , 'order_note: set to processing');						
					break;
					*/
					$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
					$headers = array(
						'p24_session_id=' . $p24_session_id, 
						'p24_merchant_id=' . $p24_merchant_id,
						"p24_pos_id=" . $p24_merchant_id,
						"p24_amount=1"						 
					);
					
					
					
					
					$ch = curl_init();
					if(count($REQ)) {
						curl_setopt($ch, CURLOPT_POST,1);
						curl_setopt($ch, CURLOPT_POSTFIELDS,join("&",$headers));
					}

					curl_setopt($ch, CURLOPT_URL,'https://sandbox.przelewy24.pl/trnVerify');
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					
					//echo '<pre>'; echo var_dump(curl_exec ($ch)); echo '</pre>';
				}
				
				
				
			}
		}
		
		//echo '<pre>'; echo var_dump( $res_data, $user_id, $pending_orders ); echo '</pre>';
	}	

}
//add_action( 'wp_head', 'gaad_check_payment' );


/**
* 
*
* @return void
*/
function manage_pages_apps (  ) {
	if( is_page( 'koszyk' ) || is_page( 'aktywne-zamowienie' ) || is_page( 'platnosci' ) ){

		?><!--Szablony koszyk-->
		<script type="text/x-template" id="cart-total"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/cart-total-template.html'); ?></script>
		<script type="text/x-template" id="cart-body"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/cart-body-template.html'); ?></script>
		<script type="text/x-template" id="status-icon"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/status-icon.html'); ?></script>
		<script type="text/x-template" id="submit-status-icon"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/submit-status-icon.html'); ?></script>
		<script type="text/x-template" id="item-overlay"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/item-overlay.html'); ?></script>
		<script type="text/x-template" id="order-payment"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/order-payment.html'); ?></script>
		
		
		<script defer type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/cart_app.js' ?>"></script>
		<script defer type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/vendors/md5.js' ?>"></script><?php
		?><link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/cart_app.css' ?>">
		
	
		<?php
			
	}
}


/**
* 
*
* @return void
*/
function gaad_mod_admin_enqueue_styles (  ) {
	
	?><link rel="stylesheet" tyle="text/css"  href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"><?php
	?><link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/admin.css' ?>"><?php
}
add_action( 'admin_enqueue_scripts', 'gaad_mod_admin_enqueue_styles' );

/**
* 
*
* @return void
*/
function gaad_mod_enqueue_styles(  ) {
	?><link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/vendors/jquery-ui-1.12.1.custom/jquery-ui.min.css' ?>"><?php
	?><link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/product-form.css' ?>"><?php 
}

/**
* Dodaje skrypty gaad mod
*
* @return void
*/
function gaad_mod_enqueue (  ) {
	global $product; 
	$calc_class_name = gaad_ajax::gaad_get_calc_class( $product->id );
	
	?><script src="https://unpkg.com/vue"></script><?php 
	?><script defer type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/jStorage/jstorage.min.js' ?>"></script><?php
	
	?><script type="text/javascript" src="<?php echo get_template_directory_uri() . '/js/product_single_gaad_mod.js' ?>"></script><?php
	
		
		
	/*
	* Tylko strona produktu
	*/	
	if( is_single() ){
		?>
		
		<!--recaptcha-->
		<script src='https://www.google.com/recaptcha/api.js'></script>
		
		<!--eksperyment z wykresami-->
		<script type="text/x-template" id="price-charts-app"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/product-price-chart.html'); ?></script>
		<script type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.flot.js"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-price-chart-app.js' ?>"></script>
		
		
		 
		<script type="text/x-template" id="product-nav"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/product-nav.html'); ?></script>
		
		
		
		
		<script type="text/x-template" id="advertise_product-template"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/advertise-product.html'); ?></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/advertise-product-app.js' ?>"></script>
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/advertise-product.css">
		
		
		
		
		<script type="text/x-template" id="product-naklad"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/product-naklad.html'); ?></script>
		
		<script type="text/x-template" id="product-summary"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/product-summary.html'); ?></script>
		<script type="text/x-template" id="summary-price"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/summary-price.html'); ?></script>
		<script type="text/x-template" id="summary-shipment"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/summary-shipment.html'); ?></script>
		<script type="text/x-template" id="summary-footer"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/summary-footer.html'); ?></script>
		
		
		
		
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/product-crosssell.css">
		<script type="text/x-template" id="product-cross-sell-template"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/product-crosssell.html'); ?></script>
		
		<!--Wczytywanie formularza z parametrami zamawianego produktu-->
		<?php 
			$variations_form_template = is_readable( get_template_directory() . '/gaad-woo-mod/templates/variations-form-'. $calc_class_name .'.html' ) ? 
				get_template_directory_uri() . '/gaad-woo-mod/templates/variations-form-'. $calc_class_name .'.html' : 
				get_template_directory_uri() . '/gaad-woo-mod/templates/variations-form.html';
		?>		
		<script type="text/x-template" id="variations-form"><?php readfile( $variations_form_template ); ?></script>
		
		
		
		<script type="text/x-template" id="g-input"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/g-input-template.html'); ?></script>
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/g-input.css">
		<script type="text/x-template" id="custom-format-option"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/custom-format-option.html'); ?></script>		
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/custom-format-option.css">
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/product-single.css">
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/naklady-app.css">
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/product-summary-app.css">
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/product-description-app.css">
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/product-send-app.css">
		
		
		<script type="text/x-template" id="product-templates"><?php include(get_template_directory() . '/gaad-woo-mod/templates/product-templates.php'); ?></script>
		<script type="text/x-template" id="product-send-template"><?php include(get_template_directory() . '/gaad-woo-mod/templates/product-send.php'); ?></script>
		
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri(); ?>/gaad-woo-mod/css/product-templates.css">
		
		
		<script type="text/x-template" id="product-description"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/product-description.html'); ?></script>
		
		
		<script type="text/x-template" id="product-description"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/product-description.html'); ?></script>
		
		
		
		
		<script type="text/x-template" id="summary-attr-basic"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/summary-attr-basic.html'); ?></script>
		<script type="text/x-template" id="summary-attr-ksiazka"><?php readfile(get_template_directory_uri() . '/gaad-woo-mod/templates/summary-attr-ksiazka.html'); ?></script>
				
								
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/product-nav.css' ?>">
				
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-single.js' ?>"></script>
		
		
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/naklady-app.js' ?>"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-summary-app.js' ?>"></script>
		
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-nav-app.js' ?>"></script>
		
		
		<link rel="stylesheet" tyle="text/css"  href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/variations-form-app.css' ?>">
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/variations-form-app.js' ?>"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/variations-form-validate-app.js' ?>"></script>
		
		
		
		
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-templates-app.js' ?>"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-description-app.js' ?>"></script>
		
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-crosssell-app.js' ?>"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-send-app.js' ?>"></script>
		
		
		<script type="text/x-template" id="wawa_product_allegro"><?php include(get_template_directory() . '/gaad-woo-mod/templates/product-allegro.php'); ?></script>
		<script type="text/x-template" id="allegro-auction-item"><?php include(get_template_directory() . '/gaad-woo-mod/templates/allegro-auction-item.php'); ?></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product-allegro.js' ?>"></script>
		

		<script  defer type="text/javascript">
				calc_data.get = productSummaryApp.getVariation;
				naklady_app.q = calc_data.q;
				productSummaryApp.variation_id = calc_data.variation_id;				 
		</script>
		
		
		<?php
	}	

	

?><script type="text/javascript" src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/vendors/jquery-ui-1.12.1.custom/jquery-ui.js' ?>"></script><?php
	
		
	?><script defer
				  id="gformat-js" 
				  type="text/javascript" 
				  src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/gformat.js' ?>"></script>
	
				  
	<?php	
		
	
		
}


/**
* Dodaje skrypt do obsługi automatycznego nadawania cen wariantom produktu
*
* @return void
*/
function gaad_product_add_all_variations_prices (  ) {
	
	if( $_GET['post_type'] == 'product' ){		
		
		?><script defer
				  id="gaad_product_add_all_variations_prices" 
				  type="text/javascript" 
				  src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/product_variations_prices.js' ?>"></script><?php
		?><script defer
				  id="gformat-js" 
				  type="text/javascript" 
				  src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/gformat.js' ?>"></script><?php	
		
		
	}
	
	
	
}




//Dodadkowe statusy zamówien


/*
* wc-awaiting-files
*/

/** 
 * Register order new status awaiting files
 * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
**/
function register_awaiting_files_order_status() {
    register_post_status( 'wc-awaiting-files', array(
        'label'                     => 'Awaiting files',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Awaiting shipment <span class="count">(%s)</span>', 'Awaiting shipment <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'register_awaiting_files_order_status' );

// Add to list of WC Order statuses
function add_awaiting_files_to_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {

        $new_order_statuses[ $key ] = $status;

        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-awaiting-files'] = 'Awaiting files';
        }
    }

    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_awaiting_files_to_order_statuses' );






/*
* wc-files-error
*/

/** 
 * Register order new status awaiting files
 * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
**/
function register_files_error_order_status() {
    register_post_status( 'wc-files-error', array(
        'label'                     => 'Files Error',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Files error <span class="count">(%s)</span>', 'Files error <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'register_files_error_order_status' );

// Add to list of WC Order statuses
function add_files_error_to_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {

        $new_order_statuses[ $key ] = $status;

        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-files-error'] = 'Files Error';
        }
    }

    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_files_error_to_order_statuses' );




/*
* wc-files-ok
*/

/** 
 * Register order new status files ok
 * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
**/
function register_files_ok_order_status() {
    register_post_status( 'wc-files-ok', array(
        'label'                     => 'Files ok',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Files ok <span class="count">(%s)</span>', 'Files ok <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'register_files_ok_order_status' );

// Add to list of WC Order statuses
function add_files_ok_to_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {

        $new_order_statuses[ $key ] = $status;

        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-files-ok'] = 'Files ok';
        }
    }

    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_files_ok_to_order_statuses' );

?>