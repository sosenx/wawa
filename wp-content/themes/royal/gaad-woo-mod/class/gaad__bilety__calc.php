<?php 
/*
* Oblicza cenę wariatu dla produktu wizytówki ( wersja ekonomiczna )
*/
class gaad__bilety__calc extends gaad_calc {	
	
	var $product_id;
	var $variation_id;
	var $product_cat_slug;
	var $use_top_level_discount = true;
	
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
		
		add_filter( 'gaad_calc_markup', __CLASS__ . '::set_markup', 10, 1 );		
		add_filter( 'gaad_calc_piece_price', __CLASS__ . '::set_piece_price', 10, 1 );
		add_filter( 'gaad_calc_pieces_per_sheet', __CLASS__ . '::set_pieces_per_sheet', 10, 1 );
	}
	
	
	/**
	* 
	*
	* @return void
	*/
	function set_pieces_per_sheet ( $pieces_per_sheet ) {		
		$pieces_per_sheet['145x50'] = 10;
		return $pieces_per_sheet;
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
		if($quantity <= 50 ) $markup = 25;
		if($quantity <= 100 ) $markup = 22;
		if($quantity <= 200 ) $markup = 20;
		if($quantity <= 300 ) $markup = 15;
		if($quantity <= 400 ) $markup = 14;
		if($quantity <= 500 ) $markup = 8;
		if($quantity <= 1000 ) $markup = 4;
		
		return $markup;
		
	}
	
	
	
}
