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
			<h3> This is it! </h3>
		</td>
	</tr>	
		
	<?php 		
		add_action( 'gaad_attribute_ui_pa_format_after_value', 'gaad_attribute_ui_pa_format_after_value_callback_outdoor' );
		/*
		* Generowanie pola formularza dla format
		*/
		gaad_attribute_ui ( 'pa_format', $attributes[ 'pa_format' ] );
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