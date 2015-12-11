'use strict';
jQuery(document).ready(function($) {

/* ==========================================================================

========================================================================== */
	var $form = $('form.edit-shortcode-form');
	// Option Groups
	var group = [
		'title',
		'id',
	];

	var $__temp = $();

	$( group ).each(function( index ) {
//		console.log( index + ": " + $( this ).text() );

		$__temp.add( $form.find('.shortcode-ui-attribute-'+ group[index]) );
		
	});

	$__temp.wrapAll( "<div style='background-color:red;' class='new' />");

});