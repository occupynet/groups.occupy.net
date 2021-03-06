<?php
/*
Plugin Name: FWP+: Limit size of posts
Plugin URI: http://projects.radgeek.com/fwp-limit-size-of-posts/
Description: enables you to limit the size of incoming syndicated posts from FeedWordPress by word count or by character count
Version: 2010.1011 
Author: Charles Johnson
Author URI: http://radgeek.com/
License: GPL
*/

class FWPLimitSizeOfPosts {
	var $mb;
	var $myCharset;

	// Careful multibyte support (fallback to normal functions if not available)
	function FWPLimitSizeOfPosts () {
		$this->name = strtolower(get_class($this));
		$this->myCharset = get_bloginfo('charset');
		
		// Carefully support multibyte languages
		if (extension_loaded('mbstring') and function_exists('mb_list_encodings')) :
			$this->mb = in_array($this->myCharset, mb_list_encodings());
		endif;

		add_filter(
		/*hook=*/ 'syndicated_item_content',
		/*function=*/ array(&$this, 'syndicated_item_content'),
		/*priority=*/ 10010,
		/*arguments=*/ 2
		);
		
		add_filter(
		/*hook=*/ 'syndicated_item_excerpt',
		/*function=*/ array(&$this, 'syndicated_item_excerpt'),
		/*priority=*/ 10010,
		/*arguments=*/ 2
		);
		
		add_action(
		/*hook=*/ 'feedwordpress_admin_page_posts_meta_boxes',
		/*function=*/ array(&$this, 'add_settings_box'),
		/*priority=*/ 100,
		/*arguments=*/ 1
		);
		
		add_action(
		/*hook=*/ 'feedwordpress_admin_page_posts_save',
		/*function=*/ array(&$this, 'save_settings'),
		/*priority=*/ 100,
		/*arguments=*/ 2
		);
	} /* FWPLimitSizeOfPosts constructor */
	
	function __construct () {
		self::FWPLimitSizeOfPosts();
	} /* FWPLimitSizeOfPosts::__construct() */

	// Carefully support multibyte languages
	function is_mb () { return $this->mb; }
	function charset () { return $this->myCharset; }
	
	function substr ($str, $start, $length = null) {
		$length = (is_null($length) ? $this->strlen($str) : $length);
		$str = ($this->is_mb()
			? mb_substr($str, $start, $length, $this->charset())
			: substr($str, $start, $length));
		return $str;
	} /* FWPLimitSizeOfPosts::substr() */
	
	function strlen ($str) {
		if ($this->is_mb()) :
			return mb_strlen($str, $this->charset());
		else :
			return strlen($str);
		endif;
	} /* FWPLimitSizeOfPosts::strlen() */

