<?php



function gaad_show_product_order( $columns ){

   //remove column
   unset( $columns['price'] );
   unset( $columns['product_tag'] );
   unset( $columns['product_type'] );
   unset( $columns['taxonomy-brand'] );
   unset( $columns['is_in_stock'] );


   return $columns;
}
add_filter( 'manage_edit-product_columns', 'gaad_show_product_order',15 );


/* zmiana wartosci pola w kolumnnie is_in_stock
add_action( 'manage_product_posts_custom_column', 'wpso23858236_product_column_is_in_stock', 10, 2 );

function wpso23858236_product_column_is_in_stock( $column, $postid ) {
    echo '<pre>'; echo var_dump($column); echo '</pre>';
}
*/

/* 
* Dodanie nazwy używanego przez produkt kalkulatora jako klasy body
* Stworzone by regulowac wyświetlanie komunikatów o braku dostęnej wariacji w pliku add-to-cart-variations.min.js
*/

function gaad_woo_mod_body_classes($classes) {
        global $post;
		$product_calc_class_terms = wp_get_object_terms( $post->ID,  'calc_class' );			
		$calc_class = is_array($product_calc_class_terms) ? $product_calc_class_terms[0]->slug : '';
	
        $classes[] = 'gaad-calc-'.$calc_class;
        return $classes;
}
add_filter('body_class', 'gaad_woo_mod_body_classes');


/*
* Modyfikacja admina
*/
if( is_admin() ){
	
	
	/*
	* ROW ACTION: Wyceń warianty
	* Automatyzacja nadawania cen wariantom produktów
	* Ograniczenia: tylko dla typu produkt
	*/
	function set_variables_prices($actions, $page_object) {
		if( $page_object->post_type == 'product' ){

			$href = $_SERVER["SCRIPT_URI"] .'?'. implode( '&', $_SERVER["argv"]) . '#';
			$actions['google_link'] = '<a href="'.$href.'" class="wycen_warianty">' . __('Wyceń warianty') . '</a>';
			
		}

		return $actions;	
	}
	
	add_filter('post_row_actions', 'set_variables_prices', 999, 2);
	
	
	
	
}