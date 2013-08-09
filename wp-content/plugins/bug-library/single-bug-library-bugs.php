<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

			<div id='bug-library-list'>
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			
			<?php 
				global $post, $wpdb;
				//print_r($post);
				$products = wp_get_post_terms( $post->ID, "bug-library-products");
				$product = $products[0];
				$statuses = wp_get_post_terms( $post->ID, "bug-library-status");
				$status = $statuses[0];
				$types = wp_get_post_terms( $post->ID, "bug-library-types");
				$type = $types[0];
				$productversion = get_post_meta($post->ID, "bug-library-product-version", true);
				$resolutiondate = get_post_meta($post->ID, "bug-library-resolution-date", true);
				$resolutionversion = get_post_meta($post->ID, "bug-library-resolution-version", true);
				$imagepath = get_post_meta($post->ID, "bug-library-image-path", true);
				
				$dateformat = get_option("date_format");
				$postdatetimestamp = $wpdb->get_var("SELECT UNIX_TIMESTAMP(post_date) from " . $wpdb->get_blog_prefix() . "posts where ID = " . $post->ID);
				
				$genoptions = get_option('BugLibraryGeneral', "");
				if ($genoptions['permalinkpageid'] != -1)
				{
					$parentpage = get_post($genoptions['permalinkpageid']);
					//print_r($parentpage);
					$parentslug = $parentpage->post_name;
				}
				else
				{
					$parentslug = "bugs";
				}
			?>

				<div id='bug-library-breadcrumb'><a href='/<?php echo $parentslug; ?>'>All Items</a> &raquo; <a href='/<?php echo $parentslug; ?>/?bugcatid=<?php echo $products[0]->term_id; ?>'><?php echo $products[0]->name; ?></a> &raquo; Bug #<?php echo $post->ID; ?></div>
				
				<div id='bug-library-item-table'>
					<table>
					
						<tr id="<?php if ($counter % 2 == 1) echo 'odd'; else echo 'even'; ?>">
							<td id='bug-library-type'><div id='bug-library-type-<?php echo $type->slug ?>'><?php echo $type->name ?></div></td>
							<td id='bug-library-title'><a href='<?php echo get_permalink($post->ID); ?>'><?php echo stripslashes($post->post_title); ?></a></td>
						</tr>
						<tr id="<?php if ($counter % 2 == 1) echo 'odd'; else echo 'even'; ?>">
							<td id='bug-library-data' colspan='2'>ID: <a href='<?php echo get_permalink( $post->ID); ?>'><?php echo $post->ID; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status: <?php echo $status->name; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Version: <?php if ($productversion != '') echo $productversion; else echo 'N/A'; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Report Date: <?php echo date($dateformat, $postdatetimestamp); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Product: <?php echo $product->name; ?><br />
							<?php if ($resolutiondate != '') echo "Resolution Date: " . $resolutiondate . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Resolution Version: " . $resolutionversion; ?>
							</td>
						</tr>
						<tr id='bug-library-filler'><td></td></tr>
						<tr id='bug-library-desc'>
							<td colspan ='2'><div id='bug-library-desc-title'>Description</div><br />
							<?php if ($post->post_content != ''): ?>
							<?php the_content(); ?>
							<?php else: ?>
							No Description Available
							<?php endif; ?>
							
							<?php 	if ($imagepath != '') {
										echo "File Attachment<br /><br />";
										echo "<a href='" . $imagepath . "'>Attached File</a><br />";
									}
							?>
							</td>
						</tr>
					</table>
				</div>
					

				<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>
			</div>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
