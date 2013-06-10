<?php
/*
Plugin Name: WP-Piwik

Plugin URI: http://wordpress.org/extend/plugins/wp-piwik/

Description: Adds Piwik stats to your dashboard menu and Piwik code to your wordpress header.

Version: 0.9.9.6
Author: Andr&eacute; Br&auml;kling
Author URI: http://www.braekling.de

****************************************************************************************** 
	Copyright (C) 2009-2013 Andre Braekling (email: webmaster@braekling.de)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*******************************************************************************************/

// Avoid direct calls to this file if wp core files not present
if (!function_exists ('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

// Makes sure all required include files are loaded before trying to use it
if (!function_exists('is_plugin_active_for_network'))
	require_once(ABSPATH.'/wp-admin/includes/plugin.php');

if (!class_exists('wp_piwik')) {
class wp_piwik {

	private static
		$intRevisionId = 90961,
		$strVersion = '0.9.9.6',
		$blog_id,
		$intDashboardID = 30,
		$strPluginBasename = NULL,
		$bolJustActivated = false,
		$logger,
		$settings;
				
	private
		$intStatsPage = NULL,
		$bolNetwork = false,
		$aryAttributes = array(),
		$strResult = '';

	public function __construct() {
		global $blog_id;
		self::$blog_id = (isset($blog_id)?$blog_id:'n/a');
		$this->openLogger();
		$this->openSettings();
        // Call install function on activation
        register_activation_hook(__FILE__, array($this, 'installPlugin'));
		// Store plugin basename
		self::$strPluginBasename = plugin_basename(__FILE__);
		// Upgrade?
		if (self::$settings->getGlobalOption('revision') && self::$settings->getGlobalOption('revision') < self::$intRevisionId) $this->upgradePlugin();
		// Settings changed?
		if (isset($_POST['action']) && $_POST['action'] == 'save_wp-piwik_settings')
			$this->applySettings();
		// Set Piwik globals if PHP API is used
		elseif (self::$settings->getGlobalOption('piwik_mode') && self::$settings->getGlobalOption('piwik_mode') == 'php')
			self::definePiwikConstants();
		// Load language file
		load_plugin_textdomain('wp-piwik', false, dirname(self::$strPluginBasename)."/languages/");
		// Add meta links to plugin details
		add_filter('plugin_row_meta', array($this, 'setPluginMeta'), 10, 2);
		// Register columns
		add_filter('screen_layout_columns', array(&$this, 'onScreenLayoutColumns'), 10, 2);
		// Add network admin menu if required
		if (is_plugin_active_for_network('wp-piwik/wp-piwik.php'))
			add_action('network_admin_menu', array($this, 'buildNetworkAdminMenu'));
		// Add admin menu		
		add_action('admin_menu', array($this, 'buildAdminMenu'));
		// Register the callback been used if options of page been submitted and needs to be processed
		add_action('admin_post_save_wp-piwik_stats', array(&$this, 'onStatsPageSaveChanges'));
		// Register own post meta boxes
		add_action('load-post.php', array(&$this, 'postMetaboxes'));
		add_action('load-post-new.php', array(&$this, 'postMetaboxes'));
		// Add dashboard widget if enabled
		/* TODO: Use bitmask here */
		if (self::$settings->getGlobalOption('dashboard_widget') || self::$settings->getGlobalOption('dashboard_chart') || self::$settings->getGlobalOption('dashboard_seo'))
			add_action('wp_dashboard_setup', array($this, 'extendWordPressDashboard'));
		// Add Toolbar graph if enabled
		if (self::$settings->getGlobalOption('toolbar')) {
			add_action(is_admin()?'admin_head':'wp_head', array($this, 'loadToolbarRequirements'));
			add_action('admin_bar_menu', array(&$this, 'extendWordPressToolbar'), 1000);
		}
		// Enable shortcodes if enabled
		if (self::$settings->getGlobalOption('shortcodes'))
			add_shortcode( 'wp-piwik', array(&$this, 'shortcode'));
			
		// Add tracking code to header if enabled
		if (self::$settings->getGlobalOption('add_tracking_code')) {
			add_action('wp_head', array($this, 'site_header'));
			// Add <noscript> code if enabled
			if (self::$settings->getGlobalOption('track_noscript')) add_action('wp_footer', array($this, 'site_footer'));
		}
		self::$settings->save();
	}

	function __destruct() {
		$this->closeLogger();
	}
			
	private function includeFile($strFile) {
		self::$logger->log('Include '.$strFile.'.php');
		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$strFile.'.php'))
			include(dirname(__FILE__).DIRECTORY_SEPARATOR.$strFile.'.php');
	}	
	
	function installPlugin() {
		self::$logger->log('Running WP-Piwik installation');
        add_action('admin_notices', array($this, 'updateMessage'));
	    self::$bolJustActivated = true;
		self::$settings->setGlobalOption('revision', self::$intRevisionId);
		self::$settings->setGlobalOption('last_settings_update', time());
	}

	static function uninstallPlugin() {
		self::$logger->log('Running WP-Piwik uninstallation');
        // Check if uninstall call is valid
        if (!defined('WP_UNINSTALL_PLUGIN'))
        	exit();
        self::$settings->resetSettings(true);
	}

    function upgradePlugin() {
    	self::$logger->log('Upgrade WP-Piwik to '.self::$strVersion);
        add_action('admin_notices', array($this, 'updateMessage'));
        // Update: Translate options
        if (self::$settings->getGlobalOption('revision') < 80403)
            self::includeFile('update/80403');
        if (self::$settings->getGlobalOption('revision') < 80502)
            self::includeFile('update/80502');
        if (self::$settings->getGlobalOption('revision') < 80602)
            self::includeFile('update/80602');
        if (self::$settings->getGlobalOption('revision') < 80800)
            self::includeFile('update/80800');
        if (self::$settings->getGlobalOption('revision') < 90001)
            self::includeFile('update/90001');
        if (self::$settings->getGlobalOption('revision') < 90206)
            self::includeFile('update/90206');
		if (self::$settings->getGlobalOption('revision') < 90405)
            self::includeFile('update/90405');
		if (self::$settings->getGlobalOption('revision') < 90601)
            self::includeFile('update/90601');
		if (self::$settings->getGlobalOption('revision') < 90700)
            self::includeFile('update/90700');
		if (self::$settings->getGlobalOption('revision') < 90805)
            self::includeFile('update/90801');
        if (self::$settings->getGlobalOption('revision') < 90821)
        	self::includeFile('update/90821');
        if (self::$settings->getGlobalOption('revision') < 90910)
        	self::includeFile('update/90910');
        if (self::$settings->getGlobalOption('revision') < 90921)
        	self::includeFile('update/90920');
        if (self::$settings->getGlobalOption('revision') < 90940)
        	self::includeFile('update/90940');
        if (self::$settings->getGlobalOption('revision') < 90961)
        	self::includeFile('update/90961');

        // Install new version
        $this->installPlugin();      
    }

	function updateSite() {
		self::$logger->log('Reset site settings to force a site setting update. Blog ID: '.self::$blog_id.' Site ID: '.self::$settings->getOption('site_id'));
		self::$arySettings = array(
			'tracking_code' => '',
			'site_id' => get_option('wp-piwik_siteid', NULL),
			'last_tracking_code_update' => 0,
			'dashboard_revision' => get_option('wp-piwik_dashboardid', 0)
		);
		self::$settings->setGlobalOption('last_settings_update', time());
		// Remove deprecated option values
		$aryRemoveOptions = array('wp-piwik_siteid','wp-piwik_404','wp-piwik_scriptupdate','wp-piwik_dashboardid','wp-piwik_jscode');
		foreach ($aryRemoveOptions as $strRemoveOption) delete_option($strRemoveOption);
		// Save upgraded or default settings
		self::saveSettings();
	}

	function updateMessage() {
		$strText = self::$settings->getGlobalOption('plugin_display_name').' '.self::$strVersion.' '.__('installed','wp-piwik').'.';
		$strNotice = (!self::isConfigured()?__('Next you should connect to Piwik','wp-piwik'):__('Please validate your configuration','wp-piwik'));
		$strLink = sprintf('<a href="'.(is_plugin_active_for_network('wp-piwik/wp-piwik.php')?'settings':'options-general').'.php?page=%s">%s</a>', self::$strPluginBasename, __('Settings', 'wp-piwik'));
		echo '<div class="updated fade"><p>'.$strText.' <strong>'.__('Important', 'wp-piwik').':</strong> '.$strNotice.': '.$strLink.'.</p></div>';
	}
	
	function site_header() {
		global $blod_id;
		// Hotfix: Custom capability problem with WP multisite
		if (is_multisite()) {
			foreach (self::$settings->getGlobalOption('capability_stealth') as $strKey => $strVal)
				if ($strVal && current_user_can($strKey)) {
					self::$logger->log('Do not add tracking code to site header (user should not be tracked) Blog ID: '.self::$blog_id.' Site ID: '.self::$settings->getOption('site_id'));
					return;
				}
		// Don't add tracking code?
		} elseif (current_user_can('wp-piwik_stealth')) {
			self::$logger->log('Do not add tracking code to site header (user should not be tracked) Blog ID: '.self::$blog_id.' Site ID: '.self::$settings->getOption('site_id'));
			echo '<!-- *** WP-Piwik - see http://wordpress.org/extend/plugins/wp-piwik/ ******** -->'."\n";
			echo '<!-- Current user should not be tracked. -->'."\n";
			echo '<!-- *** /WP-Piwik *********************************************************** -->'."\n";
			return;
		}
		// Hotfix: Update network site if not done yet
		if (is_plugin_active_for_network('wp-piwik/wp-piwik.php') && get_option('wp-piwik_siteid', false)) $this->updateSite();
		// Autohandle site if no tracking code available
		if (!self::$settings->getOption('tracking_code'))
			$aryReturn = $this->addPiwikSite();
		// Update/get code if outdated/unknown
		if (self::$settings->getOption('last_tracking_code_update') < self::$settings->getGlobalOption('last_settings_update') || !self::$settings->getOption('tracking_code')) {
			self::$settings->setOption('tracking_code', $this->callPiwikAPI('SitesManager.getJavascriptTag'));
			self::$settings->save();
		}
		// Change code if 404
		if (is_404() && self::$settings->getGlobalOption('track_404')) {
			self::$logger->log('Apply 404 changes. Blog ID: '.self::$blog_id.' Site ID: '.self::$settings->getOption('site_id'));
			$strTrackingCode = str_replace("_paq.push(['trackPageView']);", "_paq.push(['setDocumentTitle', '404/URL = '+String(document.location.pathname+document.location.search).replace(/\//g,'%2f') + '/From = ' + String(document.referrer).replace(/\//g,'%2f')]);\n_paq.push(['trackPageView']);", self::$settings->getOption('tracking_code'));
		}
		// Change code if search result
		elseif (is_search() && self::$settings->getGlobalOption('track_search')) {
			self::$logger->log('Apply search tracking changes. Blog ID: '.self::$blog_id.' Site ID: '.self::$settings->getOption('site_id'));
			$objSearch = new WP_Query("s=" . get_search_query() . '&showposts=-1'); 
			$intResultCount = $objSearch->post_count;
			$strTrackingCode = str_replace("_paq.push(['trackPageView']);", "_paq.push(['trackSiteSearch','".get_search_query()."', false, ".$intResultCount."]);\n_paq.push(['trackPageView']);", self::$settings->getOption('tracking_code'));
		// Use default tracking code
		} else 
			$strTrackingCode = self::$settings->getOption('tracking_code');
		// Send tracking code
		self::$logger->log('Add tracking code. Blog ID: '.self::$blog_id.' Site ID: '.self::$settings->getOption('site_id'));
		echo '<!-- *** WP-Piwik - see http://wordpress.org/extend/plugins/wp-piwik/ ******** -->'."\n";
		// Add custom variables if set:
		if (is_single()) {
			$strCustomVars = '';
			for ($i = 1; $i <= 5; $i++) {
				// Get post ID
				$intID = get_the_ID();
				// Get key
				$strMetaKey = get_post_meta($intID, 'wp-piwik_custom_cat'.$i, true);
				// Get value
				$strMetaVal = get_post_meta($intID, 'wp-piwik_custom_val'.$i, true);
				if (!empty($strMetaKey) && !empty($strMetaVal))
					$strCustomVars .= "_paq.push(['setCustomVariable',".$i.", '".$strMetaKey."', '".$strMetaVal."', 'page']);\n";
			}
			if (!empty($strMetaKey)) $strTrackingCode = str_replace("_paq.push(['trackPageView']);", $strCustomVars."_paq.push(['trackPageView']);", $strTrackingCode);
		}
		echo $strTrackingCode;
		echo '<!-- *** /WP-Piwik *********************************************************** -->'."\n";
		$strName = get_bloginfo('name');
		if (self::$settings->getOption('name') != $strName)
			$this->updatePiwikSite();
	}

	/**
	 * Add noscript code
	 */
	function site_footer() {
		// Hotfix: Custom capability problem with WP multisite
		if (is_multisite()) {
			foreach (self::$settings->getGlobalOption('capability_stealth') as $strKey => $strVal)
				if ($strVal && current_user_can($strKey))
					return;
		// Don't add tracking code?
		} elseif (current_user_can('wp-piwik_stealth')) return;
		// Send tracking code
		self::$logger->log('Add noscript code. Blog ID: '.self::$blog_id.' Site ID: '.self::$settings->getOption('site_id'));
		echo '<!-- *** WP-Piwik - see http://wordpress.org/extend/plugins/wp-piwik/ ******** -->'."\n";
		echo self::$settings->getOption('noscript_code')."\n";
		echo '<!-- *** /WP-Piwik *********************************************************** -->'."\n";
	}
	
	/**
	 * Shortcode function
	 **/
	 
	function shortcode($aryAttributes) {
		$this->aryAttributes = shortcode_atts(
			array(
				'title' => '',
				'module' => 'overview',
				'period' => 'day',
				'date' => 'yesterday',
				'limit' => 10,
				'width' => '100%',
				'height' => '200px',
				'language' => 'en'
			), $aryAttributes);
		switch ($this->aryAttributes['module']) {
			case 'opt-out':
				$this->strResult = '<iframe frameborder="no" width="'.$this->aryAttributes['width'].'" height="'.$this->aryAttributes['height'].'" src="'.self::$settings->getGlobalOption('piwik_url').'index.php?module=CoreAdminHome&action=optOut&language='.$this->aryAttributes['language'].'"></iframe>';
			break;
			case 'overview':
			default:
				self::includeFile('shortcodes/overview');
		}
		return $this->strResult;
	}
	
	/**
	 * Add metaboxes to posts
	 */
	function postMetaboxes() {
		add_action('add_meta_boxes', array(&$this, 'postAddMetaboxes'));
		add_action('save_post', array(&$this, 'postCustomvarsSave'), 10, 2);
	}

	/**
	 * Create post meta boxes
	 */
	function postAddMetaboxes() {
		add_meta_box(
			'wp-piwik_post_customvars',
			__('Piwik Custom Variables', 'wp-piwik'),
			array(&$this, 'postCustomvars'),
			'post',
			'side',
			'default'
		);
	}
	
	/**
	 * Display custom variables meta box
	 */
	function postCustomvars($objPost, $objBox ) {
		wp_nonce_field(basename( __FILE__ ), 'wp-piwik_post_customvars_nonce'); ?>
	 	<table>
	 		<tr><th></th><th><?php _e('Name', 'wp-piwik'); ?></th><th><?php _e('Value', 'wp-piwik'); ?></th></tr>
	 	<?php for($i = 1; $i <= 5; $i++) { ?>
		 	<tr>
		 		<th><label for="wp-piwik_customvar1"><?php echo $i; ?>: </label></th>
		 		<td><input class="widefat" type="text" name="wp-piwik_custom_cat<?php echo $i; ?>" value="<?php echo esc_attr(get_post_meta($objPost->ID, 'wp-piwik_custom_cat'.$i, true ) ); ?>" size="200" /></td>
		 		<td><input class="widefat" type="text" name="wp-piwik_custom_val<?php echo $i; ?>" value="<?php echo esc_attr(get_post_meta($objPost->ID, 'wp-piwik_custom_val'.$i, true ) ); ?>" size="200" /></td>
		 	</tr>
		<?php } ?>
		</table>
		<p><?php _e('Set custom variables for a page view', 'wp-piwik'); ?>. (<a href="http://piwik.org/docs/custom-variables/"><?php _e('More information', 'wp-piwik'); ?></a>.)</p>
		<?php 
	}

	/**
	 * Save post custom variables
	 */
	function postCustomvarsSave($intID, $objPost) {
		// Verify the nonce before proceeding.
		if (!isset( $_POST['wp-piwik_post_customvars_nonce'] ) || !wp_verify_nonce( $_POST['wp-piwik_post_customvars_nonce'], basename( __FILE__ ) ) )
			return $intID;
		// Get post type object
		$objPostType = get_post_type_object($objPost->post_type);
		// Check if the current user has permission to edit the post.
		if (!current_user_can($objPostType->cap->edit_post, $intID))
			return $intID;
		$aryNames = array('cat', 'val');
		for ($i = 1; $i <= 5; $i++)
			for ($j = 0; $j <= 1; $j++) {
				// Get data
				$strMetaVal = (isset($_POST['wp-piwik_custom_'.$aryNames[$j].$i])?htmlentities($_POST['wp-piwik_custom_'.$aryNames[$j].$i]):'');
				// Create key
				$strMetaKey = 'wp-piwik_custom_'.$aryNames[$j].$i;
				// Get the meta value of the custom field key
				$strCurVal = get_post_meta($intID, $strMetaKey, true);
				// Add meta val:
				if ($strMetaVal && '' == $strCurVal)
					add_post_meta($intID, $strMetaKey, $strMetaVal, true);
				// Update meta val:
				elseif ($strMetaVal && $strMetaVal != $strCurVal)
					update_post_meta($intID, $strMetaKey, $strMetaVal);
				// Delete meta val:
				elseif (''==$strMetaVal && $strCurVal)
					delete_post_meta($intID, $strMetaKey, $strCurVal);
			}
	}

	/**
	 * Add pages to admin menu
	 */
	function buildAdminMenu() {
		// Show stats dashboard page if WP-Piwik is configured
		if (self::isConfigured()) {
			// Add dashboard page
			$this->intStatsPage = add_dashboard_page(
				__('Piwik Statistics', 'wp-piwik'), 
				self::$settings->getGlobalOption('plugin_display_name'), 
				'wp-piwik_read_stats',
				'wp-piwik_stats',
				array($this, 'showStats')
			);
			// Add required scripts
			add_action('admin_print_scripts-'.$this->intStatsPage, array($this, 'loadStatsScripts'));
			// Add required styles
			add_action('admin_print_styles-'.$this->intStatsPage, array($this, 'addAdminStyle'));
			// Add required header tags
			add_action('admin_head-'.$this->intStatsPage, array($this, 'addAdminHeaderStats'));
			// Stats page onload callback
			add_action('load-'.$this->intStatsPage, array(&$this, 'onloadStatsPage'));
		}
		if (!is_plugin_active_for_network('wp-piwik/wp-piwik.php')) {
			// Add options page
			$intOptionsPage = add_options_page(
				self::$settings->getGlobalOption('plugin_display_name'),
				self::$settings->getGlobalOption('plugin_display_name'), 
				'activate_plugins',
				__FILE__,
				array($this, 'showSettings')
			);
			// Add required scripts
			add_action('admin_print_scripts-'.$this->intStatsPage, array($this, 'loadSettingsScripts'));
			// Add required header tags
			add_action('admin_head-'.$intOptionsPage, array($this, 'addAdminHeaderSettings'));
			// Add styles required by options page
			add_action('admin_print_styles-'.$intOptionsPage, array($this, 'addAdminStyle'));
		}
	}

	/**
	 * Add pages to network admin menu
	 */
	function buildNetworkAdminMenu() {
		// Show stats dashboard page if WP-Piwik is configured
		if (self::isConfigured()) {
			// Add dashboard page
			$this->intStatsPage = add_dashboard_page(
				__('Piwik Statistics', 'wp-piwik'), 
				self::$settings->getGlobalOption('plugin_display_name'), 
				'manage_sites',
				'wp-piwik_stats',
				array($this, 'showStatsNetwork')
			);
			// Add required scripts
			add_action('admin_print_scripts-'.$this->intStatsPage, array($this, 'loadStatsScripts'));
			// Add required styles
			add_action('admin_print_styles-'.$this->intStatsPage, array($this, 'addAdminStyle'));
			// Add required header tags
			add_action('admin_head-'.$this->intStatsPage, array($this, 'addAdminHeaderStats'));
			// Stats page onload callback
			add_action('load-'.$this->intStatsPage, array(&$this, 'onloadStatsPage'));
		}
        $intOptionsPage = add_submenu_page(
			'settings.php',
			self::$settings->getGlobalOption('plugin_display_name'),
			self::$settings->getGlobalOption('plugin_display_name'),
			'manage_sites',
			__FILE__,
			array($this, 'showSettings')
		);
		
		// Add styles required by options page
		add_action('admin_print_styles-'.$intOptionsPage, array($this, 'addAdminStyle'));
		add_action('admin_head-'.$intOptionsPage, array($this, 'addAdminHeaderSettings'));
	}
	
	/**
	 * Support two columns 
	 * seen in Heiko Rabe's metabox demo plugin 
	 * 
	 * @see http://tinyurl.com/5r5vnzs 
	 */ 
	function onScreenLayoutColumns($aryColumns, $strScreen) {		
		if ($strScreen == $this->intStatsPage)
			$aryColumns[$this->intStatsPage] = 3;
		return $aryColumns;
	}
	
	/**
	 * Add widgets to WordPress dashboard
	 */
	function extendWordPressDashboard() {
		// Is user allowed to see stats?
		if (current_user_can('wp-piwik_read_stats')) {
			// TODO: Use bitmask here
			// Add data widget if enabled
			if (self::$settings->getGlobalOption('dashboard_widget'))
				$this->addWordPressDashboardWidget();
			// Add chart widget if enabled
			if (self::$settings->getGlobalOption('dashboard_chart')) {				
				// Add required scripts
				add_action('admin_print_scripts-index.php', array($this, 'loadStatsScripts'));
				// Add required styles
				add_action('admin_print_styles-index.php', array($this, 'addAdminStyle'));
				// Add required header tags
				add_action('admin_head-index.php', array($this, 'addAdminHeaderStats'));
				$this->addWordPressDashboardChart();
			}
			// Add SEO widget if enabled
			if (self::$settings->getGlobalOption('dashboard_seo'))
				$this->addWordPressDashboardSEO();
		}
	}
	
	/**
	 * Add widgets to WordPress Toolbar
	 */
	public function extendWordPressToolbar(&$objToolbar) {
		// Is user allowed to see stats?
		if (current_user_can('wp-piwik_read_stats')) {
			$aryUnique = $this->callPiwikAPI('VisitsSummary.getUniqueVisitors','day','last30',null);
			if (!is_array($aryUnique)) $aryUnique = array();
			$strGraph = '<script type="text/javascript">';	
			$strGraph .= "var \$jSpark = jQuery.noConflict();\$jSpark(function() {var piwikSparkVals=[".implode(',',$aryUnique)."];\$jSpark('.wp-piwik_dynbar').sparkline(piwikSparkVals, {type: 'bar', barColor: '#ccc', barWidth:2});});";
			$strGraph .= '</script>';
			$strGraph .= '<span class="wp-piwik_dynbar">Loading...</span>';
			$objToolbar->add_menu(array(
				'id' => 'wp-piwik_stats',
				'title' => $strGraph,
				'href' => admin_url().'?page=wp-piwik_stats'
			));
		}		
	}

	/**
     * Add a data widget to the WordPress dashboard
	 */
	function addWordPressDashboardWidget() {
		$aryConfig = array(
			'params' => array('period' => 'day','date'  => self::$settings->getGlobalOption('dashboard_widget'),'limit' => null),
			'inline' => true,			
		);
		$strFile = 'overview';
		add_meta_box(
				'wp-piwik_stats-dashboard-overview', 
				self::$settings->getGlobalOption('plugin_display_name').' - '.__(self::$settings->getGlobalOption('dashboard_widget'), 'wp-piwik'), 
				array(&$this, 'createDashboardWidget'), 
				'dashboard', 
				'side', 
				'high',
				array('strFile' => $strFile, 'aryConfig' => $aryConfig)
			);
	}
	
	/**
	 * Add a visitor chart to the WordPress dashboard
	 */
	function addWordPressDashboardChart() {
		$aryConfig = array(
			'params' => array('period' => 'day','date'  => 'last30','limit' => null),
			'inline' => true,			
		);
		$strFile = 'visitors';
		add_meta_box(
				'wp-piwik_stats-dashboard-chart', 
				self::$settings->getGlobalOption('plugin_display_name').' - '.__('Visitors', 'wp-piwik'), 
				array(&$this, 'createDashboardWidget'), 
				'dashboard', 
				'side', 
				'high',
				array('strFile' => $strFile, 'aryConfig' => $aryConfig)
			);
	}	

	/**
	 * Add a SEO widget to the WordPress dashboard
	 */
	function addWordPressDashboardSEO() {
		$aryConfig = array(
			'params' => array('period' => 'day','date'  => 'today','limit' => null),
			'inline' => true,			
		);
		$strFile = 'seo';
		add_meta_box(
				'wp-piwik_stats-dashboard-seo', 
				self::$settings->getGlobalOption('plugin_display_name').' - '.__('SEO', 'wp-piwik'), 
				array(&$this, 'createDashboardWidget'), 
				'dashboard', 
				'side', 
				'high',
				array('strFile' => $strFile, 'aryConfig' => $aryConfig)
			);
	}

	/**
	 * Add plugin meta links to plugin details
	 * 
	 * @see http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
	 */
	function setPluginMeta($strLinks, $strFile) {
		// Get plugin basename
		$strPlugin = plugin_basename(__FILE__);
		// Add link just to this plugin's details
		if ($strFile == self::$strPluginBasename) 
			return array_merge(
				$strLinks,
				array(
					sprintf('<a href="'.(is_plugin_active_for_network('wp-piwik/wp-piwik.php')?'settings':'options-general').'.php?page=%s">%s</a>', self::$strPluginBasename, __('Settings', 'wp-piwik'))
				)
			);
		// Don't affect other plugins details
		return $strLinks;
	}

	/**
	 * Load required scripts to stats page
	 */
	function loadStatsScripts() {
		// Load WP-Piwik script
		wp_enqueue_script('wp-piwik', $this->getPluginURL().'js/wp-piwik.js', array(), self::$strVersion, true);
		// Load jqPlot
		wp_enqueue_script('wp-piwik-jqplot',$this->getPluginURL().'js/jqplot/wp-piwik.jqplot.js',array('jquery'));
	}

	/**
	 * Load scripts required by Toolbar graphs
	 */
	function loadToolbarRequirements() {
		// Only load if user is allowed to see stats
		if (current_user_can('wp-piwik_read_stats')) {
			// Load Sparklines
			wp_enqueue_script('wp-piwik-sparkline',$this->getPluginURL().'js/sparkline/jquery.sparkline.min.js',array('jquery'),'2.1.1');
			// Load CSS
			wp_enqueue_style('wp-piwik', $this->getPluginURL().'css/wp-piwik-spark.css');
		}
	}

	/**
	 * Load required scripts to settings page
	 */
	function loadSettingsScripts() {
		wp_enqueue_script('jquery');
	}

	/**
	 * Load required styles to admin pages
	 */
	function addAdminStyle() {
		// Load WP-Piwik styles
		wp_enqueue_style('wp-piwik', $this->getPluginURL().'css/wp-piwik.css',array(),self::$strVersion);
	}

	/**
	 * Add required header tags to stats page
	 */
	function addAdminHeaderStats() {
		// Load jqPlot IE compatibility script
		echo '<!--[if IE]><script language="javascript" type="text/javascript" src="'.$this->getPluginURL().'js/jqplot/excanvas.min.js"></script><![endif]-->';
		// Load jqPlot styles
		echo '<link rel="stylesheet" href="'.$this->getPluginURL().'js/jqplot/jquery.jqplot.min.css" type="text/css"/>';
		echo '<script type="text/javascript">var $j = jQuery.noConflict();</script>';
	}

	/**
	 * Add required header tags to settings page
	 */
	function addAdminHeaderSettings() {
		echo '<script type="text/javascript">var $j = jQuery.noConflict();</script>';
	}
	
	/**
	 * Get this plugin's URL
	 */
	function getPluginURL() {
		// Return plugins URL + /wp-piwik/
		return trailingslashit(plugins_url().'/wp-piwik/');
	}

	/**
	 * Call REST API
	 * 
	 * @param $strURL Remote file URL
	 */
	function callREST($strURL) {
		$strPiwikURL = self::$settings->getGlobalOption('piwik_url');
		if (substr($strPiwikURL, -1, 1) != '/') $strPiwikURL .= '/';
		$strURL = $strPiwikURL.'?module=API'.$strURL;
		// Use cURL if available	
		if (function_exists('curl_init')) {
			// Init cURL
			$c = curl_init($strURL);
			// Disable SSL peer verification if asked to
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, !self::$settings->getGlobalOption('disable_ssl_verify'));
			// Set user agent
			curl_setopt($c, CURLOPT_USERAGENT, self::$settings->getGlobalOption('piwik_useragent')=='php'?ini_get('user_agent'):self::$settings->getGlobalOption('piwik_useragent_string'));
			// Configure cURL CURLOPT_RETURNTRANSFER = 1
			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			// Configure cURL CURLOPT_HEADER = 0 
			curl_setopt($c, CURLOPT_HEADER, 0);
			// Set cURL timeout
			curl_setopt($c, CURLOPT_TIMEOUT, self::$settings->getGlobalOption('connection_timeout'));
			if (WP_HTTP_Proxy::is_enabled() && WP_HTTP_Proxy::send_through_proxy($strURL)) {
				curl_setopt($c, CURLOPT_PROXY, WP_HTTP_Proxy::host());
				curl_setopt($c, CURLOPT_PROXYPORT, WP_HTTP_Proxy::port());
				if (WP_HTTP_Proxy::use_authentication())
					curl_setopt($c, CURLOPT_PROXYUSERPWD, WP_HTTP_Proxy::username().':'.WP_HTTP_Proxy::password());
			}
			// Get result
			$strResult = curl_exec($c);
			// Close connection			
			curl_close($c);
		// cURL not available but url fopen allowed
		} elseif (ini_get('allow_url_fopen')) {
			// Set timeout
			$resContext = stream_context_create(array('http'=>array('timeout' => self::$settings->getGlobalOption('connection_timeout'))));
			// Get file using file_get_contents
			$strResult = @file_get_contents($strURL, false, $strContext);
		// Error: Not possible to get remote file
		} else $strResult = serialize(array(
				'result' => 'error',
				'message' => 'Remote access to Piwik not possible. Enable allow_url_fopen or CURL.'
			));
		// Return result
		return $strResult;
	}
	
	/**
	 * Call PHP API
	 * 
	 * @param $strParams API call params
	 */
	function callPHP($strParams) {
		if (PIWIK_INCLUDE_PATH === FALSE)
			return serialize(array('result' => 'error', 'message' => __('Could not resolve','wp-piwik').' &quot;'.htmlentities(self::$settings->getGlobalOption('piwik_path')).'&quot;: '.__('realpath() returns false','wp-piwik').'.'));
		if (file_exists(PIWIK_INCLUDE_PATH . "/index.php"))
			require_once PIWIK_INCLUDE_PATH . "/index.php";
		if (file_exists(PIWIK_INCLUDE_PATH . "/core/API/Request.php"))
			require_once PIWIK_INCLUDE_PATH . "/core/API/Request.php";
		if (class_exists('Piwik_FrontController'))
			Piwik_FrontController::getInstance()->init();
		// Add Piwik URL to params
		$strParams .= '&piwikUrl='.urlencode(self::$settings->getGlobalOption('piwik_url'));
		// This inits the API Request with the specified parameters
		if (class_exists('Piwik_API_Request'))
			$objRequest = new Piwik_API_Request($strParams);
		else return NULL;
		// Calls the API and fetch XML data back
		return $objRequest->process();		
	}
		
	/**
	 * Get remote file
	 * 
	 * @param String $strURL Remote file URL
	 */
	function getRemoteFile($strURL) {
		if (self::$settings->getGlobalOption('piwik_mode') == 'php')
			return $this->callPHP($strURL);
		else
			return $this->callREST($strURL);
	}

	/**
	 * Add a new site to Piwik if a new blog was requested,
	 * or get its ID by URL
	 */ 
	function addPiwikSite() {
		if (isset($_GET['wpmu_show_stats']) && is_plugin_active_for_network('wp-piwik/wp-piwik.php')) {
			self::$logger->log('Switch blog ID: '.(int) $_GET['wpmu_show_stats']);
			switch_to_blog((int) $_GET['wpmu_show_stats']);
		}
		self::$logger->log('Get the blog\'s site ID by URL: '.get_bloginfo('url'));
		// Check if blog URL already known
		$strURL = '&method=SitesManager.getSitesIdFromSiteUrl';
		$strURL .= '&url='.urlencode(get_bloginfo('url'));
		$strURL .= '&format=PHP';
		$strURL .= '&token_auth='.self::$settings->getGlobalOption('piwik_token');
		$aryResult = unserialize($this->getRemoteFile($strURL));
		if (!empty($aryResult) && isset($aryResult[0]['idsite'])) {
			self::$settings->setOption('site_id', (int) $aryResult[0]['idsite']);
		// Otherwise create new site
		} elseif (self::isConfigured() && !empty($strURL)) {
			self::$logger->log('Blog not known yet - create new site');
			$strName = get_bloginfo('name');
			if (empty($strName)) $strName = get_bloginfo('url');
			self::$settings->setOption('name', $strName);
			$strURL .= '&method=SitesManager.addSite';
			$strURL .= '&siteName='.urlencode($strName).'&urls='.urlencode(get_bloginfo('url'));
			$strURL .= '&format=PHP';
			$strURL .= '&token_auth='.self::$settings->getGlobalOption('piwik_token');
			$strResult = unserialize($this->getRemoteFile($strURL));
			if (!empty($strResult)) self::$settings->setOption('site_id', (int) $strResult);
		}
		// Store new data if site created
		if (self::$settings->getOption('site_id')) {
			self::$logger->log('Get the site\'s tracking code');
			self::$settings->setOption('tracking_code', $this->callPiwikAPI('SitesManager.getJavascriptTag'));
		} else self::$settings->getOption('tracking_code', '');
		self::$settings->save();
		if (isset($_GET['wpmu_show_stats']) && is_plugin_active_for_network('wp-piwik/wp-piwik.php')) {
			self::$logger->log('Back to current blog');
			restore_current_blog();
		}
		return array('js' => self::$settings->getOption('tracking_code'), 'id' => self::$settings->getOption('site_id'));
	}

	/**
	 * Update a site 
	 */ 
	function updatePiwikSite() {
		$strBlogURL = get_bloginfo('url');
		// Check if blog URL already known
		$strName = get_bloginfo('name');
		if (empty($strName)) $strName = $strBlogURL;
		self::$settings->setOption('name', $strName);
		$strURL = '&method=SitesManager.updateSite';
		$strURL .= '&idSite='.self::$settings->getOption('site_id');
		$strURL .= '&siteName='.urlencode($strName).'&urls='.urlencode($strBlogURL);
		$strURL .= '&format=PHP';
		$strURL .= '&token_auth='.self::$settings->getGlobalOption('piwik_token');
		$strResult = unserialize($this->getRemoteFile($strURL));		
		// Store new data
		self::$settings->getOption('tracking_code', $this->callPiwikAPI('SitesManager.getJavascriptTag'));
		self::$settings->save();
	}

	/**
	 * Apply configured Tracking Code changes
	 */
	function applyJSCodeChanges($strCode) {
		self::$logger->log('Apply tracking code changes.');
		self::$settings->setOption('last_tracking_code_update', time());
		$strCode = html_entity_decode($strCode);
		// Change code if js/index.php should be used
		if (self::$settings->getGlobalOption('track_mode') == 1) {
			$strCode = str_replace('piwik.js', 'js/', $strCode);
			$strCode = str_replace('piwik.php', 'js/', $strCode);
		} elseif (self::$settings->getGlobalOption('track_mode') == 2) {
			$strCode = str_replace('piwik.js', 'piwik.php', $strCode);
			$strURL = str_replace('https://', '://', self::$settings->getGlobalOption('piwik_url'));
			$strURL = str_replace('http://', '://', $strURL);
			$strProxy = str_replace('https://', '://', plugins_url('wp-piwik'));
			$strProxy = str_replace('http://', '://', $strProxy);
			$strCode = str_replace($strURL, $strProxy, $strCode);
		}
		$strCode = str_replace('//";','/"',$strCode);
		if (self::$settings->getGlobalOption('track_cdnurl')||self::$settings->getGlobalOption('track_cdnurlssl')) {
			$strCode = str_replace("var d=doc", "var ucdn=(('https:' == document.location.protocol) ? 'https://".(self::$settings->getGlobalOption('track_cdnurlssl')?self::$settings->getGlobalOption('track_cdnurlssl'):self::$settings->getGlobalOption('track_cdnurl'))."/' : 'http://".(self::$settings->getGlobalOption('track_cdnurl')?self::$settings->getGlobalOption('track_cdnurl'):self::$settings->getGlobalOption('track_cdnurlssl'))."/');\nvar d=doc", $strCode);
			$strCode = str_replace("g.src=u+", "g.src=ucdn+", $strCode);
		}
		// Change code if POST is forced to be used
		if (self::$settings->getGlobalOption('track_post') && self::$settings->getGlobalOption('track_mode') != 2) $strCode = str_replace("_paq.push(['trackPageView']);", "_paq.push(['setRequestMethod', 'POST']);\n_paq.push(['trackPageView']);", $strCode);
		// Change code if cookies are disabled
		if (self::$settings->getGlobalOption('disable_cookies')) $strCode = str_replace("_paq.push(['trackPageView']);", "_paq.push(['disableCookies']);\n_paq.push(['trackPageView']);", $strCode);
		// Store <noscript> code
		$aryNoscript = array();
		preg_match('/<noscript>(.*)<\/noscript>/', $strCode, $aryNoscript);
		if (isset($aryNoscript[0])) {
			if (self::$settings->getGlobalOption('track_nojavascript'))
				$aryNoscript[0] = str_replace('?idsite', '?rec=1&idsite', $aryNoscript[0]);
			self::$settings->setOption('noscript_code', $aryNoscript[0]);
		}
		// Remove <noscript> code
		$strCode = preg_replace('/<noscript>(.*)<\/noscript>/', '', $strCode);
		// Return code without empty lines
		return preg_replace('/\s+(\r\n|\r|\n)/', '$1', $strCode);
	}
	
	/**
	 * Create a WordPress dashboard widget
	 */
	function createDashboardWidget($objPost, $aryMetabox) {
		// Create description and ID
		$strDesc = $strID = '';
		$aryConfig = $aryMetabox['args']['aryConfig'];
		foreach ($aryConfig['params'] as $strParam)
			if (!empty($strParam)) {
				$strDesc .= $strParam.', ';
				$strID .= '_'.$strParam;
			}
		// Remove dots from filename
		$strFile = str_replace('.', '', $aryMetabox['args']['strFile']);
		// Finalize configuration
		$aryConf = array_merge($aryConfig, array(
			'id' => $strFile.$strID,
			'desc' => substr($strDesc, 0, -2)));
		// Include widget file
		if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'dashboard/'.$strFile.'.php'))
			include(dirname(__FILE__).DIRECTORY_SEPARATOR.'dashboard/'.$strFile.'.php');
 	}

	/**
	 * Call Piwik's API
	 */
	function callPiwikAPI($strMethod, $strPeriod='', $strDate='', $intLimit='',$bolExpanded=false, $intId = false, $strFormat = 'PHP', $strPageURL = '') {
		// Create unique cache key
		$strKey = 'wp-piwik'.md5($strMethod.'_'.$strPeriod.'_'.$strDate.'_'.$intLimit);
		// Call API if data not cached
		$result = (self::$settings->getGlobalOption('cache')?get_transient($strKey):false);
		if (false === $result) {
			$strToken = self::$settings->getGlobalOption('piwik_token');
			// If multisite stats are shown, maybe the super admin wants to show other blog's stats.
			if (is_plugin_active_for_network('wp-piwik/wp-piwik.php') && function_exists('is_super_admin') && function_exists('wp_get_current_user') && is_super_admin() && isset($_GET['wpmu_show_stats'])) {
				$aryOptions = get_blog_option((int) $_GET['wpmu_show_stats'], 'wp-piwik_settings' , array());
				if (!empty($aryOptions) && isset($aryOptions['site_id']))
					$intSite = $aryOptions['site_id'];
				else $intSite = self::$settings->getOption('site_id');
			// Otherwise use the current site's id.
			} else {
				if (!self::$settings->getOption('site_id'))
					self::addPiwikSite();
				$intSite = self::$settings->getOption('site_id');
			}
			//die($intSite);
			// Create error message if WP-Piwik isn't configured
			if (!self::isConfigured()) {
				$result = array(
					'result' => 'error',
					'message' => 'Piwik URL/path or auth token not set.'
				);
				return $result;
			}
			// Build URL			
			$strURL = '&method='.$strMethod;
			$strURL .= '&idSite='.(int)$intSite.'&period='.$strPeriod.'&date='.$strDate;
			$strURL .= '&filter_limit='.$intLimit;
			$strURL .= '&token_auth='.$strToken;
			$strURL .= '&expanded='.$bolExpanded;
			$strURL .= '&url='.urlencode(get_bloginfo('url'));
			$strURL .= '&format='.$strFormat;
			$strURL .= ($strPageURL?'&pageUrl='.urlencode($strPageURL):'');
			// Fetch data if site exists
			if (!empty($intSite) || $strMethod='SitesManager.getSitesWithAtLeastViewAccess') {
				self::$logger->log('API method: '.$strMethod.' API call: '.$strURL);
				$strResult = (string) $this->getRemoteFile($strURL);			
				$result = ($strFormat == 'PHP'?unserialize($strResult):$strResult);
				// Apply tracking code changes if configured
				if ($strMethod == 'SitesManager.getJavascriptTag' && !empty($result)) {
					$result = is_string($result)?$this->applyJSCodeChanges($result):'<!-- WP-Piwik ERROR: Tracking code not availbale -->'."\n";
				}
			// Otherwise return error message
			} else $result = array('result' => 'error', 'message' => 'Unknown site/blog.');
			if (
					$strMethod != 'SitesManager.getJavascriptTag' &&
					$strDate != 'today' && $strDate != date('Ymd') && substr($strDate, 0, 4) != 'last' &&
					self::$settings->getGlobalOption('cache') &&
					!(isset($result['result']) && $result['result'] == 'error')
				) set_transient($strKey, $result, WEEK_IN_SECONDS);
		}	
		return $result;	
	}
 	
	/* TODO: Add post stats
	 * function display_post_unique_column($aryCols) {
	 * 	$aryCols['wp-piwik_unique'] = __('Unique');
	 *        return $aryCols;
	 * }
	 *
	 * function display_post_unique_content($strCol, $intID) {
	 *	if( $strCol == 'wp-piwik_unique' ) {
	 *	}
	 * }
	 */

	function onloadStatsPage() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		$strToken = self::$settings->getGlobalOption('piwik_token');
		$strPiwikURL = self::$settings->getGlobalOption('piwik_url');
		$aryDashboard = array();
		// Set default configuration
		$arySortOrder = array(
			'side' => array(
				'overview' => array(__('Overview', 'wp-piwik'), 'day', 'yesterday'),
				'seo' => array(__('SEO', 'wp-piwik'), 'day', 'yesterday'),
				'pages' => array(__('Pages', 'wp-piwik'), 'day', 'yesterday'),
				'keywords' => array(__('Keywords', 'wp-piwik'), 'day', 'yesterday', 10),
				'websites' => array(__('Websites', 'wp-piwik'), 'day', 'yesterday', 10),
				'plugins' => array(__('Plugins', 'wp-piwik'), 'day', 'yesterday'),
				'search' => array(__('Site Search Keywords', 'wp-piwik'), 'day', 'yesterday', 10),
				'noresult' => array(__('Site Search without Results', 'wp-piwik'), 'day', 'yesterday', 10),
			),
			'normal' => array(
				'visitors' => array(__('Visitors', 'wp-piwik'), 'day', 'last30'),
				'browsers' => array(__('Browser', 'wp-piwik'), 'day', 'yesterday'),
				'browserdetails' => array(__('Browser Details', 'wp-piwik'), 'day', 'yesterday'),
				'screens' => array(__('Resolution', 'wp-piwik'), 'day', 'yesterday'),
				'systems' => array(__('Operating System', 'wp-piwik'), 'day', 'yesterday')
			)
		);
		// Don't show SEO stats if disabled
		if (!self::$settings->getGlobalOption('stats_seo'))
			unset($arySortOrder['side']['seo']);
			
		foreach ($arySortOrder as $strCol => $aryWidgets) {
			if (is_array($aryWidgets)) foreach ($aryWidgets as $strFile => $aryParams) {
					$aryDashboard[$strCol][$strFile] = array(
						'params' => array(
							'title'	 => (isset($aryParams[0])?$aryParams[0]:$strFile),
							'period' => (isset($aryParams[1])?$aryParams[1]:''),
							'date'   => (isset($aryParams[2])?$aryParams[2]:''),
							'limit'  => (isset($aryParams[3])?$aryParams[3]:'')
						)
					);
					if (isset($_GET['date']) && preg_match('/^[0-9]{8}$/', $_GET['date']) && $strFile != 'visitors')
						$aryDashboard[$strCol][$strFile]['params']['date'] = $_GET['date'];
					elseif ($strFile != 'visitors') 
						$aryDashboard[$strCol][$strFile]['params']['date'] = self::$settings->getGlobalOption('default_date');
			}
		}
		$intSideBoxCnt = $intContentBox = 0;
		foreach ($aryDashboard['side'] as $strFile => $aryConfig) {
			$intSideBoxCnt++;
			if (preg_match('/(\d{4})(\d{2})(\d{2})/', $aryConfig['params']['date'], $aryResult))
				$strDate = $aryResult[1]."-".$aryResult[2]."-".$aryResult[3];
			else $strDate = $aryConfig['params']['date'];
			add_meta_box(
				'wp-piwik_stats-sidebox-'.$intSideBoxCnt, 
				$aryConfig['params']['title'].' '.($aryConfig['params']['title']!='SEO'?__($strDate, 'wp-piwik'):''), 
				array(&$this, 'createDashboardWidget'), 
				$this->intStatsPage, 
				'side', 
				'core',
				array('strFile' => $strFile, 'aryConfig' => $aryConfig)
			);
		}
		foreach ($aryDashboard['normal'] as $strFile => $aryConfig) {
			if (preg_match('/(\d{4})(\d{2})(\d{2})/', $aryConfig['params']['date'], $aryResult))
				$strDate = $aryResult[1]."-".$aryResult[2]."-".$aryResult[3];
			else $strDate = $aryConfig['params']['date'];
			$intContentBox++;
			add_meta_box(
				'wp-piwik_stats-contentbox-'.$intContentBox, 
				$aryConfig['params']['title'].' '.($aryConfig['params']['title']!='SEO'?__($strDate, 'wp-piwik'):''),
				array(&$this, 'createDashboardWidget'), 
				$this->intStatsPage, 
				'normal', 
				'core',
				array('strFile' => $strFile, 'aryConfig' => $aryConfig)
			);
		}
	}
	
	// Open stats page as network admin
	function showStatsNetwork() {
		$this->bolNetwork = true;
		$this->showStats();
	}	
	
	function showStats() {
		// Disabled time limit if required
		if (self::$settings->getGlobalOption('disable_timelimit') && self::$settings->getGlobalOption('disable_timelimit')) 
			set_time_limit(0);
		//we need the global screen column value to be able to have a sidebar in WordPress 2.8
		global $screen_layout_columns;
		if (empty($screen_layout_columns)) $screen_layout_columns = 2;
/***************************************************************************/ ?>
<div id="wp-piwik-stats-general" class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2><?php echo (self::$settings->getGlobalOption('plugin_display_name') == 'WP-Piwik'?'Piwik '.__('Statistics', 'wp-piwik'):self::$settings->getGlobalOption('plugin_display_name')); ?></h2>
<?php /************************************************************************/
		if (is_plugin_active_for_network('wp-piwik/wp-piwik.php') && function_exists('is_super_admin') && is_super_admin() && $this->bolNetwork) {
			if (isset($_GET['wpmu_show_stats'])) {
				switch_to_blog((int) $_GET['wpmu_show_stats']);
				// TODO OPTIMIZE
			} else {
				$this->includeFile('settings/sitebrowser');
				return;
			}
			echo '<p>'.__('Currently shown stats:').' <a href="'.get_bloginfo('url').'">'.(int) $_GET['wpmu_show_stats'].' - '.get_bloginfo('name').'</a>.'.' <a href="?page=wp-piwik_stats">Show site overview</a>.</p>'."\n";			
			echo '</form>'."\n";
		}
/***************************************************************************/ ?>
	<form action="admin-post.php" method="post">
		<?php wp_nonce_field('wp-piwik_stats-general'); ?>
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
        <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
		<input type="hidden" name="action" value="save_wp-piwik_stats_general" />		
		<div id="dashboard-widgets" class="metabox-holder columns-<?php echo $screen_layout_columns; ?><?php echo 2 <= $screen_layout_columns?' has-right-sidebar':''; ?>">
				<div id='postbox-container-1' class='postbox-container'>
					<?php $meta_boxes = do_meta_boxes($this->intStatsPage, 'normal', null); ?>	
				</div>
				
				<div id='postbox-container-2' class='postbox-container'>
					<?php do_meta_boxes($this->intStatsPage, 'side', null); ?>
				</div>
				
				<div id='postbox-container-3' class='postbox-container'>
					<?php do_meta_boxes($this->intStatsPage, 'column3', null); ?>
				</div>
				
    	</div>
	</form>
</div>
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		// close postboxes that should be closed
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
		// postboxes setup
		postboxes.add_postbox_toggles('<?php echo $this->intStatsPage; ?>');
	});
	//]]>
