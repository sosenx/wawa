<?php 
/*
* Zawiera metody wykonywane po stronie serwera do podłączenia pod akcji ajaxa wp
*/
class gaad_ajax {
  
  public static function product_send_get_emial(){
    
    ob_start();
    ?>    
      <h1 style="color:red">ala ma kota</h1>    
    <?php    
    $content = ob_get_contents();
    ob_end_clean();
    
    return $content;
  }
  
  public static function gaad_send_product_pdf(){
    require_once(ABSPATH . WPINC .'/class-phpmailer.php');
    $db = new gaad_db();
    $result = array( 'test' => 'ok',  );

	  $mail = new PHPMailer();
   
    //łatka usuwajaca subdomene serwis z domeny wysylajacej maila
    //$from = 'www@' . preg_replace( "/serwis[\.]{1}/", "", $_SERVER["HTTP_HOST"] );
    $from = 'web@wawaprint.pl';


    $send_to = $_POST['formdata']['reciever'];
    $send_to_origin = false;
    $user_email = $_POST['formdata']['sender']; 
    
    $from_label = __('Wiadomość z ') . $user_email;
    $reply_to = 'oferty@wawaprint.pl';
    $mail_title = $user_email . ' poleca produkty z wawaprint.pl';
    $to = $send_to; 
    //$to = 'barteksosnowski711@gmail.com';
    $mail_body = gaad_ajax::product_send_get_emial();  
    
	
	//Set who the message is to be sent from
		$mail->setFrom($from, $from_label);
		//Set an alternative reply-to address
		$mail->addReplyTo($reply_to);
		//Set who the message is to be sent to
		$mail->addAddress($to);
		//Set the subject line
	  $mail->IsHTML(true);
    
    $mail->AddAttachment(
      str_replace( 'http://wawaprint.pl', '',  $_POST['formdata']['pdfHref'] ),
      'oferta-wawaprint-pl.pdf'      
    );
    
    
		//kopia dla wysyłającego (opcjonalnie)
		//$mail->addAddress( $user_email );
			
		$mail->Subject = $mail_title;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($mail_body);
		//Replace the plain text body with one created manually
		//$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
		$mail->CharSet = 'utf8';
        
    //send the message, check for errors
      if (!$mail->send()) {
        $result['error'] = 'not sent, error: ' . $mail->ErrorInfo;
      } elseif(!is_null($success)) {
        $result['error'] = $success;
      } else {
        $result['error'] = 'sent to: ' . $send_to;
        }
    
    
    
    wp_send_json( $result );
  }
  
  
  /*
  * Sprawdza czy interakcja nie jest prowadzona przez robota
  */
  public static function gaad_recaptcha(){
    $db = new gaad_db();
    
    $secretKey = $db->recaptcha['secret'];
    $response = $_POST['g-recaptcha-response'];     
    $remoteIp = $_SERVER['REMOTE_ADDR'];

    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$remoteIp";
    
    
    $reCaptchaValidationUrl = file_get_contents( $url );
    $result = json_decode($reCaptchaValidationUrl, TRUE);

    wp_send_json( $result );
  }
  
  
  public static function product_variation_pdf(){
    chdir( '..');
    
    require_once( get_template_directory() . '/gaad-woo-mod/class/pdf_html_templates.php');
    require_once( get_template_directory() . '/gaad-woo-mod/vendors/tcpdf/tcpdf.php');
    require_once( get_template_directory() . '/gaad-woo-mod/class/product_send_tcpdf_extension.php' ); //dolaczenie rozszerzenia dla tego typu dokumentu pdf
    
    
    $uniqid = uniqid();
    $name = '/pdf/' . $_POST[ 'product_id' ] . '-'. $_POST[ 'product_id' ] .'-' . $uniqid . '.pdf';
    
    $_css = file_get_contents( get_template_directory() . '/gaad-woo-mod/css/product-send-pdf.css' );
    
    $html = pdf_html_template::product_send( $_css );
    
    
    
    
    $r = array(
      'path' => 'http://'. $_SERVER['HTTP_HOST'] . $name,
      '_html' => $html, 
      '_css' => $_css, 
    );
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // create new PDF document
$pdf = new wawa_product_send_PDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    
   // set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Wawaprint.pl');
$pdf->SetTitle('Oferta handlowa');
$pdf->SetSubject('Wawaprint - oferta handlowa');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(20, 10, 20);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)


// output the Emogrified HTML content 
 $pdf->writeHTML($html, true, false, true, false, '');
    
    
// Close and output PDF document
    
    
    
    ob_clean(); 
  //  echo '<pre>'; echo var_dump( $_SERVER  ); echo '</pre>';
    $pdf->Output($name, 'F');
    
    
    wp_send_json( $r );
    
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
	
	
	
