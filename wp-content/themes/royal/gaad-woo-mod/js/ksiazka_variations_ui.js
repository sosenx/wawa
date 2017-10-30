(function($, w){
	
	
	"use strict";
	
	var ksiazka_variations_UI = function( args ){
		
		this.args = args;
		
		this.calculationCallback = function(){
			debugger
		}
		
		/*
		* Wysya zapytanie o kalkulacje
		*/
		this.calculateVariationPrice = function(){
			
			
			this.getFormData();
			
			/*
			Pobieranie foramtu produkcyjnego
			*/

			var pa_format_patt = /(\d+)x(\d+)m?m?$/ig;
			var pa_format = this.post_data.attribute_pa_format;
			var uformat = pa_format_patt.exec( pa_format );

			var getFormat = new gformat({ 		
				'uformat' : {
					w: uformat[1],
					h: uformat[2],
					bleed : 2
				}
			});

			var production_formats = getFormat.init();
			
			
			
			jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data : { 
						"variation_id" : this.post_data.variation_id,
						"product_id" : this.post_data.product_id,
						"action" : 'gaad_calculate_ksiazka',
						"post_data" : this.post_data,
						"production_formats" : production_formats
					},
					success: function (data) { alert(data); },
					error: function(errorThrown){
						console.log(errorThrown);
					}
				});
			
	/*	
debugger
			jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data: data,
					success: this.calculationCallback,
					error: function(errorThrown){
						console.log(errorThrown);
					}
				});
			
			
			jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					action : 'gaad_calculate_ksiazka',
					success: function (data) { alert(data); },
					error: function(errorThrown){
						console.log(errorThrown);
					}
				});
			*/
			
			
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
		
		this.set = function(){
			var $form = this.$form = jQuery('.variations_form');
			$form.off();
			$form.on( 'change', '.variations_form select', { obj: this }, this.select_onChange );
			$form.on( 'blur', '.variations_form input', { obj: this }, this.input_onBlur );
			
			return this;
		};
		
		
		
		
		
		
		
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
					
			ksiazka_UI.enable_submit();
			
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
			
			ksiazka_UI.enable_submit();
			
		};
		
		
		
		
		
		
		
		
		/*
		* Wlącza klawisz dodaj do koszyka		
		*/
		this.enable_submit = function(){
			if( ksiazka_UI.validate_form() ){
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
			
			alert('ksiazka_UI.add_to_cart');
			
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
		
		
		
		this.init = function( ){			
			console.log( 'ksiazka_UI initialized' );
			
			this.set();
			
			this.pa_format();
			this.pa_naklad();
			
			this.handle_submit();
			
			return this;
		};
		
		return this;
	};

	
	
	w.ksiazka_variations_UI = ksiazka_variations_UI;
	
})(jQuery, window);

var ksiazka_UI;

jQuery( document ).ready( function(){
	
	"use strict";
	
	ksiazka_UI = new ksiazka_variations_UI();
	ksiazka_UI.init();
	ksiazka_UI.setDummyData();
	
	
} );