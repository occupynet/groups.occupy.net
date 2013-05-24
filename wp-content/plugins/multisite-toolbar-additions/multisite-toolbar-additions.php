<?php 
/**
 * Main plugin file.
 * This plugin adds a few useful admin links to the WordPress Toolbar / Admin
 *    Bar in Multisite or Network installs.
 *
 * @package   Multisite Toolbar Additions
 * @author    David Decker
 * @link      http://deckerweb.de/twitter
 * @copyright Copyright (c) 2012-2013, David Decker - DECKERWEB
 *
 * Plugin Name: Multisite Toolbar Additions
 * Plugin URI: http://genesisthemes.de/en/wp-plugins/multisite-toolbar-additions/
 * Description: This plugin adds a few useful admin links to the WordPress Toolbar / Admin Bar in Multisite or Network installs.
 * Version: 1.4.0
 * Author: David Decker - DECKERWEB
 * Author URI: http://deckerweb.de/
 * License: GPL-2.0+
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: multisite-toolbar-additions
 * Domain Path: /languages/
 *
 * Copyright (c) 2012-2013 David Decker - DECKERWEB
 *
 *     This file is part of Multisite Toolbar Additions,
 *     a plugin for WordPress.
 *
 *     Multisite Toolbar Additions is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 2 of the License, or (at your option)
 *     any later version.
 *
 *     Multisite Toolbar Additions is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
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
 * Setting constants.
 *
 * @since 1.0.0
 */
/** Plugin directory */
define( 'MSTBA_PLUGIN_DIR', dirname( __FILE__ ) );

/** Plugin base directory */
define( 'MSTBA_PLUGIN_BASEDIR', dirname( plugin_basename( __FILE__ ) ) );


/**
 * Helper function to return filterable hook priority for the super admin nav menu.
 *
 * @since  1.4.0
 *
 * @param  $mstba_super_admin_nav_menu_priority
 *
 * @return int Hook priority for super admin nav menu.
 */
function ddw_mstba_menu_hook_priority() {

	/**
	 * Our default value for priority: '9999' (value was always the default of this plugin).
	 * This way it will be the last item of the left section of the toolbar.
	 * Customizeable via filter hook 'mstba_filter_super_admin_nav_menu_priority'.
	 */
	$mstba_super_admin_nav_menu_priority = 9999;

	/** Make function output filterable */
	return apply_filters( 'mstba_filter_super_admin_nav_menu_priority', absint( $mstba_super_admin_nav_menu_priority ) );

}  // end of function ddw_mstba_menu_hook_priority

	
add_action( 'init', 'ddw_mstba_init' );
/**
 * Setup the plugin.
 *
 * Load the text domain for translation of the plugin.
 * Load admin helper functions - only within 'wp-admin'.
 * Add a WordPress custom menu to the toolbar - only do and display stuff for super admins.
 *
 * @see   ddw_mstba_build_custom_menu()
 *
 * @since 1.0.0
 *
 * @uses  load_textdomain() 		To load translations first from WP_LANG_DIR sub folder.
 * @uses  load_plugin_textdomain() 	To additionally load default translations from plugin folder (default).
 * @uses  is_admin()
 * @uses  current_user_can()
 * @uses  ddw_mstba_menu_hook_priority() 	For (optionally) setting the hook priority via filter.
 *
 * @param string 	$mstba_textdomain
 * @param string 	$locale
 * @param string 	$mstba_wp_lang_dir
 * @param string 	$mstba_lang_dir
 * @param string 	$mstba_menu_string
 */
