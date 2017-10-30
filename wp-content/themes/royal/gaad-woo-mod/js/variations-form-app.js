
var customFormatOption = Vue.extend({
	
	template : '#custom-format-option',		
	
	props : [ 'input_settings' ],
	
	data : function( ){
		return {
			width : 0,
			height : 0			
		};
	},	
	
	computed: {
		value : function( val ){			
			var unit = typeof this.input_settings.unit != 'undefined' ? this.input_settings.unit : 'mm';			
			
			var width = parseInt(this.width);
			var height = parseInt(this.height);
			var val = false;
			if( width > 0 && height > 0 ){
				val = width + 'x' + height + unit;
			}
			
			return val;
			
		}
	},
  
	methods : {
		
		setValue : function(){
			this.$root.variation_data[ this.$parent.attr_name ] = this.value;      
			this.$root.refresh('variation_data');
		},
		
		check : function( e ){
			var val = e.currentTarget.value;
			var patt = new RegExp ( typeof this.input_settings.custom_val_validate == 'undefined' ? '.*' : this.input_settings.custom_val_validate, 'g' ) ;
			var ok = patt.exec( val );
			
			var validated_val = ok != null ? ok[0] : '';
			this[ e.currentTarget.name ] = validated_val;
			
		}	
	}
});


var gInput = Vue.extend({
	template : '#g-input',		
	
	props : [ 'attribute', 'attr_name', 'input_settings', 'value', 'name', 'product_attr_data' ],
	
	data : function( ){
		return {
      optionsCounter : 0,
			showOptions : false,
			tempValue : null,
			ginput_class : 'g-input'
		};
	},
  
	created : function(){
		if( typeof this.input_settings == "undefined" ){
			this.input_settings = {};
		}	
		if( typeof this.input_settings.class == "undefined" ){
			this.input_settings.class = '';
		} else 	

		this.ginput_class = [this.ginput_class,  typeof this.input_settings.class == "undefined" ? this.input_settings.class : ''].join( ' ' );
	},
	
	methods: {
    
    isOptionsPanelEmpty: function( ){
      var attr_name = this._props.attr_name;
      var attr_value = this.$root.product_attr_data[attr_name];
      var current_value = this.$root.variation_data[attr_name];
      
      var c = 0;
      for( var i in attr_value){
        if( typeof attr_value[i].protector === 'undefined' && attr_value[i].slug !== current_value ){
          c++;
        }
      }
       
     return c == 0;
    },
    
    isAvaible: function( attr_value ){
      return !(typeof attr_value.unavaible === 'undefined' || attr_value.unavaible === false);
      
    },
    
    isCurrent : function( val ){      
      return this.$root.variation_data[ 'attribute_' + val.taxonomy ] == val.slug;
    },
    
    isCheckboxProtected: function( attribute ){
      var protected = false;
      for( var i in attribute ){        
        if( typeof attribute[i].protector === 'object' ){
          protected = true;
        }        
      }
          
      return protected;      
    },    
    
		getOptionLabel : function( val ){
			
			if( typeof val == 'string' ){
				val = {
					name : val,
					slug : val					
				}
			}
			
      if( typeof val === 'undefined' ){
        debugger
        return val;
      }
      
			var flabel = null;
			try{
				flabel = this.input_settings.labels.options[ val.slug ];
			} catch( e ){
				console.warn(e, "9876875");
			}
			var label = flabel == null ? val.name : flabel;
			
			return label;
		},
    
		/*		
		* Zanzacza zawarto inputa
		*/
		selectAll : function( e ){
			var target = e.currentTarget;
			target.select();				
		},
		
		checkboxClick : function( e ){
			
      if( this.isCheckboxProtected( this.$root.product_attr_data[ e.currentTarget.name ] ) ){
        e.preventDefault();
        e.stop
        return;
      }
      
			//fn escape
			if( typeof e == 'undefined' ){
				return; 
			}
			/*
			* Sprawdzanie, czy nie została kliknięta etykieta chckboxa, wtedy zmiana targetu na dołączony do niej input[checkbox]
			*/
			var target = e.currentTarget.tagName == 'LABEL' ? document.querySelectorAll('input[name=' + e.currentTarget.getAttribute('for') + ']') : e.currentTarget;
			
			/*
			jeżeli kliknięta została etykieta wywołuję event klik na podłączonym do jniej inpucie
			*/
			if( e.currentTarget.tagName == 'LABEL' ){				
				
				target[ 0 ].click();
				return;
			}
			/*
			var validated_val = target.checked;
			this.$root.variation_data[ e.currentTarget.name ] = validated_val;
			this.$root.refresh('variation_data');	*/		
      
      this.$root.setAttr( e.currentTarget.name, target.checked );
      variations_form_validate.check( e.currentTarget.name, target.checked ); 
		},
		
		isValueChanged : function( val ){		
			return this.tempValue != val;
		},
		
		storeCurrentValue : function( e ){
			var val = e.currentTarget.value.replace(' ', '');
			this.tempValue = val;
			
		},
		
		parseTextValue : function( val ){
			if( typeof this.input_settings.unit != 'undefined' ){
				var sep = typeof this.input_settings.unit_separator != 'undefined' ? this.input_settings.unit_separator : '';
				
				
				return val.replace( this.input_settings.unit, '' ).replace( sep, '' );							
			}
			return val;
		},
	
		setTextValue : function( e ){
			var val = e.currentTarget.value.replace(' ', '');
			
			if( this.isValueChanged( val ) ){ 
				var patt = new RegExp ( typeof this.input_settings.val_validate == 'undefined' ? '.*' : this.input_settings.val_validate, 'g' ) ;
				var ok = patt.exec( val );

				var validated_val = ok != null ? ok[0] : '';
        if( validated_val.length === 0 && patt.test( 0 ) ){
            validated_val = 0;
        }
        
        if( typeof this.input_settings.min !== 'undefined' && validated_val <= parseInt( this.input_settings.min ) ){
          validated_val = this.input_settings.min;
        }
        
         if( typeof this.input_settings.max !== 'undefined' && validated_val >= parseInt( this.input_settings.max ) ){
           validated_val = this.input_settings.max;
        }
        
        
				if( typeof this.input_settings.unit != 'undefined' ){
					var sep = typeof this.input_settings.unit_separator != 'undefined' ? this.input_settings.unit_separator : '';
					validated_val = validated_val + sep + this.input_settings.unit;
				}
/*
				this.$root.variation_data[ e.currentTarget.name ] = validated_val;
				this.$root.refresh('variation_data');
        */
        this.$root.setAttr( e.currentTarget.name, validated_val );
        variations_form_validate.check( e.currentTarget.name, validated_val ); 
			}
				
		},
	
		getLabel : function( val ){
			var attribute_data = this.$root.product_attr_data[ this.name ];
			for( var i in attribute_data){
				var attr_data = attribute_data[ i ];
				if( attr_data.slug == val ){
					return this.parseTextValue( attr_data.name );
				}				
			}
			
			return this.parseTextValue( val );
		},
		
		showOptionsPanel: function ( e ) {
			e.stopPropagation();
      //jezli panel jest aktualnie pusty nie wykonuje zadnej akcji
      if( this.isOptionsPanelEmpty() ){
        return;
      }
      
			this.$root.resetOptionsPanels();
			this.showOptions = !this.showOptions;			
		},
		
		setOption : function( val, e ){		
      
      var isAvaible = variations_form_validate.isAvaible( this.attr_name, val );
      if( !isAvaible ){
        return;
      }
      
      
			this.$root.setAttr( this.attr_name, val );
      
      if( typeof val == 'undefined' ){
        debugger
      }
      
			/*this.$root.variation_data[ this.attr_name ] = val;			
			this.$root.refresh('variation_data');*/
      variations_form_validate.check( this.attr_name, val ); 
      this.$root.resetOptionsPanels();
		}
		
		
	  },
	
});

