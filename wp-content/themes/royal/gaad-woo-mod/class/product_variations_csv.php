<?php 

class product_variations_csv{
  
  public $json = array();
  public $csv;
  public $posts;
  
  
  
  public function __construct(){ 
     if( $this->show() ){
     $wawa_allegro_actions = new wawa_allegro_actions( );
    $AAPI = new gaad_allegro_api();
    
    $tst = $AAPI->getSession();
   
    
   
    
    
    
    
   
      $this->genCSV();
      echo '<pre>'; echo var_dump( $this->genCSV() ); echo '</pre>';
     
     ?>
     <script type="text/javascript">
      var product_variations_csv = <?php echo json_encode( $this->json ); ?>
  </script>
  
<a href="http://wawaprint.pl/<?php echo $this->saveCSV(); ?>">DOWNLOAD CSV</a>
   
   <div id="wawa_allegro"></div>
   <?php
        $AAPI->loginButton();
        $token = $AAPI->requestToken();      
       
    //$this->genCSV();
     // echo '<pre>'; echo var_dump( ); echo '</pre>';
    }
    
  }
    
  public function saveCSV(){
  //  saveCSV
    global $product;
    $fname = $product->id . '.csv';
    file_put_contents( $fname, implode( "\n", $this->csv ) );
    return getcwd().'/'.$fname;
  }
      
  public function genCSV(){
    $this->csv=array();
  global $product;
  $vp = new WC_Product_Variable ( $product->id );
  $children = $vp->get_children();  
  $args = array(
    'post_type' => array('product_variation'), 
    'post__in' => $children,
    'posts_per_page' => -1, 
  );  
  $query = new WP_Query( $args );  
    
    if( count($query->posts) > 0 ){
      $this->posts = $query->posts;
    }
    
    /*
    * Wszystkie atrybuty produktu
    */    
    $_attributes = $vp->get_variation_attributes();
    $_filter_data = new gaad_input_settings_filter_data();   
    $unwanted_attr = array( 'pa_naklad', 'pa_format' );
   
    
    foreach($this->posts as $i => $pdata){
       $pr = new WC_Product_Variable(  $pdata->ID );
      
      
      $attr_slugs = array();
      $attr_array = array();
      $post_attr = array();
      foreach( $_attributes as $k => $v ){        
        $att = get_post_meta($pdata->ID, 'attribute_' . $k, true);
        $post_attr[ 'attribute_' . $k ] = $att;
        if( !in_array($k, $unwanted_attr )  ){
          $attr_slugs[$k] = $_filter_data->brief[$k] [$att];       
        } else {
          
          if( $k == 'pa_naklad' ){
            $att = str_replace( '-szt', ' szt', $att );
          }
          
          $attr_slugs[$k] = $att;       
          
        }
        
     
      }
      // $input_settings = gaad_ajax::gaad_get_product_input_settings( $product->id  );
      // $calc_data = gaad_ajax::gaad_get_calc_data( $product->id, $input_settings );
        
      $tmp45 = array();
      $tmp45['post_data'] = 
      //$_POST = $tmp45['post_data'];
      $_POST['post_data'] = $post_attr;
      
      $_POST['product_id'] = $product->id;
      $_POST['post_data']["attribute_pa_termin-wykonania"] = 'termin-1';
		//echo '<pre>'; echo var_dump($_POST); echo '</pre>';
       
     
      $calculator = new gaad_calc( $product->id );
      $calculation = $calculator->use_it();
      $json = array( 'attributes' => $post_attr );  
			//	$calculation = $calculator->calculate_variant_price();
         
      //echo '<pre>'; echo var_dump($att, $calc_data["variant_production"]['color']["sheets"]); echo '</pre>';
      //echo '<pre>'; echo var_dump( $calculation["variant_production"]["color"] ); echo '</pre>';
      $calculation = $calculation["variant_production"]["color"];
      // $attr_slugs = array( implode(', ', $attr_slugs) );
      $attr_slugs[] = $calculation["pieces_per_sheet"];
      $json["pieces_per_sheet"] = $calculation["pieces_per_sheet"];
      $attr_slugs[] = $calculation["sheets"];
      $json["sheets"] = $calculation["sheets"];
      $attr_slugs[] = $calculation["print_price"];
      $json["print_price"] = $calculation["print_price"];
      $attr_slugs[] = $calculation["sheet_price"];
      $json["sheet_price"] = $calculation["sheet_price"];
      
      $attr_slugs[] = $tmp42 = $calculation["variant_production_cost"] / (int)$calculation["quantity"];
      $json["piece_price"] = $tmp42;
      $attr_slugs[] = str_replace( '.', ',', (string)($tmp42 = $calculation["variant_production_cost"]));
      $json["variant_production_cost"] = $tmp42;
      $attr_slugs[] = str_replace( '.', ',', (string)($tmp42 = $calculation["markup"]));
      $json["markup"] = $tmp42;
      $attr_slugs[] = str_replace( '.', ',', (string)( $tmp42 = round($calculation["_regular_price"], 3) ));
      $json["_regular_price"] = $tmp42;
      $attr_slugs[] = str_replace( '.', ',', (string)( $tmp42 = round($calculation["_regular_price"] * 1.23, 3)));
      $json["_regular_price_after_tax"] = $tmp42;
      $attr_slugs[] = str_replace( '.', ',', (string)( $tmp42 = round($calculation["_regular_price"], 3) - round($calculation["variant_production_cost"], 3)) );
      $json["_profit"] = $tmp42;
      
      
      $this->json[] = $json;
      
      $this->csv[] = implode( ';', $attr_slugs );
     
      
    }
    
    return implode( "\n", $this->csv );
  }
    
    
  public function show(){ 
    $cu = wp_get_current_user(); 
    return $cu->user_login === 'Admingaad';    
  }
  
}



