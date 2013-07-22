<?php get_header(); ?>
			
			<div id="content" class="clearfix">
			
				<div id="main" class="eight columns clearfix" role="main">
				
					<?php if (is_category()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("Posts Categorized:", "bonestheme"); ?></span> <?php single_cat_title(); ?>
						</h1>
					<?php } elseif (is_tag()) { ?> 
						<h1 class="archive_title h2">
							<span><?php _e("Posts Tagged:", "bonestheme"); ?></span> <?php single_tag_title(); ?>
						</h1>
					<?php } elseif (is_tax()) { ?> 
						<h1 class="archive_title h2">
							<span><?php _e("In Category:", "bonestheme"); ?></span> <?php get_terms( 'organization_categories' ); ?>
						</h1>	
					<?php } elseif (is_author()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("Posts By:", "bonestheme"); ?></span> <?php get_the_author_meta('display_name'); ?>
						</h1>
					<?php } elseif (is_day()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("Daily Archives:", "bonestheme"); ?></span> <?php the_time('l, F j, Y'); ?>
						</h1>
					<?php } elseif (is_month()) { ?>
					    <h1 class="archive_title h2">
					    	<span><?php _e("Monthly Archives:", "bonestheme"); ?>:</span> <?php the_time('F Y'); ?>
					    </h1>
					<?php } elseif (is_year()) { ?>
					    <h1 class="archive_title h2">
					    	<span><?php _e("Yearly Archives:", "bonestheme"); ?>:</span> <?php the_time('Y'); ?>
					    </h1>
					<?php } ?>


					<!-- Categories -->

					<h2>Categories</h2>

					<?php
					$orgargs = array(
						'post_type' => 'organizations',
						'post_status' => 'publish'
					);
					$get_organizations = get_posts ( $orgargs );

					$taxonomies = array( 
					    'organization_categories'
					);

					//$termargs = array(
					//	'show_count'         => 1,
					//	'taxonomy'           => 'organization_categories',
					//	'type'               => 'organizations'
					//); 
					$terms = get_terms( $taxonomies );

					?>

					<!-- Terms list -->

					<ul class="terms-list">

					<?php
					foreach($terms as $term) {
						$term_link = get_term_link( $term, $taxonomies );
						$term_name = $term->name;
						echo "<li><a href=\"$term_link\"> $term->name </a> </li>";
					} ?>

					</ul>


					<!-- Get list of Organizations by Category -->

					<?php

					 // get all the categories from the database
		            //$terms = get_terms( $taxonomies );

					foreach($terms as $term) {

						$term_link = get_term_link( $term, $taxonomies );
						$term_name = $term->name;
						$term_id = $term->id;
						$term_slug = $term->slug;

						?>


						<h3><?php echo $term_name ?></h3>

						<ul class="organization-listing">

						<?php 

						// Get List of Posts for the Term

						$taxargs = array(
						    'post_type'=> 'organizations',
						    'organization_categories'    => $term->name,
						    );              

						$the_query = new WP_Query( $taxargs );
						if($the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); 

						?>

						<li>
							<a href="<?php the_permalink();?>"><?php echo the_title(); ?></a><br />
							<?php the_meta(); ?>
						</li>

						<?php endwhile; endif;

						echo '</ul>';

						// Reset things, for good measure
					    $member_group_query = null;
					    wp_reset_postdata();

					} ?>

					
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
    
			</div> <!-- end #content -->

<?php get_footer(); ?>