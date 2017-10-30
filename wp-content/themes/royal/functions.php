<?php








session_start();
global $etheme_theme_data;
$etheme_theme_data = wp_get_theme( get_stylesheet_directory() . '/style.css' );
require_once( get_template_directory() . '/framework/init.php' );


/*
* GAAD WOO MOD
*/

//csv export
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_allegro_api.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/product_variations_csv.php' );


require_once( get_template_directory() . '/gaad-woo-mod/class/breadcrumbs.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_ajax.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_shortcodes.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_db.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_markup.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_calc.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_filters_options_labels.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_input_settings_filters.php' );

require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_accesories.php' );
require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_discount.php' );

require_once( get_template_directory() . '/gaad-woo-mod/vendors/class_przelewy24.php' );


require_once( get_template_directory() . '/gaad-woo-mod/filters.php' );
require_once( get_template_directory() . '/gaad-woo-mod/actions.php' );
require_once( get_template_directory() . '/gaad-woo-mod/functions.php' );




add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );
add_action( 'wp_enqueue_scripts', 'manage_pages_apps', 99 );



add_action( 'wp_ajax_nopriv_test123', 'wawa_allegro_actions::test123', 10, 0 );
add_action( 'wp_ajax_test123', 'wawa_allegro_actions::test123', 10, 0 );

add_action( 'wp_ajax_nopriv_addAllegroAuction', 'wawa_allegro_actions::addAllegroAuction', 10, 0 );
add_action( 'wp_ajax_addAllegroAuction', 'wawa_allegro_actions::addAllegroAuction', 10, 0 );





/*
* przejęcia kontroli nad wczytywanymi skryptami woo
*
*/
function child_manage_woocommerce_styles() {
	
	 global $wp_scripts;

	//first check that woo exists to prevent fatal errors
	if ( function_exists( 'is_woocommerce' ) ) {
		
		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';	
		$template_assets_path = str_replace( array( 'http:', 'https:' ), '', get_template_directory_uri() ) . '/woocommerce/assets/';
		$frontend_script_path = $template_assets_path . 'js/frontend/';
		$wp_scripts->registered["wc-add-to-cart-variation"]->src = $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js';
		
		
		
	}

}





/*
* Taxonomia słuzy do przypisania konkretnej klasy kalkulatora, 
* Brak przypisania do taksonomii spowoduje użycie pierwszej nadanej kategorii produktu w taki sam sposób
* Jeżeli klasa kalkulatora nie istnieje, użyta zostanie standardowa funkcja gaad_calc::calculate_variant_price
*/
function gaad_calc_taxonomy_init() {
	// create a new taxonomy
	register_taxonomy(
		'calc_class',
		'product',
		array(
			'label' => __( 'Kalkulator' ),
			'rewrite' => array( 'slug' => 'gaad_calc' ),
			
		)
	);
}
add_action( 'init', 'gaad_calc_taxonomy_init' );

/*
* Taxonomia słuzy do przypisania etykiety marży, 
* Klasa gaad_markup przechowuje marże dla poszczególnych typów kalkulatorów oraz dodadkowe tablice z marżami dla konkretnych typów produktów
* Pozwala to na ustawianie jednego kalkulatora dla wielu produktów oraz kontrolę ich ceny na poziomie każdego z nich zamiast na poziomie kalkulatora
* 
*/
function gaad_markup_tag_taxonomy_init() {
	// create a new taxonomy
	register_taxonomy(
		'markup_tag',
		'product',
		array(
			'label' => __( 'Marża' ),
			'rewrite' => array( 'slug' => 'gaad_markup_tag' ),
			
		)
	);
}
add_action( 'init', 'gaad_markup_tag_taxonomy_init' );







add_action( 'wp_footer', 'gaad_mod_enqueue', 10, 0 );
add_action( 'wp_head', 'gaad_mod_enqueue_styles', 10, 0 );

add_action( 'admin_enqueue_scripts', 'gaad_product_add_all_variations_prices', 10, 0 );



/**
* Zastępuje standardowy formularz wyboru parametrów dla artykułów: ksiazki, katalogi, broszury
*
* @return void
*/
function gaad_variation_form_ksiazki (  ) {
	
	$gaad_variation_form_ksiazki = locate_template( 'gaad-woo-mod/templates/gaad-gaad_variation_form_ksiazki.php' );
	
	include_once( $gaad_variation_form_ksiazki );	
	
}

