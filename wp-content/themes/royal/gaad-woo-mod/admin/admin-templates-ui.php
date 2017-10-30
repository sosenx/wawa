<?php 

 
function wawa_product_template_scripts() {
        wp_register_style( 'wawa_product_template_css', get_template_directory_uri() . '/gaad-woo-mod/css/wawa-product-template.css', false, '1.0.0' );
        wp_enqueue_style( 'wawa_product_template_css' );
	 	wp_enqueue_script( 'vue', "https://unpkg.com/vue" );
		wp_enqueue_script( 'wawa_product_template_js', get_template_directory_uri() . '/gaad-woo-mod/js/wawa-product-template.js', array( 'vue', 'jquery'), null, true );
	
}
add_action( 'admin_enqueue_scripts', 'wawa_product_template_scripts' );





/**
 * Rejestrowanie szablonów html pól niestandardowych
 */
function wawa_product_template_register_meta_boxes() {
    
	
	//gbox data table dla produkct post type
	add_meta_box(
		'product-templates-id',
		'GBOX2 alpha - wawa_product_template',
		'wawa_product_template_display_callback',
		array('page', 'post', 'product' )
		
	);	
}



/*
* Parsuje tablice pa_format uzuswając z niej niepotrzebne elementy, a zostawiając format w wymiarze [w] x [h] [unit]
*/
function parse_pa_format_array( $pa_format ){
	$_format = array();
	$max = count ($pa_format);
	
	for( $i=0; $i<$max ;$i++ ){
		$matches = array();
		preg_match( '/(\d+)x(\d+)\s?(mm|cm|m)/', $pa_format[$i], $matches );
		if( is_array( $matches )  ){
			$_format[ $i ] = $matches[ 1 ] .'x'.$matches[ 2 ] . $matches[ 3 ];
		}
		
		
		
	}
	
	return $_format;
}

/**
 * Dodanie gbox data table do obiektu typu produkt (produktu)
 *
 * @param WP_Post $post Current post object.
 */
