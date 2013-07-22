<?php
/*
Parameters
==========

$how_many (integer): how many recent posts are being displayed.
$how_long_days (integer): time frame to choose recent posts from (in days).
$how_many_words (integer): how many post's teaser are being displayed. Count by word. Default value are 50 words.
$remove_html (boolean): set true to remove any html tag within the post.
$sort_by (string - post_date/post_modified): You can short the lattest post by positing date (post_date) or posting update (post_modified).

Return
======

ID
post_url
post_title
post_content
author_url
author_name
post_date
post_time
comment_count
*/
function wpmu_latest_post($how_many = 10, $how_long_days = 30, $how_many_words = 50, $more_text = "[...]", $remove_html = true, $sort_by = 'post_date') {
	global $wpdb;

	//first, gat all blog id
	$query = "SELECT blog_id FROM $wpdb->blogs WHERE blog_id !='1'";
	$blogs = $wpdb->get_col($query);

	if ($blogs) {
		//we use blog id to loop post query
		foreach ($blogs as $blog) {
			$blogPostsTable = 'wp_'.$blog.'_posts';

			$db_query = "SELECT $blogPostsTable.ID,
						$blogPostsTable.post_author,
						$blogPostsTable.post_title,
						$blogPostsTable.guid,
						$blogPostsTable.post_date,
						$blogPostsTable.post_content,
						$blogPostsTable.post_modified,
						$blogPostsTable.comment_count
						FROM $blogPostsTable WHERE $blogPostsTable.post_status = 'publish'
						AND $blogPostsTable.post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL $how_long_days DAY)
						AND $blogPostsTable.post_type = 'post'";

			$thispos = $wpdb->get_results($db_query);

			foreach($thispos as $thispost) {
				if($sort_by == 'post_date') {
					$order = $thispost->post_date;
				}
				else{
					$order = $thispost->post_modified;
				}

				$post_dates[]			= $order;
				$post_guids[$order]		= $thispost->guid;
				$blog_IDs[$order]		= $blog;
				$post_IDs[$order]		= $thispost->ID;
				$post_titles[$order]	= $thispost->post_title;
				$post_authors[$order]	= $thispost->post_author;
				$post_contents[$order]	= $thispost->post_content;
				$comments[$order]		= $thispost->comment_count;
			}
		}

		rsort($post_dates);
		$union_results	= array_unique($post_dates);
		$ResultArray	= array_slice($union_results, 0, $how_many);

		foreach ($ResultArray as $date) {
			$ID					= $post_IDs[$date];
			$id_author			= $post_authors[$date];
			$post_url			= get_blog_permalink($blog_IDs[$date], $ID);/*$post_guids[$date];*/
			$post_title			= $post_titles[$date];
			$post_content		= $post_contents[$date];
			$post_date			= mysql2date(get_option('date_format'), $date);
			$post_time			= mysql2date(get_option('time_format'), $date);
			$total_comment		= $comments[$date];
			$user_info			= get_userdata($id_author);
			$author_blog_url	= get_blogaddress_by_id($user_info->primary_blog);
			$author_url			= $user_info->user_url;
			$author_email		= $user_info->user_email;
			$site_id			= get_post_meta ($ID, 'blogid', true);

			if($user_info->first_name) {
				$author_name = $user_info->first_name.' '.$user_info->last_name;
			}
			else{
				$author_name = $user_info->nickname;
			}

			if($remove_html) {
				$post_content = wpmu_cleanup_post($post_content);
			}

			$results = array();

			$results['ID']				= $ID;
			$results['post_url']		= $post_url;
			$results['post_title']		= $post_title;
			$results['post_content']	= wpmu_cut_article_by_words($post_content, $how_many_words);
			if ($results['post_content'] != $post_content)
				$results['post_content'] .= sprintf('  <a href="%s">%s</a>', $post_url, $more_text);
			$results['author_blog_url'] = $author_blog_url;
			$results['author_url'] 		= $author_url;
			$results['author_email']	= $author_email;
			$results['author_name'] 	= $author_name;
			$results['post_date']		= $post_date;
			$results['post_time']		= $post_time;
			$results['comment_count'] 	= $total_comment;

			$returns[] = $results;
		}

		$latest_posts = wpmu_bind_array_to_object($returns);
		return $latest_posts;
	}
}

function wpmu_bind_array_to_object($array) {
	$return = new stdClass();

	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$return->$k = wpmu_bind_array_to_object($v);
		}
		else {
			$return->$k = $v;
		}
	}
	return $return;
}

function wpmu_cut_article_by_words($original_text, $how_many) {
	$word_cut = strtok($original_text," ");

	$return = '';

	for ($i=1;$i<=$how_many;$i++) {
		$return	.= $word_cut;
		$return	.= (" ");
		$word_cut = strtok(" ");
	}

	$return .= '';
	return $return;
}

function wpmu_cleanup_post($source) {
	$replace_all_html = strip_tags($source);
	$bbc_tag = array('/\[caption(.*?)]\[\/caption\]/is');
	$result = preg_replace($bbc_tag, '', $replace_all_html);

	return $result;
}
?>