function ddw_mstba_init() {

	/** Set unique textdomain string */
	$mstba_textdomain = 'multisite-toolbar-additions';

	/** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
	$locale = apply_filters( 'plugin_locale', get_locale(), $mstba_textdomain );

	/** Set filter for WordPress languages directory */
	$mstba_wp_lang_dir = apply_filters(
		'mstba_filter_wp_lang_dir',
		WP_LANG_DIR . '/multisite-toolbar-additions/' . $mstba_textdomain . '-' . $locale . '.mo'
	);

	/** Set filter for plugin's languages directory */
	$mstba_lang_dir = apply_filters( 'mstba_filter_lang_dir', MSTBA_PLUGIN_BASEDIR . '/languages/' );

	/** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
	load_textdomain( $mstba_textdomain, $mstba_wp_lang_dir );

	/** Translations: Secondly, look in plugin's "languages" folder = default */
	load_plugin_textdomain( $mstba_textdomain, FALSE, $mstba_lang_dir );


	/** Include admin helper functions */
	if ( is_admin() ) {

		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-admin.php' );

	}  // end-if is_admin() check

	/** Add "Custom Menu" menus page link to plugin page */
	if ( ( is_admin() || is_network_admin() ) && current_user_can( 'edit_theme_options' ) ) {

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , 'ddw_mstba_custom_menu_link' );
		add_filter( 'network_admin_plugin_action_links_' . plugin_basename( __FILE__ ) , 'ddw_mstba_custom_menu_link' );

	}  // end-if is_admin() & cap check

	/** Define constants and set defaults for removing all or certain sections */
	if ( ! defined( 'MSTBA_DISPLAY_NETWORK_ITEMS' ) ) {
		define( 'MSTBA_DISPLAY_NETWORK_ITEMS', TRUE );
	}

	if ( ! defined( 'MSTBA_DISPLAY_SUBSITE_ITEMS' ) ) {
		define( 'MSTBA_DISPLAY_SUBSITE_ITEMS', TRUE );
	}

	if ( ! defined( 'MSTBA_SUPER_ADMIN_NAV_MENU' ) ) {
		define( 'MSTBA_SUPER_ADMIN_NAV_MENU', TRUE );
	}

	if ( ! defined( 'MSTBA_DISPLAY_NETWORK_EXTEND_GROUP' ) ) {
		define( 'MSTBA_DISPLAY_NETWORK_EXTEND_GROUP', TRUE );
	}

	if ( ! defined( 'MSTBA_DISPLAY_SITE_EXTEND_GROUP' ) ) {
		define( 'MSTBA_DISPLAY_SITE_EXTEND_GROUP', TRUE );
	}

	if ( ! defined( 'MSTBA_DISPLAY_SITE_GROUP' ) ) {
		define( 'MSTBA_DISPLAY_SITE_GROUP', TRUE );
	}

	if ( ! defined( 'MSTBA_DISPLAY_RESOURCES' ) ) {
		define( 'MSTBA_DISPLAY_RESOURCES', TRUE );
	}


	/** Check for Custom Menus support */
	if ( ! current_theme_supports( 'menus' ) ) {
		add_theme_support( 'menus' );
	}

	/** Only register & add additional toolbar menu for super admins */
	if ( ( defined( 'MSTBA_SUPER_ADMIN_NAV_MENU' ) && MSTBA_SUPER_ADMIN_NAV_MENU ) && is_super_admin() ) {

		/** Menu label string */
		$mstba_menu_string = sprintf(
			esc_attr__( '%s Toolbar Menu', 'multisite-toolbar-additions' ),
			( is_multisite() ) ? __( 'Multisite', 'multisite-toolbar-additions' ) : __( 'Site', 'multisite-toolbar-additions' )
		);

		/** Register the menu */
		register_nav_menu( 'mstba_menu', $mstba_menu_string );

		/** Add menu logic/ structure etc. */
		add_action( 'admin_bar_menu', 'ddw_mstba_build_custom_menu', ddw_mstba_menu_hook_priority() );

	}  // end-if constant plus super admin check

}  // end of function ddw_mstba_init


/**
 * Build the custom menu for the toolbar and hook it in.
 *
 * @since  1.0.0
 *
 * @uses   has_nav_menu() To check if menu is registered.
 * @uses   get_nav_menu_locations() To get menu locations.
 * @uses   wp_get_nav_menu_object() To get menu object.
 * @uses   wp_get_nav_menu_items() To get menu args.
 *
 * @param  string 	$mstba_menu_name
 * @param  string 	$mstba_menu_locations
 * @param  string 	$mstba_menu
 * @param  string 	$mstba_menu_items
 * @param  string 	$mstba_menu_item
 * @param  string 	$mstba_menu_args
 *
 * @global mixed $wp_admin_bar
 */
function ddw_mstba_build_custom_menu( $wp_admin_bar ) {

	global $wp_admin_bar;
	
	/** Set unique menu slug */
	$mstba_menu_name = 'mstba_menu';

	/** Only add menu items if location exists and an actual menu is applied to it */
	if ( has_nav_menu( 'mstba_menu' ) ) {

		if ( ( $mstba_menu_locations = get_nav_menu_locations() ) && isset( $mstba_menu_locations[ $mstba_menu_name ] ) ) {

			$mstba_menu_locations = get_nav_menu_locations();
			$mstba_menu = wp_get_nav_menu_object( $mstba_menu_locations[ $mstba_menu_name ] );
			$mstba_menu_items = (array) wp_get_nav_menu_items( $mstba_menu->term_id );

			foreach( $mstba_menu_items as $mstba_menu_item ) {

				/** Retrieve the args from the custom menu */
				$mstba_menu_args = array(
							'id'    => 'mstba_' . $mstba_menu_item->ID,
							'title' => $mstba_menu_item->title,
							'href'  => esc_url_raw( $mstba_menu_item->url ),
							'meta'  => array(
										'target' => $mstba_menu_item->target,
										'title'  => $mstba_menu_item->attr_title,
			                			'class'  => implode( ' ', $mstba_menu_item->classes ),
				) );  // end of array

				/** Check for parent menu items to allow for threaded menus */
				if ( $mstba_menu_item->menu_item_parent ) {
					$mstba_menu_args[ 'parent' ] = 'mstba_' . $mstba_menu_item->menu_item_parent;
				}

				/** Only hook items if the menu is setup for our menu location */
				if ( $mstba_menu_item ) {
					$wp_admin_bar->add_node( $mstba_menu_args );
				}

				unset( $mstba_menu_args );

			}  // end foreach

		}  // end-if menu location check

	}  // end-if check if a 'mstba_menu' menu exists

}  // end of function ddw_mstba_build_custom_menu


