<?php 

class gaad_discount{
	
	public $user;
	public $userID;
	public $userType;
	public $discount;
	public $total;
	
	/**
	* Pobiera typ kleinta (b2c, b2b)
	*
	* @return void
	*/
	function getClientType(  ) {
		global $current_user;		
		$default = 'b2c';		
		$ID = $current_user->ID != 0 ? $current_user->ID : 0;		
			
		if( $ID > 0 ){			
			$current_user_type = get_user_meta( $ID, 'user_type' );
			$current_user_type = is_array( $current_user_type ) ? $current_user_type[0] : null;			
			$current_user_type = !is_null( $current_user_type ) ? $current_user_type : $default;
			
		} else return $default;
		
		return $current_user_type;
	}
	
	/**
	* Pobiera bazę rabatu względem typu kleinta (b2b czy nie)
	*
	* @return void
	*/
	function getDiscountBase( ) {
		$db = new gaad_db();
		$client_type = $this->getClientType();		
		return $db->discounts[ 'base' ][ $client_type ];		
	}
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_discount (  ) {
		global $current_user;
	
		$this->user = $current_user;
		$this->discount_base = $current_user->ID > 0 ? $this->getDiscountBase() : 0;
		
		$total = get_user_meta( $ID, 'user_sale');
		$this->total = is_array( $total ) ? $total[0] : $this->getOrdersTotal();
		
	}

	/**
	* 
	*
	* @return void
	*/
	function getDiscount (  ) {
		$db = new gaad_db();
		$client_type = $this->getClientType();		
		
		foreach( $db->discounts[ 'tables' ][ $client_type ] as $k => $v ){
			$min = $v[0];
			$max = $v[1];			
			if( $this->total >= $min && $this->total <= $max ){				
				break;
			}				
		}
		
		return $this->discount_base + $k;	
	}

	/**
	* 
	*
	* @return void
	*/
	function getOrdersTotal (  ) {
	
		$db = new gaad_db();
		$client_type = $this->getClientType();		
		$days = $db->discounts[ 'interval' ][ $client_type ];
		$from = date( 'Y-m-d', strtotime( "-".( $days - 1 ) . " days" ) );
		
		$customer_orders = get_posts( array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types(),
			'post_status' => array_keys( wc_get_order_statuses() ),
			'date_query' => array(
				array(
					'after'     => $from,
					//'before'  => 'December 31st, 2018',
					'inclusive' => true,
				),
			),
		) );
		
		$total = 0;			
		foreach( $customer_orders as $order_post ){						
			$order = new WC_order( $order_post->ID );
			$total += $order->get_total();			
		}		
		return $total;
	}

}

/*
* Poligon ustawianie usera
*/

/**
* 
*
* @return void
*/
function setMeta (  ) {
	global $current_user;
	$ID = $current_user->ID;
	
	/*
	* Rabatownie produktu
	* Obiekt oblicza ilość % rabatu dla produktu względem ustalonych wytycznych
	* Naztępnie wartość rabatu jest odejmowana od ceny końcowej produktu
	*/
	$discount = new gaad_discount();
	

	
	if( $ID != 0 ){		
		update_user_meta( $ID, 'user_type', 'b2c' );	
		update_user_meta( $ID, 'user_sale',  $discount->getOrdersTotal() );	
	}
	

}


//add_action( 'wp_head', 'setMeta' );




?>