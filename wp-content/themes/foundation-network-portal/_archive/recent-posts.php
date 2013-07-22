<?php

function recent_posts($numberposts = 15,$orderby = 'id') {

		//Returns a list of recently updated blogs
		$numberposts = 15;
		$blogs = get_last_updated(0);
		// $ignore_blogs = '';
		$orderby = "id"; //change this to whatever key you want from the array

		foreach ($blogs AS $blog) {
			$post_blog_id = $blog['blog_id'];
			if($post_blog_id != $blog_id) {

			//Returns the most recent posts for the site
			$posts = get_posts('posts_per_page=$numberposts&orderby=$orderby');
			$order_posts = array();

			foreach ($posts as $post) {
				echo $post->post_date . '<br />';
				$order_posts[] = $post;
			}

			restore_current_blog();
		}
	}
}

?>