add_action( 'wp_before_admin_bar_render', 'ddw_mstba_toolbar_main_site_remove_view_site' );
/**
 * Remove original 'View Site' for main site within Network Admin.
 *
 * @see    ddw_mstba_toolbar_main_site_dashboard()
 *
 * @since  1.2.0
 *
 * @global mixed $wp_admin_bar
 */
function ddw_mstba_toolbar_main_site_remove_view_site() {

	global $wp_admin_bar;

	/** Only for super admins within network_admin & if network our items are enabled */
	if ( ( is_network_admin()
			&& MSTBA_DISPLAY_NETWORK_ITEMS
			&& is_super_admin()
			&& is_user_logged_in()
			&& is_admin_bar_showing()
		) || ( is_admin()
				&& MSTBA_DISPLAY_SUBSITE_ITEMS
				&& is_super_admin()
				&& is_user_logged_in()
				&& is_admin_bar_showing()
		)
	) {

		$wp_admin_bar->remove_menu( 'view-site' );

	}  // end-if network_admin check

	/** Remove original Custom Background / Header */
	if ( is_super_admin()
			&& is_user_logged_in()
			&& is_admin_bar_showing()
	) {

		$wp_admin_bar->remove_menu( 'background' );
		$wp_admin_bar->remove_menu( 'header' );

	}

}  // end of function ddw_mstba_toolbar_main_site_remove_view_site


add_action( 'admin_bar_menu', 'ddw_mstba_toolbar_main_site_dashboard' );
/**
 * Adding 'Dashboard' for main site within Network Admin.
 *
 * @since  1.2.0
 *
 * @uses   WP_Admin_Bar::add_menu()
 *
 * @global mixed $wp_admin_bar
 */