	/*
	* Parsuje tablice pa_format uzuswając z niej niepotrzebne elementy, a zostawiając format w wymiarze [w] x [h] [unit]
	*/
	static function parse_pa_format_array( $pa_format ){
		$_format = array();
		$max = count ($pa_format);
		
		foreach( $pa_format as $i  ){
			$matches = array();
			
			preg_match( '/(\d+)x(\d+)\s?(mm|cm|m)\)?$/', $i, $matches );
		
			if( is_array( $matches )  ){
				
				$_format[ ] = $matches[ 1 ] .'x'.$matches[ 2 ] . $matches[ 3 ];
			}



		}

		return $_format;
	}
	
	
	static function gaad_get_product_templates( $source_ID ){		
		
    $product = new WC_product( $source_ID );
    $pa_format = wc_get_product_terms( $product->id, 'pa_format' );
    $max = count ($pa_format);
	$pa_format = gaad_ajax::parse_pa_format_array( $pa_format );
    $r = array();
    
	
		
    for( $i=0; $i<$max ;$i++ ){				

      $product_template_show = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_show__'.$pa_format[$i], true);
      $product_template_ai = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_ai__'.$pa_format[$i], true);
      $product_template_psd = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_psd__'.$pa_format[$i], true);
      $product_template_cdr = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_cdr__'.$pa_format[$i], true);	
      $product_template_pdf = get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_pdf__'.$pa_format[$i], true);
      
      $r[ $pa_format[ $i ] ] = array(
        'product_template_show' => $product_template_show, 
        'product_template_ai' => $product_template_ai, 
        'product_template_psd' => $product_template_psd, 
        'product_template_cdr' => $product_template_cdr, 
        'product_template_pdf' => $product_template_pdf,         
      );
      
    }
	return $r;
    
    /*
    
		$product_template_ai = get_post_meta( $source_ID, 'wawa_product_template_ai', true);
		$product_template_psd = get_post_meta( $source_ID, 'wawa_product_template_psd', true);
		$product_template_cdr = get_post_meta( $source_ID, 'wawa_product_template_cdr', true);	
		$product_template_pdf = get_post_meta( $source_ID, 'wawa_product_template_pdf', true);

		return array(
			'ai' => array(
					'icon' => 'http://wawaprint.pl/wp-content/uploads/2017/06/template-icon-pdf.png',
					'src' => $product_template_ai, 
				),
			'psd' => array(
					'icon' => 'http://wawaprint.pl/wp-content/uploads/2017/06/template-icon-psd.png',
					'src' => $product_template_psd, 
				),
			'cdr' => array(
					'icon' => 'http://wawaprint.pl/wp-content/uploads/2017/06/template-icon-cdr.png',
					'src' => $product_template_cdr, 
				),
			'pdf' => array(
					'icon' => 'http://wawaprint.pl/wp-content/uploads/2017/06/template-icon-pdf.png',
					'src' => $product_template_pdf, 
				),
			
		);	
    
    */
    
	}
	
	
	/*
	* Łączy (inline) podany css z podanym HTMLem
	*
	*/	
	static function emogrifyIt( $body, $css ){
		
		$emogrifier_autoload_file = get_template_directory() . '/gaad-woo-mod/vendors/emogrifier/vendor/autoload.php';
		$emogrifier_include_file = get_template_directory() . '/gaad-woo-mod/vendors/emogrifier/Classes/Emogrifier.php';

			/*
			*wczytanie emogfier do zapisu cssa z szablonu jako inline
			*/			
			if( is_file( $emogrifier_autoload_file ) ){
				include_once( $emogrifier_autoload_file );
				
				if( is_file( $emogrifier_include_file ) ){
					 include_once( $emogrifier_include_file );
				}//if
			}//if

		
		$emogrifier = new \Pelago\Emogrifier();
		$emogrifier->setHtml( $body );
		$emogrifier->setCss( $css );
		$mergedHtml = $emogrifier->emogrify(); // content + meta tag
		
		return $emogrifier->emogrifyBodyContent();		
		
	}
	
	
	/*
	* Wysyłanie maila poleć product
	*/
	static function gaad_send_product_adrvertisment( ){
		
		$r = array();
		require_once(ABSPATH . WPINC .'/class-phpmailer.php');
		$mail = new PHPMailer();
		
		$from = 'web@' . preg_replace( "/serwis[\.]{1}/", "", $_SERVER["HTTP_HOST"] );		
		$to = $_POST['email'];
		$from_label = $_POST['name'];
		 
		$mail_title = $from_label . ' poleca Ci produkty wawaprint.pl';
		//$string = file_get_contents( get_template_directory() . '/gaad-woo-mod/templates/advertise-product-email-body.html' );
		$string_css = file_get_contents( get_template_directory() . '/gaad-woo-mod/css/advertise-product-email-body.css' );
		
		ob_start( 'callback' );
			include( get_template_directory() . '/gaad-woo-mod/templates/advertise-product-email-body.php' );	
			$string = ob_get_contents();
		ob_end_flush();
		
		
		function callback( $input ){
			return $input;
		}
		
		
		$string = gaad_ajax::emogrifyIt( $string, $string_css );
		echo $string;
		
		$images = array();
		preg_match_all("/src=\"([\S]*)\"/m", $string, $images);

		foreach($images[1] as $img){
			$img_id = uniqid('img');	
			$string = str_replace($img, 'cid:' . $img_id, $string);
			$img_path = array();
			preg_match("/wp-content\/\S*$/", $img, $img_path);		

		
		
			$mail->AddEmbeddedImage('/'.$img_path[0], $img_id);
		}//foreach
		
		
		//Set who the message is to be sent from
		$mail->setFrom($from, $from_label);
		//Set an alternative reply-to address
		$mail->addReplyTo($reply_to);
		//Set who the message is to be sent to
		$mail->addAddress($to);
		//Set the subject line
	
	
		$mail->Subject = $mail_title;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($string);
		//Replace the plain text body with one created manually
		//$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
		$mail->CharSet = 'utf8';
	
	
	
	
	//send the message, check for errors
	if (!$mail->send()) {
		echo 'false';
	} else {
		echo 'true';
		}
		
		
		die();
		
	}
	
	
	
	
	/*
	*pobiera obiekty produktów zapisanych jako cross sell do produktu o podanym id
	*/
	
