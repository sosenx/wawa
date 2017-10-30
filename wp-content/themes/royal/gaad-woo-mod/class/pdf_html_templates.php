<?php 
/*
*Zawiera funkcje zwracające htmlowe szablony 
*/



class pdf_html_template{
  
  /*  
  * Parsowanie danych usera zamawiającego (dane dostawy)
  */
  public function parseUserShipmentMeta( $usermeta ){
    /*
    * Tablicy służy do dowolnego formatowania linii z danymi usera kupujacego towar
    */
    $predefined_usermeta = array(
      array( 'l' => 'Kupujący', 'v' => $usermeta['shipping_first_name']['v'] . ' ' . $usermeta['shipping_last_name']['v'],  ),
      array( 'l' => 'Firma', 'v' => $usermeta['shipping_company']['v']  ),
      array( 'l' => 'Adres', 'v' => 'ul. ' . $usermeta['shipping_address_1']['v'] . '<br>' . $usermeta['shipping_postcode']['v'] . ' ' . $usermeta['shipping_city']['v'] ),
      array( 'l' => 'Email', 'v' => isset( $usermeta['shipping_email']['v'] ) ? $usermeta['shipping_email']['v'] : $usermeta['billing_email']['v'] ),
      array( 'l' => 'Telefon', 'v' => isset( $usermeta['shipping_phone']['v'] ) ? $usermeta['shipping_phone']['v'] : $usermeta['billing_phone']['v'] )
    );
    
    mb_http_output("UTF-8");
    ob_start("mb_output_handler");
    
    ?><table class="kontrahent-data"><?php
    
      foreach( $predefined_usermeta as $k => $v ){
        ?><tr>
            <td><?php echo $v['l']; ?></td>
            <td><?php echo $v['v']; ?></td>
        </tr><?php
      }
    
    ?></table><?php
        
    $html = ob_get_contents();    
    ob_end_clean();
    
    return $html;
  }
  
  /*  
  * Parsowanie danych usera zamawiającego
  */
  public function parseUserMeta( $usermeta ){
    
    /*
    * Tablicy służy do dowolnego formatowania linii z danymi usera kupujacego towar
    */
    $predefined_usermeta = array(
      array( 'l' => 'Nazwa', 'v' => $usermeta['billing_first_name']['v'] . ' ' . $usermeta['billing_last_name']['v'],  ),
      array( 'l' => 'Login', 'v' => $usermeta['nickname']['v'] ),
      
      array( 'l' => 'Firma', 'v' => $usermeta['billing_company']['v']  ),
      array( 'l' => 'Adres', 'v' => 'ul. ' . $usermeta['billing_address_1']['v'] . '<br>' . $usermeta['billing_postcode']['v'] . ' ' . $usermeta['billing_city']['v'] ),
      array( 'l' => 'Email', 'v' => $usermeta['billing_email']['v'] ),
      array( 'l' => 'Telefon', 'v' => $usermeta['billing_phone']['v'] )      
    );
    
    ob_start();
    
    ?><table class="kontrahent-data"><?php
    
      foreach( $predefined_usermeta as $k => $v ){
        ?><tr>
            <td><?php echo $v['l']; ?></td>
            <td><?php echo $v['v']; ?></td>
        </tr><?php
      }
    
    ?></table><?php
        
    $html = ob_get_contents();    
    ob_end_clean();
    
    return $html;
  }
  
  /*
  * parsuje obiek WP_user i zwraca ubrany w html za pomocą parseUserMeta (dane dostawy usera zamawiającego)
  */
  public function getBuyerShipmentData( $usermeta ){
    $get= array(
      "nickname" => 'Login',
      "shipping_first_name" => 'Imię',
      "shipping_last_name" => 'Nazwisko',
      "shipping_company" => 'Firma',
      
      "shipping_phone" => 'Telefon',
      "shipping_email" => 'Email',
      
      "billing_phone" => 'Telefon',
      "billing_email" => 'Email',
      
      "shipping_address_1" => 'Adres',
      "shipping_city" => 'miasto',
      "shipping_postcode" => 'Kod pocztowy'
    );    
    
    $r = array();
    
    foreach( $get as $k => $v ){    
      if( isset( $usermeta[$k] ) ){
        $r[ $k ] = array(
          'l' => $v,
          'v' => $usermeta[$k][0]
        );
      }
    }
    
    return pdf_html_template::parseUserShipmentMeta( $r );
  } 
  
