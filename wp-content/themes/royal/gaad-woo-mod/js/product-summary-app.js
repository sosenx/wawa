//Vue.config.devtools = false;

Vue.component( 'summary-attr-ksiazka', {
  template : '#summary-attr-ksiazka',
  props: [ 'v' ],
  
data : function(){
    return {      
      ov : product_basic_variation,
      v2 : this.v,
      
      sections: [
        'basic-info', 
        'bw-info',
        'color-info',
        'cover-info',
        'extended-info'        
      ],
      
      order : {
          'basic-info' : [
            'attribute_pa_naklad',                
            'attribute_pa_format',
            'attribute_pa_orientacja',            
            'attribute_pa_oprawa',
            //'attribute_pa_okladka', //papier okladki    
            'attribute_pa_papier-okladki', //papier okladki
            'attribute_pa_obwoluta',            
          ], 
          'bw-info' : [
            'attribute_pa_ilosc-stron-czarno-bialych',
            'attribute_pa_papier-czarno-bialy',
            'attribute_pa_zadruk-strony-czarno-biale',
          ],
          'color-info' : [
            'attribute_pa_ilosc-stron-kolorowych',            
            'attribute_pa_papier-kolor',
            'attribute_pa_zadruk-strony-kolorowe',
            'attribute_pa_numery-stron-kolorowych',
            'attribute_pa_porozrzucane-str-kolor',
          ],
          'cover-info' : [            
            'attribute_pa_zadruk-okladki',
            'attribute_pa_rodzaj-okladki',         
            'attribute_pa_uszlachetnienie-okladki',   
            'attribute_pa_lakier-wybiorczy-okladki',            
          ],
          'extended-info' : [
            'attribute_pa_numer-isbnissn',
            'attribute_pa_pakowanie-w-folie',
            'attribute_pa_tytul-ksiazki',
            'attribute_pa_wiercenie-otworow',
          ] 
        
      }
  
    }
  },
  
    
  watch: {
   
    
    v: function(){      
      this.parseAttributes();      
    }, 
    
    v2: function(){      
      this.parseAttributes();
    }
    
  },
  
  methods: {
    
    parseAttributes: function(){      
      var tmp = {};
      
      
      
      for( var section in this.order ){
        if( typeof this.order[ section ] !== 'undefined' ){
          
       
          
          
          for( var section in this.order ){
            var counter = 0;
            if( typeof tmp[section] === 'undefined' ){
              tmp[section] = [];
            }   
            
            for( var i=0; i<this.order[ section ].length; i++ ){
              var value = this.v[ this.order[ section ][ i ] ];
              var labeled_val = this.getValueLabel( value , this.order[ section ][ i ] );
              var attr_name = this.order[ section ][ i ];
              
              
              if( typeof value === 'boolean' ){
                if( !value ){ continue; } 
                else {
                  labeled_val = 'Tak';
                }                                
              }
              
              if( labeled_val.length == 0 || labeled_val == '0' || /brak/.test( value ) ){
                continue;
              }
              
                          
                          
              tmp[ section ][ counter ] = {
                name : attr_name,
                label : this.getLabel( this.order[ section ][ i ] ),                
                val : labeled_val             
              }
              
              if( /naklad|ilosc-stron/.test(this.order[ section ][ i ]) && typeof this.$root.variation._quantity !== 'undefined' ){
                tmp[ section ][ i ].val = this.$root.variation._quantity + ' szt';
              }
              if( /ilosc-stron/.test(this.order[ section ][ i ]) ){
                tmp[ section ][ i ].val = this.$root.variation._quantity + ' stron';
              }
              
              
              counter++;
            }
          }
        }
      }
      
 
      /*
      * Usuwanie sekcji jeżeli są niepotrzebne
      */
      
      //sprawdzanie ilosci stron bw      
      var clear = true;
      for( i in tmp['bw-info'] ){
        if( tmp['bw-info'][ i ].name == 'attribute_pa_ilosc-stron-czarno-bialych' ){
          clear = false;
        }
      }
      if( clear ){
        tmp['bw-info'] = [];
      }
      
      var clear = true;
      for( i in tmp['color-info'] ){
        if( tmp['color-info'][ i ].name == 'attribute_pa_ilosc-stron-kolorowych' ){
          clear = false;
        }
      }
      if( clear ){
        tmp['color-info'] = [];
      }
      
      
      this.ov = tmp;
    },
    
    /*
    parseAttributes: function(){      
      var tmp = {};
      
      
      
      for( var section in this.order ){
        if( typeof this.order[ section ] !== 'undefined' ){
          
          if( typeof tmp[section] === 'undefined' ){
            tmp[section] = [];
          }
          var counter = 0;
          
          for( var i=0; i<this.order[ section ].length; i++ ){
            
            var value = this.v[ this.order[ section ][ i ] ];
            var labeled_val = this.getValueLabel( value , this.order[ section ][ i ] );
            
            wartosc jest false, opcja nie obowiązuje
            if( 
              ( typeof value === 'boolean' && !value ) ||
              ( typeof value === 'number' && value === 0 ) ||
              typeof labeled_val === 'undefined'
            ){
              continue;
            }                        
            
            if( typeof value === 'boolean' && value ){
              debugger
            }
            
            tmp[ section ][ counter ] = {
              name: this.order[ section ][ i ],
              label : this.getLabel( this.order[ section ][ i ] ),          
                        
              val: labeled_val
            }
            
            if( /naklad|ilosc-stron/.test(this.order[ i ]) && typeof this.$root.variation._quantity !== 'undefined' ){
              tmp[ section ][ i ].val = this.$root.variation._quantity + ' szt';
            }
            
            counter++;
          }
          
          
        }
      }
             
      this.ov = tmp;
    },*/
    
    getOptionLabel: function( val, attr_name ){
      if( typeof val == 'string' ){
				val = {
					name : val,
					slug : val					
				}
			}
			
      if( typeof val === 'undefined' ){
        console.log( 'getOptionLabel: val undefined', attr_name );
        return val;
      }
      
			var flabel;
			try{
			 flabel = input_settings[ attr_name ].labels.options[ val.slug ];
      } catch(e){}
			return flabel;
    },
    
		parseTextValue : function( val, attr_name ){

      var tmp = input_settings[attr_name];
      var optionLabel = this.getOptionLabel( val, attr_name );      
      
      if( typeof optionLabel !== 'undefined' ){
        return optionLabel;
      } else {
        
        //nie wszystkie atrybuty podstawowe znajdują się w bardziej złożonnych produktach
        if( typeof input_settings[ attr_name ] !== 'undefined' ){
          
          if( typeof input_settings[ attr_name ].unit != 'undefined' ){
            var sep = typeof input_settings[ attr_name ].unit_separator != 'undefined' ? input_settings[ attr_name ].unit_separator : '';
            return val.replace( input_settings[ attr_name ].unit_separator, ' ' );						
          } 
        }
        
          
        
        
      }
      
		},
    
    getValueLabel: function( val, attr_name ){
      if( val == '' ){
        return val;
      }
      
      var attribute_data = product_attr_data[ val ];
      for( var i in attribute_data){
				var attr_data = attribute_data[ i ];
				if( attr_data.slug == val ){
					return this.parseTextValue( attr_data.name, attr_name );
				}				
			}
			return this.parseTextValue( val, attr_name );
      
    },
    
    getLabel: function( val ){
      if( typeof input_settings[val] !== 'undefined' ){
        return input_settings[val].labels.l; 
      } else {
        return val;        
      }
      
    }
    
    
  }
} );

