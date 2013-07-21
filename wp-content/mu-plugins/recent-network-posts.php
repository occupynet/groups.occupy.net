<?php

/*
Plugin Name: Network Recent Posts
Plugin URI: http://occupy.net
Description: Retrieves a list of the most recent posts in a WordPress Multisite installation.
Author: Pea
Author URI: http://occupywallstreet.et
*/

/*
Parameter explanations
$how_many: how many recent posts are being displayed
$how_long: time frame to choose recent posts from (in days)
$titleOnly: true (only title of post is displayed) OR false (title of post and name of blog are displayed)
$begin_wrap: customise the start html code to adapt to different themes
$end_wrap: customise the end html code to adapt to different themes

Sample call: $recent_posts = recent_network_posts(20, 3)  get a total of 20 posts, get 3 posts per blog
*/


function recent_network_posts($numberposts = '', $postsperblog = '') { //Start Function

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

                // Get tags for each post and put into $all_tags array
                // $post_tags = get_tags();
                $post_tags = wp_get_post_tags($post->ID);
                $all_tags[$post->guid] = array();
                foreach ($post_tags as $post_tag) {
                    $all_tags[$post->guid][] = '<a href="' . $blog_url . 'tag/' . $post_tag->slug . '" title="' . $post_tag->name . '" class="' . $post_tag->slug . ' label success radius" rel="tag">' . $post_tag->name . '</a>';
                }

                // Get categories for each post and put into $all_categories array
                $post_categories = wp_get_post_categories($post->ID);
                $all_categories[$post->guid] = array();
                foreach ($post_categories as $post_category) {
                    $cat = get_category($post_category);
                    $all_categories[$post->guid][] = '<a href="' . $blog_url . 'category/' . $cat->slug . '" title="' . $cat->name . 'Category" class="' . $cat->slug . ' ' . $cat->name . '" rel="tag">' . $cat->name . '</a>';
                }
                $all_categories_slugs[$post->guid] = array();
                foreach ($post_categories as $post_category) {
                    $cat = get_category($post_category);
                    $all_categories_slugs[$post->guid][] = $cat->slug;
                }

            }

        // Back the current blog
        restore_current_blog();

        }

        // Sort by date
        @krsort($all_posts);

    }
        

    $i=0;
    foreach ($all_posts as $wp_post){
        // Number to retrieve set $numberposts
        if($i==$numberposts) break;

          $wp_post->post_url = $all_permalinks[$wp_post->guid];
          $wp_post->blog_id = $all_blogkeys[$wp_post->guid];
          $wp_post->post_thumbnail = $all_thumbnails[$wp_post->guid];
          $wp_post->post_categories = $all_categories[$wp_post->guid];
          $wp_post->post_category_slugs = $all_categories_slugs[$wp_post->guid];
          $wp_post->post_tags = $all_tags[$wp_post->guid];
          $blog_posts[$wp_post->guid] = $wp_post;

        $i++; 
    }



// return array($all_posts, $all_permalinks, $all_blogkeys);

return $blog_posts;

} // End Function

function recent_posts_excerpt($count = 55, $content, $permalink, $excerpt_trail = 'Read More'){
    /* Strip shortcodes
     * Due to an incompatibility issue between Visual Composer
     * and WordPress strip_shortcodes hook, I'm stripping
     * shortcodes using regex. (27-09-2012)
     *
     * $content = strip_tags(strip_shortcodes($content));
     *
     * replaced by
     *
     * $content = preg_replace("/\[(.*?)\]/i", '', $content);
     * $content = strip_tags($content);
     */
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