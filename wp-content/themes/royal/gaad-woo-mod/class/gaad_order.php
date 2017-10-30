<?php
class gaad_order extends WC_order{
		
		/**
		* 
		*
		* @return void
		*/
		function __construct ( $order_id ) {
			parent::__construct( $order_id );
			
		}
		
		
		/**
		* 
		*
		* @return void
		*/
		function get_payment_status_data ( ) {
		
			global $wpdb;
			$active_order = get_user_meta( get_current_user_id(), 'active-order-id', true );
			
			$sql = "SELECT `id` FROM `wp_gaad_p24` WHERE `post_id` = {$active_order} ORDER BY `id`";

			$results = $wpdb->get_results( $sql, ARRAY_A );
			if( !empty( $results ) ){
				return $results[0];
			}
			
			return array( 'id' => false );
		
		 }
		
		
		/**
		* 
		*
		* @return void
		*/
		function create_verify_p24_sign ( $data ) { 
			$db = new gaad_db();
			$p24_crc = $db->payments['p24']['CRC'];
			$p24_amount= $data["p24_amount"];
			$p24_currency = $data["p24_currency"];
			$p24_merchant_id = $data["p24_merchant_id"];
			$p24_order_id= $data["p24_order_id"];
			$p24_pos_id = $data["p24_pos_id"];
			$p24_session_id= $data["p24_session_id"];
			 
			return md5( $p24_session_id."|".$p24_order_id."|".$p24_amount."|".$p24_currency."|".$p24_crc);		
		}
		
		
		/**
		* 
		* 
		* @return void
		*/
		function create_p24_sign ( $p24_session_id, $order ) {
			$db = new gaad_db();
			$p24_merchant_id = $db->payments['p24']['p24_merchant_id'];
			$p24_pos_id = $db->payments['p24']['p24_pos_id'];
			$p24_currency = $db->payments['p24']['p24_currency'];
			$p24_crc = $db->payments['p24']['CRC'];
						
			$shipment_total = $order->get_shipment_total( 'shipping' );
			$p24_amount = (int)( ( $order->get_total() + $shipment_total['total']['total'] ) * 100);
					
			return md5( $p24_session_id.'|'.$p24_merchant_id.'|'.$p24_amount.'|'.$p24_currency.'|'.$p24_crc );
		}
		
		/**
		* 
		*
		* @return void
		*/
		function p24_get_total_price (  ) {
			return 1;
		}
		
		function parseAttributesTerms( $terms ){
			$parsed = array();
			foreach( $terms as $k => $v ){
				$parsed[ $v->slug ] = $v->name; 
				
			}
			return $parsed;
			
		}
		
		
		
		/**
		* Zbiera informacje do podsumowania zamówienia
		*
		* @return void
		*/
		function get_totals (  ) {
			$db = new gaad_db();
			
			$r = array();
				$r[ 'total' ] = $this->get_total();
				$r[ 'total_tax' ] = $this->get_total_tax();
				$r[ 'total_net' ] = $r[ 'total' ] - $r[ 'total_tax' ];
				$r[ 'total_shipping' ] = $this->get_shipment_total( 'shipping' );
				$r[ 'min_file_status' ] = $db->file_uploads[ 'file_min_status_to_place_order' ];
				
			return $r;
		}
		
		/**
		* 
		*
		* @return void
		*/
		function get_shipment_total ( ) { 
			$items = parent::get_items();
			$ship_class = array();
			$ship_class_items = array();
			$r = array();
			$db = new gaad_db();
			foreach( $items as $k => $v ){
				$product = new WC_product( $v["item_meta"]["_product_id"][0] );
				$shipping_class_id = $product->get_shipping_class_id();
				$shipping_class = $product->get_shipping_class();
				if( !in_array( $shipping_class, $ship_class ) ){
					$ship_class[] = $shipping_class;
					$ship_class_items[ $shipping_class] = 1;
					} else{
						$ship_class_items[ $shipping_class] += 1;
					}
			}
			
			/*
			* Obliczanie kosztów transportu klas towarow z 'koszyka'
			*/
			
			$max = count( $ship_class );
			for( $i=0; $i<$max; $i++ ){
				$class = $ship_class[ $i ];
				$costs = $db->shipment[ $class ];
				
				$class_cost = $costs[ 'cost' ];
				$class_cost_tax = $costs[ 'tax' ];
				
				if( $costs['calc_cost'] == 'per_item' ){
					$class_cost = $class_cost * $ship_class_items[ $class ];
					$class_cost_tax = $class_cost_tax * $ship_class_items[ $class ];
				}
				$r[ $class ] = array(
					'cost' => $class_cost, 
					'tax' => $class_cost_tax, 
					'total' => $class_cost + $class_cost_tax, 
					'calc_cost' => $costs['calc_cost'], 
				);				
			};
			
			/*
			* Obliczanie całkowitych kosztow transportu zamówienia
			*/
			$total = array(
				'cost' => 0, 
				'tax' => 0, 
				'total' => 0
			);
			foreach( $r as $class => $cost ){
				$total[ 'cost' ] = $total[ 'cost' ] + $cost[ 'cost' ];
				$total[ 'tax' ] = $total[ 'tax' ] + $cost[ 'tax' ];
				$total[ 'total' ] = $total[ 'cost' ] + $total[ 'tax' ];				
			}
			
			$order_total = $this->get_total(); 
			
			/*
			* Darmowa wysyłka
			*/
			if( $order_total >= $db->shipment['free_shipment_from'] ){
				$total = array(
					'cost' => 0, 
					'tax' => 0, 
					'total' => 0,
					'free_shipment' => true, 
				);
			}
			$r[ 'total' ] = $total;
			
			return $r;
		}
		
		function get_items(){
			$items = parent::get_items();
			
			foreach( $items as $k => $v ){
				$product = new WC_product( $v["item_meta"]["_product_id"][0] );
				$attributes = $product->get_attributes();
				
				
				foreach( $attributes as $k2 => $v2 ){
					$attributes[$k2]['label'] = wc_attribute_label($k2);
					
					$attributes[$k2]['terms'] = $this->parseAttributesTerms( wp_get_post_terms( $product->get_id(), $k2, 'all' ) );
				}
				
				$items[$k]['labels']['attributes'] = $attributes;
				
				
				
				
				
			}
			
			
			
			
			
			


			
			
			return $items;
		}
		
		
	}
	?>