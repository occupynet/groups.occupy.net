<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>

	<div id="content">

		<div id="main" class="twelve columns clearfix" role="main">

			<!-- Home page intro text -->

			<article role="article">

				<div class="intro-text">
					<?php echo get_post_meta($post->ID, 'custom_tagline', true); ?>
				</div>

				<script>
				jQuery(document).ready(function(){
				  $('.bxslider').bxSlider({
					  minSlides: 3,
					  maxSlides: 3,
					  slideWidth: 360,
					  slideMargin: 20
					});
				});
				</script>

				<h2>Upcoming Events <span class="slider-all-events"><a href="/events/">View All</a></span></h2>
				<?php if (class_exists('EM_Events')) {
					// Get placeholder image defined in Theme Options
					$placeholder_image = of_get_option('placeholder_image');
					echo EM_Events::output( array('format_header'=>'<ul class="bxslider">','format_footer'=>'</ul>','limit'=>5,'orderby'=>'date','format'=>'
						<li class="slider-item">
							<div class="event-image"><a href="#_EVENTURL">{no_image}<img src="' . $placeholder_image .'">{/no_image}{has_image}#_EVENTIMAGE{350,250}{/has_image}</a></div>
							<h3 class="event-title"><span class="event-date">#M #j:</span> #_EVENTLINK</h3>
						</li>
						'
						) );
					} ?> 

			</article>

			<!-- Recent posts -->

			<?php

			if ($orbit_slider) { //If slider is turned on, offset posts by number set in Theme Options
				$args = array(
					'post_type' => 'post',
					'offset' => $number_featured_posts
				);
			} else {
				$args = array( //If the slider is turned off, show all posts
					'post_type' => 'post',
					'offset' => 0
				);
			}

			$home_posts = new WP_Query( $args ); 
			 
			if($home_posts->have_posts()) : 
			      while($home_posts->have_posts()) : 
			         $home_posts->the_post();
			?>
				
			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">

				
				<header>
					
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					
					<p class="meta"><?php _e("Posted", "bonestheme"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> <?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?> <span class="amp">&</span> <?php _e("filed under", "bonestheme"); ?> <?php the_category(', '); ?>.</p>
				
				</header> <!-- end article header -->
			
				<section class="post_content clearfix">
					<div class="post-thumbnail">
					<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'wpf-featured' ); ?></a>
					</div>

					<?php the_excerpt('100'); ?>
			
				</section> <!-- end article section -->
				
				<footer>
	
					<p class="tags"><?php the_tags('<span class="tags-title">Tags:</span> ', ' ', ''); ?></p>

					<div style="clear: both;"></div>
					
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
				
			<?php endif; ?>	
				
			<!-- end recent articles -->

		</div> <!-- end #main -->

		<?php// get_sidebar('sidebar2'); // sidebar 2 ?>

	</div> <!-- end #content -->

<?php get_footer(); ?>