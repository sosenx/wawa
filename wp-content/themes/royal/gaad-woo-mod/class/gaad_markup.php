<?php 
/*
* Klasa oblizca marże danego produktu w zależności od zdeklarowanych progów
* Stworzona do obliczania marży ksiązek i bardziej złożonych realizacji
* proste marże można liczyć też za pomocą funkcji get_markup i filtra gaad_calc_markup
*/ 


class gaad_markup{
	
	/*
	przchowuje granice marży dla konkretnych kalkulatorów i części realizacji np kalkulator: ksiażka, część kolorowa
	*/
	public $calc_class;
	public $quantity;
	public $part;
	
	public $_markup_db = array(
		
		'kalendarz' => array(
			'trojdzielny' => array(		
		
				'qlist' => array(
					
					'1.31' => array( 10, 1000000 ),
					'1.32' => array( 20, 1000000 ),
					'1.38' => array( 30, 1000000 ),
					'1.38' => array( 50, 1000000 )
				),
				
				'medium' => array(				
					'4' => array( 1, 3 ),
					'3.9' => array( 4, 6 ),
		
					'3.8' => array( 7, 12 ),
					'3.4' => array( 13, 30 ),
					'2.9' => array( 31, 49 ),
					'1.7' => array( 51, 99999 )
				),
				
				'accesories' => array(				
					'1.55' => array( 1, 3 ),
					'1.5' => array( 4, 6 ),
		
					'1.45' => array( 7, 12 ),
					'1.43' => array( 13, 30 ),
					'1.4' => array( 31, 49 ),
					'1.35' => array( 51, 99999 )
				),
			),
		),
		
		
		
		
		
		
		'rollup' => array(
			'default' => array(		
		
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'4' => array( 1, 3 ),
					'3.9' => array( 4, 6 ),
		
					'3.8' => array( 7, 12 ),
					'3.4' => array( 13, 30 ),
					'2.9' => array( 31, 49 ),
					'1.7' => array( 51, 99999 )
				),
				
				'accesories' => array(				
					'1.55' => array( 1, 3 ),
					'1.5' => array( 4, 6 ),
		
					'1.45' => array( 7, 12 ),
					'1.43' => array( 13, 30 ),
					'1.4' => array( 31, 49 ),
					'1.35' => array( 51, 99999 )
				),
		
		
			),
		
			'xbaner' => array(
		
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'4' => array( 1, 3 ),
					'3.9' => array( 4, 6 ),
		
					'3.8' => array( 7, 12 ),
					'3.4' => array( 13, 30 ),
					'2.9' => array( 31, 49 ),
					'1.7' => array( 51, 99999 )
				),
				
				'accesories' => array(				
					'1.55' => array( 1, 3 ),
					'1.5' => array( 4, 6 ),
		
					'1.45' => array( 7, 12 ),
					'1.43' => array( 13, 30 ),
					'1.4' => array( 31, 49 ),
					'1.35' => array( 51, 99999 )
				),
		
		
			),	
		
			'potykacz' => array(
		
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'4' => array( 1, 3 ),
					'3.9' => array( 4, 6 ),
		
					'3.8' => array( 7, 12 ),
					'3.4' => array( 13, 30 ),
					'2.9' => array( 31, 49 ),
					'1.7' => array( 51, 99999 )
				),
				
