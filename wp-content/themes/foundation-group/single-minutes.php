<?php get_header(); ?>
			
			<div id="content" class="clearfix">
			
				<div id="main" class="eight columns clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">


					<?php
					$meeting_date = get_post_meta($post->ID,'be_meeting_date',true);
					$meeting_taker = get_post_meta($post->ID,'be_meeting_minutes_takers',true);
					$meeting_agenda = get_post_meta($post->ID,'be_meeting_agenda',true);
					$meeting_attendees = get_post_meta($post->ID,'be_meeting_attendees',true);
					?>
						
						<header>
							
							<h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>

							<p class="meta">

							<?php
							echo '<p class="meeting-date">Meeting Date: ' . $meeting_date . '</p>';
							echo '<p class="minutes-taker">Minutes Taker: ' . $meeting_taker . '</p>';
							echo '<p class="meeting-attendees"><h3>Meeting Attendees</h3>' . $meeting_attendees . '</p>';
							?>
							</p>
													
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">

							<?php the_content(); ?>
							
						</section> <!-- end article section -->
						
						<footer>

							<p class="meta">
								<?php echo '<p class="meeting-agenda"><h3>Agenda</h3>' . $meeting_agenda . '</p>'; ?>


								<time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> <?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?> | <?php the_category(' | '); ?>.</p>
								<?php edit_post_link('edit', '<p class="meta edit-link">', '</p>'); ?>
			
							<?php the_tags('<p class="tags">', ' ', '</p>'); ?>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php// comments_template(); ?>
					
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