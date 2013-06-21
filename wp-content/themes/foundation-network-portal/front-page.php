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
		

		<div class="filter twelve columns clearfix">

			<h4 class="filter-add"><a href="/join">Contribute</a></h4>

			
			<h4 class="filter-view">View: <span class="filter-title" id="view-current">List</span>
				<ul class="f-dropdown option-set" data-option-key="layoutMode">
				  <li><a href="#straightDown" data-option-value="straightDown" class="selected">List</a></li>
				  <li><a href="#masonry" data-option-value="masonry">Grid</a></li>
				</ul>
			</h4>



			<h4 class="filter-category">Category: <span class="filter-title" id="category-current">All</span>
				<ul class="f-dropdown" data-option-key="filter">
					<li><a href="#" data-option-value="*" class="selected">All</a></li>

			<?php 
			$args=array(
			  'taxonomy' => 'category',
			  'order' => 'ASC'
			  );
			$categories=get_categories($args);
			
			foreach($categories as $category) {
				$category_slug = $category->slug;
				echo '<li><a href="#" data-option-value=".category-' . $category_slug . '">' . $category . '</a></li>';
			}
			?>

				</ul>
			</h4>

			<h4 class="filter-category">Category: <span class="filter-title" id="category-current">All</span>
				<ul class="f-dropdown" data-option-key="filter">
				  <li><a href="#" data-option-value="*" class="selected">All</a></li>
				  <li><a href="#" data-option-value=".category-actions">Actions</a></li>
				  <li><a href="#" data-option-value=".category-citibank">Banks</a></li>
				  <li><a href="#" data-option-value=".category-featured">Featured</a></li>
				  <li><a href="#" data-option-value=".category-news">News</a></li>
				</ul>
			</h4>

			<h4 class="filter-format">Format: <span class="filter-title" id="format-current">All</span>
				<ul class="f-dropdown" data-option-key="filter">
				  <li><a href="#" data-option-value="*" class="selected">All</a></li>
				  <li><a href="#" data-option-value=".format-standard">Standard</a></li>
				  <li><a href="#" data-option-value=".format-photo">Photo</a></li>
				  <li><a href="#" data-option-value=".format-video">Video</a></li>
				  <li><a href="#" data-option-value=".format-quote">Quote</a></li>
				</ul>
			</h4>

		</div>


		<div id="main" class="twelve columns clearfix main-feeds" role="main">

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
				     $syndicated_site = get_syndication_source ();
					 $syndicated_url = get_syndication_source_link ();

			?>
				
			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix network-post'); ?> role="article">
				
				<header>
					
					<p class="meta">
							<?php if (is_syndicated ()) { ?>
							<span class="site-name"><a href="<?php echo $syndicated_url; ?>"><?php echo $syndicated_site; ?></a></span> 
							<?php } else { ?>
							<span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span> 
							<?php } ?>
							<time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> 

							<?php 
							if (!is_syndicated ()) {
								_e("by", "bonestheme"); ?> <?php the_author_posts_link(); 
							} 
							?>

							<?php if ($category->cat_name != 'Uncategorized') {
								the_category(' | '); 
							}
							?>
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

	<script src="wp-content/themes/foundation-network-portal/js/jquery.isotope.min.js"></script>
	<script>
	jQuery(document).ready(function(){
		$(function(){
		  
		  var $container = $('#main');
		
		  $container.isotope({
		    itemSelector : '.network-post'
		  });
		  
		  
		  var $optionSets = $('.f-dropdown'),
		      $optionLinks = $optionSets.find('a');
		
		  $optionLinks.click(function(){
		    var $this = $(this);
		    // don't proceed if already selected
		    if ( $this.hasClass('selected') ) {
		      return false;
		    }
		    
		    var $optionSet = $this.parents('.f-dropdown');
		    $optionSet.find('.selected').removeClass('selected');
		    $this.addClass('selected');

		    var $linkTitle = $this.text();
		    var $currentTitle = $this.parents('h4');
		    $currentTitle.find('.filter-title').text($linkTitle);
		    
		
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

		    if ( key === 'layoutMode' && value === 'straightDown' ) {
		      $container.removeClass('masonry');
		      $container.isotope('reLayout');
		    }
		    if ( key === 'layoutMode' && value === 'masonry' ) {
		      $container.addClass('masonry');
		      $container.isotope('reLayout');
		    }
		    
		    return false;
		  });
		
		  
		});
	});
	</script>


<?php get_footer(); ?>