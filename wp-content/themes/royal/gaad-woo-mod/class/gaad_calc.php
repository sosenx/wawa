<?php

class gaad_calc {
	
  /*
  * Id from WOO post
  */
	var $product_id;
  
  /*
  * To nie jest ID posta z wariacją WOO, to id witrulanej wariacji używany przez apki frontowe
  */
	var $variation_id;
  
  /*
  * Slug do rozszerzenia klasy obliczeniowej
  */
	var $product_cat_slug;
  
  
	var $post = array();
	
  /*  
  * true = zaokrąglanie cen do formatu xx.99
  */
  var $raw_price = false;
  
  /*
  * Tymczsowa!!<br>
  * Ilość kalkulowanych użytkow na arkuszu produkcyjnym, w planach napisanie klasy liczącej tą wartość
  */
	var $gaad_calc_pieces_per_sheet;
  
  /*
  * Przechowuje najlepszy format produkcyjny dla danego formatu do kalkulacji<br>
  * Docelowo potrzeba będzie klasa licząca tą wartość, która weżmie pod uwagę bieżącą zajętość maszyn i ich charakterystykę etc ... 
  */
	var $best_production_format;
  
  /*
  * Używane gdy produkt docelowy złożony jest z mniejszych kalkulacji, np książką sklada się z kalkulacji bloku cz-b, kolor + okladka<br>
  * Jeżeli używane są jakieś rabaty, wyłącza je by zrabatować produkt złożony na samym końcu.
  *
  */
	var $use_top_level_discount = false;

