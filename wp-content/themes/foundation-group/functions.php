<?php
/*
Author: Pea
URL: htp://misfist.com

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images, 
sidebars, comments, ect.
*/

/************* REGISTER STYLES ********************/

if( !function_exists( 'custom_fonts_styles' ) ) {
    function custom_fonts_styles() { 
        wp_register_style( 'oswald', 'http://fonts.googleapis.com/css?family=Oswald', array(), '', 'all' );
        wp_enqueue_style( 'oswald' );
    }
    add_action('wp_enqueue_scripts', 'custom_fonts_styles', 25);
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
        'id' => 'footerbar',
        'name' => 'Footer Bar',
        'description' => 'Displays a full-width widget in the footer.',
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



/************* SHOW TEMPLATE NAME - FOR DEBUGGING ********************/

add_action('wp_head', 'show_template');

function show_template() {
    global $template;

    if ( is_user_logged_in() ) {
        echo ($template);
    }
}


?>
