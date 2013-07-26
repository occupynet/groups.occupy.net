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
            [advocacy-engagement] => Array
                (
                    [slug] => advocacy-engagement
                    [name] => Advocacy &amp; Engagement
                    [id] => 5
                    [url] => http://web.net/web/occuevolve/blog/category/advocacy-engagement/
                    [nice_link] => <a href="http://web.net/web/occuevolve/blog/category/advocacy-engagement/" title="Advocacy &amp; Engagement" class="category-advocacy-engagement" rel="tag">Advocacy &amp; Engagement</a>
                )
            [statements] => Array
                (
                    [slug] => statements
                    [name] => Statements
                    [id] => 59
                    [url] => http://web.net/web/occuevolve/blog/category/statements/
                    [nice_link] => <a href="http://web.net/web/occuevolve/blog/category/statements/" title="Statements" class="category-statements" rel="tag">Statements</a>
                )
        )
    [post_tags] => Array
        (
            [financial-literacy] => Array
                (
                    [slug] => financial-literacy
                    [name] => financial literacy
                    [url] => http://web.net/web/occuevolve/blog/tag/financial-literacy/
                    [nice_link] => <a href="http://web.occupy.net/web/occuevolve/blog/tag/financial-literacy/" title="financial literacy" class="tag-financial-literacy label success radius" rel="tag">financial literacy</a>
                )
        )
    [post_format] => aside 
)

Note: If the post format is set to standard, no value will be returned.

Sample call: $recent_posts = recent_network_posts(20, 3)  get a total of 20 posts, get 3 posts per blog

LIST OF PARAMETERS
* @numberposts             : Specifies total number of posts to display
* @postsperblog            : Specifies number of posts per blog to display
* @postoffset              : Specifies number of posts to skip (useful if displaying results in different places)

*/


function recent_network_posts($numberposts = 25, $postsperblog = 3, $postoffset = 0, $imagesize ='thumbnail') { //Start Function

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
                    $all_thumbnails[$post->guid] = get_the_post_thumbnail($post->ID, $imagesize);
                } 
                else {
                    $all_thumbnails[$post->guid] = '';   
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
                    $all_categories[$post->guid][$cat->slug]['nice_link'] = '<a href="' . $cat_link . '" title="' . $cat->name . '" class="category-' . $cat->slug . '" rel="tag">' . $cat->name . '</a>';
                }

                // Get tags for each post and put into $all_tags array
                $post_tags = wp_get_post_tags($post->ID);
                // $post_tags = get_the_tags($post->ID);
                $all_tags[$post->guid] = array();
                foreach ($post_tags as $post_tag) {
                    $tag_id = $post_tag->term_id;
                    $tag_link = get_tag_link($tag_id);
                    $all_tags[$post->guid][$post_tag->slug]['slug'] = $post_tag->slug;
                    $all_tags[$post->guid][$post_tag->slug]['name'] = $post_tag->name;
                    $all_tags[$post->guid][$post_tag->slug]['url'] = $tag_link;
                    $all_tags[$post->guid][$post_tag->slug]['nice_link'] = '<a href="' . $tag_link . '" title="' . $post_tag->name . '" class="tag-' . $post_tag->slug . ' label success radius" rel="tag">' . $post_tag->name . '</a>';
                }

                // $post_format = get_post_format($post->ID);
                $all_post_formats[$post->guid] = get_post_format($post->ID);

            }

        // Back the current blog
        restore_current_blog();

        }

        // Sort by date
        @krsort($all_posts);

    }
        
    // Count the number of posts
    $post_count = count($all_posts);

    // If the number of posts is less then the post offset, make offset = 0
    if($post_count < $postoffset) {
        $o = 0;
    } else {
        $o = $postoffset; // Number to skip; set in $postoffset 
    }

    // If the number of posts is less then the number of posts selected to display, change the limit to total number minus offset
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
          $wp_post->post_format = $all_post_formats[$wp_post->guid];
          $blog_posts[$wp_post->guid] = $wp_post;

    }
