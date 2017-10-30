<?php 

class gaad_allegro_api{
  
  
  private $clientID = '36880b31-5af3-48e9-9c86-77bc514b7979';
  private $clientSecret = 'VUnv8uocozPOLM1TBPwPN4QoddzoYRuRyYwIs1gVIY9HBziXRT4hEA6W1Haj1kSG';
  private $APIKey = 'eyJjbGllbnRJZCI6IjM2ODgwYjMxLTVhZjMtNDhlOS05Yzg2LTc3YmM1MTRiNzk3OSJ9.B1PyBk0_xwbmiYqCQ5ZOrXUNf7xxOxNyFDI6Al_JY84=';
  private $token = false;
  
  
  
  
  
  private $SESSION;
  
  private $LOGIN = 'MCP-HUNK';
 /* 
  private $LINK = "https://webapi.allegro.pl/service.php?wsdl"; //nowy wsdl  
  private $PASSWORD = 'W3kNuH!!';
  private $KEY = '70fd2de8';*/
  
  private $LINK = "https://webapi.allegro.pl.webapisandbox.pl/service.php?wsdl"; //nowy wsdl  
  private $PASSWORD = '8421d4cec0Bdc474';
  private $KEY = 's8421d4c';/**/
  private $WEBAPI_USER_ENCODED_PASSWORD = '';  
  private $COUNTRY = 1;
  private $SYSVAR = 1;
  
  private $client;
  
  
  
  
  
  public function requestToken(){
    if( isset( $_GET['code'] ) ){
      
      
      $curl_post_data = array(
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        'api-key' => $this->APIKey, 
        'redirect_uri' => 'http://wawaprint.pl/produkt/wizytowki-skladane/', 
      );

      $service_url = 'https://ssl.allegro.pl/auth/oauth/token';
      $curl = curl_init($service_url);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl, CURLOPT_USERPWD, $this->clientID.':'.$this->clientSecret ); //Your credentials goes here
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate

      $this->token = json_decode( curl_exec($curl) );
      
      $_SESSION['wawa-allegro-token'] = $this->token;
     
      curl_close($curl);
      return $this->token;            
    }
    
  }
  
  
  public function loginButton(){
    ?>
    <a style="background-color: orange;padding: 1rem; color:white" href="https://ssl.allegro.pl/auth/oauth/authorize?response_type=code&client_id=<?php echo $this->clientID; ?>&api-key=<?php echo $this->APIKey; ?>&redirect_uri=http://wawaprint.pl/produkt/wizytowki-skladane/">zaloguj z allegro</a>
    <?php
  }
  
  public function __construct(){
    $this->WEBAPI_USER_ENCODED_PASSWORD = base64_encode(hash('sha256', $this->PASSWORD, true));
    $this->SESSION = $this->doLogin();
    echo '<pre>'; echo var_dump($this->SESSION); echo '</pre>';
  }
    
  
    
  public function getSession(){
    return $this->SESSION->sessionHandlePart;
  }
   
  public function getToken(){
    return $this->token;
  }
  
  
  
  /*
  * autoryzacja w webapi
  */
  public function doLogin(){
    try {
      $this->client = new SoapClient( $this->LINK );
      $version_params = array(
        'sysvar' => $this->SYSVAR,
        'countryId' => $this->COUNTRY,
        'webapiKey' => $this->KEY
      );
      $version = (array)($this->client->doQuerySysStatus($version_params));
      $request = array(
        'userLogin' => $this->LOGIN,
        //'userPassword' => $this->PASSWORD,
        'userHashPassword' => $this->WEBAPI_USER_ENCODED_PASSWORD,
        'countryCode' => $this->COUNTRY,
        'webapiKey' => $this->KEY,
        'localVersion' => $version['verKey']
      );
      $session = $this->client->doLoginEnc($request);
      $_SESSION['wawa-allegro-session'] = $session;
      
      return $session;
    }
    catch(SoapFault $error) {
          return 'Błąd '. $error->faultcode. ': '. $error->faultstring;
    }
    
  }
  
  
  
}


?>