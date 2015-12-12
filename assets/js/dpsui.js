//jQuery(function($) {
(function($) {
	'use strict';

	// Wait for window to load
	$(window).load(function () {


		/* ==========================================================================

		========================================================================== */
		var reArrangeFormFields = function () {

			// Option Groups
			var group = [
				{'Standard Queries':[
					'title',
					'post_type',
					'posts_per_page',
					'ignore_sticky_posts',
					'offset',
					'order',
					'orderby',
					'author',
				]},
				{'Date Queries':[
					'date_format',
					'date',
					'date_column',
					'date_compare',
					'date_query_before',
					'date_query_after',
					'time',
				]},
				{'Tags, Categories and Taxonomies':[
					'category',
					'category_display',
					'category_label',
					'tag',
					'tax_operator',
					'tax_term',
					'tax_relation',
					'taxonomy',
				]},
				{'misc':[
					'id',
					'post_parent',
					'exclude_current',
					'post_status',
					'no_posts_message',
					'display_posts_off',
				]},
				{'Behaviour':[
					'image_size',
					'include_title',
					'include_author',
					'include_content',
					'include_date',
					'include_excerpt',
					'wrapper',
					'wrapper_class',
					'wrapper_id',
				]},
			];

			var iterate = 0;

			// iterate over our option groups
			$.each( group, function( key, value ) {

				// temp jQuery object to hold our settings-elements
				var __temp = $();

				// iterate over our options
				$.each( this, function( k, v ) {
					$.each( this, function( k, v ) {

						// find html that fits our settings
						var _selector = $('.shortcode-ui-attribute-'+ v);

						// break if nothing is selected
						// TODO

						// add selected setting to our temp holder obj
						__temp = __temp.add( _selector.parent() );

					}); // end 3rd each

					if( iterate != 0 )
						var css_class = 'closed'

					__temp
						// wrap them together in sense-making groups
						.wrapAll( '<div class="inside" />')
						// The .inside is now the parent of our elements
						.parent()
						// id is important to postbox.js
						.wrapAll( '<div class="postbox" id="dpsui-option-group-'+k+'" />')
						// The .postbox is now the parent of our elements
						.parent()
						// Add some default wp markup
						.prepend( '<h3 class="hndle ui-sortable-handle"><span>'+k+'</span></h3>' )
						.prepend( '<div class="handlediv" title="Zum Umschalten klicken"><br></div>' )
						.addClass( css_class );

				}); // end 2nd each

				iterate++;
			}); // end 1st each

			//
		//	$('.edit-shortcode-form .postbox h3').prepend('<a class="togbox">+</a> ');
			//
			$('.edit-shortcode-form .postbox h3').click( function() {
				$($(this).parent().get(0)).toggleClass('closed');
			});

		} // end fn // 

		/* ==========================================================================

		========================================================================== */
		$(".media-frame-content").bind("DOMSubtreeModified", reArrangeFormFields() );

		// hacky helper for WP UI styling
		$('.edit-shortcode-form').addClass('meta-box-sortables ui-sortable').attr('id', 'poststuff');


	});


})(jQuery);