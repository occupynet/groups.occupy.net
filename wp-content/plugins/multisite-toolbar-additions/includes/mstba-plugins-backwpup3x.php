<?php
/**
 * Display links to active plugins/extensions settings' pages: BackWPup v3.x.
 *
 * @package    Multisite Toolbar Additions
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
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
 * BackWPup v3.x - free lite version (free, by Inpsyde GmbH/ Daniel Hüsken)
 *
 * @since 1.3.0
 *
 * @uses  is_plugin_active_for_network()
 */
/** If plugin is network activated, display stuff in 'network_admin' */
if ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'backwpup/backwpup.php' ) ) {

	$mstba_tb_items[ 'networkext_backwpup' ] = array(
		'parent' => $networkextgroup,
		'title'  => __( 'BackWPup Jobs', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpupjobs' ),
		'meta'   => array( 'target' => '', 'title' => __( 'BackWPup Jobs', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'networkext_backwpup_add' ] = array(
		'parent' => $networkext_backwpup,
		'title'  => __( 'Add new Job', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpupeditjob' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Add new Job', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'networkext_backwpup_logs' ] = array(
		'parent' => $networkext_backwpup,
		'title'  => __( 'Log Files', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpuplogs' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Log Files', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'networkext_backwpup_archive' ] = array(
		'parent' => $networkext_backwpup,
		'title'  => __( 'Backups Archive', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpupbackups' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Backups Archive', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'networkext_backwpup_settings' ] = array(
		'parent' => $networkext_backwpup,
		'title'  => __( 'Settings', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpupsettings' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'networkext_backwpup_dashboard' ] = array(
		'parent' => $networkext_backwpup,
		'title'  => __( 'Dashboard', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpup' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Dashboard', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'networkext_backwpup_about' ] = array(
		'parent' => $networkext_backwpup,
		'title'  => __( 'About/ Info', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpupabout' ),
		'meta'   => array( 'target' => '', 'title' => __( 'About &amp; Backup Info', 'multisite-toolbar-additions' ) )
	);

}  // end-if multisite check

	/** Otherwise, if plugin is only site activated, display stuff in a sub site admin */
else {

	$mstba_tb_items[ 'siteext_backwpup' ] = array(
		'parent' => $siteextgroup,
		'title'  => __( 'BackWPup Jobs', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'admin.php?page=backwpupjobs' ),
		'meta'   => array( 'target' => '', 'title' => __( 'BackWPup Jobs', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'siteext_backwpup_add' ] = array(
		'parent' => $siteext_backwpup,
		'title'  => __( 'Add new Job', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'admin.php?page=backwpupeditjob' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Add new Job', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'siteext_backwpup_logs' ] = array(
		'parent' => $siteext_backwpup,
		'title'  => __( 'Log Files', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'admin.php?page=backwpuplogs' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Log Files', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'siteext_backwpup_archive' ] = array(
		'parent' => $siteext_backwpup,
		'title'  => __( 'Backups Archive', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'admin.php?page=backwpupbackups' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Backups Archive', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'siteext_backwpup_settings' ] = array(
		'parent' => $siteext_backwpup,
		'title'  => __( 'Settings', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'admin.php?page=backwpupsettings' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'siteext_backwpup_dashboard' ] = array(
		'parent' => $siteext_backwpup,
		'title'  => __( 'Dashboard', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'admin.php?page=backwpup' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Dashboard', 'multisite-toolbar-additions' ) )
	);

	$mstba_tb_items[ 'siteext_backwpup_about' ] = array(
		'parent' => $siteext_backwpup,
		'title'  => __( 'About/ Info', 'multisite-toolbar-additions' ),
		'href'   => admin_url( 'admin.php?page=backwpupabout' ),
		'meta'   => array( 'target' => '', 'title' => __( 'About &amp; Backup Info', 'multisite-toolbar-additions' ) )
	);

}  // end-if ! multisite check


/**
 * Hook "New Job" into (site) 'new-content' section as well as our own
 *    'new-content' within Network admin.
 *
 * @since 1.4.0
 */
	$mstba_tb_items[ 'newcontent_backwpup_add' ] = array(
		'parent' => 'new-content',
		'title'  => __( 'BackWPup Job', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=backwpupeditjob' ),
		'meta'   => array( 'target' => '', 'title' => __( 'BackWPup Job', 'multisite-toolbar-additions' ) )
	);