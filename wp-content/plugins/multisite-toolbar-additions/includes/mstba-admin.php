<?php
/**
 * Helper functions for the admin - plugin links and help tabs.
 *
 * @package    Multisite Toolbar Additions
 * @subpackage Admin
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
 * Setting internal plugin helper links constants.
 *
 * @since 1.0.0
 *
 * @uses  get_locale()
 */
define( 'MSTBA_URL_TRANSLATE',		'http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/multisite-toolbar-additions' );
define( 'MSTBA_URL_WPORG_FAQ',		'http://wordpress.org/extend/plugins/multisite-toolbar-additions/faq/' );
define( 'MSTBA_URL_WPORG_FORUM',	'http://wordpress.org/support/plugin/multisite-toolbar-additions' );
define( 'MSTBA_URL_DDW_SERIES',		'http://wordpress.org/extend/plugins/tags/ddwtoolbar' );
define( 'MSTBA_URL_WPORG_MORE',		'http://wordpress.org/extend/plugins/search.php?q=toolbar+multisite' );
define( 'MSTBA_URL_SNIPPETS',		'https://gist.github.com/3498510' );
define( 'MSTBA_PLUGIN_LICENSE', 	'GPL-2.0+' );
if ( get_locale() == 'de_DE' || get_locale() == 'de_AT' || get_locale() == 'de_CH' || get_locale() == 'de_LU' ) {
	define( 'MSTBA_URL_DONATE', 	'http://genesisthemes.de/spenden/' );
	define( 'MSTBA_URL_PLUGIN',		'http://genesisthemes.de/plugins/multisite-toolbar-additions/' );
} else {
	define( 'MSTBA_URL_DONATE', 	'http://genesisthemes.de/en/donate/' );
	define( 'MSTBA_URL_PLUGIN',		'http://genesisthemes.de/en/wp-plugins/multisite-toolbar-additions/' );
}


/**
 * Add "Custom Menu" link to plugin page.
 *
 * @since  1.0.0
 *
 * @param  $mstba_links
 * @param  $mstba_menu_link
 *
 * @return strings Menu Admin link.
 */
function ddw_mstba_custom_menu_link( $mstba_links ) {

	/** Add link only if user can edit theme options */
	if ( current_user_can( 'edit_theme_options' ) ) {

		/** Settings Page link */
		$mstba_menu_link = sprintf(
			'<a href="%s" title="%s">%s</a>',
			admin_url( 'nav-menus.php' ),
			__( 'Setup a custom toolbar menu', 'multisite-toolbar-additions' ),
			__( 'Custom Menu', 'multisite-toolbar-additions' )
		);
	
		/** Set the order of the links */
		array_unshift( $mstba_links, $mstba_menu_link );

		/** Display plugin settings links */
		return apply_filters( 'mstba_filter_settings_page_link', $mstba_links );

	}  // end-if cap check

}  // end of function ddw_mstba_custom_menu_link


add_filter( 'plugin_row_meta', 'ddw_mstba_plugin_links', 10, 2 );
/**
 * Add various support links to plugin page.
 *
 * @since  1.0.0
 *
 * @param  $mstba_links
 * @param  $mstba_file
 *
 * @return strings plugin links
 */
