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
		
		<script src="/wp-includes/js/jquery.isotope.min.js"></script>
		<script>
			jQuery(document).ready(function(){
			      var $container = $('#main');
			      $container.isotope({
			        itemSelector: '.network-post',
			        layoutMode: 'masonry'
			      });


			      // change layout
			      var isHorizontal = false;
			      function changeLayoutMode( $link, options ) {
			        var wasHorizontal = isHorizontal;
			        isHorizontal = $link.hasClass('horizontal');
			
			        if ( wasHorizontal !== isHorizontal ) {
			          // orientation change
			          // need to do some clean up for transitions and sizes
			          var style = isHorizontal ? 
			            { height: '80%', width: $container.width() } : 
			            { width: 'auto' };
			          // stop any animation on container height / width
			          $container.filter(':animated').stop();
			          // disable transition, apply revised style
			          $container.addClass('no-transition').css( style );
			          setTimeout(function(){
			            $container.removeClass('no-transition').isotope( options );
			          }, 100 )
			        } else {
			          $container.isotope( options );
			        }
			      }
			
			
			      
			      var $optionSets = $('#options .option-set'),
			          $optionLinks = $optionSets.find('a');
			
			      $optionLinks.click(function(){
			        var $this = $(this);
			        // don't proceed if already selected
			        if ( $this.hasClass('selected') ) {
			          return false;
			        }
			        var $optionSet = $this.parents('.option-set');
			        $optionSet.find('.selected').removeClass('selected');
			        $this.addClass('selected');
			  
			        // make option object dynamically, i.e. { filter: '.my-filter-class' }
			        var options = {},
			            key = $optionSet.attr('data-option-key'),
			            value = $this.attr('data-option-value');
			        // parse 'false' as false boolean
			        value = value === 'false' ? false : value;
			        options[ key ] = value;
			        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
			          // changes in layout modes need extra logic
			          changeLayoutMode( $this, options )
			        } else {
			          // otherwise, apply new options
			          $container.isotope( options );
			        }
			        
			        return false;
			      });
			
			      

			});

    	</script>
		

		<div class="filter twelve columns clearfix">

			<h4 class="filter-add"><a href="/join">Add a post</a></h4>

			
			<h4 class="filter-view">View: <span id="view-current">List</span>
				<ul class="f-dropdown option-set" data-option-key="layoutMode">
				  <li><a href="#straightDown" data-option-value="straightDown" class="selected">List</a></li>
				  <li><a href="#masonry" data-option-value="masonry">Grid</a></li>
				</ul>
			</h4>

			<h4 class="filter-category">Category: <span id="category-current">All</span>
				<ul class="f-dropdown">
				  <li><a href="#" data-filter="*">All</a></li>
				  <li><a href="#" data-filter=".category-actions">Actions</a></li>
				  <li><a href="#" data-filter=".category-citibank">Banks</a></li>
				  <li><a href="#" data-filter=".category-featured">Featured</a></li>
				  <li><a href="#" data-filter=".category-news">News</a></li>
				</ul>
			</h4>

			<h4 class="filter-format">Format: <span id="format-current">All</span>
				<ul class="f-dropdown">
				  <li><a href="#" data-filter="*">All</a></li>
				  <li><a href="#" data-filter=".format-standard">Standard</a></li>
				  <li><a href="#" data-filter=".format-photo">Photo</a></li>
				  <li><a href="#" data-filter=".format-video">Video</a></li>
				  <li><a href="#" data-filter=".format-quote">Quote</a></li>
				</ul>
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