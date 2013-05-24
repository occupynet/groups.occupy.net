<?php
/**
 * Display links to active plugins/extensions settings' pages: Smart Cleanup Tools.
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
 * Smart Cleanup Tools (premium, by Smart Plugins/ Milan Petrovic)
 *
 * @since 1.3.0
 *
 * @uses  is_multisite()
 * @uses  current_user_can()
 */
/** Multisite check */
if ( is_multisite() && current_user_can( 'manage_network' ) ) {

	/** List the network menu items */
	$mstba_tb_items[ 'networkext_smartcleanuptools' ] = array(
		'parent' => $networkextgroup,
		'title'  => __( 'Smart Network Cleanup', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Smart Network Cleanup Tools', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

		$mstba_tb_items[ 'networkext_smartcleanuptools_cleanup' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'Cleanup', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools&tab=cleanup' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Cleanup', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartcleanuptools_reset' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'Reset', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools&tab=reset' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Reset', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartcleanuptools_scheduler' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'Scheduler', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools&tab=scheduler' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Scheduler', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartcleanuptools_settings' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'Settings', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools&tab=settings' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartcleanuptools_statistics' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'Statistics', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools&tab=statistics' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Statistics', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartcleanuptools_logs' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'Logs', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools&tab=log' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Logs', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartcleanuptools_about' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'About', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=smart-cleanup-tools&tab=about' ),
			'meta'   => array( 'target' => '', 'title' => __( 'About', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_smartcleanuptools_support' ] = array(
			'parent' => $networkext_smartcleanuptools,
			'title'  => __( 'Support Forum', 'multisite-toolbar-additions' ),
			'href'   => 'http://www.millan.rs/forums/forum/smart/smart-cleanup-tools/',
			'meta'   => array( 'title' => __( 'Support Forum', 'multisite-toolbar-additions' ) )
		);

}  // end-if is_multisite() & cap check

if ( current_user_can( 'activate_plugins' ) ) {

	/** List the (site) menu items */
	$mstba_tb_items[ 'siteext_smartcleanuptools' ] = array(
		'parent' => $siteextgroup,
		'title'  => __( 'Smart Site Cleanup', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'index.php?page=smart-cleanup-tools' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Smart Site Cleanup Tools', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

		$mstba_tb_items[ 'siteext_smartcleanuptools_cleanup' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'Cleanup', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=smart-cleanup-tools&tab=cleanup' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Cleanup', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartcleanuptools_reset' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'Reset', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=smart-cleanup-tools&tab=reset' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Reset', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartcleanuptools_scheduler' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'Scheduler', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=smart-cleanup-tools&tab=scheduler' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Scheduler', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartcleanuptools_settings' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'Settings', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=smart-cleanup-tools&tab=settings' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartcleanuptools_statistics' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'Statistics', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=smart-cleanup-tools&tab=statistics' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Statistics', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartcleanuptools_logs' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'Logs', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=smart-cleanup-tools&tab=logs' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Logs', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartcleanuptools_about' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'About', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=smart-cleanup-tools&tab=about' ),
			'meta'   => array( 'target' => '', 'title' => __( 'About', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_smartcleanuptools_support' ] = array(
			'parent' => $siteext_smartcleanuptools,
			'title'  => __( 'Support Forum', 'multisite-toolbar-additions' ),
			'href'   => 'http://www.millan.rs/forums/forum/smart/smart-cleanup-tools/',
			'meta'   => array( 'title' => __( 'Support Forum', 'multisite-toolbar-additions' ) )
		);

}  // end-if cap check