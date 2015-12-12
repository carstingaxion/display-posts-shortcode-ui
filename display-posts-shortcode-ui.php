<?php # -*- coding: utf-8 -*-
defined( 'ABSPATH' ) OR exit;

/**
 * Plugin Name: 	Display Posts Shortcode UI
 * Plugin URI: 		https://github.com/carstingaxion/display-posts-shortcode-ui
 * Version: 		1.0.0
 * Description: 	Adds a Shortcake powered UI to the [display-posts] shortcode.
 * Author: 			Carsten Bach
 * Author URI: 		http://www.carsten-bach.de
 * Text Domain: 	display-posts-shortcode-ui
 * License: 		GPL v3
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */


if ( !is_admin() ) {
	return;
}


/*
 * This plugin handles the registration of one shortcode, and the related Shortcode UI:
 *  a. [display-posts-ui] - a wrapper shortcode for [display-posts]
 *
 * The plugin is broken down into four stages:
 *  0. Check to see if Shortcake is running, with an admin notice if not.
 *  X. Register the shortcode - this is standard WP behaviour, nothing new here.
 *  1. Register the Shortcode UI setup for the shortcode.
 *  2. Hook onto first and last [display-posts]-output-filter to add some output buffering and make shortcode-content visible to TinyMCE.
 */



/*
 * 0. Check to see if Shortcake is running, with an admin notice if not.
 */

add_action( 'init', 'dpsui_detection' );
/**
 * If Shortcake isn't active, then add an administration notice.
 *
 * This check is optional. The addition of the shortcode UI is via an action hook that is only called in Shortcake.
 * So if Shortcake isn't active, you won't be presented with errors.
 *
 * Here, we choose to tell users that Shortcake isn't active, but equally you could let it be silent.
 *
 * Why not just self-deactivate this plugin? Because then the shortcodes would not be registered either.
 *
 * @since 1.0.0
 */
function dpsui_detection() {
	if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		add_action( 'admin_notices', 'dpsui_notice_dependency_missing_shortcode_ui_plugin' );
	}
	if ( ! function_exists( 'be_display_posts_shortcode' ) ) {
		add_action( 'admin_notices', 'dpsui_notice_dependency_missing_display_posts_plugin' );
	}
	if ( function_exists( 'shortcode_ui_register_for_shortcode' )
		 &&
		 function_exists( 'be_display_posts_shortcode' )
		) {

		/*
		 * 1. Register the Shortcode UI setup for the shortcode.
		 */
		add_action( 'register_shortcode_ui', 'dpsui_shortcode_ui' );
		//add_action( 'init', 'dpsui_shortcode_ui' );

		/*
		 * 2. Hook onto first and last [display-posts]-output-filter
		 */
		// first filter applied inside the Plugin
		add_filter('display_posts_shortcode_args', 'dpsui_shortcode_ob_start', 1, 2);
		// last filter
		add_filter('display_posts_shortcode_wrapper_close', 'dpsui_shortcode_ob_get_clean', 99999, 2);

		/*
		 * 3. Load Scripts & Styles
		 *    Fires at the conclusion of wp_enqueue_media().
		 */
		add_action( 'wp_enqueue_media', 'dpsui_assets' );
	}
}



/**
 * Display an administration notice if the user can activate plugins.
 *
 * Testing for both plugins
 *
 * @since 1.0.0
 */
function dpsui_notice_dependency_missing_shortcode_ui_plugin() {
	if ( current_user_can( 'activate_plugins' ) ) {
		?>
		<div class="error message">
			<p><?php esc_html_e( '"Shortcode UI" plugin must be active for "Display Posts Shortcode UI" plugin to function.', 'display-posts-shortcode-ui' ); ?></p>
		</div>
		<?php
	}
}
function dpsui_notice_dependency_missing_display_posts_plugin() {
	if ( current_user_can( 'activate_plugins' ) ) {
		?>
		<div class="error message">
			<p><?php esc_html_e( '"Display Posts Shortcode" plugin must be active for "Display Posts Shortcode UI" plugin to function.', 'display-posts-shortcode-ui' ); ?></p>
		</div>
		<?php
	}
}



/*
 * 1. Register the shortcodes.


add_action( 'init', 'dpsui_register_shortcode' ); */

/* *
 * Register two shortcodes, shortcake_dev and shortcake-no-attributes.
 *
 * This registration is done independently of any UI that might be associated with them, so it always happens, even if
 * Shortcake is not active.
 *
 * @since 1.0.0

function dpsui_register_shortcode() {
	// 
//	add_shortcode( 'display-posts-ui', 'dpsui_shortcode_handler' );
}
 */



