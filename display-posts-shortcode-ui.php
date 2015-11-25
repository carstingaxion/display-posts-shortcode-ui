<?php # -*- coding: utf-8 -*-
defined( 'ABSPATH' ) OR exit;

/**
 * Plugin Name: 	Display Posts Shortcode UI
 * Version: 		1.0.0
 * Description: 	Adds a Shortcake powered UI to the [display-posts] shortcode.
 * Author: 			Carsten Bach
 * Author URI: 		
 * Text Domain: 	display-posts-ui
 * License: 		GPL v2 or later
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

/*
 * This plugin handles the registration one shortcode, and the related Shortcode UI:
 *  a. [display_posts_ui] - a wrapper shortcode ...
 *
 * The plugin is broken down into four stages:
 *  0. Check to see if Shortcake is running, with an admin notice if not.
 *  1. Register the shortcode - this is standard WP behaviour, nothing new here.
 *  2. Register the Shortcode UI setup for the wrapper shortcode.
 *  3. Define the callback for the advanced shortcode - fairly standard WP behaviour, nothing new here.
 */


/*
 * 0. Check to see if Shortcake is running, with an admin notice if not.
 */

add_action( 'init', 'shortcode_ui_detection' );
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
function shortcode_ui_detection() {
	if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		add_action( 'admin_notices', 'shortcode_ui_dev_example_notices' );
	}
}

/**
 * Display an administration notice if the user can activate plugins.
 *
 * If the user can't activate plugins, then it's poor UX to show a notice they can't do anything to fix.
 *
 * @since 1.0.0
 */
function shortcode_ui_dev_example_notices() {
	if ( current_user_can( 'activate_plugins' ) ) {
		?>
		<div class="error message">
			<p><?php esc_html_e( 'Shortcode UI plugin must be active for Shortcode UI Example plugin to function.', 'display-posts-ui' ); ?></p>
		</div>
		<?php
	}
}




/*
 * 1. Register the shortcodes.
 */

add_action( 'init', 'shortcode_ui_dev_register_shortcodes' );
/**
 * Register two shortcodes, shortcake_dev and shortcake-no-attributes.
 *
 * This registration is done independently of any UI that might be associated with them, so it always happens, even if
 * Shortcake is not active.
 *
 * @since 1.0.0
 */
function shortcode_ui_dev_register_shortcodes() {
	// 

}
	add_shortcode( 'display_posts_ui', 'shortcode_ui_display_posts_wrapper_shortcode' );




/*
 * 2. Register the Shortcode UI setup for the shortcodes.
 */

//add_action( 'register_shortcode_ui', 'shortcode_ui_for_display_posts_shortcode' );
add_action( 'init', 'shortcode_ui_for_display_posts_shortcode' );
/**
 * Shortcode UI setup for the shortcake-no-attributes shortcode.
 *
 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
 *
 * This example shortcode has no attributes and minimal UI.
 *
 * @since 1.0.0
 */
