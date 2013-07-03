<?php
/*
Template Name: Groups Directory
*/
?>

<?php
// Options Variables and Plugin Checks
	$post_offset = of_get_option('posts_in_orbit_slider');
	$display_slider = of_get_option('orbit_slider');
	$feedwordpress_plugin = is_plugin_active( 'feedwordpress/feedwordpress.php' );
	$events_manager_plugin = is_plugin_active( 'events-manager/events-manager.php' );
?>

<?php get_header(); ?>
			
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

				<div id="main" class="eight columns clearfix" role="main">

					<header>
							
						<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
					
					</header> <!-- end article header -->

					<ul class="groups-list">

						<!-- If Site Categories is active, display category with the site.
						Would be nice to add isotype filtering here. -->

					<?php

					//Returns a list of recently updated blogs
					$blogs = get_last_updated();
					
					foreach ($blogs AS $blog) {
						?>
						<li class="group-item clearfix">

						<h3><a href="http://<?php echo $blog[ 'domain' ] . $blog[ 'path' ] ?>"><?php echo get_blog_option( $blog[ 'blog_id' ], 'blogname' ) ?></a></h3>

						<?php 
						//Returns the most recent post for the site
						$posts = get_posts('numberposts=1');

						foreach($posts as $post) :
							setup_postdata($post);
					
						?>
						<div class="site-description"><?php echo bloginfo('description'); ?></div>
						<div class="site-post">Latest Update: <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> - <?php  the_date(); ?></div>

						</li>
					
					<?php
					endforeach;
					restore_current_blog(); 
					}
					?>

					</ul>
					
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