/**
 * Shortcode UI setup for the shortcake-no-attributes shortcode.
 *
 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
 *
 * This example shortcode has no attributes and minimal UI.
 *
 * @since 1.0.0
 */
function dpsui_shortcode_ui() {

	global $_wp_additional_image_sizes;

	// prepare [author]
	$args = array(
		'fields' => array('ID','display_name'),
	);
	$user_select = wp_list_pluck( get_users( $args ), 'display_name', 'ID' );
	// Add empty default option
	array_unshift($user_select, "");

	// prepare [date_compare]
	// (cloned)
	$date_compare_ops = array( '=', '!=', '>', '>=', '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );

	// prepare [date_column]
	// (cloned)
	$date_columns = array(
		'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt',
		'comment_date', 'comment_date_gmt'
	);

	// prepare registered images-sizes
	$image_sizes = array( '' => __('None','display-posts-shortcode-ui') );
	foreach ($_wp_additional_image_sizes as $img_name => $img_values) {
		$image_sizes[$img_name] = $img_values['width'] . ' x ' . $img_values['height'] . ' px  -  ' . $img_name;
	}

	// prepare list of taxonomies
	// query for public taxonomy objects
	$args = array(
		'public'   => true,
	); 
	$output = 'objects'; // or names
	$taxonomy_names = array();
	foreach ( get_taxonomies($args, $output ) as $key => $value) {
		$taxonomy_names[$key] = $value->labels->name;
	}

	// prepare list of post_types
	// query for public post_type objects
	$args = array(
		'public'   => true,
	); 
	$output = 'objects'; // or names
	$post_type_names = array();
	foreach ( get_post_types($args, $output ) as $key => $value) {
		$post_type_names[$key] = $value->labels->name;
	}

	// prepare list of post_tags
	// query for public post_tag objects
	$post_tags = array();
	foreach ( get_tags() as $key => $value) {
		$post_tags[$value->slug] = $value->name;
	}
	// Add empty default option
	array_unshift($post_tags, "");

	// prepare list of post stati
	// query for public post_status objects
	$args = array(
//		'public'   => true,
	); 
	$output = 'objects'; // or names
	$post_status_names = array();
	foreach ( get_post_stati($args, $output ) as $key => $value) {
		$post_status_names[$key] = $value->label;
	}
#fb(get_post_stati($args, $output ));


	/*
	 * Define the UI for attributes of the shortcode. Optional.
	 *
	 * In this demo example, we register multiple fields related to showing a quotation
	 * - Attachment, Citation Source, Select Page, Background Color, Alignment and Year.
	 *
	 * If no UI is registered for an attribute, then the attribute will
	 * not be editable through Shortcake's UI. However, the value of any
	 * unregistered attributes will be preserved when editing.
	 *
	 * Each array must include 'attr', 'type', and 'label'.
	 * * 'attr' should be the name of the attribute.
	 * * 'type' options include: text, checkbox, textarea, radio, select, email,
	 *     url, number, and date, post_select, attachment, color.
	 * * 'label' is the label text associated with that input field.
	 *
	 * Use 'meta' to add arbitrary attributes to the HTML of the field.
	 *
	 * Use 'encode' to encode attribute data. Requires customization in shortcode callback to decode.
	 *
	 * Depending on 'type', additional arguments may be available.
	 */
	$fields = array(

		// title - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Give the list a title heading', 'display-posts-shortcode-ui' ),
			'description' => '[title] ' . esc_html__( 'Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'title',
			'type'        => 'text',
		),
		// author - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Specify the post author by the user_name', 'display-posts-shortcode-ui' ),
			'description' => '[author] ' . esc_html__( 'Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'author',
			//'type'        => 'text',
			'type'        => 'select',
			'options'     => $user_select,
		),

		// category - [display-posts] shortcode argument
		// TODO: multiselect on get_terms(), AJAX updated on 'change' of [category_display]
		array(
			'label'       => esc_html__( 'Specify the category slug (or comma separated list of category slugs)', 'display-posts-shortcode-ui' ),
			'description' => '[category] ' . esc_html__( 'Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'category',
			'type'        => 'text',
		),

		// category_display - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Taxonomy name', 'display-posts-shortcode-ui' ),
			'description' => '[category_display] ' . esc_html__( 'Default: category', 'display-posts-shortcode-ui' ),
			'attr'        => 'category_display',
			'type'        => 'select',
#			'value'       => 'category',
			'options'     => $taxonomy_names,
		),

		// category_label - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Category label', 'display-posts-shortcode-ui' ),
			'description' => '[category_label] ' . esc_html__( 'Default: Posted in: ', 'display-posts-shortcode-ui' ),
			'attr'        => 'category_label',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => 'Posted in: ',
			),
		),

		// date_format - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Specify the date format used when [include_date] is true.', 'display-posts-shortcode-ui' ),
			'description' => '[date_format] ' . esc_html__( 'See [Formatting Date and Time] on the Codex for more information. Default: (n/j/Y)', 'display-posts-shortcode-ui' ),
			'attr'        => 'date_format',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => '(n/j/Y)',
			),
		),

		// date - [display-posts] shortcode argument
		// TODO: datepicker with correct format
		array(
			'label'       => esc_html__( 'Date to compare against', 'display-posts-shortcode-ui' ),
			'description' => '[date] ' . esc_html__( 'Accepts dates entered in the YYYY-MM-DD format. Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'date',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => 'YYYY-MM-DD',
			),
		),

		// date_column - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select date type to show', 'display-posts-shortcode-ui' ),
			'description' => '[date_column] ' . esc_html__( 'Default: post_date', 'display-posts-shortcode-ui' ),
			'attr'        => 'date_column',
			'type'        => 'select',