	static function gaad_get_cross_sell( $product_id, $input_settings ){
		$crosssellProductIds   =   get_post_meta( $product_id, '_crosssell_ids' );
		$crosssellProductIds    =   $crosssellProductIds[0];	
		
		$temp = array();
		foreach($crosssellProductIds as $id){
			$product = wc_get_product( $id );
			$_POST['product_id'] = $id;
    
      
     
			$calculator = new gaad_calc( $id, $_POST['variation_id'] );
			$new_regular_price = $calculator->use_it();
		
		
			$r = array_merge( array(
				"product_id" => $id,
				"variation_id" => 0		
			), $new_regular_price);

			$calc_name = $calculator->get_calc_name();
      $product_calc_class_terms = wp_get_object_terms( $id,  'calc_class' );
      $calc_class = $product_calc_class_terms[0]->slug;
     
      
      
      /*
      * Pobieranie defaultowych wartośći parametrów
      */
      
      
      if(  class_exists( 'gaad_input_settings_filter' ) && method_exists(  'gaad_input_settings_filter', 'gaad_product_input_settings_filter__'.$calc_class ) ){
        $defaults = gaad_input_settings_filter::{'gaad_product_input_settings_filter__'.$calc_class}( array() );
        
      }	
      
      if( isset( $defaults ) ){
        $post_temp = array();
        $post_max = count( $defaults );
        
        foreach( $defaults as $k => $v ){          
          $post_temp[ $k ] = $v[ 'default' ];          
        }
      }
      
      $_POST['post_data'] = $post_temp;
      $_POST['post_data']['product_id'] = $id;
      
      
			$r["variation_id"] = uniqid('var');

			$calc_obj = new $calc_name($id, $r['variation_id'] );
			$r['q'] = $calc_obj->calc_all_quantites();

			$product->id = $id;
			
			$product->q = $r[ 'q' ];
			$product->permalink = get_permalink( $id );
			$product->thumb = get_the_post_thumbnail_url( $id, 'thumbnail' );
			
			$temp[] = $product;	
		}
		
		
		return $temp;
	}
	
	
	/**
	* 
	*
	* @return void
	*/
	static function get_product ( $product_id = NULL ) {
		/*
		* Tworzenie lub pobieranie obiektu $product, brak id == bieżący produkt
		*/			
		$product_id = !isset( $product_id ) ? $_POST[ 'product_id' ] : $product_id;
		if( !isset($product_id) || is_null($product_id) ){
			global $product;
			$product_id = $product->id;
		} else {
			$product = new WC_product( $product_id );
		}
	return $product;	
	}
	
	
	
	
	/**
	* zwraca tag marzy produktu
	*
	* @return void
	*/
	static function gaad_get_markup_tag ( $product_id = NULL ) {
		$product = gaad_ajax::get_product( $product_id );		
		$product_calc_class_terms = wp_get_object_terms( $product->id,  'markup_tag' );		
		return is_string( $product_calc_class_terms[0]->slug ) ? $product_calc_class_terms[0]->slug : false;
	}
	
	/**
	* 
	*
	* @return void
	*/
	static function gaad_get_calc_class ( $product_id = NULL ) {
		$product = gaad_ajax::get_product( $product_id );		
		$product_calc_class_terms = wp_get_object_terms( $product->id,  'calc_class' );		
		return $product_calc_class_terms[0]->slug;
	}
	