function ddw_mstba_plugin_links( $mstba_links, $mstba_file ) {

	/** Capability check */
	if ( ! current_user_can( 'install_plugins' ) ) {

		return $mstba_links;

	}  // end-if cap check

	/** List additional links only for this plugin */
	if ( $mstba_file == MSTBA_PLUGIN_BASEDIR . '/multisite-toolbar-additions.php' ) {

		$mstba_links[] = '<a href="' . esc_url( MSTBA_URL_WPORG_FAQ ) . '" target="_new" title="' . __( 'FAQ', 'multisite-toolbar-additions' ) . '">' . __( 'FAQ', 'multisite-toolbar-additions' ) . '</a>';

		$mstba_links[] = '<a href="' . esc_url( MSTBA_URL_WPORG_FORUM ) . '" target="_new" title="' . __( 'Support', 'multisite-toolbar-additions' ) . '">' . __( 'Support', 'multisite-toolbar-additions' ) . '</a>';

		$mstba_links[] = '<a href="' . esc_url( MSTBA_URL_SNIPPETS ) . '" target="_new" title="' . __( 'Code Snippets for Customization', 'multisite-toolbar-additions' ) . '">' . __( 'Code Snippets', 'multisite-toolbar-additions' ) . '</a>';

		$mstba_links[] = '<a href="' . esc_url( MSTBA_URL_TRANSLATE ) . '" target="_new" title="' . __( 'Translations', 'multisite-toolbar-additions' ) . '">' . __( 'Translations', 'multisite-toolbar-additions' ) . '</a>';

		$mstba_links[] = '<a href="' . esc_url( MSTBA_URL_DONATE ) . '" target="_new" title="' . __( 'Donate', 'multisite-toolbar-additions' ) . '"><strong>' . __( 'Donate', 'multisite-toolbar-additions' ) . '</strong></a>';

	}  // end-if plugin links

	/** Output the links */
	return apply_filters( 'mstba_filter_plugin_links', $mstba_links );

}  // end of function ddw_mstba_plugin_links


add_action( 'admin_head-nav-menus.php', 'ddw_mstba_widgets_help_content', 15 );
/**
 * Create and display plugin help tab content.
 *
 * Load it after core help tabs on Menus admin page.
 * Some plugin menu instructions for super_admins plus general plugin info.
 *
 * @since  1.0.0
 *
 * @uses   get_current_screen()
 * @uses   ddw_mstba_plugin_get_data()
 * @uses   WP_Screen::add_help_tab()
 *
 * @param  $mstba_menu_area_help
 *
 * @global mixed $mstba_widgets_screen
 */
