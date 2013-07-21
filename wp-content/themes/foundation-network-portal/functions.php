<?php
/*
Author: Pea
URL: htp://misfist.com

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images, 
sidebars, comments, ect.
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

require_once dirname( __FILE__ ) . '/lib/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'my_theme_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function my_theme_register_required_plugins() {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

        // This is an example of how to include a plugin pre-packaged with a theme
        array(
            // 'name'                  => 'TGM Example Plugin', // The plugin name
            // 'slug'                  => 'tgm-example-plugin', // The plugin slug (typically the folder name)
            // 'source'                => get_stylesheet_directory() . '/lib/plugins/tgm-example-plugin.zip', // The plugin source
            // 'required'              => true, // If false, the plugin is only 'recommended' instead of required
            // 'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            // 'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            // 'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            // 'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),

        array(
            'name'      => 'FeedWordPress',
            'slug'      => 'feedwordpress',
            'required'  => false,
        ),

        array(
            'name'      => 'Events Manager',
            'slug'      => 'events-manager',
            'required'  => false,
        ),

        array(
            'name'      => 'WordPress MU Sitewide Tags Pages',
            'slug'      => 'wordpress-mu-sitewide-tags',
            'required'  => false,
        ),

    );

    // Change this to your theme text domain, used for internationalising strings
    $theme_text_domain = 'foundation-network-portal';

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'domain'            => $theme_text_domain,          // Text domain - likely want to be the same as your theme.
        'default_path'      => '',                          // Default absolute path to pre-packaged plugins
        'parent_menu_slug'  => 'themes.php',                // Default parent menu slug
        'parent_url_slug'   => 'themes.php',                // Default parent URL slug
        'menu'              => 'install-required-plugins',  // Menu slug
        'has_notices'       => true,                        // Show admin notices or not
        'is_automatic'      => false,                       // Automatically activate plugins after installation or not
        'message'           => '',                          // Message to output right before the plugins table
        'strings'           => array(
            'page_title'                                => __( 'Install Required Plugins', $theme_text_domain ),
            'menu_title'                                => __( 'Install Plugins', $theme_text_domain ),
            'installing'                                => __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
            'oops'                                      => __( 'Something went wrong with the plugin API.', $theme_text_domain ),
            'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
            'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
            'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
            'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
            'return'                                    => __( 'Return to Required Plugins Installer', $theme_text_domain ),
            'plugin_activated'                          => __( 'Plugin activated successfully.', $theme_text_domain ),
            'complete'                                  => __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
            'nag_type'                                  => 'error' // Determines admin notice type - can only be 'updated' or 'error'
        )
    );

    tgmpa( $plugins, $config );

}

/************* REGISTER STYLES ********************/

if( !function_exists( 'include_custom_styles' ) ) {
    function include_custom_styles() { 
        wp_register_style( 'oswald', 'http://fonts.googleapis.com/css?family=Oswald', array(), '', 'all' );
        wp_enqueue_style( 'oswald' );
        wp_register_style( 'league-gothic', get_template_directory_uri() . '/fonts/league-gothic.css' , array(), '', 'all' );
        wp_enqueue_style( 'league-gothic' );
        wp_register_style( 'boxslider', get_stylesheet_directory_uri() . '/css/jquery.bxslider.css', array(), '', 'all' ); 
        wp_enqueue_style( 'boxslider' );    
    }
    add_action('wp_enqueue_scripts', 'include_custom_styles', 25);
}

