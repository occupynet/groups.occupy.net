<?php
/*
Template Name: Groups Directory
*/
?>

<?php get_header(); ?>
			
			<div id="content">
			
				<div id="main" class="eight columns clearfix" role="main">

					<header>
							
						<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
					
					</header> <!-- end article header -->

					<ul class="groups-list">

					<?php
					$blogs = get_last_updated();
					
					foreach ($blogs AS $blog) {
						echo "
						<li class='group-item'>
						<h3>" . $site_icon . "<a href='http://".$blog["domain"].$blog["path"]."'>".get_blog_option( $blog[ 'blog_id' ], 'blogname' )."</a></h3>
						<div class=''>" . $blog['description'] . "</div>";

						switch_to_blog($blog["blog_id"]);
						$lastposts = get_posts('numberposts=1');

						foreach($lastposts as $post) :
							setup_postdata($post);
						?>
					
						<div class="site-description"><?php echo bloginfo('description'); ?></div>
						<div class="site-post">Latest Update: <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> - <?php  the_date(); ?></div>

						</li>
					
					<?php
					endforeach;
					restore_current_blog(); 
					}
					?>

					</ul>
					
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