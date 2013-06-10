<?php
/*
Template Name: News Page
*/
?>
<?php get_header(); ?>
			
			<div id="content">
			
				<div id="main" class="eight columns clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
						<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix network-post'); ?> role="article">
							
							<header>
								
								<p class="meta"><span class="site-name"><a href="<?php echo $blog_details->siteurl; ?>"><?php echo $blog_details->blogname; ?></a></span> 
									<time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></time> 
									<?php the_category(' '); ?>
								</p>
							
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
					
					<?php comments_template(); ?>
					
					<?php endwhile; ?>		
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1>Not Found</h1>
					    </header>
					    <section class="post_content">
					    	<p>Sorry, but the requested resource was not found on this site.</p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
				<?php get_sidebar(); // sidebar 1 ?>
    
			</div> <!-- end #content -->

<?php get_footer(); ?>