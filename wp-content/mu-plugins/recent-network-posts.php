<?php

/*
Plugin Name: Network Recent Posts
Plugin URI: http://occupy.net
Description: Retrieves a list of the most recent posts in a WordPress Multisite installation.
Author: Pea
Author URI: http://occupywallstreet.et
*/

/*
This plug includes almost no markup. It simply returns an array of posts, organized by post_date (DESC), that you can use in your template file. Here is a sample of what is returned:

[http://web.net/?p=553] => WP_Post Object
(
    [ID] => 553
    [post_author] => 50
    [post_date] => 2013-06-28 18:36:27
    [post_date_gmt] => 2013-06-28 18:36:27
    [post_content] => 
    [post_title] => The Good, The Bad, & The Ugly
    [post_excerpt] => 
    [post_status] => publish
    [comment_status] => open
    [ping_status] => open
    [post_password] => 
    [post_name] => the-good-the-bad-the-ugly-week-of-6242013
    [to_ping] => 
    [pinged] => 
    [post_modified] => 2013-06-28 18:36:27
    [post_modified_gmt] => 2013-06-28 18:36:27
    [post_content_filtered] => 
    [post_parent] => 0
    [guid] => http://web.net/?p=553
    [menu_order] => 0
    [post_type] => post
    [post_mime_type] => 
    [comment_count] => 0
    [filter] => raw
    [post_url] => http://web.net/2013/06/28/the-good-the-bad-the-ugly-week-of-6242013/
    [blog_id] => 4
    [post_thumbnail] =>  
    [post_categories] => Array
        (
            [0] => Advocacy & Engagement
            [1] => Statements
        )

    [post_category_slugs] => Array
        (
            [0] => advocacy-engagement
            [1] => statements
        )

    [post_tags] => Array
        (
            [0] => financial literacy
        )

    [post_tag_slugs] => Array
        (
            [0] => financial-literacy
        )
)

Sample call: $recent_posts = recent_network_posts(20, 3)  get a total of 20 posts, get 3 posts per blog

LIST OF PARAMETERS
* @numberposts             : Specifies total number of posts to display
* @postsperblog            : Specifies number of posts per blog to display
* @postoffset              : Specifies number of posts to skip (useful if displaying results in different places)

*/


