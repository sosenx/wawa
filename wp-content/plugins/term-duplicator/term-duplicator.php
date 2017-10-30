<?php
/*
Plugin Name: Term Duplicator
Plugin URI: http://cgd.io
Description:  Duplicate any category, tag, or custom taxonomy term, instantly. 
Version: 1.0.0
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2009-2014 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

class CGD_TermDuplicator {
	function __construct() {
		add_action('init', array($this,'start') );
		add_action('admin_init', array($this, 'handle_duplicate') );
	}
	
	function start() {
		$taxonomies = get_taxonomies();
		
		foreach($taxonomies as $tax) {
			add_filter("{$tax}_row_actions", array($this, 'add_duplicate_link'), 10, 2);
		}
	}
	
	function add_duplicate_link($actions, $term) {
		$duplicate_url = add_query_arg( array('term_duplicator_term' => $term->term_id, '_td_nonce' => wp_create_nonce('duplicate_term'), 'taxonomy' => $term->taxonomy ), admin_url('edit-tags.php') );
		$actions['term_duplicator'] = "<a href='{$duplicate_url}'>" . __('Duplicate', 'term-duplicator') . "</a>";
		
		return $actions;
	}
	
	function handle_duplicate() {
		if ( isset($_REQUEST['_td_nonce']) && check_admin_referer('duplicate_term', '_td_nonce') ) {
			$term_id = $_REQUEST['term_duplicator_term'];
			$term_tax = $_REQUEST['taxonomy'];
			
			$existing_taxonomy_term = get_term($term_id, $term_tax);
			
			$new_term = wp_insert_term("{$existing_taxonomy_term->name} Copy", $term_tax, array('description' => $existing_taxonomy_term->description, 'slug' => "{$existing_taxonomy_term->slug}-copy", 'parent' =>  $existing_taxonomy_term->parent ) );
			
			if ( ! is_wp_error($new_term) ) {
				// add all existing posts to new term
				$posts = get_objects_in_term($term_id, $term_tax);
				
				if ( ! is_wp_error($posts) ) {
					foreach($posts as $post_id) {
						$result = wp_set_post_terms( $post_id, $new_term['term_id'], $term_tax, true );
					}
				}
			}
		}
	}
}

$CGD_TermDuplicator = new CGD_TermDuplicator();