<?php

/*
* Przechowuje dane bazowe
*/
class gaad_db {
	
	var $production_brief = array(
    'dir' => '/pb',
  );
	
	/*
	* Bazy rabatowe
	*/
	
	var $discounts = array(
		/**/
		'interval' => array( 
			'b2b' => 31,
			'b2c' => 31,
		),
		
		/*startery bazowe w %*/
		'base' => array( 
			'b2b' => 15,
			'b2c' => 10,
		),
		
		'tables' => array(
		
			'b2b' => array( 
				1 => array( 100, 199 ),
				3 => array( 200, 499 ),
				5 => array( 500, 3999 ),
				8 => array( 4000, 9999 ),		
				10 => array( 10000, 99999999999 ),
			),
		
			'b2c' => array(
				2 => array( 100, 499 ),
				
				4 => array( 500, 3999 ),
				7 => array( 4000, 9999 ),		
				9 => array( 10000, 99999999999 ),
			),
		
		), 
		
	
	);
	
	
	/*
	* Dodadkowe usługi
	*/
	
	var $addons = array(
		'plastic_wrap' => .5, 
		'drilling_holes' => .1, 
		);
	
  
  var $recaptcha = array(
    'site' => '6LcqESwUAAAAAH6HMApwFYfAN3E57gkoJEP4XHn5', 
    'secret' => '6LcqESwUAAAAABCsJoB6Vk6P0b3QaxwwHIG7m6OP'
    
  );
  
	/*
	* Płatnosci
	*/
	var $payments = array(
		
		/*przelewy 24*/		
		'p24' => array( 
			'p24_merchant_id' => '40345',
			'p24_pos_id' => '40345',
			'p24_currency' => 'PLN',
			'session_id_prefix' => 'p24-session-',
			'CRC' => '58d48ee3afd40481', 
			'return_var_name' => 'fd_lsd',
			'p24_url_return' => 'http://wawaprint.pl/u/zamowienie/platnosci/',
			'p24_url_return_var' => 'weryfikacja-platnosci'
		)
		
		
		
	);
	
	/*
	* Koszta transportu
	*/
	var $shipment = array(
		'standard' => array(
			'cost' => '19.98',
			'tax' => '4.60',
			'calc_cost' => 'per_order' /* per_order, per_item*/
		),
		
		'free_shipment_from' => 300, 
	);
	
	
	/*Dane dotyczace obsugi transferu plików*/
	var $file_uploads = array(
		
		'file_min_status_to_place_order' => 200, 
		
		'paths' => array( 
			'temp' => '/up/o'
		),
		
		'thumbnails' => array(
			'cart' => array(
				'max_width' => 200, 
				'max_height' => 150, 
			)
		), 
		
		'ftp' => array(
			'host' => 'wordpress1753670.home.pl',
			'username' => 'www@wawaprint.pl',
			'password' => '4@ppi%tNWzRj'
		), 
		
	);
	
	
	/*
	* Cena kilograma papieru, indeks 0 przechowuje wartość domyślną<br>
	* Inne indekxy zawierają klucz pdłoża będącym wartością atrybutu produktu (attribute_pa_podloze) zapisanego w wariancie
	*/
	var $paper_kg_price = array(
		3.5,
		"kreda-blysk-350g" => 3.5,    
		'munken_cream_vol_1_5-80g' => 4.5, 
		'munken_white_vol_1_5-80g' => 4.5, 
		'munken_cream_vol_1_8-80g' => 4.5, 
		'munken_white_vol_1_8-80g' => 4.5,
		'munken_cream_vol_1_5-90g' => 4.5, 
		'munken_white_vol_1_5-90g' => 4.5, 
		'munken_cream_vol_1_8-90g' => 4.5, 
		'munken_white_vol_1_8-90g' => 4.5,
		'alto_vol_1_5-80g' => 5.5,
		'alto_vol_1_5-90g' => 5.5,
		
		
		// papiery ozdobne
		'papier-ozdobny-1-300g' => 10,
		'papier-ozdobny-2-220g' => 15,
		'papier-ozdobny-3-350g' => 20,
		
	);
	