  /*
  * parsuje obiek WP_user i zwraca ubrany w html za pomocą parseUserMeta (dane usera zamawiającego)
  */
  public function getBuyerData( $usermeta ){
    $get= array(
      "nickname" => 'Login',
      "billing_first_name" => 'Imię',
      "billing_last_name" => 'Nazwisko',
      "billing_company" => 'Firma',
      "billing_phone" => 'Telefon',
      "billing_email" => 'Email',
      "billing_address_1" => 'Adres',
      "billing_city" => 'miasto',
      "billing_postcode" => 'Kod pocztowy'
    );    
    
    $r = array();
    
    foreach( $get as $k => $v ){    
      if( isset( $usermeta[$k] ) ){
        $r[ $k ] = array(
          'l' => $v,
          'v' => $usermeta[$k][0]
        );
      }
    }
    
    return pdf_html_template::parseUserMeta( $r );
  }
  
  
  public static function get_product_production_processes_array( $calc_class ){
    $r = array();
    
    switch( $calc_class ){
      case 'wizytowki' : 
        $r = array(
          'file_accept' => 'Sprawdzenie pliku', 
          'imposition' => 'Impozycja', 
          'print' => 'Druk',
          'wrap' => 'Foliowanie', 
          'uv' => 'Lakier UV',
          'cut' => 'Cięcie',
          'pack' => 'Pakowanie',
          'send' => 'Wysyłka', 
        );
        break;   
      default: 
        $r = array(
          'file_accept' => 'Sprawdzenie pliku', 
          'imposition' => 'Impozycja', 
          'print' => 'Druk',          
          'cut' => 'Cięcie',
          'pack' => 'Pakowanie',
          'send' => 'Wysyłka', 
        );
        break;
        
    }
    
    return $r;
  }
  
  
  /*
  * Pobieranie listy atrybutów produktu
  */
  public static function getItemAttributes( $item ){
    $product_id = (int)$item["item_meta"]["_product_id"][0];    
    $calc_class = gaad_ajax::gaad_get_calc_class( $product_id );
    $_filter_data = new gaad_input_settings_filter_data();   
    $brief_filter_data = $_filter_data->brief;          
    $keys_labels = $brief_filter_data[ 'keys_labels' ];
    $r = array();
    echo '<pre>'; echo var_dump($values_labels); echo '</pre>';
    
    
    if( isset( $item ) ){
      foreach( $item as $k => $v ){

        if( preg_match( '/^pa_/', $k ) ){
          $option_value_label = isset( $brief_filter_data[$k][$v] ) ? $brief_filter_data[$k][$v] : $v;
          $key_label = isset( $keys_labels[ $k ] ) ? $keys_labels[ $k ] : $k;
          
          /*
          * Tutaj ewentualne filtrowanie atrybutów, nie wszystkie będą potrzebne produkcji
          */
          $r[ $key_label ] = $option_value_label;
          
        }
      }          
    }
    return $r;
  }
  /*
  * footer karty produktu
  */
  public static function product_brief_page_footer( $item, $css = NULL ){
    $product_id = (int)$item["item_meta"]["_product_id"][0];    
    $calc_class = gaad_ajax::gaad_get_calc_class( $product_id );
    $product_production_processes = pdf_html_template::get_product_production_processes_array( $calc_class );
    
    ob_start();
    
    ?>
    
    <table class="production-process">
      <tr>
        <td colspan="4" ><h3>Procesy technologiczne</h3></td>        
      </tr>
      
      <tr>
        <td>Proces</td>
        <td>Wykoanwca</td>
        <td>Data</td>
        <td>Podpis</td>
      </tr>
      
      <?php foreach( $product_production_processes as $k => $v ) :?>
        <tr>
          <td><?php echo $v; ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>      
      <?php endforeach; ?>
      
    </table>
    
    
<?php

    

    
    $html = ob_get_contents();    
    ob_end_clean();
        
    return is_null($css) ? pdf_html_template::process_data_alan( $html ) : pdf_html_template::process_data_alan( gaad_ajax::emogrifyIt($html, $css) );
  }
  
