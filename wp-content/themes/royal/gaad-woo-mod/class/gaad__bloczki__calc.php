<?php 
/*
* Oblicza cenę wariatu dla produktu wizytówki ( wersja ekonomiczna )
*/
class gaad__bloczki__calc extends gaad_calc {	
	
	var $product_id;
	var $variation_id;
	var $product_cat_slug;
	var $post;
	var $apply_filters_attr;
	var $best_production_format;
	var $tmp;
	
	function __construct( $product_id, $variation_id ){		//
		$this->product_id = $product_id;
		$this->variation_id = $variation_id;
		$this->product_cat_slug = $this->get_category_slug();
		$this->set_calc_data();		
		
	}	
	
	
	
	
	/**
	* Funkcja nadpisuje funkcję rodzica, 
	*  -- Oblicza ceny wszystkich zdeklarowanych w atrybucie naklad ilosci 
		++ ustala granice marży i generuje listę nakladów większych niż podany przez usera
	*
	* @return void
	*/
	function calc_all_quantites (  ) {
		$tmp = array();
		
		$product = new WC_Product( $_POST['post_data']['product_id'] );		
		//$pa_naklad = wc_get_product_terms( $product->id, 'pa_naklad', array( 'fields' => 'slugs' ) );
		
		$_markup = new gaad_markup( 'qlist', $quantity, $_POST["post_data"]["product_id"] );
		$pa_naklad = $_markup->get_qlist();
		
		
		
		foreach( $pa_naklad as $c => $naklad ){
			$quantity_match = array();		
			preg_match("/(\d*)-szt/", $naklad, $quantity_match);
			
			
			if( isset( $quantity_match[1] ) ){				
				$_POST['post_data']["attribute_pa_termin-wykonania"] = 'termin-1';
				$_POST['post_data']["attribute_pa_naklad"] =  (int) $quantity_match[1] . '-szt';
				$calculation = $this->calculate_variant_price();
				
				$r = array_merge( array(
					"product_id" => $_POST['product_id']					
				), $calculation);								
				//$r["variation_id"] = $this->save_variation( $r );
				$r["variation_id"] = uniqid('var');
				
				
				$_POST['post_data']["attribute_pa_termin-wykonania"] = 'termin-1-5';
				$express = $this->calculate_variant_price();
				
				
				
				$express_variation_id = uniqid('var');
				
				
				$r["express"] = $express;
				$r["express"]["variation_id"] = $express_variation_id;
				
				
				
				
				
				$tmp[] = $r;
				
			}
			
			
		}
		
		
		return $tmp;
	}
	
	
	
	
	
	
	
	
	
	
	/**
	* 
	*
	* @return void
	*/
	function variation_exists ( ) {
		$variation_attr_array = gaad__bloczki__calc::get_variation_attr_array();
		
		$variable_product = wc_get_product( $_POST['product_id'] );
		$variation_id = $variable_product->get_matching_variation( wp_unslash( $variation_attr_array ) );
		
		return $variation_id ? $variation_id : false;
	}
	
	/*
	*
	*/
	function save_variation(){
		
		$variation_id = gaad__bloczki__calc::variation_exists();
		if( !$variation_id ){
			
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
			$variations_counter = count( $variations ) + 1;
			
			
			
			//tworzenie posta będącego wariacją produktu
			$variation_post = array(
				'post_title'=> 'Variation',
				'post_name' => 'product-' . $_POST['product_id'] . '-variation-' . $variations_counter,
				'post_status' => 'publish',
				'post_parent' => $_POST['product_id'], //post is a child post of product post
				'post_type' => 'product_variation', //set post type to product_variation
				'guid'=> home_url() . '/?product_variation=product-' . $_POST['product_id'] . '-variation-' . $variations_counter
				);

				//Insert ea. post/variation into database:
			$variation_id = wp_insert_post( $variation_post );
			$variation_attr_array = gaad__bloczki__calc::get_variation_attr_array();
			//post meta
			foreach( $variation_attr_array as $name => $value ) {
				update_post_meta( $variation_id, $name, $value );				
			}
			
		} 
		
		return $variation_id;
	}
		