#			'value'       => 'post_date',
			'options'     => array_combine(
				$date_columns,
				$date_columns
			),
		),

		// date_compare - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select date compare', 'display-posts-shortcode-ui' ),
			'description' => '[date_compare] ' . esc_html__( 'Default: =', 'display-posts-shortcode-ui' ),
			'attr'        => 'date_compare',
			'type'        => 'select',
#			'value'       => '=',
			'options'     => array_combine(
				$date_compare_ops,
				$date_compare_ops
			),
		),

		// date_query_before - [display-posts] shortcode argument
		// TODO: datepicker with correct format
		array(
			'label'       => esc_html__( 'Query posts before Date', 'display-posts-shortcode-ui' ),
			'description' => '[date_query_before] ' . esc_html__( 'Accepts dates entered in the YYYY-MM-DD format. Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'date_query_before',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => 'YYYY-MM-DD',
			),
		),

		// date_query_after - [display-posts] shortcode argument
		// TODO: [date] with correct format
		array(
			'label'       => esc_html__( 'Query posts after Date', 'display-posts-shortcode-ui' ),
			'description' => '[date_query_after] ' . esc_html__( 'Accepts dates entered in the YYYY-MM-DD format. Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'date_query_after',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => 'YYYY-MM-DD',
			),
		),

		// date_query_column - [display-posts] shortcode argument
		// date_query_compare - [display-posts] shortcode argument

		// display_posts_off - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Filter whether to disable the [display-posts] shortcode.', 'display-posts-shortcode-ui' ),
			'description' => '[display_posts_off] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'display_posts_off',
			'type'        => 'checkbox',
		),

		// exclude_current - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Exclude current post from list.', 'display-posts-shortcode-ui' ),
			'description' => '[exclude_current] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'exclude_current',
			'type'        => 'checkbox',
		),

		// id - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select (multiple) Posts to list', 'display-posts-shortcode-ui' ),
			'description' => '[id] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'id',
#			'type'        => 'text',
			'type'        => 'post_select',
			'query'       => array( 'posts_per_page' => 15 ),
			'multiple'    => true,
		),

		// ignore_sticky_posts - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Ignore Sticky Posts', 'display-posts-shortcode-ui' ),
			'description' => '[ignore_sticky_posts] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'ignore_sticky_posts',
			'type'        => 'checkbox',
		),

		// image_size - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Image size', 'display-posts-shortcode-ui' ),
			'description' => '[image_size] ' . esc_html__( 'Specify an image size for displaying the featured image, if the post has one. The [image_size] can be set to default or a custom image sizes. Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'image_size',
			'type'        => 'select',
			'options'     => $image_sizes,
		),

		// include_title - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include title', 'display-posts-shortcode-ui' ),
			'description' => '[include_title] ' . esc_html__( 'Default: true', 'display-posts-shortcode-ui' ),
			'attr'        => 'include_title',
			'type'        => 'checkbox',
