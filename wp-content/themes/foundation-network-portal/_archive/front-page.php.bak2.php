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

				<?php
					//Use placeholder image found in Theme options if no image is provided.
					$placeholder_image = of_get_option('placeholder_image');
					$orbit_slider = of_get_option('posts_in_orbit_slider');
					$numberposts = of_get_option('posts_in_orbit_slider');
				?>

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

					sliderposts(); //Function accepts before and after arguments for post title - default is $before_title='<h3 class="event-title">', $after_title='</h3>'

				} ?> 

				</ul>

			</article>
			<?php wp_reset_postdata(); ?>

		</div>
		

		<div class="filter twelve columns clearfix">

			<h4 class="filter-add"><a href="/join">Contribute</a></h4>

			<?php

			if ($orbit_slider) { //If slider is turned on, offset posts by number set in Theme Options
				$args = array(
					'post_type' => 'post',
					'offset' => $numberposts
				);
			} else {
				$args = array( //If the slider is turned off, show all posts
					'post_type' => 'post',
					'offset' => 0
				);
			}

			?>


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


				</ul>
			</h4>
			<!-- Group Filter -->
			<h4 class="filter-group">Group: <span class="filter-title" id="group-current">All</span>
				<ul class="f-dropdown" data-option-key="filter">
					<li><a href="#" data-option-value="*" class="selected">All</a></li>
				</ul>
			</h4>

		</div>


		<div id="main" class="twelve columns clearfix main-feeds" role="main">

			<!-- recent articles -->


				
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