	/*
	* Standardowy mnożnik dla zamówień ekspresowych, każda klasa kalkulatora może mieć swoja wartośc mnożnika
	*/
	var $express_multiplier = .5;
	
	
	
	
	/*
	* Koszt oprawy, usługi oprawiania	( dane z kalkulatora na drukksiazek.pl )
	*/
	var $cover_type_price = array(
		'oprawa-miekka-klejona' => 0.7,
		'oprawa-miekka-szyta-nicmi' => 3, //napisac wykrywanie ilosci skladek i oblicz:  ilosc_skladek * 0,07 + oprawa_klejona_cena
		'oprawa-zeszytowa' => 0.2,
		'oprawa-spiralna' => 3,
		'oprawa-twarda' => 4.5,  //wartosc samej oprawy do późniejszego wykorzystania, obliczenia podobnie jak przy miękkiej szytej
		'oprawa-twarda-klejona' => 5.2, 
		'oprawa-twarda-szyta-nicmi' => 7.5,
    
    
    
	);
	
	
	
	/*
	*  
	* koszt zadruku formatu x3 A3, B3
	*/
	var $print_price = array(
			/*
			* druk czarno-biay 1+x
			*/
			1 => array(
				0 => .016, // 1+0
				1 => .019  // 1+1
			), 
			
    
    /*
			* Druk kolorowy 4+x
			*/		
			4 => array(
				0 => .18, // 4+0
				4 => .28  // 4+4
			), 
    
    
		);
	
  
  var $print_price_by_format = array(
    
    'b4' => array(
      /*
			* druk czarno-biay 1+x
			*/
			1 => array(
				0 => .016, // 1+0
				1 => .019  // 1+1
			), 
			
    
    /*
			* Druk kolorowy 4+x
			*/		
			4 => array(
				0 => .18, // 4+0
				4 => .28  // 4+4
			), 
  
  
    ),    
    
    'sra3p' => array(
      /*
			* druk czarno-biay 1+x
			*/
			1 => array(
				0 => .025, // 1+0
				1 => .031  // 1+1
			), 
			
    
      /*
			* Druk kolorowy 4+x
			*/		
			4 => array(
				0 => .19, // 4+0
				4 => .28  // 4+4
			), 
  
  
    ), 
    
    
    
  
  );
  
	
	/*
	*  
	* koszt zadruku
	*/
	var $wrap_price = array(
			'folia-brak' => array(
				'b3' => array( 0, 0), 
			),
			'folia-blysk' => array(
				'b3' => array( .2, .4), 
			),
			'folia-mat' => array(
					'b3' => array( .2, .4), 
				),
			'folia-soft-touch' => array(
					'b3' => array( .4, .8), 
			)
		);
	

  
  /*
  * okresla jaki format produkcyjny zostanie uzyty do produkcju fromatu docelowego
  */
  