/*
* FOOTER
*/
Vue.component( 'summary-footer', {
  template : '#summary-footer',
  props: [ 'block' ],
  data : function(){
    return {
      addedToCartMsg : false
      
    }
    
  },
  
  methods :{
    addToOrder: function(){
      this.$root.addToOrder();
    },
    goTo: function( val ){
      this.$root.goTo( val );
    }
  }
  
} );

/*
* SHIPMENT
*/
Vue.component( 'summary-shipment', {
  template : '#summary-shipment',
  props: [ 'ship' ]
  
} );

/*
* PRICE
*/
Vue.component( 'summary-price', {
  template: '#summary-price', 
  props: [ 'quantity', 'price' ],
  data : function(){
    return {
      gross_price : this.calcGrossPrice()
      
    }
  },
  
  mounted: function(){
    this.equalPriceSuffixWidth();
    
  },
  
  
  watch:{
    
    price: function(){
      productSend.price = this.price;
      
    },
    
    quantity: function(){
      productSend.quantity = this.quantity;
      
    }
    
  },
  
  methods : {
    
    /*
    * Ustawia równe szerokosci suffixu ceny 
    */
    equalPriceSuffixWidth: function(){      
      var tn = jQuery( this.$el ).find('.net .t');
      var tg = jQuery( this.$el ).find('.gro .t:eq(1)');
      var tw = [ tn.width(), tg.width() ].sort();  
      var min = tw.shift();
      var max = tw[0];
      var paddingLeft = max - min;
      
      jQuery( this.$el ).find('.net .t').css({ paddingLeft: paddingLeft, display: 'inline-block' });  
     // jQuery( this.$el ).find('.t').css({width: tw.sort(function(a, b){return b-a})[0], display: 'inline-block' });  
    },
    
    calcNetPrice: function(){
      if( typeof this.$root.variation._price !== 'undefined' ){
        return Math.round( this.$root.variation._price * 100) / 100;
      }      
    },
    
    calcPiecePrice: function(){
      var gPrice = this.calcGrossPrice();
      if( typeof gPrice !== 'undefined'  ){
        var q = this.$root.variation._quantity;
        return Math.round( gPrice / q * 100) / 100;
      }  
        return -1;
    },
    
    calcGrossPrice: function(){
      if( typeof this.$root.variation._price !== 'undefined' ){
        return Math.round( this.$root.variation._price * this.getVat() * 100) / 100;
      }      
    },
    
    getVat: function(){
      
      if( calc_class == 'ksiazka' ){
        
        if( variations_form.variation_data['attribute_pa_numer-isbnissn'].length > 0 ){
          return 1.05;
        }        
      }
      /**/
      
      return 1.23;
    },
    
  }
} );


