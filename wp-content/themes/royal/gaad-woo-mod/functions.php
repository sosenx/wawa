<?php 

	
define( 'WC_MAX_LINKED_VARIATIONS', 499);


/*
* Wielki format
*/


/**
* Akcja uzupelniajaca strukture ui atrybutu format
*
* @return void
*/
function gaad_attribute_ui_pa_format_after_value_callback_outdoor ( $variation_data ) {


	?>

	<tr class="gaad_ui custom_pa_format gaad_display_none">

		<td class="label">

			<label for="attribute_<?php echo $variation_data["attribute"]; ?>">Podaj własny format</label>

		</td>

		<td class="value">

			<input type="text" name="attribute_<?php echo $variation_data["attribute"]; ?>">				

		</td>

	</tr>

	<?php


}


	/**
	* Akcja uzupelniajaca strukture ui atrybutu naklad
	*
	* @return void
	*/
	function gaad_attribute_ui_pa_naklad_after_value_callback_outdoor ( $variation_data ) {
		
		
		?>
		
		<tr class="gaad_ui custom_pa_naklad gaad_display_none">
			
			<td class="label">
				
				<label for="attribute_<?php echo $variation_data["attribute"]; ?>">Podaj nakład</label>
				
			</td>
			
			<td class="value">
				
				<input type="text" name="attribute_<?php echo $variation_data["attribute"]; ?>">				
				
			</td>
			
		</tr>
		
		<?php

			
	}


























	/**
	* Akcja uzupelniajaca strukture ui atrybutu naklad
	*
	* @return void
	*/
	function gaad_attribute_ui_pa_naklad_after_value_callback ( $variation_data ) {
		
		
		?>
		
		<tr class="gaad_ui custom_pa_naklad gaad_display_none">
			
			<td class="label">
				
				<label for="attribute_<?php echo $variation_data["attribute"]; ?>">Podaj nakład</label>
				
			</td>
			
			<td class="value">
				
				<input type="text" name="attribute_<?php echo $variation_data["attribute"]; ?>">				
				
			</td>
			
		</tr>
		
		<?php

			
	}

	/**
	* Akcja uzupelniajaca strukture ui atrybutu format
	*
	* @return void
	*/
	function gaad_attribute_ui_pa_format_after_value_callback ( $variation_data ) {
		
		
		?>
		
		<tr class="gaad_ui custom_pa_format gaad_display_none">
			
			<td class="label">
				
				<label for="attribute_<?php echo $variation_data["attribute"]; ?>">Podaj własny format</label>
				
			</td>
			
			<td class="value">
				
				<input type="text" name="attribute_<?php echo $variation_data["attribute"]; ?>">				
				
			</td>
			
		</tr>
		
		<?php

			
	}


/**
* 
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
* Ta funkcja jest deprecated, używana była w pierwszej wersji formularza do wyboru elemntów wariacji
* Zmieniona na szablojn aplikacji frontowej Vue el: #variations_form
*
* @return void
*/

	if( ! function_exists( 'gaad_input_ui' )  ){
		/**
		* 
		*
		* @return void
		*/
		function gaad_input_ui ( $name, $options = array() ) {
			global $product;
			
			
			$tag =  isset( $options['tag'] ) ? $options['tag'] : 'input';
			$select_options =  isset( $options['select-options'] ) ? $options['select-options'] : false;
			
			$type =  isset( $options['type'] ) ? $options['type'] : 'text';
			$type = " type=\"".$type."\"";
			$name = isset( $options['name'] ) ? $options['name'] : sanitize_title( $name );
			$label = isset( $options['title'] ) ? $options['title'] : sanitize_title( $name );
			
			
			if( $tag == 'textarea' || $tag == 'select' ){
				$type = "";
			}
			
			?>
			<tr>
					<td class="label"><label for="<?php echo $name; ?>"><?php echo $label; ?></label></td>
					<td class="value">
						
						<?php 
							if( $select_options ){
								?><select>
<?php

								foreach( $select_options as $opt => $d ){
									
									?><option value="<?php echo $d['value']  ?>"><?php echo $d['label']  ?></option><?php

								}
								
								?>
								
								</select><?php
							}

						?>	
									
							
							
						<<?php echo $tag; ?> <?php echo $type; ?> name="<?php echo $name; ?>">					
							
						<?php if( $tag != 'input' ){
							echo "</". $tag .">";
						} ?>
					
					</td>
			</tr>		
			<?php

		}
	}



	if( ! function_exists( 'gaad_attribute_ui' )  ){

		/**
		
		* Ta funkcja jest deprecated, używana była w pierwszej wersji formularza do wyboru elemntów wariacji
		* Zmieniona na gaad_ajax :: gaad_get_calc_data
		
		* Generuje kod HTML elementu formularza do wyboru parametrów ksiązki, katalogu, zeszytu
		*
		* @return void
		*/
		function gaad_attribute_ui ( $attribute_name, $options, $settings = NULL ) { 
			global $product;
			
			$type = isset( $settings[ 'type' ] ) ? $settings[ 'type' ] : 'select';
			if( isset( $settings[ 'selected' ] ) ){
				$GLOBALS[ 'gaad_current_variation' ][ sanitize_title( $attribute_name ) ] = $settings[ 'selected' ];
				
				
				
			}
			?><?php if( $type === 'select' ){ ?>
				<tr>
				
					<td class="label"><label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></td>
					
					
					<td class="value">
					<?php } ?>
						<?php
							$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? 
								wc_clean( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) : 
								$product->get_variation_default_attribute( $attribute_name );
							$product->visible = null;
							$variation_data = array( 
									'options' => $options,
									'attribute' => $attribute_name,
									'product' => $product,
									'selected' => $selected
								);
			//echo '<pre>'; echo var_dump($variation_data); echo '</pre>';
							//wc_dropdown_variation_attribute_options( $variation_data );
							
			
							$attributes 			= $product->get_attributes();
							$attribute 				= $attributes[ $attribute_name ];
							$values 				= wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'all' ) );
							$id                    	= $args['id'] ? $args['id'] : sanitize_title( $attribute_name );
						
						if( $type === 'select' ){
							echo '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="attribute_' . esc_attr( $attribute_name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute_name ) ) . '"' . '">';

								foreach ( $values as $k => $v) {
									$selected = ($settings['selected'] === esc_attr( $v->slug )) ? 'selected="selected"' : '';
									 echo '<option '. $selected .' class="attached enabled" value="' . esc_attr( $v->slug ) . '" >' . esc_html( apply_filters( 'woocommerce_variation_option_name', $v->name ) ) . '</option>';
								}

							echo '</select>';						
						}
			
						/*
						* Select acting as hidden field, uset when attribute ui is handled elseware
						*/
						if( $type === 'hidden' ){
							echo '<input type="hidden" value="' . $settings['selected'] . '" id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="attribute_' . esc_attr( $attribute_name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute_name ) ) . '">';
						}


						?>
					<?php if( $type === 'select' ){ ?>	
					</td>
					
					<?php 						
						echo '<!-- action: '. 'gaad_attribute_ui_'. $attribute_name .'_after_value' .'-->';
						do_action( 'gaad_attribute_ui_'. $attribute_name .'_after_value', $variation_data );			
					?>
					
				</tr>
				
				<?php } ?>
				
			<?php


		}
	}


	?>