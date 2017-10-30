var wawa_breadcrumbs_app = new Vue({
	
	template : "#wawa_breadcrumbs_template",
	el : "#wawa_breadcrumbs",
	data: breadcrumbs_model,
	
	
	created : function(){
		this.price = calc_data.q[0];    
	},
	
	mounted : function(){		
		
		var pos = jQuery.extend( jQuery('.crumbs-main-row').offset(), {
			
			height : jQuery('.crumbs-main-row').height(),
			width : jQuery('.crumbs-main-row').width()
			
		} );
		
		pos.bottom = pos.top + pos.height;
		
		
		var $price_wrap = jQuery( this.$el ).find( '.price-wrap' );
		var $product_actions = jQuery( this.$el ).find( '.product-actions' );
		
		
		
		$price_wrap.css( { top : pos.height - ( $price_wrap.outerHeight() + 30 ) } );
		$product_actions.css( { top : pos.height - ( $price_wrap.outerHeight() + 30 ) } );
		
	},
	
	methods : {
	
		
		
		event : function( eName ){
			if( typeof this[ eName ] == 'function'){
				this[ eName ]();
			}
		
		},
		
		show_crosssell_event : function(){
			gaad_nav_app.changeView( 'crosssell' );
		
		},
		
		show_advertise_product : function(){
			advertise_product_app.showForm( );
		
		}
		
		
		
		
	
	}
	
	
});