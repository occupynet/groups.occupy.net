<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>

	<div id="content">

		<div id="promo" class="twelve columns clearfix">
		
			<!-- Home page intro text -->

			<article role="article" class="home-intro three columns">

				<div class="intro-text">
					<?php echo get_post_meta($post->ID, 'custom_tagline', true); ?>
				</div>

			</article>

			<article role="article" class="home-slider nine columns">

				<script>
				jQuery(document).ready(function(){
				  $('.bxslider').bxSlider({
					  minSlides: 3,
					  maxSlides: 3,
					  slideWidth: 220,
					  controls: true,
					  pager: false,
					  slideMargin: 5
					});
				});
				</script>

				<h2 class="slider-title">Upcoming Events <span class="slider-all-events"><a href="/events/">View All</a></span></h2>
				<?php if (class_exists('EM_Events')) {
					// Get placeholder image defined in Theme Options
					$placeholder_image = of_get_option('placeholder_image');
					echo EM_Events::output( array('format_header'=>'<ul class="bxslider">','format_footer'=>'</ul>','limit'=>5,'orderby'=>'date','format'=>'
						<li class="slider-item">
							<div class="event-image"><a href="#_EVENTURL">{no_image}<img src="' . $placeholder_image .'">{/no_image}{has_image}#_EVENTIMAGE{220,150}{/has_image}</a></div>
							<h3 class="event-title"><span class="event-date">#M #j:</span> #_EVENTLINK</h3>
						</li>
						'
						) );
					} ?> 

			</article>

		
		</div>
		
		<div id="main" class="twelve columns clearfix" role="main">


			<!-- Recent posts -->

			<h2 class="main-title">News </h2>

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
				     $org_blog_id = get_post_meta ($post->ID, 'blogid', true);
				     $blog_details = get_blog_details($org_blog_id);

			?>
				
			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix network-post'); ?> role="article">
				
				<header>
					
					<p class="meta"><span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></time> <?php the_category(' '); ?></p>
				
				</header> <!-- end article header -->
			
				<footer>
	
					<p class="tags"><?php the_tags('', ' ', ''); ?></p>
					<div style="clear: both;"></div>
					
				</footer> <!-- end article footer -->

				<section class="post_content clearfix">

					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

					<div class="post-thumbnail">
					<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'wpf-featured' ); ?></a>
					</div>

					<?php the_excerpt('100'); ?>
			
				</section> <!-- end article section -->
							
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