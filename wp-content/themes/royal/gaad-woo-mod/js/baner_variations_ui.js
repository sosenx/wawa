(function($, w){
	
	
	"use strict";
	
	var baner_variations_UI = function( args ){
		
		this.args = args;
		
		this.calculationCallback = function(){
			debugger
		}
		
		
		
		this.setCalculatedPrice = function ( data ){
			var $form = jQuery( '.variations_form' );
			calc_data = data;
			calc_data.get = productSummaryApp.getVariation;
			
			var $singleVariation = $form.find( '.single_variation' );
			
			/*
			Przekazanie danych do aplikacji generujacej tabale z cenami poszczegolnych nakladów
			*/
			naklady_app.q = data.q;
			productSummaryApp.variation_id = data.variation_id;
		
			
			var tpl = jQuery('<div>' +
					'<div class="woocommerce-variation-description"></div>' + 

					'<div class="woocommerce-variation-price">' + 
						'<span class="price"><span class="woocommerce-Price-amount amount">'+ data._regular_price +' <span class="woocommerce-Price-currencySymbol">zł</span></span></span>' +
					'</div>' +

					'<div class="woocommerce-variation-availability">'+
						'<p class="stock in-stock">Na stanie</p>'+
					'</div>'+
					'</div>'							 
			);
			
			$singleVariation.html( tpl.html() );
			
			/*
			* ustawienie pobranej/stworzonej wariacji w formularzu
			*/
			$form.find('input.variation_id').val(data.variation_id);
			
						
		};
		
		
		this.getCurrentProductId = function(){
			
			if( typeof this.post_data.product_id === 'undefined' ){
				return this.post_data.product_id = product_id;
			} 
			
			return this.post_data.product_id;
			
		};
		
		/*
		* Wysya zapytanie o kalkulacje
		*/
		this.calculateVariationPrice = function(){
			
			/*
			* Dane dostarczone w zmiennej calc_data, utworzonej podczas loadu storny
			*/
			if( typeof calc_data !== 'undefined' ){
				this.setCalculatedPrice( calc_data );
				return true;
			} 
			
			this.getFormData();
			
			jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data : { 
						"variation_id" : this.post_data.variation_id,
						"product_id" : this.getCurrentProductId(),
						"action" : 'gaad_calculate_',
						"post_data" : this.post_data
					},
					success: this.setCalculatedPrice,
					error: function(errorThrown){
						console.log(errorThrown);
					}
				});
			
		}
		
		this.getFormData = function(){
			var $fields = jQuery( this.$form ).find( '[name]' );
			var post_data = {};
			for( var i=0; i<$fields.length; i++ ){				
				var $field = $fields.eq( i );
				var name = $field.attr('name');
				post_data[ name ] = $field.val();
				
			}
			this.post_data = post_data;
			return post_data;
		}
		
		
		
		this.set = function(){
			var $form = this.$form = jQuery('.variations_form');
			$form.off();
			$form.on( 'change', 'select', { obj: this }, this.select_onChange );
			$form.on( 'blur', 'input', { obj: this }, this.input_onBlur );
			
			return this;
		};
				
		
		this.input_onBlur = function(){
			var tmp;
			calc_data = tmp;
			
			baner_UI.calculateVariationPrice();	
		}
		
		
		this.select_onChange = function  (  ) {
			var tmp;
			calc_data = tmp;
			
			baner_UI.calculateVariationPrice();			
		}
		
		/*
		* Nakład
		*/
		this.pa_naklad = function(){
			jQuery('select[name="attribute_pa_naklad"]').off().on( 'change', this.attribute_pa_naklad_change );
		};
		
		/*
		* Nakład eventy
		*/
		this.attribute_pa_naklad_change = function( e ){
			var value = jQuery(e.currentTarget).val();
			
			if( value === 'wlasny-naklad' ){
				jQuery('.custom_pa_naklad').removeClass( 'gaad_display_none' );
			} else {
				jQuery('.custom_pa_naklad').addClass( 'gaad_display_none' );
			}
					
			baner_UI.enable_submit();
			
		};
		
		
		/*
		* Format
		*/
		this.pa_format = function(){
			jQuery('select[name="attribute_pa_format"]').off().on( 'change', this.attribute_pa_format_change );
		};
		
		/*
		* Format eventy
		*/
		this.attribute_pa_format_change = function( e ){
			var value = jQuery(e.currentTarget).val();
			
			if( value === 'wlasny-format' ){
				jQuery('.custom_pa_format').removeClass( 'gaad_display_none' );
			} else {
				jQuery('.custom_pa_format').addClass( 'gaad_display_none' );
			}
			
			baner_UI.enable_submit();
			
		};
		
		
		
		/*
		* Wlącza klawisz dodaj do koszyka		
		*/
		this.enable_submit = function(){
			if( baner_UI.validate_form() ){
				this.$submit.removeClass( 'disabled' ).removeClass( 'wc-variation-selection-needed' );
			}
		};
		
		
		/*
		* Sprawdza poprawność formularza
		*/
		this.validate_form = function(){
			return true;
		};
	
		
		/*
		* Dodanie do koszyka
		*/
		this.add_to_cart = function(){
			
			alert('baner_UI.add_to_cart');
			
		};
		
		
		/*
		* Kliknięcie dodaj do koszyka
		*/
		this.submit_click = function( e ){
			e.preventDefault();
			
			if( e.data.obj.validate_form() ){
				e.data.obj.add_to_cart();
			}
		};
		
		
		
		this.handle_submit = function(){
			
			this.$submit = jQuery('button[type="submit"].single_add_to_cart_button');
			this.$submit.off().on( 'click', { obj : this }, this.submit_click);
			
		};
		
		
		this.setDummyData = function(){
			var $fields = jQuery( this.$form ).find( '[name]' );
			var post_data = {};
			for( var i=0; i<$fields.length; i++ ){
				
				var $field = $fields.eq( i );
				var name = $field.attr('name');
				if( $field.is( 'input:not([type="hidden"]):not([type="submit"])' ) ){
					
					if( $field.is( 'input[type="checkbox"]' )  ){
						$field.attr( { checked: true } ).val( true );
						continue;
					}
					if( $field.is( 'input[name="attribute_pa_format"]' )  ){
						var size = ( Math.floor( Math.random() * 100 ) + 100 ) + 'x' + ( Math.floor( Math.random() * 100 ) + 100 ) + 'mm';
						$field.val( size );
						continue;
					}
					
					
					
					$field.val( Math.floor( Math.random() * 100 ) + 1 );
					
					
				}
			
				if( $field.is( 'textarea' ) ){
					 $field.val( '666' );
				}
				
				if( $field.is( 'select' ) ){
					var $options = $field.children('option:not([data-show_option_none="yes"])')
					var random_option = Math.floor( Math.random() * $options.length-1 ) + 1;
					$options.eq( random_option ).attr( 'selected', 'selected' );										
				}
				
				
			
				
			
		}
		}
		
		
		this.init = function( ){			
			console.log( 'baner_UI initialized' );
			
			this.set();
			
			this.pa_format();
			this.pa_naklad();
			
			this.handle_submit();
			
			return this;
		};
		
		return this;
	};

	
	
	
	
	
	w.baner_variations_UI = baner_variations_UI;
	
})(jQuery, window);

var baner_UI;

jQuery( document ).ready( function(){
	
	"use strict";
	
	baner_UI = new baner_variations_UI();
	baner_UI.init();
	//baner_UI.setDummyData();
	//baner_UI.calculateVariationPrice();
	
} );