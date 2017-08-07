<?php
/*
    Plugin Name: WordPress Exporter - Advanced Options
	Plugin URI: http://www.spingroup.com/
	Description: Adds an export profile for Wordpress Exporter (http://www.ground6.com/wordpress-plugins/wordpress-exporter/) which allows you to include AND exclue posts from one or more categories
	Author: Gary Wong
	Version: 0.0.1
	Author URI: http://www.spingroup.com
	License: Under GPL2
 
	Copyright 2017 spingroup.com (email: gary.wong@spingroup.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_filter( 'wp_exporter_queries', 'my_post_tag_export_query' );

function my_post_tag_export_query( $queries ) {
	$queries['my_post_tag'] = __( 'Posts w/ Advanced Categories Selection' );
	return $queries;
}

add_filter( 'wp_exporter_post_ids', 'my_exporter_post_ids', 1, 3 );

function my_exporter_post_ids( $post_ids, $query, $args ) {
    $cat_ids_inc =  $_GET['spin_post_category_inc'];
    $cat_ids_exc =  $_GET['spin_post_category_exc'];
    if (!count($cat_ids_inc) && !count($cat_ids_exc))
        return [];
	if ( 'my_post_tag' == $query ) {
        $q = new WP_Query( 
            array( 
                'post_type' => 'post',
                'category__in' => $cat_ids_inc,
                'category__not_in'  => $cat_ids_exc,
                'post_status' => array( 'publish', 'future' ),
                'nopaging' => true,
                'fields' => 'ids' 
                ) 
            );	
        $post_ids = $q->posts;
	}
	
	return $post_ids;
}

add_action( 'wp_exporter_form', 'wordpress_exporter_form_spin' ); // custom action

/**
 * Lists all posts/pages
 * @param $query (array) query argument
 * @since 0.0.1
 */
function wordpress_exporter_form_spin( $query ) {
	if ( 'my_post_tag' == $query ) {
		//wp_category_checklist();
        $cats_boxes = wp_terms_checklist( $post_id, array(
                'taxonomy' => 'category',
                'descendants_and_self' => 0,
                'selected_cats' => false,
                'popular_cats' => false,
                'walker' => null,
                'checked_ontop' => false,
                'echo' => false
        ) );
        $cats_boxes_inc = str_replace('post_category[]','spin_post_category_inc[]',$cats_boxes);
        $cats_boxes_exc = str_replace('post_category[]','spin_post_category_exc[]',$cats_boxes);

		echo "<label>". __( 'Select one or more categories to <strong>include</strong>:','wp-exporter' ) ."<br />".
		"<style>.spin_ul li { padding-left: 40px;{</style><ul class=\"spin_ul\">";
        echo $cats_boxes_inc;
        echo "</ul></label>";

		echo "<label>". __( 'Select one or more categories to <strong>exclude</strong>:','wp-exporter' ) ."<br />".
		"<style>.spin_ul li { padding-left: 40px;{</style><ul class=\"spin_ul\">";
        echo $cats_boxes_exc;
        echo "</ul></label>";

	}
}