  /*
  * header karty produktu
  */
  public static function product_brief_page_header( $order, $item, $usermeta, $css = NULL ){    
    $order_id = $order->id;
    $user_id = (int)get_post_meta( $order->id, '_customer_user', true );
   
    ob_start();
    
    ?>
    
    <table class="brief-header-table">
      <tr>
        <td colspan="3" class="header-title" >
          <h3>Karta technologiczna zamówienia</h3>
        </td>
      </tr>  
        
      <tr class="header-tr" >
        <td><img class="wawa-logo" src="http://wawaprint.pl/wp-content/uploads/2017/03/WAWAprint-hdr.png"><br>
<br>
</td>
        <td class="order-id">
          <strong><?php echo $order_id; ?></strong>                    
        </td>
        
        <td class="item-name">          
          <span><?php echo $item['name'] ?></span>
        </td>
        
      </tr>   
      
      <tr>
        <td colspan="3">
          
          <table class="header-info">
            <tr class="header-tr">              
              <td>Kontrahent</td>
              <td>Dostawa</td>
            </tr>   
            <tr class="regular-tr">              
              <td><br><br><?php echo pdf_html_template::getBuyerData( $usermeta ); ?></td>              
              <td><br><br><?php echo pdf_html_template::getBuyerShipmentData( $usermeta ); ?></td>              
            </tr>            
          </table>
          
          
        </td>        
      </tr>   
               
    </table>
    
    
    
<?php
//echo '<pre>'; echo var_dump(  $order ); echo '</pre>';
    
    
    $html = ob_get_contents();    
    ob_end_clean();
        
    return is_null($css) ? pdf_html_template::process_data_alan( $html ) : pdf_html_template::process_data_alan( gaad_ajax::emogrifyIt($html, $css) );
  }  
  
  
  /*
  * Okresla rodzaj produktu p[od kątem wyświetlania listy atruutów na karcie produktu
  * Jeżeli produkt jest prosty, lista jest wypisywana ciurkiem, jeżeli złożony to lista jest podzielona na działy, okładka, blok kolor, cz-b etc.
  */
  function product_brief_product_type( $item ){
    $product_id = (int)$item["item_meta"]["_product_id"][0];    
    $calc_class = gaad_ajax::gaad_get_calc_class( $product_id );
    
    $simple_product = array( 'ulotki', 'wizytowki' );
    $complex_product = array( 'ksiazka', 'katalog' );
    
    if( in_array( $calc_class, $simple_product ) ){
      return 'simple';
    }
    
    if( in_array( $calc_class, $complex_product ) ){
      return 'complex';
    }
    
    return 'unknown';
  }
  
  
  /*
  * Body karty produktu
  */
  public static function product_brief_page( $item, $css = NULL ){
    $attributes = pdf_html_template::getItemAttributes( $item );
    $brief_product_type = pdf_html_template::product_brief_product_type( $item );

    
    ob_start();
    
    ?><br><br><br><table class="product-attributes-list-table">
     
     <tr>
       <td colspan="2" class="header-title">
         <h3>Parametry produktu</h3>
       </td>
     </tr>
     
      <tr class="header-tr">
        <td>Nazwa atrybutu</td>
        <td>Wartość</td>        
      </tr>
    
    <?php

    //wypisywanie atrybutów ciurkiem
    if( $brief_product_type === 'simple' ){
      
      foreach( $attributes as $k => $v ){
        ?><tr class="regular-line">
          <td><?php echo $k; ?></td>
          <td><?php echo $v; ?></td>
        </tr><?php

      }
      
    }
    
    ?></table><br><br><br><?php

    
    $html = ob_get_contents();    
    ob_end_clean();
        
    return is_null($css) ? pdf_html_template::process_data_alan( $html ) : pdf_html_template::process_data_alan( gaad_ajax::emogrifyIt($html, $css) );
  }
  
  
  public static function product_send( $css = NULL ){
    $product = new WC_product( $_POST["product_id"] );
    $breadcrumbs_model = $_POST["breadcrumbs_model"];
    $calc_class = $_POST[ 'calc_class' ];
      $h1_prod_name = $breadcrumbs_model['title'];
      $thumbnail_url = $breadcrumbs_model['thumbnail_url'];
      $summary_ov = $_POST[ "post_data" ];  
    
    if(  class_exists( 'gaad_input_settings_filter' ) && method_exists(  'gaad_input_settings_filter', 'gaad_product_input_settings_filter__'.$calc_class ) ){
      $product_input_settings = call_user_func( 'gaad_input_settings_filter::gaad_product_input_settings_filter__'.$calc_class );      
    }
    
    
    
    
    ob_start();
   // echo '<pre>'; echo var_dump( $product_input_settings ); echo '</pre>';
    ?>
     
     
      <table class="main">
        
        
        <!-- header --> 
        <tr class="header">
          <td class="logo">
            <img class="wawa_logo" src="http://wawaprint.pl/wp-content/uploads/2017/03/WAWAprint-hdr.png" alt="Twoja Drukarnia Internetowa">
          </td>
          <td class="addr">
            <p>
              <strong>www.wawaprint.pl</strong><br>
              Mazowieckie Centrum Poligrafii <br>
              ul. Słoneczna 3c, 05-270 Marki<br>              
              tel.: +48 22 889 00 61
            </p>                       
          </td>
        </tr>
        <!-- margin-->         
        <tr>
          <td colspan="2"><br></td>
        </tr>        
        <!-- h1-->         
        <tr>
          <td colspan="2">
            <span class="h1"><?php echo $h1_prod_name; ?></span>
            <span class="excerpt"><?php echo $product->post->post_excerpt; ?></span>
          </td>
        </tr>                
        <!-- body -->         
        <tr class="body">
          <td class="left">
            <div class="section-1">
              <img src="<?php echo $thumbnail_url; ?>" class="product_thumbnail" >              
            </div>            
          </td>
          <td class="right">
            <table class="attr-table">
              <?php 
                foreach( $summary_ov as $attr => $val ){ ?>
                  <?php if( strlen(  $product_input_settings[ $attr ][ 'labels' ]['l'] ) > 0
                          && pdf_html_template::parseValue( $val ) != 'Nie'
                          && strlen( pdf_html_template::parseValue( $val ) )  > 0
                          ) :  ?>
                  <tr>
                                    
                      <td class="attr-name"><?php echo  $product_input_settings[ $attr ][ 'labels' ]['l']; ?></td>
                      <td class="attr-value"><?php echo !is_string($product_input_settings[ $attr ][ 'labels' ][ 'options' ][ $val ]) ? 
                          pdf_html_template::parseValue( $val )
                          :
                          $product_input_settings[ $attr ][ 'labels' ][ 'options' ][ $val ];
                                                   
                        ?></td>
                    
                  </tr>
                  <?php endif; ?>
              <?php } ?>
            </table>
          </td>
        </tr>
        
        
        <!-- margin-->
        <tr>
          <td colspan="2"><br><br></td>
        </tr>
        
        
        <tr class="after-body">
          <td class="left"></td>
          <td class="right">
            
            <?php echo pdf_html_template::parsePriceBlock(); ?>
            
          </td>
          
        </tr>
        
        <!-- margin-->
        <tr>
          <td colspan="2"><br><br><br><br></td>
        </tr>
        
        <!-- margin-->
        <tr class="extended-prices-title">
          <td colspan="2"><h3>Polecamy również inne nakłady tej konfiguracji.</h3></td>
        </tr>
        
        
        
        <!-- inne naklady -->
        <tr class="extended-prices">          
             
          <td class="margin"></td>
          <td class="center">
            
            <?php echo pdf_html_template::parseOtherPricesBlock(); ?>
          </td>          
        </tr>
        
        
        <!-- margin-->
        <tr>
          <td colspan="2"><br><br></td>
        </tr>
        
        <!-- footer-->
        <tr>
          <td colspan="2">
            
            <a class="go-to-btn" href="<?php echo $_POST['origin'] ?>">Przejdź do strony produktu</a>
            
          </td>
        </tr>
        
        
      </table>
    <?php


    $html = ob_get_contents();    
    ob_end_clean();
        
    return is_null($css) ? pdf_html_template::process_data_alan( $html ) : pdf_html_template::process_data_alan( gaad_ajax::emogrifyIt($html, $css) );
  }
  
