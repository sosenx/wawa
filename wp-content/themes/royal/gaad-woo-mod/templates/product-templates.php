
 <?php 
	global $product;
	$source_ID = $product->id;
	$wawa_product_template_json = 
	  str_replace( array( '[[[', ']]]' ), array( '{{', '}}'), get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_json', true) );
	$wawa_product_template_json2 = 
	  str_replace( array( '[[[', ']]]' ), array( '{{', '}}'), get_post_meta( $source_ID, 'wawa_product_'.$source_ID.'_template_json2', true) );

?>

<div class="product-templates to-print">



	<div  v-if="visible">
		
		
		<div class="container-fluid">
		
		<div class="row">
				
			<div class="col-sm-12 col-md-6 top-col-left header-title">	
				<img src="http://wawaprint.pl/wp-content/uploads/2017/03/WAWAprint-hdr.png" class="print-only">									
			</div>
			
			<div class="col-md-6 top-col-right">										
				<a class="print-tempaltes-layout" @click="prepareToPrint()">
					<i class="fa fa-print" aria-hidden="true"></i>
					<span>Drukuj instrukcję</span>						
				</a>				
			</div>
			
			
		</div>
				
		
			<div class="row">
				
				<div class="col-md-6 left-col">										
					
          			<?php echo $wawa_product_template_json; ?>
					<section class="print-only">
						<h3>Zapoznaj się z FAQ</h3>
						<p>Dodatkowe informacje dotyczące przygotowania plików znajdziesz w dziale FAQ. Znajduje się tam sporo informacji i porad dotyczących czcionek, kolorystyki, rozdzielczości, a także technicznych zasad przygotowania plików w różnych programach graficznych.</p>					
					</section>
				</div>
					
				<div class="col-md-6 right-col">						
					
					<?php   echo $wawa_product_template_json2;    ?>
					
					<section class="do-not-print">
						<h3>Zapoznaj się z FAQ</h3>
						<p>Dodatkowe informacje dotyczące przygotowania plików znajdziesz w dziale FAQ. Znajduje się tam sporo informacji i porad dotyczących czcionek, kolorystyki, rozdzielczości, a także technicznych zasad przygotowania plików w różnych programach graficznych.</p>					
					</section>
					
				</div>
				
			</div>			
			
		</div>
		
		
		
		
		
		
		
		
		
	</div>	
	
</div> 	