/**
* Zastępuje standardowy formularz wyboru parametrów dla artykułów: outdoor duży format
*
* @return void
*/
function gaad_woocommerce_variations_form_outdoor_baner (  ) {	
	include_once( locate_template( 'gaad-woo-mod/templates/gaad-gaad_variation_form_outdoor_baner.php' ) );	
}

/**
* Ręczne wykrywanie czy dany produkt jest książka
* produkty: książki, katalogi
*
* @return void
*/
function is_ksiazka (  ) {
	global $post;
	$product_calc_class_terms = wp_get_object_terms( $post->ID,  'calc_class' );

	if( is_array( $product_calc_class_terms ) && !empty( $product_calc_class_terms )  ){	
		if( $product_calc_class_terms[0]->slug == 'ksiazka' ){
			return true;
		}	
	}

	
	return false;
}

add_action( 'gaad_woocommerce_variations_form', 'gaad_variation_form_ksiazki', 10 );

add_action( 'gaad_woocommerce_variations_form_outdoor_baner', 'gaad_woocommerce_variations_form_outdoor_baner', 10 );




/**
* Ręczne wykrywanie czy dany produkt jest książka
* produkty: plakaty xxl, produkty z druku solventowego
*
* @return void
*/
function is_outdoor_baner (  ) {
	global $post;
	$product_calc_class_terms = wp_get_object_terms( $post->ID,  'calc_class' );

	if( is_array( $product_calc_class_terms ) && !empty( $product_calc_class_terms )  ){	
		
		
		
		if( $product_calc_class_terms[0]->slug == 'outdoor_baner' ){
			return true;
		}	
	}
	
	return false;
}








/*
* AJAX action: gaad_recaptcha
*/
add_action( 'wp_ajax_gaad_send_product_pdf', 'gaad_ajax::gaad_send_product_pdf' );
add_action( 'wp_ajax_nopriv_gaad_send_product_pdf', 'gaad_ajax::gaad_send_product_pdf' ); 

/*
* AJAX action: gaad_recaptcha
*/
add_action( 'wp_ajax_gaad_recaptcha', 'gaad_ajax::gaad_recaptcha' );
add_action( 'wp_ajax_nopriv_gaad_recaptcha', 'gaad_ajax::gaad_recaptcha' ); 


/*
* AJAX action: Wysyłanie oferty na produkt o ustawionych parametrach
*/
add_action( 'wp_ajax_product_variation_pdf', 'gaad_ajax::product_variation_pdf' );
add_action( 'wp_ajax_nopriv_product_variation_pdf', 'gaad_ajax::product_variation_pdf' ); 



/*
* AJAX action: Wysyłanie reklamy produktu
*/
add_action( 'wp_ajax_gaad_send_product_adrvertisment', 'gaad_ajax::gaad_send_product_adrvertisment' );
add_action( 'wp_ajax_nopriv_gaad_send_product_adrvertisment', 'gaad_ajax::gaad_send_product_adrvertisment' ); 


/*
* AJAX action: Ustawianie statusu pliku
*/
add_action( 'wp_ajax_gaad_verify_payment', 'gaad_ajax::gaad_verify_payment' );
add_action( 'wp_ajax_nopriv_gaad_verify_payment', 'gaad_ajax::gaad_verify_payment' ); 


/*
* AJAX action: Ustawianie statusu pliku
*/
add_action( 'wp_ajax_gaad_process_payment', 'gaad_ajax::gaad_process_payment' );
add_action( 'wp_ajax_nopriv_gaad_process_payment', 'gaad_ajax::gaad_process_payment' ); 



/*
* AJAX action: Ustawianie statusu pliku
*/
add_action( 'wp_ajax_gaad_get_totals', 'gaad_ajax::gaad_get_totals' );
add_action( 'wp_ajax_nopriv_gaad_get_totals', 'gaad_ajax::gaad_get_totals' ); 