Vue.component( 'summary-attr-basic', {
  template : '#summary-attr-basic',
  props: [ 'v', 'quantity', 'ship' ],
  data : function(){
    return {      
      ov : product_basic_variation,
      v2 : this.v,
      order : [
                'attribute_pa_naklad',                
                'attribute_pa_format',                
                'attribute_pa_podloze',        
                'attribute_pa_zadruk',
                'attribute_pa_uszlachetnienie'
              ]      
    }
  },
  
  
 
  
  methods: {
    parseAttributes: function(){      
      var tmp = {};
      for( var i=0; i<this.order.length; i++ ){
        tmp[i] = {
          name: this.order[ i ],
          label : this.getLabel( this.order[ i ] ),          
          val: this.getValueLabel(this.v[ this.order[ i ] ], this.order[ i ] )
        }
        
        if( /naklad/.test(this.order[ i ]) && typeof this.$root.variation._quantity !== 'undefined' ){
          tmp[ i ].val = this.$root.variation._quantity + ' szt';
        }
        
      }
      
      if( typeof this.$root.ship.days !== 'undefined' && this.$root.ship.days !== null ){
        tmp[ i+1 ] = {
          name : 'Termin',
          label : 'Termin',
          val : parseInt( this.$root.ship.days ) >= 2 ? 'Standard' : 'Express'
        };
        
      } else {
        tmp[ i+1 ] = {
          name : 'Termin',
          label : 'Termin',
          val : 'Standard'          
        };
        
      }       
      this.ov = tmp;
    window['summary-ov'] = tmp;
      
    },
    
    getOptionLabel: function( val, attr_name ){
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
      
			var flabel;
			try{
			 flabel = input_settings[ attr_name ].labels.options[ val.slug ];
      } catch(e){}
			return flabel;
    },
    
		parseTextValue : function( val, attr_name ){
      var tmp = input_settings[attr_name];
      var optionLabel = this.getOptionLabel( val, attr_name );      
      
      if( typeof optionLabel !== 'undefined' ){
        return optionLabel;
      } else {
        
        if( typeof input_settings[ attr_name ].unit != 'undefined' ){
          var sep = typeof input_settings[ attr_name ].unit_separator != 'undefined' ? input_settings[ attr_name ].unit_separator : '';
          return val.replace( input_settings[ attr_name ].unit_separator, ' ' );						
        }
      }
      
		},
    
    getValueLabel: function( val, attr_name ){
      var attribute_data = product_attr_data[ val ];
      for( var i in attribute_data){
				var attr_data = attribute_data[ i ];
				if( attr_data.slug == val ){
					return this.parseTextValue( attr_data.name, attr_name );
				}				
			}
			
			return this.parseTextValue( val, attr_name );
      
    },
    
    getLabel: function( val ){
      if( typeof input_settings[val] !== 'undefined' ){
        return input_settings[val].labels.l; 
      } else {
        debugger
      }
      
    }
    
  },
  
  watch: {
   
    
    v: function(){
      
      this.parseAttributes();
      
    }, 
    v2: function(){
      
      this.parseAttributes();
      
    }
    
  }
} );