	function filter ($text, $params = array()) {
		global $id, $post;
		
		if (isset($params['sentences'])) : $sentencesMax = $params['sentences']; else : $sentencesMax = NULL; endif;
		if (isset($params['words'])) : $wordsMax = $params['words']; else : $wordsMax = NULL; endif;
		if (isset($params['characters'])) : $charsMax = $params['characters']; else : $charsMax = NULL; endif;
		if (isset($params['breaks'])) : $breaks = !!$params['breaks']; else : $breaks = false; endif;
		if (isset($params['insert break'])) : $insertBreak = $params['insert break']; else : $insertBreak = false; endif;
		if (isset($params['allowed tags'])) : $allowed_tags = $params['allowed tags']; else : $allowed_tags = NULL; endif;
		if (isset($params['finish word'])) : $finish_word = $params['finish word'] ; else : $finish_word = false; endif;

		if (isset($params['ellipsis'])) :
			$ellipsis = $params['ellipsis'] ;
		elseif ($insertBreak) :
			$ellipsis = "<!--more-->";
		else :
			$ellipsis = '...';
		endif;

		$ellipsis = apply_filters('feedwordpress_limit_size_of_posts_ellipsis', $ellipsis);
		
		$originalText = $text;

		if ($breaks) :
			$gen = new SyndicatedPostGenerator($params['post']);

			$moreMarks = array(
				'http://www.sixapart.com/movabletype/' => array('(<div id=["\']more["\']>)', '$1'),
				'http://www.blogger.com' => array('<a name=["\']more["\'](></a>|\s*/>)', ''),
				'http://wordpress.org/' => array('<span id=["\']more-[0-9]+["\'](></span>|\s*/>)', ''),
				'LiveJournal / LiveJournal.com' => array('<a name=["\']cutid[0-9]+["\'](></a>|\s*/>)', ''),
			);
			foreach ($moreMarks as $url => $rewrite) :
				$gotIt = $gen->generated_by(NULL, $url);
				if ($gotIt or is_null($gotIt)) :
					if ($insertBreak) :
						// Keep remainder
						$pattern = $rewrite[0];
						$replacement = $ellipsis.$rewrite[1];
					else :
						// Cut it off
						$pattern = $rewrite[0] . '.*$'; // Eat it all to the end of the string
						$replacement = $ellipsis;
					endif;

					// Search for HTML artifact of jump tag
					$text = preg_replace(
						"\007".$pattern."\007"."i",
						$replacement,
						$text
					);
				endif;
			endforeach;
		endif;
		
		if ($originalText == $text) :
			// From the default wp_trim_excerpt():
			// Some kind of precaution against malformed CDATA in RSS feeds I suppose
			$text = str_replace(']]>', ']]&gt;', $text);
			
			if (!is_null($allowed_tags)) :
				$text = strip_tags($text, $allowed_tags);
			endif;
	
			$sentencesOK = (is_null($sentencesMax) OR ($sentencesMax > count(preg_split(':([.?!]|</p>|</li>):i', $text, -1))));
			$wordsOK = (is_null($wordsMax) OR ($wordsMax > count(preg_split('/[\s]+/', strip_tags($text), -1))));
			$charsOK = (is_null($charsMax) OR ($charsMax > $this->strlen(strip_tags($text))));
			if ($sentencesOK and $wordsOK and $charsOK) :
				return $text;
			else :
				// Break string into "words" based on
				// (1) whitespace, or
				// (2) tags
				// Hence "un<em>frakking</em>believable" will
				// be treated as 3 words, not as 1 word. You
				// might refine this, if it is really important,
				// by keeping a list of "word-breaking" tags
				// (e.g. <br/>, <p>/</p>, <div>/</div>, etc.)
				// and non-word-breaking tags (e.g. <em>/</em>,
				// etc.).
				//
				// Tags do not count towards either words or characters;
				// Whitespace chunks count towards characters, not words
	
				$text_bits = preg_split(
					'/([\s]+|[.?!]+|<[^>]+>)/',
					$text,
					-1,
					PREG_SPLIT_DELIM_CAPTURE
				);
	
				$sentencesN = 0;
				$wordsN = 0;
				$charsN = 0;
				$length = 0;
				$text = ''; $rest = '';
				$prefixDone = false;
				foreach ($text_bits as $chunk) :
					if ($prefixDone) :
						$rest .= $chunk;
					else :
						// This is a tag, or whitespace.
						if (preg_match('/^<[^>]+>$/s', $chunk)) :
							// Closer tags might break a sentence
							if (preg_match('!(</p>|</li>)!i', $chunk)) :
								$sentencesN += 1;
							endif;
						elseif (strlen(trim($chunk)) == 0) :
							$charsN += $this->strlen($chunk);
						elseif (preg_match('/[.?!]+/', $chunk)) :
							$charsN += $this->strlen($chunk);
							$sentencesN += 1;
						else :
							$charsN += $this->strlen($chunk);
							$wordsN += 1;
						endif;
		
						if (!is_null($wordsMax) and ($wordsN > $wordsMax)) :
							$prefixDone = true;
						else :
							$text .= $chunk;
			
							if (!is_null($charsMax) and ($charsN > $charsMax)) :
								$length += ($this->strlen($chunk) - ($charsN - $charsMax));
								$prefixDone = true;
							elseif (!is_null($sentencesMax) and ($sentencesN >= $sentencesMax)) :
								$length += ($this->strlen($chunk));
								$prefixDone = true;
							else :
								$length += $this->strlen($chunk);
							endif;
						endif;
					endif;
				endforeach;
	
				if ($charsN > $charsMax and !$finish_word) :
					// Break it off right there!
					$text = $this->substr($text, 0, $length);
					$rest = $this->substr($text, $length).$rest;
				endif;
	
				$wordsLimited = (!is_null($wordsMax) AND ($wordsN > $wordsMax));
				$charsLimited = (!is_null($charsMax) AND ($charsN > $charsMax));
				$sentencesLimited = (!is_null($sentencesMax) AND ($sentencesN >= $sentencesMax));
				if ($wordsLimited OR $charsLimited OR $sentencesLimited) :
					$text = $text . $ellipsis;
				endif;
	
				if ($insertBreak) :
					$text = $text . $rest;
				else :
					$text = force_balance_tags($text);
				endif;
			endif;
		else :
			$text = force_balance_tags($text);
		endif;
		return $text;
	} /* FWPLimitSizeOfPosts::filter() */

