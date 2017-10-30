<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attributes = $product->get_variation_attributes();


/*
* Dolączanie modułu obsługującego validację i stany formularza
*/
wp_enqueue_script( 'ksiazka_variations_ui', get_template_directory_uri() . '/gaad-woo-mod/js/ksiazka_variations_ui.js', array( 'jquery' ) );
wp_enqueue_style( 'ksiazka_variations_ui', get_template_directory_uri() . '/gaad-woo-mod/css/ksiazka_variations_ui.css' );




do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="" method="post" enctype='multipart/form-data'>
	<?php do_action( 'woocommerce_before_variations_form' ); ?>



<table class="variations" cellspacing="0">
	<tbody>
	
	<tr>
		<!--Informacje ogólne -->		
		<td class="header" colspan="2">
			<h3>Informacje o książce</h3>
		</td>
	</tr>	
		
		<?php 		
			add_action( 'gaad_attribute_ui_pa_format_after_value', 'gaad_attribute_ui_pa_format_after_value_callback' );
			/*
			* Generowanie pola formularza dla format
			*/
			gaad_attribute_ui ( 'pa_format', $attributes[ 'pa_format' ] );
		?>
		
		
	
		<?php 
			add_action( 'gaad_attribute_ui_pa_naklad_after_value', 'gaad_attribute_ui_pa_naklad_after_value_callback' );
			/*
			* Generowanie pola formularza dla format
			*/
			gaad_attribute_ui ( 'pa_naklad', $attributes[ 'pa_naklad' ] );
		?>
		
		
		<?php 			
			/*
			* Generowanie pola formularza dla orientacja książki
			*/
			gaad_attribute_ui ( 'pa_orientacja', $attributes[ 'pa_orientacja' ] );
		?>		
		
		
		<?php 			
			/*
			* Generowanie pola formularza dla ilość stron czb (udawany atrybut)
			*/
			gaad_input_ui ( 'Ilość stron czarno-białych', array( 'title' => 'Ilość stron czarno-białych', 'name' => 'attribute_pa_ilosc-stron-czarno-bialych', ) );
		?>

		<?php 			
			/*
			* Generowanie pola formularza dla ilość stron czb
			*/
			gaad_input_ui ( 'Ilość stron kolorowych', array( 'title' => 'Ilość stron kolorowych', 'name' => 'attribute_pa_ilosc-stron-kolorowych', ) );
		?>
				
		<?php 			
			/*
			* Generowanie pola formularza dla orientacja książki
			*/
			gaad_attribute_ui ( 'pa_porozrzucane-str-kolor', $attributes[ 'pa_porozrzucane-str-kolor' ] );
		?>
		
		<?php 			
			/* 
			* Generowanie pola formularza dla ilość stron czb
			*/
			gaad_input_ui ( 'Numery stron kolorowych', array( 'tag' => 'textarea', 'title' => 'Numery stron kolorowych', 'name' => 'pa_numery-stron-kolorowych' ) );
		?>
		
		
		
		
		
		
		
		
		
		<tr>
			<!--Okladka -->
			<td class="header" colspan="2">
				<h3>Okładka</h3>
			</td>
		</tr>		
		
		<?php 			
			/*
			* Generowanie pola formularza dla obwoluta (bookjacket)
			*/
			//gaad_input_ui ( 'Obwoluta', array( 'type' => 'checkbox' ) );
		?>	
			
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_obwoluta', $attributes[ 'pa_obwoluta' ] );
		?>
		
		<?php 			
			/*
			* Generowanie pola formularza dla okladka ze skrzydelkami
			*/
			gaad_attribute_ui ( 'pa_rodzaj-okladki', $attributes[ 'pa_rodzaj-okladki' ] );
		?>	
		
		<?php 			
			/*
			* Generowanie pola formularza dla okladka
			*/
			gaad_attribute_ui ( 'pa_lakier-wybiorczy-okladki', $attributes[ 'pa_lakier-wybiorczy-okladki' ] );
		?>		
			
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_oprawa', $attributes[ 'pa_oprawa' ] );
		?>
		
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_papier-okladki', $attributes[ 'pa_papier-okladki' ] );
		?>
		
		<?php 			
			/*
			* Generowanie pola formularza dla zadruk okladki
			*/
			gaad_attribute_ui ( 'pa_zadruk-okladki', $attributes[ 'pa_zadruk-okladki' ] );
		?>
		
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_uszlachetnienie-okladki', $attributes[ 'pa_uszlachetnienie-okladki' ] );
		?>	
		
		
		
		
		
		<tr>
			<!--Blok kolorowy -->
			<td class="header" colspan="2">
				<h3>Strony kolorowe</h3>
			</td>
		</tr>
		
		<?php 			
			/*
			* Generowanie pola formularza dla zadruk okladki
			*/
			gaad_attribute_ui ( 'pa_zadruk-strony-kolorowe', $attributes[ 'pa_zadruk-strony-kolorowe' ] );
		?>	
		
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_papier-kolor', $attributes[ 'pa_papier-kolor' ] );
		?>
		
		
		
		
		
		
		<tr>
			<!--Blok czarno-biały -->
			<td class="header" colspan="2">
				<h3>Strony czarno-białe</h3>
			</td>
		</tr>
		
		<?php 			
			/*
			* Generowanie pola formularza dla zadruk okladki
			*/
			gaad_attribute_ui ( 'pa_zadruk-strony-czarno-biale', $attributes[ 'pa_zadruk-strony-czarno-biale' ] );
		?>	
		
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_papier-czarno-bialy', $attributes[ 'pa_papier-czarno-bialy' ] );
		?>
		
		
		
		
		<tr>
			<!--Informacje o publikacji -->
			<td class="header" colspan="2">
				<h3>Informacje o publikacji</h3>

				<?php 			
					/*
					* Generowanie pola formularza dla ilość stron czb
					*/
					gaad_input_ui ( 'Tytuł książki', array( 'title' => 'Tytuł książki', 'name' => 'pa_tytul-ksiazki'  ) );
				?>
				<?php 			
					/*
					* Generowanie pola formularza dla ilość stron czb
					*/
					gaad_input_ui ( 'Numer ISBN/ISSN', array( 'title' => 'Numer ISBN/ISSN', 'name' => 'pa_numer-isbnissn'  ) );
				?>


			</td>		
		</tr>
		
		
		<tr>
			<!--Parametry dodadkowe -->
			<td class="header" colspan="2">
				<h3>Parametry dodadkowe</h3>
			</td>
		
		</tr>
		
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_pakowanie-w-folie', $attributes[ 'pa_pakowanie-w-folie' ] );
		?>
		
		<?php 			
			/*
			* Generowanie pola formularza dla oprawa
			*/
			gaad_attribute_ui ( 'pa_wiercenie-otworow', $attributes[ 'pa_wiercenie-otworow' ] );
		?>
		
		
	
	
	

	
	
	<?php //foreach ( $attributes as $attribute_name => $options ) { gaad_attribute_ui ( $attribute_name, $options ); } ?>
	
	</tbody>
</table>



<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<div class="single_variation_wrap">
			<?php
				/**
				 * woocommerce_before_single_variation Hook.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button' );
				do_action( 'woocommerce_single_variation' );

				/**
				 * woocommerce_after_single_variation Hook.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>