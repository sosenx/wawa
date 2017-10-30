(function($, w){
	
	var variations_prices = function( args ){
		
		this.args = args || {};
		this.process_array = [];
		
		/*
		* Zaokrągla liczne n do digits miejsc po przecinku
		*/
		this.roundTo = function(n, digits) {
			 if (digits === undefined) {
			   digits = 0;
			 }

			 var multiplicator = Math.pow(10, digits);
			 n = parseFloat((n * multiplicator).toFixed(11));
			 return (Math.round(n) / multiplicator).toFixed(2);
		   }
		
		
		
		/*
		* Zamykanie apletu dodawania cen
		* Funkcja sprawdza, czy aplet powinien zostać zamknięty ( praca w tle powinna zostać dokończona przed zamknięciem apletu )
		*/
		this.closeUI = function(){
			//debugger;
		} 
		
		/*
		* Usuwa inne aplety szybkiej edycji, np: szybka edycja WP
		*/
		this.cleanUp = function(){
			jQuery('#the-list').find('tr[id^="edit"]').remove();	
		}
		
		/*
		* Usuwa wszystkie elementy UI, nizależnie od tego gdzie się znajdują
		* Funkcja sprawdza, czy aplet powinien zostać zamknięty ( praca w tle powinna zostać dokończona przed zamknięciem apletu )
		*/
		this.destroyUI = function(){
			jQuery( '.gaad-variations-ui' ).remove();		
			jQuery( '#the-list' ).find('tr').removeAttr( 'style' );
		};
		
		this.showUI = function( e ){
			e.preventDefault();
			variations_prices_app.cleanUp();
			
			var $currentRow = variations_prices_app.args.$currentRow = jQuery(e.currentTarget).parents('tr[id]');			
			var product_id = variations_prices_app.postData.product_id = $currentRow.attr('id').replace('post-', '');
			
			variations_prices_app.getUI();			
		};
		
		
		/**
		* Rysuje pojedyncza linie wariacji i wysyła zapytanie ajax o jej cene
		* Narysowaną wariacje usuwa z listy i przezuca do tablicy process skad jest pobierana przez calculateVariationPrice i uzupełniana o cene
		*
		* @return void
		*/
		this.drawVariationLine = function  (  ) {
			var stack_size = variations_prices_app.process_array.length;
			var max_stack_size = variations_prices_app.args.max_stack_size;
			var $target = variations_prices_app.args.$currentRow.next().find("#variations-list > td");
			
			if( stack_size < max_stack_size && variations_prices_app.variations.length > 0 ){
				var variation = variations_prices_app.variations.pop();
				variations_prices_app.process_array.push( variation );
				
				var html = _.template(
					"<div class=\"variation-line\" id=\"variation-<%= id %>\">"+
					"<span><strong>ID#<%= id %></strong></span>"+
					
					
					"<% _.each( attributes, function( attr ) { %> "+
						"&nbsp|&nbsp<span class=\"attr\"><%= attr %></span>"+
					"<% }); %>"+
				
					
					"&nbsp|&nbsp<span class=\"variation-price\" style=\"display: none;\"><strong>CENA: </strong><span class=\"price-holder\"></span></span>"+
					"&nbsp|&nbsp<span class=\"action\" data-action=\"pending\"><i class=\"fa fa-cog fa-spin fa-3x fa-fw\"></i></span>"+
					"</div>"
				);


				variation.$line = jQuery(html(variation));
				variation.pending = true;
				variation.$line.prependTo($target);
				
			}
			
			
			/*
			* Zakończenie dodawania, obliczanie czasu operacji
			*/
			if( stack_size == 0 && variations_prices_app.variations.length == 0 ){
				variations_prices_app.stopTimestamp = + new Date()
				variations_prices_app.processTime	= variations_prices_app.stopTimestamp - variations_prices_app.startTimestamp;
				
				clearInterval(variations_prices_app.calculateVariationPriceInterval);
				clearInterval(variations_prices_app.drawVariationLineInterval);				
				
				var operationsTime = variations_prices_app.processTime / 1000;
				//console.log( 'Czas operacji: ' + operationsTime + 'sekund');
			}
			
		};
		
		
		this.calculateVariationPrice = function(){
			var stack_size = variations_prices_app.process_array.length;
			
			if( stack_size > 0){
				var variation = variations_prices_app.process_array.pop();
				var data = jQuery.extend(  jQuery.extend( { 
						"variation_id" : variation.id,
						"product_id" : variations_prices_app.postData.product_id
						}, variation.attributes ), { 'action':'gaad_variation_calculate' });
				
				
				
				
				
				/*
				Pobieranie foramtu produkcyjnego
				
				*/
				if( jQuery('body').is('.gaad-calc-ksiazka') && jQuery('body').is('.gaad-calc-katalog') ){
					var pa_format_patt = /(\d+)x(\d+)mm$/ig;
					var pa_format = variation.attributes.attribute_pa_format;
					var uformat = pa_format_patt.exec( pa_format );

					var getFormat = new gformat({ 		
						'uformat' : {
							w: uformat[1],
							h: uformat[2],
							bleed : 2
						}
					});

					var production_formats = getFormat.init();
					data = jQuery.extend( data, { 'production_formats' : production_formats });	
				}
				
				
				
				jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data: data,
					success:variations_prices_app.updateLinePrice,
					error: function(errorThrown){
						console.log(errorThrown);
					}
				});  
				
			} 
			
			
			
		};
		
		/*
		* 
		*/
		this.updateLinePrice = function( data ){
			console.log( data );
			var $variation_line = jQuery('.variation-line[id="variation-'+data.variation_id+'"]');
			
			var $variation_price = $variation_line.find('.variation-price');
			var $price_holder = $variation_line.find('.price-holder');
			
			var price_string = data._regular_price + ' ( '+ variations_prices_app.roundTo(data._regular_price * 123 / 100, 2) +' )';
			$variation_price.removeAttr('style');
			$variation_line.find('.action[data-action="pending"]').remove();
			$price_holder.html( price_string );
			
		}
		
		/**
		* Sprawdza czy każdy rekord został już obliczony
		* Jeżeli został operacja wyceniania okreslona jest jako całkowicie zakończona i liczony jest realny czas jej trwania
		*
		* @return void
		*/
		this.checkCompleteProcess = function (  ) {
			
			var isDone = variations_prices_app.args.$currentRow.next().find('.action[data-action="pending"]').length;
			//console.log(isDone);
			if( isDone == 0 ){
				//alert( 'Wszystkie warianty zostały wycenione' );
			}		
		}
		
		
		/**
		* 
		*
		* @return void
		*/
		this.drawVariationsSummaryDataUI = function ( variations ) {			
			variations_prices_app.variations = variations;
			variations_prices_app.drawVariationLineInterval = setInterval( variations_prices_app.drawVariationLine, 10 );			
			variations_prices_app.calculateVariationPriceInterval = setInterval( variations_prices_app.calculateVariationPrice, 20 );
			/*
			* Obliczanie realnego czasu wykonania obliczeń i pobierania wybików liczony od startu pieerwszego requestu do odpowiedzi ostatniego
			*/
			//variations_prices_app.calculateVariationPriceInterval = setInterval( variations_prices_app.checkCompleteProcess, 100 );
			
			variations_prices_app.startTimestamp = + new Date();
		}	
		

			
		/*
		* Pobiera dane o ilosci wariantów itp
		*
		*/
		this.getVariationsSummaryData = function(){
			
			var data = jQuery.extend(  variations_prices_app.postData, { 'action':'gaad_get_product_variations' });
			
			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,
				success:variations_prices_app.drawVariationsSummaryDataUI,
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});  
		}
		
		
		/**
		* 
		*
		* @return void
		*/
		this.ui_events = function (  ) {
			jQuery( '.gaad-variations-ui' ).find('.cancel[type="button"]').on( 'click', this.destroyUI );
			this.args.$currentRow.next().find( '.button-primary' ).on( 'click', this.getVariationsSummaryData );
			
		}
		
		
		/**
		* Generuje UI do dodawania cen
		*
		* @return void
		*/
		this.drawUI = function  ( data ) {	
			
			variations_prices_app.args.$currentRow.css( { "display" : "none" } );
			var $variations_prices_IU = jQuery( data );
			$variations_prices_IU.addClass( 'gaad-variations-ui' ).insertAfter( variations_prices_app.args.$currentRow );
			
			variations_prices_app.ui_events();
			
		}
		
		/*
		* Pobiera htmla UI do dowania cen za pomocą ajax
		*/
		this.getUI = function(){
			
			
			var data = jQuery.extend( { 'action':'gaad_add_variations_prices_UI' }, this.postData );
			
			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,
				success:this.drawUI,
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});  			
		}
		
		this.setDefaults = function(){
			if( typeof this.postData == 'undefined' ){
				this.postData = {
					'action':'gaad_add_variations_prices_UI'
				};
			}
			if( typeof this.args.max_stack_size == 'undefined' ){
				this.args.max_stack_size = 5;
			}
			
		}
		
		this.events = function(){		
			jQuery('.wycen_warianty').on( 'click', this.showUI );
			return this;
		}	
				
		this.init = function( ){			
			
			this.setDefaults();
			this.events();
			return this;
		}
		
		return this;
	}
	
	w.variations_prices = variations_prices;
	
})(jQuery, window);


var variations_prices_app;


jQuery( document ).ready( function(){

 	"use strict";
	
	variations_prices_app = new window.variations_prices();
	variations_prices_app.init();
	

});


/**
* To jest jet bardzo zajebista funkcja, tylko chuj wie co robi
*
* @return void
*/
function new_fn (  ) { }