function ddw_mstba_toolbar_main_site_dashboard() {

	global $wp_admin_bar;

	/** Only for super admins within network_admin & if network, our items are enabled */
	if ( is_network_admin()
		&& MSTBA_DISPLAY_NETWORK_ITEMS
		&& is_super_admin()
		&& is_user_logged_in()
		&& is_admin_bar_showing()
	) {

		/** Add 'Dashboard' for main site */
		$wp_admin_bar->add_menu( array(  
			'parent' => 'site-name',  
			'id'     => 'ddw-mstba-main-site-dashboard',  
			'title'  => __( 'Dashboard', 'multisite-toolbar-additions' ),  
			'href'   => admin_url( '/' ),  
			'meta'   => array( 'target' => '', 'title' => _x( 'Dashboard (Main Site)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) ) )  
		);

		/** Re-add 'View Site' item */
		$wp_admin_bar->add_menu( array(  
			'parent' => 'site-name',  
			'id'     => 'ddw-mstba-main-site-view',  
			'title'  => __( 'View Site', 'multisite-toolbar-additions' ),  
			'href'   => get_home_url(),  
			'meta'   => array( 'target' => '_blank', 'title' => _x( 'View Site (Main Site)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) ) )  
		);

	}  // end-if is_network_admin() check

	/** Re-add 'View Site' item */
	if ( ( is_admin() && ! is_network_admin() )
		&& MSTBA_DISPLAY_SUBSITE_ITEMS
		&& is_super_admin()
		&& is_user_logged_in()
		&& is_admin_bar_showing()
	) {

		$wp_admin_bar->add_menu( array(  
			'parent' => 'site-name',  
			'id'     => 'ddw-mstba-view_site',  
			'title'  => __( 'View Website', 'multisite-toolbar-additions' ),  
			'href'   => esc_url( get_home_url( '/' ) ),  
			'meta'   => array( 'target' => '_blank', 'title' => _x( 'View Website', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) ) )  
		);

	}  // end-if is_admin() check

}  // end of function ddw_mstba_toolbar_main_site_dashboard


add_action( 'admin_bar_menu', 'ddw_mstba_toolbar_additions', 99 );
/**
 * Add new menu items to the WordPress Toolbar / Admin Bar.
 * 
 * @since  1.0.0
 *
 * @uses   WP_Admin_Bar::remove_menu()
 * @uses   WP_Admin_Bar::add_menu()
 * @uses   WP_Admin_Bar::add_group()
 *
 * @param  $mstba_prefix
 * @param  $mstba_tb_items
 * @param  $mstba_tb_item
 * @param  $mstba_menu_id
 *
 * @global mixed $wp_admin_bar
 */
function ddw_mstba_toolbar_additions() {

	global $wp_admin_bar;

	/**
	 * Required WordPress cabability to display new toolbar bar entries
	 * Only showing items if toolbar / admin bar is activated and super admin user is logged in!
	 *
	 * @since 1.0.0
	 */
	
	if ( ! is_super_admin()
		|| ! is_user_logged_in()
		|| ! is_admin_bar_showing()
		|| ! MSTBA_DISPLAY_NETWORK_ITEMS	// allows for custom disabling
	) {
		return;
	}


	/** Remove original "Visit Network" menu item (only to re-add later on as last item!) */
	$wp_admin_bar->remove_menu( 'network-admin-v' );

	/** Set unique prefix for toolbar ID */
	$mstba_prefix = 'ddw-mstba-';
	
	/** Create parent menu item references */
	$networkplugins = $mstba_prefix . 'networkplugins';				// sub level: network plugins
	$networkthemes = $mstba_prefix . 'networkthemes';				// sub level: network themes
	$networkextgroup = $mstba_prefix . 'networkextgroup';				// sub level: network extend group ("hook" place)
		$networkext_quickcache = $mstba_prefix . 'networkext_quickcache';	// third level: quick cache (network)
		$networkext_wpsupercache = $mstba_prefix . 'networkext_wpsupercache';	// third level: wp super cache (network)
		$networkext_wppiwik = $mstba_prefix . 'networkext_wppiwik';			// third level: wp-piwik (network)
		$networkext_orgmessagenotifier = $mstba_prefix . 'networkext_orgmessagenotifier';	// third level: o.messg.not (network)
		$networkext_codesnippets = $mstba_prefix . 'networkext_codesnippets';	// third level: code snippets (network)
		$networkext_backwpup = $mstba_prefix . 'networkext_backwpup';		// third level: backwpup (network)
		$networkext_snapshot = $mstba_prefix . 'networkext_snapshot';		// third level: snapshot (network)
		$networkext_snapshot_destinations = $mstba_prefix . 'networkext_snapshot_destinations';	// third level: snapshot dest. (nw.)
		$networkext_ubranding = $mstba_prefix . 'networkext_ubranding';		// third level: ultimate branding (network)
		$networkext_smartadmintweaks = $mstba_prefix . 'networkext_smartadmintweaks';	// third level: smart admin tweaks (network)
		$networkext_smartcleanuptools = $mstba_prefix . 'networkext_smartcleanuptools';	// third level: smart cleanup tools (network)
		$networkext_smartooptimizer = $mstba_prefix . 'networkext_smartooptimizer';	// third level: smart o.optimizer (network)
		$networkext_simplesystinfo = $mstba_prefix . 'networkext_simplesystinfo';	// third level: simple syst.info (network)
		$networkext_hidemywp = $mstba_prefix . 'networkext_hidemywp';		// third level: hide my wp (network)
		$networkext_msrobotstxt = $mstba_prefix . 'networkext_msrobotstxt';		// third level: ms robots.txt (network)
		$networkext_wpmudomainmapping = $mstba_prefix . 'networkext_wpmudomainmapping';	// third level: wpmu domain mapping (network)
		$networkext_wpmigratedbpro = $mstba_prefix . 'networkext_wpmigratedbpro';	// third level: wp migrate db pro (network)
	$siteextgroup = $mstba_prefix . 'siteextgroup';					// sub level: site extend group ("hook" place)
		$siteext_quickcache = $mstba_prefix . 'siteext_quickcache';			// third level: quick cache (site)
		$siteext_wpsupercache = $mstba_prefix . 'siteext_wpsupercache';		// third level: wp super cache (site)
		$siteext_wppiwik = $mstba_prefix . 'siteext_wppiwik';				// third level: wp-piwik (site)
		$siteext_wprcinstaller = $mstba_prefix . 'siteext_wprcinstaller';	// third level: wprc installer
		$siteext_relevanssi = $mstba_prefix . 'siteext_relevanssi';			// third level: relevanssi/premium
		$siteext_codesnippets = $mstba_prefix . 'siteext_codesnippets';		// third level: code snippets (site)
		$siteext_cwwpcsnippets = $mstba_prefix . 'siteext_cwwpcsnippets';	// third level: cwwp code snippets
		$siteext_backwpup = $mstba_prefix . 'siteext_backwpup';				// third level: backwpup (site)
		$siteext_snapshot = $mstba_prefix . 'siteext_snapshot';				// third level: snapshot (site)
		$siteext_snapshot_destinations = $mstba_prefix . 'siteext_snapshot_destinations';	// third level: snapshot dest. (si
		$siteext_ubranding = $mstba_prefix . 'siteext_ubranding';			// third level: ultimate branding (site)
		$siteext_smartadmintweaks = $mstba_prefix . 'siteext_smartadmintweaks';	// third level: smart admin tweaks (site)
		$siteext_smartcleanuptools = $mstba_prefix . 'siteext_smartcleanuptools';	// third level: smart cleanup tools (site)
		$siteext_smartooptimizer = $mstba_prefix . 'siteext_smartooptimizer';	// third level: smart o.optimizer (site)
		$siteext_simplesystinfo = $mstba_prefix . 'siteext_simplesystinfo';	// third level: simple syst.info (site)
		$siteext_rvgoptimizedb = $mstba_prefix . 'siteext_rvgoptimizedb';	// third level: rvg optimize db (site)
		$siteext_hidemywp = $mstba_prefix . 'siteext_hidemywp';				// third level: hide my wp (site)
		$siteext_p3profiler = $mstba_prefix . 'siteext_p3profiler';				// third level: p3 profiler
		$siteext_msrobotstxt = $mstba_prefix . 'siteext_msrobotstxt';		// third level: ms robots.txt (site)
		$siteext_wpmigratedbpro = $mstba_prefix . 'siteext_wpmigratedbpro';	// third level: wp migrate db pro (site)
	$sitegroup = $mstba_prefix . 'sitegroup';						// sub level: site group ("hook" place)
		$widgets = $mstba_prefix . 'widgets';								// third level: widgets
		$navmenus = $mstba_prefix . 'navmenus';								// third level: nav menus
		$mcbase = $mstba_prefix . 'mcbase';									// third level: manage content base item
		$medialibrary = $mstba_prefix . 'medialibrary';						// third level: media library
		$editthemes = $mstba_prefix . 'editthemes';							// third level: edit themes
		$siteplugins = $mstba_prefix . 'siteplugins';						// third level: plugins (site only)
	$addnewgroup = $mstba_prefix . 'addnewgroup';					// sub level: add new group ("hook" place)
		$addnew_plugin = $mstba_prefix . 'addnew_plugin';					// third level: add new plugin
		$addnew_theme = $mstba_prefix . 'addnew_theme';						// third level: add new theme
	$network_addsite = $mstba_prefix . 'network_addsite';					// third level: add new site (network)
	$view_site = $mstba_prefix . 'view_site';						// sub level: view site (site)
	$resources_getinvolved = $mstba_prefix . 'resources_getinvolved';		// third level: resources - get involved


	/**
	 * Display additional network-specific items, load only for Multisite installs.
	 *
	 * @since 1.0.0
	 */
	if ( is_multisite() ) {

		/** Include code part with Multisite items */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-items-multisite.php' );

	}  // end-if is_multisite check


	/**
	 * Display additional site-specific items (as sub level items on subsite ? item)
	 *
	 * @since 1.0.0
	 */
		/** Site Group: Main Entry */
		if ( MSTBA_DISPLAY_SITE_GROUP ) {

			$wp_admin_bar->add_group( array(
				'parent' => 'site-name',
				'id'     => $sitegroup,
			) );

		}  // end-if constant check

		/** Include code part with site group items */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-items-site-group.php' );


	/** "Plugins" items only for non-Multisite installs! */
	if ( ! is_multisite() && current_user_can( 'activate_plugins' ) ) {

		/** Include code part with site plugins items */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-items-site-plugins.php' );

	}  // end-if plugins check


	/**
	 * Display additional site-specific "New Content/ Add New" items (as sub level items on subsite ? item)
	 *
	 * @since 1.3.0
	 */
		/** Show only if "Site Group" is not hidden */
		if ( MSTBA_DISPLAY_SITE_GROUP ) {

			$wp_admin_bar->add_group( array(
				'parent' => 'new-content',
				'id'     => $addnewgroup,
			) );

		}  // end-if constant check

		$mstba_tb_items[ 'addnew_plugin' ] = array(
			'parent' => $addnewgroup,
			'title'  => __( 'Install Plugin', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'plugin-install.php?tab=dashboard' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Install Plugin - Search via WordPress.org', 'multisite-toolbar-additions' ) )
		);

			$mstba_tb_items[ 'addnew_plugin_upload' ] = array(
				'parent' => $addnew_plugin,
				'title'  => __( 'Upload ZIP file', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'plugin-install.php?tab=upload' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Install Plugin - Upload ZIP file', 'multisite-toolbar-additions' ) )
			);

			$mstba_tb_items[ 'addnew_plugin_faves' ] = array(
				'parent' => $addnew_plugin,
				'title'  => __( 'Install Favorites', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'plugin-install.php?tab=favorites' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Install Plugins - Favorites (via WordPress.org)', 'multisite-toolbar-additions' ) )
			);

		$mstba_tb_items[ 'addnew_theme' ] = array(
			'parent' => $addnewgroup,
			'title'  => __( 'Install Theme', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'theme-install.php?tab=dashboard' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Install Theme - Search via WordPress.org', 'multisite-toolbar-additions' ) )
		);

			$mstba_tb_items[ 'addnew_theme_upload' ] = array(
				'parent' => $addnew_theme,
				'title'  => __( 'Upload ZIP file', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'theme-install.php?tab=upload' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Install Theme - Upload ZIP file', 'multisite-toolbar-additions' ) )
			);

	/** Site Extend Group: Main Entry */
	if ( MSTBA_DISPLAY_SITE_EXTEND_GROUP ) {

		$wp_admin_bar->add_group( array(
			'parent' => 'site-name',
			'id'     => $siteextgroup,
		) );

		/** Action Hook 'mstba_custom_network_items' - allows for hooking in other site-specific items */
		do_action( 'mstba_custom_network_items' );

	}  // end-if constant check


	/** Include code part with plugin support items */
	require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins.php' );


	/** Action Hook 'mstba_custom_plugin_items' - allows for hooking in other plugin items */
	do_action( 'mstba_custom_plugin_items' );


	/** Include various external resources items */
	if ( MSTBA_DISPLAY_RESOURCES ) {

		/** Include code part with resources items */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-items-resources.php' );

	}  // end-if constant check


	/** Allow menu items to be filtered, but pass in parent menu item IDs */
	$mstba_tb_items = (array) apply_filters( 'mstba_filter_menu_items', $mstba_tb_items,
									$networkplugins,
									$networkthemes,
									$networkextgroup,
										$networkext_quickcache,
										$networkext_wpsupercache,
										$networkext_wppiwik,
										$networkext_orgmessagenotifier,
										$networkext_codesnippets,
										$networkext_backwpup,
										$networkext_snapshot,
										$networkext_snapshot_destinations,
										$networkext_smartadmintweaks,
										$networkext_smartcleanuptools,
										$networkext_smartooptimizer,
										$networkext_simplesystinfo,
										$networkext_hidemywp,
										$networkext_msrobotstxt,
										$networkext_wpmudomainmapping,
										$networkext_wpmigratedbpro,
									$siteextgroup,
										$siteext_quickcache,
										$siteext_wpsupercache,
										$siteext_wppiwik,
										$siteext_wprcinstaller,
										$siteext_relevanssi,
										$siteext_codesnippets,
										$siteext_cwwpcsnippets,
										$siteext_backwpup,
										$siteext_snapshot,
										$siteext_snapshot_destinations,
										$siteext_smartadmintweaks,
										$siteext_smartcleanuptools,
										$siteext_smartooptimizer,
										$siteext_simplesystinfo,
										$siteext_rvgoptimizedb,
										$siteext_hidemywp,
										$siteext_p3profiler,
										$siteext_msrobotstxt,
										$siteext_wpmigratedbpro,
									$sitegroup,
										$widgets,
										$navmenus,
										$mcbase,
										$medialibrary,
										$editthemes,
										$siteplugins,
									$addnewgroup,
										$addnew_plugin,
										$addnew_theme,
									$view_site,
									$resources_getinvolved
	);  // end of array


	/** Loop through the menu items */
	foreach ( $mstba_tb_items as $mstba_menu_id => $mstba_tb_item ) {
		
		/** Add in the item ID */
		$mstba_tb_item[ 'id' ] = $mstba_prefix . $mstba_menu_id;

		/** Add meta target to each item where it's not already set, so links open in new window/tab */
		if ( ! isset( $mstba_tb_item[ 'meta' ][ 'target' ] ) ) {

			$mstba_tb_item[ 'meta' ][ 'target' ] = '_blank';

		}

		/** Add class to links that open up in a new window/tab */
		if ( '_blank' === $mstba_tb_item[ 'meta' ][ 'target' ] ) {

			if ( ! isset( $mstba_tb_item[ 'meta' ][ 'class' ] ) ) {

				$mstba_tb_item[ 'meta' ][ 'class' ] = '';

			}

			$mstba_tb_item[ 'meta' ][ 'class' ] .= $mstba_prefix . 'mstba-new-tab';

		}  // end-if target check

		/** Add menu items */
		$wp_admin_bar->add_menu( $mstba_tb_item );

	}  // end foreach menu items

}  // end of function ddw_mstba_toolbar_additions


add_action( 'wp_before_admin_bar_render', 'ddw_mstba_toolbar_subsite_items' );
/**
 * Adding subsite items within "My Sites/[Site Name]"
 *
 * @since  1.0.0
 *
 * @uses   $blog To get Site ID.
 * @uses   WP_Admin_Bar::remove_menu()
 * @uses   WP_Admin_Bar::add_menu()
 * @uses   get_admin_url()
 *
 * @param  $mstba_blog_menu_id
 *
 * @global mixed $wp_admin_bar
 */
function ddw_mstba_toolbar_subsite_items() {

	global $wp_admin_bar;

	/**
	 * Required WordPress cabability to display new toolbar bar entries
	 * Only showing items if toolbar / admin bar is activated and super admin user is logged in!
	 *
	 * @since 1.0.0
	 */
	if ( ! is_super_admin()
		|| ! is_user_logged_in()
		|| ! is_admin_bar_showing()
		|| ! MSTBA_DISPLAY_SUBSITE_ITEMS	// allows for custom disabling
	) {
		return;
	}

	/** Adding new items for each subsite */
	foreach ( (array) $wp_admin_bar->user->blogs as $blog ) {

		/** Get ID of subsite/blog */
		$mstba_blog_menu_id = 'blog-' . $blog->userblog_id;

		/** Remove original "Visit Site" menu item (only to re-add later on as last item!) */
		$wp_admin_bar->remove_menu( $mstba_blog_menu_id . '-v' );

		/** Site > Dashboard > Settings */
		$wp_admin_bar->add_menu( array(
			'parent' => $mstba_blog_menu_id . '-d',
			'id'     => $mstba_blog_menu_id . '-mstba_site_settings_old',
			'title'  => __( 'Site Settings', 'multisite-toolbar-additions' ),
			'href'   => get_admin_url( $blog->userblog_id, 'options-general.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Site Settings', 'multisite-toolbar-additions' ) )
		) );

		/** Site > Widgets */
		$wp_admin_bar->add_menu( array(
			'parent' => $mstba_blog_menu_id,
			'id'     => $mstba_blog_menu_id . '-mstba_site_widgets',
			'title'  => __( 'Site Widgets', 'multisite-toolbar-additions' ),
			'href'   => get_admin_url( $blog->userblog_id, 'widgets.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Site Widgets', 'multisite-toolbar-additions' ) )
		) );

		/** Site > Menus */
		$wp_admin_bar->add_menu( array(
			'parent' => $mstba_blog_menu_id,
			'id'     => $mstba_blog_menu_id . '-mstba_site_menus',
			'title'  => __( 'Site Menus', 'multisite-toolbar-additions' ),
			'href'   => get_admin_url( $blog->userblog_id, 'nav-menus.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Site Menus', 'multisite-toolbar-additions' ) )
		) );

			$wp_admin_bar->add_menu( array(
				'parent' => $mstba_blog_menu_id . '-mstba_site_menus',
				'id'     => $mstba_blog_menu_id . '-mstba_site_menu_add',
				'title'  => __( 'Add new Menu', 'multisite-toolbar-additions' ),
				'href'   => get_admin_url( $blog->userblog_id, 'nav-menus.php?action=edit&menu=0' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new Menu', 'multisite-toolbar-additions' ) )
			) );

			/** Add "Menu Locations" item for WordPress 3.6+ */
			if ( function_exists( 'get_post_format_meta' ) ) {
				$wp_admin_bar->add_menu( array(
					'parent' => $mstba_blog_menu_id . '-mstba_site_menus',
					'id'     => $mstba_blog_menu_id . '-mstba_site_menu_locations',
					'title'  => __( 'Menu Locations', 'multisite-toolbar-additions' ),
					'href'   => get_admin_url( $blog->userblog_id, 'nav-menus.php?action=locations' ),
					'meta'   => array( 'target' => '', 'title' => __( 'Menu Locations', 'multisite-toolbar-additions' ) )
				) );
			}

		/** Site > Plugins */
		$wp_admin_bar->add_menu( array(
			'parent' => $mstba_blog_menu_id,
			'id'     => $mstba_blog_menu_id . '-mstba_site_plugins',
			'title'  => __( 'Site Plugins', 'multisite-toolbar-additions' ),
			'href'   => get_admin_url( $blog->userblog_id, 'plugins.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Site Plugins', 'multisite-toolbar-additions' ) )
		) );

		/** Site > Themes */
		$wp_admin_bar->add_menu( array(
			'parent' => $mstba_blog_menu_id,
			'id'     => $mstba_blog_menu_id . '-mstba_site_themes',
			'title'  => __( 'Site Themes', 'multisite-toolbar-additions' ),
			'href'   => get_admin_url( $blog->userblog_id, 'themes.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Site Themes', 'multisite-toolbar-additions' ) )
		) );

			$mstba_current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
			$wp_admin_bar->add_menu( array(
				'parent' => $mstba_blog_menu_id . '-mstba_site_themes',
				'id'     => $mstba_blog_menu_id . '-mstba_site_customizer',
				'title'  => __( 'Theme Customizer', 'multisite-toolbar-additions' ),
				'href'   => is_admin() ? get_admin_url( $blog->userblog_id, 'customize.php' ) : add_query_arg( 'url', urlencode( $mstba_current_url ), wp_customize_url() ),
				'meta'   => array(
									'class'  => ! is_admin() ? 'hide-if-no-customize' : '',
									'target' => '',
									'title'  => __( 'Theme Customizer', 'multisite-toolbar-additions' )
							)
			) );

		/** Site > Settings */
		$wp_admin_bar->add_menu( array(
			'parent' => $mstba_blog_menu_id,
			'id'     => $mstba_blog_menu_id . '-mstba_site_settings',
			'title'  => __( 'Site Settings', 'multisite-toolbar-additions' ),
			'href'   => get_admin_url( $blog->userblog_id, 'options-general.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Site Settings', 'multisite-toolbar-additions' ) )
		) );

		/** Site > Tools */
		$wp_admin_bar->add_menu( array(
			'parent' => $mstba_blog_menu_id,
			'id'     => $mstba_blog_menu_id . '-mstba_site_tools',
			'title'  => __( 'Site Tools', 'multisite-toolbar-additions' ),
			'href'   => get_admin_url( $blog->userblog_id, 'tools.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Site Tools', 'multisite-toolbar-additions' ) )
		) );

		/** Re-add "Visit Site" item as the last one - and opening in blank window/tab */
		$wp_admin_bar->add_menu( array(  
			'parent' => $mstba_blog_menu_id,  
			'id'     => $mstba_blog_menu_id . '-v',  
			'title'  => __( 'Visit Site', 'multisite-toolbar-additions' ),  
			'href'   => get_home_url( $blog->userblog_id, '/' ),  
			'meta'   => array( 'target' => '_blank', 'title' => __( 'Visit Site', 'multisite-toolbar-additions' ) ) )  
		);

	}  // end foreach

}  // end of function ddw_mstba_subsite_items


/**
 * Helper function for custom deactivation of ddw_mstba_network_new_content_helper() items.
 *
 * @since  1.4.0
 *
 * @uses   __return_true() WordPress helper function, to return 'TRUE'.
 *
 * @return bool 
 */
function ddw_mstba_network_new_content_filter() {

	return apply_filters( 'mstba_filter_display_network_new_content', '__return_true' );

}  // end of function ddw_mstba_network_new_content_filter


add_action( 'init', 'ddw_mstba_network_new_content_helper', 0 );
/**
 * Add 'new-content' area within Network admin only for Network specific items.
 *
 * @since 1.4.0
 */
function ddw_mstba_network_new_content_helper() {

	/** Only load the additions for Network admin area and if Toolbar is active */
	if ( is_network_admin()
		&& is_super_admin()
		&& is_user_logged_in()
		&& is_admin_bar_showing()
		&& MSTBA_DISPLAY_NETWORK_ITEMS				// allows for custom disabling
		&& ddw_mstba_network_new_content_filter()	// allows for custom disabling
	) {

		/** Include code part with plugin support items */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-network-new-content.php' );

	}  // end-if is_network_admin() plus Toolbar checks

}  // end of function ddw_mstba_network_new_content_helper


add_action( 'wp_head', 'ddw_mstba_admin_style' );
add_action( 'admin_head', 'ddw_mstba_admin_style' );
/**
 * Add the styles for new WordPress Toolbar / Admin Bar entry
 * 
 * @since 1.3.0
 *
 * @uses  is_admin_bar_showing()
 * @uses  is_user_logged_in()
 * @uses  MSTBA_DISPLAY_NETWORK_EXTEND_GROUP Our helper constant.
 */
function ddw_mstba_admin_style() {

	/** No styles if admin bar is disabled or user is not logged in or items are disabled via constant */
	if ( ! is_admin_bar_showing()
		|| ! is_user_logged_in()
		|| ! MSTBA_DISPLAY_NETWORK_EXTEND_GROUP
	) {
		return;
	}

	?>
	<style type="text/css">
		#wpadminbar #wp-admin-bar-my-sites-super-admin.ab-submenu {
			border-top: 0 none !important;
		}

	<?php if ( function_exists( 'mp6_register_admin_color_schemes' ) ) : // "MP6" plugin addition ?>
		#wpadminbar .ab-sub-wrapper > .ab-submenu:first-child {
			border-top: none;
		}

		#wpadminbar .ab-submenu {
			padding: 6px 0;
			border-top: 1px solid #dfdfdf;
		}

		#wp-admin-bar-dashboard {
			margin-bottom: 11px !important;
		}
	<?php endif; ?>
	</style>
	<?php

}  // end of function ddw_mstba_admin_style


/**
 * Returns current plugin's header data in a flexible way.
 *
 * @since  1.1.0
 *
 * @uses   get_plugins()
 *
 * @param  $mstba_plugin_value
 * @param  $mstba_plugin_folder
 * @param  $mstba_plugin_file
 *
 * @return string Plugin data.
 */
function ddw_mstba_plugin_get_data( $mstba_plugin_value ) {

	/** Bail early if we are not in wp-admin */
	if ( ! is_admin() ) {
		return;
	}

	/** Include WordPress plugin data */
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	$mstba_plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$mstba_plugin_file = basename( ( __FILE__ ) );

	return $mstba_plugin_folder[ $mstba_plugin_file ][ $mstba_plugin_value ];

}  // end of function ddw_mstba_plugin_get_data