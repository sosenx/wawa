<?php 
/*
* 
*/
class gaad__outdoor_baner__calc extends gaad_calc {	
	
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
	* 
	*
	* @return void
	*/
	function variation_exists ( ) {
		$variation_attr_array = gaad__outdoor_baner__calc::get_variation_attr_array();
		
		$variable_product = wc_get_product( $_POST['product_id'] );
		$variation_id = $variable_product->get_matching_variation( wp_unslash( $variation_attr_array ) );
		
		return $variation_id ? $variation_id : false;
	}
	
	/*
	*
	*/
	function save_variation(){
		
		$variation_id = gaad__outdoor_baner__calc::variation_exists();
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
			$variation_attr_array = gaad__outdoor_baner__calc::get_variation_attr_array();
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
				
		foreach( $GLOBALS['post_calc_data_tmp'] as $k => $v ){
			if( preg_match('/^_/', $k) ){
				$r[ $k ] = $v;				
			}
	 	}
		
		return $r;
	}
	
	/*
	*
	* Pozyskanie atrybutów bloku czarno-białęgo i zapisanie ich tablicy z odpowiednimi indeksami
	*/
	function parse_color_attr(){
		
		return array(
			'attribute_pa_podloze' => $_POST["post_data"]["attribute_pa_papier-kolor"],
			'attribute_pa_uszlachetnienie' => 'folia-brak',
			
			
			'attribute_pa_zadruk' => $_POST["post_data"]["attribute_pa_zadruk-strony-kolorowe"],
			
			/*
			* Atrybuty wspólne
			*/
			'attribute_pa_termin-wykonania' => 1, 
			'attribute_pa_naklad' => (int)str_replace( '-szt', '', $_POST["post_data"]['attribute_pa_ilosc-stron-kolorowych']) . '-szt',		
			'attribute_pa_format' => $_POST["post_data"]["attribute_pa_format"]
			
		);
	}
	
	/*
	*
	* Pozyskanie atrybutów bloku czarno-białęgo i zapisanie ich tablicy z odpowiednimi indeksami
	*/
	function parse_bw_attr(){
		
		return array(
			'attribute_pa_podloze' => $_POST["post_data"]["attribute_pa_papier-czarno-bialy"],
			'attribute_pa_uszlachetnienie' => 'folia-brak',
			
			
			'attribute_pa_zadruk' => $_POST["post_data"]["attribute_pa_zadruk-strony-czarno-biale"],
			
			/*
			* Atrybuty wspólne
			*/
			'attribute_pa_termin-wykonania' => 1, 
			'attribute_pa_naklad' => (int)str_replace( '-szt', '', $_POST["post_data"]['attribute_pa_ilosc-stron-czarno-bialych']) . '-szt',		
			'attribute_pa_format' => $_POST["post_data"]["attribute_pa_format"]
			
		);
	}
	
	/*
	*
	* Pozyskanie atrybutów bloku czarno-białęgo i zapisanie ich tablicy z odpowiednimi indeksami
	*/
	function parse_cover_attr(){
		
		return array(
			'attribute_pa_podloze' => $_POST["post_data"]["attribute_pa_papier-okladki"],
			'attribute_pa_uszlachetnienie' => $_POST["post_data"]["attribute_pa_uszlachetnienie-okladki"],
			
			
			'attribute_pa_zadruk' => $_POST["post_data"]['attribute_pa_zadruk-okladki'],
			
			/*
			* Atrybuty wspólne
			*/
			'attribute_pa_termin-wykonania' => 1, 
			'attribute_pa_naklad' => (int)str_replace( '-szt', '', $_POST["post_data"]['attribute_pa_naklad']) . '-szt',		
			'attribute_pa_format' => $_POST["post_data"]["attribute_pa_format"]
			
		);
	}

