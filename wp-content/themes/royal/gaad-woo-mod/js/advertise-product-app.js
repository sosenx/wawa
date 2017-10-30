var advertise_product_app = new Vue({

	template : "#advertise_product-template",
	
	el : "#advertise-product-app",
	
	data : {
		visible : false,
		breadcrumbs_model : breadcrumbs_model,
		price : null,
		
	
		name : 'Gaad',
		//email : 'barteksosnowski711@gmail.com',
		email : 'b.sosnowski@c-p.com.pl',
		
		/*
		name : null,
		email : null,
		*/
		
		validAll : -1,
		valid : {
			name : false,
			email : false
			
		},
		
		validationData : {
			name : /.+/,
			email : /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/			
		},
		
	},
	
	created : function(){
		this.price = calc_data.q[0];		
	},
	
	methods : {
		
		validate : function(){
			var valid = false;
			
			for( var i in this.validationData ){
				var val = jQuery( '[name="'+ i +'"]' ).val();
				var pat = this.validationData[i];
				
				this.valid[i] = pat.test( val );				
				
				if( this.valid[i] && ( this.validAll == -1 || this.validAll ) ){
					this.validAll = true;
				} else {					
					this.validAll = false;
				}
			}	
						
			
			return this.validAll;
		},
		
		
		/*
		* Tworzy listę atrybutów produktu do maila korzystając z danych aplikacji variations_form
		*/
		parseProductAttr : function(){
			var data = variations_form._data;
			var product_attr_data = data.product_attr_data;
			var input_settings = data.input_settings;
			var variation_data = data.variation_data;
			
			var attributes = {};
			
			for( var i in product_attr_data ){
				var sett = input_settings[ i ];
				
				if( typeof sett != 'undefined' ){
					
					var value = variation_data[ i ];					
					if( sett.type == "select" && typeof sett.labels.options != 'undefined' ){
						value = sett.labels.options[ value ];
					}
					
					attributes[ i ] = {					
						label : sett.labels.l,
						value : value
					}

				}
								
			}
			
			return attributes;
			
		},
		
		setEmail : function ( e ){
			this.email = e.currentTarget.value;			
		},
		
		setName : function ( e ){
			this.name = e.currentTarget.value;			
		},
		
		sendAdvertisment : function(){
			
			//fn escape
			if( !this.validate() ){
				return;				
			}
			
			var data = {
				email : this.email,
				name : this.name,
				product_id : this.breadcrumbs_model.id,
				product_attr : this.parseProductAttr(),
				action: 'gaad_send_product_adrvertisment',				
				price : this.price._price,
				quantity : this.price._quantity
			};
			
			
			
			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,	
				//success : this.closeWindow,
				error: function(errorThrown){
					console.log(errorThrown);
				}

			}); 
			
			
		},
		
		resetForm : function() {
			/*this.name = null;
			this.targeEmail = null;*/
			this.validAll = -1;
			
		
		},
		
		showForm : function() {
			this.visible = true;
		
		},
		
		closeWindow : function(){
			this.resetForm();
			this.visible = false;
			
		}
		

	
	}
	
	
	

});