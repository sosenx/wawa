<?php 
/*
* Oblicza cenę wariatu dla produktu wizytówki ( wersja ekonomiczna )
*/
class gaad__kartki_okolicznosciowe__calc extends gaad_calc {	
	 
	
	var $product_id;
	var $variation_id;
	var $product_cat_slug;
	
	
	function __construct( $product_id, $variation_id ){		
		$this->product_id = $product_id;
		$this->variation_id = $variation_id;
		$this->product_cat_slug = $this->get_category_slug();
		
		
		
	}
	
	
	/**
	* Ustawia filtry zmieniające parametry wyceny
	*
	* @return void
	*/
	function apply_filters (  ) { 
		/*
		* Wiele klas kalkulatora naklada te filtry, konieczne jest ich usunięcie, żeby wyniki ich działania się nie nakłądały na siebie
		*/
		
		//add_action( 'gaad_calc_class', __CLASS__ . '::set_calc_class', 10, 0 );	
		add_filter( 'gaad_calc_markup', __CLASS__ . '::set_markup', 10, 1 );		
		//add_filter( 'gaad_calc_piece_price', __CLASS__ . '::set_piece_price', 10, 1 );
		//add_filter( 'gaad_calc_pieces_per_sheet', __CLASS__ . '::set_pieces_per_sheet', 10, 1 );
	}
	 
	
	/**
	* Oblicza cene wariantu biorąc pod uwagę tylko podstawowe parametry
	*
	* @return void
	*/
	function calculate_variant_price ( ) { 
		
		$db = new gaad_db();
		
		/*
		* Wiele klas kalkulatora naklada te filtry, konieczne jest ich usunięcie, żeby wyniki ich działania się nie nakłądały na siebie
		*/		
		remove_all_filters( 'gaad_calc_markup', 10);
		remove_all_filters( 'gaad_calc_piece_price', 10);
		remove_all_filters( 'gaad_calc_pieces_per_sheet', 10);
		remove_all_filters( 'sheet_B3_print_price_array', 10);
		remove_all_filters( 'sheet_B3_folding_price_array', 10);
		remove_all_filters( 'sheet_B3_gold_price_array', 10);
		remove_all_filters( 'sheet_B3_rounding_corners_price', 10);
		 
		
		if( method_exists( $this, 'apply_filters') ){
			$this->apply_filters( $this->apply_filters_attr );
		} 
		
		/*
		* Warstwy produkcji, kolejne koszta, nakadane ne acay arkusz produkcyjny
		*/
		$price_parts = array(
			"sheet_price" => $this->sheet_B_price(),
			"print_price" => $this->sheet_B3_print_price(),
			"wrap_price" => $this->sheet_B3_wrap_price(),
			"folding_price" => $this->sheet_B3_folding_price(),
			"gold_price" => $this->sheet_B3_gold_price(),
			"rounding_corners_price" => $this->sheet_B3_rounding_corners_price()
			
		);
		
		$price_parts = apply_filters( 'gaad_calc_piece_price', $price_parts );
		
		/*
		* Suma kosztów produkcji jednego arkusza produkcyjnego $total_sheet_price
		*/
		$max = count( $price_parts );
		foreach( $price_parts as $k => $v ){
			$total_sheet_price += (float)$v;
		}
		
		
		/*
		* ilość użytkow wariantu
		*/
		$quantity = $this->get_quantity();
		
		/*
		* ilość użytków na arkuszu produkcyjnym
		*/
		$pieces_per_sheet = $this->get_pieces_per_sheet( );
		
		
		/*
		* ilość potrezbnych do produkcji wariantu arkuszy
		*/
		$sheets = $this->get_production_sheets();
		/* 
		*tablica przechowuje koszty poszczegolnycg faz produkcji arkusza produkcyjnego B3
		*/
		
		/*
		* Całkowity koszt bezpośredni produkcji wariantu
		*/
		$spotuv_price = $this->sheet_B3_spot_uv_price();
		$variant_production_cost = $sheets * $total_sheet_price + $spotuv_price;
		
		
		/*
		* Pobieranie marzy produktu
		*/
		
		$_markup = apply_filters( 'gaad_calc_markup', $this->get_markup(), $this );
		
		/*
		* Obliczanie ceny wariantu
		*/
		$express_price_multiplier = $this->express_price_multiplier();
		$price = $variant_production_cost * $_markup * $express_price_multiplier;
		
		
		
		echo '<pre>'; echo var_dump( 'jestem' ); echo '</pre>';
		
		
		/*
		* Zaookrąglanie ceny do formatu xx.99		
		*/
		if( !$this->raw_price ){
			$price = $this->gaad_round( $price ); 
		}
		
		return array_merge( array(
				'quantity' => $quantity, 
				'pieces_per_sheet' => $pieces_per_sheet, 
				'sheets' => $sheets, 
				'spotuv_price' => $spotuv_price, 
				'variant_production_cost' => $variant_production_cost,
				'markup' => $_markup,
				'express_price_multiplier' => $express_price_multiplier,
				'production_format' => $this->best_production_format, 
				'_regular_price' => $price,
				'post_data' => $_POST, 
			), $price_parts, is_array( $GLOBALS[ 'calc_data' ] ) ? $GLOBALS[ 'calc_data' ] : array() );
	}
	
	
	
	
	
	
	/**
	* 
	*
	* @return void
	*/
	function set_calc_class () {	
		$GLOBALS[ 'calc_data' ][ 'calc_class' ] = __CLASS__;
	}
	
	/**
	* 
	*
	* @return void
	*/
	function set_piece_price ( $price ) {
		return $price;
	}
	
	
	/**
	* Zwraca marżę handlową
	*
	* @return void
	*/
	public static function set_markup ( $quantity ) {
		$quantity = gaad_calc::get_quantity();
		$markup = 1;		
		if($quantity >= 10 ) $markup = 30;
		if($quantity >= 20 ) $markup = 28;
		if($quantity >= 30 ) $markup = 25;
		if($quantity >= 40 ) $markup = 20;
		if($quantity >= 50 ) $markup = 15;
		if($quantity >= 100 ) $markup = 10;
		if($quantity >= 250 ) $markup = 5;
		
		return $markup;
	}
	
	
	
}