function shortcode_ui_for_display_posts_shortcode() {

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
			'label'       => esc_html__( 'Give the list a title heading', 'display-posts-ui' ),
			'description' => '[title] ',
			'attr'        => 'title',
			'type'        => 'text',
		),
		// author - [display-posts] shortcode argument
		// category - [display-posts] shortcode argument
		// category_display - [display-posts] shortcode argument
		// category_label - [display-posts] shortcode argument
		// date_format - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Give the list a title heading', 'display-posts-ui' ),
			'description' => '[date_format] ' . esc_html__( 'Specify the date format used when [include_date] is true. See [Formatting Date and Time] on the Codex for more information. Default: (n/j/Y)', 'display-posts-ui' ),
			'attr'        => 'date_format',
			'type'        => 'text',
			'meta'        => array(
				'placeholder' => esc_html__( '(n/j/Y)', 'display-posts-ui' ),
			),
		),

		// date - [display-posts] shortcode argument
		// date_column - [display-posts] shortcode argument
		// date_compare - [display-posts] shortcode argument
		// date_query_before - [display-posts] shortcode argument
		// date_query_after - [display-posts] shortcode argument
		// date_query_column - [display-posts] shortcode argument
		// date_query_compare - [display-posts] shortcode argument
		// display_posts_off - [display-posts] shortcode argument
		// exclude_current - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Exclude current post from list.', 'display-posts-ui' ),
			'description' => '[exclude_current] ',
			'attr'        => 'exclude_current',
			'type'        => 'checkbox',
		),

		// id - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select (multiple) Posts', 'display-posts-ui' ),
			'description' => '[id] ' . esc_html__( 'Default: empty', 'display-posts-ui' ),
			'attr'        => 'id',
			'type'        => 'post_select',
			'query'       => array( 'post_type' => 'any', 'post_per_page' => 15 ),
			'multiple'    => true,
		),

		// ignore_sticky_posts - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Ignore Sticky Posts', 'display-posts-ui' ),
			'description' => '[ignore_sticky_posts] ',
			'attr'        => 'ignore_sticky_posts',
			'type'        => 'radio',
			'options'     => array(
				'1'          => esc_html__( 'show', 'display-posts-ui' ),
				'0'          => esc_html__( 'hide', 'display-posts-ui' ),
			),		),

		// image_size - [display-posts] shortcode argument
		// include_title - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include title', 'display-posts-ui' ),
			'description' => '[include_title] ',
			'attr'        => 'include_title',
			'type'        => 'radio',
			'options'     => array(
				'1'          => esc_html__( 'show', 'display-posts-ui' ),
				'0'          => esc_html__( 'hide', 'display-posts-ui' ),
			),
		),

		// include_author - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include author', 'display-posts-ui' ),
			'description' => '[include_author] ',
			'attr'        => 'include_author',
			'type'        => 'radio',
			'options'     => array(
				'1'          => esc_html__( 'show', 'display-posts-ui' ),
				'0'          => esc_html__( 'hide', 'display-posts-ui' ),
			),		),

		// include_content - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include content', 'display-posts-ui' ),
			'description' => '[include_content] ',
			'attr'        => 'include_content',
			'type'        => 'radio',
			'options'     => array(
				'1'          => esc_html__( 'show', 'display-posts-ui' ),
				'0'          => esc_html__( 'hide', 'display-posts-ui' ),
			),		),

		// include_date - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include date', 'display-posts-ui' ),
			'description' => '[include_date] ' . esc_html__( 'Include the posts date after the post title. The default format is (7/30/12), but this can be customized using the [date_format] parameter. Default: empty', 'display-posts-ui' ),
			'attr'        => 'include_date',
			'type'        => 'radio',
			'value'       => '0',
			'options'     => array(
				'1'          => esc_html__( 'show', 'display-posts-ui' ),
				'0'          => esc_html__( 'hide', 'display-posts-ui' ),
			),		),

		// include_excerpt - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Include excerpt', 'display-posts-ui' ),
			'description' => '[include_excerpt] ' . esc_html__( 'Include the posts excerpt after the title (and date if provided). Default: empty', 'display-posts-ui' ),
			'attr'        => 'include_excerpt',
			'type'        => 'radio',
			'value'       => '0',
			'options'     => array(
				'1'          => esc_html__( 'show', 'display-posts-ui' ),
				'0'          => esc_html__( 'hide', 'display-posts-ui' ),
			),
		),

		// meta_key - [display-posts] shortcode argument
		// meta_value - [display-posts] shortcode argument
		// no_posts_message - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'No posts error message', 'display-posts-ui' ),
			'description' => '[no_posts_message] ' . esc_html__( 'Content to show if created query matches no posts.', 'display-posts-ui' ),
			'attr'        => 'no_posts_message',
			'type'        => 'textarea',
			'meta'        => array(
				'placeholder' => esc_html__( 'Ooops, nothing here.', 'display-posts-ui' ),
			),
		),

		// offset - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Offset', 'display-posts-ui' ),
			'description' => '[offset] ' . esc_html__( 'The number of posts to pass over. Default: 0', 'display-posts-ui' ),
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
			'label'       => esc_html__( 'Order', 'display-posts-ui' ),
			'description' => '[order] ' . esc_html__( 'Specify whether posts are ordered in descending order (DESC) or ascending order (ASC). Default: DESC', 'display-posts-ui' ),
			'attr'        => 'order',
			'type'        => 'radio',
			'value'       => 'DESC',
			'options'     => array(
				'DESC'         => esc_html__( 'Descending order', 'display-posts-ui' ),
				'ASC'          => esc_html__( 'Ascending order', 'display-posts-ui' ),
			),
		),

		// orderby - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Order by ...', 'display-posts-ui' ),
			'description' => '[orderby] ' . esc_html__( 'Specify what the posts are ordered by. Default: date', 'display-posts-ui' ),
			'attr'        => 'orderby',
			'type'        => 'select',
			'value'       => 'date',
			'options'     => array(
				'none'           => esc_html__( 'No order', 'display-posts-ui' ),
				'ID'             => esc_html__( 'Order by post id', 'display-posts-ui' ),
				'author'         => esc_html__( 'Order by author', 'display-posts-ui' ),
				'title'          => esc_html__( 'Order by title', 'display-posts-ui' ),
				'name'           => esc_html__( 'Order by post name', 'display-posts-ui' ),
				'type'           => esc_html__( 'Order by post type', 'display-posts-ui' ),
				'date'           => esc_html__( 'Order by date', 'display-posts-ui' ),
				'modified'       => esc_html__( 'Order by last modified date', 'display-posts-ui' ),
				'parent'         => esc_html__( 'Order by post/page parent id', 'display-posts-ui' ),
				'rand'           => esc_html__( 'Random order', 'display-posts-ui' ),
				'comment_count'  => esc_html__( 'Order by number of comments', 'display-posts-ui' ),
				'menu_order'     => esc_html__( 'Order by Page Order', 'display-posts-ui' ),
				'meta_value'     => esc_html__( 'Order by meta value', 'display-posts-ui' ),
				'meta_value_num' => esc_html__( 'Order by numeric meta value', 'display-posts-ui' ),
				'post__in'       => esc_html__( 'Preserve post ID order given in the post__in array', 'display-posts-ui' ),
			),
		),


		// post_parent - [display-posts] shortcode argument
		// post_status - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select post status', 'display-posts-ui' ),
			'description' => '[post_status] ' . esc_html__( 'Show posts associated with a certain post status. Default: publish', 'display-posts-ui' ),
			'attr'        => 'post_status',
			'type'        => 'select',
			'value'       => 'publish',
			'options'     => array_combine(
				get_post_stati(),
				get_post_stati()
			),
		),

		// post_type - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Select post type', 'display-posts-ui' ),
			'description' => '[post_type] ' . esc_html__( 'Specify which post type to use. You can use a default one (post or page), or a custom post type you have created. Default: post', 'display-posts-ui' ),
			'attr'        => 'post_type',
			'type'        => 'select',
			'value'       => 'post',
			'options'     => array_combine(
				get_post_types( array('public'   => true), 'names' ),
				get_post_types( array('public'   => true), 'names' )
			),
		),

		// posts_per_page - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Post count', 'display-posts-ui' ),
			'description' => '[posts_per_page] ' . esc_html__( 'How many posts to display. Default: 10', 'display-posts-ui' ),
			'attr'        => 'posts_per_page',
			'type'        => 'number',
			'meta'        => array(
				'placeholder' => '10',
				'min'         => '-1',
				'step'        => '1',
			),
		),

		// tag - [display-posts] shortcode argument
		// tax_operator - [display-posts] shortcode argument
		// tax_term - [display-posts] shortcode argument
		// taxonomy - [display-posts] shortcode argument
		// time - [display-posts] shortcode argument
		// wrapper - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Wrapper', 'display-posts-ui' ),
			'description' => '[wrapper] ' . esc_html__( 'What type of HTML should be used to display the listings. It can be an unordered list(ul), ordered list(ol), or divs(div) which you can then style yourself. Default: (ul)', 'display-posts-ui' ),
			'attr'        => 'wrapper',
			'type'        => 'radio',
			'value'       => 'ul',
			'options'     => array(
				'ul'          => esc_html__( 'Unordered list', 'display-posts-ui' ),
				'ol'          => esc_html__( 'Ordered list', 'display-posts-ui' ),
				'div'         => esc_html__( 'Div', 'display-posts-ui' ),
			),
		),

		// wrapper_class - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Wrapper CSS class', 'display-posts-ui' ),
			'description' => '[wrapper_class] ' . esc_html__( 'Class applied to the wrapper tag for custom css formatting for this instance. Default: empty', 'display-posts-ui' ),
			'attr'        => 'wrapper_class',
			'type'        => 'text',
		),

		// wrapper_id - [display-posts] shortcode argument
		array(
			'label'       => esc_html__( 'Wrapper CSS id', 'display-posts-ui' ),
			'description' => '[wrapper_id] ',
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
		'label' => esc_html__( 'Display Posts UI', 'display-posts-ui' ),
		/*
		 * Include an icon with your shortcode. Optional.
		 * Use a dashicon, or full URL to image.
		 */
		'listItemImage' => 'dashicons-feedback',
		/*
		 * Limit this shortcode UI to specific posts. Optional.
		 
		'post_type' => array( 'post' ),*/
		/*
		 * Register UI for the "inner content" of the shortcode. Optional.
		 * If no UI is registered for the inner content, then any inner content
		 * data present will be backed-up during editing.
		 
		'inner_content' => array(
			'label'        => esc_html__( 'Quote', 'display-posts-ui' ),
			'description'  => esc_html__( 'Include a statement from someone famous.', 'display-posts-ui' ),
		),*/
		/*
		 * Define the UI for attributes of the shortcode. Optional.
		 *
		 * See above, to where the the assignment to the $fields variable was made.
		 */
		'attrs' => $fields,
	);
	shortcode_ui_register_for_shortcode( 'display_posts_ui', $shortcode_ui_args );

}