if( !function_exists( 'include_custom_scripts' ) ) {
    function include_custom_scripts() { 
        wp_register_script('boxsliderscript', get_stylesheet_directory_uri() . '/js/jquery.bxslider/jquery.bxslider.js', true); 
        wp_enqueue_script('boxsliderscript');
        wp_register_script('isotopelibrary', get_stylesheet_directory_uri() . '/js/jquery.isotope.min.js', true); 
        wp_enqueue_script('isotopelibrary');
        // wp_register_script('isotopefilters', get_stylesheet_directory_uri() . '/js/isotope.filters.js', true); 
        // wp_enqueue_script('isotopefilters');
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

/************* OPTIONS FRAMEWORK STUFF ********************/
/* 
 * Helper function to return the theme option value. If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 * This code allows the theme to work without errors if the Options Framework plugin has been disabled.
 */

if ( !function_exists( 'of_get_option' ) ) {
function of_get_option($name, $default = false) {
    
    $optionsframework_settings = get_option('optionsframework');
    
    // Gets the unique option id
    $option_name = $optionsframework_settings['id'];
    
    if ( get_option($option_name) ) {
        $options = get_option($option_name);
    }
        
    if ( isset($options[$name]) ) {
        return $options[$name];
    } else {
        return $default;
    }
}
}

/************* SET FEATURED IMAGE ********************/
// If there is no featured image set, use the first image attached to post
function feature_image($size ='thumbnail') {

if ( has_post_thumbnail() ) {
    the_post_thumbnail( $size );
} else {
        $attachments = get_children( array(
            'post_parent' => get_the_ID(),
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'orderby' => 'menu_order ID',
            'numberposts' => 1)
        );
        foreach ( $attachments as $thumb_id => $attachment )
            return wp_get_attachment_image($thumb_id, $size);
        }
}

function feature_or_placeholder($size ='wpf-featured') {
    $feature_image = feature_image($size);
    $placeholder_image = of_get_option('placeholder_image');
    if($feature_image) {
        echo $feature_image;
    } else {
        echo $placeholder_image;
    }

} 

/************* SHOW SLIDER POSTS ********************/

function sliderposts($before_title='<h3 class="event-title">', $after_title='</h3>', $imagesize ='wpf-featured') {
    global $post;
    $placeholder_image = feature_or_placeholder();
    $numberposts = of_get_option('posts_in_orbit_slider');
    $args = array( 'posts_count' => $numberposts, 'post_type' => 'post' );
    $posts = get_posts( $args );

foreach( $posts as $post ) : setup_postdata($post); 
    echo '<li class="slider-item"><a href="';
    echo the_permalink();
    echo '">';
        if ( has_post_thumbnail() ) { 
            echo '<div class="event-image">';
            the_post_thumbnail('medium');
            echo '</div>';
        } else {
            echo '<div class="event-image"><img src="';
            feature_or_placeholder();
            echo '"></div>';
        }

    the_title( $before_title, $after_title, true);
    echo '</a>';
    echo '</li>';

endforeach;
}

function sliderevents($before_title='<h3 class="event-title">', $after_title='</h3>'){

        $placeholder_image = of_get_option('placeholder_image');
        $numberposts = of_get_option('posts_in_orbit_slider');

    //If Events Manager is active, display events coming up in slider
if (is_plugin_active( 'events-manager/events-manager.php' )) {
    echo EM_Events::output( 
        array('format_header' => '<ul class="bxslider">',
            'format_footer' => '</ul>',
            'limit'=> $numberposts,
            'orderby' => 'date',
            'format' =>
            '<li class="slider-item">
                <div class="event-image"><a href="#_EVENTURL">{no_image}<img src="' . $placeholder_image .'">{/no_image}{has_image}#_EVENTIMAGE{230,157}{/has_image}</a></div>'
                . $before_title . '<span class="event-date">#M #j:</span> #_EVENTLINK' . $after_title .
            '</li>'
            ) );
    } else {
        echo 'Events Manager needs to be active in order to display events';
    }

}

/************* GET LIST OF GROUPS ********************/

function get_group_ids() {

    global $wpdb;

    // Query all blogs from multi-site install
    $groups = $blogs = $wpdb->get_results("SELECT blog_id,domain,path FROM wp_blogs where blog_id > 1 ORDER BY path");

    // Initialize groupids array
    $groupids = array();

    // For each group, search for blog id

        foreach( $groups as $group ) {
            $groupids[] = $group->blog_id;
        }
        return $groupids;

}

function get_group_names() {
    $groupids = get_group_ids();

    foreach($groupids as $groupid) {
        $groupnames[] = get_blog_option( $groupid, 'blogname' );
    }
    return $groupnames;
}

function get_group_list() {
    $groupids = get_group_ids();

    echo '<ul class="group-list">';
    foreach($groupids as $groupid) {
        $groupname = get_blog_option( $groupid, 'blogname' );
        sort($groupids);
        echo '<li class="group-item" id="group-' . $groupid . '">';
        echo $groupname;
        echo '</li>';
    }
    echo '</ul>';
}

/************* GET HOME EXCERPT ********************/

function home_excerpt($chars = 55, $content, $permalink, $excerpt_trail) {
    $count = $chars;
    $content = preg_replace("/\[(.*?)\]/i", '', $content);
    $content = strip_tags($content);
    // Get the words
    $words = explode(' ', $content, $count + 1);
    // Pop everything
    array_pop($words);
    // Add trailing dots
    array_push($words, '...');
    // Add white spaces
    $content = implode(' ', $words);
    // Add the trail
    switch( $excerpt_trail ) {
        // Text
        case 'text':
            $content = $content.'<a href="'.$permalink.'">'.__('more','trans-nlp').'</a>';
            break;
        // Image
        case 'image':
            $content = $content.'<a href="'.$permalink.'"><img src="'.plugins_url('/img/excerpt_trail.png', __FILE__) .'" alt="'.__('more','trans-nlp').'" title="'.__('more','trans-nlp').'" /></a>';
            break;
        // Text by default
        default:
            $content = $content.'<a href="'.$permalink.'">'.__('more','trans-nlp').'</a>';
            break;
    }
    // Return the excerpt
    return $content;
}


//allow redirection, even if my theme starts to send output to the browser
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
} 

/************* GET RECENT POSTS FOR HOME ********************/

//Moved to mu-plugin

/************* SHOW TEMPLATE NAME - FOR DEBUGGING ********************/

add_action('wp_head', 'show_template');

function show_template() {
    global $template;

    if ( is_user_logged_in() ) {
        print_r($template);
    }
}

?>