	function syndicated_item_content ($content, $post) {
		$link = $post->link;

		$rule = $link->setting('limit size of posts', $this->name.'_limit_size_of_posts', NULL);
		// Rules are stored in the format array('metric' => $count, 'metric' => $count);
		// 'metric' is the metric to be limited (characters|words)
		// $count is a numeric value indicating the maximum to limit it to
		// NULL indicates that no limiting rules have been stored.
		
		if (is_string($rule)) : $rule = unserialize($rule); endif;

		$haveRule = is_array($rule);
		if ($haveRule) :
			$rule['post'] = $post;
			$content = $this->filter($content, $rule);
		endif;
		return $content;
	} /* FWPLimitSizeOfPosts::syndicated_item_content() */

	function syndicated_item_excerpt ($excerpt, $post) {
		$link = $post->link;

		$rule = $link->setting('limit size of posts', $this->name.'_limit_size_of_posts', NULL);
		// Rules are stored in the format array('metric' => $count, 'metric' => $count);
		// 'metric' is the metric to be limited (characters|words)
		// $count is a numeric value indicating the maximum to limit it to
		// NULL indicates that no limiting rules have been stored.
		
		if (is_string($rule)) : $rule = unserialize($rule); endif;

		$filtered = is_array($rule);
		if ($filtered) :
			// Force FWP to generate an excerpt from the filtered text.
			$excerpt = NULL;
		endif;
		return $excerpt;
	} /* FWPLimitSizeOfPosts::syndicated_item_excerpt() */
	
	function add_settings_box ($page) {
		add_meta_box(
			/*id=*/ "feedwordpress_{$this->name}_box",
			/*title=*/ __("Limit size of posts"),
			/*callback=*/ array(&$this, 'display_settings'),
			/*page=*/ $page->meta_box_context(),
			/*context=*/ $page->meta_box_context()
		);
	} /* FWPLimitSizeOfPosts::add_settings_box() */

	function limit_objects () {
		return array(
			'words' => 300 /*=default limit*/,
			'sentences' => 10 /*=default limit*/,
			'characters' => 1000 /*=default limit*/,
			'breaks' => 1 /*=default limit*/,
		);
	}