	/**
	* tworzy tablicę atrybutów wariantu
	*
	* @return void
	*/
	function get_variation_attr_array ( ) {
		// $_POST["post_data"], $GLOBALS['post_calc_data_tmp'], $GLOBALS['post_calc_data']
		$attr = array(
			"quantity"
			);
		$r = array();
		$post = $_POST["post_data"];
		
		foreach( $post as $k => $v ){
			if( preg_match('/^attribute_pa_/', $k) ){
				$r[ $k ] = $v;				
			}
			
			if( in_array( $k, $attr ) ){
				$r[ 'attribute_pa_' . $k ] = $v;				
			}
			
			unset( $post[ $k ] );			
		}
		
		if( is_array( $GLOBALS['post_calc_data_tmp'] ) ){
			foreach( $GLOBALS['post_calc_data_tmp'] as $k => $v ){
				if( preg_match('/^_/', $k) ){
					$r[ $k ] = $v;				
				}
			}
		}		
			
		
		return $r;
	}
	
	/*
	*
	* Pozyskanie atrybutów bloku kolorowego i zapisanie ich tablicy z odpowiednimi indeksami
	*/
	function parse_color_attr(){
		$termin = (float)str_replace( array( "termin-", "-"), array( "", "." ), $_POST['post_data']["attribute_pa_termin-wykonania"] );
		$termin = $termin == 0 ? 1 : $termin;
		
		$ilosc_kart = (int)str_replace( '-szt', '', ( $_POST["post_data"]['attribute_pa_ilosc-kart']) );
		$is_color = filter_var( preg_match( '/kolor/', $_POST["post_data"]["attribute_pa_zadruk"]), FILTER_VALIDATE_BOOLEAN );
		
		$ilosc_kart = $is_color ? $ilosc_kart : 0;
		
		
		return array(
			'attribute_pa_podloze' => $_POST["post_data"]["attribute_pa_podloze"],
			'attribute_pa_uszlachetnienie' => $_POST["post_data"]["attribute_pa_uszlachetnienie"],			
			'attribute_pa_zadruk' => $_POST["post_data"]["attribute_pa_zadruk"],
			
			/*
			* Atrybuty wspólne
			*/
			'attribute_pa_termin-wykonania' => $termin, 
			'attribute_pa_naklad' => (int)$ilosc_kart . '-szt',
			'attribute_pa_format' => $_POST["post_data"]["attribute_pa_format"],
			
			/*
			* Atrybuty szczególowe dla koloru
			*/
			'attribute_pa_porozrzucane-str-kolor' => $_POST[ 'attribute_pa_porozrzucane-str-kolor' ] 
			
			
		);
	}
	
	/*
	* 
	* Pozyskanie atrybutów bloku czarno-białęgo i zapisanie ich tablicy z odpowiednimi indeksami
	*/
	function parse_bw_attr(){
		$termin = (float)str_replace( array( "termin-", "-"), array( "", "." ), $_POST['post_data']["attribute_pa_termin-wykonania"] );
		$termin = $termin == 0 ? 1 : $termin;
		
		$ilosc_kart = (int)str_replace( '-szt', '', ( $_POST["post_data"]['attribute_pa_ilosc-kart']) );
		$is_bw = filter_var( preg_match( '/czarno/', $_POST["post_data"]["attribute_pa_zadruk"]), FILTER_VALIDATE_BOOLEAN );
		
		$ilosc_kart = $is_bw ? $ilosc_kart : 0;
		
		
		
		return array(
			'attribute_pa_podloze' => $_POST["post_data"]["attribute_pa_podloze"],
			'attribute_pa_uszlachetnienie' => 'folia-brak',
			
			
			'attribute_pa_zadruk' => $_POST["post_data"]["attribute_pa_zadruk"],
			
			/*
			* Atrybuty wspólne
			*/
			'attribute_pa_termin-wykonania' => $termin, 
			'attribute_pa_naklad' => (int)$ilosc_kart . '-szt',		
			'attribute_pa_format' => $_POST["post_data"]["attribute_pa_format"]
			
		);
	}
	
	/*
	*
	* Pozyskanie atrybutów bloku czarno-białęgo i zapisanie ich tablicy z odpowiednimi indeksami
	*/
	function parse_cover_attr(){
		$termin = (float)str_replace( array( "termin-", "-"), array( "", "." ), $_POST['post_data']["attribute_pa_termin-wykonania"] );
		$termin = $termin == 0 ? 1 : $termin;
		return array(
			'attribute_pa_podloze' => $_POST["post_data"]["attribute_pa_papier-okladki"],
			'attribute_pa_uszlachetnienie' => $_POST["post_data"]["attribute_pa_uszlachetnienie-okladki"],
			
			
			'attribute_pa_zadruk' => $_POST["post_data"]["attribute_pa_zadruk-okladki"],
			
			/*
			* Atrybuty wspólne
			*/
			'attribute_pa_termin-wykonania' => $termin,  
			'attribute_pa_naklad' => (int)str_replace( '-szt', '', $_POST["post_data"]['attribute_pa_naklad']) . '-szt',		
			'attribute_pa_format' => $_POST["post_data"]["attribute_pa_format"] 
			
			
		);
	}

