

var wawa_product_template_admin_UI = new Vue({
	
	
	el: "#product-templates-id",
	
	
	created: function(){
		
		
	},
	mounted: function(){
		
		
	},
  
  methods : {
    setTemplateFile : function( event ){
      
       file_frame = wp.media.frames.file_frame = wp.media({
          frame:    'post',
          state:    'insert',
          multiple: false
      });
      
      file_frame.targetInput = jQuery('[name="wawa_product_template[' + jQuery(event.currentTarget).data('for') + ']"]').eq(0);
      
      file_frame.on( 'insert', function() {
        
        var selection = file_frame.state().get('selection');
        var src = selection.models[ 0 ].attributes.url;
        file_frame.targetInput.val(src);

      });
      file_frame.open();
      
    }
    
  }
	
	
	
});