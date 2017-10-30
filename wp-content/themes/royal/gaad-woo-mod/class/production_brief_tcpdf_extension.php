<?php

// Extend the TCPDF class to create custom Header and Footer
class production_brief_PDF extends TCPDF {
    
    private $order;

    
  
  
    public function get_css( $filename ) {
      $file_path = '/wp-content/themes/royal/gaad-woo-mod/css/' . $filename;
      if( is_file( $file_path ) ){
        return file_get_contents( $file_path );
      }
    }
  
    public function generate_item_page( $item, $userdata ) {
      
      // set font
      $this->SetFont('dejavusans', '', 9);
      $this->AddPage();
      
      /*
      * Nagłówek karty produkcyjnej
      */
      $html = pdf_html_template::product_brief_page_header( $this->order, $item, $userdata, $this->get_css( 'brief-pdf.css' ) );
      
      /*
      * Parametry produktu
      */
      $html .= pdf_html_template::product_brief_page( $item, $this->get_css( 'brief-pdf.css' ) );
      
      
      /*
      * Tabela proces technologiczny
      */
      $html .= pdf_html_template::product_brief_page_footer( $item, $this->get_css( 'brief-pdf.css' ) );
      
      echo '<pre>'; echo var_dump( $html ); echo '</pre>';
      
      
      // output the Emogrified HTML content 
      $this->writeHTML($html, true, false, true, false, '');
    }
  
    public function generate( ) {
      if( is_object( $this->order ) ){
        $order = $this->order;
        $items = $order->get_items(); //pobranie elementów zamówienia        
        $user_id = (int)get_post_meta( $order->id, '_customer_user', true );
        $usermeta = get_user_meta( $user_id );
        
        foreach( $items as $item ){
          if( is_array( $item ) ) {
            
            $this->generate_item_page( $item, $usermeta ); // generowanie strony pojedyńczego elementu zamówienia, każdy ma swoją kartę produktu
            
          }
            
        }
        
      }
      
    }
      
    public function setOrder( $order ) {
      $this->order = $order;
    }
  
      
    //Page header
    public function Header() {
      //empty header
      
    }  
    
    /*
    * 
    */
    public function save() {
      $db  = new gaad_db();
      chdir( '/' );
      $order_id = $this->getOrderId();
      $owner = $this->getUserData( get_post_meta( $order_id, '_customer_user', true ) );
      $owner_name = $owner->user_meta[ 'first_name' ][0] . '_' . $owner->user_meta[ 'last_name' ][0];
      
      
      $order = $this->order;
      $items = $order->get_items(); //pobranie elementów zamówienia
      $dir = $db->production_brief['dir'];
      
      $order_dir = $dir . '/' . $order_id;
      
      $pdf_path = $order_dir . '/' . $order_id . '-' . count( $items ) . '-'. $owner_name .'.pdf';
      if( !is_dir( $dir ) ){
        mkdir( $dir, 0775 );
      }
      if( !is_dir( $order_dir ) ){
        mkdir( $order_dir, 0775 );
      }
      
      //Close and save PDF document to file
      $this->Output( $pdf_path, 'F');      
    }

    /* w order variacje są tworzone dynamicznie, nie posiadają stałych id
    public function getOrderItemsIds() {
      $order = $this->order;
      $items = $order->get_items(); //pobranie elementów zamówienia
      $r = array();
      foreach( $items as $item ){
          if( is_array( $item ) ) {
            $r[] = $item['id'] ;      
              echo '<pre>'; echo var_dump( $item ); echo '</pre>';
          }
      }
      
      return $r;
    }*/
  
    public function getOrderId() {
      return $this->order->id;
    }

    public function getUserData( $userid ) {
      $r = get_userdata( $userid );
      $r->user_meta = get_user_meta( $userid );
      return $r;
    }

    // Page footer
    public function Footer() {
    }
      
    
  
}

?>