	/**
	* 
	*
	* @return void
	*/
	static function gaad_get_product_input_settings ( $product_id = NULL ) {		
		$calc_class = gaad_ajax::gaad_get_calc_class( $product_id );
		
		$default_input_settings = array( 
			'attribute_pa_format' => array(
				'type' => 'select', 
				'default' => 0, 
				'labels' => array(
					'l' => 'Format (wymiar)',
				), 
			)
		);
		
		$input_settings = has_filter( 'product_input_settings' ) ? apply_filters( 'product_input_settings', $default_input_settings, $calc_class ) : $default_input_settings;
	
		return $input_settings;
	}
	
	
	/**
	* Pobiera wszystkie atrybuty produktu ze wszystkimi wartościami
	*
	* @return void
	*/
	function gaad_get_product_attr_data ( $product_id = NULL ) {
	
		$product = gaad_ajax::get_product( $product_id );
		
		$unwanted_att = array('pa_item_status', 'pa_ship_days', 'pa_ship_date', 'pa_max_files', 'pa_termin-wykonania');
		$r = array();

		$attributes				= $product->get_attributes();
		foreach( $attributes as $att_name => $attribute ){
			
			if( !in_array( $att_name, $unwanted_att ) ){

				$attributes		= $product->get_attributes();
				$attribute		= $attributes[ $att_name ];
				$values 		= wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'all' ) );
				$id				= $args['id'] ? $args['id'] : sanitize_title( $attribute_name );

				if( is_array( $values ) && !empty( $values ) ){
					$r['attribute_' . $att_name] = $values;
				}					
			}
		}				
		
		return $r;
		
	}
	
	
	/*
	*
	* wczytywanie danych produktu, obliczanie cen wariantów trzeba to przeniesc w jakies bardziej cywilizowane miejsce
	*/
	static function gaad_get_calc_data( $product_id = NULL, $input_settings = NULL ){

		$product = gaad_ajax::get_product( $product_id );
		
		$_POST['product_id'] = $product->id;
		$_POST['post_data'] = gaad_ajax::gaad_get_basic_variation( $input_settings,  $product->id );		
		
		$calculator = new gaad_calc( $product->id );
		$tmp = $calculator->use_it();
		$calc_name = $calculator->get_calc_name();
		$calc_obj = new $calc_name($_POST['product_id'], 0 );
		$tmp['q'] = $calc_obj->calc_all_quantites();
		$GLOBALS['loaded_calc_data'] = $tmp;
		
		return $tmp;
		
	}
	

	/*
	* Tworzy prostą wariację złożona z 1 wartośći kazdego z atrybutów
	*
	*/	
	static function gaad_get_basic_variation( $input_settings = NULL, $product_id = NULL ){

		/*
		* Tworzenie lub pobieranie obiektu $product, brak id == bieżący produkt
		*/			
		$product_id = !isset( $product_id ) ? $_POST[ 'product_id' ] : $product_id;
		if( !isset($product_id) || is_null($product_id) ){
			global $product;
			$product_id = $product->id;
		} else {
			$product = new WC_product( $product_id );
		}
		
		/*
		* Tablica z ustawieniami pól formularza zawierająca defaultowe wartości kazego z atrybutów
		*/
		$input_settings = is_array( $input_settings ) && !empty( $input_settings ) ? $input_settings : false;
		
		
		$unwanted_att = array('pa_item_status', 'pa_ship_days', 'pa_ship_date', 'pa_max_files');
		$r = array();

		$attributes				= $product->get_attributes();
		foreach( $attributes as $att_name => $attribute ){
			if( !in_array( $att_name, $unwanted_att )){

				$attributes		= $product->get_attributes();
				$attribute		= $attributes[ $att_name ];
				$values 		= wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'all' ) );
				$id				= $args['id'] ? $args['id'] : sanitize_title( $attribute_name );

				//pobranie części tablicy $input_settings zawierającej ustawiwenia atrybutu $att_name
				$_s = isset( $input_settings['attribute_' . $att_name] ) ? $input_settings['attribute_' . $att_name] : false; 
				if( is_array( $values ) && !empty( $values ) ){
					if( $_s ){
						$default_value = isset( $_s['default'] ) ? $_s['default'] : $values[0]->slug;						
						$r['attribute_' . $att_name] = $default_value;
						
					} else {
						$r['attribute_' . $att_name] = $values[0]->slug;	
					}
					
				}					
			}
		}		
		$r[ 'product_id' ] = $product_id;

		return $r;
	}
	
	/**
	* Weryfikacja transakcji w Przelewy24
	* .przelewy24.pl/trnVerify
	*
	* @return void
	*/
	function gaad_verify_payment (  ) { //die('die-gaad_verify_payment 	 ');
		require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_order.php' );
		global $wpdb;
		$db = new gaad_db();
		$active_order = get_user_meta( get_current_user_id(), 'active-order-id', true );
		$sql = "SELECT * FROM `wp_gaad_p24` WHERE `post_id` = {$_POST[ '_id' ]} ORDER BY `id` DESC LIMIT 0,1";

		$results = $wpdb->get_results( $sql, ARRAY_A );
		/*sprawdzanie, czy platnosc dotyczy biezacego aktywnego zamowienia */
		if( !empty( $results ) && $results[0]['post_id'] == $active_order ){
			$order_id = $results[0]['post_id'];
			$order = new gaad_order( $order_id );
			$data = $results[0];			
			
			$post_id= $data["post_id"];
			$p24_amount= $data["p24_amount"];
			$p24_currency = $data["p24_currency"];
			$p24_merchant_id = $data["p24_merchant_id"];
			$p24_method = $data["p24_method"];
			$p24_order_id= $data["p24_order_id"];
			$p24_pos_id = $data["p24_pos_id"];
			$p24_session_id= $data["p24_session_id"];
			$p24_sign = $order->create_verify_p24_sign( $data );
			$p24_statement = $data["p24_statement"];
  
			$headers = array(				
				'p24_merchant_id=' . $p24_merchant_id,
				'p24_pos_id=' . $p24_merchant_id,
				'p24_session_id=' . $p24_session_id, 
				'p24_amount='.$p24_amount,
				'p24_currency='.$p24_currency,
				'p24_order_id=' . $p24_order_id,				
				'p24_sign=' . $p24_sign
			);
			
			$ch = curl_init();
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, join( "&", $headers));
				curl_setopt($ch, CURLOPT_URL,'https://sandbox.przelewy24.pl/trnVerify');
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
				curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				$p24_response = curl_exec( $ch );
					$res = array();
					if( $p24_response ){
						$p24_response_arr = explode( '&', $p24_response );
						foreach( $p24_response_arr as $k => $v ){
							$line = explode( '=', $v );
							$res[$line[0]] = $line[1];
						}
					}

			/*
			* Zmiana statusu zamóienia na processing ( w realizacji )
			*/
			if( is_array( $res ) && !empty( $res ) && $res['error'] == 0 ){
				$status =  $order->update_status( 'processing' , 'order_note');	
				update_user_meta( get_current_user_id(), 'active-order-id', '' );
			}
			
			wp_send_json( $res );
		} else {
			wp_send_json( array( 'error' => -1, ) );
			//echo '<pre>'; echo var_dump(!empty( $results ), $results[0]['post_id'] == $active_order); echo '</pre>';
		
		}
		
	}
	
	
	/**
	* Rejestrowanie transakcji w Przelewy24
	* przelewy24.pl/trnRegister
	*
	* @return void
	*/
	function gaad_process_payment (  ) {
	require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_order.php' );
		$db = new gaad_db();
		$user_id = get_current_user_id();
		$order_id = get_user_meta( get_current_user_id(), 'active-order-id', true );
		$order = new gaad_order( $order_id );
		$var_name = $db->payments['p24'][ 'return_var_name' ];
		
		$r = array( 'order_id' => $order_id );
		
		$p24_merchant_id = $db->payments['p24']['p24_merchant_id'];		
		$p24_pos_id = $db->payments['p24']['p24_pos_id'];
		
		$r[ 'p24_pos_id' ] = $p24_pos_id;
		$r[ 'p24_merchant_id' ] = $p24_merchant_id;
		
		
		$p24_currency = $db->payments['p24']['p24_currency'];
		
		$p24_session_id = uniqid( $db->payments['p24']['session_id_prefix'] );
		$r[ 'p24_session_id' ] = $p24_session_id;
		
		$p24_sign = $order->create_p24_sign( $p24_session_id, $order );
		$r[ 'p24_sign' ] = $p24_sign;
		
		//zapisanie session id do bazy danych
		update_post_meta( $order_id, 'p24_session_id', $p24_session_id);
		update_post_meta( $order_id, 'p24_sign', $p24_sign);
		
		
		$current_user = get_user_meta( $user_id );
		
		$billing_address = array(
            'first_name' => $current_user['billing_first_name'][0],
            'last_name'  => $current_user['billing_last_name'][0],
            'company'    => $current_user['billing_company'][0],
            'email'      => $current_user['billing_email'][0],
            'phone'      => $current_user['billing_phone'][0],
            'address_1'  => $current_user['billing_address_1'][0],
            'address_2'  => $current_user['billing_address_2'][0], 
            'city'       => $current_user['billing_city'][0],
            'state'      => $current_user['billing_state'][0],
            'postcode'   => $current_user['billing_postcode'][0],
            'country'    => $current_user['billing_country'][0]
        );
		
		$client = $billing_address['company'] .' '. $billing_address['first_name'] .' '. $billing_address['last_name'];
		$address = $billing_address['address_1']; 
		$shipment_total = $order->get_shipment_total( 'shipping' );
		$p24_amount = (int)( ( $order->get_total() + $shipment_total['total']['total'] ) * 100);
		
		//echo '<pre>'; echo var_dump(); echo '</pre>';
		$headers = array(
			'p24_session_id=' . $p24_session_id, 
			'p24_merchant_id=' . $p24_merchant_id,
			"p24_pos_id=" . $p24_merchant_id,
			 "p24_amount=".$p24_amount,
			 "p24_currency=".$p24_currency,
			 "p24_description=wawaprint - zamówienie nr " . $order_id,
			 
			 "p24_client=".$client,
			 "p24_address=".$address,
			 "p24_zip=".$billing_address['postcode'],
			 "p24_city=".$billing_address['city'],
			 "p24_country=".$billing_address['country'],
			 "p24_email=".$billing_address['email'],
			 "p24_language=pl", 
			 "p24_url_status=http://wawaprint.pl/wp-content/themes/royal/gaad-woo-mod/payment-ok.php",
			 "p24_url_return=".$db->payments['p24']['p24_url_return'].'?'.$db->payments['p24']['p24_url_return_var'].'='.$order_id,
			 "p24_api_version=3.2",
			 "p24_sign=" . $p24_sign
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, join( "&", $headers));
		curl_setopt($ch, CURLOPT_URL,'https://sandbox.przelewy24.pl/trnRegister');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$p24_response = curl_exec( $ch );
		$res = array();
		if( $p24_response ){
			$p24_response_arr = explode( '&', $p24_response );
			foreach( $p24_response_arr as $k => $v ){
				$line = explode( '=', $v );
				$res[$line[0]] = $line[1];
			}
		}
		
		//$r = array( '' => '', )
	//echo '<pre>'; echo var_dump( $res ); echo '</pre>';
	//echo '<pre>'; echo var_dump($headers); echo '</pre>';
		wp_send_json( $res );		
	}
	
	/**
	* 
	*
	* @return void
	*/
	function get_pending_orders (  ) {
		$customer_orders = get_posts( $customer_orders_args = array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types(),
			'post_status' => array( 'wc-pending' ),
		) );
		
		if( is_array( $customer_orders ) && ! empty( $customer_orders )  ){
			return $customer_orders;
		}
	}
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_get_totals (  ) { 
		require_once( get_template_directory() . '/gaad-woo-mod/class/gaad_order.php' );
		$order = new gaad_order( $_POST[ 'order_id' ] );
		wp_send_json( $order->get_totals() );		
	}
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_set_order_status (  ) {
		$user_id = get_current_user_id();
		$order_id = !isset( $_POST[ '_id' ] ) ? gaad_ajax::get_active_order() : $_POST[ '_id' ];
		$new_status = $_POST[ '_st' ];
		
		$r = array( 'order_id' => $order_id );
		
		$order = new WC_Order($order_id);
		$status =  $order->update_status( $new_status , 'order_note');	
		$r['status'] = $status;
		
		if( $new_status === 'pending' ){
			update_user_meta( $user_id, 'active-order-id', $order_id );
		}
		
		wp_send_json( $r );
	}
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_delete_attachment_file (  ) {
		$r = array();
		$file_name = $_POST['file_name'];
		$item_id = $_POST['item_id'];
		$file_id = gaad_ajax::get_file_id( $file_name );
		$meta_key = '_attachment-'.$file_id;
		
		$r[] = array(
			'item_id' => $item_id,
			'file_id' => $file_id,
			'meta_key' => $meta_key,
			'ok' => wc_delete_order_item_meta( $item_id, $meta_key, $meta_value )
		);
		$meta_key = '_status-'.$file_id;
		$r[] = array(
			'item_id' => $item_id,
			'file_id' => $file_id,
			'meta_key' => $meta_key,
			'ok' => wc_delete_order_item_meta( $item_id, $meta_key, $meta_value )
		);
		
		unlink( $_POST['file_name'] );
		$thumb_file_name = explode( '/', $_POST['file_name'] );
		$thumb_file_name[ count( $thumb_file_name ) - 1 ] = 'th_' . $thumb_file_name[ count( $thumb_file_name ) - 1 ];
		$thumb_file_name = implode( '/', $thumb_file_name );
		unlink($thumb_file_name);
		
		wp_send_json( $r );
	}
	
	/**
	* Aktualizuje status pliku w attachmencie
	*
	* @return void
	*/
	function gaad_update_item_status (  ) {
		$r = array_merge( array(), $_POST );
		
		$file_name = $_POST['file_name'];
		$item_id = $_POST['item_id'];
		$file_id = gaad_ajax::get_file_id( $file_name );
		$meta_key = '_status-'.$file_id;
		$meta_value = $_POST['status'];		
		
		$r['status'] = $meta_value;		
		$r['update_ok'] = wc_update_order_item_meta( $item_id, $meta_key, $meta_value );
		
		wp_send_json( $r );
	}
	
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_update_attachment_meta (  ) {
		$r = array(			
			'status' => 'update_attachment_meta', 
			'id' => $_POST[ 'item_id' ]
		);	
		
		$files = $_POST[ 'attachment_data' ]['files'];
		
		foreach( $files as $k => $file_name){
			$file_id = gaad_ajax::get_file_id( $file_name );
			$item_id = $r['id'];
			$meta_key = '_attachment-'.$file_id;
			$meta_value = $file_name;
			$unique = true;
			
			woocommerce_add_order_item_meta( $item_id, $meta_key, $meta_value, $unique ); 
			
			echo $meta_key = '_status-'.$file_id;
			$meta_value = $_POST['attachment_data']['attachmentStatus'];
			woocommerce_add_order_item_meta( $item_id, $meta_key, $meta_value, $unique ); 
			
		}
		
		wp_send_json( $r );
	}
	
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_check_file (  ) {
		$r = array_merge( $_POST, array() );
		/*
		Tutaj musi nastap[ic rzeczywiste sprawdzanie pliku
		Obecnie ustawiny jest status 2 - plik ok
		*/
		
		$random_status = array( 111, 112, 198, 201, 200, 200, 200, 200, 200);
		
		
		
		$r['attachmentStatus'] = $random_status[ array_rand( $random_status, 1 ) ];
		
		//sleep(5);
		wp_send_json( $r );
	}
	
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_remove_item_from_order ( $item_id = NULL ) {
		
		$order = gaad_ajax::get_active_order( true );
		$order = new WC_order( $order );
		
		$r = array(
			'action' => 'remove_order_item',
			'status' => 0, 
			'id' => $_POST[ 'item_id' ]
		);		
		
		if( wc_delete_order_item( $_POST[ 'item_id' ] ) ){
			$r[ 'status' ] = 1;
			$order->calculate_totals();
		}
		
		wp_send_json( $r );
	}
	
	
	/**
	* Pobiera aktywne zamówienie 
	* Aktywne zamówiuenie w wawaprint to takie, któe ma statusy: 
	* - awaiting-fiels
	*
	* @return void
	*/
	public static function get_active_order ( $returnObject = false ) {
		$activeOrderID = 0;
		
		$customer_orders = get_posts( $customer_orders_args = array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types(),
			'post_status' => array( 'wc-awaiting-files', 'files-ok', 'files-error' ),
		) );
		
		if( is_array( $customer_orders ) && ! empty( $customer_orders )  ){
			$first_match = $customer_orders[0];
			$activeOrderID =  $first_match->ID;
		}
		
		return $returnObject ? $first_match : $activeOrderID;
	}
	
	
	/**
	* Pobiera aktywne zamówienie 
	* Aktywne zamówiuenie w wawaprint to takie, któe ma statusy: 
	* - awaiting-fiels
	*
	* @return void
	*/
	public static function get_pending_order ( $returnObject = false ) {
		$activeOrderID = 0;
		
		$customer_orders = get_posts( $customer_orders_args = array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types(),
			'post_status' => array( 'wc-pending' ),
		) );
		
		if( is_array( $customer_orders ) && ! empty( $customer_orders )  ){
			$first_match = $customer_orders[0];
			$activeOrderID =  $first_match->ID;
		}
		
		return $returnObject ? $first_match : $activeOrderID;
	}
	
	/**
	* 
	*
	* @return void
	*/
	public static function gaad_create_order (  ) {
	
		$default_args = array(
			'status'        => 'awaiting-files',
			'customer_id'   => get_current_user_id(),
			'customer_note' => null,
			'parent'        => null,
			'created_via'   => null,
			'cart_hash'     => null,
			'order_id'      => gaad_ajax::get_active_order(),
		  );
		
		
		$current_user = get_user_meta( $default_args['customer_id'] );
		
		$billing_address = array(
            'first_name' => $current_user['billing_first_name'][0],
            'last_name'  => $current_user['billing_last_name'][0],
            'company'    => $current_user['billing_company'][0],
            'email'      => $current_user['billing_email'][0],
            'phone'      => $current_user['billing_phone'][0],
            'address_1'  => $current_user['billing_address_1'][0],
            'address_2'  => $current_user['billing_address_2'][0], 
            'city'       => $current_user['billing_city'][0],
            'state'      => $current_user['billing_state'][0],
            'postcode'   => $current_user['billing_postcode'][0],
            'country'    => $current_user['billing_country'][0]
        );
		$shipping_address = array(
            'first_name' => $current_user['shipping_first_name'][0],
            'last_name'  => $current_user['shipping_last_name'][0],
            'company'    => $current_user['shipping_company'][0],
            'email'      => $current_user['shipping_email'][0],
            'phone'      => $current_user['shipping_phone'][0],
            'address_1'  => $current_user['shipping_address_1'][0],
            'address_2'  => $current_user['shipping_address_2'][0], 
            'city'       => $current_user['shipping_city'][0],
            'state'      => $current_user['shipping_state'][0],
            'postcode'   => $current_user['shipping_postcode'][0],
            'country'    => $current_user['shipping_country'][0]
        );
		
		
		$product_id = $_POST[ 'product_id' ];
		$product_args = array( 
			'variation' => $_POST['variation_attr'],
			'totals' => $_POST[ 'totals' ], 
		);
		
		$product = new WC_product( $product_id );
		
		  
		$_max_files = isset( $_POST[ 'attribute_pa_max_files' ] ) ? $_POST[ 'attribute_pa_max_files' ] : $product->get_attribute( 'pa_max_files' );
		
		
		
	
		$product_args[ 'variation' ][ 'attribute_pa_item_status' ] = 'awaiting-files';
		$product_args[ 'variation' ][ 'ship_days' ] = $_POST[ 'shipment' ][ 'days' ];
		$product_args[ 'variation' ][ 'ship_date' ] = $_POST[ 'shipment' ][ 'date' ];
		$product_args[ 'variation' ][ 'attribute_pa_max_files' ] = (int)$_max_files < 0 ? 1 : (int)$_max_files;
				
		$order = wc_create_order( $default_args );
		$p = get_product( $product_id );
		
		$item_id = $order->add_product( $p, 1, $product_args );
		
		
		$order->set_address( $billing_address, 'billing' );
        $order->set_address( $shipping_address, 'shipping' );
		
		$order->calculate_totals();
		/*
		Dodawanie produktu do zamówienia 
		*/
		//gaad_ajax::gaad_order_add_product( $order, $p, $_POST['variation_attr'] );
		wp_send_json( $order );
		die();
	}
	
	
	/**
	 * Get a matching variation based on posted attributes.
	 */
	public static function gaad_get_variation() {
		ob_start();
		$calculator = new gaad_calc( $_POST['product_id'], $_POST['variation_id'] );
		if ( empty( $_POST['product_id'] ) || ! ( $variable_product = wc_get_product( absint( $_POST['product_id'] ), array( 'product_type' => 'variable' ) ) ) ) {
			die();
		}
		
		$variation_id = $variable_product->get_matching_variation( wp_unslash( $_POST ) );

		if ( $variation_id ) {
			$variation = $variable_product->get_available_variation( $variation_id );
		} else {
			$variation = false;
		}
		
		
		$calc_name = $calculator->get_calc_name();
		$variation['q'] = $calculator->get_all_quantites();
		$variation['from_gaad'] = true;
		
		
		wp_send_json( $variation );

		die();
	}
	
	
	
	
	
	
	
	
	
	
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_calculate_ (  ) {
		$calculator = new gaad_calc( $_POST['product_id'], $_POST['variation_id'] );
		$new_regular_price = $calculator->use_it();
		
		$r = array_merge( array(
			"product_id" => $_POST['product_id'],
			"variation_id" => $_POST['variation_id']			
		), $new_regular_price);
		
		$calc_name = $calculator->get_calc_name();
		//$r["variation_id"] = $calc_name::save_variation( $r );
		$r["variation_id"] = uniqid('var');
		
		$calc_obj = new $calc_name($_POST['product_id'], $_POST['variation_id'] );
		$r['q'] = $calc_obj->calc_all_quantites();
		
		
		wp_send_json( $r );
		
	}
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_calculate_ksiazka (  ) {
		$calculator = new gaad_calc( $_POST['product_id'], $_POST['variation_id'] );
		$new_regular_price = $calculator->use_it();
		//echo '<pre>'; echo var_dump( $new_regular_price ); echo '</pre>';
		$r = array_merge( array(
			"product_id" => $_POST['product_id'],
			"variation_id" => $_POST['variation_id']			
		), $new_regular_price);
		
		$calc_name = $calculator->get_calc_name();
		$r["variation_id"] = $calc_name::save_variation( $r );
		
		wp_send_json( $r );
		
	}
	
	
	/**
	* Funkcja powołuje obiekt gaad_calc
	* oblicza cenę wariantu 
	* wprowadza zmiany w bazie dancyh w wariancie
	*
	* może być dalej rozbudowywana o moduły liczące wagę, rozmiar paczki etc
	*
	* @return void
	*/
	function gaad_variation_calculate (  ) {
		$calculator = new gaad_calc( $_POST['product_id'], $_POST['variation_id'] );
		$new_regular_price = $calculator->use_it();
		
		$r = array_merge( array(
			"product_id" => $_POST['product_id'],
			"variation_id" => $_POST['variation_id'],
			//"_regular_price" => $new_regular_price['_regular_price']
		), $new_regular_price);
		
		
		update_post_meta( $_POST['variation_id'], '_regular_price', $new_regular_price['_regular_price'] );
		update_post_meta( $_POST['variation_id'], '_price', $new_regular_price['_regular_price'] );
		
		
		wp_send_json( $r );
	}
	
	/**
	* Pobiera podstawowe dane dotyczace wariantów produktu
	*
	* @return void
	*/	
	function gaad_get_product_variations (  ) {
		
		/*
		* Formatuje zebrane z bazy dancyh wartosci meta postu wariantu
		*/
		function format_post_meta ( $arr ) {
			$tmp = array();
			$attr = array();
			
			foreach( $arr as $k => $v ){
				if( preg_match('/attribute/', $k )  ){
					//$new_key = str_replace( "attribute_", "", $k);					
					$attr[ $k ] = $v[0];
				} else $tmp[ $k ] = $v[0];
			}
			
			$tmp['attributes'] = $attr;
			return $tmp;
		}
		
		/*
		* Pobranie wszystkich stworzonych wariacji
		*/		
		$args = array(
			'post_type'     => 'product_variation',
			'post_status'   => array( 'private', 'publish' ),
			'numberposts'   => -1,
			'orderby'       => 'menu_order',
			'order'         => 'asc',
			'post_parent'   => $_POST['product_id']
		);
		$variations = get_posts( $args ); 
		
		$r_var = array();
		foreach( $variations as $variation => $vdata ){			
			$product_variation = new WC_Product_Variation( $vdata->ID );			
			$r_var[] = array_merge( array( 'id' => $vdata->ID ), format_post_meta( get_post_meta( $vdata->ID ) ) );			
		}
		
		wp_send_json( $r_var );
	}
	
	
	
	
	/**
	* Beneruje interface na liscie produktów służący do startu generowania cen wariantów danego produktu
	*
	* @return void
	*/
	function gaad_add_variations_prices_UI (  ) {
		
		$product = new WC_product( $_POST['product_id'] );
		
		?>
		
		<tr id="edit-<?php echo $_POST['product_id']; ?>" class=" inline-edit-row inline-edit-row-post inline-edit-product quick-edit-row quick-edit-row-post inline-edit-product inline-editor">
			<td colspan="12" class="colspanchange">

			<h3><?php echo $product->post->post_title ?>: automatyzacja wyceniania wariantów produktu</h3>
			<form method="get"><table calss="variation_prices"><tbody id="inlineedit">
			
			<tr id="variations-list" class="inline-edit-row"><td colspan="12" class="colspanchange">
				
				
				
			</td></tr>
			
			<tr id="inline-edit" class="inline-edit-row"><td colspan="12" class="colspanchange">



			<p class="inline-edit-save submit">
				<button type="button" class="cancel button alignleft"><?php _e( 'Cancel' ); ?></button>
				<button type="button" class="add-variations-prices button button-primary alignright">Wyceń warianty</button>
				<span class="spinner"></span>
				<span class="error" style="display:none;"></span>

				<br class="clear" />
			</p>
			</td></tr>
			</tbody></table></form>
        
		</td> </tr>
        
	<?php

		die();
	}
	
	
		
	
	
	/**
	* 
	*
	* @return void
	*/
	static function get_file_id ( $file_name ) {
	
		$matches = array();
		preg_match( '/'.$item_id.'-([a-zA-Z0-9]+)-.*/', $file_name, $matches )[1];
		$file_id = $matches[1];
		if( isset( $file_id ) ){
			return $file_id;
		}
		return null;
	}
	
	
	
}