	/*
	* Główna funkcja licząca
	* Kalkulowanie ceny wariantu
	**/
	function calculate_variant_price() {
		
		$db = new gaad_db();
		
		/*
		* Wyłącza zaokrąglanie cen do formatu xx.99
		*/
		$this->raw_price = true;
		
		/* okładka */
		
		$variant_production = array();
		
			/*
			* Parsowanie podstawowych atrybutów okładki
			* Część z nich jest narzucona przez specyfikę produktu np.: nie drukuje się okładki notesu po obu stronach
			*/
			$okladka_parts = $this->parse_cover_attr();
			$this->apply_filters_attr = 'cover' ;
			$GLOBALS['post_calc_data'] = array( 'production_formats' => $_POST["production_formats"] );
			foreach( $okladka_parts as $k => $v ){
				$GLOBALS['post_calc_data'][ $k ] = $okladka_parts[ $k ];
			}
			
			
			$okladka_calculation = parent::calculate_variant_price();
			$variant_production['cover'] = $okladka_calculation;
		
		
			/*
			* Parsowanie podstawowych atrybutów bloku czarno-białego			
			*/
			$this->apply_filters_attr = 'bw';
			$bw_parts = $this->parse_bw_attr();
			$GLOBALS['post_calc_data'] = array( 'production_formats' => $_POST["production_formats"] );
			foreach( $bw_parts as $k => $v ){
				$GLOBALS['post_calc_data'][ $k ] = $bw_parts[ $k ];
			}
			$bw_calculation = parent::calculate_variant_price();
			$variant_production['bw'] = $bw_calculation;
		
		
			/*
			* Parsowanie podstawowych atrybutów bloku kolorowego
			*/
			$this->apply_filters_attr = 'color';
			$color_parts = $this->parse_color_attr();
			$GLOBALS['post_calc_data'] = array( 'production_formats' => $_POST["production_formats"] );
			foreach( $color_parts as $k => $v ){
				$GLOBALS['post_calc_data'][ $k ] = $color_parts[ $k ];
			}
			$color_calculation = parent::calculate_variant_price();
			$variant_production['color'] = $color_calculation;
		
		
		/*
		* ilość użytkow wariantu
		*/		
		$quantity = (int)str_replace( '-szt', '', $_POST["post_data"]["attribute_pa_naklad"] );
		/*
		* Całkowite koszty produkcji nakładu
		*/
		$total_price = array(
			'cover' => $variant_production['cover']["_regular_price"] / $quantity,
			'cover_type' => $db->cover_type_price[ $_POST["post_data"]["attribute_pa_oprawa"] ],
			'bw' => $variant_production['bw']["_regular_price"],
			'color' => $variant_production['color']["_regular_price"]			
		);
		
		/*
		* Elementy wpływające na koszta produkcji egzemplarza
		*/
		
			/*
			* Podwojenie wartości druku koloru jeżeli strony kolorowe są porozrzucane
			*/
			if( filter_var( $_POST["post_data"]["attribute_pa_porozrzucane-str-kolor"], FILTER_VALIDATE_BOOLEAN ) ){
				$total_price[ 'mixed_color' ] = $total_price['color'];
			}
		
			/*
			* Obwoluta
			*/
			if( filter_var( $_POST["post_data"]["attribute_pa_obwoluta"], FILTER_VALIDATE_BOOLEAN ) ){
				$total_price['jacket'] = $variant_production['cover']["sheet_price"] + $variant_production['cover']["print_price"];				
			}
		
			/*
			* Skrzydełka, uproszczone liczenie poprzed podwojenie kosztów produkcyjnych okladki
			*/
			if( $_POST["post_data"]["attribute_pa_rodzaj-okladki"] == 'ze-skrzydelkami'){
				$total_price['covel_long'] = $variant_production['cover']["sheet_price"] + $variant_production['cover']["print_price"];				
			}
			/*
			* Lakier wybiórczy okładki
			*/
			if( filter_var( $_POST["post_data"]["attribute_pa_lakier-wybiorczy-okladki"], FILTER_VALIDATE_BOOLEAN ) ){
				
				$uv_sheets_pallets = (int)($quantity / $db->uv_sheets_per_pallet) + 1;
				$uv_pallete_price = $db->uv_pallete_price;
				
				$total_price['spot_wrap'] = $uv_sheets_pallets * $uv_pallete_price / $quantity;
			}
		
			/*
			* Pakowanie egzemplarzy w folię
			*/
			if( filter_var( $_POST["post_data"]["attribute_pa_pakowanie-w-folie"], FILTER_VALIDATE_BOOLEAN ) ){
				$total_price['plastic_wrap'] = $db->addons['plastic_wrap'];				
			}
		
			/*
			* Pakowanie egzemplarzy w folię
			*/
			if( filter_var( $_POST["post_data"]["attribute_pa_wiercenie-otworow"], FILTER_VALIDATE_BOOLEAN ) ){
				$total_price['drilling_holes'] = $db->addons['drilling_holes'];				
			}
		
		
		/*
		* Pobieranie marzy produktu
		*/
		$_markup = apply_filters( 'gaad_calc_markup', $this->get_markup(), $this );
		
		/*
		* Suma kosztów produkcji 
		*/
		$max = count( $total_price );
		$_price = 0;
		foreach( $total_price as $k => $v ){
			$_price += (float)$v;
		}
		$total_price_layers_only = $total_price;
		$total_price['_piece_price'] = round( $_price, 2 );
		$total_price['_markup'] = $_markup;
		$total_price['_quantity'] = $quantity;
		
		
		$total_price['_regular_price'] = $this->gaad_round( $this->calculatePriceAfterDiscount( $_price * $quantity * $_markup ) );
		
		
		$total_price['_price'] = $total_price['_regular_price'];
		
		$variant_production['totals'] = $this->total_variant_production( $total_price_layers_only, $variant_production , $quantity, $_price );
		
		return $GLOBALS['post_calc_data_tmp'] = array_merge( $total_price, array( 
			'variant_production' => $variant_production,
			'variation_attr' => gaad__bloczki__calc::get_variation_attr_array()
			) );
		
	}
	
	
	function set_pieces_per_sheet( $pieces_per_sheet, $calc_obj ){
		//echo '<pre>'; echo var_dump($calc_obj->apply_filters_attr); echo '</pre>';
		if( $calc_obj->apply_filters_attr == 'cover' ){
					
				$pieces_per_sheet[ str_replace( 'mm', '', $_POST["post_data"]["attribute_pa_format"] ) ] = 1;//(int)$best_format["counter"];		
				return $pieces_per_sheet;	
			
		} else {
			/*
			* Pobranie ilosci użytków na arkuszu produkcyjnym
			*/
			
			
			if( is_array( $GLOBALS['post_calc_data']["production_formats"] ) && !empty( $GLOBALS['post_calc_data']["production_formats"] ) ){
				$best_format = array_shift( $GLOBALS['post_calc_data']["production_formats"] );			
				$pieces_per_sheet[ str_replace( 'mm', '', $_POST["post_data"]["attribute_pa_format"] ) ] = (int)$best_format["counter"];			
			}
		}
		
		return $pieces_per_sheet;		
	}
	
	
	/**
	* Ustawia filtry zmieniające parametry wyceny
	*
	* @return void
	*/
	function apply_filters ( $apply_filters_attr ) { 
		/*
		* Wiele klas kalkulatora naklada te filtry, konieczne jest ich usunięcie, żeby wyniki ich działania się nie nakłądały na siebie
		*/
		add_filter( 'gaad_calc_markup', __CLASS__ . '::set_markup', 10, 2 );	
		add_filter( 'gaad_calc_pieces_per_sheet', __CLASS__ . '::set_pieces_per_sheet', 10, 2 );
		//add_filter( 'gaad_calc_piece_price', __CLASS__ . '::set_piece_price', 10, 1 );		
	}
	
	
	/**
	* Zwraca marżę handlową
	*
	* @return void
	*/
	public static function set_markup ( $markup, $calc_obj ) {
		
		$quantity = (int)str_replace( '-szt', '', $_POST["post_data"]["attribute_pa_naklad"] );
		
		
		if( $calc_obj->apply_filters_attr == 'cover' ){
			$_markup = new gaad_markup( 'cover', $quantity, $_POST["post_data"]["product_id"] );
			$markup = $_markup->get_markup();
			
		}
		
		if( $calc_obj->apply_filters_attr == 'bw' ){
			$_markup = new gaad_markup( 'bw', $quantity, $_POST["post_data"]["product_id"] );
			$markup = $_markup->get_markup();
			
		}
		
		if( $calc_obj->apply_filters_attr == 'color' ){
			$_markup = new gaad_markup( 'color', $quantity, $_POST["post_data"]["product_id"] );
			$markup = $_markup->get_markup();
			
		}
		
		
		return $markup;
		
	}
	
	
	
}
