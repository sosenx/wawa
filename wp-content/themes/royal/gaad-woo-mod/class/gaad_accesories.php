<?php


/*
* Klasa przchowuje zasady oraz bazę akcesoriów dołączanych do produktów drukowanych
*/


class gaad_accesories{
	
	/*
	* tablica przechowuje bazę akcesoriów podzielonych wg typu kalkulatora
	*/
	public $acc_db = array(
		'rollup' => array(
		
			'default' => array( 
				'85' => array( 
					'economic' => 30.5, 
					'standard' => 36.5, 
					'exclusive' => 111, 
					'rocker' => 66.5, 

				),

				'100' => array( 
					'economic' => 37, 
					'standard' => 43.5, 
					'exclusive' => 127.5, 
					'rocker' => 66.5, 

				),

				'120' => array( 
					'economic' => 10000000, // brak takiej wersji w tej długości
					'standard' => 66, 
					'exclusive' => 173.5, 
					'rocker' => 10000000, 

				),				
			),
		
			'xbaner' => array(
				
		
				'80' => array( 							
					'standard' => 14.5, 
				),
		
		
			), 
			
			'potykacz' => array(
				//a1
				'59' => array(
					'owz' => 267, 
					'spring' => 267, 
					'waterbase' => 278
				), 
				//a2
				'42' => array(
					'owz' => 139, 
					
				
				), 
				//b1
				'70' => array(
					'owz' => 173, 					 
				), 
				//b2
				'50' => array(
					'owz' => 108, 
				), 				
				
			), 
		

		
			
		
		)
	
	);
	
	
	
}


?>