<?php 
/*
* Oblicza cenę wariatu dla produktu plakaty 
*/
class gaad__plakaty__calc extends gaad_calc {	
	
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
		
		//$pieces_per_sheet['90x50'] = 10;
		return $pieces_per_sheet;
	}
	
	/**
	* 
	*
	* @return void
	*/
	function set_piece_price ( $price ) {
		//$price[] = 1;
		return $price;
	}
	
	
	/**
	* Zwraca marżę handlową
	*
	* @return void
	*/
	public static function set_markup ( $quantity ) {
			
		switch($quantity){
			case 10 : $markup = 10; break;
			case 25 : $markup = 4; break;
			case 50 : $markup = 3; break;
			case 100 : $markup = 2.5; break;
			
			default : $markup = 5; break;
		}
		
		return $markup;
		
	}
	
	
	
}
