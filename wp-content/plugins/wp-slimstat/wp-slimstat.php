<?php
/*
Plugin Name: WP SlimStat
Plugin URI: http://wordpress.org/extend/plugins/wp-slimstat/
Description: A powerful real-time web analytics plugin for Wordpress.
version: 3.2.1
Author: Camu
Author URI: http://slimstat.getused.to.it/
*/

if (!empty(wp_slimstat::$options)) return true;

class wp_slimstat{
	public static $version = '3.2.1';
	public static $options = array();
	
	protected static $data_js = array('id' => -1);
	protected static $stat = array();
	protected static $browser = array();

	/**
	 * Initializes variables and actions
	 */
	public static function init(){
		// Load all the settings
		self::$options = get_option('slimstat_options', array());
		self::$options = apply_filters('slimstat_init_options', self::$options);
		if (empty(self::$options)) self::init_options();

		if (!is_admin()){
			// Is server-side tracking active?
			if (self::$options['is_tracking'] == 'yes' && self::$options['javascript_mode'] != 'yes'){
				add_action('wp', array(__CLASS__, 'slimtrack'), 5);
				if (self::$options['track_users'] == 'yes') add_action('login_init', array(__CLASS__, 'slimtrack'), 10);
			}

			// WP SlimStat tracks screen resolutions, outbound links and other client-side information using javascript
			if ((self::$options['enable_javascript'] == 'yes' || self::$options['javascript_mode'] == 'yes') && self::$options['is_tracking'] == 'yes'){
				add_action('wp', array(__CLASS__, 'wp_slimstat_enqueue_tracking_script'), 15);
				if (self::$options['track_users'] == 'yes') add_action('login_enqueue_scripts', array(__CLASS__, 'wp_slimstat_enqueue_tracking_script'), 10);
			}
		}

		// Add a dropdown menu to the admin bar
		if (self::$options['use_separate_menu'] != 'yes' && is_admin_bar_showing()){
			add_action('admin_bar_menu', array(__CLASS__, 'wp_slimstat_adminbar'), 100);
		}

		// Create a hook to use with the daily cron job
		add_action('wp_slimstat_purge', array(__CLASS__, 'wp_slimstat_purge'));
	}
	// end init

