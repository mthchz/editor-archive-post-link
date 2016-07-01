<?php
/*
Plugin Name: Editor archive link
Description: Adding post type archive link to TinyMCE link pop-up box
Version: 1.0
Author: Mathieu Cheze
*/

// No direct requests
if ( !defined('ABSPATH') ) die('-1');

/* Filter WP Link Query
*******************************************************************************/
function add_custom_post_type_archive_link($results, $query){
    if($query['offset'] > 0){ // Add only on the first result page
        return $results;
    }

    $match = '/'.str_remove_accents($query["s"]).'/i';

    foreach ($query['post_type'] as $post_type) {
        $pt_archive_link = get_post_type_archive_link( $post_type );
        $pt_obj = get_post_type_object( $post_type );

        if($pt_archive_link !== false && $pt_obj->has_archive !== false){ // Add only post type with 'has_archive'
            if (preg_match($match, str_remove_accents($pt_obj->labels->name)) > 0) {
                array_unshift($results, array(
                    'ID' => $pt_obj->has_archive,
                    'title' => trim( esc_html( strip_tags( $pt_obj->labels->name ) ) ),
                    'permalink' => $pt_archive_link,
                    'info' => 'Archive',
                ));
            }
        }
    }

    return $results;
}
add_filter('wp_link_query', 'add_custom_post_type_archive_link', 10, 2);

/* Util : remove accent
*******************************************************************************/
function str_remove_accents($str, $charset='utf-8') {
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    $str = preg_replace('#&[^;]+;#', '', $str);

    return $str;
}

?>