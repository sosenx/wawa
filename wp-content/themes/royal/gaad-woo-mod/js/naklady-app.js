
var naklady_app = new Vue({
	template : '#product-naklad',
	
	el: '#nakladyapp',	

	data: {
		selected : null,
		express_disabled : false, 
		express_allowed : true, 
    blocked : false,
		visible : true,
		q : {}			
	},

	created : function(){
		if( calc_class == 'ksiazka' ){
			this.express_disabled = true;
      this.express_allowed = false;      
		}		
    
	},

  mounted : function(){

    if( this.visible  ){
      jQuery( this.$el ).parent().addClass( 'is_visible' );
    } else {
      jQuery( this.$el ).parent().removeClass( 'is_visible' );
    }

  },

	watch : {
    
    visible : function( val ){
      
      if( this.visible  ){
        jQuery( this.$el ).parent().addClass( 'is_visible' );
      } else {
        jQuery( this.$el ).parent().removeClass( 'is_visible' );
      }
      
      return val;      
    },
    
		selected : function( val ){
			jQuery(this.$el).find('.selected').removeClass('selected');
			jQuery('[variation_id="'+val+'"] a').addClass('selected')			
		}
		
	},
	
	updated : function(){
		/*
		*  Zaznaczenie podanego nakladu na liscie
		*/
		this.removeSelection();
		//debugger
		
		this.makeSelection( this.getVariationBy_pa_naklad( parseInt(variations_form.variation_data.attribute_pa_naklad) ).standard.variation_id );
		 
				
	},
	
	methods : {
    
    blockInteractions( e ){      
      e.stopPropagation();      
      return false;
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
    
		enableExpress : function(){
			//debugger
		},	
	
		getVariationBy_pa_naklad : function( pa_naklad ){
			var r = {};
      
      if( typeof calc_data === 'undefined' ){
        calc_data = calc_data_bak;
      }
      
			for(var i =0; i< calc_data.q.length; i++){
				
				if( calc_data.q[ i ]._quantity === pa_naklad ){
					r.standard = calc_data.q[ i ];
				}
				if( calc_data.q[ i ].express.pa_naklad === pa_naklad ){
					r.express =  calc_data.q[ i ].express;
				}				
			}
			return r;
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
			
		},
	
		showSummary : function( variation_id, e){
			productSummaryApp.variation_id = variation_id; 
      productSummaryApp.variation_data = Object.assign({}, variations_form.variation_data);
			this.removeSelection();
			
			/*Uaktualnienie formularza variations_form*/
			variations_form.variation_data.attribute_pa_naklad = this.getVariation(variation_id).variation_attr.attribute_pa_naklad;
			
			this.makeSelection( variation_id );
      
		},

		removeSelection : function(){
			this.selected = null;
		},
		
		makeSelection : function( variation_id ){		
			jQuery(this.$el).find( '[variation_id="'+ variation_id +'"] a' ).addClass( 'selected' );
			
			this.selected = variation_id;
      ;
			//nakazuje odtworzyc pdf z ofertą
      productSend.renewPdf = true;
      productSend.q = Object.assign({}, this._data.q);
		},
		
		refresh : function( dataObjName ){
			var tmp = this[ dataObjName ];
			this[ dataObjName ] = null;
			this[ dataObjName ] = tmp;			
		},

	}
	});