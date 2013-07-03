<?php
/*
Template Name: Homepage
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

		<div id="promo" class="twelve columns clearfix">
		
			<!-- Home page intro text -->

			<article role="article" class="home-intro twelve columns clearfix">

				<div class="intro-text">
					<?php echo get_post_meta($post->ID, 'custom_tagline', true); ?>
				</div>

			</article>

			<!-- Check if slider is selected in theme options -->
			<?php if (of_get_option('orbit_slider')) { ?>

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

				<!-- Check which post type is selected to display -->
				<?php $slider_post_type = of_get_option('slider_item_type'); ?>

				<!-- If posts are selected in the theme options, display them - add post type class -->
				<?php if ($slider_post_type == 'post') { ?>

					<article role="article" class="home-slider <?php echo $slider_post_type; ?> twelve columns clearfix">
						<ul class="bxslider">
							<?php sliderposts() ?>
						</ul>
					</article>

				<?php } elseif ($slider_post_type == 'event') { ?>
					<!-- Check if Events Manager is active -->
					<?php if (is_plugin_active('events-manager/events-manager.php')) { ?>
					<!-- If events are selected in the theme options, display them - add post type class -->
					<article role="article" class="home-slider <?php echo $slider_post_type; ?> twelve columns clearfix">
						<?php sliderevents() ?>
					</article>
					<?php } ?>

				<?php } ?>

			<?php } ?>

		</div>
		

		<div class="filter twelve columns clearfix">

			<h4 class="contribute filter-add"><a href="/join">Contribute</a></h4>

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
					<?php $categories = get_categories(); 
					foreach ($categories as $category) {
						echo '<li><a href="#" data-option-value=".category-' . $category->slug . '">' . $category->name . ' (' . $category->count . ')</a></li>';
					} ?> 
				</ul>
			</h4>
			<!-- Group Filter -->
			<!-- <h4 class="filter-group">Group: <span class="filter-title" id="group-current">All</span>
				<ul class="f-dropdown" data-option-key="filter">
					<li><a href="#" data-option-value="*" class="selected">All</a></li>
				</ul>
			</h4> -->

		</div>

		<div id="main" class="twelve columns clearfix main-feeds" role="main">

			<?php
			if (($display_slider) && ($slider_post_type == 'post')) { 
				$post_offset = of_get_option('posts_in_orbit_slider');
			} else {
				$post_offset ='0';
			}

			$args = array(
				'post_type' => 'post',
				'offset' => $post_offset,
			);

			$recent_posts = new WP_Query( $args ); 
			 
			if($recent_posts->have_posts()) : 
			      while($recent_posts->have_posts()) : 
			         $recent_posts->the_post();
				     $org_blog_id = get_post_meta ($post->ID, 'blogid', true);
				     $blog_details = get_blog_details($org_blog_id);
				     if ($feedwordpress_plugin) {
				     	$source_name = get_syndication_source ();
				     	$source_link = get_syndication_source_link ();
				     } 
			     	$group_name = $blog_details->blogname;
			     	$group_link = $blog_details->siteurl;

			?>

			<!-- recent articles -->

			<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix network-post'); ?> role="article">
				
				<header>
					<p class="meta">
					<!-- If using FeedWordPress, display the name of the syndicated site instead of the Wordpress blogname -->
					<?php if ($feedwordpress_plugin) { ?>

						<?php if (is_syndicated ()) { ?>
						<span class="site-name"><a href="<?php echo $source_link; ?>"><?php echo $source_name; ?></a></span> 
						<?php } else { ?>
						<span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span> 
						<?php } ?>
						<time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> 
						<?php 
						if (!is_syndicated ()) {
							the_author_posts_link(); 
						}
							the_category(' | '); 
						?>

					<?php } else { ?>
					
					<!-- If not using FeedWordPress, display the Wordpress blogname -->
						<span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span>
						<time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> 
						<?php the_author_posts_link(); ?> <?php the_category(' | '); ?>
					<?php } ?>
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
					<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php echo feature_image('wpf-featured') ?></a>
					</div>

					<?php the_excerpt('100'); ?>
			
				</section> <!-- end article section -->
							
			</article> <!-- end article -->

			<!-- end recent articles -->

			<?php endwhile; else: ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php endif; ?>

		</div> <!-- end #main -->

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