	function display_settings ($page, $box = NULL) {
		$global_rule = get_option("feedwordpress_{$this->name}_limit_size_of_posts", NULL);
		if ($page->for_feed_settings()) :
			$rule = $page->link->setting('limit size of posts', NULL, NULL);
		else :
			$rule = $global_rule;
		endif;
		$thesePosts = $page->these_posts_phrase();	

		if (is_string($global_rule)) : $global_rule = unserialize($global_rule); endif;
	
		if (is_string($rule)) : $rule = unserialize($rule); endif;

		foreach ($this->limit_objects() as $thingy => $default) :
			$checked[$thingy] = ((isset($rule[$thingy]) and !is_null($rule[$thingy])) ? 'checked="checked"' : '');
			if ($thingy != 'breaks') :
				$input[$thingy] = '<input type="number"
				min="0" step="1"
				size="4"
				name="'.$this->name.'_'.$thingy.'_limit" value="'.(isset($rule[$thingy]) ? $rule[$thingy] : $default).'" />';
			else :
				$input[$thingy] = '<input type="hidden" name="'.$this->name.'_'.$thingy.'_limit" value="1" />';
			endif;
		endforeach;
		
		$selector = array();
			
		$wordsSet = isset($rule['words']);
		$sentsSet = isset($rule['sentences']);
		$charsSet = isset($rule['characters']);
		$breaksSet = isset($rule['breaks']);
		$insertBreaks = ((isset($rule['insert break']) and $rule['insert break']) ? 'break' : 'yes');
		
		if (!($wordsSet or $charsSet or $sentsSet or $breaksSet)) : $selected = 'no';
		else : $selected = $insertBreaks;
		endif;
		if ($page->for_feed_settings()) :
			if (is_null($rule)) : $selected = 'default'; endif;
		endif;

		$selector[] = '<ul>';
		
		$magicId = $this->name.'-limit-controls';
		foreach (array('default', 'yes', 'no', 'break') as $response) :
			$boxId[$response] = $this->name.'-limit-'.$response;
			$input[$response] = '<input type="radio" onclick="'.$this->name.'_display_limit_controls();" id="'.$boxId[$response].'" name="'.$this->name.'_limits" value="'.$response.'" '.($selected==$response?' checked="checked"':'').' />';
		endforeach;

		if ($page->for_feed_settings()) :
			$siteWide = array();
			if (is_null($global_rule)) :
				$siteWide[] = 'no limit';
			else:
				if (isset($global_rule['words'])) : $siteWide[] = $global_rule['words'].' words'; endif;
				if (isset($global_rule['sentences'])) : $siteWide[] = $global_rule['words'].' sentences'; endif;
				if (isset($global_rule['characters'])) : $siteWide[] = $global_rule['characters'].' characters'; endif;
				if (isset($global_rule['breaks'])) : $siteWide[] = 'at breaks from the original post'; endif;
			endif;
			
			$selector[] = '<li><label>'.$input['default'].' '.sprintf(__("Use site-wide settings for $thesePosts (currently: %s)"), implode(' / ',$siteWide)).'</label></li>';
		endif;
		$selector[] = '<li><label>'.$input['no'].' '.__("Do not limit the size of $thesePosts").'</label></li>';
		$selector[] = '<li><label>'.$input['yes'].' '.__("Cut off $thesePosts after a certain length").'</label></li>';
		$selector[] = '<li><label>'.$input['break'].' '.__("Insert a \"Read More...\" break into $thesePosts after a certain length").'</label></li>';
		$selector[] = '</ul>';
		?>
		<table class="edit-form narrow">
		<tbody>
		<tr>
		<th scope="row"><?php _e('Limit post size:'); ?></th>
		<td><?php print implode("\n", $selector); ?></td>
		</tr>
		</tbody>
		</table>
		
		<table class="edit-form narrow" id="<?php print $this->name; ?>-limit-controls">
		<tbody>
		<tr><th scope="row"><?php _e('Word count:'); ?></th>
		<td><input type="checkbox" <?php print $checked['words']; ?> name="<?php print $this->name; ?>_limit_words" value="yes" /> <?php printf(__("Limit $thesePosts to no more than %s words"), $input['words']); ?></td>
		</tr>

		<tr><th scope="row"><?php _e('Sentences:'); ?></th>
		<td><input type="checkbox" <?php print $checked['sentences']; ?> name="<?php print $this->name; ?>_limit_sentences" value="yes" /> <?php printf(__("Limit $thesePosts to no more than %s sentences"), $input['sentences']); ?>
		</tr>
		
		<tr><th scope="row"><?php _e('Characters:'); ?></th>
		<td><input type="checkbox" <?php print $checked['characters']; ?> name="<?php print $this->name; ?>_limit_characters" value="yes" /> <?php printf(__("Limit $thesePosts to no more than %s characters"), $input['characters']); ?></td>
		</tr>

		<tr><th scope="row"><?php _e('At Breaks:'); ?></th>
		<td><input type="checkbox" <?php print $checked['breaks']; ?> name="<?php print $this->name; ?>_limit_breaks" value="yes" /> <?php printf(__("Use \"Read More...\" break-points from the original source post instead, if available. %s"), $input['breaks']); ?></label></td>
		</tr>

		</tbody>
		</table>
		
		<script type="text/javascript">
		function <?php print $this->name; ?>_display_limit_controls (init) {
			if ('undefined' != typeof(jQuery('#<?php print $this->name; ?>-limit-yes:checked, #<?php print $this->name; ?>-limit-break:checked').val())) {
				var val = 600;
				if (init) { val = 0; }
				jQuery('#<?php print $this->name; ?>-limit-controls').show(val);
			} else {
				jQuery('#<?php print $this->name; ?>-limit-controls').hide();
			}
		}
		function <?php print $this->name; ?>_display_limit_controls_init () {
			<?php print $this->name; ?>_display_limit_controls(/*init=*/ true);
		}
		
		jQuery(document).ready( <?php print $this->name; ?>_display_limit_controls_init );
		</script>
		<?php
	} /* FWPLimitSizeOfPosts::display_settings() */
	
