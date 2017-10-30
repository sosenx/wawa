
var templateDescritpion = Vue.extend({  
  template : "#template-descritpion",
});
 
Vue.component( 'template-description', templateDescritpion );


var productTemplates = new Vue({
	
	template : '#product-templates',
	
	el : "#product-templates-app",
	
	data : {
		bleed : 3, 	
		safeMargin : 3, 	
    	attribute_pa_format : '0x0mm',
		visible : false,
		templates : product_templates,
		productPermalink : window.location.href
	},
  
	created : function(){
		this.attribute_pa_format = this.parse_attribute_pa_format( variations_form.variation_data.attribute_pa_format );				
	},
	
	updated: function(){
		/*
		* Roszady w położeniu elementów
		*/
		var first_title = jQuery(this.$el).find('section').first().find('h3').first();
		var top_col = jQuery(this.$el).find('.top-col-left');
		
		
		first_title.appendTo( top_col );
		
	},
	
	watch: {
		
		attribute_pa_format:  function( format ){
				
			return this.parse_attribute_pa_format( format );
		}
				
	},
	
	
  methods : {
   
	parse_attribute_pa_format : function( format ){
		var format = variations_form.variation_data.attribute_pa_format;
				var patt = /(\d+)x(\d+)(mm|cm|m)\)?$/g;
		
				var parsedFormat = patt.exec(format);
			
					if( parsedFormat != null ){
						var w = parsedFormat[1];
						var h = parsedFormat[2];

						this.attribute_pa_format = w + 'x' + h + parsedFormat[3];		
						return this.attribute_pa_format;
					}
			
				return pa_format;
		
	}, 
	  
	  
	prepareToPrint: function (){
		
		var link = document.createElement('link')
		link.setAttribute('rel', 	'stylesheet' );
		link.setAttribute('type', 	'text/css' );
		link.setAttribute('media', 	'print' );
		link.setAttribute('id', 	'print-css' );
		
		link.setAttribute('href', 'http://wawaprint.pl/wp-content/themes/royal/gaad-woo-mod/css/wawa-product-template-print.css')
		document.getElementsByTagName('head')[0].appendChild(link)
		
		setTimeout( window.print, 1500);
	},
	  
	calcGrossFormat: function( format ){
		var patt = /(\d+)x(\d+)(mm|cm|m)/g;		
		var parsedFormat = patt.exec(format);
		
		if( parsedFormat != null ){
			var w = parseInt(parsedFormat[1]) + this.bleed * 2;
			var h = parseInt(parsedFormat[2]) + this.bleed * 2;

			return w + ' x ' + h + ' <span class="unit">' + parsedFormat[3] + '</span>';		
		}
		return format;
	},
	  
	formatToText: function( format ){
		var patt = /(\d+)x(\d+)(mm|cm|m)/g;
		
		var parsedFormat = patt.exec(format);
		
		if( parsedFormat != null ){
			return parsedFormat[1] + ' x ' + parsedFormat[2] + ' ' + parsedFormat[3];		
		}
				
		return format;
	},
	  
    getIcon: function( src ){
    	var patt = /.(jpg|pdf|psd|ai|cdr)$/;
		
		
		
      if( patt.test( src ) ){
        var ext = patt.exec(src);
		var icon = 'http://wawaprint.pl/wp-content/uploads/2017/07/' + ext[1] + '-icon.jpg';
		  
		  if( ext[1] == 'jpg' ){
			  return false;
		  }
		  
        return '<img class="template-file-icon icon-' + ext[1] + '" src="' + icon + '">';
      }
      
     
    }
    
  }
	
});