	/**
	 * Ajax Tracking
	 */
	public static function slimtrack_js(){
		$data_string = base64_decode($_REQUEST['data']);
		if ($data_string === false){
			do_action('slimstat_track_exit_101');
			exit('-101.0');
		}

		// Parse the information we received
		parse_str($data_string, self::$data_js);
		self::$data_js = apply_filters('slimstat_filter_pageview_data_js', self::$data_js);

		if (empty(self::$data_js['ci']) && empty(self::$data_js['id'])){
			do_action('slimstat_track_exit_102');
			exit('-102.0');
		}

		if (!empty(self::$data_js['ci'])){
			list(self::$data_js['ci'], $nonce) = explode('.', self::$data_js['ci']);
			if ($nonce != md5(self::$data_js['ci'].self::$options['secret'])){
				do_action('slimstat_track_exit_103');
				exit('-103.0');
			}
		}
		else{
			list(self::$data_js['id'], $nonce) = explode('.', self::$data_js['id']);
			if ($nonce != md5(self::$data_js['id'].self::$options['secret'])){
				do_action('slimstat_track_exit_104');
				exit('-104.0');
			}
			self::$stat['id'] = self::$data_js['id'];

			// This script can be called to track outbound links
			if (!empty(self::$data_js['obr'])){
				self::$stat['outbound_resource'] = strip_tags(trim(self::$data_js['obr']));
				self::$stat['outbound_domain'] = !empty(self::$data_js['obd'])?strip_tags(self::$data_js['obd']):'';
				if (strpos(self::$stat['outbound_resource'], '://') == false && substr(self::$stat['outbound_resource'], 0, 1) != '/' && substr(self::$stat['outbound_resource'], 0, 1) != '#'){
					self::$stat['outbound_resource'] = '/'.self::$stat['outbound_resource'];
				}
				self::$stat['notes'] = !empty(self::$data_js['no'])?strip_tags(stripslashes(trim(self::$data_js['no']))):'';
				self::$stat['position'] = !empty(self::$data_js['po'])?strip_tags(trim(self::$data_js['po'])):'';
				self::$stat['type'] = isset(self::$data_js['ty'])?abs(intval(self::$data_js['ty'])):0;

				$timezone = get_option('timezone_string');
				if (!empty($timezone)) date_default_timezone_set($timezone);
				$lt = localtime();
				if (!empty($timezone)) date_default_timezone_set('UTC');
				self::$stat['dt'] = mktime($lt[2], $lt[1], $lt[0], $lt[4]+1, $lt[3], $lt[5]+1900);

				self::insert_row(self::$stat, $GLOBALS['wpdb']->prefix.'slim_outbound');

				do_action('slimstat_track_success_outbound', self::$stat);
				exit(self::$stat['id'].'.'.md5(self::$stat['id'].self::$options['secret']));
			}
		}

		// Track client-side information (screen resolution, plugins, etc)
		$screenres = array(
			'resolution' => (!empty(self::$data_js['sw']) && !empty(self::$data_js['sh']))?self::$data_js['sw'].'x'.self::$data_js['sh']:'',
			'colordepth' => !empty(self::$data_js['cd'])?self::$data_js['cd']:'',
			'antialias' => !empty(self::$data_js['aa'])?intval(self::$data_js['aa']):0
		);
		$screenres = apply_filters('slimstat_filter_pageview_screenres', $screenres, self::$stat);

		// Now we insert the new screen resolution in the lookup table, if it doesn't exist
		self::$stat['screenres_id'] = self::maybe_insert_row($screenres, $GLOBALS['wpdb']->base_prefix.'slim_screenres', 'screenres_id');
		self::$stat['plugins'] = !empty(self::$data_js['pl'])?substr(str_replace('|', ',', self::$data_js['pl']), 0, -1):'';

		// If Javascript mode is enabled, record this pageview
		if (self::$options['javascript_mode'] == 'yes'){
			self::slimtrack();
		}
		else{
			self::_set_visit_id(true);
			
			$GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("
				UPDATE {$GLOBALS['wpdb']->prefix}slim_stats
				SET screenres_id = %s, plugins = %s
				WHERE id = %d", self::$stat['screenres_id'], self::$stat['plugins'], self::$stat['id']));
		}

		// Was this pageview tracked?
		if (self::$stat['id'] <= 0){
			$abs_error_code = abs(self::$stat['id']);
			switch ($abs_error_code){
				case '212':
					do_action('slimstat_track_exit_'.$abs_error_code, self::$stat, self::$browser);
					break;
				default:
					do_action('slimstat_track_exit_'.$abs_error_code, self::$stat);
			}
			exit(self::$stat['id'].'.0');
		}

		// Send the ID back to Javascript to track future interactions
		do_action('slimstat_track_success');
		exit(self::$stat['id'].'.'.md5(self::$stat['id'].self::$options['secret']));
	}

	/**
	 * Core tracking functionality
	 */
	public static function slimtrack($_argument = ''){
		self::$stat['dt'] = date_i18n('U');
		self::$stat['notes'] = '';

		// Should we ignore this user?
		if (!empty($GLOBALS['current_user']->ID)){
			// Don't track logged-in users, if the corresponding option is enabled
			if (self::$options['track_users'] == 'no'){
				self::$stat['id'] = -214;
				return $_argument;
			}

			// Don't track users with given capabilities
			foreach(self::string_to_array(self::$options['ignore_capabilities']) as $a_capability){
				if (array_key_exists(strtolower($a_capability), $GLOBALS['current_user']->allcaps)){
					self::$stat['id'] = -200;
					return $_argument;
				}
			}

			if (strpos(self::$options['ignore_users'], $GLOBALS['current_user']->data->user_login) !== false){
				self::$stat['id'] = -201;
				return $_argument;
			}

			self::$stat['user'] = $GLOBALS['current_user']->data->user_login;
			self::$stat['notes'] .= '[user:'.$GLOBALS['current_user']->data->ID.']';
			$not_spam = true;
		}
		elseif (isset($_COOKIE['comment_author_'.COOKIEHASH])){
			// Is this a spammer?
			$spam_comment = $GLOBALS['wpdb']->get_row("SELECT comment_author, COUNT(*) comment_count FROM {$GLOBALS['wpdb']->prefix}comments WHERE INET_ATON(comment_author_IP) = '$long_user_ip' AND comment_approved = 'spam' GROUP BY comment_author LIMIT 0,1", ARRAY_A);
			if (isset($spam_comment['comment_count']) && $spam_comment['comment_count'] > 0){
				if (self::$options['ignore_spammers'] == 'yes'){
					self::$stat['id'] = -202;
					return $_argument;
				}
				else{
					self::$stat['notes'] .= '[spam]';
					self::$stat['user'] = $spam_comment['comment_author'];
				}
			}
			else
				self::$stat['user'] = $_COOKIE['comment_author_'.COOKIEHASH];
		}

		// User's IP address
		list($long_user_ip, $long_other_ip) = self::_get_ip2long_remote_ip();
		if ($long_user_ip == 0){
			self::$stat['id'] = -203;
			return $_argument;
		}

		// Should we ignore this IP address?
		foreach(self::string_to_array(self::$options['ignore_ip']) as $a_ip_range){
			list ($ip_to_ignore, $mask) = @explode("/", trim($a_ip_range));
			if (empty($mask)) $mask = 32;
			$long_ip_to_ignore = ip2long($ip_to_ignore);
			$long_mask = bindec( str_pad('', $mask, '1') . str_pad('', 32-$mask, '0') );
			$long_masked_user_ip = $long_user_ip & $long_mask;
			$long_masked_ip_to_ignore = $long_ip_to_ignore & $long_mask;
			if ($long_masked_user_ip == $long_masked_ip_to_ignore){
				self::$stat['id'] = -204;
				return $_argument;
			}
		}

		if (self::$options['anonymize_ip'] == 'yes'){
			$long_user_ip = $long_user_ip&4294967040;
			$long_other_ip = $long_other_ip&4294967040;
		}
		self::$stat['ip'] = sprintf("%u", $long_user_ip);
		if (!empty($long_other_ip) && $long_other_ip != $long_user_ip) self::$stat['other_ip'] = sprintf("%u", $long_other_ip);

		// Country and Language
		self::$stat['language'] = self::_get_language();
		self::$stat['country'] = self::_get_country($long_user_ip);

		// Is this country blacklisted?
		if (stripos(self::$options['ignore_countries'], self::$stat['country']) !== false){
			self::$stat['id'] = -206;
			return $_argument;
		}

		$referer = array();
		if ((self::$options['javascript_mode'] != 'yes' && !empty($_SERVER['HTTP_REFERER'])) || !empty(self::$data_js['ref'])){
			if (!empty(self::$data_js['ref'])){
				self::$stat['referer'] = base64_decode(self::$data_js['ref']);
			}
			else{
				self::$stat['referer'] = $_SERVER['HTTP_REFERER'];
			}
			$referer = parse_url(self::$stat['referer']);

			// This must be a 'seriously malformed' URL
			if (!$referer){
				self::$stat['id'] = -208;
				return $_argument;
			}
			
			if (isset($referer['host'])){
				self::$stat['domain'] = $referer['host'];

				// Fix Google Images referring domain
				if ((strpos(self::$stat['domain'], 'www.google') !== false) && (strpos(self::$stat['referer'], '/imgres?') !== false))
					self::$stat['domain'] = str_replace('www.google', 'images.google', self::$stat['domain']);
			}
		}

		// Is this referer blacklisted?
		if (!empty(self::$stat['referer'])){
			foreach(self::string_to_array(self::$options['ignore_referers']) as $a_filter){
				$pattern = str_replace( array('\*', '\!') , array('(.*)', '.'), preg_quote($a_filter, '/'));
				if (preg_match("/^$pattern$/i", self::$stat['referer'])){
					self::$stat['id'] = -207;
					return $_argument;
				}
			}
		}

		// We want to record both hits and searches (performed through the site search form)
		if (is_array(self::$data_js) && isset(self::$data_js['res'])){
			$parsed_permalink = parse_url(base64_decode(self::$data_js['res']));
			self::$stat['searchterms'] = self::_get_search_terms($referer);

			// Was this an internal search?
			if (empty(self::$stat['searchterms']))
				self::$stat['searchterms'] = self::_get_search_terms($parsed_permalink);

			self::$stat['resource'] = !is_array($parsed_permalink)?self::$data_js['res']:$parsed_permalink['path'].(!empty($parsed_permalink['query'])?'?'.urldecode($parsed_permalink['query']):'');
		}
		elseif (empty($_REQUEST['s'])){
			self::$stat['searchterms'] = self::_get_search_terms($referer);
			if (isset($_SERVER['REQUEST_URI'])){
				self::$stat['resource'] = $_SERVER['REQUEST_URI'];
			}
			elseif (isset($_SERVER['SCRIPT_NAME'])){
				self::$stat['resource'] = isset($_SERVER['QUERY_STRING'])?$_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING']:$_SERVER['SCRIPT_NAME'];
			}
			else{
				self::$stat['resource'] = isset($_SERVER['QUERY_STRING'])?$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']:$_SERVER['PHP_SELF'];
			}
		}
		else{
			self::$stat['searchterms'] = str_replace('\\', '', $_REQUEST['s']);
			self::$stat['resource'] = ''; // Mark the resource to remember that this is a 'local search'
		}
		//self::$stat['resource'] = htmlentities(self::$stat['resource'], ENT_QUOTES, 'UTF-8');

		// Is this resource blacklisted?
		if (!empty(self::$stat['resource'])){
			foreach(self::string_to_array(self::$options['ignore_resources']) as $a_filter){
				$pattern = str_replace( array('\*', '\!') , array('(.*)', '.'), preg_quote($a_filter, '/'));
				if (preg_match("/^$pattern$/i", self::$stat['resource'])){
					self::$stat['id'] = -209;
					return $_argument;
				}
			}
		}

		// Mark or ignore Firefox/Safari prefetching requests (X-Moz: Prefetch and X-purpose: Preview)
		if ((isset($_SERVER['HTTP_X_MOZ']) && (strtolower($_SERVER['HTTP_X_MOZ']) == 'prefetch')) ||
			(isset($_SERVER["HTTP_X_PURPOSE"]) && (strtolower($_SERVER['HTTP_X_PURPOSE']) == 'preview'))){
			if (self::$options['ignore_prefetch'] == 'yes'){
				self::$stat['id'] = -210;
				return $_argument;
			}
			else{
				self::$stat['notes'] .= '[pre]';
			}
		}

		// Information about this resource
		$content_info = (is_array(self::$data_js) && isset(self::$data_js['ci']))?unserialize(base64_decode(self::$data_js['ci'])):self::_get_content_info();
		if (!is_array($content_info)) $content_info = array('content_type' => 'unknown');

		// Detect user agent
		self::$browser = self::_get_browser();

		// Are we ignoring bots?
		if (self::$options['javascript_mode'] == 'yes' && self::$browser['type']%2 != 0){
			self::$stat['id'] = -211;
			return $_argument;
		}

		// Is this browser blacklisted?
		foreach(self::string_to_array(self::$options['ignore_browsers']) as $a_filter){
			$pattern = str_replace( array('\*', '\!') , array('(.*)', '.'), preg_quote($a_filter, '/'));
			if (preg_match("/^$pattern$/i", self::$browser['browser'].'/'.self::$browser['version'])){
				self::$stat['id'] = -212;
				return $_argument;
			}
		}

		// Do we need to assign a visit_id to this user?
		$cookie_has_been_set = self::_set_visit_id(false);

		// Allow third-party tools to modify all the data we've gathered so far
		self::$stat = apply_filters('slimstat_filter_pageview_stat', self::$stat, self::$browser, $content_info);
		self::$browser = apply_filters('slimstat_filter_pageview_browser', self::$browser, self::$stat, $content_info);
		$content_info = apply_filters('slimstat_filter_pageview_content_info', $content_info, self::$stat, self::$browser);
		do_action('slimstat_track_pageview', self::$stat, self::$browser, $content_info);

		// Third-party tools can decide that this pageview should not be tracked, by setting its datestamp to zero
		if (empty(self::$stat) || empty(self::$stat['dt'])){
			self::$stat['id'] = -213;
			return $_argument;
		}

		// Now let's save this information in the database
		if (!empty($content_info)) self::$stat['content_info_id'] = self::maybe_insert_row($content_info, $GLOBALS['wpdb']->base_prefix.'slim_content_info', 'content_info_id');
		self::$stat['browser_id'] = self::maybe_insert_row(self::$browser, $GLOBALS['wpdb']->base_prefix.'slim_browsers', 'browser_id');
		self::$stat['id'] = self::insert_row(self::$stat, $GLOBALS['wpdb']->prefix.'slim_stats');

		// Something went wrong during the insert
		if (empty(self::$stat['id'])){
			self::$stat['id'] = -214;
			return $_argument;
		}

		// Is this a new visitor?
		$is_set_cookie = apply_filters('slimstat_set_visit_cookie', true);
		if ($is_set_cookie){
			if (empty(self::$stat['visit_id']) && !empty(self::$stat['id'])){
				// Set a cookie to track this visit (Google and other non-human engines will just ignore it)
				@setcookie('slimstat_tracking_code', self::$stat['id'].'id.'.md5(self::$stat['id'].'id'.self::$options['secret']), time()+2678400, COOKIEPATH); // one month
			}
			elseif (!$cookie_has_been_set && self::$options['extend_session'] == 'yes' && self::$stat['visit_id'] > 0){
				@setcookie('slimstat_tracking_code', self::$stat['visit_id'].'.'.md5(self::$stat['visit_id'].self::$options['secret']), time()+self::$options['session_duration'], COOKIEPATH);
			}
		}
		return $_argument;
	}
	// end slimtrack

	/**
	 * Searches for country associated to a given IP address
	 */
	protected static function _get_country($_ipnum = ''){
		$country_codes = array("","ap","eu","ad","ae","af","ag","ai","al","am","cw","ao","aq","ar","as","at","au","aw","az","ba","bb","bd","be","bf","bg","bh","bi","bj","bm","bn","bo","br","bs","bt","bv","bw","by","bz","ca","cc","cd","cf","cg","ch","ci","ck","cl","cm","cn","co","cr","cu","cv","cx","cy","cz","de","dj","dk","dm","do","dz","ec","ee","eg","eh","er","es","et","fi","fj","fk","fm","fo","fr","sx","ga","gb","gd","ge","gf","gh","gi","gl","gm","gn","gp","gq","gr","gs","gt","gu","gw","gy","hk","hm","hn","hr","ht","hu","id","ie","il","in","io","iq","ir","is","it","jm","jo","jp","ke","kg","kh","ki","km","kn","kp","kr","kw","ky","kz","la","lb","lc","li","lk","lr","ls","lt","lu","lv","ly","ma","mc","md","mg","mh","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nc","ne","nf","ng","ni","nl","no","np","nr","nu","nz","om","pa","pe","pf","pg","ph","pk","pl","pm","pn","pr","ps","pt","pw","py","qa","re","ro","ru","rw","sa","sb","sc","sd","se","sg","sh","si","sj","sk","sl","sm","sn","so","sr","st","sv","sy","sz","tc","td","tf","tg","th","tj","tk","tm","tn","to","tl","tr","tt","tv","tw","tz","ua","ug","um","us","uy","uz","va","vc","ve","vg","vi","vn","vu","wf","ws","ye","yt","rs","za","zm","me","zw","a1","a2","o1","ax","gg","im","je","bl","mf","bq","ss","o1");
		if (!$handle = fopen(WP_PLUGIN_DIR."/wp-slimstat/mapping/maxmind.dat", "rb")) return 'xx';

		$offset = 0;
		for ($depth = 31; $depth >= 0; --$depth) {
			if (fseek($handle, 6 * $offset, SEEK_SET) != 0) return 'xx';
			$buf = fread($handle, 6);
			$x = array(0,0);
			for ($i = 0; $i < 2; ++$i) {
				for ($j = 0; $j < 3; ++$j) {
					$x[$i] += ord(substr($buf, 3 * $i + $j, 1)) << ($j * 8);
				}
			}

			if ($_ipnum & (1 << $depth)) {
				if ($x[1] >= 16776960 && !empty($country_codes[$x[1] - 16776960])) {
					fclose($handle);
					return $country_codes[$x[1] - 16776960];
				}
				$offset = $x[1];
			} else {
				if ($x[0] >= 16776960 && !empty($country_codes[$x[0] - 16776960])) {
					fclose($handle);
					return $country_codes[$x[0] - 16776960];
				}
				$offset = $x[0];
			}
		}
		fclose($handle);
		return 'xx';
	}
	// end _get_country

	/**
	 * Tries to find the user's REAL IP address
	 */
	protected static function _get_ip2long_remote_ip(){
		$long_ip = array(0, 0);

		if (isset($_SERVER["REMOTE_ADDR"]) && long2ip($ip2long = ip2long($_SERVER["REMOTE_ADDR"])) == $_SERVER["REMOTE_ADDR"])
			$long_ip[0] = $ip2long;

		if (isset($_SERVER["HTTP_CLIENT_IP"]) && long2ip($long_ip[1] = ip2long($_SERVER["HTTP_CLIENT_IP"])) == $_SERVER["HTTP_CLIENT_IP"])
			return $long_ip;

		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $a_ip){
				if (long2ip($long_ip[1] = ip2long($a_ip)) == $a_ip)
					return $long_ip;
			}

		if (isset($_SERVER["HTTP_X_FORWARDED"]) && long2ip($long_ip[1] = ip2long($_SERVER["HTTP_X_FORWARDED"])) == $_SERVER["HTTP_X_FORWARDED"])
			return $long_ip;

		if (isset($_SERVER["HTTP_FORWARDED_FOR"]) && long2ip($long_ip[1] = ip2long($_SERVER["HTTP_FORWARDED_FOR"])) == $_SERVER["HTTP_FORWARDED_FOR"])
			return $long_ip;

		if (isset($_SERVER["HTTP_FORWARDED"]) && long2ip($long_ip[1] = ip2long($_SERVER["HTTP_FORWARDED"])) == $_SERVER["HTTP_FORWARDED"])
			return $long_ip;

		return $long_ip;
	}
	// end _get_ip2long_remote_ip

