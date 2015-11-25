=== Display Posts Shortcode UI ===
Contributors: carstenbach
Tags: shortcode ui, shortcake, display-posts, display-posts-shortcode, TinyMCE
Requires at least: 
Tested up to: 4.3.1
Stable tag: 4.3.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds a Shortcake powered UI to the [display-posts] shortcode.

== Description ==



This plugin is a bridge between the well-known `[display-posts]` - shortcode plugin and the 


handles the registration of one shortcode, and the related Shortcode UI:
a. [display-posts-ui] - a wrapper shortcode for [display-posts]

The plugin is broken down into four stages:
0. Check to see if "Shortcode UI (Shortcake)" and "Display Posts Shirtcode" plugins are running, with an admin notice if not.
1. Register a wraper shortcode which gets the settings from the UI and delivers theese to the `[display-posts]`-shortcode.
2. Register the Shortcode UI setup for the wrapper shortcode.
3. Define the callback for the wrapper shortcode - fairly standard WP behaviour, nothing new here.
 

== Installation ==

1. Upload `/display-posts-shortcode-ui`-folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use UI via "Insert element" in TinyMCE
4. Happy short-coding!


== Screenshots ==

1. 
2. 

== Changelog ==

= 2015.11.25 =
Initial release
