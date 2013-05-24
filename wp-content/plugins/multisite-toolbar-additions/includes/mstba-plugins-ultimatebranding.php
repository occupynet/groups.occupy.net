<?php
/**
 * Display links to active plugins/extensions settings' pages: Ultimate Branding.
 *
 * @package    Multisite Toolbar Additions
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/multisite-toolbar-additions/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.2.0
 */

/**
 * Prevent direct access to this file.
 *
 * @since 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Ultimate Branding (premium, by Incsub Team/ WPMU DEV)
 *
 * @since 1.2.0
 */
/** Multisite check */
if ( is_multisite() ) {

	$mstba_ubranding_pre_id = 'networkext';
	$mstba_ubranding_parent = $networkext_ubranding;
	$mstba_ubranding_parentfirst = $networkextgroup;

} else {

	$mstba_ubranding_pre_id = 'siteext';
	$mstba_ubranding_parent = $siteext_ubranding;
	$mstba_ubranding_parentfirst = $siteextgroup;
	
}  // end-if multisite check

/** List the menu items */
$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding' ] = array(
	'parent' => $mstba_ubranding_parentfirst,
	'title'  => __( 'Ultimate Branding', 'multisite-toolbar-additions' ),
	'href'   => network_admin_url( 'admin.php?page=branding' ),
	'meta'   => array( 'target' => '', 'title' => _x( 'Ultimate Branding Dashboard', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
);

/** Images tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=images' ) ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_images' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'Images', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=images' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Images', 'multisite-toolbar-additions' ) )
	);

}  // end-if images tab check

/** Admin Bar tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=adminbar' ) ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_adminbar' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'Admin Bar', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=adminbar' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Admin Bar', 'multisite-toolbar-additions' ) )
	);

}  // end-if admin bar tab check

/** Widgets tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=widgets' ) ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_widgets' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'Widgets', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=widgets' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Widgets', 'multisite-toolbar-additions' ) )
	);

}  // end-if widgets tab check

/** Help Content tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=help' ) ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_help' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'Help Content', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=help' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Help Content', 'multisite-toolbar-additions' ) )
	);

}  // end-if help content tab check

/** Footer Content tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=footer' ) ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_footer' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'Footer Content', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=footer' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Footer Content', 'multisite-toolbar-additions' ) )
	);

}  // end-if footer content tab check

/** Site Generator tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=sitegenerator' ) && is_multisite() ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_sitegenerator' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'Site Generator', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=sitegenerator' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Site Generator', 'multisite-toolbar-additions' ) )
	);

}  // end-if site generator tab check

/** Text Change tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=textchange' ) ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_textchange' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'Text Change', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=textchange' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Text Change', 'multisite-toolbar-additions' ) )
	);

}  // end-if text change tab check

/** CSS tab */
if ( function_exists( 'ub_has_menu' ) && ub_has_menu( 'branding&amp;tab=css' ) ) {

	$mstba_tb_items[ $mstba_ubranding_pre_id . '_ubranding_css' ] = array(
		'parent' => $mstba_ubranding_parent,
		'title'  => __( 'CSS', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=branding&tab=css' ),
		'meta'   => array( 'target' => '', 'title' => __( 'CSS', 'multisite-toolbar-additions' ) )
	);

}  // end-if css tab check