var variations_form_validate = new Vue({
	
	data : {		
		product_attr_data : product_attr_data,
		input_settings : input_settings,
		variation_data : product_basic_variation
    
	},
  
	methods : {
    
    check: function( attr_name, val ){
      if( typeof variations_form === 'object' ){
        variations_form.resetOptionsPanels();  
      }        
      eval('this.validate_' + calc_class + '("'+attr_name+'", "'+val+'")');       
    },
    
    validate_ksiazka: function( attr_name, val ){
       
     this.validate_katalog( attr_name, val );
      
      if( attr_name === 'attribute_pa_oprawa' ){
        
        /*
        * Oprawa twarda, szyta, klejona
        */
        if( /oprawa-twarda-/.test( val )){
          
          /*
          * Wyłączenie niedostępnych przy oprawie twardej papierów okładki
          */
          this.makeUnavailbe( 'attribute_pa_papier-okladki',
                              [ 'kreda-115g', 'kreda-130g', 'kreda-135g', 'kreda-150g', 'kreda-170g', 'kreda-250g', 'kreda-300g',
                                'karton-arktika-230g', 'karton-alaska-230g', 'karton-arktika-250g', 'karton-alaska-250g', ], 
                              [ 'attribute_pa_oprawa', [ 'oprawa-twarda-klejona', 'oprawa-twarda-szyta-nicmi' ] ]);
          
          /*
          * Wyłączenie okladki ze skrzydełkami
          */
          this.makeUnavailbe( 'attribute_pa_rodzaj-okladki',
                              ['ze-skrzydelkami'],
                              [ 'attribute_pa_oprawa', [ 'oprawa-twarda-klejona', 'oprawa-twarda-szyta-nicmi' ] ]
                            );
          
          /*
          * Wyłączanie druku dwustronnego okladki
          */
          this.makeUnavailbe( 'attribute_pa_zadruk-okladki',
                              [ 'dwustronnie-kolorowe-4x4-cmyk',  'dwustronnie-czarno-biale-1x1' ],
                              [ 'attribute_pa_oprawa', [ 'oprawa-twarda-klejona', 'oprawa-twarda-szyta-nicmi' ] ]
                            );
          
          this.makeUnavailbe( 'attribute_pa_uszlachetnienie-okladki',
                              [ 'folia-blysk-dwustronnie', 'folia-mat-dwustronnie','folia-soft-touch-dwustronnie' ], 
                              [ 'attribute_pa_oprawa', [ 'oprawa-twarda-klejona', 'oprawa-twarda-szyta-nicmi' ] ]); 
          
          this.makeEnabled( 'attribute_pa_obwoluta' ); 
          
        } //twarda
        
        
        /*
        * Oprawa inna niż twarda
        */
        
         if( !/oprawa-twarda-/.test( val )){
           /*
          * Wyłączenie niedostępnych przy oprawie twardej papierów okładki
          */
          this.makeUnavailbe( 'attribute_pa_papier-okladki',
                              [ 'karton-2mm', 'karton-2_5mm' ], 
                              [ 'attribute_pa_oprawa', 'oprawa-spiralna' ]);
          
          this.makeUnavailbe( 'attribute_pa_obwoluta',
                              true, 
                              [ 'attribute_pa_oprawa', 'oprawa-spiralna' ]);
          
          /*
          * Wyłączanie druku dwustronnego okladki
          * Wyjątkiem jest oprawa zeszytowa i spiralna, gdzie nie ma przeciwskazań, by zadrukować rewers okladki
          */
          if( !/oprawa-spiralna|oprawa-zeszytowa/.test( val )){  
            
            this.makeUnavailbe( 'attribute_pa_zadruk-okladki',
                                [ 'dwustronnie-kolorowe-4x4-cmyk',  'dwustronnie-czarno-biale-1x1' ],
                                [ 'attribute_pa_oprawa', [ "oprawa-miekka-klejona", "oprawa-miekka-szyta-nicmi", 'oprawa-twarda-klejona', 'oprawa-twarda-szyta-nicmi' ] ]
                              );
          }
          
           variations_form.setAttr( 'attribute_pa_obwoluta', false );
           
        } 
      
        
      } 
      
      
      if( attr_name === 'attribute_pa_ilosc-stron-czarno-bialych' ) {
        
           
           
        if( parseInt(val) > 0 ){
          variations_form.sections['bw-info'].visibility = true; 
          variations_form.sections['cover-info'].visibility = true;
        } else {
          variations_form.sections['bw-info'].visibility = false;
        }
        
      }    
      
      if( attr_name === 'attribute_pa_ilosc-stron-kolorowych' ) {
        variations_form.sections['color-info'].visibility = parseInt(val) > 0 ? true : false;
        
        if( parseInt( val ) > 0 ){
          this.makeEnabled( 'attribute_pa_porozrzucane-str-kolor' );
        } else {          
          this.makeDisabled( 'attribute_pa_porozrzucane-str-kolor' );
        }
        
        
      }
      
      
    },
    
    
    
    validate_katalog : function( attr_name, val ){
      
      //reset opcji, włączeie wszystkich opcji
      this.makeAllOptionsAvaible();
      
      /*
      * otwieranie sekcji cover
      */
      if( attr_name === 'attribute_pa_format' || attr_name === 'attribute_pa_orientacja' ){        
        variations_form.sections['cover-info'].visibility = true;        
      }
      /*
      * otwieranie sekcji color
      */
      if( attr_name === 'attribute_pa_oprawa' || attr_name === 'attribute_pa_papier-okladki'||
        attr_name ==='attribute_pa_uszlachetnienie-okladki' || attr_name === 'attribute_pa_lakier-wybiorczy-okladki' ){        
        variations_form.sections['color-info'].visibility = true;        
      }
      
      if( attr_name === 'attribute_pa_oprawa' ){
        
        if( /oprawa-miekka-klejona/.test( val )){
          
        
        
        this.makeUnavailbe( 'attribute_pa_papier-okladki',
                              [ 'kreda-115g', 'kreda-130g', 'kreda-135g', 'kreda-150g', 'kreda-170g', 'kreda-250g' ], 
                              [ 'attribute_pa_oprawa', 'oprawa-miekka-klejona' ]);  
          
        this.makeUnavailbe( 'attribute_pa_uszlachetnienie-okladki',
                              [ 'folia-blysk-dwustronnie', 'folia-mat-dwustronnie','folia-soft-touch-dwustronnie' ], 
                              [ 'attribute_pa_oprawa', 'oprawa-miekka-klejona' ]);  
        }
        
        
        /*
        * SPIRALA
        */
        if( /oprawa-spiralna/.test( val )){          
          
       
        
        
        
        this.makeUnavailbe( 'attribute_pa_papier-okladki',
                              [ 'kreda-115g', 'kreda-130g', 'kreda-135g', 'kreda-150g', 'kreda-170g', 'kreda-250g' ], 
                              [ 'attribute_pa_oprawa', 'oprawa-spiralna' ]);
         
        }  
        
      }
      
      if( attr_name === 'attribute_pa_uszlachetnienie' ){
          
        if ( !/brak/.test( this.variation_data['attribute_pa_uszlachetnienie'] )){
          this.makeUnavailbe( 'attribute_pa_papier-kolor',
                              [ 'kreda-90g', 'kreda-115g'], 
                              [ 'attribute_pa_uszlachetnienie', ['folia-blysk-dwustronnie', 'folia-mat-dwustronnie', 'folia-soft-touch-dwustronnie'] ]);
        } 
      }
            
      if( attr_name === 'attribute_pa_lakier-wybiorczy-okladki' && variations_form.variation_data['attribute_pa_lakier-wybiorczy-okladki'] ){
        
        //bez względu na ilośc stron lakierowania
        //wybrana jest blyszczaca folia, zmiana na matową
        if( /-blysk-/.test( this.variation_data["attribute_pa_uszlachetnienie-okladki"] ) ){
          this.variation_data["attribute_pa_uszlachetnienie-okladki"] = this.variation_data["attribute_pa_uszlachetnienie-okladki"].replace('-blysk-', '-mat-');             
        }
        
        if( /brak/.test( this.variation_data["attribute_pa_uszlachetnienie-okladki"] ) ){
          //this.variation_data["attribute_pa_uszlachetnienie-okladki"] = 'folia-mat-jednostronnie'; 
          variations_form.setAttr( "attribute_pa_uszlachetnienie-okladki", 'folia-mat-jednostronnie' );
          this.makeUnavailbe( 'attribute_pa_uszlachetnienie-okladki', 'folia-brak', [ 'attribute_pa_lakier-wybiorczy-okladki', 'true' ]);
        }
        
        
        this.makeUnavailbe( 'attribute_pa_uszlachetnienie-okladki', 'folia-blysk-dwustronnie', [ 'attribute_pa_lakier-wybiorczy-okladki', 'true' ]);
        this.makeUnavailbe( 'attribute_pa_uszlachetnienie-okladki', 'folia-blysk-jednostronnie', [ 'attribute_pa_lakier-wybiorczy-okladki', 'true' ]);
        
    //    debugger
      }
      
      
    },
    
    validate_ulotki : function( attr_name, val ){
      /*
      * w wizytówkach walidowane są te same atrybuty co w ulotce
      */
      this.validate_wizytowki( attr_name, val );
      
    },
    
    validate_wizytowki: function( attr_name, val ){
      //reset opcji, włączeie wszystkich opcji
      this.makeAllOptionsAvaible();
      


      
      if( attr_name === 'attribute_pa_zadruk' ){
        
        if( variations_form.testAttr( 'attribute_pa_zadruk', /jednostronnie/)  ){
         
          //zmiana dwustronnego uszlachetnienia na jednostronne
          if( variations_form.testAttr( 'attribute_pa_uszlachetnienie', /dwustronnie/ ) ){            
            variations_form.setAttr( 'attribute_pa_uszlachetnienie', variations_form.getAttr( 'attribute_pa_uszlachetnienie').replace('dwustronnie', 'jednostronnie') );
          } 
          
           //zmiana dwustronnego lakieru punktowego na jednostronne
          if( variations_form.testAttr( 'attribute_pa_lakier-wybiorczy', /dwustronnie/ ) ){            
            variations_form.setAttr( 'attribute_pa_lakier-wybiorczy', variations_form.getAttr( 'attribute_pa_lakier-wybiorczy').replace('dwustronnie', 'jednostronnie') );
          } 
          
          
        }
        
        
        if( variations_form.testAttr( 'attribute_pa_zadruk', /dwustronnie/)  ){
          
          //zmiana jednostronnego uszlachetnienia na dwustronnie
          if( variations_form.testAttr( 'attribute_pa_uszlachetnienie', /jednostronnie/ ) ){            
            variations_form.setAttr( 'attribute_pa_uszlachetnienie', variations_form.getAttr( 'attribute_pa_uszlachetnienie').replace('jednostronnie', 'dwustronnie') );
          }
          
          //zmiana jednostronnego lakieru punktowego na dwustronnie
          if( variations_form.testAttr( 'attribute_pa_lakier-wybiorczy', /jednostronnie/ ) ){            
            variations_form.setAttr( 'attribute_pa_lakier-wybiorczy', variations_form.getAttr( 'attribute_pa_lakier-wybiorczy').replace('jednostronnie', 'dwustronnie') );
          }
          
        }
        
        
        
      }
      
      if( attr_name === 'attribute_pa_uszlachetnienie' ){
        
        if( variations_form.testAttr( 'attribute_pa_uszlachetnienie', /jednostronnie/)  ){
          variations_form.setAttr( 'attribute_pa_zadruk', 'jednostronnie-kolorowe-4x0-cmyk' );
          
          //zmiana dwustronnego lakieru punktowego na jednostronne
          if( variations_form.testAttr( 'attribute_pa_lakier-wybiorczy', /dwustronnie/ ) ){            
            variations_form.setAttr( 'attribute_pa_lakier-wybiorczy', variations_form.getAttr( 'attribute_pa_lakier-wybiorczy').replace('dwustronnie', 'jednostronnie') );
          } 
          
        }        
        
        if( variations_form.testAttr( 'attribute_pa_uszlachetnienie', /dwustronnie/) ) {
          variations_form.setAttr( 'attribute_pa_zadruk', 'dwustronnie-kolorowe-4x4-cmyk' );
          
          //zmiana jednostronnego lakieru punktowego na dwustronnie
          if( variations_form.testAttr( 'attribute_pa_lakier-wybiorczy', /jednostronnie/ ) ){            
            variations_form.setAttr( 'attribute_pa_lakier-wybiorczy', variations_form.getAttr( 'attribute_pa_lakier-wybiorczy').replace('jednostronnie', 'dwustronnie') );
          }
        }
      }
      
      if( attr_name === 'attribute_pa_lakier-wybiorczy' ){
                
        //bez względu na ilośc stron lakierowania
        //wybrana jest blyszczaca folia, zmiana na matową        
        if( variations_form.testAttr( 'attribute_pa_uszlachetnienie', /-blysk-/) ){
          variations_form.setAttr( 'attribute_pa_uszlachetnienie', variations_form.getAttr( 'attribute_pa_uszlachetnienie').replace('-blysk-', '-mat-') );
        }
        
        
        //wybrano lakierowanie wybiorcze jednostronne   
        if (/jednostronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){          
          
          variations_form.setAttr('attribute_pa_zadruk', 'jednostronnie-kolorowe-4x0-cmyk');
          
          //jezeli nie ma ustawionego uszlachetnienia, ustawianie jednostronna folia matowa
          if( variations_form.testAttr( 'attribute_pa_uszlachetnienie', /brak|dwustronnie/ ) ){
            variations_form.setAttr( 'attribute_pa_uszlachetnienie', "folia-mat-jednostronnie" );
          } 
          
        }
        
        //wybrano lakierowanie wybiorcze dwustronne
        if (/dwustronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){
          
          variations_form.setAttr('attribute_pa_zadruk', 'dwustronnie-kolorowe-4x4-cmyk');
          
          //jezeli nie ma ustawionego uszlachetnienia, ustawianie dwustronna folia matowa
          if( variations_form.testAttr( 'attribute_pa_uszlachetnienie', /brak|jednostronnie/ ) ){
            variations_form.setAttr( 'attribute_pa_uszlachetnienie', "folia-mat-dwustronnie" );
          } 
          
        }
        
      }
      
      //wyłączanie opcji       
        //wyłączenie folii blyszczacej jeżeli wybrany jest lakier wybiórczy
        if(
          typeof variations_form.variation_data['attribute_pa_lakier-wybiorczy'] !== 'undefined' &&
          !/brak/.test( variations_form.variation_data['attribute_pa_lakier-wybiorczy'] )){
            this.makeUnavailbe( 'attribute_pa_uszlachetnienie', 'folia-blysk-dwustronnie');
            this.makeUnavailbe( 'attribute_pa_uszlachetnienie', 'folia-blysk-jednostronnie');
        
        }

      
      variations_form.calculateVariationPrice( true );
      
    },
    
    
    validate_wizytowki_bak: function( attr_name, val ){
      
        
      /*        
      * attribute_pa_zadruk
      */
      if( attr_name === 'attribute_pa_zadruk' ){
        
        //jeżeli wybrany jest lakier wybiorczy zmiana foliowania na matowe
        if ( !/brak/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){
          this.variation_data.attribute_pa_uszlachetnienie = this.variation_data.attribute_pa_uszlachetnienie.replace('-blysk-', '-mat-');               
        }
        
        
        //wybrano zadruk jednostronny
        if( /jednostronnie/.test( this.variation_data.attribute_pa_zadruk ) ){

          // uszlachetnienie
          //jezli wybrana jest folia dwustronna zamiana na jednostronną
          if( /dwustronnie/.test( this.variation_data.attribute_pa_uszlachetnienie ) ){
            this.variation_data.attribute_pa_uszlachetnienie = this.variation_data.attribute_pa_uszlachetnienie.replace('dwustronnie', 'jednostronnie');            
          }
          
          //lakier wybiorczy
          if (/dwustronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){
            this.variation_data['attribute_pa_lakier-wybiorczy'] = this.variation_data['attribute_pa_lakier-wybiorczy'].replace('dwustronnie', 'jednostronnie');            
          }
          
        }
        
        //wybrano zadruk dwustronny
        if( /dwustronnie/.test( this.variation_data.attribute_pa_zadruk ) ){

          // uszlachetnienie
          //jezli wybrana jest folia jednostronnie zamiana na dwustronnie
          if( /jednostronnie/.test( this.variation_data.attribute_pa_uszlachetnienie ) ){
            this.variation_data.attribute_pa_uszlachetnienie =
            this.variation_data.attribute_pa_uszlachetnienie.replace('jednostronnie', 'dwustronnie');            
          }
          
          //lakier wybiorczy
          if (/jednostronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){
            this.variation_data['attribute_pa_lakier-wybiorczy'] = this.variation_data['attribute_pa_lakier-wybiorczy'].replace('jednostronnie', 'dwustronnie');            
          }
          
        }
        
      }
      
      /*        
      * attribute_pa_uszlachetnienie
      */
      if( attr_name === 'attribute_pa_uszlachetnienie' ){
        
        //wybrano folię jednostronnie
        if( /jednostronnie/.test( this.variation_data.attribute_pa_uszlachetnienie ) ){
          
          //wykryto dwustronny zadruk, zmiana na jednostronny
          if( /dwustronnie/.test( this.variation_data.attribute_pa_zadruk ) ){
            this.variation_data.attribute_pa_zadruk = 'jednostronnie-kolorowe-4x0-cmyk';
          }
          
          //jezeli wybrany jest jakiś lakier wybiorczy zmiana na jednostronny
          if( /dwustronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] ) ) {
            this.variation_data['attribute_pa_lakier-wybiorczy'] = 'blyszczacy-lakier-punktowy-jednostronnie';
          }
          
          
        }
        
        //wybrano folię dwustronnie
        if( /dwustronnie/.test( this.variation_data.attribute_pa_uszlachetnienie ) ){
          
          //wykryto jednostronny zadruk, zmiana na dwustronny
          if( /jednostronnie/.test( this.variation_data.attribute_pa_zadruk ) ){
            this.variation_data.attribute_pa_zadruk = 'dwustronnie-kolorowe-4x4-cmyk';
          }
          
          //jezeli wybrany jest jednostronnie lakier wybiorczy zmiana na dwustronnie
          if( /jednostronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] ) ) {
            this.variation_data['attribute_pa_lakier-wybiorczy'] = 'blyszczacy-lakier-punktowy-dwustronnie';
          }
          
          
        }
        
        
      }
      
      /*        
      * attribute_pa_lakier-wybiorczy
      */
      if( attr_name === 'attribute_pa_lakier-wybiorczy' ){    
        
        //bez względu na ilośc stron lakierowania
        //wybrana jest blyszczaca folia, zmiana na matową
        if( /-blysk-/.test( this.variation_data.attribute_pa_uszlachetnienie ) ){
          this.variation_data.attribute_pa_uszlachetnienie = this.variation_data.attribute_pa_uszlachetnienie.replace('-blysk-', '-mat-');             
        }
        debugger
        //wybrano lakierowanie wybiorcze jednostronne        
        if (/jednostronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){
           
          //wykryto dwustronny zadruk, zmiana na jednostronny
          if( /dwustronnie/.test( this.variation_data.attribute_pa_zadruk ) ){
            this.variation_data.attribute_pa_zadruk = 'jednostronnie-kolorowe-4x0-cmyk';
          }
          
          //jezli wybrana jest folia dwustronna zamiana na jednostronną
          if( /dwustronnie|brak/.test( this.variation_data.attribute_pa_uszlachetnienie ) ){
            //pobieranie ustawnienia foliowania
            var test = /dwustronnie|brak/.exec( this.variation_data.attribute_pa_uszlachetnienie )[0];
            
            //nie wybrano foliowania, zmiana na jednostronny mat
            if( test == 'brak' ){ 
              this.variation_data.attribute_pa_uszlachetnienie = "folia-mat-jednostronnie";  
            }
            
            if( test == 'dwustronnie' ){ 
              this.variation_data.attribute_pa_uszlachetnienie = 
              this.variation_data.attribute_pa_uszlachetnienie.replace('dwustronnie', 'jednostronnie').replace('-blysk-', '-mat-');
            }
            
          }
          
        }
        
        //wybrano lakierowanie wybiorcze dwustronne
        if (/dwustronnie/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){
          
          //wykryto jednostronny zadruk, zmiana na dwustronny
          if( /jednostronnie/.test( this.variation_data.attribute_pa_zadruk ) ){
            this.variation_data.attribute_pa_zadruk = 'dwustronnie-kolorowe-4x4-cmyk';
          }
          
           //jezli wybrana jest folia jednostronnie zamiana na dwustronnie
          if( /jednostronnie|brak/.test( this.variation_data.attribute_pa_uszlachetnienie ) ){
            //pobieranie ustawnienia foliowania
            var test = /jednostronnie|brak/.exec( this.variation_data.attribute_pa_uszlachetnienie )[0];
            
            //nie wybrano foliowania, zmiana na dwustronny mat
            if( test == 'brak' ){ 
              this.variation_data.attribute_pa_uszlachetnienie = "folia-mat-dwustronnie";  
            }
            
            //wybrana jest folia jednostronna
            //zmiana  na dwustronną i NIE BŁYSK!
            if( test == 'jednostronnie' ){ 
              this.variation_data.attribute_pa_uszlachetnienie = 
              this.variation_data.attribute_pa_uszlachetnienie.replace('jednostronnie', 'dwustronnie').replace('-blysk-', '-mat-'); 
            }
            
          }
        }
        
        
        
        
        
      }
      
      
      
        //wyłączanie opcji       
        //wyłączenie folii blyszczacej jeżeli wybrany jest lakier wybiórczy
        if(
          typeof this.variation_data['attribute_pa_lakier-wybiorczy'] !== 'undefined' &&
          !/brak/.test( this.variation_data['attribute_pa_lakier-wybiorczy'] )){
            this.makeUnavailbe( 'attribute_pa_uszlachetnienie', 'folia-blysk-dwustronnie');
            this.makeUnavailbe( 'attribute_pa_uszlachetnienie', 'folia-blysk-jednostronnie');
        
        }

      
      variations_form.calculateVariationPrice( true );      
    },
    
    /*
    * Metody wspólne
    *
    */
    
    isAvaible: function( attrName, valueSlug ){
      var values = variations_form.product_attr_data[ attrName ];
      for(var i in values ){
        var value = values[ i ];
        if( value.slug == valueSlug ){          
          return typeof value.unavaible === 'undefined' ? true : !value.unavaible;
        }
      }
    },
    
    getLength: function( input ){
      
      if( typeof input === 'object' ){
        var length = 0;
        for( var i in input){
          if( typeof parseInt(i) === 'number' ){
            length++;
          }
          
        }
        //input.length = length;    
        return length;    
      }
      
    },
    
    isObjEmpty: function (obj) {

        // null and undefined are "empty"
        if (obj == null) return true;

        // Assume if it has a length property with a non-zero value
        // that that property is correct.
        if (obj.length > 0)    return false;
        if (obj.length === 0)  return true;

        // If it isn't an object at this point
        // it is empty, but it can't be anything *but* empty
        // Is it empty?  Depends on your application.
        if (typeof obj !== "object") return true;

        // Otherwise, does it have any properties of its own?
        // Note that this doesn't handle
        // toString and valueOf enumeration bugs in IE < 9
        for (var key in obj) {
            if (hasOwnProperty.call(obj, key)) return false;
        }

        return true;
    },
    
    /*
    * Włącza wszystkie opcje poza chronionymi
    */
    makeAllOptionsAvaible: function(){
      var allOptions = variations_form.product_attr_data;
      for( var attrName in allOptions){
        var option = allOptions[ attrName ];
        
        //sprawdzanie czy atrybut zawiera tablicę dostępnych wartości
        if( typeof option == 'object' &&            
            ( typeof option.length == 'number' || 
                (typeof option.length === 'undefined' && typeof this.getLength( option ) == 'number' )
            )
          ){
          
          for(var i=0; i<option.length; i++ ){
            if( typeof option[ i ].unavaible !== 'undefined' ){
              var protected = false;
              /*
              * sprwdzanie czy wartosc nie jest chroniona przez jakis atrybut
              */
              for( var p in option[ i ].protector){
                
                if( typeof option[ i ].protector[p] === 'object' ){
                  
                  var pat = new RegExp(variations_form.variation_data[p]);
                  if( pat.test(option[ i ].protector[p].join('|')) ){
                    protected = true;
                  }
                  else {                  
                    delete option[ i ].protector[p]; 
                    if( this.isObjEmpty( option[ i ].protector ) ){
                      delete option[ i ].protector;
                    }
                    option[ i ].unavaible = false;
                    continue;
                  }
                } else {
                  
                  if( variations_form.variation_data[p] == option[ i ].protector[p] || variations_form.variation_data[p].toString() === option[ i ].protector[p] ){
                    protected = true;
                  } else {                  
                    delete option[ i ].protector[p];    
                    if( this.isObjEmpty( option[ i ].protector ) ){
                      delete option[ i ].protector;
                    }
                  }                  
                }  
              }
              
              if( !protected ){
                option[ i ].unavaible = false;  
              }
            }
          }          
        }
      }
      
      variations_form.input_settings = Object.assign({}, variations_form.input_settings);
      
    },
    
    /*
    * Znajduje pierwsza dostępną wartośc w atrybucie
    */
    getAvaible: function( attr_name ){
      var target = variations_form.product_attr_data['attribute_'+attr_name];
      for(var i in target){
        if( !target[i].unavaible ){
          return target[i].slug;
        }        
      }      
    },
    
    
    /*
    * Włącza opcje
    */
    makeEnabled: function( attrName, valueSlug ){
      variations_form.input_settings[ attrName ].disabled = 'false';
    },
    
    /*
    * Wyłącza opcje
    */
    makeDisabled: function( attrName, valueSlug ){
      variations_form.input_settings[ attrName ].disabled = 'true';
    },
    
    
    /*
    * Ustawia dostępność wartośći artybutu
    */
    makeUnavailbe: function( attrName, valueSlug, protector ){
      var values = variations_form.product_attr_data[ attrName ];
      var protector = typeof protector !== 'object' ? false : protector;
      
      
      
      //make unavaible object changes
      function ma( value, protector ){
        value.unavaible = true;  
        /*
        if( typeof value.protector === 'object' ){
          console.log( value.protector, value.taxonomy );
          
          var attr_value = typeof value.protector[ protector[0] ] === 'string' ? value.protector[ protector[0] ] : value.protector[ protector[0] ].join('|');
           var pat = new RegExp(attr_value);
            if( pat.test( variations_form.variation_data[ protector[0] ] ) ){
              debugger
            }      
          debugger
          if( attr_value !== variations_form.variation_data[ protector[0] ] ){
            delete value.protector[ protector[0] ];
            value.unavaible = false;
          }
          
        }
        */
        value.protector = typeof value.protector === 'undefined' ? {} : value.protector;   
        
        if( typeof protector[ 0 ] !== 'undefined' ){
          value.protector[ protector[ 0 ] ] = protector[ 1 ];          
        }        
        //debugger
        //wyłączana opcja jest wybrana
        if( variations_form.variation_data[ 'attribute_' + value.taxonomy ] == value.slug 
          
          && typeof variations_form_validate.getAvaible( value.taxonomy ) !== 'undefined'
          
          ){          
          variations_form.variation_data[ 'attribute_' + value.taxonomy ] = variations_form_validate.getAvaible( value.taxonomy );          
        }
        
        return value;
      }      
      
      for(var i in values ){
        var value = values[ i ];
        
        /*
        * mozliwosc wylączania wielu opcji na raz
        */
        if( typeof valueSlug === 'object' ){
          
          for( var j=0; j<valueSlug.length; j++){
            if( value.slug == valueSlug[j] ){
              value = ma( value, protector );  
            }
          }
          
        } else {
          if( value.slug == valueSlug || ( typeof valueSlug === "boolean" && value.slug === valueSlug.toString() ) ){
            value = ma( value, protector );  
          }
        }
        
          
        
        
      }
      
    }
    
    
  },
	
	created : function(){
    
    /*
    * Początkowy stan formularza: ustawianie ograniczen dla defaultowego zestawu atrybutów
    */
    
    if( calc_class == 'ksiazka' || calc_class == 'katalog' ){
		  
      if( variations_form.variation_data.attribute_pa_oprawa === 'oprawa-miekka-klejona' ){      
        this.makeUnavailbe( 'attribute_pa_papier-okladki',
                              [ 'kreda-115g', 'kreda-130g', 'kreda-135g', 'kreda-150g', 'kreda-170g', 'kreda-250g' ], 
                              [ 'attribute_pa_oprawa', 'oprawa-miekka-klejona' ]);  
          
        this.makeUnavailbe( 'attribute_pa_uszlachetnienie-okladki',
                              [ 'folia-blysk-dwustronnie', 'folia-mat-dwustronnie','folia-soft-touch-dwustronnie' ], 
                              [ 'attribute_pa_oprawa', 'oprawa-miekka-klejona' ]);  
        
      }
		}		
   
    
  }
});
