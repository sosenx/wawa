
Vue.component('allegro-auction-item', {
  template: '#allegro-auction-item',
  props: [ 'item'],
  data: function(){
    return {message: 'hello'}
  }
})



let wawa_allegro = new Vue({
	
	template : "#wawa_product_allegro",
	el : "#wawa_allegro",
	data: {
		
		a : product_variations_csv
		
	},
  
  
  methods:{
    
    defaultSuccess: function(){
      
    },
    
    request: function( action, data, success ){
      if( typeof action === 'undefined' ){
        return false;
      }
      var data = typeof data !== 'undefined' ? data : {};
      var success = typeof success !== 'undefined' ? success : this.defaultSuccess;
      data.action = action;
      
      jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,	
				success: success,
				error: function(errorThrown){
					console.log(errorThrown);
				}

			});
      
    },
    
    
    addAuction: function( item ){
      this.request( 'addAllegroAuction', item );
    },
    
    callback_test123: function( data ){
      console.log('test213a', data);
    },
    test123: function(){
      
      console.log('test213');
      
      
      var data = {
				action: 'addAllegroAuction'				
			}

			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,	
				success: this.callback_test123,
				error: function(errorThrown){
					console.log(errorThrown);
				}

			});
			
      
      
    }
    
  }
	
});