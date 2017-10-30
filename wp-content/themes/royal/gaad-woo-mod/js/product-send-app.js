var productSend = new Vue({
  
  el: '#product-send-app',
  
  template: '#product-send-template',
  
  data: {
    //pokazywanie zgenerowanego pdfa
    showPdfData: false,
    //blokuje interakcje usera z klaiwszami podczas pracy programu
    block : false,
    renewPdf: false,
    visible : false,
    waiting4pdf: true,
    pdfData : false,
    pdfHref : false,
    summaryOV: window['summary-ov'],
    quantity : 1,
    price: 1,
    q : false,
    recaptchaResponse: false,
    captchaError: false,
    noRobot : false,
    formValid: false,
    
    senderEmail: '',
    recieverEmail: '',
    
    validateData: {
      sender : null,
      reciever : null
    },
    
    formSent : false
    
  },
  
  created :function(){
     this.renewPdf = true;
  },
  
  mounted: function(){
   
    
    
  },
  
  watch: {
    
    
    
    
    
    renewPdf: function( val ){
      
    },
    
    visible: function( val ){      
      if( val && this.renewPdf){                  
        this.waiting4pdf = true;      
        this.pdfData = false;         
        this.pdfCreate();        
      }      
    },
        
    pdfData: function( val ){
      if( !val ){ return; }
      
      this.pdfHref = val.path;
      
      return val;
    }
    
  },
  
  methods: {
   
    
    getFormdata: function( data ){
      var data = {};
      for( var i in this.validateData){
        
        data[ i ] = jQuery( this.$el ).find('[data-validation-name="'+ i +'"]').val();
      
      }
      
      data.pdfHref = this.pdfHref;
      
      return data;
    },
    
    productPdfSent: function( data ){
      this.reset( );
      console.log('productPdfSent');      
    },
    
    sendForm: function(){
      
      this.formSent = true;
      
        var data = {
          action : 'gaad_send_product_pdf',
          formdata: this.getFormdata()
        }
      
       jQuery.post({
        url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
        data : data,
        success: this.productPdfSent,
        error: function(errorThrown){
          console.log(errorThrown);
        }
      });
      
      
    },
    
    inputBlur: function( e, label ){
      var test = this.validateEmail( jQuery(e.currentTarget).val() );
      this.validateData[ label ] = test;      
    },
    
    validateEmail: function(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    },
    
    reset: function(){
      this.block = false;            
      this.validateData = {
        sender : null,
        reciever : null
      };
      this.formSent = false;      
      grecaptcha.reset();

    },
    
    formValidate: function(){
      this.block = true;
            
      var valid = true;
      for( var i in this.validateData){
        
        if( !this.validateEmail( jQuery( this.$el ).find('[data-validation-name="'+ i +'"]').val() ) ){
            valid = false;
            this.validateData[i] = false;
        }        
      }
      
      if( !valid ){
        grecaptcha.reset();        
      }
      
      return this.formValid = valid;      
    },
    
    
    reCaptchaCheck: function( d ){
      this.recaptchaResponse = typeof d !== 'undefined' ? d : this.recaptchaResponse;
            
      if( this.formValidate() ){
         jQuery.post({
          url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
          data : {
            action: 'gaad_recaptcha',          
            'g-recaptcha-response'  : this.recaptchaResponse
          },
          success: this.reCaptchaResponse
        });        
      }       

      
    },
    
    reCaptchaResponse: function( data ){
      if( data.success ){
        this.captchaError = false;
        this.sendForm();
      } else {
        this.captchaError = true;
      }
    },
        
    getQ: function(){
      var shortQ = [];
      for( var i in this.q ){
        var line = this.q[ i ];
        shortQ[ i ] = {
          standard: {
            price : line._price,
            quantity : line._quantity  
          }
        }
        
        if( typeof line.express._price !== 'undefined' ){
          shortQ[ i ].express = {
            price : line.express._price,
            quantity : line.express._quantity  
          }
        }
        
      }
      
      return shortQ; 
     
    },
    
    pdfCreate: function(){

      var data = { 
          "variation_id" : 0,
          "product_id" : variations_form.variation_data.product_id,
          "action" : 'product_variation_pdf',
          "post_data" : product_basic_variation,
          "breadcrumbs_model" : breadcrumbs_model,
          "summary_ov" : this.summaryOV,
          "calc_class" : calc_class,
          'q' : this.getQ(), 
          'origin' : window.location.href, 
          "price" : {
            "price" : this.price,
            "quantity" : this.quantity            
          }, 
        };

      jQuery.post({
        url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
        data : data,
        success: this.pdfCreated,
        error: function(errorThrown){
          console.log(errorThrown);
        }
      });
      
    },    
    
    pdfCreated: function( data ){      
      this.waiting4pdf = false;      
      this.pdfData = data;    
      this.renewPdf = false;
    }
    
    
  }
  
  
});

//wysłąnie kodu captcha do sprawdzenia
function newReCaptchaCallback( d ){   
  if( !productSend.block && productSend.formValidate()  ){
    productSend.reCaptchaCheck( d );    
  } else {
    console.log('productSend app: Klawisz send zablokowany');
  }
  
}

