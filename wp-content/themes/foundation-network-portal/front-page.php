<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>

	<div id="content">

		<div id="promo" class="twelve columns clearfix">
		
			<!-- Home page intro text -->

			<article role="article" class="home-intro twelve columns clearfix">

				<div class="intro-text">
					<?php echo get_post_meta($post->ID, 'custom_tagline', true); ?>
				</div>

			</article>

			<article role="article" class="home-slider twelve columns clearfix">

				<script>
				jQuery(document).ready(function(){
				  $('.bxslider').bxSlider({
					  minSlides: 4,
					  maxSlides: 4,
					  slideWidth: 230,
					  controls: true,
					  pager: false,
					  slideMargin: 10
					});
				});
				</script>

				<?php if (class_exists('EM_Events')) {
					// Get placeholder image defined in Theme Options
					$placeholder_image = of_get_option('placeholder_image');
					echo EM_Events::output( array('format_header'=>'<ul class="bxslider">','format_footer'=>'</ul>','limit'=>5,'orderby'=>'date','format'=>'
						<li class="slider-item">
							<div class="event-image"><a href="#_EVENTURL">{no_image}<img src="' . $placeholder_image .'">{/no_image}{has_image}#_EVENTIMAGE{230,157}{/has_image}</a></div>
							<h3 class="event-title"><span class="event-date">#M #j:</span> #_EVENTLINK</h3>
						</li>
						'
						) );
					} ?> 

			</article>
		

		</div>
		
		<script src="/wp-includes/js/packery.pkgd.min.js"></script>
		<script>
		jQuery(document).ready(function(){
			var container = document.querySelector('#main');
			var pckry = new Packery( container, {
			  // options
			  itemSelector: '.network-post',
			  gutter: 10
			});
		});
		</script>
		
		<script>
		jQuery(document).ready(function(){
			$('#as-grid').click(function(){
				$('#main').addClass('packery');
				$('.by-view a').toggleClass('on');
				$('#main').packery('reloadItems');
			});
			$('#as-list').click(function(){
				$('#main').removeClass('packery');
				$('.by-view a').toggleClass('on');
				$('#main').packery('reloadItems');
			});
		});
		</script>

		<div class="filter twelve columns clearfix">

			<h4 class="filter-add"><a href="/join">Add a post</a></h4>

			<h4 class="filter-view">View:
				<a id="view-list" class="on">
					<svg xml:space="preserve" enable-background="new 0 0 48 48" viewBox="0 0 48 48" height="18px" width="18px" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1">
						<line y2="8.907" x2="48" y1="8.907" x1="0" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="24.173" x2="48" y1="24.173" x1="0" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="39.439" x2="48" y1="39.439" x1="0" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
					</svg>
				</a>
				<a id="view-grid">
					<svg xml:space="preserve" enable-background="new 0 0 48 48" viewBox="0 0 48 48" height="18px" width="18px" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1">
						<line y2="8.907" x2="9" y1="8.907" x1="0" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="24.173" x2="9" y1="24.173" x1="0" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="39.439" x2="9" y1="39.439" x1="0" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="8.907" x2="27" y1="8.907" x1="18" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="24.173" x2="27" y1="24.173" x1="18" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="39.439" x2="27" y1="39.439" x1="18" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="8.907" x2="45" y1="8.907" x1="36" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="24.173" x2="45" y1="24.173" x1="36" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
						<line y2="39.439" x2="45" y1="39.439" x1="36" stroke-miterlimit="10" stroke-width="8" stroke="#000000" fill="none"></line>
					</svg>
				</a>
			</h4>

			<h4 class="filter-category">Category:
				<a id="category-all" class="on">All</a>
			</h4>


			<h4 class="filter-format">Format:
				<a id="format-all" class="on">All</a>
			</h4>

		</div>


		<div id="main" class="twelve columns clearfix" role="main">




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
				     $org_blog_id = get_post_meta ($post->ID, 'blogid', true);
				     $blog_details = get_blog_details($org_blog_id);

			?>
				
			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix network-post'); ?> role="article">
				
				<header>
					
					<p class="meta"><span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span> 
						<time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></time> 
						<?php the_category(' '); ?>
					</p>
				
				</header> <!-- end article header -->
			
				<footer>
	
					<p class="tags"><?php the_tags('', ' ', ''); ?></p>

					<?php edit_post_link('edit', '<p>', '</p>'); ?>
					
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