/*
*
*/
Vue.component( 'summary-attr', {
  template : '<div class="summary-attr"><component :v="v" :v2="v2" :is="\'summary-attr-\' + getCalcClass()" ref="attr"></component></div>',
  props: [ 'v' ],
  
  data : function(){
    
    return {
      v2 : this.v
      
    }
  },
  
  methods: {
    getCalcClass: function(){
      var tmp = calc_class;
      switch( calc_class ){
        case 'ksiazka' :              tmp = calc_class;            
          break;  
        case 'katalog' :              tmp = 'ksiazka';            
          break;  
        default:                      tmp = 'basic';
          break;
      }
      
      return tmp;
    }
    
  }
} );


var productSummaryApp = new Vue({
	template : '#product-summary',
	el : "#productSummaryApp",
	data : {
		variation_id : null,
		variation : {},	
    block: false,
    variation_data : variations_form.variation_data,
		ship : {
			days : null,
			date : null
		},
		visible : true
	},
  
  mounted : function(){

    if( this.visible  ){
      jQuery( this.$el ).parent().addClass( 'is_visible' );
    } else {
      jQuery( this.$el ).parent().removeClass( 'is_visible' );
    }
    
    
  },
  
  updated : function(){
    
    if( this.visible ){      
     
     
      this.$refs.attr_mng.$refs.attr.v2 = Object.assign({}, variations_form.variation_data );            
     
    }  
  },
  
  
	methods: {
		goTo : function( url ){
			window.location = url;
		},
		
		orderAdded : function( data ){	
      productSummaryApp.block = false;      
      productSummaryApp.$refs.summaryFooter.addedToCartMsg = true;
			console.log( data );
		},
		
		addToOrder : function(){	
      if( this.block == true ){
        return;
      }
      
			var variation_attr = this.variation.variation_attr;
			this.block = true;
      
			var totals = {
				subtotal : this.variation._regular_price,
				total : this.variation._regular_price
			};
			
			var data = {
				variation_attr : this.parseVariationAttr(variation_attr),
				action : 'gaad_create_order',
				product_id : product_id,
				totals : totals,
				shipment : this.ship
			};
			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,
				success:this.orderAdded,
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});  
			
		},
		
		/*
		* Filtruje tablicę variation_attr w poszukiwaniu kluczy zaczynajacych się an attribute_
		* Pozostałe zawarte w tblicy klucze będą usunięte
		*/
		parseVariationAttr : function( variation_attr ){
			var parsed = {}
			var patt = /^attribute_/;
			if( typeof variation_attr == 'object' ){
				for(var i in variation_attr){
					if( patt.exec( i ) ){
						parsed[i] = variation_attr[i];
					}	
				}
				
			}		
			return parsed;
		},
		
		shipDate : function( val ){
			var date = (new Date()).toISOString().slice(0,10).split('-').reverse();
				date[0] = parseInt(date[0]) + val - 1;
			this.ship.days = val - 1;
			this.ship.date = date.join('.') ;
			
		},
		
		getVariation : function( val ){
			for(var i =0; i< calc_data.q.length; i++){
				
				if( calc_data.q[ i ].variation_id === val ){
					return calc_data.q[ i ];
				}
				if( calc_data.q[ i ].express.variation_id === val ){
					return calc_data.q[ i ].express;
				}				
			}
			
		}
		
	},
	
	watch : {
	  
		variation_id : function( val ){
			var variation_json = this.getVariation(val);
			if( variation_json !== null && typeof variation_json !== 'object' && typeof variation_json !== 'undefined' ){
				this.variation = JSON.parse( variation_json );
			}else if( typeof variation_json === 'object' )
			{
				this.variation = variation_json;
			} else {
				this.variation = calc_data;
			}
						
			var termin = this.variation.variation_attr['attribute_pa_termin-wykonania'];
			if( typeof termin == 'undefined' ){
				termin = 5;
			} else {
				var patt = /termin-(.+)/;
				termin = parseFloat( patt.exec( termin )[1].replace('-', '.') )  == 1 ? 5 : 2;
			}
      
      
      /*
      * wyjatek: lakierowanie wybiorcze
      */
      
      if( 
        typeof this.variation.variation_attr['attribute_pa_lakier-wybiorczy'] !== 'undefined' &&   
        this.variation.variation_attr['attribute_pa_lakier-wybiorczy'] !== 'brak' ){        
        naklady_app.express_disabled = true;
        
        termin += gaad_db.uv_days_number;
        
      } else {
        if( naklady_app.express_allowed ){          
          naklady_app.express_disabled = false;            
        }
        
        
      }
      
			this.shipDate( termin );						
		}	
	}
	
	
});