Vue.component( 'ginput', gInput );
Vue.component( 'custom-format-option', customFormatOption );

var variations_form = new Vue({
	template : "#variations-form",
	el: "#variations_form",
	data : {		
		product_attr_data : product_attr_data,
		input_settings : input_settings,
		variation_data : product_basic_variation,
		visible : true,
    sections: null
	},
  
  mounted : function(){

    if( this.visible  ){
      jQuery( this.$el ).parent().addClass( 'is_visible' );
    } else {
      jQuery( this.$el ).parent().removeClass( 'is_visible' );
    }
    
    this.getSections();
    
    productSummaryApp.variation_data = this.variation_data;
    
  },
  

	methods : {
    
    getAttr: function( attr_name ){
      return this.variation_data[ attr_name ];
    },
    
    /*
    test attribute value agains pattern
    */
		testAttr: function(attr_name, patt ){
      var patt = typeof patt === 'undefined' ? /.*/ : patt;
      
      try{
        
        return patt.test( this.getAttr( attr_name ) );
        
      } catch(e){
        return false;
        console.error( 'testAttr error', attr_name, patt )
      }
      
      return false;
    },
    
    setAttr: function ( attr_name, value ){      
      this.variation_data[ attr_name ] = value;  
      
      this.variation_data = Object.assign({}, this.variation_data);
      variations_form_validate.variation_data = Object.assign({}, this.variation_data);
      
    },
    /*
    * zmienia widocznośc sekcji na przeciwną
    */
    sectonVisibility: function( e ) {
      e.stopImmediatePropagation();
      var id = jQuery(e.currentTarget).parents('section').attr('id');
      this.sections[id].visibility = !this.sections[id].visibility;
      
    },
    
    getSections: function(){
      var sections = jQuery( this.$el ).find( 'section[id]' );
      var sectionsObj = {};
      
      for( var i=0; i<sections.length; i++ ){
        var section = jQuery(sections[i]);
        var id = section.attr('id');
        var visibility = section.data('default-visibility') || false;
        sectionsObj[id] = {
            'visibility' : visibility
        };
        
      }
      
      this.sections = sectionsObj;     
    },
    
    checkSectionVisibility: function( sectionID ){
      //sekcje jeszcze nie zostały pobrane, wszystkie są niewidoczne
      if( this.sections == null ){
        return false;
      } else {        
       return this.sections[ sectionID ].visibility
      }      
    },
  
		resetOptionsPanels : function() {
			var children = this.$children;
			for( var i in children ){
				children[ i ].showOptions = false;
			}
      
      
      //resetowanie wyswietlania komunikatu dodano do koszyka
      if( typeof productSummaryApp.$refs.summaryFooter !== 'undefined' ){
        productSummaryApp.$refs.summaryFooter.addedToCartMsg = false;  
      }
      
      
      
		},
		
		setCalculatedPrice : function( data ){
			
			var data = typeof data == "string" ? JSON.parse( data ) : data;
			
			calc_data = data;
			calc_data.get = productSummaryApp.getVariation;
			
			naklady_app.lockSelection = true; 
			naklady_app.selection = data._quantity;
			naklady_app.q = data.q;
			
			
			productSummaryApp.variation_id = data.variation_id;
			productSummaryApp.variation_data = this.variation_data;
			product_basic_variation = Object.assign({}, this.variation_data );
      
			priceChartApp.calc_data = {};
			priceChartApp.calc_data = calc_data;
			naklady_app.blocked = false;
			
      
      //nakazuje odtworzyc pdf z ofertą
      productSend.renewPdf = true
      
		},
		
		/* nie dziala
		getProductionFormats : function(){
			var pa_format_patt = /(\d+)x(\d+)mm$/ig;
			var pa_format = this.$root.variation_data.attribute_pa_format;
			var uformat = pa_format_patt.exec( pa_format );

			var getFormat = new gformat({ 		
				'uformat' : {
					w: uformat[1],
					h: uformat[2],
					bleed : 0
				}
			});

			var f = getFormat.init();
			return f;
			
		},*/
		
		/*
		* Wysya zapytanie o kalkulacje
		*/
		calculateVariationPrice : function( refresh ){
			
      //wymuszenie pobrania kalkulacji
      if( refresh ){
        var a;
        calc_data = a;
      }
      
			/*
			* Dane dostarczone w zmiennej calc_data, utworzonej podczas loadu storny
			*/
			if( typeof calc_data !== 'undefined' ){
				this.setCalculatedPrice( calc_data );
				return true;
			} 
			
			var data = { 
				"variation_id" : 0,
				"product_id" : this.$root.variation_data.product_id,
				"action" : 'gaad_calculate_',
				"post_data" : this.$root.variation_data,
				"calc_class" : calc_class,
				//"production_formats" : this.getProductionFormats()
			};
			
			jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data : data,
					success: this.setCalculatedPrice,
					error: function(errorThrown){
						console.log(errorThrown);
					}
				});
			
		},
    
    //deprecated
		refresh : function( dataObjName ){
			var tmp = this[ dataObjName ];
			this[ dataObjName ] = null;
			this[ dataObjName ] = tmp;			
		},
		
	},
	
	watch : {
		
		variation_data : function( val ){			
			var tmp;
			calc_data = tmp;

      naklady_app.blocked = true;
			this.calculateVariationPrice();	
			this.resetOptionsPanels();
      
      if( typeof productTemplates !== 'undefined' ){
        productTemplates.attribute_pa_format = variations_form.variation_data.attribute_pa_format;  
      }
      
      /*
      * reaktywność w tym wypadku zawiodła, za dużo poziomów, odświeżanie wartości zmiennych przekazywancyh do gInput
      * Stworzone podczas obsługi validacji pól checkbox
      */
      for(var i in variations_form.product_attr_data ){          
        if(variations_form.product_attr_data[ i ] instanceof Array){
          var temp4565 = Array.from(variations_form.product_attr_data[ i ]);          
          variations_form.product_attr_data[ i ] = temp4565;  
        } else {
          
          variations_form.product_attr_data[ i ] = Object.assign( {}, variations_form.product_attr_data[ i ] );  
        }        
      }      
      return val;		
		},
		
	}
});

document.addEventListener("click", function(){
    variations_form.resetOptionsPanels();
});