  /*
  * Konstruktor, lets go!  
  */
	function __construct( $product_id, $variation_id = NULL ){
		
		$this->product_id = $product_id;
		$this->variation_id = $variation_id;
		$this->product_cat_slug = $this->get_category_slug();
		
		$this->set_calc_data();
	}
	
	

	
	/**
	* tworzy tablicę atrybutów wariantu z danytch pobranych z bazy
  * jeżeli klucze wymagają jakiegoś przeparsowania to jest to miejsce by zrobić
	*
  * Na koniec dane zrzucone są do globals. Tablica globals i dane w nij są kopią wartości atrybutów kalkulacji.
  * Służy to ułatwieniu kalkulowania produktów skladających się z pomniejszych kalkulacji,
    gdzie czasami wymienić trzeba sporą części atrybutów w kolejnych krokacha a czasami tylko jeden
  
	* @return void
	*/
	function get_variation_attr_array ( ) {

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
	
	
	/**
	* 	Tworzy dodadkowy obiekt z podsumowanie kostów produkcji bazując na obliczanej za pomocą fn calculate_variant_price tablicy $total_price
	*	@param $_price - cena sztuki netto
	* @return void
	*/
	function total_variant_production ( $total_price_layers_only, $variant_production, $quantity, $_price ) {
		
		
			$total = array(
				'piece' => array(), 	
				'all' => array(), 	
			);
			$t = 0;
			/*
			* Obliczanie kosztów produkcji (bez marży) każdego etapu pracy
			*/
			foreach( $total_price_layers_only as $part => $data ){
			
			
				$variant_production_markup = 
					isset( $variant_production[$part]['markup'] ) && !is_null( $variant_production[$part]['markup'] )
					? 
					( (float)$variant_production[$part]['markup'] == 0 ? 1 : (float)$variant_production[$part]['markup'] )
					: 1;
					
				$total['piece'][$part] = $data / $variant_production_markup;	
				$total['all'][$part] = $data / $variant_production_markup * $quantity;	
				
				$t += $data / $variant_production_markup;
			}
    
		// wymuszenie minimalki ilośći kartek, np okładka drukowana po 1 stronie musi być zaokrąglona do 1 kartki, czyli 2 stron
		$t = $t == 0 ? 1 : $t;
      
    /*
    * Uwaga dot quantity/2: niektóe produkty zwyczajowo liczone są za pomocą ilośći stron a inne kartek (książka - strony, ulotka - kartka, sztuka)
    * Procesy produkcji natomiast liczone są na strony (zadruk x+0, x+x, foliowanie, złocenie etc)
    * Dzielę ilość kartek na 2 by ujedolicić postrzeganie ilości -  liczone są strony, nie kartki
    */  
    
    /*
    * Tablica total przechowuje cząstkowe wartości oblicze poszczególnych wartstw produktu<br>
    * Jeżeli dawa wartwa nie występuje w produkcie element tablicy będzie miał wartość 0 i nie wpłynie na sumę
    */
    
    //ilość kart, papieru do druku cz-b
		$total['bw_sheets'] = $variant_production['bw']['sheets'] * $quantity/2;	 
    //ilość kart, papieru do druku kolor
		$total['color_sheets'] = $variant_production['color']['sheets'] * $quantity/2 + $variant_production['cover']['sheets'];
    //koszt materiału i druku cz-b
		$total['bw_cost'] = $variant_production['bw']['variant_production_cost'];
		//koszt materiału oraz druku kolorowego
    $total['color_cost'] = $variant_production['color']['variant_production_cost'] + $variant_production['cover']['variant_production_cost'];
		
		//wartość pomocnicza, może zostać usunięta w produkcji, oblicza średnią marżę każdego elementu produktu złożonego
    //pomoc dla Wojtka do określenia marży na poszczególnych etapach podczas dostsowywania ceny (gotowego produktu) do realiów rynkowych na dany produkt
		$total['average_markup'] = round( $_price / $t, 2 );
    //ilość sztuk gotowego wyrobu
		$total['quantity'] = $quantity;
		//cena sztuki gotowego wyrobu
		$total['piece_cost'] = $t;
    //cena sprzedaży
		$total['piece_regular_price'] = round( $t * $total['average_markup'], 2 ) ;
		//Koszta nakładu
		$total['all_cost'] = $t * $quantity;
		
		return $total;
	}
	
	
	/**
	* Dokłada dane serwisowe do jsona zwracanego po obliczeniu wariacji
	*
	* @return void
	*/
	function set_calc_data (  ) {
		
		/*
		* Przepisywanie tablicy post do tablicy globals, umożliwia to uzyskanie dużej elasatyczności dostępu do kalkulacji zarówno po stronie 
		*/
		if( is_array( $_POST ) ){			
			foreach( $_POST as $k => $v ){
				$GLOBALS[ 'post_calc_data' ][ str_replace( '-', '_', $k ) ] = $_POST[$k];
			}			
		}
		
		if( !is_array( $GLOBALS[ 'calc_data' ] )  ){
			 $GLOBALS[ 'calc_data' ] = array( );			
		}	
		
		/*
		* najlepszy format produkcyjny, wybrany za zasadzie obliczenia strat na spadach. <br>
    * Nie jest to optymalna droga, nie bierze pod uwagę wielu parametrów, które częśto bardziej wpłyną na końcową cenę niż straty materiałowe <br>
    * Obecnie firma nie dysponuje szcegółowymi danymi o produkcji i jej etapach by to zrobić lepiej
		*/
		if( is_array( $GLOBALS['post_calc_data']["production_formats"] ) && !empty( $GLOBALS['post_calc_data']["production_formats"] ) ){
			$this->best_production_format = array_shift( $GLOBALS['post_calc_data']["production_formats"] );
		}
		
	}
	
	
	/**
	* Zwraca marżę handlową uzależniona od nakładu.<br>
  * Funkcja zwraca mnożnik 1 ponieważ każda klasa dziedzicząca ma swoje bardziej bądź mniej skomplikowane zasady marżowania<br>
  * zależne od rodzaju towaru
	* 
	*
	* @return void
	*/
	public function get_markup ( ) {		
		return 1;		
	}
	
	
	/**
	* Zwraca ilość użytów formatu wariantu na arkuszu produkcyjnym
	*
	* @return void
	*/
	function get_pieces_per_sheet ( ) {
		$db = new gaad_db();
		$format = $this->get_format();	
		$pieces_per_sheet = apply_filters( 'gaad_calc_pieces_per_sheet', $db->pieces_per_sheet, $this );
		
		if( isset($pieces_per_sheet[ $format ]) ){
			return $pieces_per_sheet[ $format ]; 
		}
		
		if( isset( $this->best_production_format ) ){					
			return $this->best_production_format["counter"];
		}
		
		/*
		* Odwracanie wartości szerokośći i wysokośći i ponowne sprawdzanie
		*/
		
		$format = implode( 'x', array_reverse( explode( 'x', $format ) ) ) ;
		
		
		if( isset($pieces_per_sheet[ $format ]) ){
			return $pieces_per_sheet[ $format ]; 
		}
		
		if( isset( $this->best_production_format ) ){					
			return $this->best_production_format["counter"];
		}
			
		return 1;
	}
	
	
	/**  DEPRECATED - służyła do rozszerzenia tej klasy, klasą właściwą dla typu towaru, początkowo to działało, ale nie wziąłem pod uwagę faktu, że proces bazuje na id terma nbadanego w czasie tworzenia towaru. Jeżeli admin zmieni kategorię, kalkulację się posypią.<br>
    Wybór kalkulatora został zaimplemntowany na bazie custom taxonomy, co jest dla skryptu jednoznaczne i pozwala dowolnie kategorywać produkt 
	* Zwraca pierwszą nadaną kategorię produktu
	* Funkcja może zostać wzbogazcona o wykrywanie kategorii nadrzędnej, wprowadzonej przez seo yoast
	*

	* @return void
	*/
	function get_category (  ) {
		$product_cat = get_the_terms($this->product_id, 'product_cat');
		
		if( $product_cat ){
			return array_shift( $product_cat );
		}
		return false;
	}
	
	
	
	/**
	* DEPRECATED - wariacje tradycujne zostały zastąpione wirtualnymi
	*
	* @return void
	*/
	function variation_exists ( ) {
		$variation_attr_array = $_POST;
		//echo '<pre>'; echo var_dump($variation_attr_array); echo '</pre>';
		$variable_product = wc_get_product( $_POST['product_id'] );
		
		$tmp = array_merge( wp_unslash( $_POST ), array( 'product_id' => $_POST['product_id'], 
		));
		unset( $tmp['add-to-cart'] );
		unset( $tmp['variation_id'] );
		
		$variation_id = $variable_product->get_matching_variation($tmp);
		
		return $variation_id ? $variation_id : false;
	}
	
	
	/**
	* Pobiera oznaczenie mnożki dla ekspresowych zamówień, dodadkowo płatncyh
  *
	* @return void
	*/
	function get_express (  ) {
		$pa_termin = wc_get_product_terms( $_POST['product_id'], 'pa_termin-wykonania', array( 'fields' => 'slugs' ) );
		if( !empty( $pa_termin ) ){
			$buf = 1;
			foreach( $pa_termin as $k => $v ){
				if( !is_null( $v )  ){
					$termin = (float)str_replace( array( "termin-", "-"), array( "", "." ), $v );
					if( $termin > $buf ) $buf = $termin;					
				}				
			}
		}
		return "termin-" . str_replace( '.', '-', $buf);
	}
	
	/** DEPRECATED, użyj fn calc_all_quantites
	* Pobiera wszystkie naklady danej wariacji produktu (dane służą do zbudowania tabeli cennika dla każdego produktu)
	* Stosowana dla produktów bez swojego formularza 
	*
	* @return void
	*/
	function get_all_quantites (  ) {
		$tmp              = array();		
		$product          = new WC_Product( $_POST['product_id'] );
		$pa_naklad        = wc_get_product_terms( $product->id, 'pa_naklad', array( 'fields' => 'slugs' ) );		
		$express_termin   = $this->get_express();
		
		/*
    * Obliczenia każdego zdeklarowanego nakladu
    */
		foreach( $pa_naklad as $c => $naklad ){
			$quantity_match = array();		
			preg_match("/(\d*)-szt/", $naklad, $quantity_match);
      
      
      
			if( isset( $quantity_match[1] ) ){	
				/*
				* Zmiana parametrów
				*/
				$_POST["attribute_pa_termin-wykonania"] = 'termin-1';
				$_POST["attribute_pa_naklad"] =  (int) $quantity_match[1] . '-szt';
				
				$variation_id = $this->variation_exists();
				
				$r = array(
					"product_id" => $_POST['product_id']					
				);
				
				if( $variation_id ){
					$variable_product = new WC_Product_Variation( $variation_id );
					$variation_attributes = array();
					
					$variation_attributes['_quantity'] = (int) $quantity_match[1];
					$variation_attributes['variation_attr'] = $variable_product->get_variation_attributes();
					$variation_attributes['_regular_price'] = (float)$variable_product->get_price();
					$variation_attributes['variation_id'] = (int)$variation_id;
					
					$r = array_merge( $r, $variation_attributes );
					
					
				} 
				
				
				/*
				* Zmiana parametrów, termin Ekspress!
				*/
				$_POST["attribute_pa_termin-wykonania"] = $express_termin;
				$variation_id = $this->variation_exists();
				
				if( $variation_id ){
					$variable_product = new WC_Product_Variation( $variation_id );
					$variation_attributes = array();
					
					$variation_attributes['_quantity'] = (int) $quantity_match[1];
					$variation_attributes['variation_attr'] = $variable_product->get_variation_attributes();
					$variation_attributes['_regular_price'] = (float)$variable_product->get_price();
					$variation_attributes['variation_id'] = (int)$variation_id;
					
					$r[ 'express' ] = $variation_attributes;
					
				} 
				
				
				if( count( $r ) > 1  ){
					$tmp[] = $r;
				}
				
			}
			
		}
		
		return $tmp;
	}
	
	/**
	* Oblicza ceny wszystkich zdeklarowanych w atrybucie naklad ilosci 
	*
	* @return void
	*/
	function calc_all_quantites (  ) {
		$tmp = array();
		
		$product = new WC_Product( $_POST['post_data']['product_id'] );	
		
		$_markup = new gaad_markup( 'qlist', $quantity, $_POST["post_data"]["product_id"] );
    /*
    * Niektóre produkty (katalog, książka) nie posiadają zdeklarowanych atrybutów do nakładu, kleint zwykle podaje naklad sam<br>
    * By móc wygenerować cennik stworzona zostałą klasa pomocnicza markup, gdzie przchowywane są wartośći nakladó oraz odpowiednie marże w tych przedziałach
    */
		$pa_naklad = $_markup->get_qlist();
		
		if( empty( $pa_naklad ) ){
			$pa_naklad = wc_get_product_terms( $product->id, 'pa_naklad', array( 'fields' => 'slugs' ) );
		}
   
		/*
    * Obliczenia każdego zdeklarowanego nakladu
    */
		foreach( $pa_naklad as $c => $naklad ){
			$quantity_match = array();		
			preg_match("/(\d*)-szt/", $naklad, $quantity_match);
			
			
			if( isset( $quantity_match[1] ) ){				
				$_POST['post_data']["attribute_pa_termin-wykonania"] = 'termin-1';
				$_POST['post_data']["attribute_pa_naklad"] =  (int) $quantity_match[1] . '-szt';
        
       // echo '<pre>'; echo var_dump( $_POST['post_data'] ); echo '</pre>';
        
        
				$calculation = $this->calculate_variant_price();
				
				$r = array_merge( array(
					"product_id" => $_POST['product_id']					
				), $calculation);								
				//$r["variation_id"] = $this->save_variation( $r );
				$r["variation_id"] = uniqid('var');
				
				
				$_POST['post_data']["attribute_pa_termin-wykonania"] = 'termin-1-5';
				$express = $this->calculate_variant_price();
				
				$r2 = array_merge( array(
					"product_id" => $_POST['product_id']					
				), $express);
				
				//$express_variation_id = $this->save_variation( $r2 );
				$express_variation_id = uniqid('var');
				
				/*
        * Wariacja dla cen ekspresowych jest dzieckim wariacji głównej, różni się mnożnikiem marży pobranym z panelu admina
        */
				$r["express"] = $express;
				$r["express"]["variation_id"] = $express_variation_id;
				
				
				$tmp[] = $r;
				
			}
			
		}
		
		return $tmp;
	}
	
	
	
	
	/** GŁÓWNA FN klasy!
	* Oblicza cene wariantu biorąc pod uwagę tylko podstawowe parametry
	*
	* @return void
	*/
	function calculate_variant_price ( ) { 
		
		$db = new gaad_db();
		
		/*
		* Wiele klas kalkulatora naklada filtry, zmieniające proces liczenia<br>
    * konieczne jest ich usunięcie, żeby wyniki ich działania się nie nakłądały na siebie
		*/		
		remove_all_filters( 'gaad_calc_markup', 10);
		remove_all_filters( 'gaad_calc_piece_price', 10);
		remove_all_filters( 'gaad_calc_pieces_per_sheet', 10);
		remove_all_filters( 'sheet_B3_print_price_array', 10);
		remove_all_filters( 'sheet_B3_folding_price_array', 10);
		remove_all_filters( 'sheet_B3_gold_price_array', 10);
		remove_all_filters( 'sheet_B3_rounding_corners_price', 10);
		
		/*
		* Zakładanie filtrów klasy pochodnej
		*/
		if( method_exists( $this, 'apply_filters') ){
			$this->apply_filters( $this->apply_filters_attr );
		} 
		
		/*
		* Pobieranie marzy produktu
		*/		
		$_markup = apply_filters( 'gaad_calc_markup', $this->get_markup(), $this );
    //markup failsafe
		$_markup = $_markup == 0 ? 1 : $_markup;
		
		/*
		* Warstwy produkcji, kolejne koszta, nakadane ne acay arkusz produkcyjny
		*/
		$price_parts = array(
			"sheet_price"            => $this->sheet_B_price(),  //cena papieru
			"print_price"            => $this->sheet_B3_print_price(),//cena przelotu przez maszynę, druku
			"wrap_price"             => $this->sheet_B3_wrap_price(),//cena foliowania arkusza produkcyjnego, (nie użytku!)
			"folding_price"          => $this->sheet_B3_folding_price(),//cena bigowania arkusza
			"gold_price"             => $this->sheet_B3_gold_price(), //cena złocenia arkusza
			"rounding_corners_price" => $this->sheet_B3_rounding_corners_price() //cena zaokrąglania narożników
			
		);
   
		//zastosuj dostępne filtry
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
		$spotuv_price = $this->sheet_B3_spot_uv_price() / $_markup;
		
		$variant_production_cost = $sheets * $total_sheet_price + $spotuv_price;
		
		
		 
		/*
		* Obliczanie ceny wariantu
		*/
		$express_price_multiplier = $this->express_price_multiplier();		
		$price = $variant_production_cost * $_markup * $express_price_multiplier;
		
		
		/*
		* Zaookrąglanie ceny do formatu xx.99		
		*/
		if( !$this->raw_price ){
			$price = $this->gaad_round( $price ); 
		}
		
		$total_price_layers_only = $price_parts;
		$quantity = isset( $quantity ) && $quantity > 0 ? $quantity : 1;
		
		$total_price['_markup'] = $_markup;
		$total_price['_quantity'] = $quantity;

		
		$total_price['_regular_price'] = $this->gaad_round( $_price * $quantity * $_markup );
		//tutaj może jest trochę mylące nazewnictwo, trudno
		$total_price['_price'] = $total_price['_regular_price'];
		
		$total_price['_piece_price'] = round( $total_price['_price'] / $quantity, 2 );
		
		$variant_production['totals'] = $this->total_variant_production( $total_price_layers_only, $variant_production , $quantity, $_price );
		
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
				'post_data' => $_POST
			), $price_parts, is_array( $GLOBALS[ 'calc_data' ] ) ? $GLOBALS[ 'calc_data' ] : array() );
	}
	
	
	
	/**
	* Calculate discounted price 
	*
	* @return void
	*/
	function calculatePriceAfterDiscount ( $_regular_price ) {
	
		/*
		* Rabatownie produktu
		* Obiekt oblicza ilość % rabatu dla produktu względem ustalonych wytycznych
		* Naztępnie wartość rabatu jest odejmowana od ceny końcowej produktu
		*/
		$discount = new gaad_discount();
		$discount_value = $discount->getDiscount();
		
				
		return  $_regular_price - $_regular_price * $discount_value / 100 ;
	
	}
	
	/**
	* Pobiera slug kategorii produktu
	*
	* @return void
	*/
	function get_category_slug (  ) {
		$category = $this->get_category();
		if( $category ){
			return $category->slug;
		}		
	}
	
	
	/**
	* Tworzy obiekt kalkulatora z konstruktora klasy pochodnej od gaad_calc
	* Oblicza cene wariatu produktu przy uzyciu zewnętrznej klasy zależnej od kategorii produktu
	* Jeżeli dana kategoria nie posiada klasy liczącej używana jest standardowa funkcja licząca gaad_cal::calculate_variant_price
	*
	* @return void
	*/
	function use_it (  ) {
		/*
		* Sprawdzanie czy ustawiona jest taxonomia calc_class<br>
		* Taxonomia ta powinna mieć tylko 1 wartość, jeżeli ma ich więcej po uwagę brana będzie tylko ta z indeksem 0
		* Jeżeli jest
		*/
		$product_calc_class_terms = wp_get_object_terms( $this->product_id,  'calc_class' );			
		$calc_class = is_array($product_calc_class_terms) ? $product_calc_class_terms[0]->slug : $this->product_cat_slug;
		
	

		/*
		* Nazwa klasy pochodnej od gaad_calc służącej do liczenia konkretnego rodzaju towarów<br>
		* Szablon nazwy klasy pochodnej: gaad__[taxonomy:calc_class || taxonomy:category]__calc
		*/
		$product_calc_class_name = 'gaad__'. str_replace( "-", "_", $calc_class ) .'__calc';

		$product_calc_class_path = get_template_directory() . '/gaad-woo-mod/class/' . $product_calc_class_name . '.php';
		
		
		/*
		* Automatyczne dołączanie potrzebnej klasy kalkulatora		
		*/
		if( is_file( $product_calc_class_path ) ) {
			require_once( $product_calc_class_path );
		}
		
		//echo '<pre>'; echo var_dump($product_calc_class_name, class_exists( $product_calc_class_name )); echo '</pre>';

		if( class_exists( $product_calc_class_name ) ){			
			$calculator = new $product_calc_class_name( $this->product_id, $this->variation_id );
			return $calculator->calculate_variant_price();
		} else {
			//echo '<pre>'; echo var_dump( $product_calc_class_name, 'missing class error' ); echo '</pre>';		
			return $this->calculate_variant_price();
		}
	
	}

  
  /*
  * Pobiera taxonomię calc_name -> nazwa kalkulatora docelowego dla danego typu produktu
  */
	function get_calc_name(){
		
		$product_calc_class_terms = wp_get_object_terms( $this->product_id,  'calc_class' );			
		$calc_class = is_array($product_calc_class_terms) ? $product_calc_class_terms[0]->slug : $this->product_cat_slug;
		
		/*
		* Nazwa klasy pochodnej od gaad_calc służącej do liczenia konkretnego rodzaju towarów<br>
		* Szablon nazwy klasy pochodnej: gaad__[taxonomy:calc_class || taxonomy:category]__calc
		*/
		$product_calc_class_name = 'gaad__'. str_replace( "-", "_", $calc_class ) .'__calc';
		return $product_calc_class_name;
	}
	
	
	/**
	* Zwraca ilosc mniejszych arkuszy produkcyjnych z duzego arkusza zakupowego
	* 
	* @return void
	*/
	function get_B_devider ( $outer_format = 'B3' ) {
		$source = array(
			'b3' => 4,
			'b4' => 8
		);
	
		return $source[ strtolower( $outer_format ) ];		
	}
	
	/**
	* Oblicza wartość foliowania arkusza
	*
	* @return void
	*/
	function sheet_B3_wrap_price( ) {
		$db = new gaad_db();
		$wrap = isset( $GLOBALS['post_calc_data']["attribute_pa_uszlachetnienie"] ) ? $GLOBALS['post_calc_data']["attribute_pa_uszlachetnienie"] : $_POST["attribute_pa_uszlachetnienie"];
		$wrap_match = array();		
		preg_match("/(^folia-blysk|folia-mat|folia-soft-touch)-(jednostronnie|dwustronnie)/", $wrap, $wrap_match);
		$sides = str_replace( array( 'jednostronnie', 'dwustronnie' ), array( 1, 2 ), $wrap_match[2] );
		$wrap = $wrap_match[1];
		
		$price_table = $db->wrap_price[ $wrap_match[1] ]['b3'];
		if( is_null( $price_table )  ){
			return 0;
		}
		
		if( is_array( $price_table ) && count( $price_table ) == 2 ){
			$wrap_price = $price_table[ (int)$sides - 1 ];
		} elseif( (int)$sides == 2 ){
			$wrap_price = $price_table * 2;
		}
		
		return $wrap_price;
	}
	
	
	/**
	* Oblicza koszt przelotu przez maszynę (przeoly są uśrednione, docelowo ma być klasa rożróżniająca możliwośći i koszty na poszczególnych maszynach)
	*
	* @return void
	*/
	function sheet_B3_print_price( ) {
		$db = new gaad_db();
		$print = isset( $GLOBALS['post_calc_data']["attribute_pa_zadruk"] ) ? $GLOBALS['post_calc_data']["attribute_pa_zadruk"] : 
			( isset( $_POST["attribute_pa_zadruk"] ) ? $_POST["attribute_pa_zadruk"] : $_POST['post_data']["attribute_pa_zadruk"]) ;
		
		$print_match = array();		
		preg_match("/(\d)x(\d)/", $print, $print_match);
		$print_price_array = apply_filters( 'sheet_B3_print_price_array', $db->print_price );
		
		return $print_price_array[ $print_match[ 1 ] ][ $print_match[ 2 ] ];		
	}

	
	/**
	* Koszt składania ( bigowania ) arkusza
	*
	* @return void
	*/
	function sheet_B3_folding_price( ) {		
		$db = new gaad_db();
		$format = $this->get_format(true);
		
		$sheets = $this->get_production_sheets();
		$folding_price_array = apply_filters( 'sheet_B3_folding_price_array', $db->folding_price_array );
		
		return $folding_price_array[ $format ];
	}

	
	/**
	* Cena złocenia arkusza
	*
	* @return void
	*/
	function sheet_B3_gold_price( ) {		
		$db = new gaad_db();
		$format = $this->get_format();
		$gold = isset( $GLOBALS['post_calc_data']["attribute_pa_zlocenia"] ) ? $GLOBALS['post_calc_data']["attribute_pa_zlocenia"] : $_POST["attribute_pa_zlocenia"];
		
		$gold_match = array();	
		preg_match("/(jednostronnie|dwustronnie)/", $gold, $gold_match);
		$sides = str_replace( array( 'jednostronnie', 'dwustronnie' ), array( 1, 2 ), $gold_match[1] );
		
		$gold_price_array = apply_filters( 'sheet_B3_gold_price_array', $db->gold_price_array );
		
		return $gold_price_array[ $format ] * $sides;		
	}

	
	/**
	* Oblicza ilość potrzebnych do produkcji kartek formatu produklcynego, <br>
  obecnie B3 (wartości uśrednione), ale docelowo zostanie stworzona klasa dopasowująca pracę do odpowiedniego arkusza Bx, Ax
	*
	* @return void
	*/
	function get_production_sheets ( ) {
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
		
		$pieces_per_sheet = $pieces_per_sheet < 1 ? 1 : $pieces_per_sheet;		
		$sheets = (int)round( $quantity / $pieces_per_sheet, 0, PHP_ROUND_HALF_UP );
		
		if( $sheets > 0 ){
			$sheets += ($quantity % $pieces_per_sheet > 0 ? 1 : 0 );
		}		
		
		return $sheets;
	}
	
	/**
	* Zwraca marżę jeżeli w prcesie produkcji użyty jest spot UV
	*
	* @return void
	*/
	static function get_spot_uv_markup (  ) {
		$format = gaad_calc::get_format();
		$db = new gaad_db();
		
		return isset( $db->spot_uv_markup[ $format ] ) ? $db->spot_uv_markup[ $format ] : $db->spot_uv_markup[ 0 ];
	}

	/**
	* Oblicza koszt lakieru wybiórczego dla arkusza produkcyjnego
	*
	* @return void
	*/
	function sheet_B3_spot_uv_price (  ) {		
		$db = new gaad_db();
		$quantity = $this->get_quantity();
		$format = $this->get_format();
		$spotuv = isset( $GLOBALS['post_calc_data']["attribute_pa_lakier-wybiorczy"] ) ? $GLOBALS['post_calc_data']["attribute_pa_lakier-wybiorczy"] : 
			(isset( $_POST["attribute_pa_lakier-wybiorczy"] ) ? $_POST["attribute_pa_lakier-wybiorczy"] : $_POST['post_data']["attribute_pa_lakier-wybiorczy"]);
		$spot_uv_markup = gaad_calc::get_spot_uv_markup();
		
		
		$spotuv_match = array();	
		preg_match("/(jednostronnie|dwustronnie)/", $spotuv, $spotuv_match);
		$sides = (int)str_replace( array( 'jednostronnie', 'dwustronnie' ), array( 1, 2 ), $spotuv_match[1] );		
		
		//brak lakieru wybiórczego w wariancie 
		if( $sides == 0 ){
			return 0;
		}
		
		$pieces_per_sheet = $this->get_pieces_per_sheet( );
		$spotuv_price = apply_filters( 'sheet_B3_spot_uv_price', $db->uv_pallete_price );
		$rest = ($quantity / $pieces_per_sheet ) < $db->uv_sheets_per_pallet ? 0 : ($quantity / $pieces_per_sheet ) % $db->uv_sheets_per_pallet;
		$stacks = ($quantity / $pieces_per_sheet ) / $db->uv_sheets_per_pallet < 1 ? 1 : (int)( ($quantity / $pieces_per_sheet ) / $db->uv_sheets_per_pallet );
		$spot_uv_price = ( $rest + $stacks ) * $spotuv_price * $sides;
				
		/*
		* Pobranie ilości uzytkow jakie wchodzą na format do naświetlenia
		*/
		$spot_uv_format_devider = isset( $db->spot_uv_format_devider[ $format ] ) ? $db->spot_uv_format_devider[ $format ] : $db->spot_uv_format_devider[ 0 ];
		
		if( ! isset( $db->spot_uv_format_devider[ $format ] ) ){			
			$format = implode( 'x', array_reverse( explode( 'x', $format ) ) ) ;
			$spot_uv_format_devider = isset( $db->spot_uv_format_devider[ $format ] ) ? $db->spot_uv_format_devider[ $format ] : $db->spot_uv_format_devider[ 0 ];			
		}	
		
		$tmp = $spot_uv_price / $spot_uv_format_devider * $spot_uv_markup;
		
		return $tmp;
	}
	
	
	/**
	* Funkcja oblicza koszt za zaokrąglenie narożników
	* 
	*
	* @return void
	*/
	function sheet_B3_rounding_corners_price( ) {		
		$db = new gaad_db();
		$quantity = $this->get_quantity();
		$round = isset( $GLOBALS['post_calc_data']["attribute_pa_zaokraglone-narozniki"] ) ? $GLOBALS['post_calc_data']["attribute_pa_zaokraglone-narozniki"] : $_POST["attribute_pa_zaokraglone-narozniki"];
		
		$round_match = array();	
		preg_match("/-([1234]+)-/", $round, $round_match );
		
		
		if( !isset($round_match[1]) ){
			return 0;
		}
		
		$roundig_price_array = apply_filters( 'sheet_B3_rounding_corners_price', $db->rounding_corners_price );
		
		$rest = $quantity < $db->rounding_corners_stack ? 0 : $quantity % $db->rounding_corners_stack;
		$stacks = $quantity / $db->rounding_corners_stack < 1 ? 1 : (int)( $quantity / $db->rounding_corners_stack );
		$rounding_price = ( $rest + $stacks ) * $db->rounding_corners_price;
			
		// wynik zależny od ilości sztuk uzytków musi być podzielony przez ilośc arkuszy uzytych do ich produkcji.
		// konieczne jest to ze względu na miejsce zliczania wartości poszególnych warstw\
		// funkcja zwraca wartśći do tablicy liczącej koszt obróbki pojedyńczego arkusza produkcyjnego
		return $rounding_price / $this->get_production_sheets() ;		
	}

	
	/**
  * Przeliczanie jednostek między sobą
	* Oblicza wartości w tablicy formatu do podanej w argumencie jednostki
	*
	* @return void
	*/
	function recalculate_format_array ( $unit, $format_array = NULL ) {
		$uarray = array(
			'mm' => 1000,
			'cm' => 100, 
			'm' => 1, 
		);
	
		if( !$format_array ){
			$format_array = gaad_calc::get_format_array();
		}
		
		$meters_arary = array(
			($format_array[1] / $uarray[ $format_array[3] ]) .'x'. ($format_array[2] / $uarray[ $format_array[3] ]) . 'm',
			$format_array[1] / $uarray[ $format_array[3] ],
			$format_array[2] / $uarray[ $format_array[3] ],
			'm'
		);
		
		if( $unit == $format_array[3] ){
			return $meters_arary;			
		} else {
			
			$unit_array = array(
				($meters_arary[1] * $uarray[ $unit ]) .'x'. ($meters_arary[2] * $uarray[ $unit ]) . $unit,
				$meters_arary[1] * $uarray[ $unit ],
				$meters_arary[2] * $uarray[ $unit ],
				$unit
			);
			
		} 
		
		return $unit_array;	
	}
	
	/**
	* Pobiera tablicę z formatami arkuszy
	*
	* @return void
	*/
	function get_format_array (  ) {
		$format = isset( $GLOBALS['post_calc_data']["attribute_pa_format"] ) ? $GLOBALS['post_calc_data']["attribute_pa_format"] : 
					( isset( $_POST["attribute_pa_format"] ) ? $_POST["attribute_pa_format"] : $_POST['post_data']["attribute_pa_format"]);
		
		$format_match = array();						
		preg_match("/(\d+)x(\d+)(cm$|mm$|m$)/", $format, $format_match);
		
		if( !empty( $format_match )  ){
			return $format_match;
		} 
		
		return false;		
	}
	
	
	/**
	* Zwraca pobrany z query string parametr format w formacie width x height
	*
	* @return void
	*/
	function get_format ( $full = false ) {
		$format = isset( $GLOBALS['post_calc_data']["attribute_pa_format"] ) ? $GLOBALS['post_calc_data']["attribute_pa_format"] : 
				( isset( $_POST["attribute_pa_format"] ) ? $_POST["attribute_pa_format"] : $_POST['post_data']["attribute_pa_format"] );
		
		if( $full ){
			return $format;
		}
		
		$format_match = array();				
		//preg_match("/(\d+)x(\d+)mm$/", $format, $format_match);
		preg_match("/(\d+)x(\d+)(cm$|mm$|m$)/", $format, $format_match);
		
		
		return $format_match[1] . 'x' . $format_match[2];
	}
	
	/**
	* Pobiera ilosc użytków w wariancie
	*
	* @return void
	*/
	static function get_quantity (  ) { 
		$quantity = isset( $GLOBALS['post_calc_data']["attribute_pa_naklad"] ) ? $GLOBALS['post_calc_data']["attribute_pa_naklad"] : $_POST["attribute_pa_naklad"];
		
		$quantity_match = array();		
		preg_match("/(\d*)-szt/", $quantity, $quantity_match);
		
		return (int)$quantity_match[ 1 ];
	}
	
	
	/**
	* Pobiera gramature papieru wariantu, zwraca wartosc w kg
	*
	* @return void
	*/
	function get_paper_weight ( ) {
		$paper = isset( $GLOBALS['post_calc_data']["attribute_pa_podloze"] ) ? $GLOBALS['post_calc_data']["attribute_pa_podloze"] : $_POST["attribute_pa_podloze"];
		
		$paper_match = array();		
		preg_match("/-(\d*)g/", $paper, $paper_match);
		
		return (int)$paper_match[1] / 1000;
	}	
	
	
	/**
	* zwraca atrybut podloze (rodzaj papieru, banera etc)
	*
	* @return void
	*/
	function get_medium (  ) {
		return isset($GLOBALS['post_calc_data']["attribute_pa_podloze"]) ? $GLOBALS['post_calc_data']["attribute_pa_podloze"] : $_POST[ "attribute_pa_podloze" ];
	}
	
	/**
	* Pobiera cenę kilograma papieru
	*
	*
	* @return void
	*/
	function get_paper_kg_price (  ) {
		$db = new gaad_db();		
		$podloze = isset($GLOBALS['post_calc_data']["attribute_pa_podloze"]) ? $GLOBALS['post_calc_data']["attribute_pa_podloze"] : $_POST[ "attribute_pa_podloze" ];		
		$paper_price = isset( $db->paper_kg_price[ $podloze ] ) ? $db->paper_kg_price[ $podloze ] : $db->paper_kg_price[ 0 ];		 
		
		return $paper_price;		
	}	
	
	/**
	* Oblicza wartość arkusza B3 z fromatu b1 (funkcja rozwojowa, docelowo ma liczyc dowolny arkusz mniejszy z dowolnego arkusza większego)
	* Wartosci podawane w metrach i kg
	* @return void
	*/
	function sheet_B_price ( $outer_format = 'B3', $paper_weight = NULL, $kg_price = NULL ) {
		$w = 1;
		$h = .7;
		$db = new gaad_db();
		$kg_price = is_null( $kg_price ) ? $this->get_paper_kg_price() : $kg_price;		
		$paper_weight = is_null( $paper_weight ) ? $this->get_paper_weight() : $paper_weight;		
		$square = $w * $h;
		$weight = $square * $paper_weight;
		$price = $weight * $kg_price; 		
		$devider = $this->get_B_devider( $outer_format );
		
		$sheet_B_price =  $price / $devider;				
    return $sheet_B_price;		    
	}	
	
	
	/** Cool Prices :) .99
	* Funkcja pobiera cene i zwraca jej odpowiednik z końcówka x.99 (jest to zabieg marketingowy, nie matematyczny)
	* 
	* @return void
	*/
	function gaad_round( $price ) {
		/*
		* Wyciągnięcie części dziesiętnej z liczby i powiększenie jej o 1
		*/
		$upper_price = (int)$price + 1;
		/*
		* Zaokrągląną cene w górę pomniejszamy o 0.01 by uzyskać końcówkę .99
		*/
		//$new_price = (int)round( $upper_price , 0, PHP_ROUND_HALF_UP ) - 0.01;
		$new_price = $upper_price - 0.01;
		
		return $new_price;
	}
	
	/**
	* rzeźba, wiem, ale obecnie działa
	*
	* @return void
	*/
	function express_price_multiplier ( ) { 
		
		if( $GLOBALS['post_calc_data']["attribute_pa_termin_wykonania"] ){
			$termin = $GLOBALS['post_calc_data']["attribute_pa_termin_wykonania"];
		}
		
			else if( $GLOBALS['post_calc_data']["attribute_pa_termin-wykonania"] ){
				$termin = $GLOBALS['post_calc_data']["attribute_pa_termin-wykonania"];
			}
				
				else {
					
					if( $_POST[ "attribute_pa_termin_wykonania" ] ){
						$termin = $_POST[ "attribute_pa_termin_wykonania" ];
					} else $termin = 1;
				}
		$termin = (float)str_replace( array( "termin-", "-"), array( "", "." ), $termin );
		return $termin != 0 ? $termin : 1;
	}
	
	
}