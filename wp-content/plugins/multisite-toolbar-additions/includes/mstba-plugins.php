<?php
/**
 * Display links to active plugins/extensions settings' pages
 *
 * @package    Multisite Toolbar Additions
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/multisite-toolbar-additions/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.0.0
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
 * Helper variable.
 *
 * @since 1.1.0
 */
$mstba_multisite_check = is_multisite() ? _x( 'Site', 'Translators: For sitewide code snippets', 'multisite-toolbar-additions' ) . ' ' : '';


/**
 * Network Extend Group - hooking in network-aware plugin items
 *
 * @since 1.0.0
 */

	/**
	 * Smart Admin Tweaks (premium, by Smart Plugins/ Milan Petrovic)
	 *
	 * @since 1.3.0
	 */
	if ( class_exists( 'sat_loader' ) ) {

		/** Include code part for Smart Admin Tweaks plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-smartadmintweaks.php' );

	}  // end-if Smart Admin Tweaks


	/**
	 * Smart Cleanup Tools (premium, by Smart Plugins/ Milan Petrovic)
	 *
	 * @since 1.3.0
	 */
	if ( defined( 'SCT_WP_CRON' ) ) {

		/** Include code part for Smart Cleanup Tools plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-smartcleanuptools.php' );

	}  // end-if Smart Cleanup Tools


	/**
	 * Smart Options Optimizer (premium, by Smart Plugins/ Milan Petrovic)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'soo_loader' ) ) {

		/** Include code part for Smart Options Optimizer plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-smartoptionsoptimizer.php' );

	}  // end-if Smart Options Optimizer


	/**
	 * Snapshot (premium, by Paul Menard (Incsub)/ WPMU DEV)
	 *
	 * @since 1.2.0
	 */
	if ( defined( 'SNAPSHOT_I18N_DOMAIN' ) && current_user_can( 'export' ) ) {

		/** Include code part for Snapshot plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-snapshot.php' );

	}  // end-if Snapshot


	/**
	 * BackWPup v3.x (free & premium, by Inpsyde Gmbh & Daniel Hüsken)
	 *
	 * @since 1.3.0
	 */
	if ( current_user_can( 'backwpup' ) && ! get_site_option( 'backwpup_cfg_showadminbar', TRUE ) ) {

		/** Include code part for BackWPup v3.x plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-backwpup3x.php' );

	}  // end-if BackWPup 3.x check


	/**
	 * BackWPup v2.x (free, by Daniel Hüsken)
	 *
	 * @since 1.1.0
	 */
	if ( defined( 'BACKWPUP_USER_CAPABILITY' ) && current_user_can( BACKWPUP_USER_CAPABILITY ) ) {

		/** Load BackWPup plugin settings */
		$mstba_backwpup_option = get_option( 'backwpup' );

		/** Load our stuff only if user has the BackWPup Admin Bar option NOT activated */
		if ( ! $mstba_backwpup_option[ 'showadminbar' ] ) {

			/** Include code part for BackWPup v2.x plugin support */
			require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-backwpup.php' );

		}  // end-if options check

	}  // end-if BackWPup v2.x


	/**
	 * Quick Cache (free, by PriMoThemes.com / WebSharks, Inc.)
	 *
	 * @since 1.0.0
	 */
	if ( defined( 'WS_PLUGIN__QCACHE_VERSION' ) && current_user_can( 'edit_plugins' ) ) {

		/** Include code part for Quick Cache plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-quickcache.php' );

	}  // end-if Quick Cache


	/**
	 * WP Super Cache (free, by Donncha O Caoimh)
	 *
	 * @since 1.0.0
	 */
	if ( ( defined( 'WP_CACHE' ) && WP_CACHE )
		&& function_exists( 'wp_super_cache_text_domain' )
		&& current_user_can( 'manage_options' )
	) {

		/** Include code part for WP Super Cache plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-wpsupercache.php' );

	}  // end-if WP Super Cache


	/**
	 * WP-Piwik (free, by Andr&eacute; Br&auml;kling)
	 *
	 * @since 1.0.0
	 */
	if ( class_exists( 'wp_piwik' ) && current_user_can( 'wp-piwik_read_stats' ) ) {

		/** Include code part for WP-Piwik plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-wppiwik.php' );

	}  // end-if WP-Piwik


	/**
	 * Code Snippets (free, by Shea Bunge)
	 *
	 * @since 1.1.0
	 */
	if ( class_exists( 'Code_Snippets' )
		&& ( current_user_can( 'manage_snippets' ) || current_user_can( 'manage_network_snippets' ) )
	) {

		/** Include code part for Code Snippets plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-codesnippets.php' );

	}  // end-if Code Snippets


	/**
	 * WordPress MU Domain Mapping (free, by Donncha O Caoimh, Ron Rennick, Automatic Inc.)
	 *
	 * @since 1.4.0
	 */
	if ( is_multisite() && function_exists( 'dm_text_domain' ) && current_user_can( 'manage_options' ) ) {

		/** Include code part for WP MU Domain Mapping plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-wpmudomainmapping.php' );

	}  // end-if Multisite Robots.txt Manager


	/**
	 * Multisite Robots.txt Manager (free, by tribalNerd)
	 *
	 * Note: Currently versions 0.4.x and former 0.3.x are supported.
	 *
	 * @since 1.4.0
	 */
	if ( is_multisite()
		&& ( class_exists( 'msrtm_robots_txt' ) || class_exists( 'display_robots' ) )
		&& current_user_can( 'manage_options' )
	) {

		/** Include code part for MS Robots.txt Manager plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-msrobotstxt.php' );

	}  // end-if Multisite Robots.txt Manager


	/**
	 * WPMS Site Maintenance Mode (free, by 7 Media Web Solutions, LLC)
	 *
	 * @since 1.0.0
	 */
	if ( class_exists( 'wpms_sitemaint' ) && current_user_can( 'manage_network_options' ) ) {

		$mstba_tb_items[ 'networkext_wpmsmaintenance' ] = array(
			'parent' => $networkextgroup,
			'title'  => __( 'Maintenance Mode', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'settings.php?page=wpms_site_maint' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'Multisite Maintenance Mode', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

	}  // end-if WPMS Site Maintenance Mode


	/**
	 * Organizational Message Notifier (free, by Zaantar)
	 *
	 * @since 1.1.0
	 */
	if ( function_exists( 'omn_network_admin_menu' ) && current_user_can( 'manage_network_options' ) ) {

		$mstba_tb_items[ 'networkext_orgmessagenotifier' ] = array(
			'parent' => $networkextgroup,
			'title'  => __( 'Organizational Messages', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'settings.php?page=omn-settings' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'Organizational Message Notifier for Users/ Admins', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_orgmessagenotifier_create' ] = array(
			'parent' => $networkext_orgmessagenotifier,
			'title'  => __( 'Create Messages', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'index.php?page=omn-superadmin-overview' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Create Messages', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'networkext_orgmessagenotifier_messages' ] = array(
			'parent' => $networkext_orgmessagenotifier,
			'title'  => __( 'User Messages', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'index.php?page=omn-messages' ),
			'meta'   => array( 'target' => '', 'title' => __( 'User Messages', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Organizational Message Notifier


	/**
	 * Login Security Solution (free, by Daniel Convissor)
	 *    Part I: for Multisite installs (part II see below)
	 *
	 * @since 1.2.0
	 */
	if ( class_exists( 'login_security_solution' ) && is_multisite() && current_user_can( 'manage_network_options' ) ) {

		$mstba_tb_items[ 'networkext_loginsecuritysolution' ] = array(
			'parent' => $networkextgroup,
			'title'  => __( 'Login Security Solution', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'settings.php?page=login-security-solution' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'Login Security Solution', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Login Security Solution


	/**
	 * Hide My WP (premium, by Hassan Jahangiri)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'HideMyWP' ) && current_user_can( 'manage_options' ) ) {

		/** Include code part for Hide My WP plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-hidemywp.php' );

	}  // end-if Hide My WP


	/**
	 * Ultimate Branding (premium, by Incsub Team/ WPMU DEV)
	 *
	 * @since 1.2.0
	 */
	if ( class_exists( 'UB_Help' ) && current_user_can( 'manage_options' ) ) {

		/** Include code part for Quick Cache plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-ultimatebranding.php' );

	}  // end-if Ultimate Branding


	/**
	 * Blog Copier (free, by Modern Tribe, Inc.)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'BlogCopier' ) && current_user_can( 'manage_sites' ) ) {

		$mstba_tb_items[ 'networkext_blogcopier' ] = array(
			'parent' => 'network-admin-s',
			'title'  => __( 'Blog Copier', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'sites.php?page=blog-copier' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'Blog Copier - Clone Website (Sub Site)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

		/** For our Network 'new-content' section */
		if ( is_network_admin() && ddw_mstba_network_new_content_filter() ) {

			$mstba_tb_items[ 'networkext_newcontent_blogcopier' ] = array(
				'parent' => 'new-content-site',
				'title'  => __( 'Clone Site', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'sites.php?page=blog-copier' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'Blog Copier - Clone Website (Sub Site)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
			);

		}  // end-if is_network_admin() check

	}  // end-if Blog Copier


	/**
	 * NS Cloner - Site Copier (free, by Never Settle [neversettle.it])
	 *
	 * @since 1.4.0
	 */
	if ( defined( 'NS_CLONER_PLUGIN_URL' ) && current_user_can( 'manage_network_options' ) ) {

		$mstba_tb_items[ 'networkext_nscloner' ] = array(
			'parent' => 'network-admin-s',
			'title'  => __( 'NS Cloner', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'sites.php?page=ns-cloner' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'NS Cloner - Clone Website (Sub Site)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

		/** For our Network 'new-content' section */
		if ( is_network_admin() && ddw_mstba_network_new_content_filter() ) {

			$mstba_tb_items[ 'networkext_newcontent_nscloner' ] = array(
				'parent' => 'new-content-site',
				'title'  => __( 'Clone Site', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'sites.php?page=ns-cloner' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'NS Cloner - Clone Website (Sub Site)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
			);

		}  // end-if is_network_admin() check

	}  // end-if NS Cloner


	/**
	 * NS Cloner Pro (premium, by Never Settle [neversettle.it])
	 *
	 * @since 1.4.0
	 */
	if ( defined( 'NS_CLONER_PRO_PLUGIN_URL' ) && current_user_can( 'manage_network_options' ) ) {

		$mstba_tb_items[ 'networkext_nsclonerpro' ] = array(
			'parent' => 'network-admin-s',
			'title'  => __( 'NS Cloner Pro', 'multisite-toolbar-additions' ),
			'href'   => network_admin_url( 'sites.php?page=ns-cloner-pro' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'NS Cloner Pro - Clone Website (Sub Sites)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

		/** For our Network 'new-content' section */
		if ( is_network_admin() && ddw_mstba_network_new_content_filter() ) {

			$mstba_tb_items[ 'networkext_newcontent_nsclonerpro' ] = array(
				'parent' => 'new-content-site',
				'title'  => __( 'Clone Site', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'sites.php?page=ns-cloner-pro' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'NS Cloner Pro - Clone Website (Sub Sites)', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
			);

		}  // end-if is_network_admin() check

	}  // end-if NS Cloner Pro


	/**
	 * WP Migrate DB Pro (premium, by Delicious Brains (Brad Touesnard & Chris Aprea))
	 *
	 * @since 1.4.0
	 */
	if ( function_exists( 'wp_migrate_db_pro_init' ) ) {

		/** Include code part for WP Migrate DB Pro support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-wpmigratedbpro.php' );

	}  // end-if WP Migrate DB Pro


	/**
	 * Simple System Info (premium, by Smart Plugins/ Milan Petrovic)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'ssi_loader' ) ) {

		/** Include code part for Simple System Info plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-simplesysteminfo.php' );

	}  // end-if Simple System Info


/**
 * Site Extend Group - hooking in (site-specific, non network-aware) plugin items.
 *
 * @since 1.0.0
 */
	/**
	 * Installer [WP Installer/Manage Repositories] (free, by WP-Compatibility.com/ OnTheGoSystems, Inc.)
	 *
	 * @since 1.0.0
	 */
	if ( class_exists( 'WPRC_Installer' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_wprcinstaller' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Manage Repositories', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=installer/pages/repositories.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Manage Repositories', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_wprcinstaller_add' ] = array(
			'parent' => $siteext_wprcinstaller,
			'title'  => __( 'Add new Repository', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=installer/pages/repositories.php&action=add' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Repository', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Installer


	/**
	 * Code With WP Custom Snippets (free, by Thomas Griffin)
	 *
	 * @since 1.1.0
	 */
	if ( class_exists( 'Cwwp_Init' ) && current_user_can( 'edit_posts' ) ) {

		$mstba_tb_items[ 'siteext_cwwpcsnippets' ] = array(
			'parent' => $siteextgroup,
			'title'  => $mstba_multisite_check . __( 'Code Snippets', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'edit.php?post_type=cwwp-custom-snippets' ),
			'meta'   => array( 'target' => '', 'title' => $mstba_multisite_check . __( 'Code Snippets', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'siteext_cwwpcsnippets_add' ] = array(
			'parent' => $siteext_cwwpcsnippets,
			'title'  => __( 'Add new Snippet', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'post-new.php?post_type=cwwp-custom-snippets' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Snippet', 'multisite-toolbar-additions' ) )
		);

		/** Capability check for import/export function */
		if ( current_user_can( 'manage_options' ) ) {

			$mstba_tb_items[ 'siteext_cwwpcsnippets_importexport' ] = array(
				'parent' => $siteext_cwwpcsnippets,
				'title'  => __( 'Import/ Export', 'multisite-toolbar-additions' ),
				'href'   => admin_url( 'edit.php?post_type=cwwp-custom-snippets&page=import-export-code-snippets' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Import/ Export', 'multisite-toolbar-additions' ) )
			);

		}  // end-if cap check

	}  // end-if Code With WP Custom Snippets


	/**
	 * Toolbox (free, by Sergej Müller)
	 *
	 * @since 1.1.0
	 */
	if ( class_exists( 'Toolbox' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_smtoolbox' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Toolbox Modules', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=toolbox' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Toolbox Modules', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Toolbox


	/**
	 * Relevanssi & Relevanssi Premium (free & premium, by Mikko Saari, www.relevanssi.com)
	 *
	 * @since 1.1.0
	 */
	if ( function_exists( '_relevanssi_install' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_relevanssi' ] = array(
			'parent' => $siteextgroup,
			'title'  => RELEVANSSI_PREMIUM ? __( 'Relevanssi Premium', 'multisite-toolbar-additions' ) : __( 'Relevanssi', 'multisite-toolbar-additions' ),
			'href'   => RELEVANSSI_PREMIUM ? admin_url( 'options-general.php?page=relevanssi-premium/relevanssi.php' ) : admin_url( 'options-general.php?page=relevanssi/relevanssi.php' ),
			'meta'   => array( 'target' => '', 'title' => RELEVANSSI_PREMIUM ? __( 'Relevanssi Premium', 'multisite-toolbar-additions' ) : __( 'Relevanssi', 'multisite-toolbar-additions' ) )
		);

		/** Display menu item only if option is true */
		if ( 'on' == get_option( 'relevanssi_log_queries' ) ) {

			$mstba_tb_items[ 'siteext_relevanssi_searches' ] = array(
				'parent' => $siteext_relevanssi,
				'title'  => __( 'User Searches', 'multisite-toolbar-additions' ),
				'href'   => RELEVANSSI_PREMIUM ? admin_url( 'index.php?page=relevanssi-premium/relevanssi.php' ) : admin_url( 'index.php?page=relevanssi/relevanssi.php' ),
				'meta'   => array( 'target' => '', 'title' => __( 'User Searches', 'multisite-toolbar-additions' ) )
			);

		}  // end-if relevanssi option check

	}  // end-if Relevanssi


	/**
	 * Multisite Language Switcher (free, by Dennis Ploetner)
	 *
	 * @since 1.0.0
	 */
	if ( defined( 'MSLS_PLUGIN_VERSION' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_mslangswitcher' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Multisite Language Switcher', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=MslsAdmin' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Multisite Language Switcher', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Multisite Language Switcher


	/**
	 * Howdy Tweaks (free, by Kailey Lampert)
	 *
	 * @since 1.0.0
	 */
	if ( class_exists( 'howdy_tweaks' ) && current_user_can( 'administrator' ) ) {

		$mstba_tb_items[ 'siteext_howdytweaks' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Howdy Tweaks', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=howdy-tweaks' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Howdy Tweaks', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Howdy Tweaks


	/**
	 * White Label CMS (free, by www.videousermanuals.com)
	 *
	 * @since 1.1.0
	 */
	if ( function_exists( 'wlcms_check_for_login' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_whitelabelcms' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'White Label CMS', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=wlcms-plugin.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'White Label CMS', 'multisite-toolbar-additions' ) )
		);

	}  // end-if White Label CMS


	/**
	 * Cachify (free, by Sergej Müller)
	 *
	 * @since 1.0.0
	 */
	if ( class_exists( 'Cachify' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_cachify' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Cachify Settings', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=cachify' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Cachify Settings', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Cachify


	/**
	 * Hyper Cache (free, by Stefano Lissa)
	 *
	 * @since 1.0.0
	 */
	if ( ( defined( 'WP_CACHE' ) && WP_CACHE ) && function_exists( 'hyper_admin_menu' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_hypercache' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Hyper Cache', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=hyper-cache/options.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Hyper Cache', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Hyper Cache


	/**
	 * Hyper Cache Extended (free, by Martin Lazarov)
	 *
	 * @since 1.0.0
	 */
	if ( ( defined( 'WP_CACHE' ) && WP_CACHE ) && function_exists( 'hyper_log_cache' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_hypercache' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Hyper Cache Extended', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=hyper-cache-extended/options.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Hyper Cache Extended', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Hyper Cache Extended


	/**
	 * Codestyling Localization (free, by Heiko Rabe)
	 *
	 * @since 1.4.0
	 */
	if ( defined( 'CSP_PO_TEXTDOMAIN' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_codestylinglocalization' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Manage Translations', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'tools.php?page=codestyling-localization/codestyling-localization.php' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'Codestyling Localization - Manage Translations', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Codestyling Localization


	/**
	 * P3 (Plugin Performance Profiler) (free, by GoDaddy.com)
	 *
	 * @since 1.4.0
	 */
	if ( defined( 'P3_PLUGIN_SLUG' ) && current_user_can( 'manage_options' ) ) {

		/** Include code part for P3 plugin support */
		require_once( MSTBA_PLUGIN_DIR . '/includes/mstba-plugins-p3profiler.php' );

	}  // end-if P3 (Plugin Performance Profiler)


	/**
	 * Limit Login Attempts (free, by Johan Eenfeldt)
	 *
	 * @since 1.0.0
	 */
	if ( defined( 'LIMIT_LOGIN_DIRECT_ADDR' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_limitloginattempts' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Limit Login Attempts', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=limit-login-attempts' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Limit Login Attempts', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Limit Login Attempts


	/**
	 * Login Security Solution (free, by Daniel Convissor)
	 *    Part II: for non-Multisite installs (part I see above)
	 *
	 * @since 1.2.0
	 */
	if ( class_exists( 'login_security_solution' ) && ! is_multisite() && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_loginsecuritysolution' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Login Security Solution', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=login-security-solution' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'Login Security Solution', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Login Security Solution


	/**
	 * WP Migrate DB (free, by Brad Touesnard)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'WP_Migrate_DB' ) && current_user_can( 'update_core' ) ) {

		$mstba_tb_items[ 'siteext_wpmigratedb' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Migrate Database', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'tools.php?page=wp-migrate-db' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Migrate Database', 'multisite-toolbar-additions' ) )
		);

	}  // end-if WP Migrate DB


	/**
	 * WP-Optimize (free, by Ruhani Rabin)
	 *
	 * @since 1.0.0
	 */
	if ( function_exists( 'optimize_admin_actions' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_wpoptimize' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'WP-Optimize (DB)', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'admin.php?page=WP-Optimize' ),
			'meta'   => array( 'target' => '', 'title' => _x( 'Database Optimize via WP-Optimize', 'Translators: For the tooltip', 'multisite-toolbar-additions' ) )
		);

	}  // end-if WP-Optimize


	/**
	 * Optimize Database after Deleting Revisions (free, by Rolf van Gelder)
	 *
	 * @since 1.3.0
	 */
	if ( function_exists( 'rvg_odb_admin_menu' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'siteext_rvgoptimizedb' ] = array(
			'parent' => $siteextgroup,
			'title'  => __( 'Optimize Database', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'tools.php?page=rvg-optimize-db.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Optimize Database', 'multisite-toolbar-additions' ) )
		);

			$mstba_tb_items[ 'siteext_rvgoptimizedb_settings' ] = array(
				'parent' => $siteext_rvgoptimizedb,
				'title'  => __( 'Settings', 'multisite-toolbar-additions' ),
				'href'   => admin_url( 'options-general.php?page=rvg_odb_admin' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'multisite-toolbar-additions' ) )
			);

	}  // end-if Optimize Database after Deleting Revisions


	/**
	 * Go Sidebar Wizard (premium, by Granth)
	 *
	 * @since 1.4.0
	 */
	if ( defined( 'GW_GO_SBWIZARD_VER' ) && current_user_can( 'edit_pages' ) ) {

		$mstba_tb_items[ 'widgets-plggosbwizard' ] = array(
			'parent' => $widgets,
			'title'  => __( 'Go Sidebar Wizard', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'admin.php?page=go-sbwizard' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Go Sidebar Wizard', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'widgets-plggosbwizard-sidebars' ] = array(
			'parent' => $widgets,
			'title'  => __( 'Custom Sidebars', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'admin.php?page=go-sbwizard-custom-sidebars' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Custom Sidebars', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Go Sidebar Wizard


	/**
	 * Widget Settings Importer/Exporter (free, by Kevin Langley, Sean McCafferty, Mark Parolisi)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'Widget_Data' ) && current_user_can( 'administrator' ) ) {

		$mstba_tb_items[ 'widgets-plgwdata-export' ] = array(
			'parent' => $widgets,
			'title'  => __( 'Widget Settings Export', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'tools.php?page=widget-settings-export' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Widget Settings Export', 'multisite-toolbar-additions' ) )
		);

		$mstba_tb_items[ 'widgets-plgwdata-import' ] = array(
			'parent' => $widgets,
			'title'  => __( 'Widget Settings Import', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'tools.php?page=widget-settings-import' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Widget Settings Import', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Widget Settings Importer/Exporter


/**
 * Special Hook Ins
 *
 * @since 1.1.0
 */
	/**
	 * User Management Tools (free, by scribu/ AppThemes)
	 *
	 * @since 1.1.0
	 */
	if ( is_super_admin()
		&& ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'user-management-tools/user-management-tools.php' ) )
	) {

		if ( class_exists( 'User_Management_Tools' ) && current_user_can( 'manage_options' ) ) {

			$mstba_tb_items[ 'network-usermgmttools' ] = array(
				'parent' => 'network-admin-u',
				'title'  => __( 'Entire Network', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'users.php?umt_network=1' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'Entire Network - User Bulk Actions', 'Translators: for the tooltip', 'multisite-toolbar-additions' ) )
			);

		}  // end-if plugin & cap check

	}  // end-if User Management Tools (network activated)


	/**
	 * Network Mass Email (free, by Kenny Zaron)
	 *
	 * @since 1.1.0
	 */
	if ( is_super_admin()
		&& ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'network-mass-email/network-mass-email.php' ) )
	) {

		if ( function_exists( 'nme_decider' ) && current_user_can( 'manage_network' ) ) {

			$mstba_tb_items[ 'network-massemail' ] = array(
				'parent' => 'network-admin-u',
				'title'  => __( 'Network Mass Email', 'multisite-toolbar-additions' ),
				'href'   => network_admin_url( 'users.php?page=nme_menupage' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Network Mass Email', 'multisite-toolbar-additions' ) )
			);

		}  // end-if plugin & cap check

	}  // end-if Network Mass Email (network activated)


	/**
	 * Restrict Widgets (free, by Digital Factory)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'RestrictWidgets' ) && current_user_can( 'manage_widgets' ) ) {

		$mstba_tb_items[ 'widgets-plgrestrictw' ] = array(
			'parent' => $widgets,
			'title'  => __( 'Restrict Widgets', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'widgets.php' ) . '#widgets-options',
			'meta'   => array( 'target' => '', 'title' => __( 'Restrict Widgets', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Restrict Widgets


	/**
	 * Tabify Edit Screen (free, by Marko Heijnen)
	 *
	 * @since 1.4.0
	 */
	if ( class_exists( 'Tabify_Edit_Screen' ) && current_user_can( 'manage_options' ) ) {

		$mstba_tb_items[ 'mcbase-plgtabifyedits' ] = array(
			'parent' => $mcbase,
			'title'  => __( 'Tabify Edit Screens', 'multisite-toolbar-additions' ),
			'href'   => admin_url( 'options-general.php?page=tabify-edit-screen' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Tabify Edit Screens', 'multisite-toolbar-additions' ) )
		);

	}  // end-if Tabify Edit Screen