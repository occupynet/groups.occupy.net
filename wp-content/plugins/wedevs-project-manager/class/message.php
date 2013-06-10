<?php

/**
 * Description of message
 *
 * @author weDevs
 */
class CPM_Message {

    private static $_instance;

    public function __construct() {
        add_filter( 'init', array($this, 'register_post_type') );
    }

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new CPM_Message();
        }

        return self::$_instance;
    }

    function register_post_type() {
        register_post_type( 'message', array(
            'label' => __( 'Messages', 'cpm' ),
            'description' => __( 'message post type', 'cpm' ),
            'public' => false,
            'show_in_admin_bar' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_in_admin_bar' => false,
            'show_ui' => false,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => ''),
            'query_var' => true,
            'supports' => array('title', 'editor', 'comments'),
            'labels' => array(
                'name' => __( 'Messages', 'cpm' ),
                'singular_name' => __( 'Message', 'cpm' ),
                'menu_name' => __( 'Message', 'cpm' ),
                'add_new' => __( 'Add Message', 'cpm' ),
                'add_new_item' => __( 'Add New Message', 'cpm' ),
                'edit' => __( 'Edit', 'cpm' ),
                'edit_item' => __( 'Edit Message', 'cpm' ),
                'new_item' => __( 'New Message', 'cpm' ),
                'view' => __( 'View Message', 'cpm' ),
                'view_item' => __( 'View Message', 'cpm' ),
                'search_items' => __( 'Search Messages', 'cpm' ),
                'not_found' => __( 'No Messages Found', 'cpm' ),
                'not_found_in_trash' => __( 'No Messages Found in Trash', 'cpm' ),
                'parent' => __( 'Parent Message', 'cpm' ),
            ),
        ) );
    }

    function get_all( $project_id, $count = -1 ) {
        $messages = get_posts( array(
            'numberposts' => $count,
            'post_type' => 'message',
            'post_parent' => $project_id
        ));

        return $messages;
    }

    function get( $message_id ) {
        $message = get_post( $message_id );
        $message->milestone = get_post_meta( $message_id, '_milestone', true );
        $message->files = $this->get_attachments( $message_id );

        return $message;
    }

    function create( $project_id, $files = array(), $message_id = 0 ) {
        $post = $_POST;
        $is_update = $message_id ? true : false;

        $postarr = array(
            'post_parent' => $project_id,
            'post_title' => $post['message_title'],
            'post_content' => $post['message_detail'],
            'post_type' => 'message',
            'post_status' => 'publish'
        );

        if( $is_update ) {
            $postarr['ID'] = $message_id;
            $message_id = wp_update_post( $postarr );
        } else {
            $message_id = wp_insert_post( $postarr );
        }

        if ( $message_id ) {
            $milestone_id = (int) $post['milestone'];

            update_post_meta( $message_id, '_milestone', $milestone_id );

            //if there is any file, update the object reference
            if ( count( $files ) > 0 ) {
                update_post_meta( $message_id, '_files', $files );

                $this->associate_file( $files, $message_id, $project_id );
            }

            if ( $is_update ) {
                do_action( 'cpm_message_update', $message_id, $project_id, $postarr );
            } else {
                do_action( 'cpm_message_new', $message_id, $project_id, $postarr );
            }
        }

        return $message_id;
    }

    function update( $project_id, $files = array(), $message_id ) {
        return $this->create( $project_id, $files, $message_id );
    }

    function delete( $message_id, $force = false ) {
        do_action( 'cpm_message_delete', $message_id, $force );

        wp_delete_post( $message_id, $force );
    }

    function get_comments( $message_id, $sort = 'ASC' ) {
        $comments = CPM_Comment::getInstance()->get_comments( $message_id, $sort );

        return $comments;
    }

    function get_by_milestone( $milestone_id ) {
        $args = array(
            'post_type' => 'message',
            'meta_key' => '_milestone',
            'meta_value' => $milestone_id
        );

        $messages = get_posts( $args );

        return $messages;
    }

    /**
     * Get the attachments of a post
     *
     * Getting attachment for a message doesn't query to attachment as
     * post parent. But it's queried via a meta key `_parent`. This was done
     * because, every attachments parent_id in messages and comments are set
     * to as message ID. So that every attachments shows in media listing under
     * the message ID.
     *
     * @param int $post_id
     * @return array attachment list
     */
    function get_attachments( $post_id ) {
        $att_list = array();

        $args = array(
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_status' => null,
            'meta_name' => '_parent',
            'meta_value' => $post_id,
            'order' => 'ASC'
        );

        $attachments = get_posts( $args );

        foreach ($attachments as $attachment) {

            $att_list[$attachment->ID] = array(
                'id' => $attachment->ID,
                'name' => $attachment->post_title,
                'url' => wp_get_attachment_url( $attachment->ID ),
            );

            if ( wp_attachment_is_image( $attachment->ID ) ) {

                $thumb = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
                $att_list[$attachment->ID]['thumb'] = $thumb[0];
                $att_list[$attachment->ID]['type'] = 'image';
            } else {
                $att_list[$attachment->ID]['thumb'] = wp_mime_type_icon( $attachment->post_mime_type );
                $att_list[$attachment->ID]['type'] = 'file';
            }
        }

        return $att_list;
    }

    /**
     * Associate an attachment with a project
     *
     * Will be easier to find attachments by project
     *
     * @param array $files attachment file ID's
     * @param int $parent_id parent post id
     * @param int $project_id
     */
    function associate_file( $files, $parent_id, $project_id ) {

        foreach ($files as $file_id) {

            // add message id as the parent
            wp_update_post( array(
                'ID' => $file_id,
                'post_parent' => $parent_id
            ) );

            // set the _project meta in the file, so that we can find
            // attachments by project id
            update_post_meta( $file_id, '_project', $project_id );
            update_post_meta( $file_id, '_parent', $parent_id );
        }
    }

}