</script>
<?php /************************************************************************/
		if (is_plugin_active_for_network('wp-piwik/wp-piwik.php') && function_exists('is_super_admin') && is_super_admin()) {
			restore_current_blog();
		}
	}

	/* Stats page changes by POST submit
	   seen in Heiko Rabe's metabox demo plugin 
	   http://tinyurl.com/5r5vnzs */
	function onStatsPageSaveChanges() {
		//user permission check
		if ( !current_user_can('manage_options') )
			wp_die( __('Cheatin&#8217; uh?') );			
		//cross check the given referer
		check_admin_referer('wp-piwik_stats');
		//process here your on $_POST validation and / or option saving
		//lets redirect the post request into get request (you may add additional params at the url, if you need to show save results
		wp_redirect($_POST['_wp_http_referer']);		
	}

    /**
     * Add tabs to settings page
     * See http://wp.smashingmagazine.com/2011/10/20/create-tabs-wordpress-settings-pages/
     */
    function showSettingsTabs($bolFull = true, $strCurr = 'homepage') {
        $aryTabs = ($bolFull?array(
            'homepage' => __('Home','wp-piwik'),
            'piwik' => __('Piwik Settings','wp-piwik'),
            'tracking' => __('Tracking','wp-piwik'),
            'views' => __('Statistics','wp-piwik'),
            'support' => __('Support','wp-piwik'),
            'credits' => __('Credits','wp-piwik')
        ):array(
            'piwik' => __('Piwik Settings','wp-piwik'),
            'support' => __('Support','wp-piwik'),
            'credits' => __('Credits','wp-piwik')
        ));
		if (empty($strCurr)) $strCurr = 'homepage';
		elseif (!isset($aryTabs[$strCurr]) && $strCurr != 'sitebrowser') $strCurr = 'piwik';
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach($aryTabs as $strTab => $strName) {
            $strClass = ($strTab == $strCurr?' nav-tab-active':'');
            echo '<a class="nav-tab'.$strClass.'" href="?page=wp-piwik/wp-piwik.php&tab='.$strTab.'">'.$strName.'</a>';
        }
        echo '</h2>';
		return $strCurr;
    }
		
	/**
	 * Apply & store new settings
	 */
	function applySettings() {
		$strTab = (isset($_GET['tab'])?$_GET['tab']:'homepage');
		self::$logger->log('Apply changes: '.$strTab);
		switch ($strTab) {
			case 'views':
				self::$settings->setGlobalOption('plugin_display_name', (!empty($_POST['wp-piwik_displayname'])?htmlentities($_POST['wp-piwik_displayname']):'WP-Piwk'));
				self::$settings->setGlobalOption('dashboard_widget',(isset($_POST['wp-piwik_dbwidget'])?$_POST['wp-piwik_dbwidget']:0));
				self::$settings->setGlobalOption('dashboard_chart',(isset($_POST['wp-piwik_dbchart'])?$_POST['wp-piwik_dbchart']:false));
				self::$settings->setGlobalOption('dashboard_seo',(isset($_POST['wp-piwik_dbseo'])?$_POST['wp-piwik_dbseo']:false));
				self::$settings->setGlobalOption('stats_seo',(isset($_POST['wp-piwik_statsseo'])?$_POST['wp-piwik_statsseo']:false));
				self::$settings->setGlobalOption('piwik_shortcut', (isset($_POST['wp-piwik_piwiklink'])?$_POST['wp-piwik_piwiklink']:false));
				self::$settings->setGlobalOption('default_date', (isset($_POST['wp-piwik_default_date'])?$_POST['wp-piwik_default_date']:'yesterday'));
				self::$settings->setGlobalOption('capability_read_stats', (isset($_POST['wp-piwik_displayto'])?$_POST['wp-piwik_displayto']:array()));
				self::$settings->setGlobalOption('disable_timelimit', (isset($_POST['wp-piwik_disabletimelimit'])?$_POST['wp-piwik_disabletimelimit']:false));
				self::$settings->setGlobalOption('toolbar', (isset($_POST['wp-piwik_toolbar'])?$_POST['wp-piwik_toolbar']:false));
				self::$settings->setGlobalOption('shortcodes', (isset($_POST['wp-piwik_shortcodes'])?$_POST['wp-piwik_shortcodes']:false));
			break;
			case 'tracking':
				self::$settings->setGlobalOption('add_tracking_code', (isset($_POST['wp-piwik_addjs'])?$_POST['wp-piwik_addjs']:false));
				self::$settings->setGlobalOption('track_404', (isset($_POST['wp-piwik_404'])?$_POST['wp-piwik_404']:false));
				self::$settings->setGlobalOption('track_search', (isset($_POST['wp-piwik_search'])?$_POST['wp-piwik_search']:false));
				self::$settings->setGlobalOption('track_mode', (isset($_POST['wp-piwik_trackingmode'])?(int)$_POST['wp-piwik_trackingmode']:0));
				self::$settings->setGlobalOption('track_post', (isset($_POST['wp-piwik_reqpost'])?$_POST['wp-piwik_reqpost']:false));
				self::$settings->setGlobalOption('track_proxy', (isset($_POST['wp-piwik_proxy'])?$_POST['wp-piwik_proxy']:false));
				self::$settings->setGlobalOption('track_cdnurl', trim(isset($_POST['wp-piwik_cdnurl'])?$_POST['wp-piwik_cdnurl']:''));				
				self::$settings->setGlobalOption('track_cdnurlssl', trim(isset($_POST['wp-piwik_cdnurlssl'])?$_POST['wp-piwik_cdnurlssl']:self::$settings->getGlobalOption('track_cdnurl')));
				self::$settings->setGlobalOption('track_noscript', (isset($_POST['wp-piwik_noscript'])?$_POST['wp-piwik_noscript']:false));
				self::$settings->setGlobalOption('track_nojavascript', (isset($_POST['wp-piwik_nojavascript'])?$_POST['wp-piwik_nojavascript']:false));
				self::$settings->setGlobalOption('capability_stealth', (isset($_POST['wp-piwik_filter'])?$_POST['wp-piwik_filter']:array()));
				self::$settings->setGlobalOption('cache', (isset($_POST['wp-piwik_cache'])?$_POST['wp-piwik_cache']:false));
				self::$settings->setGlobalOption('disable_cookies', (isset($_POST['wp-piwik_disable_cookies'])?$_POST['wp-piwik_disable_cookies']:false));
				self::$settings->setOption('tracking_code', $this->callPiwikAPI('SitesManager.getJavascriptTag'));
			break;
			case 'piwik':
				self::$settings->setGlobalOption('piwik_token', (isset($_POST['wp-piwik_token'])?$_POST['wp-piwik_token']:''));
				self::$settings->setGlobalOption('piwik_url', self::checkURL((isset($_POST['wp-piwik_url'])?$_POST['wp-piwik_url']:'')));
				self::$settings->setGlobalOption('piwik_path', (isset($_POST['wp-piwik_path']) && !empty($_POST['wp-piwik_path'])?realpath($_POST['wp-piwik_path']):''));
				self::$settings->setGlobalOption('piwik_mode', (isset($_POST['wp-piwik_mode'])?$_POST['wp-piwik_mode']:'http'));
				self::$settings->setGlobalOption('piwik_useragent', (isset($_POST['wp-piwik_useragent'])?$_POST['wp-piwik_useragent']:'php'));
				self::$settings->setGlobalOption('connection_timeout', (isset($_POST['wp-piwik_timeout'])?(int)$_POST['wp-piwik_timeout']:5));
				self::$settings->setGlobalOption('piwik_useragent_string', (isset($_POST['wp-piwik_useragent_string'])?$_POST['wp-piwik_useragent_string']:'WP-Piwik'));
				self::$settings->setGlobalOption('disable_ssl_verify', (isset($_POST['wp-piwik_disable_ssl_verify'])?$_POST['wp-piwik_disable_ssl_verify']:false));
				if (!is_plugin_active_for_network('wp-piwik/wp-piwik.php')) {
					self::$settings->setGlobalOption('auto_site_config', (isset($_POST['wp-piwik_auto_site_config'])?$_POST['wp-piwik_auto_site_config']:false));
					if (!self::$settings->getGlobalOption('auto_site_config'))
						self::$settings->setOption('site_id', (isset($_POST['wp-piwik_siteid'])?$_POST['wp-piwik_siteid']:self::$settings->getOption('site_id')));
				} else self::$settings->setGlobalOption('auto_site_config', true);
			break;
		}
		if (self::$settings->getGlobalOption('auto_site_config') && self::isConfigured()) {
			if (self::$settings->getGlobalOption('piwik_mode') == 'php' && !defined('PIWIK_INCLUDE_PATH')) 
				self::definePiwikConstants();
			$aryReturn = $this->addPiwikSite();
			self::$settings->getOption('tracking_code', $aryReturn['js']);
			self::$settings->getOption('site_id', $aryReturn['id']);
		}
		self::$settings->setGlobalOption('last_settings_update', time());
	}

	/**
	 * Check & prepare URL
	 */
	static function checkURL($strURL) {
		if (empty($strURL)) return '';
		if (substr($strURL, -1, 1) != '/' && substr($strURL, -10, 10) != '/index.php') 
			$strURL .= '/';
		return $strURL;
	}
	
	/**
	 * Show settings page
	 */
	function showSettings() {
		// Define globals and get request vars
		global $pagenow;
		$strTab = (isset($_GET['tab'])?$_GET['tab']:'homepage');
		// Show update message if stats saved
		if (isset($_POST['wp-piwik_settings_submit']) && $_POST['wp-piwik_settings_submit'] == 'Y')
			echo '<div id="message" class="updated fade"><p>'.__('Changes saved','wp-piwik').'</p></div>';
		// Show settings page title
		echo '<div class="wrap"><h2>'.self::$settings->getGlobalOption('plugin_display_name').' '.__('Settings', 'wp-piwik').'</h2>';
		// Show tabs
		$strTab = $this->showSettingsTabs(self::isConfigured(), $strTab);
		if ($strTab != 'sitebrowser') {
/***************************************************************************/ ?>
		<div class="wp-piwik-donate">
			<p><strong><?php _e('Donate','wp-piwik'); ?></strong></p>
			<p><?php _e('If you like WP-Piwik, you can support its development by a donation:', 'wp-piwik'); ?></p>
			<div>
				<script type="text/javascript">
					var flattr_url = 'http://www.braekling.de/wp-piwik-wpmu-piwik-wordpress';
				</script>
				<script src="http<?php echo (self::isSSL()?'s':''); ?>://api.flattr.com/button/load.js" type="text/javascript"></script>
			</div>
			<div>Paypal
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick" />
					<input type="hidden" name="hosted_button_id" value="6046779" />
					<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online." />
					<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1" />
				</form>
			</div>
			<div>
				<a href="http://www.amazon.de/gp/registry/wishlist/111VUJT4HP1RA?reveal=unpurchased&amp;filter=all&amp;sort=priority&amp;layout=standard&amp;x=12&amp;y=14"><?php _e('My Amazon.de wishlist', 'wp-piwik'); ?></a>
			</div>
			<div>
				<?php _e('Please don\'t forget to vote the compatibility at the','wp-piwik'); ?> <a href="http://wordpress.org/extend/plugins/wp-piwik/">WordPress.org Plugin Directory</a>. 
			</div>
		</div>
<?php /***************************************************************************/
		}
		echo '<form class="'.($strTab != 'sitebrowser'?'wp-piwik-settings':'').'" method="post" action="'.admin_url(($pagenow == 'settings.php'?'network/':'').$pagenow.'?page=wp-piwik/wp-piwik.php&tab='.$strTab).'">';
		echo '<input type="hidden" name="action" value="save_wp-piwik_settings" />';
		wp_nonce_field('wp-piwik_settings');
		// Show settings
		if (($pagenow == 'options-general.php' || $pagenow == 'settings.php') && $_GET['page'] == 'wp-piwik/wp-piwik.php') {
			echo '<table class="wp-piwik-form-table form-table">';
			// Get tab contents
			require_once('settings/'.$strTab.'.php');				
		// Show submit button
			if (!in_array($strTab, array('homepage','credits','support','sitebrowser')))
				echo '<tr><td><p class="submit" style="clear: both;padding:0;margin:0"><input type="submit" name="Submit"  class="button-primary" value="'.__('Save settings', 'wp-piwik').'" /><input type="hidden" name="wp-piwik_settings_submit" value="Y" /></p></td></tr>';
			echo '</table>';
		}
		// Close form
		echo '</form></div>';
	}

	/**
	 * Check if SSL is used
	 */
	private static function isSSL() {
		return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
	}

	/**
	 * Show an error message extended by a support site link
	 */
	private static function showErrorMessage($strMessage) {
		echo '<strong class="wp-piwik-error">'.__('An error occured', 'wp-piwik').':</strong> '.$strMessage.' [<a href="'.(is_plugin_active_for_network('wp-piwik/wp-piwik.php')?'network/settings':'options-general').'.php?page=wp-piwik/wp-piwik.php&tab=support">'.__('Support','wp-piwik').'</a>]';
	}

	/**
	 * Read a RSS feed
	 */
	private static function readRSSFeed($strFeedURL, $intCount = 5) {
 		$aryResult = array();
		if (function_exists('simplexml_load_file') && !empty($strFeedURL)) {
			$objXML = @simplexml_load_file($strFeedURL);
			if (empty($strFeedURL) || !$objXML || !isset($objXML->channel[0]->item))
    			return array(array('title' => 'Can\'t read RSS feed.','url' => $strFeedURL));
 			foreach($objXML->channel[0]->item as $objItem) {
    			if( $intCount-- == 0 ) break;
    			$aryResult[] = array('title' => $objItem->title[0], 'url' => $objItem->link[0]);
			}
		}
		return $aryResult;
	}

	/**
	 * Execute test script
	 */
	private static function loadTestscript() {
		require_once('debug/testscript.php');
	}
	
	/**
	 * Get a blog's piwik ID
	 */
	public static function getSiteID($intBlogID = null) {
		$intResult = self::$settings->getOption('site_id');
		if (is_plugin_active_for_network('wp-piwik/wp-piwik.php') && !empty($intBlogID)) {
			$aryResult = get_blog_option($intBlogID, 'wp-piwik_settings');
			$intResult = $aryResult['site_id'];
		}
		return (is_int($intResult)?$intResult:'n/a');
	}
	
	/**
	 * Is WP-Piwik configured?
	 */
	public static function isConfigured() {
		return (
			self::$settings->getGlobalOption('piwik_token') 
			&& (
				(
					(self::$settings->getGlobalOption('piwik_mode') == 'http') && (self::$settings->getGlobalOption('piwik_url'))
				) || (
					(self::$settings->getGlobalOption('piwik_mode') == 'php') && (self::$settings->getGlobalOption('piwik_path'))
				)
			)
		);
	}
	
	/**
	 * Set Piwik PHP API constants
	 */
	private static function definePiwikConstants() {
		define('PIWIK_INCLUDE_PATH', self::$settings->getGlobalOption('piwik_path'));
		define('PIWIK_USER_PATH', self::$settings->getGlobalOption('piwik_path'));
		define('PIWIK_ENABLE_DISPATCH', false);
		define('PIWIK_ENABLE_ERROR_HANDLER', false);
		define('PIWIK_ENABLE_SESSION_START', false);
	}
	
	private function openLogger() {
		switch (WP_PIWIK_ACTIVATE_LOGGER) {
			case 2:
				require_once('classes/WP_Piwik_Logger_File.php');
				self::$logger = new WP_Piwik_Logger_File(__CLASS__);
			break;
			default:
				require_once('classes/WP_Piwik_Logger_Dummy.php');
				self::$logger = new WP_Piwik_Logger_Dummy(__CLASS__);
		}
	}

	private function closeLogger() {
		self::$logger = null;
	}

	private function openSettings() {
		$this->includeFile('classes/WP_Piwik_Settings');
		self::$settings = new WP_Piwik_Settings(self::$logger);
	}

}

}

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php');

if (class_exists('wp_piwik'))
	$GLOBALS['wp_piwik'] = new wp_piwik();

/* EOF */