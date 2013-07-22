<?php
/*
Template Name: Homepage - Test
*/
?>

<?php get_header(); ?>

<?php
// Options Variables and Plugin Checks
	$post_offset = of_get_option('posts_in_orbit_slider');
	$display_slider = of_get_option('orbit_slider');
	$feedwordpress_plugin = is_plugin_active( 'feedwordpress/feedwordpress.php' );
	$events_manager_plugin = is_plugin_active( 'events-manager/events-manager.php' );
?>

	<div id="content">

	<?php
	//****************************
	//Check if multisite is active
	//****************************
	if ( is_multisite() ) { 
		//************************************
		//If multisite is active, display page
		//************************************
		?>

	<div id="main" class="twelve columns clearfix main-feeds" role="main">

			<?php
			$number_posts = 20;
			$custom_post_type = 'post';
			$post_status = 'publish';
			$sorting_order = 'DESC';
			$blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
                public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'  ORDER BY last_updated DESC");

			foreach( $blogs as $blog_key ) {
            // Options: Site URL, Blog Name, Date Format
	            ${'blog_url_'.$blog_key} = get_blog_option($blog_key,'siteurl');
	            ${'blog_name_'.$blog_key} = get_blog_option($blog_key,'blogname');
	            ${'date_format_'.$blog_key} = get_blog_option($blog_key,'date_format');
	            // Orderby
	            $orderby = 'post_date';

	            $args = array(
                    'numberposts' => $number_posts,
                    'post_status' => $post_status,
                    'post_type' => $custom_post_type,
                    'orderby' => $orderby
                );

                // Switch to the blog
	            switch_to_blog($blog_key);
	            // Get posts
	            ${'posts_'.$blog_key} = get_posts($args);

	            // Check if posts with the defined criteria were found
	            if( empty(${'posts_'.$blog_key}) ) {
	                /* If no posts matching the criteria were found then
	                 * move to the next blog
	                 */
	                next($blogs);
	            }

	            // Put everything inside an array for sorting purposes
	            foreach( ${'posts_'.$blog_key} as $post ) {
	                // Access all post data
	                setup_postdata($post);
	                // Put everything inside another array using the modified date as
                    // the array keys
                    $all_posts[$post->post_modified] = $post;
	            }
	            // The guid is the only value which can differenciate a post from
                // others in the whole network
                $all_permalinks[$post->guid] = get_blog_permalink($blog_key, $post->ID);
                $all_blogkeys[$post->guid] = $blog_key;
            
            }
            // Back the current blog
            restore_current_blog();
			// Sort the array
			@krsort($all_posts);

			foreach( $all_posts as $field ) {
				$post_title = $field->post_title;
				$post_url = $field->guid;
				$post_permalink = get_permalink( $field->id );
				$post_id = $field->id;
				// $post_author = the_author_meta( 'display_name', $field->post_author );
				$post_date = date_i18n(get_option('date_format') ,strtotime(trim($field->post_date)));
				$blog_name = get_blog_option($blog_key,'blogname');
				$post_content = $field->post_content;
				$post_categories = get_the_category_list( ' | ', '', $field->id );
				$post_tags = wp_get_post_tags( $field->id, array( 'fields' => 'name' ) );
				if(function_exists('home_excerpt')) {
					$post_excerpt = home_excerpt(50, $field->post_content, $field->guid, '');
				} 
				?>

			<!-- recent articles -->

			<article id="post-<?php $post_id; ?>" class="clearfix network-post" role="article">
				
				<header>
					<p class="meta">
						<span class="site-name"><a href="<?php echo $post_permalink; ?>"><?php echo $blog_name; ?></a></span>
						<time datetime="<?php echo $post_date; ?>" pubdate><?php $post_date; ?></time>
						<?php echo $post_categories; ?>
					</p>
				</header>

				<footer>
					<p class="tags"><?php the_tags('', ' ', ''); ?></p>

					<?php edit_post_link('edit', '<p>', '</p>'); ?>
					
					<div style="clear: both;"></div>
				</footer> <!-- end article footer -->

				<section class="post_content clearfix">
					<h2><a href="<?php echo post_permalink( $post_id ); ?>" rel="bookmark" title="<?php echo $post_title; ?>"><?php echo $post_title; ?></a></h2>

					<div class="post-thumbnail">
					<a href="<?php echo $post_permalink; ?>" title="<?php echo $post_title; ?>"><?php echo feature_image('wpf-featured') ?></a>
					</div>

					<p><?php echo $post_excerpt; ?></p>
				</section> <!-- end article section -->

			</article>
				
				<?php 
				// echo 'Title: ' . $post_title;
				// echo "URL: <a href='". $post_permalink ."'>". $post_title ."</a>";
				// echo 'Blog Name: ' . $blog_name;
				// echo 'Author: ' . $post_author;
				// echo 'Date: ' . $post_date;
				// echo $post_categories;
				// if(function_exists('home_excerpt')) {
				// 	$post_excerpt = home_excerpt(50, $field->post_content, $field->guid, '');
				// 	echo 'excerpt: ' . $post_excerpt;
				// }
				// echo 'Link: ' . $post_permalink;
				// var_dump($field);
			   
				// echo '<br />'; 
				?>


			<?php } ?>

			<!-- recent articles -->

			
		</div> <!-- end #main -->
		<?php if (function_exists('page_navi')) { // if expirimental feature is active ?>
				
				<?php page_navi(); // use the page navi function ?>

			<?php } else { // if it is disabled, display regular wp prev & next links ?>
				<nav class="wp-prev-next">
					<ul class="clearfix">
						<li class="prev-link"><?php next_posts_link(_e('&laquo; Older Entries', "bonestheme")) ?></li>
						<li class="next-link"><?php previous_posts_link(_e('Newer Entries &raquo;', "bonestheme")) ?></li>
					</ul>
				</nav>
			<?php } ?>

		<?php } else {
		//****************************************
		//If multisite isn't active, display error
		//****************************************
		?>

		<div id="promo" class="twelve columns clearfix">
			<article role="article" class="twelve columns clearfix">
				<div class="alert-box alert">This theme is intended for use as the main site on a <a href="https://codex.wordpress.org/Create_A_Network" target="_blank">Wordpress multisite network</a>. 
					Please activate <a href="https://codex.wordpress.org/Create_A_Network" target="_blank">multisite</a> in order to use this theme.</div>
			</article>		
		</div>

		<?php } ?>

	</div> <!-- end #content -->


<?php get_footer(); ?>