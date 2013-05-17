<?php
/*
Author: Pea
URL: htp://misfist.com

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images, 
sidebars, comments, ect.
*/

/************* REGISTER STYLES ********************/

if( !function_exists( 'include_custom_styles' ) ) {
    function include_custom_styles() { 
        wp_register_style( 'oswald', 'http://fonts.googleapis.com/css?family=Oswald', array(), '', 'all' );
        wp_enqueue_style( 'oswald' );
        wp_register_style( 'league-gothic', get_template_directory_uri() . '/fonts/league-gothic.css' , array(), '', 'all' );
        wp_enqueue_style( 'league-gothic' );
        wp_register_style( 'boxslider', get_stylesheet_directory_uri() . '/css/jquery.bxslider.css', array(), '', 'all' ); 
        // wp_register_style( 'boxslider', 'http://localhost/groups.occupy.net/web/wp-content/themes/foundation-network-portal/css/jquery.bxslider.css', array(), '', 'all' );
        wp_enqueue_style( 'boxslider' );    
    }
    add_action('wp_enqueue_scripts', 'include_custom_styles', 25);
}

if( !function_exists( 'include_custom_scripts' ) ) {
    function include_custom_scripts() { 
        // wp_register_script('boxsliderscript', 'http://localhost/groups.occupy.net/web/wp-content/themes/foundation-network-portal/js/jquery.bxslider/jquery.bxslider.js', true);  
         wp_register_script('boxsliderscript', get_stylesheet_directory_uri() . '/js/jquery.bxslider/jquery.bxslider.js', true); 
        wp_enqueue_script('boxsliderscript');
    }
    add_action('wp_enqueue_scripts', 'include_custom_scripts', 25);
}

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas


    register_sidebar(array(
        'id' => 'actionpanel',
        'name' => 'Action Panel',
        'description' => 'Displays an action panel.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'footerleft',
        'name' => 'Left Footer',
        'description' => 'Displays a widget in the left footer.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));
    register_sidebar(array(
        'id' => 'footercenter',
        'name' => 'Center Footer',
        'description' => 'Displays a widget in the center footer.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));
    register_sidebar(array(
        'id' => 'footerright',
        'name' => 'Right Footer',
        'description' => 'Displays a widget in the right footer.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

    
    /* 
    to add more sidebars or widgetized areas, just copy
    and edit the above sidebar code. In order to call 
    your new sidebar just use the following code:
    
    Just change the name to whatever your new
    sidebar's id is, for example:
    
    
    
    To call the sidebar in your template, you can just copy
    the sidebar.php file and rename it to your sidebar's name.
    So using the above example, it would be:
    sidebar-sidebar2.php
    */

    // Custom excerpt length

    function custom_excerpt_length( $length = "40" ) {
        return $length;
    }
    add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

    function new_excerpt_more( $more ) {
        return ' <a class="read-more" href="'. get_permalink( get_the_ID() ) . '">Read More &raquo;</a>';
    }
    add_filter( 'excerpt_more', 'new_excerpt_more' );

/*
*******************************************
* Register Custom Post Types and Fields
*******************************************
*/

// Initialize custom post type and taxonomy registration
add_action( 'init', 'register_organizations_post_type' );

function register_organizations_post_type() {

     // Register Resources Custom Post type
    register_post_type('organizations', 
        array(   
            'label' => 'Organizations',
            'description' => '',
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array(
                'slug' => 'directory'),
            'query_var' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'supports' => array(
                'title',
                'editor','excerpt',
                'custom-fields','comments',
                'revisions','thumbnail',
                'author','page-attributes',),
            'taxonomies' => array(
                'organization_categories',
                'post_tag',),
            'labels' => array(
                'name' => 'Organizations',
                'singular_name' => 'Organization',
                'menu_name' => 'Directory',
                'add_new' => 'Add Organization',
                'add_new_item' => 'Add New Organization',
                'edit' => 'Edit',
                'edit_item' => 'Edit Organization',
                'new_item' => 'New Organization',
                'view' => 'View Organization',
                'view_item' => 'View Organization',
                'search_items' => 'Search Organizations',
                'not_found' => 'No Organizations Found',
                'not_found_in_trash' => 'No Organizations Found in Trash',
                'parent' => 'Parent Organization',
            ),
        ) 
    );

    register_taxonomy('organization_categories',
        array(
            0 => 'organizations',
            ),
        array( 'hierarchical' => true, 
            'label' => 'Organization Categories',
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'organizations'
                ),
            'singular_label' => 'Organization Category'
            ) 
        );
}

/************* REGISTER CUSTOM METABOXES ********************/

if(function_exists("register_field_group"))
{
    register_field_group(array (
        'id' => 'organization-fields',
        'title' => 'Organization Fields',
        'fields' => array (
            array (
                'key' => 'organization_address',
                'label' => 'Address',
                'name' => 'organization_address',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_city',
                'label' => 'City',
                'name' => 'organization_city',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_state',
                'label' => 'State',
                'name' => 'organization_state',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_zip',
                'label' => 'Zip',
                'name' => 'organization_zip',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_email',
                'label' => 'Email',
                'name' => 'organization_email',
                'type' => 'email',
                'default_value' => '',
            ),
            array (
                'key' => 'organization_phone',
                'label' => 'Phone',
                'name' => 'organization_phone',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_website',
                'label' => 'Website',
                'name' => 'organization_website',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_facebook',
                'label' => 'Facebook',
                'name' => 'organization_facebook',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_twitter',
                'label' => 'Twitter',
                'name' => 'organization_twitter',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_googleplus',
                'label' => 'Google+',
                'name' => 'organization_googleplus',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_linkedin',
                'label' => 'LinkedIn',
                'name' => 'organization_linkedin',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_contact_name',
                'label' => 'Contact Name',
                'name' => 'organization_contact_name',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
            array (
                'key' => 'organization_contact_email',
                'label' => 'Contact Email',
                'name' => 'organization_contact_email',
                'type' => 'email',
                'default_value' => '',
            ),
            array (
                'key' => 'organization_contact_phone',
                'label' => 'Contact Phone',
                'name' => 'organization_contact_phone',
                'type' => 'text',
                'default_value' => '',
                'formatting' => 'html',
            ),
        ),
        'location' => array (
            'rules' => array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'organizations',
                    'order_no' => 0,
                ),
            ),
            'allorany' => 'all',
        ),
        'options' => array (
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array (
                0 => 'send-trackbacks',
            ),
        ),
        'menu_order' => 0,
    ));
}

/************* SHOW TEMPLATE NAME - FOR DEBUGGING ********************/

add_action('wp_head', 'show_template');

function show_template() {
    global $template;

    if ( is_user_logged_in() ) {
        print_r($template);
    }
}

/************* SHOW ALL SITES********************/

if(!function_exists('get_all_sites')){
  /**
   * Retrieves all multisite blogs
   *
   * @return array Blog IDs as keys and blog names as values.
   */
  function get_all_sites() {

    global $wpdb;
    $multisite = array();
    // Query all blogs from multi-site install
    $blogs = $wpdb->get_results("SELECT blog_id,domain,path FROM wp_blogs ORDER BY path");

    // Get primary blog
    $blogname = $wpdb->get_row("SELECT option_value FROM wp_options WHERE option_name='blogname' ");
    $multisite[1] = $blogname->option_value;

    // For each blog search for blog name in respective options table
    foreach( $blogs as $blog ) {
      // Get rest of the sites
      $blogname = $wpdb->get_results("SELECT option_value FROM wp_".$blog->blog_id ."_options WHERE option_name='blogname' ");
      foreach( $blogname as $name ) {
        $multisite[$blog->blog_id] = $name->option_value;
      }
    }
    return $multisite;
  }
}
 
 /************* SHOW ALL SITES********************/
// Output a single menu item
function bloglist_entry($id, $title, $link_self) {
    global $blog_id;
        if ($link_self || $id != $blog_id) {
            echo '<li>';
            if ($id == $blog_id) {
            echo '<strong>';
            }
            $url = get_home_url($id);
                if (substr($url, -1) != '/') {
                // Note: I added a "/" to the end of the URL because WordPress
                // wasn't doing that automatically in v3.0.4. YMMV.
                $url .= '/';
                }
                echo '<a href="' . $url . '">' . $title . '</a>';
                    if ($id == $blog_id) {
                    echo '</strong>';
            }
        echo '</li>';
    }
}
 
// Output the whole menu
// If $link_self is false, skip the current site - used to display the menu on the homepage
function bloglist($link_self = true) {
    global $wpdb;

    echo '<h4 class="widgettitle">Active Groups</h4>';
    echo '<ul class="blog-list">';
     
    bloglist_entry(1, 'Home', $link_self);
     
    $blogs = $wpdb->get_results("
    SELECT blog_id
        FROM {$wpdb->blogs}
        WHERE site_id = '{$wpdb->siteid}'
        AND spam = '0'
        AND deleted = '0'
        AND archived = '0'
        AND blog_id != 1
    ");
     
    $sites = array();
    foreach ($blogs as $blog) {
        $sites[$blog->blog_id] = get_blog_option($blog->blog_id, 'blogname');
    }
     
    natsort($sites);
    foreach ($sites as $blog_id => $blog_title) {
        bloglist_entry($blog_id, $blog_title, $link_self);
    }
    echo '</ul>';
}
 
// Adds a [bloglist] shortcode, so I can embed the menu into the static homepage.
// Note: I originally put it directly into the template, but that didn't work
// with WPtouch.
function bloglist_shortcode($atts)
{
    bloglist(false);
}
 
add_shortcode('bloglist', 'bloglist_shortcode');


//allow redirection, even if my theme starts to send output to the browser
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}    

?>