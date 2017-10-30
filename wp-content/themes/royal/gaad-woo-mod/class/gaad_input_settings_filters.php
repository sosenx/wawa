<?php 

/*
*
*/

class gaad_input_settings_filter{
	
	/*
	* Wspólne funcke pomocnicze
	*
	*/
	
	
	/**
	* Zwraca wartość startową parametru, jeżeli nie zostanie znaleziona zwraca wartość z sekcji default
	*
	* @return void
	*/
	static function get_default ( $attr_name, $defaults_array ) {		
		$markup_tag = gaad_ajax::gaad_get_markup_tag();
		$default_value = null;
		
		if( is_array( $defaults_array ) ){
			
			if( is_array( $defaults_array[ $markup_tag ] ) ){
					
				$default_value = isset( $defaults_array[ $markup_tag ][ $attr_name ] ) ? 
								$defaults_array[ $markup_tag ][ $attr_name ] : 
								$defaults_array[ 'default' ][ $attr_name ];
			}
		}
		
		return $default_value;
	}

	
	
	
	
	
	
	
	
	
	
	/* ======================================================= FILTRY ======================================================= */
	
	
	/**
	* Kalkulator ULOTKI:
	*	
	*
	* dopisuje szczegółową konfiguracje pól formularza do wyboru wariantu produktu
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__kalendarz ( $input_settings ){	
		$_filter_data = new gaad_input_settings_filter_data();
		/*
		* Ustawienia  wartości początkowych dla pól formularza w zależności od ustawionego w adminie markup_tag
		*/		
		$defaults_by_markup_tag = array(
			'default' => array( 
				'attribute_pa_format' => '295x700mm',
				'attribute_pa_podloze' => 'kreda-300g',				
				
			)
		
		);		
		
		
		//format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_format", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Format', 
					'options' => array( 
						
					),
					'opanel' => array(
						'title' => 'Wybierz rozmiar'
					), 
				)
			)
		);
		
		
		//podloze
		$input_settings = update_input_settings( $input_settings, "attribute_pa_podloze", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_podloze", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Rodaj papieru',
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru', 
					), 
				)
			)
		);	
		
		//okienko
		$input_settings = update_input_settings( $input_settings, "attribute_pa_okienko-kalendarz", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_okienko-kalendarz", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Kolor okienka',
					'opanel' => array(
						'title' => 'Wybierz kolor okienka', 
					), 
				)
			)
		);	
		
		
		//koperta
		$input_settings = update_input_settings( $input_settings, "attribute_pa_koperta-kalendarz", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_koperta-kalendarz", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Koperta',
					
					'opanel' => array(
						'title' => 'Wybierz kolor okienka', 
					), 
				)
			)
		);	
		
		
		//foliowanie
		$input_settings = update_input_settings( $input_settings, "attribute_pa_uszlachetnienie", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'folia-brak',			
				'labels' => array( 
					'l' => 'Foliowanie',
					'options' => $_filter_data->options[ 'foliowanie' ],
					'opanel' => array(
						'title' => 'Wybierz folię', 
					), 
				)
			)
		);	
		
		

		return $input_settings;
	}
	
	
		
	/**
	* Kalkulator ULOTKI:
	*	
	*
	* dopisuje szczegółową konfiguracje pól formularza do wyboru wariantu produktu
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__rollup ( $input_settings ){	
		$_filter_data = new gaad_input_settings_filter_data();
		/*
		* Ustawienia  wartości początkowych dla pól formularza w zależności od ustawionego w adminie markup_tag
		*/		
		$defaults_by_markup_tag = array(
			'default' => array( 
				'attribute_pa_format' => '85x200cm',
				'attribute_pa_podloze' => 'blockout-eco',				
				'attribute_pa_rodzaj-kasety' => 'economic', 
			),
			
			'potykacz' => array( 
				'attribute_pa_format' => '59x84cm',
				'attribute_pa_podloze' => 'kreda-150g',				
				'attribute_pa_rodzaj-kasety' => 'owz',
			)
		
		);		
		
		
		//format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_format", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Format', 
					'options' => array( 
						'42x59cm' => 'A2 ( 420x594mm ) ',
						'59x84cm' => 'A1 ( 594x841mm ) ',
						'70x100cm' => 'B1 ( 707x1000mm ) ',
						'50x70cm' => 'B2 ( 500x707mm ) ',
					),
					'opanel' => array(
						'title' => 'Wybierz rozmiar'
					), 
				)
			)
		);
		
		
		//podloze
		$input_settings = update_input_settings( $input_settings, "attribute_pa_podloze", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_podloze", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Rodaj papieru',
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru', 
					), 
				)
			)
		);	
		
		
		//rodzaj kasety
		$input_settings = update_input_settings( $input_settings, "attribute_pa_rodzaj-kasety", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_rodzaj-kasety", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Rodaj kasety',
					'opanel' => array(
						'title' => 'Wybierz kasetę', 
					), 
				)
			)
		);	


		return $input_settings;
	}
	
	
	
	/**
	* Kalkulator ULOTKI:
	*	
	*
	* dopisuje szczegółową konfiguracje pól formularza do wyboru wariantu produktu
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__ulotki ( $input_settings ){	
		$_filter_data = new gaad_input_settings_filter_data();
    
		/*
		* Ustawienia  wartości początkowych dla pól formularza w zależności od ustawionego w adminie markup_tag
		*/		
		$defaults_by_markup_tag = array(
			'default' => array( 
				'attribute_pa_format' => 'a6-105x148mm',
				'attribute_pa_podloze' => 'kreda-130g',
				'attribute_pa_orientacja' => 'pionowo',
				'attribute_pa_zadruk' => 'dwustronnie-kolorowe-4x4-cmyk', 
			),
			
			'ulotki-skladane' => array( 
				'attribute_pa_format' => '210x99mm-210x198mm',
				'attribute_pa_podloze' => 'kreda-130g', 
				'attribute_pa_orientacja' => 'pionowo', 
				'attribute_pa_zadruk' => 'dwustronnie-kolorowe-4x4-cmyk', 
			),
			
			'pocztowki' => array( 
				'attribute_pa_format' => 'a6-105x148mm',
				'attribute_pa_podloze' => 'kreda-300g', 
				'attribute_pa_orientacja' => 'pionowo',
				'attribute_pa_zadruk' => 'dwustronnie-kolorowe-4x4-cmyk', 
			),
			
			'bilety' => array( 
				'attribute_pa_format' => '145x50mm',
				'attribute_pa_podloze' => 'kreda-130g', 
				'attribute_pa_orientacja' => 'pionowo',
				'attribute_pa_zadruk' => 'dwustronnie-kolorowe-4x4-cmyk', 
			),
			
			'dyplomy' => array( 
				'attribute_pa_format' => 'a4-210x297mm',
				'attribute_pa_podloze' => 'kreda-170g', 
				'attribute_pa_orientacja' => 'Pionowo',
				'attribute_pa_zadruk' => 'dwustronnie-kolorowe-4x4-cmyk', 
			),
			
			'plakaty' => array( 
				'attribute_pa_format' => 'a4-210x297mm',
				'attribute_pa_podloze' => 'kreda-130g', 
				'attribute_pa_orientacja' => 'pionowo',
				'attribute_pa_zadruk' => 'jednostronnie-kolorowe-4x0-cmyk', 
				
			)			
		
		);
		
		//naklad eksperyment z nowym summary app
		$input_settings = update_input_settings( $input_settings, "attribute_pa_naklad", array(				
        'unit' => 'szt',
        'unit_separator' => '-',
      
				'labels' => array( 
					'l' => 'Nakład'
				)
			)
		);
    		
		//format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_format", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Format',
          'options' => $_filter_data->options[ 'ulotka' ][ 'format' ],
					'opanel' => array(
						'title' => 'Wybierz rozmiar', 
					), 
				)
			)
		);
		
   
    
		//orientacja
		$input_settings = update_input_settings( $input_settings, "attribute_pa_orientacja", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_orientacja", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Orientacja',
          'options' => $_filter_data->options[ 'orientacja'],
					'opanel' => array(
						'title' => 'Wybierz orientcję', 
					), 
				)
			)
		);
		
		//podloze
		$input_settings = update_input_settings( $input_settings, "attribute_pa_podloze", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_podloze", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Rodaj papieru',
          'options' => $_filter_data->options[ 'rodzaj-papieru' ],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru', 
					), 
				)
			)
		);	


		//Zadruk 
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zadruk", array(
				'type' => 'select',		
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_zadruk", $defaults_by_markup_tag ),
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Zadruk',
					'options' => array(
						'jednostronnie-kolorowe-4x0-cmyk' => 'Jednostronnie kolorowy', 
						'dwustronnie-kolorowe-4x4-cmyk' => 'Dwustronnie kolorowy'
					),
					'opanel' => array(
						'title' => 'Wybierz zadruk', 
					), 
				)
			)
		);


		//foliowanie
		$input_settings = update_input_settings( $input_settings, "attribute_pa_uszlachetnienie", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'folia-brak',			
				'labels' => array( 
					'l' => 'Foliowanie',
					'options' => $_filter_data->options[ 'foliowanie' ],
					'opanel' => array(
						'title' => 'Wybierz folię', 
					), 
				)
			)
		);	
		
    /*
    
		//lakier punktowy
		$input_settings = update_input_settings( $input_settings, "attribute_pa_lakier-wybiorczy", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'brak',			
				'labels' => array( 
					'l' => 'Lakier punktowy',	
          'options' => array(
						'brak' => 'Brak lakieru punktowego', 
            'blyszczacy-lakier-punktowy-dwustronnie' => 'Dwustronny lakier punktowy', 
            'blyszczacy-lakier-punktowy-jednostronnie' => 'Jednostronny lakier punktowy', 
					),
          'opanel' => array(
						'title' => 'Lakierowanie wybiórcze', 
					), 
				)
			)
		);	
		*/
		
    
    
		
		return $input_settings;
	}
	
	

	/**
	* Kalkulator WIZYTÓWKI:
	*	
	*
	* dopisuje szczegółową konfiguracje pól formularza do wyboru wariantu produktu
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__wizytowki ( $input_settings ){
		$_filter_data = new gaad_input_settings_filter_data();
		
		/*
		* Ustawienia  wartości początkowych dla pól formularza w zależności od ustawionego w adminie markup_tag
		*/		
		$defaults_by_markup_tag = array(
			'default' => array( 
				'attribute_pa_format' => '90x50mm',
				'attribute_pa_podloze' => 'kreda-300g', 
				'attribute_pa_zlocenia' => 'brak-zlocenia-0x0', 
				'attribute_pa_lakier-wybiorczy' => 'brak', 
			),
			
			'wizytowki-zlocone' => array( 
				'attribute_pa_format' => '90x50mm',
				'attribute_pa_podloze' => 'kreda-300g',
				'attribute_pa_zlocenia' => 'zlocenie-jednostronnie-1x0', 	
				'attribute_pa_lakier-wybiorczy' => 'brak', 
			),
			
			'wizytowki-skladane' => array( 
				'attribute_pa_format' => '90x50mm-180x50mm',
				'attribute_pa_podloze' => 'kreda-300g',
				'attribute_pa_zlocenia' => 'brak-zlocenia-0x0', 	
				'attribute_pa_lakier-wybiorczy' => 'brak', 
			),
			
			'wizytowki-ozdobne' => array( 
				'attribute_pa_format' => '90x50mm',
				'attribute_pa_podloze' => 'papier-ozdobny-1-300g', 
				'attribute_pa_zlocenia' => 'brak-zlocenia-0x0', 
				'attribute_pa_lakier-wybiorczy' => 'brak',
			),
			
			'zaproszenia' => array( 
				'attribute_pa_format' => 'dl-99x210mm',
				'attribute_pa_podloze' => 'kreda-300g', 
				'attribute_pa_zlocenia' => 'brak-zlocenia-0x0', 
				'attribute_pa_lakier-wybiorczy' => 'brak',
			),
			
		
		);
		
		
		
		
		//naklad eksperyment z nowym summary app
		$input_settings = update_input_settings( $input_settings, "attribute_pa_naklad", array(				
        'unit' => 'szt',
        'unit_separator' => '-',
      
				'labels' => array( 
					'l' => 'Nakład'
				)
			)
		);
    
    
    
    
    	//format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_format", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Format',
          'options' => array( 
            '90x50mm' => '90 x 50 mm',
            '85x55mm' => '85 x 55 mm',
      
            '85x55mm-170x55mm' => '85 x 55 mm (V)',
            '90x50mm-180x50mm' => '90 x 50 mm (V)',
            '90x55mm-135x55mm' => '90 x 55 mm + 55 x 55 mm (V)',
      
          ),
					'opanel' => array(
						'title' => 'Wybierz rozmiar wizytówki', 
					), 
				)
			)
		);
    

		//podloze
		$input_settings = update_input_settings( $input_settings, "attribute_pa_podloze", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_podloze", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Rodaj papieru',      
          'options' => $_filter_data->options[ 'rodzaj-papieru' ],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru', 
					), 
				)
			)
		);	


		//Zadruk 
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zadruk", array(
				'type' => 'select',		
				'class' => 'inline',
				'default' => 'dwustronnie-kolorowe-4x4-cmyk',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Zadruk',
					'options' => array(
						'jednostronnie-kolorowe-4x0-cmyk' => 'Jednostronnie kolorowy', 
						'dwustronnie-kolorowe-4x4-cmyk' => 'Dwustronnie kolorowy'
					),
					'opanel' => array(
						'title' => 'Wybierz zadruk wizytówki', 
					),
				)
			)
		);


		//foliowanie
		$input_settings = update_input_settings( $input_settings, "attribute_pa_uszlachetnienie", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'folia-brak',			
				'labels' => array( 
					'l' => 'Foliowanie',
					'options' => $_filter_data->options[ 'foliowanie' ],
					'opanel' => array(
						'title' => 'Wybierz folię', 
					), 
				)
			)
		);	

		//zlocenie
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zlocenia", array(
				'type' => 'select',
				'custom_val' => false, 			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_zlocenia", $defaults_by_markup_tag ),'zlocenie-jednostronnie-1x0',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Złocenia',
					'options' => array(
						'brak-zlocenia-0x0' => 'Brak złocenia'
					),
					'opanel' => array(
						'title' => 'Wybierz ilość stron złocenia', 
					), 
				)
			)
		);	
		

		
		//lakier punktowy
		$input_settings = update_input_settings( $input_settings, "attribute_pa_lakier-wybiorczy", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'brak',			
				'labels' => array( 
					'l' => 'Lakier punktowy',	
          'options' => array(
						'brak' => 'Brak lakieru punktowego', 
            'blyszczacy-lakier-punktowy-dwustronnie' => 'Dwustronny lakier punktowy', 
            'blyszczacy-lakier-punktowy-jednostronnie' => 'Jednostronny lakier punktowy', 
					),
          'opanel' => array(
						'title' => 'Lakierowanie wybiórcze', 
					), 
				)
			)
		);	
		
		
		
		
		return $input_settings;
	}


	/**
	* dopisuje szczegółową konfiguracje pól formularza do wyboru wariantu produktu
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__katalog ( $input_settings ) {
    $_filter_data = new gaad_input_settings_filter_data();
		//format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',
				'custom_val' => true, 
				'unit' => 'mm', 
				'unit_separator' => '',
				'class' => 'inline',
				'default' => 'a5-148x210mm',
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Format',
          'options' => $_filter_data->options['ksiazka']['format'], 
					'opanel' => array(
						'title' => 'Wybierz rozmiar katalogu', 
					), 
				)
			)
		);	

		//oprawa	
		$input_settings = update_input_settings( $input_settings, "attribute_pa_oprawa", array(
				'type' => 'select',			
				'unit' => 'mm', 			
				'class' => 'inline',
				'default' => 'oprawa-zeszytowa',	//tutaj filrt trzeba umiescic pobierajacy defaul zaleznie od podstrony katalogu: szyty klejony		
				'labels' => array( 
					'l' => 'Rodzaj',				
					'options' => $_filter_data->options['ksiazka']['rodzaj-oprawy'], 
					'opanel' => array(
						'title' => 'Wybierz rodzaj oprawy katalogu', 
					), 
				)
			)
		);


		//papier okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_papier-okladki", array(
				'type' => 'select',
				'custom_val' => false, 
				'unit' => 'mm', 
				'unit_separator' => '',
				'class' => 'inline',
				'default' => 'kreda-300g',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Papier okładki',
					'options' => $_filter_data->options['rodzaj-papieru'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru okładki', 
					), 
				)
			)
		);


		//foliowanie okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_uszlachetnienie-okladki", array(
				'type' => 'select',
				'custom_val' => false, 			
				'class' => 'inline',
				'default' => 'folia-brak',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Uszlachetnienie okładki',
					'options' => $_filter_data->options[ 'foliowanie' ],
					'opanel' => array(
						'title' => 'Wybierz uszlachetnienie okładki', 
					), 
				)
			)
		);	

		//lakier wybiorczy okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_lakier-wybiorczy-okladki", array(
				'type' => 'checkbox',			
				'class' => '',
				'default' => false,			
				'labels' => array( 
					'l' => 'Lakier punktowy na okładce',				 
				)
			)
		);

		//uszlachetnienie stron
		$input_settings = update_input_settings( $input_settings, "attribute_pa_uszlachetnienie", array(
				'type' => 'select',
				'custom_val' => false, 			
				'class' => 'inline',
				'default' => 'folia-brak',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Uszlachetnienie stron',
					'options' => $_filter_data->options[ 'foliowanie' ],
					'opanel' => array(
						'title' => 'Wybierz uszlachetnienie stron', 
					), 
				)
			)
		);


		//ilość stron 4+4
		$input_settings = update_input_settings( $input_settings, "attribute_pa_ilosc-stron-kolorowych", array(
				'type' => 'select',
				'class' => 'inline',
				'default' => '8',			
				'labels' => array( 
					'l' => 'Ilość stron',
					'options' => array(					
					),
					'opanel' => array(
						'title' => 'Ilość stron + okładka (4 str.)', 
					),
				)
			)
		);

		//Orientacja	
		$input_settings = update_input_settings( $input_settings, "attribute_pa_orientacja", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'pionowa',			
				'labels' => array( 
					'l' => 'Orientacja',
					'options' => array(
						'pionowa' => 'Pionowo', 
						'pozioma' => 'Poziomo',
					),
					'opanel' => array(
						'title' => 'Orientacja katalogu',
					), 
				)
			)
		);

		//rodzaj papieru kolor
		$input_settings = update_input_settings( $input_settings, "attribute_pa_papier-kolor", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'kreda-115g',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Rodzaj papieru',
					'options' => $_filter_data->options['rodzaj-papieru'],
					'opanel' => array(
						'title' => 'Wybierz rodzja papieru wnętrza katalogu', 
					), 
				)
			)
		);



		return $input_settings;
	}


	/**
	* 
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__ksiazka ( $input_settings ) {
    $_filter_data = new gaad_input_settings_filter_data();
		
    
    
		/*
    * niep[ełne wpisy ponizej sluza tyko do summary attribute app]
    */
    
    
		
		//naklad eksperyment z nowym summary app
		$input_settings = update_input_settings( $input_settings, "attribute_pa_ilosc-stron-czarno-bialych", array(				
        'unit' => 'szt',
        'unit_separator' => '-',
      
				'labels' => array( 
					'l' => 'Ilość stron BW'
				)
			)
		);
		
		//naklad eksperyment z nowym summary app
		$input_settings = update_input_settings( $input_settings, "attribute_pa_ilosc-stron-kolorowych", array(				
        'unit' => 'szt',
        'unit_separator' => '-',
      
				'labels' => array( 
					'l' => 'Ilość stron kolor'
				)
			)
		);
    
		//naklad eksperyment z nowym summary app
		$input_settings = update_input_settings( $input_settings, "attribute_pa_tytul-ksiazki", array(				       
				'labels' => array( 
					'l' => 'Tytuł'
				)
			)
		);
    
    //naklad eksperyment z nowym summary app
		$input_settings = update_input_settings( $input_settings, "attribute_pa_numer-isbnissn", array(				       
				'labels' => array( 
					'l' => 'ISBN/ISSN'
				)
			)
		);
    
    
    
    
    
    
    
    
    
    
    //format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',
				'custom_val' => false, //własny format
				'unit' => 'mm', 
				'unit_separator' => '',
				'class' => 'inline',
				//'default' => 'a5-148x210mm',
				'default' => 'b5-175x250mm',
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Format',
          'options' => $_filter_data->options['ksiazka']['format'], 
					'opanel' => array(
						'title' => 'Wybierz rozmiar książki', 
					), 
				)
			)
		);	

		//naklad	
		$input_settings = update_input_settings( $input_settings, "attribute_pa_naklad", array(
				'type' => 'text',
				'custom_val' => true, 
				'unit' => 'szt', 
        'max' => '1000',
        'min' => '2', 
        'unit' => 'szt',
				'unit_separator' => '-',
				'class' => 'inline',
				'default' => '50',
        'val_validate' => '\d+', 
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Nakład'				
				)
			)
		);

    
		//ilosc stron 1+1
		$input_settings = update_input_settings( $input_settings, "attribute_pa_ilosc-stron-czarno-bialych", array(
				'type' => 'text',			
				'class' => 'inline',
				'default' => '50',
        'max' => '500',
        'min' => '2', 
        'val_validate' => '\d+', 
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Ilość stron 1+0, 1+1'
				)
			)
		);
 
		//ilosc stron 4+4
		$input_settings = update_input_settings( $input_settings, "attribute_pa_ilosc-stron-kolorowych", array(
				'type' => 'text',			
				'class' => 'inline',
				'default' => '0',
        'max' => '500',
        'min' => '0', 
        'val_validate' => '\d+', 
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Ilość stron 4+0, 4+4'
				)
			)
		);


		//numery stron kolorowych
		$input_settings = update_input_settings( $input_settings, "attribute_pa_numery-stron-kolorowych", array(
				'type' => 'text',			
				'class' => '',
				'default' => '',
				'custom_val_validate' => '.*', // tutaj regexp poprawic
				'labels' => array( 
					'l' => 'Numery stron kolorowych'
				)
			)
		);

		//Porozrzucane strony kolorowe
		$input_settings = update_input_settings( $input_settings, "attribute_pa_porozrzucane-str-kolor", array(
				'type' => 'checkbox',			
				'default' => false,
        'disabled' => 'true', 
				'labels' => array( 
					'l' => 'Porozrzucane strony kolorowe',		
				)
			)
		);

		//Orientacja	
		$input_settings = update_input_settings( $input_settings, "attribute_pa_orientacja", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'pionowa',			
				'labels' => array( 
					'l' => 'Orientacja',
					'options' => $_filter_data->options['ksiazka']['orientacja'],
					'opanel' => array(
						'title' => 'Orientacja katalogu',
					), 
				)
			)
		);

		//oprawa
		$input_settings = update_input_settings( $input_settings, "attribute_pa_oprawa", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'oprawa-miekka-klejona',			
				'labels' => array( 
					'l' => 'Oprawa',
					'options' => $_filter_data->options['ksiazka']['rodzaj-oprawy'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj oprawy ksiażki', 
					), 
				)
			)
		);

		//rodzaj okładki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_rodzaj-okladki", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'standardowa',			
				'labels' => array( 
					'l' => 'Rodzaj okładki',
					'options' => $_filter_data->options['ksiazka']['rodzaj-okladki'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj okładki', 
					), 
				)
			)
		);

		//tutaj pole z szerokością skrzydełek, potem :)

		//papier okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_papier-okladki", array(
				'type' => 'select',
				'custom_val' => false, 
				'unit' => 'mm', 
				'unit_separator' => '',
				'class' => 'inline two-columns',
				'default' => 'kreda-300g',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Papier okładki',
					'options' => $_filter_data->options['rodzaj-papieru'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru okładki', 
					), 
				)
			)
		);

		//foliowanie okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_uszlachetnienie-okladki", array(
				'type' => 'select',
				'custom_val' => false, 			
				'class' => 'inline',
				'default' => 'folia-brak',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Uszlachetnienie okładki',
					'options' => $_filter_data->options[ 'foliowanie' ],
					'opanel' => array(
						'title' => 'Wybierz uszlachetnienie okładki', 
					), 
				)
			)
		);	

		//lakier wybiorczy okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_lakier-wybiorczy-okladki", array(
				'type' => 'checkbox',			
				'class' => '',
				'default' => false,			
				'labels' => array( 
					'l' => 'Lakier punktowy na okładce',				 
				)
			)
		);

		//Obwoluta
		$input_settings = update_input_settings( $input_settings, "attribute_pa_obwoluta", array(
				'type' => 'checkbox',			
				'class' => '',
				'default' => false,
        'disabled' => 'true', 
				'labels' => array( 
					'l' => 'Obwoluta',				 
				)
			)
		);

		//Zadruk okladka
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zadruk-okladki", array(
				'type' => 'select',		
				'class' => 'inline',
				'default' => 'jednostronnie-kolorowe-4x0-cmyk',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Zadruk okładki',
					'options' => $_filter_data->options[ 'zadruk' ],
					'opanel' => array(
						'title' => 'Wybierz zadruk okładki książki', 
					), 
				)
			)
		);

		//Rodzaj papieru 1+1, 1+0
		$input_settings = update_input_settings( $input_settings, "attribute_pa_papier-czarno-bialy", array(
				'type' => 'select',
				'class' => 'inline two-columns',
				'default' => 'offset-80g',			
				'labels' => array( 
					'l' => 'Papier 1+0, 1+1',
					'options' => $_filter_data->options['rodzaj-papieru'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru stron czarno-białych', 
					), 
				)
			)
		);

		//Zadruk 1+1
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zadruk-strony-czarno-biale", array(
				'type' => 'select',		
				'class' => 'inline',
				'default' => 'dwustronnie-czarno-biale-1x1',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Zadruk cz-b',
					'options' =>  $_filter_data->options[ 'zadruk' ],
					'opanel' => array(
						'title' => 'Wybierz zadruk stron czarno-białych', 
					), 
				)
			)
		);


		//Rodzaj papieru 4+4
		$input_settings = update_input_settings( $input_settings, "attribute_pa_papier-kolor", array(
				'type' => 'select',
				'class' => 'inline two-columns',
				'default' => 'offset-90g',			
				'labels' => array( 
					'l' => 'Papier 4+0, 4+4',
					'options' => $_filter_data->options['rodzaj-papieru'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru stron kolorowych', 
					), 
				)
			)
		);


		//Zadruk okladka
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zadruk-strony-kolorowe", array(
				'type' => 'select',		
				'class' => 'inline',
				'default' => 'dwustronnie-kolorowe-4x4-cmyk',			
				'labels' => array( 
					'l' => 'Zadruk kolor',
					'options' =>  $_filter_data->options[ 'zadruk' ],
					'opanel' => array(
						'title' => 'Wybierz zadruk stron kolorowych', 
					), 
				)
			)
		);


		//tytuł ksiażki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_tytul-ksiazki", array(
				'type' => 'text',
				'default' => '',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Tytuł książki'
				)
			)
		);


		//attribute_pa_numer-isbnissn
		$input_settings = update_input_settings( $input_settings, "attribute_pa_numer-isbnissn", array(
				'type' => 'text',
				'default' => '',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Numer ISBN/ISSN'
				)
			)
		);


		//Pakowanie egzemplarzy w folię
		$input_settings = update_input_settings( $input_settings, "attribute_pa_pakowanie-w-folie", array(
				'type' => 'checkbox',			
				'default' => false,		
				'labels' => array( 
					'l' => 'Pakowanie egzemplarzy w folię'				
				)
			)
		);

		//Wiercenie otworów
		$input_settings = update_input_settings( $input_settings, "attribute_pa_wiercenie-otworow", array(
				'type' => 'checkbox',			
				'default' => false,		
				'labels' => array( 
					'l' => 'Wiercenie otworów'				
				)
			)
		);



		return $input_settings;
	}

	/**
	* 
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__bloczki ( $input_settings ) {
    $_filter_data = new gaad_input_settings_filter_data();
		//format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',
				'custom_val' => true, 
				'unit' => 'mm', 
				'unit_separator' => '',
				'class' => 'inline',
				'default' => 'a5-148x210mm',
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Format',
					'opanel' => array(
						'title' => 'Wybierz rozmiar książki', 
					), 
				)
			)
		);	

		//naklad	
		$input_settings = update_input_settings( $input_settings, "attribute_pa_naklad", array(
				'type' => 'text',
				'custom_val' => true, 
				'unit' => 'szt', 
				'unit_separator' => '-',
				'class' => 'inline',
				'default' => '50',
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Nakład'				
				)
			)
		);

		//podloze
		$input_settings = update_input_settings( $input_settings, "attribute_pa_podloze", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_podloze", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Rodaj papieru',
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru', 
					), 
				)
			)
		);	

		//ilosc kartek notesu
		$input_settings = update_input_settings( $input_settings, "attribute_pa_ilosc-kart", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => '25',
				
				'labels' => array( 
					'l' => 'Ilość kartek'
				)
			)
		);


		//Orientacja	
		$input_settings = update_input_settings( $input_settings, "attribute_pa_orientacja", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'pionowa',			
				'labels' => array( 
					'l' => 'Orientacja',
					'options' => array(
						'pionowa' => 'Pionowo', 
						'pozioma' => 'Poziomo',
					),
					'opanel' => array(
						'title' => 'Orientacja katalogu',
					), 
				)
			)
		);

		//oprawa
		$input_settings = update_input_settings( $input_settings, "attribute_pa_oprawa", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'oprawa-miekka-klejona',			
				'labels' => array( 
					'l' => 'Oprawa',
					'options' => array(
						'oprawa-miekka-klejona' => 'Oprawa miękka klejona', 
						'oprawa-miekka-szyta-nicmi' => 'Oprawa miękka szyto-klejona', 
						'oprawa-spiralna' => 'Oprawa spiralna', 
						'oprawa-zeszytowa' => 'Oprawa zeszytowa',
						'oprawa-twarda-klejona' => 'Oprawa twarda klejona', 
						'oprawa-twarda-szyta-nicmi' => 'Oprawa twarda szyto-klejona', 
					),
					'opanel' => array(
						'title' => 'Wybierz rodzaj oprawy ksiażki', 
					), 
				)
			)
		);

		//rodzaj okładki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_rodzaj-okladki", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'standardowa',			
				'labels' => array( 
					'l' => 'Rodzaj okładki',
					'options' => array(
						'standardowa' => 'Standrdowa', 
						'ze-skrzydelkami' => 'Ze skrzydełkami', 
					),
					'opanel' => array(
						'title' => 'Wybierz rodzaj okładki', 
					), 
				)
			)
		);

		//tutaj pole z szerokością skrzydełek, potem :)

		//papier okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_papier-okladki", array(
				'type' => 'select',
				'custom_val' => false, 
				'unit' => 'mm', 
				'unit_separator' => '',
				'class' => 'inline two-columns',
				'default' => 'karton-arktika-230g',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Papier okładki',
					'options' => $_filter_data->options['rodzaj-papieru'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru okładki', 
					), 
				)
			)
		);

		//foliowanie okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_uszlachetnienie-okladki", array(
				'type' => 'select',
				'custom_val' => false, 			
				'class' => 'inline',
				'default' => 'folia-brak',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Uszlachetnienie okładki',
					'options' => $_filter_data->options[ 'foliowanie' ],
					'opanel' => array(
						'title' => 'Wybierz uszlachetnienie okładki', 
					), 
				)
			)
		);	

		//lakier wybiorczy okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_lakier-wybiorczy-okladki", array(
				'type' => 'checkbox',			
				'class' => '',
				'default' => false,			
				'labels' => array( 
					'l' => 'Lakier punktowy na okładce',				 
				)
			)
		);

		

		//Zadruk okladka
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zadruk", array(
				'type' => 'select',		
				'class' => 'inline',
				'default' => 'jednostronnie-kolorowe-4x0-cmyk',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Zadruk stron',
					'options' => array(
						'jednostronnie-kolorowe-4x0-cmyk' => 'Jednostronnie kolorowy', 					
						'jednostronnie-czarno-biale-1x0' => 'Jednostronnie czarno-biały', 
					
					),
					'opanel' => array(
						'title' => 'Wybierz zadruk', 
					), 
				)
			)
		);
		
		//oprawa
		$input_settings = update_input_settings( $input_settings, "attribute_pa_oprawa", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => 'oprawa-miekka-klejona',			
				'labels' => array( 
					'l' => 'Oprawa',
					'options' => array(
						'oprawa-miekka-klejona' => 'Oprawa miękka klejona', 
						'oprawa-miekka-szyta-nicmi' => 'Oprawa miękka szyto-klejona', 
						'oprawa-spiralna' => 'Oprawa spiralna', 
						'oprawa-zeszytowa' => 'Oprawa zeszytowa',
						'oprawa-twarda-klejona' => 'Oprawa twarda klejona', 
						'oprawa-twarda-szyta-nicmi' => 'Oprawa twarda szyto-klejona', 
					),
					'opanel' => array(
						'title' => 'Wybierz rodzaj oprawy ksiażki', 
					), 
				)
			)
		);
		//Zadruk okladka
		$input_settings = update_input_settings( $input_settings, "attribute_pa_zadruk-okladki", array(
				'type' => 'select',		
				'class' => 'inline',
				'default' => 'jednostronnie-kolorowe-4x0-cmyk',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Zadruk okładki',
					'options' => array(
						'jednostronnie-kolorowe-4x0-cmyk' => 'Jednostronnie kolorowy', 						
						'jednostronnie-czarno-biale-1x0' => 'Jednostronnie czarno-biały', 						
					),
					'opanel' => array(
						'title' => 'Wybierz zadruk okładki książki', 
					), 
				)
			)
		);
		
		
		
		//papier okladki
		$input_settings = update_input_settings( $input_settings, "attribute_pa_papier-okladki", array(
				'type' => 'select',
				'custom_val' => false, 
				'unit' => 'mm', 
				'unit_separator' => '',
				'class' => 'inline',
				'default' => 'kreda-170g',
				'custom_val_validate' => '.*',
				'labels' => array( 
					'l' => 'Papier okładki',
					'options' => $_filter_data->options['rodzaj-papieru'],
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru okładki', 
					), 
				)
			)
		);

		
		
		
		//Pakowanie egzemplarzy w folię
		$input_settings = update_input_settings( $input_settings, "attribute_pa_pakowanie-w-folie", array(
				'type' => 'checkbox',			
				'default' => false,		
				'labels' => array( 
					'l' => 'Pakowanie egzemplarzy w folię'				
				)
			)
		);

		//Wiercenie otworów
		$input_settings = update_input_settings( $input_settings, "attribute_pa_wiercenie-otworow", array(
				'type' => 'checkbox',			
				'default' => false,		
				'labels' => array( 
					'l' => 'Wiercenie otworów'				
				)
			)
		);



		return $input_settings;
	}
	

	/**
	* Ustawiernia kalkulatora baner
	*
	* @return void
	*/
	static function gaad_product_input_settings_filter__outdoor_baner ( $input_settings ) {
		$_filter_data = new gaad_input_settings_filter_data();
    
		$defaults_by_markup_tag = array(
			'default' => array( 
				'attribute_pa_format' => '100x200cm',
				'attribute_pa_podloze' => '',				
			),
			
			'plakatxxl' => array( 
				'attribute_pa_format' => 'a1-594x841mm',
				'attribute_pa_podloze' => 'kreda-150g',				
			),
			
			'tapeta' => array( 
				'attribute_pa_format' => '100x260cm',
				'attribute_pa_podloze' => 'tapeta-1',			
			),
			
			
			
		);
		
		//format
		$input_settings = update_input_settings( $input_settings, "attribute_pa_format", array(
				'type' => 'select',
				'custom_val' => true, 
				'unit' => 'cm', 
				'unit_separator' => '-',
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_format", $defaults_by_markup_tag ),	
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Format',
					'opanel' => array(
						'title' => 'Wybierz rozmiar banera', 
					), 
				)
			)
		);	
		
		//podloze
		$input_settings = update_input_settings( $input_settings, "attribute_pa_podloze", array(
				'type' => 'select',			
				'class' => 'inline',
				'default' => gaad_input_settings_filter::get_default( "attribute_pa_podloze", $defaults_by_markup_tag ),			
				'labels' => array( 
					'l' => 'Rodaj papieru',
					'opanel' => array(
						'title' => 'Wybierz rodzaj papieru', 
					), 
				)
			)
		);	
		
		//wykonczenie banera
		$input_settings = update_input_settings( $input_settings, "attribute_pa_wykonczenie-banera", array(
				'type' => 'select',
				'custom_val' => true, 
				'unit' => 'cm', 
				'unit_separator' => '-',
				'class' => 'inline',
				'default' => 'oczka-50cm',
				'custom_val_validate' => '\d+',
				'labels' => array( 
					'l' => 'Wykończenie banera',
					'opanel' => array(
						'title' => 'Wybierz rodzaj wykończenia', 
					), 
				)
			)
		);	

		
		

		return $input_settings;
	}
	
}

?>