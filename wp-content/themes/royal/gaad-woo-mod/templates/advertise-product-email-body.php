<?php
	$id = $_POST[ 'product_id' ];
	$product_attr = $_POST[ 'product_attr' ];
	$product = new WC_Product( $id );	
	$_price = $_POST[ 'price' ];
	$_quantity = $_POST[ 'quantity' ];

	$product_title = $product->post->post_title;
	$permalink = get_permalink( $id );

?>



 
	 <table>

	  <tr class="header">
		<td class="logo">
		  <a href="http:\\wawaprint.pl"><img src="http://wawaprint.pl/wp-content/uploads/2017/03/WAWAprint-hdr.png" alt="Drukarnia Internetowa"></a>	  
		</td>    

		<td class="company-data">	
			<p><strong class="company-name">MAZOWIECKIE CENTRUM POLIGRAFII</strong><br>
			ul. Słoneczna 3c, 05-270 Marki<br></p>
			<div class="hr"></div>
			<p><strong>Zapraszamy:</strong>&nbsp; Poniedziałek - Piątek: 9:00 - 17:00<br>
			tel. +48 22 889 00 60, fax +48 22 889 00 60  <br></p>
		</td>	
	  </tr>


	  <tr>
		<td colspan="2" class="text">  		
			<h1><?php echo '<br>' . $_POST[ 'name' ]; ?> poleca Ci produkty z wawaprint.pl!</h1>
			<p>Zobacz co przygotowaliśmy dla swoich klientów.</p>
		</td>
	  </tr>



	  <tr>
		<td colspan="2" class="product-title">  		
			<h2><?php echo $product_title; ?></h2>
		</td>
	  </tr>






	  <tr>
		<td>
			<img class="product_thumbnail" src="<?php echo get_the_post_thumbnail_url( $id ); ?>">


		</td>
		<td class="product-attributes">
		
			<ul>
				<?php foreach( $product_attr as $value ){ ?>
					<li>
						<div class="attr-label"><?php echo $value[ 'label' ] ?></div>
						<div class="attr-value"><?php echo $value[ 'value' ] ?></div>

					</li>
				<?php } ?>
			</ul>
					
					
			<div class="price-wrap">							
				<i class="fa fa-certificate" aria-hidden="true"></i>


				<span class="price_prefix">Tylko</span>
				<span class="price_price"><?php echo $_price; ?> zł</span>
				<span class="price_sufix">
					<span class="separator">za&nbsp;</span><?php echo $_quantity; ?><span class="separator">&nbsp;szt.</span>
				</span>
			</div>			
						
		</td> 

	  </tr>  



	  <tr>
		
		<td colspan="2">
		
			<a class="go-to-product-btn" href="<?php echo $permalink; ?>">Przejdź do wawaprint.pl</a>

		</td> 

	  </tr>




	</table>