class wawa_allegro_actions extends gaad_allegro_api{
  
  private $clientID = '36880b31-5af3-48e9-9c86-77bc514b7979';
  private $clientSecret = 'VUnv8uocozPOLM1TBPwPN4QoddzoYRuRyYwIs1gVIY9HBziXRT4hEA6W1Haj1kSG';
  private $APIKey = 'eyJjbGllbnRJZCI6IjM2ODgwYjMxLTVhZjMtNDhlOS05Yzg2LTc3YmM1MTRiNzk3OSJ9.B1PyBk0_xwbmiYqCQ5ZOrXUNf7xxOxNyFDI6Al_JY84=';
  
  public function getClientID( ){
    return $this->clientID;    
  }
   public function getAPIKey( ){
    return $this->APIKey;    
  }
  
  public function __construct( ){
  
    
    
  }
  
  
  public function getToken(){
    return $this->token;
  }
  
  public static function addAllegroAuction(){   
    $rrr = new wawa_allegro_actions();
   
    $ch = curl_init();
    $curl_post_data = array(
            'Accept: application/vnd.allegro.public.v1+json;charset=UTF-8',
            'Content-Type: application/vnd.allegro.public.v1+json',
            'Accept-Language:'=>'PL',
            'Authorization: Bearer ' . $_SESSION['wawa-allegro-token']->access_token,
            'Api-Key: '. $rrr->getAPIKey()
    );
    
    echo '<pre>'; echo var_dump( $curl_post_data ); echo '</pre>';
    
   curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_post_data);
    curl_setopt($ch, CURLOPT_URL, "https://allegroapi.io/after-sales-service-conditions/implied-warranties?sellerId=".$rrr->getClientID());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_data);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);

echo '<pre>'; echo var_dump($result); echo '</pre>';

    
    $r = array( 'ClientID' => $rrr->getClientID(), 'session' => $_SESSION['wawa-allegro-session'], 'token' => $_SESSION['wawa-allegro-token'] );    
    wp_send_json( $r );
  }
  
  
  public static function test123(){     
    $r = array( 'data' => $_SESSION['wawa-allegro-token'] );    
    wp_send_json( $r );
  }
  
  
  
  public static function setActions(){
    
    
  }
  
  
}

?>