#			'value'       => 'true',
		),

		// include_author - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include author', 'display-posts-shortcode-ui' ),
			'description' => '[include_author] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'include_author',
			'type'        => 'checkbox',
		),

		// include_content - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include content', 'display-posts-shortcode-ui' ),
			'description' => '[include_content] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'include_content',
			'type'        => 'checkbox',
		),

		// include_date - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include date', 'display-posts-shortcode-ui' ),
			'description' => '[include_date] ' . esc_html__( 'Include the posts date after the post title. The default format is (7/30/12), but this can be customized using the [date_format] parameter. Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'include_date',
			'type'        => 'checkbox',
		),

		// include_excerpt - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include excerpt', 'display-posts-shortcode-ui' ),
			'description' => '[include_excerpt] ' . esc_html__( 'Include the posts excerpt after the title (and date if provided). Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'include_excerpt',
			'type'        => 'checkbox',
		),

		// meta_key - [display-posts] shortcode argument
		// meta_value - [display-posts] shortcode argument

		// no_posts_message - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'No posts error message', 'display-posts-shortcode-ui' ),
			'description' => '[no_posts_message] ' . esc_html__( 'Content to show if created query matches no posts. Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'no_posts_message',
			'type'        => 'textarea',
		),

		// offset - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Offset', 'display-posts-shortcode-ui' ),
			'description' => '[offset] ' . esc_html__( 'The number of posts to pass over. Default: 0', 'display-posts-shortcode-ui' ),
			'attr'        => 'offset',
			'type'        => 'number',
			'meta'        => array(
				'placeholder' => '0',
				'min'         => '0',
				'step'        => '1',
			),
		),

		// order - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Order', 'display-posts-shortcode-ui' ),
			'description' => '[order] ' . esc_html__( 'Specify whether posts are ordered in descending order (DESC) or ascending order (ASC). Default: DESC', 'display-posts-shortcode-ui' ),
			'attr'        => 'order',
			'type'        => 'radio',
			'value'       => 'DESC',
			'options'     => array(
				'DESC'         => esc_html__( 'Descending order', 'display-posts-shortcode-ui' ),
				'ASC'          => esc_html__( 'Ascending order', 'display-posts-shortcode-ui' ),
			),
		),

		// orderby - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Order by ...', 'display-posts-shortcode-ui' ),
			'description' => '[orderby] ' . esc_html__( 'Specify what the posts are ordered by. Default: date', 'display-posts-shortcode-ui' ),
			'attr'        => 'orderby',
			'type'        => 'select',
			'value'       => 'date',
			'options'     => array(
				'none'           => esc_html__( 'No order', 'display-posts-shortcode-ui' ),
				'ID'             => esc_html__( 'Order by post id', 'display-posts-shortcode-ui' ),
				'author'         => esc_html__( 'Order by author', 'display-posts-shortcode-ui' ),
				'title'          => esc_html__( 'Order by title', 'display-posts-shortcode-ui' ),
				'name'           => esc_html__( 'Order by post name', 'display-posts-shortcode-ui' ),
				'type'           => esc_html__( 'Order by post type', 'display-posts-shortcode-ui' ),
				'date'           => esc_html__( 'Order by date', 'display-posts-shortcode-ui' ),
				'modified'       => esc_html__( 'Order by last modified date', 'display-posts-shortcode-ui' ),
				'parent'         => esc_html__( 'Order by post/page parent id', 'display-posts-shortcode-ui' ),
				'rand'           => esc_html__( 'Random order', 'display-posts-shortcode-ui' ),
				'comment_count'  => esc_html__( 'Order by number of comments', 'display-posts-shortcode-ui' ),
				'menu_order'     => esc_html__( 'Order by Page Order', 'display-posts-shortcode-ui' ),
				'meta_value'     => esc_html__( 'Order by meta value', 'display-posts-shortcode-ui' ),
				'meta_value_num' => esc_html__( 'Order by numeric meta value', 'display-posts-shortcode-ui' ),
				'post__in'       => esc_html__( 'Preserve post ID order given in the post__in array', 'display-posts-shortcode-ui' ),
			),
		),

		// post_parent - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Enter [current] or any post ID, to show children of.', 'display-posts-shortcode-ui' ),
			'description' => '[post_parent] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'post_parent',
			'type'        => 'text',
