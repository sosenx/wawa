<?php 
/*
* Oblicza cenę wariatu dla ulotki ( wersja ekonomiczna )
*/
class gaad__ulotki__calc extends gaad_calc {var $product_id;
	var $variation_id;
	var $product_cat_slug;
	var $use_top_level_discount = false;
	
	function __construct( $product_id, $variation_id ){		
		$this->product_id = $product_id;
		$this->variation_id = $variation_id;
		$this->product_cat_slug = $this->get_category_slug();
		
	}
	
	
	
	/*
	*
	* Pozyskanie atrybutów bloku kolorowego i zapisanie ich tablicy z odpowiednimi indeksami
	*/
	function parse_color_attr(){
	
	
	$termin = (float)str_replace( array( "termin-", "-"), array( "", "." ), $_POST['post_data']["attribute_pa_termin-wykonania"] );
	$termin = $termin == 0 ? 1 : $termin;
		
		$_P = array(
		//	'attribute_pa_podloze' => $_POST["post_data"]["attribute_pa_podloze"],
			//'attribute_pa_uszlachetnienie' => $_POST["post_data"]["attribute_pa_uszlachetnienie"],			
//'attribute_pa_zadruk' => 'dwustronnie-kolorowe-4x4-cmyk',
			
			/*
			* Atrybuty wspólne
			*/
			'attribute_pa_termin-wykonania' => $termin, 
		//	'attribute_pa_naklad' => (int)str_replace( '-szt', '', ( $_POST["post_data"]['attribute_pa_ilosc-stron-kolorowych'] / 2 + 1) ) . '-szt',
			//'attribute_pa_format' => $_POST["post_data"]["attribute_pa_format"],
			
			/*
			* Atrybuty szczególowe dla koloru
			*/
		//	'attribute_pa_porozrzucane-str-kolor' => $_POST[ 'attribute_pa_porozrzucane-str-kolor' ] 
			
			
		);
		return array_merge($_POST['post_data'], $_P );
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
		if( is_null( $quantity ) || $quantity == 0 ){
			$quantity = 1;
		}
		
		/*
		* Całkowite koszty produkcji nakładu
		*/
		$total_price = array(
			'color' => $variant_production['color']["_regular_price"]			
		);
		
		/*
		* Pobieranie marzy produktu
		*/
		$_markup = $color_calculation[ 'markup' ];
		
		
		/*
		* Suma kosztów produkcji 
		*/
		$max = count( $total_price );
		$_price = 0;
		foreach( $total_price as $k => $v ){
			$_price += (float)$v;
		}
			$total_price_layers_only = $total_price;
			$total_price['_piece_price'] = round( $_price, 2 ) / $quantity;
			$total_price['_markup'] = $_markup;
			$total_price['_quantity'] = $quantity;			
			$total_price['_regular_price'] = $this->gaad_round( $this->calculatePriceAfterDiscount( $_price ) );
		
			$total_price['_price'] = $total_price['_regular_price'];

			$variant_production['totals'] = $this->total_variant_production( $total_price_layers_only, $variant_production , $quantity, $_price );

			return $GLOBALS['post_calc_data_tmp'] = array_merge( $total_price, array( 
				'variant_production' => $variant_production,
				'variation_attr' => gaad__ulotki__calc::get_variation_attr_array()
				) );
		
		
		
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
		//add_filter( 'gaad_calc_piece_price', __CLASS__ . '::set_piece_price', 10, 1 );
		//add_filter( 'gaad_calc_pieces_per_sheet', __CLASS__ . '::set_pieces_per_sheet', 10, 1 );
	}
	
	
	/**
	* Zwraca marżę handlową
	*
	* @return void
	*/
	public static function set_markup ( $markup ) {
		
		$quantity = parent::get_quantity();
		$markup = 1;		
		
		$_markup = new gaad_markup( 'color', $quantity, $_POST["post_data"]["product_id"] );
		$markup = $_markup->get_markup();
		
		return $markup;
		
	}

}
