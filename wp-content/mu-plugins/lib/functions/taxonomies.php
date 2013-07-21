<?php
/**
 * Taxonomies
 *
 * This file registers any custom taxonomies
 *
 * @package      Core_Functionality
 * @since        1.0.0
 * @link         https://github.com/billerickson/Core-Functionality
 * @author       Bill Erickson <bill@billerickson.net>
 * @copyright    Copyright (c) 2011, Bill Erickson
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


/**
 * Create Location Taxonomy
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
 */



//Register Minutes Post Type Taxonomies
function be_register_minutes_taxonomy() {
	$labels = array(
		'name' => 'Minutes Categories',
		'singular_name' => 'Minutes Category',
		'search_items' =>  'Search Minutes Categories',
		'all_items' => 'All Minutes Categories',
		'parent_item' => 'Parent Minutes Category',
		'parent_item_colon' => 'Parent Minutes Category:',
		'edit_item' => 'Edit Minutes Category',
		'update_item' => 'Update Minutes Category',
		'add_new_item' => 'Add New Minutes Category',
		'new_item_name' => 'New Minutes Category Name',
		'menu_name' => 'Category'
	); 	

	register_taxonomy( 'minutes-category', array('minutes'), 
		array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'minutes' ),
		)
	);
}
add_action( 'init', 'be_register_minutes_taxonomy' );