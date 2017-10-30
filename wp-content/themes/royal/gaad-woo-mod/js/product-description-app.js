var productDescription = new Vue({
	
	template : '#product-description',
	
	el : "#product-description-app",
	
	data : {
		visible : false,
    post: gaad_post,
    data : breadcrumbs_model,
    ship : productSummaryApp.ship
	},
  
  /*created: function(){
    
    if( typeof breadcrumbs_model !== 'undefined' ){
      this.data.data = Object.assign( this.data, breadcrumbs_model );
    }
    
    if( typeof gaad_post !== 'undefined' ){
      this.data.post = Object.assign( this.post, gaad_post );
    }
        
  },*/
  
  methods: {
    
    
    gotoParams: function(){
      gaad_nav_app.changeView( 'parameters' );
    },
    
    getTitle_c2a: function( title ){
      var plural = {
        'wizytówki' : 'wizytówki',
        'wizytówki ozdobne' : 'ozdobne wizytówki'
        
      }
      
      if( typeof plural[ title ] !== 'undefined' ){
        return plural[ title ];
      }
      
      return title;
    }
    
  }
	
});