/*
 * 3. Define the callback for the advanced shortcode.
 */

/**
 * Callback for the shortcake_dev shortcode.
 *
 * It renders the shortcode based on supplied attributes.
 */
function shortcode_ui_display_posts_wrapper_shortcode( $attr, $content, $shortcode_tag ) {

	// Default shortcode attributes
	// Cloned from original plugin file
	$default_atts = array(
		'title'              => '',
		'author'              => '',
		'category'            => '',
		'category_display'    => '',
		'category_label'      => 'Posted in: ',
		'date_format'         => '(n/j/Y)',
		'date'                => '',
		'date_column'         => 'post_date',
		'date_compare'        => '=',
		'date_query_before'   => '',
		'date_query_after'    => '',
		'date_query_column'   => '',
		'date_query_compare'  => '',
		'display_posts_off'   => false,
		'exclude_current'     => false,
		'id'                  => false,
		'ignore_sticky_posts' => false,
		'image_size'          => false,
		'include_title'       => true,
		'include_author'      => false,
		'include_content'     => false,
		'include_date'        => false,
		'include_excerpt'     => false,
		'meta_key'            => '',
		'meta_value'          => '',
		'no_posts_message'    => '',
		'offset'              => 0,
		'order'               => 'DESC',
		'orderby'             => 'date',
		'post_parent'         => false,
		'post_status'         => 'publish',
		'post_type'           => 'post',
		'posts_per_page'      => '10',
		'tag'                 => '',
		'tax_operator'        => 'IN',
		'tax_term'            => false,
		'taxonomy'            => false,
		'time'                => '',
		'wrapper'             => 'ul',
		'wrapper_class'       => 'display-posts-listing',
		'wrapper_id'          => false,
	);

	// Overwrite defaults with custom values
	#$atts = wp_parse_args( $attr, $default_atts );
	$atts = $attr;
	// remove atts with empty values, keep 'false' ones
	$atts = array_filter($atts, function($item){
		return $item !== null && $item !== '';});

	// implode array with key and value without foreach
#	$arguments = http_build_query( $atts,'',' ');
	$arguments = implode(' ', array_map(function ($v, $k) { return sprintf("%s='%s'", $k, $v); }, $atts, array_keys($atts)));


	// Shortcode callbacks must return content, hence, output buffering here.
	ob_start();
	#echo '<pre>[display-posts '.$arguments.']</pre>';
	echo do_shortcode( "[display-posts $arguments]" );
	return ob_get_clean();
}