#			'type'        => 'post_select',
#			'query'       => array( 'post_type' => 'any', 'posts_per_page' => 15 ),
#			'multiple'    => false,
		),

		// post_status - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select post status', 'display-posts-shortcode-ui' ),
			'description' => '[post_status] ' . esc_html__( 'Show posts associated with a certain post status. Default: publish', 'display-posts-shortcode-ui' ),
			'attr'        => 'post_status',
			'type'        => 'select',
			'value'       => 'publish',
			'options'     => $post_status_names,
		),

		// post_type - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select post type', 'display-posts-shortcode-ui' ),
			'description' => '[post_type] ' . esc_html__( 'Specify which post type to use. You can use a default one (post or page), or a custom post type you have created. Default: post', 'display-posts-shortcode-ui' ),
			'attr'        => 'post_type',
			'type'        => 'select',
			'value'       => 'post',
			'options'     => $post_type_names,
		),

		// posts_per_page - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Post count', 'display-posts-shortcode-ui' ),
			'description' => '[posts_per_page] ' . esc_html__( 'How many posts to display. Default: 10', 'display-posts-shortcode-ui' ),
			'attr'        => 'posts_per_page',
			'type'        => 'number',
			'meta'        => array(
				'placeholder' => '10',
				'min'         => '-1',
				'step'        => '1',
			),
		),

		// tag - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Display posts from a specific tag, or tags.', 'display-posts-shortcode-ui' ),
			'description' => '[tag] ' . esc_html__( 'You must use the tag slug(ex: example-tag), not the tags name (ex: Example Tag) (or a comma separated list of tag slugs). Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'tag',
			'type'        => 'select',
			'options'     => $post_tags,
		),

		// tax_operator - [display-posts] shortcode argument
		// TODO: make repeatable to make use of tax_(count)_operator
		array(
			'label'       => esc_html__( 'Taxonomy meta operator', 'display-posts-shortcode-ui' ),
			'description' => '[tax_operator] ' . esc_html__( 'How to query the terms (IN, NOT IN, or AND). Default: IN', 'display-posts-shortcode-ui' ),
			'attr'        => 'tax_operator',
			'type'        => 'radio',
#			'value'       => 'IN',
			'options'     => array(
#				''            => esc_html__( 'None', 'display-posts-shortcode-ui' ),
				'IN'          => 'IN',
				'NOT IN'      => 'NOT IN',
				'AND'         => 'AND',
			),
		),

		// tax_term - [display-posts] shortcode argument
		// TODO: make repeatable to make use of tax_(count)_term
		array(
			'label'       => esc_html__( 'Display posts from a specific term, or terms.', 'display-posts-shortcode-ui' ),
			'description' => '[tax_term] ' . esc_html__( 'You must use the term slug(ex: example-term), not the terms name (ex: Example Term) (or a comma separated list of term slugs). Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'tax_term',
			'type'        => 'text',
		),

		// tax_relation - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Taxonomy relation', 'display-posts-shortcode-ui' ),
			'description' => '[tax_relation] ' . esc_html__( 'Describe the relationship between the multiple taxonomy queries (should the results match all the queries or just one of them). Default: AND', 'display-posts-shortcode-ui' ),
			'attr'        => 'tax_relation',
			'type'        => 'radio',
#			'value'       => 'AND',
			'options'     => array(
				'AND'         => 'AND',
				'OR'          => 'OR',
			),
		),

		// taxonomy - [display-posts] shortcode argument
		// TODO: make repeatable to make use of taxonomy_(count)
		array(
			'label'       => esc_html__( 'Which taxonomy to query', 'display-posts-shortcode-ui' ),
			'description' => '[taxonomy] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'taxonomy',
			'type'        => 'text',
		),

		// time - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Time to compare against', 'display-posts-shortcode-ui' ),
			'description' => '[time] ' . esc_html__( 'Accepts times entered in the HH:MM:SS or HH:MM formats. Default: empty', 'display-posts-shortcode-ui' ),
			'attr'        => 'time',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => 'HH:MM',
			),
		),

		// wrapper - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Wrapper', 'display-posts-shortcode-ui' ),
			'description' => '[wrapper] ' . esc_html__( 'What type of HTML should be used to display the listings. It can be an unordered list(ul), ordered list(ol), or divs(div) which you can then style yourself. Default: ul', 'display-posts-shortcode-ui' ),
			'attr'        => 'wrapper',
			'type'        => 'radio',
			'value'       => 'ul',
			'options'     => array(
				'ul'          => esc_html__( 'Unordered list', 'display-posts-shortcode-ui' ),
				'ol'          => esc_html__( 'Ordered list', 'display-posts-shortcode-ui' ),
				'div'         => esc_html__( 'Div', 'display-posts-shortcode-ui' ),
			),
		),

		// wrapper_class - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Wrapper CSS class', 'display-posts-shortcode-ui' ),
			'description' => '[wrapper_class] ' . esc_html__( 'Class applied to the wrapper tag for custom css formatting for this instance. Default: display-posts-listing', 'display-posts-shortcode-ui' ),
			'attr'        => 'wrapper_class',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => 'display-posts-listing',
			),
		),

		// wrapper_id - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Wrapper CSS id', 'display-posts-shortcode-ui' ),
			'description' => '[wrapper_id] ' . esc_html__( 'Default: false', 'display-posts-shortcode-ui' ),
			'attr'        => 'wrapper_id',
			'type'        => 'text',
		),

	);

	/*
	 * Define the Shortcode UI arguments.
	 */
	$shortcode_ui_args = array(
		/*
		 * How the shortcode should be labeled in the UI. Required argument.
		 */
		'label' => esc_html__( 'Display Posts', 'display-posts-shortcode-ui' ),
		/*
		 * Include an icon with your shortcode. Optional.
		 * Use a dashicon, or full URL to image.
		 */
		'listItemImage' => 'dashicons-list-view',
		/*
		 * Limit this shortcode UI to specific posts. Optional.
		 
		'post_type' => array( 'post' ),*/
		/*
		 * Register UI for the "inner content" of the shortcode. Optional.
		 * If no UI is registered for the inner content, then any inner content
		 * data present will be backed-up during editing.
		 
		'inner_content' => array(
			'label'        => esc_html__( 'Quote', 'display-posts-shortcode-ui' ),
			'description'  => esc_html__( 'Include a statement from someone famous.', 'display-posts-shortcode-ui' ),
		),*/
		/*
		 * Define the UI for attributes of the shortcode. Optional.
		 *
		 * See above, to where the the assignment to the $fields variable was made.
		 */
		'attrs' => apply_filters( 'dpsui_shortcode_ui_fields', $fields ),
	);