function ddw_mstba_widgets_help_content() {

	global $mstba_widgets_screen;

	$mstba_widgets_screen = get_current_screen();

	/** Display help tabs only for WordPress 3.3 or higher */
	if ( ! class_exists( 'WP_Screen' ) || ! $mstba_widgets_screen ) {
		return;
	}

	/** Content: Multisite Toolbar Additions plugin */
	$mstba_menu_area_help =
		'<h3>' . __( 'Plugin', 'multisite-toolbar-additions' ) . ': ' . __( 'Multisite Toolbar Additions', 'multisite-toolbar-additions' ) . ' <small>v' . esc_attr( ddw_mstba_plugin_get_data( 'Version' ) ) . '</small></h3>' .
		'<p>' . __( 'All menu items via a Custom Menu here - and at all other places in the Toolbar (a.k.a. Admin Bar) - are only visible and accessable for Super Admins. That means in a Multisite Environment all Admins who can manage the Network. In regular WordPress (single) installs these are users with the Administrator user role.', 'multisite-toolbar-additions' ) .		
		'<p><strong>' . __( 'Added Menu Location by the plugin - only for Super Admins:', 'multisite-toolbar-additions' ) . ' "' . __( 'Multisite Toolbar Menu', 'multisite-toolbar-additions' ) . '" &mdash; <em>' . __( 'How to use it?', 'multisite-toolbar-additions' ) . '</em></strong>' .
		'<ul>' . 
			'<li>' . sprintf( __( 'Create a new menu, set a name like %s', 'multisite-toolbar-additions' ), '<code>Super Admin Toolbar</code>' ) . '</li>' .
			'<li>' . __( 'Setup your links, might mostly be custom links, or any other...', 'multisite-toolbar-additions' ) . '</li>' .
			'<li>' . __( 'Save the new menu to the plugin\'s menu location. That\'s it :)', 'multisite-toolbar-additions' ) . '</li>' .
		'<ul>' .
		'<em>' . __( 'Please note:', 'multisite-toolbar-additions' ) . '</em> ' . __( 'Every parent item = one main toolbar entry! So best would be to only use one parent item and set all other items as children.', 'multisite-toolbar-additions' ) . ' (<a href="https://www.dropbox.com/s/7u83c0g5ehk4ozq/screenshot-5.png" target="_new">' . __( 'See also this screenshot.', 'multisite-toolbar-additions' ) . '</a>)</p>' .
		'<p><strong>' . __( 'Other, recommended Multisite &amp; Toolbar plugins:', 'multisite-toolbar-additions' ) . '</strong>' .
			'<br />&raquo; <a href="' . esc_url( MSTBA_URL_DDW_SERIES ) . '" target="_new" title="David Decker ' . __( 'Toolbar plugin series', 'multisite-toolbar-additions' ) . ' ...">' . __( 'My Toolbar plugin series', 'multisite-toolbar-additions' ) . ' (David Decker, DECKERWEB.de)</a>' .
			'<br />&raquo; <a href="' . esc_url( MSTBA_URL_WPORG_MORE ) . '" target="_new" title="' . __( 'More plugins at WordPress.org', 'multisite-toolbar-additions' ) . ' ...">' . __( 'More plugins at WordPress.org', 'multisite-toolbar-additions' ) . '</a></p>' .
		'<p><strong>' . __( 'Important plugin links:', 'multisite-toolbar-additions' ) . '</strong>' . 
		'<br /><a href="' . esc_url( MSTBA_URL_PLUGIN ) . '" target="_new" title="' . __( 'Plugin website', 'multisite-toolbar-additions' ) . '">' . __( 'Plugin website', 'multisite-toolbar-additions' ) . '</a> | <a href="' . esc_url( MSTBA_URL_WPORG_FAQ ) . '" target="_new" title="' . __( 'FAQ', 'multisite-toolbar-additions' ) . '">' . __( 'FAQ', 'multisite-toolbar-additions' ) . '</a> | <a href="' . esc_url( MSTBA_URL_WPORG_FORUM ) . '" target="_new" title="' . __( 'Support', 'multisite-toolbar-additions' ) . '">' . __( 'Support', 'multisite-toolbar-additions' ) . '</a> | <a href="' . esc_url( MSTBA_URL_SNIPPETS ) . '" target="_new" title="' . __( 'Code Snippets for Customization', 'multisite-toolbar-additions' ) . '">' . __( 'Code Snippets', 'multisite-toolbar-additions' ) . '</a> | <a href="' . esc_url( MSTBA_URL_TRANSLATE ) . '" target="_new" title="' . __( 'Translations', 'multisite-toolbar-additions' ) . '">' . __( 'Translations', 'multisite-toolbar-additions' ) . '</a> | <a href="' . esc_url( MSTBA_URL_DONATE ) . '" target="_new" title="' . __( 'Donate', 'multisite-toolbar-additions' ) . '"><strong>' . __( 'Donate', 'multisite-toolbar-additions' ) . '</strong></a></p>' .
		'<p><a href="http://www.opensource.org/licenses/gpl-license.php" target="_new" title="' . esc_attr( MSTBA_PLUGIN_LICENSE ). '">' . esc_attr( MSTBA_PLUGIN_LICENSE ). '</a> &copy; 2012-' . date( 'Y' ) . ' <a href="' . esc_url( ddw_mstba_plugin_get_data( 'AuthorURI' ) ) . '" target="_new" title="' . esc_attr__( ddw_mstba_plugin_get_data( 'Author' ) ) . '">' . esc_attr__( ddw_mstba_plugin_get_data( 'Author' ) ) . '</a></p>';

	/** Add the new help tab */
	$mstba_widgets_screen->add_help_tab( array(
		'id'      => 'mstba-menus-help',
		'title'   => __( 'Multisite Toolbar Additions', 'multisite-toolbar-additions' ),
		'content' => apply_filters( 'mstba_help_tab', $mstba_menu_area_help, 'mstba-menus-help' ),
	) );

}  // end of function ddw_mstba_menu_help_content