				'accesories' => array(				
					'1.55' => array( 1, 3 ),
					'1.5' => array( 4, 6 ),
		
					'1.45' => array( 7, 12 ),
					'1.43' => array( 13, 30 ),
					'1.4' => array( 31, 49 ),
					'1.35' => array( 51, 99999 )
				),
		
		
			),
		
		
		),
		
		
		
		
		'outdoor_baner' => array(
			'default' => array(
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'3.7' => array( 1, 6 ),
					'2.8' => array( 7, 12 ),
					'2.4' => array( 13, 30 ),
					'1.9' => array( 31, 49 ),
					'1.5' => array( 51, 99999 )
				),
			),
			
			'owv' => array(
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'3.7' => array( 1, 6 ),
					'2.8' => array( 7, 12 ),
					'2.4' => array( 13, 30 ),
					'1.9' => array( 31, 49 ),
					'1.5' => array( 51, 99999 )
				),
			),
			
			'monomer' => array(
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'3.7' => array( 1, 6 ),
					'2.8' => array( 7, 12 ),
					'2.4' => array( 13, 30 ),
					'1.9' => array( 31, 49 ),
					'1.5' => array( 51, 99999 )
				),
			),
			
			'baner' => array(
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'3.7' => array( 1, 6 ),
					'2.8' => array( 7, 12 ),
					'2.4' => array( 13, 30 ),
					'1.9' => array( 31, 49 ),
					'1.5' => array( 51, 99999 )
				),
			),
			
			'plakatyxxl' => array(
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 ),					
					'1.8' => array( 6, 299 ),
					'1.7' => array( 7, 499 ),
					'1.5' => array( 8, 699 ),
					'1.4' => array( 9, 999 ),
					'1.31' => array( 10, 1000000),
					'1.32' => array( 20, 1000000),
					'1.38' => array( 30, 1000000),
					'1.38' => array( 50, 1000000)
				),
				
				'medium' => array(				
					'3' => array( 1, 6 ),
					'2.8' => array( 7, 12 ),
					'2.6' => array( 13, 30 ),
					'2.4' => array( 31, 49 ),
					'2' => array( 51, 99999 )
				),
			),
		
			'tapeta' => array(
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 2, 24 ),
					'4' => array( 3, 49 ),
					'2.5' => array( 4, 99 ),
					'2' => array( 5, 199 )					
				),
				
				'medium' => array(				
					'3' => array( 1, 6 ),
					'2.8' => array( 7, 12 ),
					'2.6' => array( 13, 30 ),
					'2.4' => array( 31, 49 ),
					'2' => array( 51, 99999 )
				),
			),
		),
		
		
		
		
		'ksiazka' => array(
			'default' => array(

				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 10, 24 ),
					'4' => array( 25, 49 ),
					'2.5' => array( 50, 99 ),
					'2' => array( 100, 199 ),
					//'1.9' => array( 150, 199 ),
					'1.8' => array( 200, 299 ),
					'1.7' => array( 300, 499 ),
					'1.5' => array( 500, 699 ),
					'1.4' => array( 700, 999 ),
					'1.38' => array( 1000, 1000000)
				),

				'bw' => array( //po kilku wycenach, podnioslem o .1
					'0' => array( 0, 0 ),
					'6' => array( 1, 9 ),
					'4' => array( 10, 24 ),
					'3' => array( 25, 49 ),
					'2.2' => array( 50, 99 ),
					'1.9' => array( 100, 199 ),				
					'1.7' => array( 200, 299 ),
					'1.6' => array( 300, 499 ),
					'1.5' => array( 500, 699 ),
					'1.4' => array( 700, 999 ),
					'1.38' => array( 1000, 1000000)
				),

				'color' => array(
				   '7' => array( 1, 9 ),
				   '4.5' => array( 10, 24 ),   
				   '4' => array( 25, 49 ),   
				   '3' => array( 50, 99 ),
				   '2.2' => array( 100, 199 ),			   			  
				   '1.9' => array( 200, 299 ),
				   '1.8' => array( 300, 499 ),
				   '1.7' => array( 500, 699 ),
				   '1.6' => array( 700, 999 ),
				   '1.5' => array( 1000, 1999 ),
				   '1.4' => array( 2000, 999999 ),

				),

				'cover' => array(
				   '5' => array( 1, 9 ),
				   '4' => array( 10, 24 ),   
				   '3' => array( 25, 49 ),   
				   '2' => array( 50, 99 ),
				   '1.7' => array( 100, 199 ),
				   '1.3' => array( 200, 299 ),
				   '1.2' => array( 300, 10000000
				   )
				)
			)
		),
		
		
		
		'bloczki' => array(
			'default' => array(

				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'10' => array( 1, 9 ),
					'5' => array( 10, 24 ),
					'4' => array( 25, 49 ),
					'2.5' => array( 50, 99 ),
					'2' => array( 100, 199 ),
					//'1.9' => array( 150, 199 ),
					'1.8' => array( 200, 299 ),
					'1.7' => array( 300, 499 ),
					'1.5' => array( 500, 699 ),
					'1.4' => array( 700, 999 ),
					'1.38' => array( 1000, 1000000)
				),

				'bw' => array( //po kilku wycenach, podnioslem o .1
					'0' => array( 0, 0 ),
					'6' => array( 1, 9 ),
					'4' => array( 10, 24 ),
					'3' => array( 25, 49 ),
					'2.2' => array( 50, 99 ),
					'1.9' => array( 100, 199 ),				
					'1.7' => array( 200, 299 ),
					'1.6' => array( 300, 499 ),
					'1.5' => array( 500, 699 ),
					'1.4' => array( 700, 999 ),
					'1.38' => array( 1000, 1000000)
				),

				'color' => array(
				   '7' => array( 1, 9 ),
				   '4.5' => array( 10, 24 ),   
				   '4' => array( 25, 49 ),   
				   '3' => array( 50, 99 ),
				   '2.2' => array( 100, 199 ),			   			  
				   '1.9' => array( 200, 299 ),
				   '1.8' => array( 300, 499 ),
				   '1.7' => array( 500, 699 ),
				   '1.6' => array( 700, 999 ),
				   '1.5' => array( 1000, 1999 ),
				   '1.4' => array( 2000, 999999 ),

				),

				'cover' => array(
				   '5' => array( 1, 9 ),
				   '4' => array( 10, 24 ),   
				   '3' => array( 25, 49 ),   
				   '2' => array( 50, 99 ),
				   '1.7' => array( 100, 199 ),
				   '1.3' => array( 200, 299 ),
				   '1.2' => array( 300, 10000000
				   )
				)

			
			),
		
		
			'klejone' => array(

				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					//'10' => array( 1, 9 ),
					//'5' => array( 10, 24 ),
					'2.5' => array( 20, 39 ),
					'2.3' => array( 40, 59 ),
					'2.2' => array( 60, 79 ),
					'2.1' => array( 80, 99 ),		
					'2' => array( 100, 199 ),					
					'1.8' => array( 200, 299 ),
					'1.7' => array( 300, 499 ),
					'1.5' => array( 500, 699 ),
					'1.4' => array( 700, 999 ),
					'1.38' => array( 1000, 1000000)
				),

				'bw' => array( //po kilku wycenach, podnioslem o .1
					'2.5' => array( 20, 39 ),
					'2.3' => array( 40, 59 ),
					'2.2' => array( 60, 79 ),
					'2.1' => array( 80, 99 ),		
					'2' => array( 100, 199 ),					
					'1.8' => array( 200, 299 ),
					'1.7' => array( 300, 499 ),
					'1.5' => array( 500, 699 ),
					'1.4' => array( 700, 999 ),
					'1.38' => array( 1000, 1000000)
				),

				'color' => array(
				   '2.5' => array( 20, 39 ),
					'2.3' => array( 40, 59 ),
					'2.2' => array( 60, 79 ),
					'2.1' => array( 80, 99 ),		
					'2' => array( 100, 199 ),					
					'1.8' => array( 200, 299 ),
					'1.7' => array( 300, 499 ),
					'1.5' => array( 500, 699 ),
					'1.4' => array( 700, 999 ),
					'1.38' => array( 1000, 1000000)

				),

				'cover' => array(
				   '5' => array( 1, 9 ),
				   '4' => array( 10, 24 ),   
				   '3' => array( 25, 49 ),   
				   '2' => array( 50, 99 ),
				   '1.7' => array( 100, 199 ),
				   '1.3' => array( 200, 299 ),
				   '1.2' => array( 300, 10000000
				   )
				)

			
			)
		
		),
		
		
		
		
		'katalog' => array(
			'default' => array(
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					//'5' => array( 1, 9 ),
				   '2.7' => array( 10, 19 ),   
				   '2.3' => array( 20, 29 ),   
				   '1.95' => array( 30, 39 ),
				   '1.7' => array( 40, 49 ),
				   '1.5' => array( 50, 59 ),
				   '1.45' => array( 60, 69 ),
				   '1.4' => array( 70, 79 ),
				   '1.37' => array( 80, 89 ),
				   '1.35' => array( 90, 100 ),
					'1.38' => array( 100, 199 ),
					'1.36' => array( 200, 299 ),
					'1.34' => array( 300, 499999 )
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '4' => array( 1, 9 ),
				   '2.3' => array( 10, 19 ),   
				   '2.08' => array( 20, 29 ),   
				   '1.85' => array( 30, 39 ),
				   '1.79' => array( 40, 49 ),
				   '1.7' => array( 50, 59 ),
				   '1.57' => array( 60, 69 ),
				   '1.51' => array( 70, 79 ),
				   '1.45' => array( 80, 89 ),
				   '1.37' => array( 90, 99 ),
				   '1.333' => array( 100, 199 ),
				   '1.276' => array( 200, 299 ),
				   '1.25' => array( 300, 1000000 )
				),

				'cover' => array(
				   '5' => array( 1, 9 ),
				   '4' => array( 10, 19 ),   
				   '3' => array( 20, 29 ),   
				   '2.5' => array( 30, 39 ),
				   '2.3' => array( 40, 49 ),
				   '2.1' => array( 50, 59 ),
				   '1.97' => array( 60, 69 ),
				   '1.86' => array( 70, 79 ),
				   '1.79' => array( 80, 89 ),
				   '1.74' => array( 90, 99 ),
				   '1.7' => array( 100, 199 ),
				   '1.6' => array( 200, 299 ),
				   '1.5' => array( 300, 1000000 )
				   )

			), 
		),
		
		
		
		
		
		
		'ulotki' => array(
			'default' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '6' 	=> array( 100, 249 ),
				   '4.5' 		=> array( 250, 499 ),   
				   '3.5' 	=> array( 500, 749 ),   
				   '3' 	=> array( 750, 999 ),
				   '2.6' 		=> array( 1000, 1499 ),
				   '1.7' 	=> array( 1500, 1999 ),
				   '1.49' 	=> array( 2000, 2499 ),
				   '1.29' 		=> array( 2500, 49999 ), 
				   
				)
			
			
			),
		
			'plakaty' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '3' 	=> array( 100, 249 ),
				   '2.5' 		=> array( 250, 499 ),   
				   '2' 	=> array( 500, 749 ),   
				   '1.9' 	=> array( 750, 999 ),
				   '1.8' 		=> array( 1000, 1499 ),
				   '1.7' 	=> array( 1500, 1999 ),
				   '1.6' 	=> array( 2000, 2499 ),
				   '1.5' 		=> array( 2500, 49999 ), 
				   
				)
			
			
			),
		
			'ulotki-skladane' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '12' 	=> array( 100, 249 ),
				   '8.5' 		=> array( 250, 499 ),   
				   '5.5' 	=> array( 500, 749 ),   
				   '5' 	=> array( 750, 999 ),
				   '4.6' 		=> array( 1000, 1499 ),
				   '3.7' 	=> array( 1500, 1999 ),
				   '3.49' 	=> array( 2000, 2499 ),
				   '3.22' 		=> array( 2500, 49999 ), 
				   
				)
			
			
			),
		
			'pocztowki' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '11' 	=> array( 100, 249 ),
				   '7.5' 		=> array( 250, 499 ),   
				   '5.2' 	=> array( 500, 749 ),   
				   '4.5' 	=> array( 750, 999 ),
				   '4' 		=> array( 1000, 1499 ),
				   '3.4' 	=> array( 1500, 1999 ),
				   '3' 	=> array( 2000, 2499 ),
				   '2.5' 		=> array( 2500, 4999 ), 
				   '2' 		=> array( 5000, 99999 ), 
				   
				)
			
			
			),
		
			'bilety' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '11' 	=> array( 100, 249 ),
				   '7.5' 		=> array( 250, 499 ),   
				   '5.2' 	=> array( 500, 749 ),   
				   '4.5' 	=> array( 750, 999 ),
				   '4' 		=> array( 1000, 1499 ),
				   '3.4' 	=> array( 1500, 1999 ),
				   '3' 	=> array( 2000, 2499 ),
				   '2.5' 		=> array( 2500, 4999 ), 
				   '2' 		=> array( 5000, 99999 ), 
				   
				)
			
			
			),
		
			'dyplomy' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '8.5' 	=> array( 100, 249 ),
				   '7.5' 		=> array( 250, 499 ),   
				   '5.2' 	=> array( 500, 749 ),   
				   '4.5' 	=> array( 750, 999 ),
				   '4' 		=> array( 1000, 1499 ),
				   '3.4' 	=> array( 1500, 1999 ),
				   '3' 	=> array( 2000, 2499 ),
				   '2.5' 		=> array( 2500, 4999 ), 
				   '2' 		=> array( 5000, 99999 ), 
				   
				)
			
			
			),
		
			
		
		
		),
		
		
		
		'wizytowki' => array(
			'default' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
				   '4.62' 	=> array( 100, 249 ),
				   '3.31' 		=> array( 250, 499 ),   
				   '2' 	=> array( 500, 749 ),   
				   '1.9' 	=> array( 750, 999 ),
				   '1.8' 		=> array( 1000, 1499 ),
				   '2.5' 	=> array( 1500, 1999 ),
				   '2.3' 	=> array( 2000, 2499 ),
				   '1.5' 		=> array( 2500, 49999 ), 
				   
				)/*,

				'color' => array(
				   '12' 	=> array( 100, 249 ),
				   '8.5' 		=> array( 250, 499 ),   
				   '5.5' 	=> array( 500, 749 ),   
				   '5' 	=> array( 750, 999 ),
				   '4.6' 		=> array( 1000, 1499 ),
				   '3.7' 	=> array( 1500, 1999 ),
				   '3.49' 	=> array( 2000, 2499 ),
				   '3.22' 		=> array( 2500, 49999 ), 
				   
				)*/
			
			
			),
			
			'wizytowki-zlocone' => array( 
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(								
				   '2.3' => array( 20, 29 ),				
				   '1.5' => array( 50, 59 ),				  
				   '1.4' => array( 70, 79 ),				  
					'1.38' => array( 100, 199 ),
					'1.36' => array( 200, 299 ),	   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(				 
				   '21.1' => array( 20, 29 ),   
				   '20' => array( 30, 39 ),
				   '19' => array( 40, 49 ),
				   '18' => array( 50, 59 ),
				   '17' => array( 60, 70 ),
				   '15.5' => array( 100, 199 ),
				   '12.5' => array( 200, 999 )
				)
			
			
			),
			
			'wizytowki-skladane' => array( 
				
				
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(				
					'5' => array( 100, 9 ),
				   '2.7' => array( 250, 19 ),   
				   '2.3' => array( 500, 29 ),   
				   '1.95' => array( 750, 39 ),
				   '1.7' => array( 1000, 49 ),
				   '1.5' => array( 1500, 59 ),
				   '1.45' => array( 2000, 69 ),
				   '1.4' => array( 2500, 79 ),
				   '1.37' => array( 3000, 4999 ),			   
				   '1.37' => array( 5000, 10000 ),			   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(
    
          '3' 	=> array( 100, 249 ),
				   '2' 		=> array( 250, 499 ),   
				   '1.7' 	=> array( 500, 749 ),   
				   '10.7' 	=> array( 750, 999 ),
				   '1.55' 		=> array( 1000, 1499 ),
				   '2.5' 	=> array( 1500, 1999 ),
				   '2.3' 	=> array( 2000, 2499 ),
				   '1.5' 		=> array( 2500, 49999 ),
    
    
    /*
				   '10' => array( 100, 249 ),
				   '8' => array( 250, 499 ),   
				   '4.4' => array( 500, 749 ),   
				   '3.7' => array( 750, 999 ),
				   '3.2' => array( 1000, 1499 ),
				   '2.5' => array( 1500, 1999 ),
				   '2.4' => array( 2000, 2499 ),
				   '2' 	=> array( 2500, 4999 ), 
				   '1.6' => array( 5000, 30000 )
    */
				)
			
    
    
			
			),
			
			'wizytowki-ozdobne' => array( 
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(								
				   '2.3' => array( 20, 29 ),				
				   '1.5' => array( 50, 59 ),				  
				   '1.4' => array( 70, 79 ),				  
					'1.38' => array( 100, 199 ),
					'1.36' => array( 200, 299 ),	   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(				 
				   '21.1' => array( 20, 29 ),   
				   '20' => array( 30, 39 ),
				   '19' => array( 40, 49 ),
				   '18' => array( 50, 59 ),
				   '17' => array( 60, 70 ),
				   '15.5' => array( 100, 199 ),
				   '12.5' => array( 200, 999 )
				)
			
			
			),
			
			
			/*
			* Umiescilem tutaj, ale równie dobrze może być to ulotka
			*/
			'zaproszenia' => array( 
				/*Quantity list sluzy do generowania listy dodadkowych nakladów
					używana jest tylko minimalna ilość z kazdej linii, reszta w praktyce nie jest potrzebna
				*/
				'qlist' => array(								
				   '2.3' => array( 20, 29 ),				
				   '1.5' => array( 50, 59 ),				  
				   '1.4' => array( 70, 79 ),				  
					'1.38' => array( 100, 199 ),
					'1.36' => array( 200, 299 ),	   
				),

				'bw' => array(
					'0' => array( 0, 10000000 )
				),
				'cover' => array(
					'0' => array( 0, 10000000 )
				),

				'color' => array(				 
				   '11.1' => array( 20, 29 ),   
				   '10' => array( 30, 39 ),
				   '9' => array( 40, 49 ),
				   '8' => array( 50, 59 ),
				   '7' => array( 60, 70 ),
				   '5.5' => array( 100, 199 ),
				   '3.5' => array( 200, 999 )
				)
			
			
			),
		
		),
			
	);

	
	/*
	* Konstruktor
	*/
	public function __construct( $part, $quantity, $product_id = NULL ){		
		$product = gaad_ajax::get_product( $product_id );
		$this->part = !isset( $part ) ? 'qlist' : $part;
		$this->quantity = (int)( !isset( $quantity ) ? 1 : $quantity );
		$this->calc_class = gaad_ajax::gaad_get_calc_class( $product->id );
		$this->markup_tag = gaad_ajax::gaad_get_markup_tag( $product->id );
		if( $this->markup_tag == false ){ $this->markup_tag = 'default'; }
	}


	/**
	* Tworzy listę nakladów bazując na $this->_markup_db['qlist']
	*
	* @return void
	*/
	function get_qlist (  ) {
		$matrix = isset( $this->_markup_db[ $this->calc_class ][ $this->markup_tag ][ $this->part ] ) ?
					$this->_markup_db[ $this->calc_class ][ $this->markup_tag ][ $this->part ] : false;
		
		if( $matrix == false ){
		
			echo '<pre>'; echo var_dump( "ERROR", $this->calc_class, $this->markup_tag, $this->part ); echo '</pre>';
		
			return;
		}
					
		$tmp = array();
		
		$quantity_match = array();		
		preg_match("/(\d*)-szt/", $_POST['post_data']["attribute_pa_naklad"], $quantity_match);
		$pa_naklad = (int)$quantity_match[ 1 ];
		
	
		$mark = false;
		foreach( $matrix as $markup => $rage ){
			
			if( $pa_naklad >= $rage[0] && $pa_naklad <= $rage[1]  ){
				$tmp[] = $pa_naklad . '-szt';
				$mark = true;
			}
			
			if( /*$pa_naklad <= $rage[0] &&*/ !( $mark && $pa_naklad == $rage[0] ) ){
				$tmp[] = $rage[0] . '-szt';
			}			
			
		}
		return $tmp;
	}
	
	/**
	* Wyszukuje wartość marzy dla podanego produktu, nakladu i cześci (np bw, color, cover)
	*
	* @return void
	*/
	function get_markup (  ) {
		
	
		$matrix = isset( $this->_markup_db[ $this->calc_class ][ $this->markup_tag ][ $this->part ] )
					? $this->_markup_db[ $this->calc_class ][ $this->markup_tag ][ $this->part ] : false;
					
					
		$q = $this->quantity;
		if( $matrix ){
			
			foreach( $matrix as $markup => $rage){
				if(  $q >= $rage[0] && $q <= $rage[1]  ){
					return (float)$markup;
				}
				
			}
		}
	
	}
	
	
}
















?>