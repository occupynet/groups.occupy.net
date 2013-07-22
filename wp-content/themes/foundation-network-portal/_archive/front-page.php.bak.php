<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>

	<div id="content">

	<?php
	//Check if multisite

	if ( is_multisite() ) { 
		//If multisite is active, display page ?>

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

				<?php // Get placeholder image defined in Theme Options
				$placeholder_image = of_get_option('placeholder_image'); ?>

				<ul class="bxslider">

				<?php if (is_plugin_active('events-manager/events-manager.php')) {

					//If Events Manager is active, display events coming up in slider
					echo EM_Events::output( array('format_header'=>'<ul class="bxslider">','format_footer'=>'</ul>','limit'=>5,'orderby'=>'date','format'=>'
						<li class="slider-item">
							<div class="event-image"><a href="#_EVENTURL">{no_image}<img src="' . $placeholder_image .'">{/no_image}{has_image}#_EVENTIMAGE{230,157}{/has_image}</a></div>
							<h3 class="event-title"><span class="event-date">#M #j:</span> #_EVENTLINK</h3>
						</li>
						'
						) );
					} else { 
					//If Events Manager isn't active, display recent posts

					global $post;
					$args = array( 'posts_per_page' => 5, 'post_type' => 'post' );
					$featuredposts = get_posts( $args );
					?>

					<?php foreach( $featuredposts as $post ) : setup_postdata($post); ?>
						<li class="slider-item">
							<a href="<?php the_permalink(); ?>">
							<?php 
							if ( has_post_thumbnail() ) { 
								echo '<div class="event-image">';
								the_post_thumbnail('medium');
								echo '</div>';
							} else {
								echo '<div class="event-image"><img src="' . $placeholder_image .'"></div>';
							}
							?>

				    	<h3 class="event-title"><?php the_title(); ?></h3>
				    	</a>
			        </li>
					<?php endforeach; ?>

				<?php } ?> 

				</ul>

			</article>
		

		</div>
		

		<div class="filter twelve columns clearfix">

			<h4 class="filter-add"><a href="/join">Contribute</a></h4>

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

			<!-- Display Filter -->
			<h4 class="filter-view">View: <span class="filter-title" id="view-current">List</span>
				<ul class="f-dropdown option-set" data-option-key="layoutMode">
				  <li><a href="#straightDown" data-option-value="straightDown" class="selected">List</a></li>
				  <li><a href="#masonry" data-option-value="masonry">Grid</a></li>
				</ul>
			</h4>

			<!-- To get only the filters relevant to the posts being displayed, return array of recent posts, then retrieve an array of categories and groups. Then, remove duplicates and sort alphabetically. -->

			<!-- Category Filter -->
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
				echo '<li><a href="#" data-option-value=".category-' . $category->slug . '">' . $category->name . '</a></li>';
			}
			?>

				</ul>
			</h4>
			<!-- Group Filter -->
			<h4 class="filter-group">Category: <span class="filter-title" id="group-current">All</span>
				<ul class="f-dropdown" data-option-key="filter">
					<li><a href="#" data-option-value="*" class="selected">All</a></li>
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

					 //If FeedWordPress is active, test posts to see if syndicated
					 //If FeedWordPress isn't active, don't test posts to see if syndicated

			?>
				
			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix network-post'); ?> role="article">
				
				<header>
					
					<p class="meta">
					<!-- If using FeedWordPress, display the name of the syndicated site instead of the Wordpress blogname -->
							<?php if (is_syndicated ()) { ?>
							<span class="site-name"><a href="<?php echo $syndicated_url; ?>"><?php echo $syndicated_site; ?></a></span> 
							<?php } else { ?>
							<span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span> 
							<?php } ?>
							<time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> 
							<?php 
							if (!is_syndicated ()) {
								_e("By ", "bonestheme");
								the_author_posts_link(); 
							} 
								the_category(' | '); 
							
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