  static function parseOtherPricesBlock(){
    $q = $_POST[ 'q' ];
    $max = count( $q );
    
    
    ?><table class="extended">
      
        <tr class="header">          
          <td class="lp"><p>Nakład</p></td>
          <td class="std"><p>Standard</p></td>
          <?php if( pdf_html_template::expressAllowed() ) : ?><td class="exp"><p>Express</p></td><?php endif; ?>
        </tr>
     
     <?php
      
      for( $i=0; $i<$max; $i++  ){
        ?><tr class="ext-line">
          
          <td class="lp"><p><?php echo trim( $q[ $i ][ 'standard' ][ 'quantity' ] ); ?></p></td>
          <td class="std"><p><?php echo trim( $q[ $i ][ 'standard' ][ 'price' ] ); ?></p></td>
      
          <?php if( pdf_html_template::expressAllowed() ) : ?>
              <td class="exp"><p><?php echo trim( $q[ $i ][ 'express' ][ 'price' ] ); ?></p></td>
          <?php endif; ?>    
      
        </tr><?php

      }
    
    ?>
</table>


<?php
    
  }
  
  static function parsePriceBlock( ){    
    $vat = strlen( $_POST[ 'post_data' ][ 'attribute_pa_numer-isbnissn' ] ) == 0 ? 1.23 : 1.05;
    $net_price = $_POST['price']['price'];
    $quantity = $_POST['price']['quantity'];
    $gro_price = round( $net_price * $vat, 2);
    $piece_price = round( $gro_price / $quantity, 2);    
  ?>
  
  <table class="price">
    <tr>
      <td class="total">       
        Cena netto:
      </td>
      <td class="value">       
        <?php echo $net_price; ?>zł
      </td>      
    </tr>
    
    <tr>
      <td class="total">       
        Cena brutto:
      </td>
      <td class="value">       
        <?php echo $gro_price; ?>zł
      </td>      
    </tr>
    
    <tr>
      <td class="total">       
        Cena brutto (1 szt):
      </td>
      <td class="value">       
        <?php echo $piece_price; ?>zł 
      </td>      
    </tr>
    
    
    
  </table>
  
<?php

    
  }
  
  static function parseValue( $val ){
    $val = str_replace( '-szt', ' szt', $val );
    
    
    if( $val === 'true' || $val === true ){
      $val = "Tak";
    } 
    if( $val === 'false' || $val === false ){
      $val = "Nie";
    }
    return $val;
  }
  
  
  /*
  * Html minimize
  */
  public static function process_data_alan($text) {
    $re = '%# Collapse ws everywhere but in blacklisted elements.
        (?>             # Match all whitespans other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          (?:           # Begin (unnecessary) group.
            (?:         # Zero or more of...
              [^<]++    # Either one or more non-"<"
            | <         # or a < starting a non-blacklist tag.
              (?!/?(?:textarea|pre)\b)
            )*+         # (This could be "unroll-the-loop"ified.)
          )             # End (unnecessary) group.
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %ix';
    $text = preg_replace($re, "", $text);
    return $text;
  }
  
  public static function expressAllowed(){
    /*
    Brak możliwości ekspresowego wykonania gdy stosowany jest lakier wybiórczy
    */
    
    if( !preg_match('/brak/', $_POST['post_data']['attribute_pa_lakier-wybiorczy']) ){      
      return false;
    }
    
    return true;
  }  
}




?>