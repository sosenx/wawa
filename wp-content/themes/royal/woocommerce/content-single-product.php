<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product;

?>





<!--
	Aplikacja sluzy do pokazywania paneli widoku produktu
	Panele: 
	- kupowanie, wybór paramwetrów,
	- opis produktu,
	- szablony graficzne
	- przygotowanie do druku

-->
<div id="product-nav-app"></div> 
<div id="advertise-product-app"></div>
   
    

<div id="product-<?php the_ID(); ?>" <?php post_class($class); ?>>

	<div class="container-fluid tabs-content">
		<div class="row calculations-row">


			<!--Wybór parametrów wariantu-->
			<div class="col-md-4 variations_form-col">   				
				<div id="variations_form"></div>
			</div>

			<!--Lista wycenionych nakladów-->
			<div class="col-md-4 nakladyapp-col">			
				<div id="nakladyapp"></div>
			</div>

			<!--Podsumowanie wybranego wariantu-->
			<div class="col-md-4 productSummaryApp-col">   				
				<div id="productSummaryApp"></div>   				
			</div>   			

		</div>
		
		<div class="row templates-row">
			<div class="col-sm-12">
				<div  id="product-templates-app"></div>
			</div>			
		</div>   
			
		<div class="row description-row">
			<div class="col-sm-12">
				<div id="product-description-app"></div>
			</div>						
		</div>   
		 	   	   	
	 	<div class="row product-send-row">
			<div class="col-sm-12">
				<div id="product-send-app"></div>
			</div>						
		</div>   	
		
		<div class="row crosssell-row">
			<div class="col-sm-12">
				<div id="product-cross-sell-app"></div>
			</div>						
		</div>      	   	   	
	</div>

</div>
  
<!--<div id="product-price-chart"></div>-->

<?php 

$test32 = new product_variations_csv();



?>
   
   
<script id="calc_data" defer type="text/javascript">
  var gaad_post = <?php global $post; echo json_encode( $post ); ?>;
	var calc_class =  <?php echo json_encode( gaad_ajax::gaad_get_calc_class() );  ?>;	
	var markup_tag =  <?php echo json_encode( gaad_ajax::gaad_get_markup_tag() );  ?>;	
	var gaad_db =  <?php echo json_encode( new gaad_db() );  ?>;	
	
	var input_settings = <?php echo json_encode( $input_settings = gaad_ajax::gaad_get_product_input_settings( $product->id ) ); ?>;
	var product_basic_variation =  <?php echo json_encode( gaad_ajax::gaad_get_basic_variation( $input_settings ) );  ?>;
	var product_attr_data = <?php echo json_encode( gaad_ajax::gaad_get_product_attr_data( $product->id ) ); ?>;
	var calc_data  = <?php echo json_encode( gaad_ajax::gaad_get_calc_data( $product->id, $input_settings ) ); ?>;	  	
	var calc_data_bak  = <?php echo json_encode( gaad_ajax::gaad_get_calc_data( $product->id, $input_settings ) ); ?>;	  	
	var product_id = <?php echo $product->id; ?>;
	var cross_sell  = <?php echo json_encode( gaad_ajax::gaad_get_cross_sell( $product->id, $input_settings ) ); ?>;	  	
	var product_templates  = <?php echo json_encode( gaad_ajax::gaad_get_product_templates( $product->id ) ); ?>;	  	
	
	
	
		
</script>