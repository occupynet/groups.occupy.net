<?php
/*
Plugin Name: FWP+: Strip HTML Tags from Posts
Plugin URI: http://projects.radgeek.com/fwp-strip-html-tags-from-posts
Description: Filters posts syndicated through FeedWordPress to remove all HTML tags from the post content
Version: 2010.1105
Author: Charles Johnson
Author URI: http://radgeek.com/
License: GPL
*/

// Hook us in
$stripper = new FWPPlusStripHTMLTagsFromPosts;

/**
 * @package FWPPlusStripHTMLTagsFromPosts
 * @version 2010.0609
 */

class FWPPlusStripHTMLTagsFromPosts {
	function FWPPlusStripHTMLTagsFromPosts () {
		add_action(
			/*hook=*/ 'feedwordpress_admin_page_posts_meta_boxes',
			/*function=*/ array(&$this, 'posts_meta_boxes'),
			/*priority=*/ 100,
			/*arguments=*/ 1
		);
		add_action(
			/*hook=*/ 'feedwordpress_admin_page_posts_save',
			/*function=*/ array(&$this, 'posts_save'),
			/*priority=*/ 100,
			/*arguments=*/ 2
		);
		
		add_filter(
			/*hook=*/ 'the_content',
			/*function=*/ array(&$this, 'the_content'),
			/*priority=*/ 10010
		);
		add_filter(
			/*hook=*/ 'the_content_rss',
			/*function=*/ array(&$this, 'the_content'),
			/*priority=*/ 10010
		);
	}

	function posts_meta_boxes ($page) {
		add_meta_box(
			/*id=*/ 'feedwordpress_html_tags_box',
			/*title=*/ __('HTML Tags'),
			/*callback=*/ array($this, 'html_tags_metabox'),
			/*page=*/ $page->meta_box_context(),
			/*context=*/ $page->meta_box_context()
		);
	} /* FWPPlusStripHTMLTagsFromPosts::posts_meta_boxes() */
	
	function html_tags_metabox ($page, $box = NULL) {
		global $fwp_path;

		if ($page->for_feed_settings()) :
			$syndicatedPosts = 'this feed\'s posts';
		else :
			$syndicatedPosts = 'syndicated posts';
		endif;
		
		$postSelector = array(
		'no' => "Leave HTML tags in %s",
		'yes' => "Strip HTML tags from %s",
		);
		foreach ($postSelector as $index => $value) :
			$postSelector[$index] = sprintf(__($value), $syndicatedPosts);
		endforeach;
		
		$params = array(
		'input_name' => 'strip_html',
		'setting-default' => 'default',
		'global-setting-default' => 'no',
		'labels' => $postSelector,
		'default-input-value' => 'default',
		);
?>
		<table class="edit-form narrow">
		<tr><th scope="row"><?php _e('Strip HTML:'); ?></th>
		<td><?php $page->setting_radio_control(
			'strip html', 'strip_html',
			$postSelector, $params
		);
		?></td>
		</tr>
		</table>
<?php
	} /* FWPPlusStripHTMLTagsFromPosts::html_tags_box() */

	function posts_save ($params, $page) {
		if ((isset($params['save']) or isset($params['submit']))
		and isset($params['strip_html'])) :
			if ($page->for_feed_settings()) :
				if ('default'==$params['strip_html']) :
					unset($page->link->settings['strip html']);
				else :
					$page->link->settings['strip html'] = $params['strip_html'];
				endif;
				$page->link->save_settings(/*reload=*/ true);
			else :
				update_option('feedwordpress_strip_html', $params['strip_html']);
			endif;
		endif;
	} /* FWPPlusStripHTMLTagsFromPosts::posts_save () */

	function the_content ($text) {
		if (function_exists('is_syndicated') and is_syndicated()) :
			$feed = get_syndication_feed_object();
			if (is_object($feed)) :
				$strip_html = $feed->setting('strip html', 'strip_html', false);
			else :
				$strip_html = false;
			endif;

			if (is_string($strip_html) and 'yes'==strtolower($strip_html)) :
				$text = '<p class="strip_tags">'.strip_tags($text).'</p>';
			endif;
		endif;
		return $text;
	} /* FWPPlusStripHTMLTagsFromPosts::the_content () */
} /* class FWPPlusStripHTMLTagsFromPosts */