	/**
	 * Extracts the accepted language from browser headers
	 */
	protected static function _get_language(){
		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){

			// Capture up to the first delimiter (, found in Safari)
			preg_match("/([^,;]*)/", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $array_languages);

			// Fix some codes, the correct syntax is with minus (-) not underscore (_)
			return str_replace( "_", "-", strtolower( $array_languages[0] ) );
		}
		return 'xx';  // Indeterminable language
	}
	// end _get_language

	/**
	 * Sniffs out referrals from search engines and tries to determine the query string
	 */
	protected static function _get_search_terms($_url = array()){
		if (empty($_url) || !isset($_url['host']) || !isset($_url['query']) || strpos($_url['host'], 'facebook') !== false) return '';

		$query_formats = array('daum' => 'q', 'eniro' => 'search_word', 'naver' => 'query', 'google' => 'q', 'www.google' => 'as_q', 'yahoo' => 'p', 'msn' => 'q', 'bing' => 'q', 'aol' => 'query', 'lycos' => 'q', 'ask' => 'q', 'cnn' => 'query', 'about' => 'q', 'mamma' => 'q', 'voila' => 'rdata', 'virgilio' => 'qs', 'baidu' => 'wd', 'yandex' => 'text', 'najdi' => 'q', 'seznam' => 'q', 'search' => 'q', 'onet' => 'qt', 'yam' => 'k', 'pchome' => 'q', 'kvasir' => 'q', 'mynet' => 'q', 'nova_rambler' => 'words');
		$charsets = array('baidu' => 'EUC-CN');

		parse_str($_url['query'], $query);
		preg_match("/(daum|eniro|naver|google|www.google|yahoo|msn|bing|aol|lycos|ask|cnn|about|mamma|voila|virgilio|baidu|yandex|najdi|seznam|search|onet|yam|pchome|kvasir|mynet|rambler)./", $_url['host'], $matches);

		if (isset($matches[1]) && isset($query[$query_formats[$matches[1]]])){
			// Test for encodings different from UTF-8
			if (function_exists('mb_check_encoding') && !mb_check_encoding($query[$query_formats[$matches[1]]], 'UTF-8') && !empty($charsets[$matches[1]]))
				return mb_convert_encoding(urldecode($query[$query_formats[$matches[1]]]), 'UTF-8', $charsets[$matches[1]]);

			return str_replace('\\', '', trim(urldecode($query[$query_formats[$matches[1]]])));
		}

		// We weren't lucky, but there's still hope
		foreach(array('q','s','k','qt') as $a_format)
			if (isset($query[$a_format])){
				return str_replace('\\', '', trim(urldecode($query[$a_format])));
			}

		return '';
	}
	// end _get_search_terms

	/**
	 * Returns details about the resource being accessed
	 */
	protected static function _get_content_info(){
		$content_info = array('content_type' => 'unknown', 'category' => '');

		// Mark 404 pages
		if (is_404()){
			$content_info['content_type'] = '404';
		}

		// Type
		elseif (is_single()){
			if (($post_type = get_post_type()) != 'post') $post_type = 'cpt:'.$post_type;
			$content_info['content_type'] = $post_type;
			$content_info_array = array();
			foreach (get_object_taxonomies($GLOBALS['post']) as $a_taxonomy){
				$terms = get_the_terms($GLOBALS['post']->ID, $a_taxonomy);
				if (is_array($terms)){
					foreach ($terms as $a_term) $content_info_array[] = $a_term->term_id;
					$content_info['category'] = implode(',', $content_info_array);
				}
			}
			$content_info['content_id'] = $GLOBALS['post']->ID;
		}
		elseif (is_page()){
			$content_info['content_type'] = 'page';
			$content_info['content_id'] = $GLOBALS['post']->ID;
		}
		elseif (is_attachment()){
			$content_info['content_type'] = 'attachment';
		}
		elseif (is_singular()){
			$content_info['content_type'] = 'singular';
		}
		elseif (is_post_type_archive()){
			$content_info['content_type'] = 'post_type_archive';
		}
		elseif (is_tag()){
			$content_info['content_type'] = 'tag';
			$list_tags = get_the_tags();
			if (is_array($list_tags)){
				$tag_info = array_pop($list_tags);
				if (!empty($tag_info)) $content_info['category'] = "$tag_info->term_id";
			}
		}
		elseif (is_tax()){
			$content_info['content_type'] = 'taxonomy';
		}
		elseif (is_category()){
			$content_info['content_type'] = 'category';
			$list_categories = get_the_category();
			if (is_array($list_categories)){
				$cat_info = array_pop($list_categories);
				if (!empty($cat_info)) $content_info['category'] = "$cat_info->term_id";
			}
		}
		elseif (is_date()){
			$content_info['content_type']= 'date';
		}
		elseif (is_author()){
			$content_info['content_type'] = 'author';
		}
		elseif (is_archive()){
			$content_info['content_type'] = 'archive';
		}
		elseif (is_search()){
			$content_info['content_type'] = 'search';
		}
		elseif (is_feed()){
			$content_info['content_type'] = 'feed';
		}
		elseif (is_home()){
			$content_info['content_type'] = 'home';
		}
		elseif (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))){
			$content_info['content_type'] = 'login';
		}

		if (is_paged()){
			$content_info['content_type'] .= ',paged';
		}

		// Author
		if (is_singular()){
			$content_info['author'] = get_the_author_meta('user_login', $GLOBALS['post']->post_author);
		}

		return $content_info;
	}
	// end _get_content_info

	/**
	 * Retrieves some information about the user agent; relies on browscap.php database (included)
	 */
	protected static function _get_browser(){
		// Load cache
		@include_once(plugin_dir_path( __FILE__ ).'mapping/browscap.php');
		
		$browser = array('browser' => 'Default Browser', 'version' => '1', 'platform' => 'unknown', 'css_version' => 1, 'type' => 1);
		if (!is_array($slimstat_patterns)) return $browser;

		$user_agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
		$search = array();
		foreach ($slimstat_patterns as $key => $pattern){
			if (preg_match($pattern . 'i', $user_agent)){
				$search = $value = $search + $slimstat_browsers[$key];
				while (array_key_exists(3, $value) && $value[3]) {
					$value = $slimstat_browsers[$value[3]];
					$search += $value;
				}
				break;
			}
		}

		// If a meaningful match was found, let's define some keys
		if ($search[5] != 'Default Browser' && $search[5] != 'unknown'){
			$browser['browser'] = $search[5];
			$browser['version'] = intval($search[6]);
			$browser['platform'] = strtolower($search[9]);
			$browser['css_version'] = $search[28];

			// browser Types:
			//		0: regular
			//		1: crawler
			//		2: mobile
			//		3: syndication reader
			if ($search[25] == 'true') $browser['type'] = 2;
			elseif ($search[26] == 'true') $browser['type'] = 3;
			elseif ($search[27] != 'true') $browser['type'] = 0;

			return $browser;
		}

		// Let's try with the heuristic approach
		$browser['type'] = 1;

		// Googlebot 
		if (preg_match("#^Mozilla/\d\.\d\s\(compatible;\sGooglebot/(\d\.\d);[\s\+]+http\://www\.google\.com/bot\.html\)$#i", $user_agent, $match)>0){
			$browser['browser'] = "Googlebot";
			$browser['version'] = $match[1];

		// Yahoo!Slurp
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(compatible;\s(Yahoo\!\s([A-Z]{2})?\s?Slurp)/?(\d\.\d)?;\shttp\://help\.yahoo\.com/.*\)$#i', $user_agent, $match)>0){
			$browser['browser'] = $match[1];
			if (!empty($match[3])) $browser['version'] = $match[3];

		// BingBot
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(compatible;\sbingbot/(\d\.\d)[^a-z0-9]+http\://www\.bing\.com/bingbot\.htm.$#', $user_agent, $match)>0){
			$browser['browser'] = 'BingBot';
			if (!empty($match[1])) $browser['browser'] .= $match[1];
			if (!empty($match[2])) $browser['version'] = $match[2];

		// IE 8|7|6 on Windows7|2008|Vista|XP|2003|2000
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(compatible;\sMSIE\s(\d+)(?:\.\d+)+;\s(Windows\sNT\s\d\.\d(?:;\sW[inOW]{2}64)?)(?:;\sx64)?;?(?:\sSLCC1;?|\sSV1;?|\sGTB\d;|\sTrident/\d\.\d;|\sFunWebProducts;?|\s\.NET\sCLR\s[0-9\.]+;?|\s(Media\sCenter\sPC|Tablet\sPC)\s\d\.\d;?|\sInfoPath\.\d;?)*\)$#', $user_agent, $match)>0){
			$browser['browser'] = 'IE';
			$browser['version'] = $match[1];
			$browser['type'] = 0;

			// Parse the OS string and update $browser accordingly
			self::_get_os_version($match[2], $browser);

		// Firefox and other Mozilla browsers on Windows
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(Windows;\sU;\s(.+);\s([a-z]{2}(?:\-[A-Za-z]{2})?);\srv\:\d(?:\.\d+)+\)\sGecko/\d+\s([A-Za-z\-0-9]+)/(\d+(?:\.\d+)+)(?:\s\(.*\))?$#', $user_agent, $match)>0){
			$browser['browser'] = $match[3];
			$browser['version'] = $match[4];
			$browser['type'] = 0;

			self::_get_os_version($match[1], $browser);

		// Firefox and Gecko browsers on Mac|*nix|OS/2
		} elseif (preg_match('#^Mozilla/\d\.\d\s\((Macintosh|X11|OS/2);\sU;\s(.+);\s([a-z]{2}(?:\-[A-Za-z]{2})?)(?:-mac)?;\srv\:\d(?:.\d+)+\)\sGecko/\d+\s([A-Za-z\-0-9]+)/(\d+(?:\.[0-9a-z\-\.]+))+(?:(\s\(.*\))(?:\s([A-Za-z\-0-9]+)/(\d+(?:\.\d+)+)))?$#', $user_agent, $match)>0){
			$browser['browser'] = $match[4];
			$browser['version'] = $match[5];
			$os = $match[2];
			if (!empty($match[7])){ 
				$browser['browser'] = $match[7];
				$browser['version'] = $match[8];
				$os .= " {$match[4]} {$match[5]}";
			} elseif (!empty($match[6])) { 
				$os .= $match[6];
			}
			$browser['type'] = 0;
			self::_get_os_version($os, $browser);

		// Safari and Webkit-based browsers on all platforms
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(([A-Za-z0-9/\.]+);\sU;?\s?(.*);\s?([a-z]{2}(?:\-[A-Za-z]{2})?)?\)\sAppleWebKit/[0-9\.]+\+?\s\((?:KHTML,\s)?like\sGecko\)(?:\s([a-zA-Z0-9\./]+(?:\sMobile)?)/?[A-Z0-9]*)?\sSafari/([0-9\.]+)$#', $user_agent, $match)>0){
			$browser['browser'] = 'Safari';

			// version detection
			if (!empty($match[4]))
				$browser['version'] = $match[4];
			else
				$browser['version'] = $match[5];

			if (preg_match("#^([a-zA-Z]+)/([0-9]+(?:[A-Za-z\.0-9]+))(\sMobile)?#", $browser['version'], $match)>0){
				if ($match[1] != "version") { //Chrome, Iron, Shiira
					$browser['browser'] = $match[1];
				}
				$browser['version'] = $match[2];
				if ($browser['version'] == "0") $browser['version'] = '';
				if (!empty($match[3])) $browser['version'] = $match[3];
			}
			elseif (is_numeric($browser['version'])){
				$webkit_num = intval($browser['version']-0.5);
				if ($webkit_num > 533)
					$browser['version'] = '5';
				elseif ($webkit_num > 525)
					$browser['version'] = '4';
				elseif ($webkit_num > 419)
					$browser['version'] = '3';
				elseif ($webkit_num > 312)
					$browser['version'] = '2';
				elseif ($webkit_num > 85)
					$browser['version'] = '1';
				else 
					$browser['version'] = '';
			}

			if (empty($match[2]))
				$os = $match[1];
			else
				$os = $match[2];
			$browser['type'] = 0;
			self::_get_os_version($os, $browser);

		// Google Chrome browser on all platforms with or without language string
		} elseif (preg_match('#^Mozilla/\d+\.\d+\s(?:[A-Za-z0-9\./]+\s)?\((?:([A-Za-z0-9/\.]+);(?:\sU;)?\s?)?([^;]*)(?:;\s[A-Za-z]{3}64)?;?\s?([a-z]{2}(?:\-[A-Za-z]{2})?)?\)\sAppleWebKit/[0-9\.]+\+?\s\((?:KHTML,\s)?like\sGecko\)(?:\s([A-Za-z0-9_\-]+[^i])/([A-Za-z0-9\.]+)){1,3}(?:\sSafari/[0-9\.]+)?$#', $user_agent, $match)>0){
			$browser['browser'] = $match[4];
			$browser['version'] = $match[5];
			if (empty($match[2]))
				$os = $match[1];
			else
				$os = $match[2];
			$browser['type'] = 0;
			self::_get_os_version($os, $browser);
		}

		// Simple alphanumeric strings usually identify a crawler
		elseif (preg_match("#^([a-z]+[\s_]?[a-z]*)[\-/]?([0-9\.]+)*$#", $user_agent, $match)>0){
			$browser['browser'] = trim($match[1]);
			if (!empty($match[2]))
				$browser['version'] = $match[2];
		}

		if ($browser['platform'] == 'unknown'){
			$browser['type'] = 1;
			$browser['version'] = 0;
		}

		return $browser;
	}
	// end _get_browser

	/**
	 * Parses the UserAgent string to get the operating system code
	 */
	protected static function _get_os_version($_os_string, &$_browser) {
		if (empty($_os_string)) return '';

		// Microsoft Windows
		$x64 = '';
		if (strstr($_os_string, 'WOW64') || strstr($_os_string, 'Win64') || strstr($_os_string, 'x64'))
			$x64 = ' x64';

		if (strstr($_os_string, 'Windows NT 6.2'))
			return ($_browser['platform'] = 'win8'.$x64);
		if (strstr($_os_string, 'Windows NT 6.1'))
			return ($_browser['platform'] = 'win7'.$x64);
		if (strstr($_os_string, 'Windows NT 6.0'))
			return ($_browser['platform'] = 'winvista'.$x64);
		if (strstr($_os_string, 'Windows NT 5.2'))
			return ($_browser['platform'] = 'win2003'.$x64);
		if (strstr($_os_string, 'Windows NT 5.1'))
			return ($_browser['platform'] = 'winxp'.$x64);
		if (strstr($_os_string, 'Windows NT 5.0') || strstr($_os_string, 'Windows 2000'))
			return ($_browser['platform'] = 'win2000'.$x64);
		if (strstr($_os_string, 'Windows ME'))
			return ($_browser['platform'] = 'winme');
		if (preg_match('/Win(?:dows\s)?NT\s?([0-9\.]+)?/', $_os_string)>0)
			return ($_browser['platform'] = 'winnt'.$x64);
		if (preg_match('/(?:Windows95|Windows 95|Win95|Win 95)/', $_os_string)>0)
			return ($_browser['platform'] = 'win95');
		if (preg_match('/(?:Windows98|Windows 98|Win98|Win 98|Win 9x)/', $_os_string)>0)
			return ($_browser['platform'] = 'win98');
		if (preg_match('/(?:WindowsCE|Windows CE|WinCE|Win CE)[^a-z0-9]+(?:.*version\s([0-9\.]+))?/i', $_os_string)>0)
			return ($_browser['platform'] = 'wince');
		if (preg_match('/(Windows|Win)\s?3\.\d[; )\/]/', $_os_string)>0)
			return ($_browser['platform'] = 'win3.x');
		if (preg_match('/(Windows|Win)[0-9; )\/]/', $_os_string)>0)
			return ($_browser['platform'] = 'windows');

		// Linux/Unix
		if (preg_match('/[^a-z0-9](Android|CentOS|Debian|Fedora|Gentoo|Mandriva|PCLinuxOS|SuSE|Kanotix|Knoppix|Mandrake|pclos|Red\s?Hat|Slackware|Ubuntu|Xandros)[^a-z]/i', $_os_string, $match)>0)
			return ($_browser['platform'] = strtolower($match[1]));
		if (preg_match('/((?:Free|Open|Net)BSD)\s?(?:[ix]?[386]+)?\s?([0-9\.]+)?/', $_os_string, $match)>0)
			return ($_browser['platform'] = strtolower($match[1]));

		// Portable devices
		if ((preg_match('/\siPhone\sOS\s(\d+)?(?:_\d)*/i', $_os_string)>0) || (strpos($_os_string, 'iPad') !== false)){
			$browser['type'] = 2;
			return ($_browser['platform'] = 'ios');
		}
		if (strpos($_os_string, 'Mac OS X') !== false){
			$browser['type'] = 2;
			return ($_browser['platform'] = 'macosx');
		}
		if (preg_match('/Android\s?([0-9\.]+)?/', $_os_string)>0){
			$browser['type'] = 2;
			return ($_browser['platform'] = 'android');
		}
		if ((strpos($_os_string, 'BlackBerry') !== false) || (strpos($_os_string, 'RIM') !== false)){
			$browser['type'] = 2;
			return ($_browser['platform'] = 'blackberry os');
		}
		if (preg_match('/SymbianOS\/([0-9\.]+)/i', $_os_string)>0){
			$browser['type'] = 2;
			return ($_browser['platform'] = 'symbianos');
		}

		// Rare operating systems
		if (preg_match('/[^a-z0-9](BeOS|BePC|Zeta)[^a-z0-9]/', $_os_string)>0)
			return ($_browser['platform'] = 'beos');
		if (preg_match('/[^a-z0-9](Commodore\s?64)[^a-z0-9]/i', $_os_string)>0)
			return ($_browser['platform'] = 'commodore64');
		if (preg_match('/[^a-z0-9]Darwin\/?([0-9\.]+)/i', $_os_string)>0)
			return ($_browser['platform'] = 'darwin');

		return ($_browser['platform'] = 'unknown');
	}
	// end os_version

	/**
	 * Reads the cookie to get the visit_id and sets the variable accordingly
	 */
	protected static function _set_visit_id($_force_assign = false){
		$is_new_session = true;
		$identifier = 0;

		if (isset($_COOKIE['slimstat_tracking_code'])){
			list($identifier, $control_code) = explode('.', $_COOKIE['slimstat_tracking_code']);

			// Make sure only authorized information is recorded
			if ($control_code != md5($identifier.self::$options['secret'])) return false;

			$is_new_session = (strpos($identifier, 'id') !== false);
			$identifier = intval($identifier);
		}

		// User doesn't have an active session
		if ($is_new_session && ($_force_assign || self::$options['javascript_mode'] == 'yes')){
			if (empty(self::$options['session_duration'])) self::$options['session_duration'] = 1800;

			self::$stat['visit_id'] = get_option('slimstat_visit_id', -1);
			if (self::$stat['visit_id'] == -1){
				self::$stat['visit_id'] = intval($GLOBALS['wpdb']->get_var("SELECT MAX(visit_id) FROM {$GLOBALS['wpdb']->prefix}slim_stats"));
			}
			self::$stat['visit_id']++;
			update_option('slimstat_visit_id', self::$stat['visit_id']);

			$is_set_cookie = apply_filters('slimstat_set_visit_cookie', true);
			if ($is_set_cookie)
				@setcookie('slimstat_tracking_code', self::$stat['visit_id'].'.'.md5(self::$stat['visit_id'].self::$options['secret']), time()+self::$options['session_duration'], COOKIEPATH);
		}
		elseif ($identifier > 0){
			self::$stat['visit_id'] = $identifier;
		}

		if ($is_new_session && $identifier > 0){
			$GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("
				UPDATE {$GLOBALS['wpdb']->prefix}slim_stats
				SET visit_id = %d
				WHERE id = %d AND visit_id = 0", self::$stat['visit_id'], $identifier));
		}
		return ($is_new_session && ($_force_assign || self::$options['javascript_mode'] == 'yes'));
	}
	// end _set_visit_id
	
	/** 
	 * Stores the information (array) in the appropriate table (if needed) and returns the corresponding ID
	 */
	public static function maybe_insert_row($_data = array(), $_table = '', $_id_column = ''){
		if (empty($_data) || empty($_id_column) || empty($_table)) return -1;

		$select_sql = "SELECT $_id_column FROM $_table WHERE ";
		foreach ($_data as $a_key => $a_value) $select_sql .= "$a_key = %s AND ";
		$select_sql = $GLOBALS['wpdb']->prepare(substr($select_sql, 0, -5), $_data);

		// Let's see if this row is already in our lookup table
		$id = $GLOBALS['wpdb']->get_var($select_sql);

		if (empty($id)){
			$id = self::insert_row($_data, $_table);

			// This may happen if the new content type was added just before performing the INSERT here above
			if (empty($id)) $id = $GLOBALS['wpdb']->get_var($select_sql);
		}

		return $id;
	}
	// end maybe_insert_row

	/**
	 * Stores the information (array) in the appropriate table and returns the corresponding ID
	 */
	public static function insert_row($_data = array(), $_table = ''){
		if (empty($_data) || empty($_table)) return -1;

		$GLOBALS['wpdb']->query($GLOBALS['wpdb']->prepare("
			INSERT IGNORE INTO $_table (".implode(", ", array_keys($_data)).') 
			VALUES ('.substr(str_repeat('%s,', count($_data)), 0, -1).')', $_data));

		return intval($GLOBALS['wpdb']->insert_id);
	}
	// end insert_row

	/**
	 * Converts a series of comma separated values into an array
	 */
	public static function string_to_array($_option = ''){
		if (empty($_option) || !is_string($_option))
			return array();
		else
			return array_map('trim', explode(',', $_option));
	}
	// end string_to_array

	/**
	 * Imports all the 'old' options into the new array, and saves them
	 */
	public static function init_options(){
		self::$options = array(
			'version' => 0,
			'secret' => get_option('slimstat_secret', md5(time())),
			'is_tracking' => get_option('slimstat_is_tracking', 'yes'),
			'javascript_mode' => 'no',
			'auto_purge' => get_option('slimstat_auto_purge', '120'),
			'add_posts_column' => get_option('slimstat_add_posts_column', 'no'),
			'use_separate_menu' => get_option('slimstat_use_separate_menu', 'yes'),

			'convert_ip_addresses' => get_option('slimstat_convert_ip_addresses', 'no'),
			'async_load' => 'no',
			'use_european_separators' => get_option('slimstat_use_european_separators', 'yes'),
			'rows_to_show' => get_option('slimstat_rows_to_show', '20'),
			'expand_details' => 'no',
			'number_results_raw_data' => get_option('slimstat_number_results_raw_data', '50'),
			'ip_lookup_service' => 'http://www.infosniper.net/?ip_address=',
			'refresh_interval' => get_option('slimstat_refresh_interval', '0'),
			'hide_stats_link_edit_posts' => 'no',
			'custom_css' => '',
			'markings' => '',

			'track_users' => get_option('slimstat_track_users', 'yes'),
			'ignore_spammers' => get_option('slimstat_ignore_spammers', 'no'),
			'anonymize_ip' => 'no',
			'ignore_prefetch' => get_option('slimstat_ignore_prefetch', 'no'),
			'ignore_ip' => get_option('slimstat_ignore_ip', ''),
			'ignore_resources' => get_option('slimstat_ignore_resources', ''),
			'ignore_countries' => get_option('slimstat_ignore_countries', ''),
			'ignore_browsers' => get_option('slimstat_ignore_browsers', ''),
			'ignore_referers' => get_option('slimstat_ignore_referers', ''),
			'ignore_users' => get_option('slimstat_ignore_users', ''),
			'ignore_users_by_capability' => '',

			'restrict_authors_view' => 'no',
			'capability_can_view' => get_option('slimstat_capability_can_view', 'read'),
			'can_view' => get_option('slimstat_can_view', ''),
			'can_admin' => get_option('slimstat_can_admin', ''),
			
			'enable_javascript' => 'yes',
			'detect_smoothing' => 'yes',
			'enable_outbound_tracking' => 'yes',
			'session_duration' => 1800,
			'extend_session' => 'no',
			'enable_cdn' => 'no',
			'extensions_to_track' => '',
		);

		// Save these options in the database
		add_option('slimstat_options', self::$options, '', 'no');
	}
	// end init_options

	/**
	 * Enqueue a javascript to track users' screen resolution and other browser-based information
	 */
	public static function wp_slimstat_enqueue_tracking_script(){
		//if (self::$options['javascript_mode'] != 'yes' && self::$stat['id'] <= 0) return 0;

		if (self::$options['enable_cdn'] == 'yes')
			wp_register_script('wp_slimstat', 'http://cdn.jsdelivr.net/wp-slimstat/'.self::$options['version'].'/wp-slimstat.js', array(), null, true);
		else
			wp_register_script('wp_slimstat', plugins_url('/wp-slimstat.js', __FILE__), array(), null, true);

		// Pass some information to Javascript
		$params = array(
			'ajaxurl' => admin_url('admin-ajax.php')
		);

		if (self::$options['javascript_mode'] != 'yes' && !empty(self::$stat['id'])){
			$params['id'] = self::$stat['id'].'.'.md5(self::$stat['id'].self::$options['secret']);
		}
		if (self::$options['javascript_mode'] == 'yes'){
			$encoded_ci = base64_encode(serialize(self::_get_content_info()));
			$params['ci'] = $encoded_ci.'.'.md5($encoded_ci.self::$options['secret']);
		}
		if (self::$options['enable_outbound_tracking'] == 'no'){
			$params['disable_outbound_tracking'] = 'true';
		}
		if (!empty(self::$options['extensions_to_track'])){
			$params['extensions_to_track'] = str_replace(' ', '', self::$options['extensions_to_track']);
		}
		if (self::$options['enable_javascript'] == 'yes' && self::$options['detect_smoothing'] == 'no'){
			$params['detect_smoothing'] = 'false';
		}
		$params = apply_filters('slimstat_js_params', $params);

		wp_enqueue_script('wp_slimstat');
		wp_localize_script('wp_slimstat', 'SlimStatParams', $params);
	}
	// end wp_slimstat_enqueue_tracking_script

	/**
	 * Removes old entries from the database
	 */
	public static function wp_slimstat_purge(){
		if (($autopurge_interval = intval(self::$options['auto_purge'])) <= 0) return;

		// Delete old entries
		$GLOBALS['wpdb']->query("DELETE ts FROM {$GLOBALS['wpdb']->prefix}slim_stats ts WHERE ts.dt < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL $autopurge_interval DAY))");

		// Optimize table
		$GLOBALS['wpdb']->query("OPTIMIZE TABLE {$GLOBALS['wpdb']->prefix}slim_stats");
	}
	// end wp_slimstat_purge

	/**
	 * Adds a new entry to the Wordpress Toolbar
	 */
	public static function wp_slimstat_adminbar(){
		if ((function_exists('is_network_admin') && is_network_admin()) || !is_admin_bar_showing()) return;
		load_plugin_textdomain('wp-slimstat', WP_PLUGIN_DIR .'/wp-slimstat/admin/lang', '/wp-slimstat/admin/lang');

		self::$options['capability_can_view'] = empty(self::$options['capability_can_view'])?'read':self::$options['capability_can_view'];

		if (empty(self::$options['can_view']) || strpos(self::$options['can_view'], $GLOBALS['current_user']->user_login) !== false || current_user_can('manage_options')){
			if (self::$options['use_separate_menu'] != 'yes'){
				$slimstat_view_url = $slimstat_config_url = 'options.php';
			}
			else{
				$slimstat_view_url = $slimstat_config_url = 'admin.php';
			}
			$slimstat_view_url = get_site_url($GLOBALS['blog_id'], "/wp-admin/$slimstat_view_url?page=wp-slim-view-");
			$slimstat_config_url = get_site_url($GLOBALS['blog_id'], "/wp-admin/$slimstat_config_url?page=wp-slim-config");
			
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-header', 'title' => 'SlimStat', 'href' => "{$slimstat_view_url}1"));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel1', 'href' => "{$slimstat_view_url}1", 'parent' => 'slimstat-header', 'title' => __('Right Now', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel2', 'href' => "{$slimstat_view_url}2", 'parent' => 'slimstat-header', 'title' => __('Overview', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel3', 'href' => "{$slimstat_view_url}3", 'parent' => 'slimstat-header', 'title' => __('Visitors', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel4', 'href' => "{$slimstat_view_url}4", 'parent' => 'slimstat-header', 'title' => __('Content', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel5', 'href' => "{$slimstat_view_url}5", 'parent' => 'slimstat-header', 'title' => __('Traffic Sources', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel6', 'href' => "{$slimstat_view_url}6", 'parent' => 'slimstat-header', 'title' => __('World Map', 'wp-slimstat')));
			if (has_action('wp_slimstat_custom_report')) $GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel7', 'href' => "{$slimstat_view_url}7", 'parent' => 'slimstat-header', 'title' => __('Custom Reports', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel8', 'href' => "{$slimstat_view_url}addons", 'parent' => 'slimstat-header', 'title' => __('Add-ons', 'wp-slimstat')));
			
			if ((empty(wp_slimstat::$options['can_admin']) || strpos(wp_slimstat::$options['can_admin'], $GLOBALS['current_user']->user_login) !== false || $GLOBALS['current_user']->user_login == 'slimstatadmin') && current_user_can('edit_posts')){
				$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-config', 'href' => $slimstat_config_url, 'parent' => 'slimstat-header', 'title' => __('Settings', 'wp-slimstat')));
			}
		}
	}
	// end wp_slimstat_adminbar
}
// end of class declaration

// Ok, let's go, Sparky!
if (function_exists('add_action')){
	// Init the Ajax listener
	if (!empty($_POST['action']) && $_POST['action'] == 'slimtrack_js'){
		add_action('wp_ajax_nopriv_slimtrack_js', array('wp_slimstat', 'slimtrack_js'));
		add_action('wp_ajax_slimtrack_js', array('wp_slimstat', 'slimtrack_js')); 
	}

	// Add the appropriate actions
	add_action('plugins_loaded', array('wp_slimstat', 'init'), 5);

	// Load the admin API, if needed
	if (is_admin()) include_once(WP_PLUGIN_DIR.'/wp-slimstat/admin/wp-slimstat-admin.php');
}