function recent_network_posts($numberposts = '', $postsperblog = '', $postoffset = 0) { //Start Function

    global $wpdb;

    $blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
        public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' 
        ORDER BY last_updated DESC");

    if( $blogs ) {
        // Count blogs found
        $count_blogs = count($blogs);
        // Dig into each blog

        foreach( $blogs as $blog_key ) {
            // Options: Site URL, Blog Name, Date Format
            ${'blog_url_'.$blog_key} = get_blog_option($blog_key,'siteurl');
            ${'blog_name_'.$blog_key} = get_blog_option($blog_key,'blogname');
            ${'date_format_'.$blog_key} = get_blog_option($blog_key,'date_format');

            // Switch to the blog
            switch_to_blog($blog_key);

            // Get posts set number of posts for each blog in $postsperblog
            ${'posts_'.$blog_key} = get_posts('numberposts='.$postsperblog);

            // Check if posts with the defined criteria were found
            if( empty(${'posts_'.$blog_key}) ) {
                /* If no posts matching the criteria were found then
                 * move to the next blog
                 */
                next($blogs);
            }

            // Put everything inside an array for sorting purposes
            foreach( ${'posts_'.$blog_key} as $post ) {

                // Access all post data
                setup_postdata($post);


                $all_posts[$post->post_date] = $post;

                // The guid is the only value which can differenciate a post from
                // others in the whole network
                $all_permalinks[$post->guid] = get_blog_permalink($blog_key, $post->ID);
                $all_blogkeys[$post->guid] = $blog_key;

                $blog_url = get_blog_details($blog_key)->path;

                if(has_post_thumbnail($post->ID)) {
                    $all_thumbnails[$post->guid] = get_the_post_thumbnail($post->ID);
                } else {
                    $all_thumbnails[$post->guid] = ' ';   
                }

                $post_tags = wp_get_post_tags($post->ID);
                $all_tags[$post->guid] = array();
                foreach ($post_tags as $post_tag) {
                    $tag_link = get_term_link($post_tag->name, 'post_tag');
                    $all_tags[$post->guid][$post_tag->slug]['slug'] = $post_tag->slug;
                    $all_tags[$post->guid][$post_tag->slug]['name'] = $post_tag->name;
                    $all_tags[$post->guid][$post_tag->slug]['url'] = $tag_link;
                    $all_tags[$post->guid][$post_tag->slug]['nice_link'] = '<a href="' . $tag_link . '" title="' . $post_tag->name . '" class="' . $post_tag->slug . ' label success radius" rel="tag">' . $post_tag->name . '</a>';
                }

                // Get categories for each post and put into $all_categories array
                $post_categories = wp_get_post_categories($post->ID);
                $all_categories[$post->guid] = array();
                foreach ($post_categories as $post_category) {
                    $cat = get_category($post_category);
                    $cat_id = get_cat_ID($cat->name);
                    $cat_link = get_category_link($cat_id);
                    $all_categories[$post->guid][$cat->slug]['slug'] = $cat->slug;
                    $all_categories[$post->guid][$cat->slug]['name'] = $cat->name;
                    $all_categories[$post->guid][$cat->slug]['id'] = $cat_id;
                    $all_categories[$post->guid][$cat->slug]['url'] = $cat_link;
                    $all_categories[$post->guid][$cat->slug]['nice_link'] = '<a href="' . $cat_link . '" title="' . $cat->name . 'Category" class="' . $cat->slug . ' ' . $cat->name . '" rel="tag">' . $cat->name . '</a>';
                }

            }

        // Back the current blog
        restore_current_blog();

        }

        // Sort by date
        @krsort($all_posts);

    }
        
    $post_count = count($all_posts);

    if($post_count < $postoffset) {
        $o = 0;
    } else {
        $o = $postoffset; // Number to skip; set in $postoffset 
    }

    if($post_count < $numberposts + $o) {
        $limit = $post_count - $o;
    } else {
        $limit = $numberposts;
    }
    
    // Number to retrieve; set $numberposts
    $all_posts =  new ArrayIterator($all_posts);

    foreach (new LimitIterator($all_posts, $postoffset, $limit) as $wp_post){

          $wp_post->post_url = $all_permalinks[$wp_post->guid];
          $wp_post->blog_id = $all_blogkeys[$wp_post->guid];
          $wp_post->post_thumbnail = $all_thumbnails[$wp_post->guid];
          $wp_post->post_categories = $all_categories[$wp_post->guid];
          $wp_post->post_tags = $all_tags[$wp_post->guid];
          // $wp_post->post_tag_links = $all_tags_links[$wp_post->guid];
          // $wp_post->post_tag_slugs = $all_tags_slugs[$wp_post->guid];
          $blog_posts[$wp_post->guid] = $wp_post;

    }
// Debuggin...
// echo '<pre>';print_r($blog_posts);echo '</pre>';
return $blog_posts;

} // End Function

// Accepted arguments: $count (default 5), $content, $permalink, $excerpt_trail (default 'Read More')
function recent_posts_excerpt($count = 55, $content, $permalink, $excerpt_trail = 'Read More'){
    $content = preg_replace("/\[(.*?)\]/i", '', $content);
    $content = strip_tags($content);
    // Get the words
    $words = explode(' ', $content, $count + 1);
    // Pop everything
    array_pop($words);
    // Add trailing dots
    // array_push($words, '...');
    // Add white spaces
    $content = implode(' ', $words);
    // Add the trail
    $content = $content.'<a href="'.$permalink.'" target="_blank" class="read-more">'.$excerpt_trail.'</a>';
    // Return the excerpt
    return $content;
}


?>