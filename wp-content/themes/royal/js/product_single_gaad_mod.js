(function($, w){
	
	var gaad_calc = function( args ){
		
		this.args = args;
		
		
		/**
		* 
		*
		* @return void
		*/
		this.setDefaults = function (  ) {
		
			if( typeof this.args.postDataKeyPrefix == 'undefined' ){
				this.args.postDataKeyPrefix = 'attribute_';
				
				
			}
			
		}
		
		
		/**
		* Zbiera wszystkie wartosci pól skladających się na wariacje i dopisuje je do obiektu postData
		*
		* @return void
		*/
		this.getVariation = function(  ) {
			
			var $form = this.$el;
			var $fields = $form.find('table.variations').find('[id]');
			for(var i=0; i<$fields.length; i++){
				var $field = $fields.eq(i);
				
				var id = $field.attr('id');
				var val = $field.val();				
				
				if( id == 'pa_uszlachetnienie' && val == '' ){
					val = 'brak';
				}
				
				this.postData[ this.args.postDataKeyPrefix + id ] = val;
			}						
			
		}
		
		/**
		* Odbiera dane z getPorductVariant
		*
		* @return void
		*/
		this.getPorductVariantCallback = function ( data ) {
			/*jQuery(".gaad-price").remove();
			var $price = jQuery('<h1 class="gaad-price"><span>'+ data.display_regular_price +' zł</span></h1>');
			$price.delay(1000).insertAfter( gaad_calc_app.$el.find('.variations') );/**/
			
		console.log( 'current variant display_regular_price', data.display_regular_price );
		}
		
		
		
		/*
		* Pobiera skalkulowany variant 
		*/
		this.getPorductVariant = function(){
			
			debugger
			var data = jQuery.extend( { 'action':'gaad_get_product_variant' }, this.postData );
			
			
			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,
				success:this.getPorductVariantCallback,
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});  
			
		}
		
		this.formField__changeEvent = function( e ){
			gaad_calc_app.setPostData();			
			gaad_calc_app.getPorductVariant();
		}
		
		/*
		* Eventy formularza 
		*/
		this.events = function(){
			var $form = this.$el;
			var $fields = $form.find('table.variations').find('[id]').filter(':not([type="submit"])');
			
			//$fields.unbind().on('click', this.formField__changeEvent);
			
		}
		
		/*
		* Ustawia kompletny obiekt do zapytania post
		*/
		this.setPostData = function(){
			
			this.postData = {
				"product_id" : this.$el.attr('data-product_id')
			}
			
			this.getVariation();
			
		}	
				
		this.set = function( ){
			this.setPostData();
		}	
		
		this.init = function( ){			
			this.$el = $( this.args.target );
			
			this.setDefaults();
			this.set();
			this.events();
			
			//this.getPorductVariant(); 
			return this;
		}
		
		return this;
	}

	
	 
	w.gaad_calc = gaad_calc;
	
})(jQuery, window);

	var gaad_calc_app;







jQuery( document ).ready(function(){
	gaad_calc_app = new gaad_calc(
			{
				target : '.variations_form.cart'
			}
		);
	
	gaad_calc_app.init();
	
	//jQuery('#product-main-tabs').tabs({	});
	
	
	
	
	
})