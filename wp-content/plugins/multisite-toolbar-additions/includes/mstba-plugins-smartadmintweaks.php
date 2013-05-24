<?php
/**
 * Display links to active plugins/extensions settings' pages: Smart Admin Tweaks.
 *
 * @package    Multisite Toolbar Additions
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2013, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/multisite-toolbar-additions/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.3.0
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
 * Smart Admin Tweaks (premium, by Smart Plugins/ Milan Petrovic)
 *
 * @since 1.3.0
 *
 * @uses  is_multisite()
 * @uses  current_user_can()
 */
/** Multisite check */
if ( is_multisite() && current_user_can( 'manage_network' ) ) {

	/** List the network menu items */
	$mstba_tb_items[ 'networkext_smartadmintweaks' ] = array(
		'parent' => $networkextgroup,
		'title'  => __( 'Smart Network Tweaks', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'settings.php?page=smart-admin-tweaks' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Smart Network Admin Tweaks', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

		$mstba_tb_items[ 'networkext_smartadmintweaks_global' ] = array(
			'parent' => $networkext_smartadmintweaks,
			'title'  => __( 'Global Settings', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'settings.php?page=smart-admin-tweaks&tab=global' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Global Settings', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartadmintweaks_about' ] = array(
			'parent' => $networkext_smartadmintweaks,
			'title'  => __( 'About', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'settings.php?page=smart-admin-tweaks&tab=about' ),
			'meta'   => array( 'target' => '', 'title' => __( 'About', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartadmintweaks_support' ] = array(
			'parent' => $networkext_smartadmintweaks,
			'title'  => __( 'Support Forum', 'multisite-toolbar-additions' ),
			'href'   => 'http://www.millan.rs/forums/forum/smart/smart-admin-tweaks/',
			'meta'   => array( 'title' => __( 'Support Forum', 'multisite-toolbar-additions' ) )
		);

}  // end-if is_multisite() & cap check

if ( current_user_can( 'activate_plugins' ) ) {

	/** List the (site) menu items */
	$mstba_tb_items[ 'siteext_smartadmintweaks' ] = array(
		'parent' => $siteextgroup,
		'title'  => __( 'Smart Admin Tweaks', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'options-general.php?page=smart-admin-tweaks' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Smart Site Admin Tweaks', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

		$mstba_tb_items[ 'siteext_smartadmintweaks_site' ] = array(
			'parent' => $siteext_smartadmintweaks,
			'title'  => __( 'Tweaks', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=smart-admin-tweaks&tab=tweaks' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Tweaks', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartadmintweaks_header' ] = array(
			'parent' => $siteext_smartadmintweaks,
			'title'  => __( 'Header', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=smart-admin-tweaks&tab=header' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Header', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartadmintweaks_about' ] = array(
			'parent' => $siteext_smartadmintweaks,
			'title'  => __( 'About', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=smart-admin-tweaks&tab=about' ),
			'meta'   => array( 'target' => '', 'title' => __( 'About', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartadmintweaks_support' ] = array(
			'parent' => $siteext_smartadmintweaks,
			'title'  => __( 'Support Forum', 'multisite-toolbar-additions' ),
			'href'   => 'http://www.millan.rs/forums/forum/smart/smart-admin-tweaks/',
			'meta'   => array( 'title' => __( 'Support Forum', 'multisite-toolbar-additions' ) )
		);

}  // end-if cap check