#	shortcode_ui_register_for_shortcode( 'display-posts-ui', $shortcode_ui_args );
	shortcode_ui_register_for_shortcode( 'display-posts', $shortcode_ui_args );

}




/*
 * 3. Define the callback for the advanced shortcode.
 */

/* *
 * Callback for the shortcake_dev shortcode.
 *
 * It renders the shortcode based on supplied attributes.

function dpsui_shortcode_handler( $attr, $content, $shortcode_tag ) {

	// Default shortcode attributes
	// Cloned from original plugin file
	$default_atts = array(


		'date_query_column'   => '',
		'date_query_compare'  => '',

		'meta_key'            => '',
		'meta_value'          => '',

	);

	// remove atts with empty values, keep 'false' ones
	$attr = array_filter($attr, function($item){
		return $item !== null && $item !== '';});

	// implode array with key and value without foreach
	$arguments = implode(' ', array_map(function ($v, $k) { return sprintf("%s='%s'", $k, $v); }, $attr, array_keys($attr)));

	// Shortcode callbacks must return content, hence, output buffering here.
	ob_start();
	echo do_shortcode( "[display-posts $arguments]" );
	return ob_get_clean();
}
 */
function dpsui_shortcode_ob_start( $args, $original_atts ) {
	// Shortcode callbacks must return content, hence, output buffering here.
	ob_start();
	return $args;
}
function dpsui_shortcode_ob_get_clean( $wrapper, $original_atts ) {
	// Shortcode callbacks must return content, hence, output buffering here.
	echo ob_get_clean();
	return $wrapper;
}



function dpsui_assets() {
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$use_min_version = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_script( 'diplay-posts-ui', plugins_url('display-posts-shortcode-ui/assets/js/dpsui'.$use_min_version.'.js'), array('jquery','shortcode-ui'), null, true );
#	wp_register_script( 'diplay-posts-ui', plugins_url('display-posts-shortcode-ui/assets/js/dpsui.backbone.view'.$use_min_version.'.js'), array('jquery', 'backbone','shortcode-ui'), null, true );
#	wp_register_script( 'diplay-posts-ui', plugins_url('display-posts-shortcode-ui/assets/js/dpsui.backbone.view'.$use_min_version.'.js'), array('media-views','shortcode-ui'), null, true );
	wp_enqueue_script(  'diplay-posts-ui' );

#	wp_register_style( 'diplay-posts-ui', plugins_url('display-posts-shortcode-ui/assets/css/dpsui'.$use_min_version.'.css'), false, null, 'screen' );
#	wp_enqueue_style(  'display-posts-ui' );
}