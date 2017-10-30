<div class="product-send-app">

	<div class="header" v-if="visible">		
		<h3>Poleć ten produkt znajomemu / wyślij ofertę</h3>
	</div>
	

<br>
<br>
   
	<div  v-if="visible">
		

	
	  <div class="row">
	    
	    <div class="col-md-6">
	      
        <form class="product-send-form">
          
          
          <?php 
            if( is_user_logged_in() ){
              $current_user = wp_get_current_user();
              $email = $current_user->user_email;
            }
          ?>
          
          
          <label for="product-send-sender">Twój email</label>
          <label for="product-send-sender" class="error-lab" v-if="validateData.sender != null && !validateData.sender">Błąd! Podaj właściwy adres email nadawcy</label>          
          <input type="text" name="product-send-sender" data-validation-name="sender" value="" @blur="inputBlur( $event, 'sender')">
          
          <label for="product-send-reciever">Email znajomego</label>
          <label for="product-send-reciever" class="error-lab" v-if="validateData.reciever != null && !validateData.reciever">Błąd! Podaj właściwy adres email odbiorcy</label>
          <input type="text" name="product-send-reciever" data-validation-name="reciever" @blur="inputBlur( $event, 'reciever')" value="">
          
          
          
          <button 
            class="g-recaptcha" 
           :class="{ 'busy' : block }"
           data-sitekey="6LcqESwUAAAAAH6HMApwFYfAN3E57gkoJEP4XHn5" data-callback="newReCaptchaCallback" data-badge="bottomright" type="button">
            <span v-if="!block && !waiting4pdf">Poleć produkt</span>
            <span v-if="block">
              <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
              <span>Trwa wysyłanie oferty</span>
            </span>
            <span v-if="waiting4pdf">Czekam na plik</span> 
              
              
              
          </button>
          
          
          
          
        </form>
	      
	      
	    </div>
	    
	    
	    <div class="col-md-6">
       
       
        <div v-if="waiting4pdf" class="info-btn waiting">

          <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
          <span>Proszę czekać. Generowanie pliku PDF z ofertą</span>
        </div>	  

        <div v-else-if="pdfData" class="info-btn ready">

          <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
          <a :href="pdfHref" download>POBIERZ PDF z ofertą na ten produkt</a>
        </div>

        <div v-else class="info-btn error">

          <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
          <span>Wystąpił nieoczekiwany błąd podczas generowania pliku PDF. Przepraszamy, spróbuj ponownie później lub skontaktuj się z administratorem strony.</span>
        </div>	      
	     
	     
	       <div class="info-alert" v-if="formSent">Oferta została wysłana</div>
	     
	    </div>
	    
	  </div>
	
	
	

	  
	  
	  <div v-if="pdfData && showPdfData">
	    
	    <h2> Podgląd roboczy wygenerowanego pliku</h2>
	    
	    <object :data="pdfHref" type="application/pdf" style="width:100%;height:500px;" internalinstanceid="169" title="">
	      alt : <a :href="pdfHref">example_003.pdf</a>
	   </object>
	    
	  </div>
	



	</div>	

</div>