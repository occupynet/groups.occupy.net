<?php
/**
 * Display links to active plugins/extensions settings' pages: Snapshot.
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
 * Snapshot (premium, by Paul Menard (Incsub)/ WPMU DEV)
 *
 * @since 1.2.0
 */
/** Multisite check */
if ( is_multisite() ) {

	$mstba_snapshot_pre_id      = 'networkext';
	$mstba_snapshot_parent      = $networkext_snapshot;
	$mstba_snapshot_parentfirst = $networkextgroup;
	$mstba_snapshot_parentdest  = $networkext_snapshot_destinations;

} else {

	$mstba_snapshot_pre_id      = 'siteext';
	$mstba_snapshot_parent      = $siteext_snapshot;
	$mstba_snapshot_parentfirst = $siteextgroup;
	$mstba_snapshot_parentdest  = $siteext_snapshot_destinations;

}  // end-if is_multisite() check


	/**
	 * List the menu items.
	 *
	 * @since 1.2.0
	 */
	/** All Snapshots */
	$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot' ] = array(
		'parent' => $mstba_snapshot_parentfirst,
		'title'  => __( 'All Snapshots', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=snapshots_edit_panel' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'All Snapshots', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

	/** Add new */
	$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot_add' ] = array(
		'parent' => $mstba_snapshot_parent,
		'title'  => __( 'Add new', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=snapshots_new_panel' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Add new Snapshot for Backup', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

	/** Destinations */
	$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot_destinations' ] = array(
		'parent' => $mstba_snapshot_parent,
		'title'  => __( 'Destinations', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=snapshots_destinations_panel' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Backup Destinations', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
	);

		$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot_destinations_asw' ] = array(
			'parent' => $mstba_snapshot_parentdest,
			'title'  => __( 'Amazon S3', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'admin.php?page=snapshots_destinations_panel&snapshot-action=add&type=aws' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Amazon S3', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot_destinations_dropbox' ] = array(
			'parent' => $mstba_snapshot_parentdest,
			'title'  => __( 'Dropbox', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'admin.php?page=snapshots_destinations_panel&snapshot-action=add&type=dropbox' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Dropbox', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot_destinations_ftp' ] = array(
			'parent' => $mstba_snapshot_parentdest,
			'title'  => __( 'FTP/sFTP', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'admin.php?page=snapshots_destinations_panel&snapshot-action=add&type=ftp' ),
			'meta'   => array( 'target' => '', 'title' => __( 'FTP/sFTP', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot_destinations_greenqloud' ] = array(
			'parent' => $mstba_snapshot_parentdest,
			'title'  => __( 'GreenQloud Storage', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'admin.php?page=snapshots_destinations_panel&snapshot-action=add&type=greenqloud' ),
			'meta'   => array( 'target' => '', 'title' => __( 'GreenQloud Storage', 'multisite-toolbar-additions' ) )
		);

	/** Settings */
	$mstba_tb_items[ $mstba_snapshot_pre_id . '_snapshot_settings' ] = array(
		'parent' => $mstba_snapshot_parent,
		'title'  => __( 'Settings', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=snapshots_settings_panel' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'multisite-toolbar-additions' ) )
	);


/**
 * Hook "New Snapshot" into (site) 'new-content' section as well as our own
 *    'new-content' within Network admin.
 *
 * @since 1.4.0
 */
	$mstba_tb_items[ $mstba_snapshot_pre_id . '_newcontent_snapshot_add' ] = array(
		'parent' => 'new-content',
		'title'  => __( 'New Snapshot', 'multisite-toolbar-additions' ),
		'href'   => network_admin_url( 'admin.php?page=snapshots_new_panel' ),
		'meta'   => array( 'target' => '', 'title' => __( 'New Snapshot', 'multisite-toolbar-additions' ) )
	);