<?php 
/*
* Oblicza cenę wariatu dla produktu bizneskarty 85x55mm
*/
class gaad__bizneskarty__calc extends gaad_calc {
	
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
		switch($quantity){
			case 100 : $markup = 5.5; break;
			case 250 : $markup = 4; break;
			case 500 : $markup = 3.7; break;
			case 1000 : $markup = 3.6; break;
			case 1500 : $markup = 3.5; break;
			case 2500 : $markup = 3.4; break;
			case 3000 : $markup = 3.3; break;
			
			default : $markup = 5; break;
		}
		
		return $markup;
		
	}
	
	
	
}