// Debuggin...
// echo '<pre>';print_r($blog_posts);echo '</pre>';
return $blog_posts;

} // End Function

// Function to return filters, based on the posts displayed
// $numberposts should be set to the number of posts set to display on the page
function recent_posts_filters($filter_type = 'category', $numberposts = 25) {

    if(function_exists('recent_network_posts')) { // If the plugin is active, display network-wide posts

        // Filter options:
        // category = 'category-'
        // tag = 'tag-'
        // format = 'format-'
        // author = 'author-'
        // blog = 'blog-'

        $recent_posts = recent_network_posts();

        // Get an array of categories
        $cats = array();
        $cat_slugs = array();
        $cat_names = array();

        // Get an array of categories
        $tags = array();
        $tags_slugs = array();
        $tags_names = array();
        $blogs = array();
        foreach($recent_posts as $recent_post => $WP_Post) {
            $blog_details = get_blog_details($WP_Post->blog_id);
            
            // Get array of categories
            foreach ($WP_Post->post_categories as $key => $value) {
                foreach ($value as $k => $v) {
                    $cats[$value['slug']] = $value['name'];
                }
            }

            // Get array of tags
            foreach ($WP_Post->post_tags as $key => $value) {
                foreach ($value as $k => $v) {
                    $tags[$value['slug']] = $value['name'];
                }
            }

            // Get array of post formats
            if($WP_Post->post_format) {
                $formats[$WP_Post->post_format] = $WP_Post->post_format;
            } else {
                $formats['standard'] = 'standard'; // Posts set to standard return no value
            }

            // Get array of blogs
            $blogs[$blog_details->blogname] = $WP_Post->blog_id;

            // Get array of authors
            $author = get_userdata($WP_Post->post_author);
            // Hide if author is admin
            if($author->display_name != 'admin') {
                $authors[$author->display_name] = $WP_Post->post_author;
            }
        }

        if($filter_type == 'category') {
            // usort($cats, "sort_by_keyvalue");
            ksort($cats);
            foreach ($cats as $key => $value) {
                if($key != 'uncategorized') {
                echo '<li><a href="#" data-option-value="' . $filter_type . '-' . $key . '">' . $value . '</a></li>';
                }
            }
            // echo '<pre>';print_r($cats);echo '</pre>';

        } elseif ($filter_type == 'tag') {
            // usort($tags, "sort_by_keyvalue");
            ksort($tags);
            foreach ($tags as $key => $value) {
                echo '<li><a href="#" data-option-value="' . $filter_type . '-' . $key. '">' . $value . '</a></li>';
            }
            // echo '<pre>';print_r($tags);echo '</pre>';
            
        } elseif ($filter_type == 'format') {
            ksort($formats);
            foreach ($formats as $key => $value) {
                echo '<li><a href="#" data-option-value="' . $filter_type . '-' . $value . '">' . ucfirst($value) . '</a></li>';
            }
            // echo '<pre>';print_r($formats);echo '</pre>';
            
        } elseif ($filter_type == 'blog') {
            ksort($blogs);
            foreach ($blogs as $key => $value) {
                echo '<li><a href="#" data-option-value="' . $filter_type . '-' . $value . '">' . $key . '</a></li>';
            }
            // echo '<pre>';print_r($blogs);echo '</pre>';
            
        } elseif ($filter_type == 'author') {
            ksort($authors);
            foreach ($authors as $key => $value) {
                echo '<li><a href="#" data-option-value="' . $filter_type . '-' . $value . '">' . $key . '</a></li>';
            }
            // echo '<pre>';print_r($authors);echo '</pre>';

        } else {
            echo 'A filter type must be selected. Select categories, tags, formats, blogs or authors.';
        }


    } else {
        echo 'the function recent_network_posts() must be active to use this function (Located in recent-network-posts.php).';
    }

} // End function

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