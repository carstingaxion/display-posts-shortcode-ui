/*var Backbone = require('backbone'),
    $ = require('jquery');
*/
var Shortcode_UI_Extended = Backbone.View.extend({
    /*
    * This function is a good place for subscribing to events and
    * get things ready.

    initialize: function() {
        // If you don't want to change the behavior of a method
        // in the parent class Just override it as follows:
        wp.media.view.Shortcode_UI.initialize.apply( this, arguments );
    },
    */ 
    initialize: function(options) {
        this.model.on('change', this.render, this);
console.log(this);
    },


    /*
    * This method fires when the user switches between tabs or when
    * the collection of results is rendered.
    */
    render: function() {
        console.log('wp.media.view.Shortcode_UI.extend RENDER');
    },
 
    /*
    * This method returns the rendered HTML of a item passed as
    * parameter.
    * Must return the plain HTML of the parsed model.
    * Override this method in case you want to change its behavior.

    renderItem: function( model ) {
    },
    */ 
    /*
    * This method performs the AJAX request

    fetchItems: function( ) {
    },
    */ 
    /*
    * fetchedSuccess fires when the AJAX request returns a success.
    * response contains all the response data

    fetchedSuccess: function( response ) {
    },
    */ 
    /*
    * fetchedError fires when the AJAX request returns  an error

    fetchedError: function( response ) {
    },
    */
});

//module.exports = Shortcode_UI_Extended;