<?php
/*
Plugin Name: Post Type Newspages
Plugin URI:
Description: Plugin allows the user to create a custom list of news in the admin side, the ability to sort a list of the fields, delete, edit the news. Add new fields: admin comment and alternative title. Plugin added new content type with widget and shortcode for this content type.
Author: Alex Dyakonov
Version: 1.0.1
*/

//Hook for load language
add_action( 'plugins_loaded', 'newspages_load_lang' );

/**
 * function to load languages
 */
function newspages_load_lang() {
    load_plugin_textdomain( "post-type-newspages", false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

//Register new type newspages
add_action( 'init', 'create_newspages_type' );

/**
 *  Create custom post_type
 */
function create_newspages_type() {
    register_post_type( 'newspages',
        array(
            'labels'      => array(
                'name'          => __( 'NewsPages', 'post-type-newspages' ),
                'singular_name' => __( 'NewsPage', 'post-type-newspages' )
            ),
            'public'      => true,
            'has_archive' => true,
            'supports'    => array( 'title', 'editor', 'thumbnail' )
        )
    );
}

//Add Newspages categories
add_action( 'init', 'newspages_taxonomies', 0 );

/**
 * Create taxonomies
 */
function newspages_taxonomies() {
    register_taxonomy( 'newspages_category', 'newspages',
        array(
            'hierarchical' => true,
            'label'        => __( 'Categories', 'post-type-newspages' ),
        )
    );
}

//Add meta boxes
add_action( 'add_meta_boxes', 'newspages_meta_box' );

/**
 * Add meta boxes
 */
function newspages_meta_box() {
    add_meta_box(
        'newspages_info_box',
        __( 'NewsPage Information', 'post-type-newspages' ),
        'newspages_info_box',
        'newspages',
        'side'
    );
}

/**
 * print box content
 */
function newspages_info_box( $post ) {
    $comment           = get_post_meta( $post->ID, 'admin_comment', true );
    $alternative_title = get_post_meta( $post->ID, 'alternative_title', true );

    wp_nonce_field( plugin_basename( __FILE__ ), 'newspages_noncename' );

    include('view-metaboxes.phtml');
}

//save post data
add_action( 'save_post', 'newspages_save_post_data' );

/**
 * Save meta boxes data
 *
 * @param $post_id
 */
function newspages_save_post_data( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['newspages_noncename'], plugin_basename( __FILE__ ) ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['admin_comment'] ) ) {
        update_post_meta( $post_id, 'admin_comment', esc_attr( $_POST['admin_comment'] ) );
    }
    if ( isset( $_POST['alternative_title'] ) ) {
        update_post_meta( $post_id, 'alternative_title', esc_attr( $_POST['alternative_title'] ) );
    }
}

/**
 *
 * Create shortcode
 *
 * @param $atts
 *
 * @return string
 */
function newspages_show( $atts ) {
    extract( shortcode_atts( array( 'category' => '', 'newspages_show' => 3 ), $atts ) );

    $args_post = array(
        'post_type'          => 'newspages',
        'newspages_category' => $category,
        'orderby'            => 'date',
        'order'              => 'DESC',
        'showposts'          => $newspages_show,
    );

    $posts = new WP_Query( $args_post );

    if ( $posts->have_posts() ) {
        while ($posts->have_posts()) {
            $posts->the_post();
            $comment = get_post_meta(get_the_ID(), 'admin_comment', true);
            $alternative_title = get_post_meta(get_the_ID(), 'alternative_title', true);

            include('view-shortcode.phtml');
        }
    }
}

add_shortcode( 'newspages', 'newspages_show' );

//include widget
include_once( 'widget-newspages.php' );