  var $production_format = array(    
    
    'a6-105x148mm' => 'b4',
    'a5-148x210mm' => 'b4',
    'a4-210x297mm' => 'b4',
    
    'b6-125x175mm' => 'sra3p', 
    'b5-175x250mm' => 'sra3p'    
    
  );
  
  
	/*
	*
	* ilość sztuk użytku na formacie produkcyjnym
	*/
	var $pieces_per_sheet = array(			
		'90x50' => 24, //wiz
		'85x55' => 24, //b card
		
		//wizytówki skladane
		'170x55' => 10, //85x55mm
		'180x50' => 12, //90x50mm
		'135x55' => 10, //90x55mm
				
		'99x210' =>  6, //dl
		'145x50' =>  6, //bilety
		'105x148' => 8, //a6
		'125x175' => 8, //b6
    
		'148x210' => 4, //a5
    '175x250' => 4, //b5
		'210x297' => 2, //a4
		'210x198' => 2, //2xdl = a4	
		
		'297x420' => 1,  //a3
		
		'295x700' => .5,
	);
	
	
	/*
	* Cena za bigowanie całego arkusza produkcyjnego B3
	* Indeksem jest format w zapisie [w1]x[h1]-mm [-[wn]x[hn]-mm] -[wdruk]x[hdruk]mm
	* np: 90x50mm-180x50mm
	*
	*/
	var $folding_price_array = array(
		'85x55mm-170x55mm' => 1, 
		'90x50mm-180x50mm' => 1, 
		'90x55mm-135x55mm' => 1,
		
		'99x210mm-297x210mm' => .1, // A4 do dl [pion]
		'210x99mm-210x297mm' => .1, // A4 do dl [poziom]
		
		'99x210mm-198x210mm' => .1, // 2dl do dl [pion]
		'210x99mm-210x198mm' => .1, // 2dl do dl [poziom]
		
		'148x210mm-297x210mm' => .1, // A4 do A5 [pion]
		'210x148mm-210x297mm' => .1, // A4 do A5 [poziom]
		
		'105x148mm-210x148mm' => .1, // A5 do A6 [poziom]
		
		
		
	);
	
	
	/*
	* Cena za złocenie całego arkusza produkcyjnego B3
	* Indeksem jest format 
	*
	*/
	var $gold_price_array = array(
		'90x50' => 5,
		'85x55' => 5,
		
		//wizytówki skladane
		'170x55' => 5, //85x55mm
		'180x50' => 5, //90x50mm
		'135x55' => 5, //90x55mm
		
		//ulotki karty okolicznosciowe		
		'99x210' =>  5, //dl
		'145x50' =>  5, //bilety
		'105x148' => 5, //a6
		'148x210' => 5, //a5
		'210x297' => 5, //a4
		'210x198' => 5, //2xdl = a4
		
		'297x420' => 5  //a3
	);
		
	/*
	* Koszty zaokraglania naroznikow wszystkich użytków na arkuszu produkcyjnym x3 b3
	*
	**/	
	var $rounding_corners_stack = 250;
	var $rounding_corners_price = 1;
	
	
	
	/*
	* Ilosc arkuszy w palecie pokrywanej lakierem uv
	*/
	var $uv_sheets_per_pallet = 1000;
	
	
	/*
	* cena pokrycia uv palety arkuszy
	*/
	var $uv_pallete_price = 155;
	
  /*
	* ilośc dni potrzebnych do polozenia lakieru wybiórczego
  * wartość będzie dodawana do terminów  
	*/
	var $uv_days_number = 4;
	
  
  
  
	/*
	* ilość użytko danego rozmiaru, jakie wchodzą na format do lakieru wybiórczego
	*/
	var $spot_uv_format_devider = array(
		1, 
		'85x55' => 12,
		'90x50' => 12,
		'99x210' => 2,
		'148x210' => 2,
		'210x198' => 2,
		'210x297' => 1,
		'297x420' => .5
		
		
	);
	
	/*
	* marża na lakier wybiorczy, zależna od formatu, pierwsza wartość jest wartościa domyślną
	*/
	var $spot_uv_markup = array(
		2, 		
		//dopisz format, żeby marżować lakier UV niezależnie
		'90x50' => 2,		
	);
	
	
	
	/*
	* Ceny mediów wielkoformatowych
	* indeks 0 -m cena jednostkowa metra kw medium
	* indeks print cena jednostkowa zadruku metra kw medium
	*/
	var $solvent_media_price_array = array(
		'baner' => array( 6, 'print' => 7 ),
		'blockout-eco' => array( 9, 'print' => 5 ),
		'blockout-premium' => array( 12, 'print' => 5 ),
		
		'monomer' => array( 4.7, 'print' => 5 ),
		'owv' => array( 5.5, 'print' => 5 ),
		
		'kreda-150g' => array( 2, 'print' => 5 ), 
		'kreda-200g' => array( 3, 'print' => 5 ), 
		
		
		'tapeta-1' => array( 11, 'print' => 5 ),
		'tapeta-2' => array( 15, 'print' => 5 ),
		'tapeta-3' => array( 19, 'print' => 5 ),
		'tapeta-4' => array( 25, 'print' => 5 ),
		'tapeta-5' => array( 30, 'print' => 5 )
		
		
	);
	
	
	
		
}
	
	