	/*
	* Główna funkcja licząca
	* Kalkulowanie ceny wariantu
	**/
	function calculate_variant_price() {
		
		return 123;
		
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
			if( filter_var( $_POST["post_data"]["porozrzucane-str-kolor"], FILTER_VALIDATE_BOOLEAN ) ){
				$total_price[ 'mixed_color' ] = $total_price['color'];
			}
		
			/*
			* Obwoluta
			*/
			if( filter_var( $_POST["post_data"]["obwoluta"], FILTER_VALIDATE_BOOLEAN ) ){
				$total_price['jacket'] = $variant_production['cover']["sheet_price"] + $variant_production['cover']["print_price"];				
			}
		
			/*
			* Skrzydełka, uproszczone liczenie poprzed podwojenie kosztów produkcyjnych okladki
			*/
			if( $_POST["post_data"]["pa_rodzaj-okladki"] == 'ze-skrzydelkami'){
				$total_price['covel_long'] = $variant_production['cover']["sheet_price"] + $variant_production['cover']["print_price"];				
			}
		
			if( filter_var( $_POST["post_data"]["attribute_pa_lakier-wybiorczy-okladki"], FILTER_VALIDATE_BOOLEAN ) ){
				
				$uv_sheets_pallets = (int)($quantity / $db->uv_sheets_per_pallet) + 1;
				$uv_pallete_price = $db->uv_pallete_price;
				
				$total_price['spot_wrap'] = $uv_sheets_pallets * $uv_pallete_price / $quantity;
			}
		
		/*
		* Pobieranie marzy produktu
		*/
		$_markup = apply_filters( 'gaad_calc_markup', $this->get_markup() );
		
		/*
		* Suma kosztów produkcji 
		*/
		$max = count( $total_price );
		$_price = 0;
		foreach( $total_price as $k => $v ){
			$_price += (float)$v;
		}
		
		$total_price['_piece_price'] = round( $_price, 2 );
		$total_price['_markup'] = $_markup;
		$total_price['_quantity'] = $quantity;
		$total_price['_regular_price'] = $this->gaad_round( $this->calculatePriceAfterDiscount( $_price * $quantity * $_markup ) );
		$total_price['_price'] = $total_price['_regular_price'];
		
		
		return $GLOBALS['post_calc_data_tmp'] = array_merge( $total_price, array( 
			'variant_production' => $variant_production,
			'variation_attr' => gaad__outdoor_baner__calc::get_variation_attr_array()
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
	
	
	'markup' => array( 
		'bw' => array(
			'0' => array(
				'od' => 0,
				'do' => 0
			),
			'10' => array(
				'od' => 1,
				'do' => 9
			),
			'5' => array(
				'od' => 10,
				'do' => 24
			),
			'4' => array(
				'od' => 25,
				'do' => 49
			),
			'2.5' => array(
				'od' => 50,
				'do' => 99
			),
			'2' => array(
				'od' => 100,
				'do' => 149
			),
			'1.9' => array(
				'od' => 150,
				'do' => 199
			),
			'1.8' => array(
				'od' => 200,
				'do' => 299
			),
			'1.7' => array(
				'od' => 300,
				'do' => 499
			),
			'1.5' => array(
				'od' => 500,
				'do' => 699
			),
			'1.4' => array(
				'od' => 700,
				'do' => 1000
			),
			'1.38' => array(
				'od' => 1000,
				'do' => 1000000
			)
		),
		
		'color' => array(
		
		   '7' => array(
			   'od' => 1,
			   'do' => 9
		   ),
		   '4.5' => array(
			   'od' => 10,
			   'do' => 24
		   ),   
		   '4' => array(
			   'od' => 25,
			   'do' => 49
		   ),   
		   '3' => array(
			   'od' => 50,
			   'do' => 99
		   ),
		   '2' => array(
			   'od' => 100,       
			   'do' => 199
		   ),
		   '1.9' => array(
			   'od' => 200,
			   'do' => 299
		   ),
		   '1.8' => array(
			   'od' => 300,
			   'do' => 10000000
		   )
		)
		
	)
	
	
	* @return void
	*/
	public static function set_markup ( $markup, $calc_obj ) {
		
		$quantity = (int)str_replace( '-szt', '', $_POST["post_data"]["attribute_pa_naklad"] );
		
		if( $calc_obj->apply_filters_attr == 'cover' ){
		$markup = 2;	
		}
		
		if( $calc_obj->apply_filters_attr == 'bw' ){
			$markup = 3;
		}
		
		if( $calc_obj->apply_filters_attr == 'color' ){
			$markup = 4;
		}
		
		
		
		/*
		if($quantity >= 25 ) $markup = 16;
		if($quantity >= 50 ) $markup = 10;
		if($quantity >= 100 ) $markup = 6.4;
		if($quantity >= 250 ) $markup = 3.2;
		if($quantity >= 500 ) $markup = 1.9;
		if($quantity >= 1000 ) $markup = 1.5;
		if($quantity >= 2500 ) $markup = 1.4;
		
		*/
		return $markup;
		
	}
	
	
	
}
