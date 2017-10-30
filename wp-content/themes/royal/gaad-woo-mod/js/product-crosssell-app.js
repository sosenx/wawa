var productCrosssell = new Vue({
	
	template : '#product-cross-sell-template',
	
	el : "#product-cross-sell-app",
	
	data : {
		visible : false,
		items : cross_sell
	},
	
	
	
	mounted : function(){

    if( this.visible  ){
      jQuery( this.$el ).parent().addClass( 'is_visible' );
    } else {
      jQuery( this.$el ).parent().removeClass( 'is_visible' );
    }

  },
	
	methods : {
		
		
		
	}
	
	
	
});