/*

/*
* AJAX action: Ustawianie statusu pliku
*/
add_action( 'wp_ajax_gaad_set_order_status', 'gaad_ajax::gaad_set_order_status' );
add_action( 'wp_ajax_nopriv_gaad_set_order_status', 'gaad_ajax::gaad_set_order_status' ); 


/*
* AJAX action: Ustawianie statusu pliku
*/
add_action( 'wp_ajax_gaad_delete_attachment_file', 'gaad_ajax::gaad_delete_attachment_file' );
add_action( 'wp_ajax_nopriv_gaad_delete_attachment_file', 'gaad_ajax::gaad_delete_attachment_file' ); 



/*
* AJAX action: Ustawianie statusu pliku
*/
add_action( 'wp_ajax_gaad_update_item_status', 'gaad_ajax::gaad_update_item_status' );
add_action( 'wp_ajax_nopriv_gaad_update_item_status', 'gaad_ajax::gaad_update_item_status' ); 



/*
* AJAX action: Usuwanie produktu z zamowienia
*/
add_action( 'wp_ajax_gaad_update_attachment_meta', 'gaad_ajax::gaad_update_attachment_meta' );
add_action( 'wp_ajax_nopriv_gaad_update_attachment_meta', 'gaad_ajax::gaad_update_attachment_meta' ); 



/*
* AJAX action: Usuwanie produktu z zamowienia
*/
add_action( 'wp_ajax_gaad_check_file', 'gaad_ajax::gaad_check_file' );
add_action( 'wp_ajax_nopriv_gaad_check_file', 'gaad_ajax::gaad_check_file' ); 



/*
* AJAX action: Usuwanie produktu z zamowienia
*/
add_action( 'wp_ajax_gaad_remove_item_from_order', 'gaad_ajax::gaad_remove_item_from_order' );
add_action( 'wp_ajax_nopriv_gaad_remove_item_from_order', 'gaad_ajax::gaad_remove_item_from_order' );



/*
* AJAX action: Dodawanie zamóweinia
*/
add_action( 'wp_ajax_gaad_get_variation', 'gaad_ajax::gaad_get_variation' );
add_action( 'wp_ajax_nopriv_gaad_get_variation', 'gaad_ajax::gaad_get_variation' );



/*
* AJAX action: Gadowskie pobieranie wariacji z dodadkowymi obliczeniami
*/
add_action( 'wp_ajax_gaad_get_variation', 'gaad_ajax::gaad_get_variation' );
add_action( 'wp_ajax_nopriv_gaad_get_variation', 'gaad_ajax::gaad_get_variation' );

/*
* AJAX action: Gadowskie pobieranie wariacji z dodadkowymi obliczeniami
*/
add_action( 'wp_ajax_gaad_calculate_', 'gaad_ajax::gaad_calculate_' );
add_action( 'wp_ajax_nopriv_gaad_calculate_', 'gaad_ajax::gaad_calculate_' );


/*
* AJAX action: Obliczenia kosztów produkcji produktu ksiazka
*/
add_action( 'wp_ajax_gaad_calculate_ksiazka', 'gaad_ajax::gaad_calculate_ksiazka' );
add_action( 'wp_ajax_nopriv_gaad_calculate_ksiazka', 'gaad_ajax::gaad_calculate_ksiazka' );

/*
* AJAX action: Obliczenia kosztów produkcji produktu baner
*/
add_action( 'wp_ajax_gaad_create_order', 'gaad_ajax::gaad_create_order' );
add_action( 'wp_ajax_nopriv_gaad_create_order', 'gaad_ajax::gaad_create_order' );



