=== HW Image Widget ===
Contributors: Håkan Wennerberg
Tags: image widget, image, widget, responsive
Requires at least: 3.5
Tested up to: 4.2.1
Stable tag: 4.4
License: LGPLv3
License URI: http://www.gnu.org/licenses/lgpl-3.0.html

Image widget that will allow you to choose responsive or fixed sized behavior. Includes TinyMCE rich text editing of the text description.

== Description ==

This widget requires WordPress 3.5 or newer.


Primary features of HW Image Widget:

* Allow you to choose responsive or fixed behavior.
* Fixed sized images allow you to define width/height with, or without kept aspect ratio.
* Responsive sized images will allow you to define "fill width" or not.
* Uses TinyMCE for rich text editing of the image text field.
* Allow you to create a custom widget HTML-template in the active theme to override the default layout.
* Default settings can be overridden using filter.
* Works with Carrington Build.
* Works with the theme customizer.
* Available in English and Swedish.

For more info, visit http://webartisan.se/hw-image-widget/


== Installation ==

Use standard installation process for a plug-in:

1. Install HW Image Widget either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Can I change the way the widget is displayed? =

Yes

= Can I put the title below the image instead? =

Yes

= Can I change the default settings of the widget? =

Yes

For more info on how to achieve all of this and more, please visit http://webartisan.se/hw-image-widget/


== Screenshots ==

1. Back-end, using responsive behavior.
2. Back-end, using fixed behavior.
3. Editing image text using TinyMCE for rich text editing.
4. Selecting an image.

== Changelog ==

= 4.4 =
* Added option to fill width on responsive images.
* Added filter (hwim_load_backend) to allow backend to load on custom pages.
* Fixed several issues concerning the theme customizer.

= 4.3 =
* Added the rel-field including filter to alter its contents.
* Made the widget fully support the theme customizer.

= 4.2 =
* Added backend link.

= 4.1 =
* Fixed notice on front-end when no image has been selected.

= 4.0 =
* Added support for WordPress 4.0.
* Replaced custom image selection view with WordPress default "image insert" view to add compatability with WordPress 4.0 while still retaining pre-WordPress 4.0 compatability. This introduces a "Gallery" insertion option in the view, but its ignored by the HW Image Widget if used.
* Made sure JavaScript tags are stripped from the text preview area so they do not execute.
* Widget now depends on SimpleXML PHP-extention (usually available on shared hosting) to strip JavaScript from previews.
* Fixed issues with the HTML view of the TinyMCE editor.
* Made sure TinyMCE editor opens up with a decent height.
* Made sure TinyMCE editor opens up in visual mode.
* Removed all cookie handling (cookies are no longer edited to set proper TinyMCE height for some buggy WP-versions).
* Fixed issue that could cause height/width attributes for fixed sized images in admin change when multiple image widgets are used and a new image is selected.

= 3.0 =
* Adding support for using HW Image Widget in the Carrington Build plugin (widgetized pages).
* Refactoring plugin initialization.
* Minor admin UI CSS tweaks.

= 2.7 =
* Removing alt attribute from A-tag to conform with HTML standards.

= 2.6 =
* Adding title-attribute to the image tag.

= 2.5 =
* Renamed template file so its more logical for template creators to locate and use. It will not break compatability with previous versions.
* Launched http://webartisan.se/hw-image-widget/ with more helpful info on widget usage.

= 2.4 =
* Fixed issue that could cause the TinyMCE editor to fail while in WP_DEBUG on some setups.

= 2.3.2 =
* Fixed issue with text editor now opening when regular post editor was left in HTML-view.

= 2.3.1 =
* Minor stylefix for WordPress 3.6 RC1 (back-end field border color).

= 2.3 =
* Removed debug snippet.

= 2.2 =
* Fixed bug with the target option.

= 2.1 =
* Fixed JavaScript bug that could cause widget page JavaScrips to fail.
* Gracefully fail on old WordPress versions.

= 2.0 =
* Implemented updated Media Library introduced in WordPress 3.5.
* Updated text editor user interface to be more consistent with WordPress.
* Dropped support for WordPress older then 3.5 to keep it light and wonderful.
* Dropped usage of Twitter Bootstrap.
* Rewrote JavaScript to be less receptive to conflicts with other plugins (could still happen though).
* Fixed TinyMCE editor height when its first opened in the browser. 

= 1.6 =
* Fixed image selection issue when using multiple widgets.
* Play more nicely with other widgets using image selection tool.

= 1.5 =
* Fixed JS-issue with FireFox.

= 1.4 =
* Fixed "From URL" image selection bug.

= 1.3 =
* Clearing image height/width attributes when removing image.
* Fixing width/height not being set in WP 3.5+

= 1.2 =
* Some refactoring to support PHP5 < 5.3
* Dropped loading "optimizations" to improve overall compatability with custom widget area handling plugins. 

= 1.1 =
* Fixed front-end display issue.
* Fixed media upload tab not working.

= 1.0 =
* First version. Why go beta?
