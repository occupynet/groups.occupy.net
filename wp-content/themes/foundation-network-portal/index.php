<?php get_header(); ?>

	<?php
	// Options Variables and Plugin Checks
		$post_offset = of_get_option('posts_in_orbit_slider');
		$display_slider = of_get_option('orbit_slider');
		$feedwordpress_plugin = is_plugin_active( 'feedwordpress/feedwordpress.php' );
		$events_manager_plugin = is_plugin_active( 'events-manager/events-manager.php' );
	?>
			
	<div id="content" class="clearfix">

	<?php

	//****************************
	//Check if multisite is active
	//****************************
	if ( is_multisite() ) { 
		//************************************
		//If multisite is active, display page
		//************************************
		?>
	
		<div id="main" class="eight columns clearfix" role="main">
		
			
			<?php if (is_category()) { ?>
				<h1 class="archive_title h2">
					<span><?php _e("Posts Categorized:", "bonestheme"); ?></span> <?php single_cat_title(); ?>
				</h1>
			<?php } elseif (is_home()) { ?> 
				<h1 class="archive_title h2">
					<span><?php wp_title(''); ?>
				</h1>
			<?php } elseif (is_tag()) { ?> 
				<h1 class="archive_title h2">
					<span><?php _e("Posts Tagged:", "bonestheme"); ?></span> <?php single_tag_title(); ?>
				</h1>
			<?php } elseif (is_author()) { ?>
				<h1 class="archive_title h2">
					<span><?php _e("Posts By:", "bonestheme"); ?></span> <?php get_the_author_meta('display_name'); ?>
				</h1>
			<?php } elseif (is_day()) { ?>
				<h1 class="archive_title h2">
					<span><?php _e("Daily Archives:", "bonestheme"); ?></span> <?php the_time('l, F j, Y'); ?>
				</h1>
			<?php } elseif (is_month()) { ?>
			    <h1 class="archive_title h2">
			    	<span><?php _e("Monthly Archives:", "bonestheme"); ?>:</span> <?php the_time('F Y'); ?>
			    </h1>
			<?php } elseif (is_year()) { ?>
			    <h1 class="archive_title h2">
			    	<span><?php _e("Yearly Archives:", "bonestheme"); ?>:</span> <?php the_time('Y'); ?>
			    </h1>
			<?php } ?>

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<?php $org_blog_id = get_post_meta ($post->ID, 'blogid', true);
			$blog_details = get_blog_details($org_blog_id);
			$syndicated_site = get_syndication_source ();
			$syndicated_url = get_syndication_source_link ();

			?>

			
			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
				
				<header>
					
					<h3 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					 
					<?php if (is_syndicated ()) { ?>
					<p class="meta"><span class="site-name"><a href="<?php echo $syndicated_url; ?>"><?php echo $syndicated_site; ?></a></span> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> <?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?> | <?php the_category(' | '); ?></p>
					<?php } else { ?>
					<p class="meta"><span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> <?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?> | <?php the_category(' | '); ?></p>
					<?php } ?>
				</header> <!-- end article header -->
			
				<section class="post_content">
				
					<div class="post-thumbnail">
					<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php echo feature_image('wpf-featured') ?></a>
					</div>
				
					<?php the_excerpt(); ?>
			
				</section> <!-- end article section -->
				
				<footer>

					<p class="tags"><?php the_tags('<span class="tags-title"></span> ', ' ', ''); ?></p>

					<?php edit_post_link('edit', '<p>', '</p>'); ?>

					<div style="clear:both;"></div>
					
				</footer> <!-- end article footer -->
			
			</article> <!-- end article -->
			
			<?php endwhile; ?>	
			
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
						
			
			<?php else : ?>
			
			<article id="post-not-found">
			    <header>
			    	<h1><?php _e("No Posts Yet", "bonestheme"); ?></h1>
			    </header>
			    <section class="post_content">
			    	<p><?php _e("Sorry, What you were looking for is not here.", "bonestheme"); ?></p>
			    </section>
			    <footer>
			    </footer>
			</article>
			
			<?php endif; ?>
	
		</div> <!-- end #main -->

		<?php get_sidebar(); // sidebar 1 ?>

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