/*
* Zmiany w adminie
*/
if( is_admin() ){
	

	/*
	Dołączanie elemtów UI admina
	*/
	//
	require_once( get_template_directory() . '/gaad-woo-mod/admin/admin-templates-ui.php' );
	
	
	/*
	* Pobieranie UI służącego do dodawania cen wszystkim wariantom produktu z poziomu listy produktów
	*/
	add_action( 'wp_ajax_gaad_add_variations_prices_UI', 'gaad_ajax::gaad_add_variations_prices_UI' );
	add_action( 'wp_ajax_nopriv_gaad_add_variations_prices_UI', 'gaad_ajax::gaad_add_variations_prices_UI' );
	
	 
	/*
	* Pobieranie postawowych danych o wariantach produktu
	*/
	add_action( 'wp_ajax_gaad_get_product_variations', 'gaad_ajax::gaad_get_product_variations' );
	add_action( 'wp_ajax_nopriv_gaad_get_product_variations', 'gaad_ajax::gaad_get_product_variations' );
	
	/*
	* Wycena wariantu
	*/
	add_action( 'wp_ajax_gaad_variation_calculate', 'gaad_ajax::gaad_variation_calculate' );
	add_action( 'wp_ajax_nopriv_gaad_variation_calculate', 'gaad_ajax::gaad_variation_calculate' );
	
	
}






 if( !is_admin() ){

//add_filter( 'formatted_woocommerce_price', 'wawaprint_gross_price', 10, 2); 
//add_filter( 'woocommerce_currency_symbol', 'wawaprint_currency_symbol', 10, 1); 
 }

/* To jest tylko łatka, trzeba bedzie pobrac klase podatkowa, walute ... */
function wawaprint_gross_price( $price, $variation ) {
	
	$net = (float)$price;
	$gross = round( (float)$price * 1.23, 2 );
	
	$all = "<div class=\"net-price\">". $net ." zł netto</div>";
	$all .= "<div class=\"gross-price\">". $gross ." zł brutto</div>";
	
	return $all;
}


function wawaprint_currency_symbol ( $currecny ) {
	return '';
}


	





/*
* Zmiany w adminie
*/
if( is_admin() ){
	

	
	
	
	
	/**
 * Add a custom action to order actions select box on edit order page
 * Only added for paid orders that haven't fired this action yet
 *
 * @param array $actions order actions array to display
 * @return array - updated actions
 */
function sv_wc_add_order_meta_box_action( $actions ) {
    global $theorder;
   
    // add "mark printed" custom action
    $actions['wc_custom_order_action'] = __( 'Generuj KARTĘ PRACY', 'my-textdomain' );
    return $actions;
}
add_action( 'woocommerce_order_actions', 'sv_wc_add_order_meta_box_action' );
	
	
}


/**
 * Add an order note when custom action is clicked
 * Add a flag on the order to show it's been run
 *
 * @param \WC_Order $order
 */
function sv_wc_process_order_meta_box_action( $order ) {
    require_once( get_template_directory() . '/gaad-woo-mod/class/pdf_html_templates.php');
    require_once( get_template_directory() . '/gaad-woo-mod/vendors/tcpdf/tcpdf.php');
    require_once( get_template_directory() . '/gaad-woo-mod/class/production_brief_tcpdf_extension.php' ); //dolaczenie rozszerzenia dla tego typu dokumentu pdf
  
    // add the order note
    // translators: Placeholders: %s is a user's display name
    $message = sprintf( __( 'Karta pracy zostałą wygenerowana.', 'my-textdomain' ), wp_get_current_user()->display_name );
    $order->add_order_note( $message );
    
        // create new PDF document
    $pdf = new production_brief_PDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->setOrder( $order );  
    $pdf->generate( );
    $pdf->save( );
  
   // echo '<pre>'; echo var_dump( $pdf ); echo '</pre>';
  
die();
    // add the flag
    update_post_meta( $order->id, '_wc_order_marked_printed_for_production', 'yes' );
}
add_action( 'woocommerce_order_action_wc_custom_order_action', 'sv_wc_process_order_meta_box_action' );




/**
 * This snippet will add cancel order button to all (not cancelled) orders.
 */
add_filter( 'woocommerce_admin_order_actions', 'add_cancel_order_actions_button', PHP_INT_MAX, 2 );
function add_cancel_order_actions_button( $actions, $the_order ) {
    if ( ! $the_order->has_status( array( 'cancelled' ) ) ) { // if order is not cancelled yet...
        $actions['cancel'] = array(
            'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=cancelled&order_id=' . $the_order->id ), 'woocommerce-mark-order-status' ),
            'name'      => __( 'Cancel', 'woocommerce' ),
            'action'    => "view cancel", // setting "view" for proper button CSS
        );
    }
    return $actions;
}
add_action( 'admin_head', 'add_cancel_order_actions_button_css' );
function add_cancel_order_actions_button_css() {
    echo '<style>.view.cancel::after { content: "\e013" !important; }</style>';
}

