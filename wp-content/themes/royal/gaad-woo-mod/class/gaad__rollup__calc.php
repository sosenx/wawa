<?php

/*
* 
*/
class gaad__rollup__calc extends gaad_calc {	
	
	var $product_id;
	var $variation_id;
	var $product_cat_slug;
	var $post;
	var $apply_filters_attr;
	var $best_production_format;
	var $tmp;
	
	function __construct( $product_id, $variation_id ){	
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
	function print_price (  ) {
		$db = new gaad_db();
		$format = $this->recalculate_format_array( 'm' );
		$square =  $format[1] * $format[2];
		$solvent_media_price_array = apply_filters( 'solvent_media_price_array', $db->solvent_media_price_array );		
		$price = $solvent_media_price_array[ $this->get_medium() ][ 'print' ];
		return $square * $price;
	}
	
	/**
	* 
	*
	* @return void
	*/
	function sheet_price (  ) {
		$db = new gaad_db();
		$format = $this->recalculate_format_array( 'm' );
		$square =  $format[1] * $format[2];
		$solvent_media_price_array = apply_filters( 'solvent_media_price_array', $db->solvent_media_price_array );		
		$price = $solvent_media_price_array[ $this->get_medium() ][ 0 ];
			
		return $square * $price;		
	}
	
	
	/**
	* Pobiera cenę oraz marżuje akcesorium dodawane do produktu
	*
	* @return void
	*/
	function accesories_price (  ) {
		$db = new gaad_db();
		$accesories = new gaad_accesories();
		$markup_tag = gaad_ajax::gaad_get_markup_tag();
		$markup_tag = isset( $markup_tag ) ? $markup_tag : 'default';
		$class = __CLASS__;
		
		$calc_class = gaad_ajax::gaad_get_calc_class();
		$quantity = (int)str_replace( '-szt', '', $_POST["post_data"]["attribute_pa_naklad"] );
		$format = $class::recalculate_format_array( 'm', $class::get_format_array() );
				
		$acc_price = (float)$accesories->acc_db[ $calc_class ][$markup_tag][ $format[1] * 100 ][ $GLOBALS['post_calc_data']["attribute_pa_rodzaj-kasety"] ];
		
		$_markup = new gaad_markup( 'accesories', $quantity, $_POST["post_data"]["product_id"] );
		$markup = $_markup->get_markup();		
		$markup = !is_null( $markup ) ? $markup : 2;
		
		
		//echo '<pre>'; echo var_dump( $acc_price, $markup, $acc_price * $markup); echo '</pre>';
		
		return $acc_price * $markup;		
	}

	
	/*
	* Główna funkcja licząca
	* Kalkulowanie ceny wariantu
	**/
	function calculate_variant_price() {
		/*
		* Wyłącza zaokrąglanie cen do formatu xx.99
		*/
		$this->raw_price = true;
		$GLOBALS['post_calc_data'] = $_POST['post_data'];		
		$db = new gaad_db();
		
		
		/*
		* Wiele klas kalkulatora naklada te filtry, konieczne jest ich usunięcie, żeby wyniki ich działania się nie nakłądały na siebie
		*/		
		remove_all_filters( 'gaad_calc_markup', 10);
		remove_all_filters( 'gaad_calc_piece_price', 10);		
		remove_all_filters( 'solvent_print_price_array', 10);
		
		
		if( method_exists( $this, 'apply_filters') ){
			$this->apply_filters( $this->apply_filters_attr );
		} 
		
		$variant_production = array();
		
		$total_price = array(			
			"medium_price" => $this->sheet_price(),
			"print_price" => $this->print_price(),
			
			
		);	
		
		$class = __CLASS__;
		$format = $class::recalculate_format_array( 'm', $class::get_format_array() ); 
		$square =  $format[1] * $format[2];
		
		/*
		* ilość użytkow wariantu
		*/		
		$quantity = (int)str_replace( '-szt', '', $_POST["post_data"]["attribute_pa_naklad"] );
		$quantity = $quantity == 0 ? 1 : $quantity;
		
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
		
		/*
		* Obliczanie ceny wariantu
		*/
		$express_price_multiplier = (float) $this->express_price_multiplier();
		$accesories_price = $this->accesories_price();
		
		$total_price['_piece_price'] = round( $_price, 2 );
		$total_price['square'] = $square * $quantity;
		$total_price['_markup'] = $_markup;
		$total_price['_quantity'] = $quantity;
		$total_price['_accesories_price'] = $accesories_price;
		$total_price['_price_multiplier'] = $express_price_multiplier;
		
		$total_price['_regular_price'] = $this->gaad_round( $this->calculatePriceAfterDiscount( $_price * $quantity * $_markup * $express_price_multiplier + $accesories_price ) );
		
		
		$total_price['_price'] = $total_price['_regular_price'];
		$total_price['square_price'] = $total_price['_regular_price'] / $total_price['square'];
		 
		$total_price['variation_id'] = uniqid('var');
		
		return $GLOBALS['post_calc_data_tmp'] = array_merge( $total_price, array( 
			'variant_production' => $variant_production,
			'variation_attr' => gaad__rollup__calc::get_variation_attr_array(),
			
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
	public static function set_markup ( $markup ) {
		$db = new gaad_db();
		$quantity = (int)str_replace( '-szt', '', $_POST["post_data"]["attribute_pa_naklad"] );
		$class = __CLASS__;
		$format = $class::recalculate_format_array( 'm', $class::get_format_array() );
		$square =  $format[1] * $format[2];
		$total_area = $square * $quantity;
		
		$markup = 2;
		
		if( $total_area < 1 ){
			$markup = 6;
		} else {
			$_markup = new gaad_markup( 'medium', $quantity, $_POST["post_data"]["product_id"] );
			$markup = $_markup->get_markup();
			$markup = !is_null( $markup ) ? $markup : 2;
		}
		
		
		
		
		return $markup;
		
	
	}
	
	
	
}
