<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attributes = $product->get_variation_attributes();
?>
<script type="text/javascript">	
	var product_attributes = <?php echo json_encode($attributes); ?>; 
</script>
<?php


/*
* Dolączanie modułu obsługującego validację i stany formularza
*/
wp_enqueue_script( 'baner_variations_ui', get_template_directory_uri() . '/gaad-woo-mod/js/baner_variations_ui.js', array( 'jquery' ) );
wp_enqueue_style( 'baner_variations_ui', get_template_directory_uri() . '/gaad-woo-mod/css/baner_variations_ui.css' );




do_action( 'woocommerce_before_add_to_cart_form' ); ?>



<form  id="variations_form" class="variations_form cart" method="post" enctype='multipart/form-data'>
	<?php do_action( 'woocommerce_before_variations_form' ); ?>	

	

	<table class="variations" cellspacing="0">
		<tbody>

		<?php 		

			/*
			* Generowanie pola formularza dla pa_podloze, pole ukryte
			*/
			gaad_attribute_ui ( 'pa_podloze', $attributes[ 'pa_podloze' ], array( 'selected' => 'baner', 'type' => 'hidden' )  );
		?>	


		<?php 		
			//add_action( 'gaad_attribute_ui_pa_format_after_value', 'gaad_attribute_ui_pa_format_after_value_callback_outdoor' );
			/*
			* Generowanie pola formularza dla format
			*/
			gaad_attribute_ui ( 'pa_format', $attributes[ 'pa_format' ], array( 'selected' => '200x100cm' )  );
		?>			


		<?php 		
			//add_action( 'gaad_attribute_ui_pa_naklad_after_value', 'gaad_attribute_ui_pa_naklad_after_value_callback_outdoor' );
			/*
			* Generowanie pola formularza dla format
			*/
			gaad_attribute_ui ( 'pa_naklad', $attributes[ 'pa_naklad' ], array( 'selected' => '1-szt', 'type' => 'hidden' ) );
		?>		


		<?php 			
			/* 
			* Generowanie pola formularza dla format
			*/
			gaad_attribute_ui ( 'pa_wykonczenie-banera', $attributes[ 'pa_wykonczenie-banera' ], array( 'selected' => 'oczka-50cm' )  );
		?>	

		<?php 			
			/*
			* Generowanie pola formularza dla format
			*/
			gaad_attribute_ui ( 'pa_termin-wykonania', $attributes[ 'pa_termin-wykonania' ], array( 'selected' => 'termin-1', 'type' => 'hidden' )  );
		?>			



		</tbody>
</table>



<?php //do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<div class="single_variation_wrap">
			<?php
				/**
				 * woocommerce_before_single_variation Hook.
				 */
				//do_action( 'woocommerce_before_single_variation' );

				/**
				 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				//do_action( 'woocommerce_single_variation' );

				/**
				 * woocommerce_after_single_variation Hook.
				 */
				//do_action( 'woocommerce_after_single_variation' );
			?>
		</div>

		<?php //do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php //do_action( 'woocommerce_after_variations_form' ); ?>
</form>