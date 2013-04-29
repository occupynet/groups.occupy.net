<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>

	<div id="content">

		<div id="main" class="eight columns clearfix" role="main">

			<article role="article">

				<?php

				$orbit_slider = of_get_option('orbit_slider'); //Set in Theme Options
				$number_featured_posts = of_get_option('posts_in_orbit_slider'); //Value set in Theme Options
				if ($orbit_slider){

				?>
				
				<header>
				
					<div id="featured">

						<?php
							global $post;
							$tmp_post = $post;
							$args = array( 'numberposts' => $number_featured_posts );
							$myposts = get_posts( $args );
							foreach( $myposts as $post ) :	setup_postdata($post);
								$alt_text = get_post_meta($img_id , '_wp_attachment_image_alt', true);
								$post_thumbnail_id = get_post_thumbnail_id();
								$featured_src = wp_get_attachment_image_src( $post_thumbnail_id, 'wpf-home-feature' );
						?>
						
						<div class="featured-slider">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php the_excerpt('20'); ?>
							<!-- <p><a href="<?php the_permalink(); ?>" class="button nice radius">Read more »</a></p> -->
						</div>
						
						<?php endforeach; ?>
						<?php $post = $tmp_post; ?>

					</div>
					
				</header>

				<script type="text/javascript">
				   $(window).load(function() {
				       $('#featured').orbit({ 
				       	fluid: '16x7'
				       });
				   });
				</script>

			<?php } 

			// Reset Query
			wp_reset_query(); ?>

			</article>

			<!-- Home page intro text -->

			<article role="article">

				<div class="intro-text">
					<?php echo get_post_meta($post->ID, 'custom_tagline', true); ?>
				</div>

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

		<?php get_sidebar('sidebar2'); // sidebar 2 ?>

	</div> <!-- end #content -->

<?php get_footer(); ?>