function wawa_product_template_display_callback( $post ) {
    // Display code/markup goes here. Don't forget to include nonces!
	global $post;
	$source_ID = $post->ID;
	

	
	$product = new WC_product( $source_ID );
	$pa_format = wc_get_product_terms( $product->id, 'pa_format', array( 'fields' => 'slugs', ) );
	$max = count ($pa_format);
	
	//$pa_format = parse_pa_format_array( $pa_format );
	$pa_format = gaad_ajax::parse_pa_format_array($pa_format);
	//echo '<pre>'; echo var_dump($pa_format); echo '</pre>';	
	
	//pobieranie wartości pól
	$wawa_product_template_json = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_json', true);
	$wawa_product_template_json2 = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_json2', true);
	
	
		echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	
	
	for( $i=0; $i<$max ;$i++ ){
				
		
		$product_template_show = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_show__'.$pa_format[$i], true);
    $product_template_ai = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_ai__'.$pa_format[$i], true);
		$product_template_psd = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_psd__'.$pa_format[$i], true);
		$product_template_cdr = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_cdr__'.$pa_format[$i], true);	
		$product_template_pdf = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_pdf__'.$pa_format[$i], true);
		
		?>
		
		<br><br>
		<div class="tpl-row" >		
			<h2><strong>Rozmiar <?php echo $pa_format[$i] ?>;</strong></h2>	
		</div>
		
		
		<div class="tpl-row" >
			<label>Szablon produktu ogólny</label><br>
			<input type="text" name="wawa_product_template[wawa_product<?php echo '_'.$source_ID.'_'; ?>template_show__<?php echo $pa_format[$i]; ?>]" value="<?php echo $product_template_show; ?>" >
			<i 
				@click="setTemplateFile( $event )"
				
			class="fa fa-picture-o" aria-hidden="true" data-for="wawa_product<?php echo '_'.$source_ID.'_'; ?>template_show__<?php echo $pa_format[$i]; ?>" ></i>
			
		</div>
		
		<div class="tpl-row" >
		<h2><strong>wawa_product_template_ai__<?php echo $pa_format[$i]; ?></strong></h2>
		
			<label>Szablon produktu .ai</label><br>
			<input type="text" name="wawa_product_template[wawa_product<?php echo '_'.$source_ID.'_'; ?>template_ai__<?php echo $pa_format[$i]; ?>]" value="<?php echo $product_template_ai; ?>" >
			<i 
				@click="setTemplateFile( $event )"
				
			class="fa fa-picture-o" aria-hidden="true" data-for="wawa_product<?php echo '_'.$source_ID.'_'; ?>template_ai__<?php echo $pa_format[$i]; ?>" ></i>
			
		</div>
		
		
		<div class="tpl-row" >
			<label>Szablon produktu .psd</label><br>
			<input type="text" name="wawa_product_template[wawa_product<?php echo '_'.$source_ID.'_'; ?>template_psd__<?php echo $pa_format[$i]; ?>]" value="<?php echo $product_template_psd; ?>" >
			<i 
				@click="setTemplateFile( $event )"
				
			class="fa fa-picture-o" aria-hidden="true" data-for="wawa_product<?php echo '_'.$source_ID.'_'; ?>template_psd__<?php echo $pa_format[$i]; ?>" ></i>
		</div>
		
		
		<div class="tpl-row" >
			<label>Szablon produktu .cdr</label><br>
			<input type="text" name="wawa_product_template[wawa_product<?php echo '_'.$source_ID.'_'; ?>template_cdr__<?php echo $pa_format[$i]; ?>]" value="<?php echo $product_template_cdr; ?>" >
			<i 
				@click="setTemplateFile( $event )"
				
			class="fa fa-picture-o" aria-hidden="true" data-for="wawa_product<?php echo '_'.$source_ID.'_'; ?>template_cdr__<?php echo $pa_format[$i]; ?>" ></i>
		</div>
		
		
		<div class="tpl-row" >
			<label>Szablon produktu .pdf</label><br>
			<input type="text" name="wawa_product_template[wawa_product<?php echo '_'.$source_ID.'_'; ?>template_pdf__<?php echo $pa_format[$i]; ?>]" value="<?php echo $product_template_pdf; ?>" >
			<i 
				@click="setTemplateFile( $event )"
				
			class="fa fa-picture-o" aria-hidden="true" data-for="wawa_product<?php echo '_'.$source_ID.'_'; ?>template_pdf__<?php echo $pa_format[$i]; ?>" ></i>
		</div>
<?php

		
	}
	
		?>
		
		
		
		
	

<?php

	
		echo '<div class="gaad-post-meta-box-value">';	
			echo '<label for="">Dodadkowe ustawienia dla aplikacji wawaSzablonyProduktu</label>';
	
			wp_editor( $wawa_product_template_json, 'wawa_product_'.$source_ID.'_template_json', array(
					'textarea_name' => 'wawa_product_template[wawa_product_'.$source_ID.'_template_json]',
					'textarea_rows' => 20,
          'tabindex' => '',
          'tabfocus_elements' => ':prev,:next', 
          'editor_css' => '', 
          'editor_class' => '',
          'teeny' => false,
          'dfw' => false,
          'tinymce' => false, // <-----
          'quicktags' => true
					)
				);

	
	echo '</div>';
  
  
		echo '<div class="gaad-post-meta-box-value">';	
			echo '<label for="">Dodadkowe ustawienia dla aplikacji wawaSzablonyProduktu</label>';
	
			wp_editor( $wawa_product_template_json2, 'wawa_product_'.$source_ID.'_template_json2', array(
					'textarea_name' => 'wawa_product_template[wawa_product_'.$source_ID.'_template_json2]',
					'textarea_rows' => 20,
          'tabindex' => '',
          'tabfocus_elements' => ':prev,:next', 
          'editor_css' => '', 
          'editor_class' => '',
          'teeny' => false,
          'dfw' => false,
          'tinymce' => false, // <-----
          'quicktags' => true
					)
				);

	
	echo '</div>';
	
}



/**
 * Zapisywanie gbox data table w product post type
 *
 * @param int $post_id Post ID
 */
function save_wawa_product_template_meta_box( $post_id ) {
    $post = get_post ($post_id);
		
		// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}
	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	
	$var_name = array();
	preg_match('/save_([\S]+)_meta_box/', __FUNCTION__, $var_name);
	
	
	if(isset($var_name[1])){
		$_meta = $_POST[$var_name[1]];	
	}//if	
	if(!isset($_meta)){ return false; }//if
	
	foreach($_meta as $label => $value){
		if(preg_match('/-settings$/', $label)){			
			$_meta[$label] = base64_encode( urlencode($value) );
		}//if
	}

	//echo '<pre>'; echo var_dump($post, $_meta); echo '</pre>'; die();
	
	// Add values of $events_meta as custom fields
	
	foreach ($_meta as $key => $value) { // Cycle through the $events_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}


}


//dodanie akcji rejestrujacwej szablony pól niestandardowych
add_action( 'add_meta_boxes', 'wawa_product_template_register_meta_boxes', 999,999 );
add_action( 'save_post', 'save_wawa_product_template_meta_box' );


?>