<?php
/**
 * Display links to active plugins/extensions settings' pages: Smart Options Optimizer.
 *
 * @package    Multisite Toolbar Additions
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2013, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/multisite-toolbar-additions/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.4.0
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
 * Smart Options Optimizer (premium, by Smart Plugins/ Milan Petrovic)
 *
 * @since 1.4.0
 *
 * @uses  is_multisite()
 * @uses  current_user_can()
 */
/** Multisite check */
if ( is_multisite() && current_user_can( 'manage_network' ) ) {

	/** List the network menu items */
	$mstba_tb_items[ 'networkext_smartooptimizer' ] = array(
		'parent' => $networkextgroup,
		'title'  => __( 'Smart Network Options Optimizer', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'settings.php?page=smart-options-optimizer' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Smart Network Options Optimizer - Tracer', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

		$mstba_tb_items[ 'networkext_smartooptimizer_analyze' ] = array(
			'parent' => $networkext_smartooptimizer,
			'title'  => __( 'Analyze', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'settings.php?page=smart-options-optimizer&tab=analyze' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Analyze', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartooptimizer_about' ] = array(
			'parent' => $networkext_smartooptimizer,
			'title'  => __( 'About', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'settings.php?page=smart-options-optimizer&tab=about' ),
			'meta'   => array( 'target' => '', 'title' => __( 'About', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartooptimizer_support' ] = array(
			'parent' => $networkext_smartooptimizer,
			'title'  => __( 'Support Forum', 'multisite-toolbar-additions' ),
			'href'   => 'http://www.millan.rs/forums/forum/smart/smart-options-optimizer/',
			'meta'   => array( 'title' => __( 'Support Forum', 'multisite-toolbar-additions' ) )
		);

}  // end-if is_multisite() & cap check

if ( current_user_can( 'activate_plugins' ) ) {

	/** List the (site) menu items */
	$mstba_tb_items[ 'siteext_smartooptimizer' ] = array(
		'parent' => $siteextgroup,
		'title'  => __( 'Smart Site Options Optimizer', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'options-general.php?page=smart-options-optimizer' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Smarte Site Options Optimizer - Tracer', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

		$mstba_tb_items[ 'siteext_smartooptimizer_analyze' ] = array(
			'parent' => $siteext_smartooptimizer,
			'title'  => __( 'Analyze', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=smart-options-optimizer&tab=analyze' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Analyze', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartooptimizer_autoload' ] = array(
			'parent' => $siteext_smartooptimizer,
			'title'  => __( 'Auto Load', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=smart-options-optimizer&tab=autoload' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Auto Load', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartooptimizer_about' ] = array(
			'parent' => $siteext_smartooptimizer,
			'title'  => __( 'About', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=smart-options-optimizer&tab=about' ),
			'meta'   => array( 'target' => '', 'title' => __( 'About', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartooptimizer_support' ] = array(
			'parent' => $siteext_smartooptimizer,
			'title'  => __( 'Support Forum', 'multisite-toolbar-additions' ),
			'href'   => 'http://www.millan.rs/forums/forum/smart/smart-options-optimizer/',
			'meta'   => array( 'title' => __( 'Support Forum', 'multisite-toolbar-additions' ) )
		);

}  // end-if cap check