	function save_settings ($params, $page) {
		if (isset($params['save']) or isset($params['submit'])) :
			if (isset($params[$this->name.'_limits'])) :
				$rule = array();
	
				switch ($params[$this->name.'_limits']) :
				case 'default' :
					$rule = NULL;
					break;
				case 'no' :
					// allows feeds to override a default limiting
					// policy with a no-limiting policy
					$rule = array ( 'limits' => 'none' );
					break;
				case 'break' :
					$rule['insert break'] = true;
					# | Continue on below...
					# v
				case 'yes' :
				default :
					foreach ($this->limit_objects() as $thingy => $default) :
						if (isset($params[$this->name.'_limit_'.$thingy])
						and ($params[$this->name.'_limit_'.$thingy]=='yes')
						and (isset($params[$this->name.'_'.$thingy.'_limit']))) :
							$rule[$thingy] = (int) $params[$this->name.'_'.$thingy.'_limit'];
						endif;
					endforeach;
				endswitch;
				
				// Now let's write it.
				if ($page->for_feed_settings()) :
					if (!is_null($rule)) :
						$page->link->settings['limit size of posts'] = serialize($rule);
					else :
						unset($page->link->settings['limit size of posts']);
					endif;
					$page->link->save_settings(/*reload=*/ true);
				else :
					update_option("feedwordpress_{$this->name}_limit_size_of_posts", $rule);
				endif;
			endif;
		endif;
	} /* FWPLimitSizeOfPosts::save_settings() */

} /* class FWPLimitSizeOfPosts */

class SyndicatedPostGenerator {
	var $post;
	var $link;
	
	function SyndicatedPostGenerator ($post) {
		if (is_object($post) and is_a($post, 'SyndicatedPost')) :
			$this->post = $post;
			$this->link = $post->link;
		else :
			$this->post = new SyndicatedPost($post);
			$this->link = $this->post->link;
		endif;
	} /* SyndicatedPostGenerator constructor */
	
	function generated_by ($name, $url, $version = NULL) {
		$ret = NULL;
		if (method_exists($this->link, 'generated_by')) :
			$ret = $this->link->generated_by($name, $url, $version);
		else :
			$inspect = array(
				'url' => array(
				'/feed/atom:generator/@url',
				'/feed/atom:generator/@uri',
				'/feed/rss:generator',
				'/feed/admin:generatorAgent/@rdf:resource',
				),
				'name' => array(
				'/feed/atom:generator',
				),
				'version' => array(
				'/feed/atom:generator/@version',
				),
			);
			$found = array(); $final = array();
			foreach ($inspect as $item => $set) :
				$found[$item] = array();
				foreach ($set as $q) :
					$found[$item] = array_merge($found[$item], $this->post->query($q));
				endforeach;
				
				if (count($found[$item]) > 0) :
					$final[$item] = implode(" ", $found[$item]);
				else :
					$final[$item] = NULL;
				endif;
			endforeach;
			
			if (is_null($final['version'])
			and !is_null($final['url'])
			and preg_match('|^(.*)\?v=(.*)$|i', $final['url'], $ref)) :
				$final['url'] = $ref[1];
				$final['version'] = $ref[2];
			endif;
			
			if (!is_null($name)) :
				if ($final['name'] == $name) :
					$ret = true;
				elseif (!is_null($final['name'])) :
					$ret = false;
				endif;
			endif;

			if (!is_null($url)) :
				if ($final['url'] == $url) :
					$ret = true;
				elseif (!is_null($final['url'])) :
					$ret = false;
				endif;
			endif;
		endif;
		return $ret;
	}
}

$fwpPostSizeLimiter = new FWPLimitSizeOfPosts;

