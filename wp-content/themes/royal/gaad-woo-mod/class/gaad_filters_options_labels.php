<?php 

/*
* Opcje wspolne dla pól formularzy, głównie dla klasy gaad_input_settings_filter,
* Zawiera labele wartośći atrybutów
*/ 

class gaad_input_settings_filter_data {
  
  /*
  * Tablica zawierająca labele selecta foliowanie 
  */
  var $options = array(
      'orientacja' => array(
        'pionowa' => 'Pionowo', 
				'pozioma' => 'Poziomo',
        'pionowo' => 'Pionowo', 
				'poziomo' => 'Poziomo',
      ),
    
      'zadruk' => array(
        'jednostronnie-kolorowe-4x0-cmyk' => 'Jednostronnie kolorowy', 
        'dwustronnie-kolorowe-4x4-cmyk' => 'Dwustronnie kolorowy', 
        'jednostronnie-czarno-biale-1x0' => 'Jednostronnie czarno-biały', 
        'dwustronnie-czarno-biale-1x1' => 'Dwustronnie czarno-biały', 
      ), 
    
      'rodzaj-papieru' => array(
        'alto_vol_1_5-80g' => 'Alto vol 1.5 80g',     
        'alto_vol_1_5-90g' => 'Alto vol 1.5 90g',     
        
        'ecobook_vol_2_0-70g' => 'Ecobook vol 2.0 70g', 
        'ecobook_vol_2_0-80g' => 'Ecobook vol 2.0 80g', 
        'ecobook_vol_2_0-90g' => 'Ecobook vol 2.0 90g', 
    
        'munken_cream_vol_1_5-80g' => 'Munken Cream vol 1.5 80g', 
        'munken_cream_vol_1_5-90g' => 'Munken Cream vol 1.5 90g', 
        'munken_white_vol_1_5-80g' => 'Munken White vol 1.5 80g', 
        'munken_white_vol_1_5-90g' => 'Munken White vol 1.5 90g', 
    
    
        'offset-70g' => 'Offset 70g',     
        'offset-80g' => 'Offset 80g',     
        'offset-90g' => 'Offset 90g',     
        'offset-100g' => 'Offset 100g',     
        'offset-120g' => 'Offset 120g',     
        'offset-150g' => 'Offset 150g',         
    
        'kreda-90g' => 'Kreda 90g', 
        'kreda-100g' => 'Kreda 100g', 
        'kreda-115g' => 'Kreda 115g', 
        'kreda-130g' => 'Kreda 130g', 
        'kreda-135g' => 'Kreda 135g', 
        'kreda-150g' => 'Kreda 150g', 
        'kreda-170g' => 'Kreda 170g', 
        'kreda-200g' => 'Kreda 200g', 
        'kreda-250g' => 'Kreda 250g', 
        'kreda-300g' => 'Kreda 300g', 
        'kreda-350g' => 'Kreda 350g', 
        
        'satyna-300g' => 'Satyna 300g', 
    
        'karton-arktika-230g' => 'Karton Arktika 230g', 
        'karton-arktika-250g' => 'Karton Arktika 250g', 
        'karton-alaska-230g' => 'Karton Alaska 230g', 
        'karton-alaska-250g' => 'Karton Alaska 250g', 
        
        'karton-2mm' => 'Karton 2mm i oklejka', 
        'karton-2_5mm' => 'Karton 2,5mm i oklejka', 
        
      ), 
    
      'ulotka' => array(
        'format' => array(
          'a6-105x148mm' => 'A6 (max 105 x 148 mm)',
          'b6-125x175mm' => 'B6 (max 120 x 170 mm)',          
          'a5-148x210mm' => 'A5 (max 148 x 210 mm)', 
          'b5-175x250mm' => 'B5 (max 170 x 240 mm)',
          'a4-210x297mm' => 'A4 (max 210 x 297 mm)',
          '99x210mm' => 'DL (max 99 x 210 mm)',
    
          '210x99mm-210x198mm' => 'A5 do A6 (V)', 
          '210x148mm-210x297mm' => 'A4 do A5 (V)', 
          '99x210mm-297x210mm' => 'A4 do DL (Z)', 
        ),
      ),
    
      'ksiazka' => array(
        'format' => array(
          'a6-105x148mm' => 'A6 (max 105 x 148 mm)',
          'b6-125x175mm' => 'B6 (max 120 x 170 mm)',          
          'a5-148x210mm' => 'A5 (max 148 x 210 mm)', 
          'b5-175x250mm' => 'B5 (max 170 x 240 mm)',
          'a4-210x297mm' => 'A4 (max 210 x 297 mm)',
        ),
        'orientacja' => array(
          'pionowa' => 'Szycie po długim boku', 
          'pozioma' => 'Szycie po krótkim boku',
				), 
        'rodzaj-oprawy' => array(
          'oprawa-miekka-klejona' => 'Oprawa miękka klejona', 
          'oprawa-miekka-szyta-nicmi' => 'Oprawa miękka szyto-klejona', 
          'oprawa-spiralna' => 'Oprawa spiralna', 
          'oprawa-zeszytowa' => 'Oprawa zeszytowa',
          'oprawa-twarda-klejona' => 'Oprawa twarda klejona', 
          'oprawa-twarda-szyta-nicmi' => 'Oprawa twarda szyto-klejona', 
        ), 
        'rodzaj-okladki' => array(
          'standardowa' => 'Standrdowa', 
          'ze-skrzydelkami' => 'Ze skrzydełkami', 
        ), 
        
    
      ),  
    
    
      'foliowanie' => array(
          'slug' => 'option_label',
          'folia-brak' => 'Brak folii',
    
          'folia-blysk-jednostronnie' => 'Jednostronna folia błyszcząca',
          'folia-blysk-dwustronnie' => 'Dwustronna folia błyszcząca',
          
          'folia-mat-jednostronnie' => 'Jednostronna folia matowa',
          'folia-mat-dwustronnie' => 'Dwustronna folia matowa',

          'folia-soft-touch-jednostronnie' => 'Jednostronna folia Soft touch',
          'folia-soft-touch-dwustronnie' => 'Dwustronna folia soft touch',
    
      ), 
    );
  
  
  var $brief = array(
    'keys_labels' => array(
        'pa_format' => 'Format', 
        'pa_zadruk' => 'Zadruk', 
        'pa_orientacja' => 'Orientacja', 
        'pa_podloze' => 'Podłoże', 
        'pa_naklad' => 'Nakład', 
        'pa_uszlachetnienie' => 'Uszlachetnienie', 
        'pa_termin-wykonania' => 'Termin', 
        'pa_item_status' => 'Status produktu',
        'pa_max_files' => 'Maks. plików', 
      ), 
    
    
    'pa_uszlachetnienie' => array(        
          'folia-brak' => 'Brak folii',
    
          'folia-blysk-jednostronnie' => 'Jednostronna folia błyszcząca',
          'folia-blysk-dwustronnie' => 'Dwustronna folia błyszcząca',
          
          'folia-mat-jednostronnie' => 'Jednostronna folia matowa',
          'folia-mat-dwustronnie' => 'Dwustronna folia matowa',

          'folia-soft-touch-jednostronnie' => 'Jednostronna folia Soft touch',
          'folia-soft-touch-dwustronnie' => 'Dwustronna folia soft touch',
    
    ),
    
    'pa_format' => array(
        'a6-105x148mm' => 'A6 (max 105 x 148 mm)',
        'b6-125x175mm' => 'B6 (max 120 x 170 mm)',          
        'a5-148x210mm' => 'A5 (max 148 x 210 mm)', 
        'b5-175x250mm' => 'B5 (max 170 x 240 mm)',
        'a4-210x297mm' => 'A4 (max 210 x 297 mm)',
        '99x210mm' => 'DL (max 99 x 210 mm)',

        '210x99mm-210x198mm' => 'A5 do A6 (V)', 
        '210x148mm-210x297mm' => 'A4 do A5 (V)', 
        '99x210mm-297x210mm' => 'A4 do DL (Z)', 
      ), 
    
    'pa_zadruk' => array(
        'jednostronnie-kolorowe-4x0-cmyk' => 'Jednostronnie kolorowy', 
        'dwustronnie-kolorowe-4x4-cmyk' => 'Dwustronnie kolorowy', 
        'jednostronnie-czarno-biale-1x0' => 'Jednostronnie czarno-biały', 
        'dwustronnie-czarno-biale-1x1' => 'Dwustronnie czarno-biały', 
      ), 
    
    'pa_podloze' => array(
        'alto_vol_1_5-80g' => 'Alto vol 1.5 80g',     
        'alto_vol_1_5-90g' => 'Alto vol 1.5 90g',     
        
        'ecobook_vol_2_0-70g' => 'Ecobook vol 2.0 70g', 
        'ecobook_vol_2_0-80g' => 'Ecobook vol 2.0 80g', 
        'ecobook_vol_2_0-90g' => 'Ecobook vol 2.0 90g', 
    
        'munken_cream_vol_1_5-80g' => 'Munken Cream vol 1.5 80g', 
        'munken_cream_vol_1_5-90g' => 'Munken Cream vol 1.5 90g', 
        'munken_white_vol_1_5-80g' => 'Munken White vol 1.5 80g', 
        'munken_white_vol_1_5-90g' => 'Munken White vol 1.5 90g', 
    
    
        'offset-70g' => 'Offset 70g',     
        'offset-80g' => 'Offset 80g',     
        'offset-90g' => 'Offset 90g',     
        'offset-100g' => 'Offset 100g',     
        'offset-120g' => 'Offset 120g',     
        'offset-150g' => 'Offset 150g',         
    
        'kreda-90g' => 'Kreda 90g', 
        'kreda-100g' => 'Kreda 100g', 
        'kreda-115g' => 'Kreda 115g', 
        'kreda-130g' => 'Kreda 130g', 
        'kreda-135g' => 'Kreda 135g', 
        'kreda-150g' => 'Kreda 150g', 
        'kreda-170g' => 'Kreda 170g', 
        'kreda-200g' => 'Kreda 200g', 
        'kreda-250g' => 'Kreda 250g', 
        'kreda-300g' => 'Kreda 300g', 
        'kreda-350g' => 'Kreda 350g', 
        
        'satyna-300g' => 'Satyna 300g', 
    
        'karton-arktika-230g' => 'Karton Arktika 230g', 
        'karton-arktika-250g' => 'Karton Arktika 250g', 
        'karton-alaska-230g' => 'Karton Alaska 230g', 
        'karton-alaska-250g' => 'Karton Alaska 250g', 
        
        'karton-2mm' => 'Karton 2mm i oklejka', 
        'karton-2_5mm' => 'Karton 2,5mm i oklejka', 
        
      ), 
  );
  
  
  public function __constructor(){
    return $this;
  }
  
}


?>