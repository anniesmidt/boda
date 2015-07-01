=== Ambrosite Next/Previous Page Link Plus ===
Contributors: ambrosite
Donate link: http://www.ambrosite.com/plugins
Tags: adjacent, next, previous, page, link, navigation, pages, links, sort, sorted, sortable, order, loop, thumbnail, thumbnails
Requires at least: 2.5
Tested up to: 3.3
Stable tag: trunk

Creates two new template tags for generating next/previous page navigation links.

== Description ==

**IMPORTANT: Make sure you are using the right plugin.**

* **Next/Previous Page Link Plus** is intended for use in **page** templates.
* **Next/Previous Post Link Plus** is intended for use in **single post** templates.

The two plugins have similar sounding, but different, function names. If you mistakenly install the wrong plugin, you will get a "call to undefined function" error. If you want to create next/previous links for your posts (including custom post types), please check out:
http://wordpress.org/extend/plugins/ambrosite-nextprevious-post-link-plus/

This plugin creates two new template tags -- **next_page_link_plus** and **previous_page_link_plus** -- which may be used to generate next/previous navigation links for pages. The new tags include the following options:

* Sort the next/previous page links on any column, including alphabetically, by date, and by menu_order.
* Sort the next/previous links on custom fields (both string and integer sorts are supported).
* Loop around to the first page if there is no next page (and vice versa).
* Retrieve the first/last page, rather than the previous/next page (for First|Previous|Next|Last navigation links).
* Display the featured image alongside the links (WordPress 2.9 or higher).
* Truncate the link titles to any length, and display custom text in the tooltip.
* Display the title, date, author, and meta value of the next/previous links.
* Specify a custom date format for the %date variable.
* Restrict next/previous links to same parent page, author, or custom field value.
* Exclude or include individual page IDs.
* Return multiple next/previous links (e.g. the next N links, in an HTML list).
* Return the ID, title, date, href attribute, or post object of the next/previous links, instead of echoing them to the screen.
* Return false if no next/previous link is found, so themes may conditionally display alternate text.

Extensive documentation on configuring the plugin may be found here:
http://www.ambrosite.com/plugins/next-previous-page-link-plus-for-wordpress

== Installation ==

* Upload ambrosite-page-link-plus.php to the /wp-content/plugins/ directory.
* Activate the plugin through the Plugins menu in WordPress.
* Edit your page template(s), and insert the next_page_link_plus and previous_page_link_plus template tags where you want your next/previous links to be displayed. Configure them using parameters as explained in the online documentation:
http://www.ambrosite.com/plugins/next-previous-page-link-plus-for-wordpress

== Frequently Asked Questions ==

* How exactly do I install this plugin? Which file needs to be edited, and where do I put the code?
* I am getting a "call to undefined function" error. Why?
* How can I get rid of the arrows on my next/previous links?
* Is this plugin compatible with page reordering plugins like PageMash, My Page Order, CMS Tree Page View, and Post Types Order?
* I am using a custom field with a simple integer value to order my pages, but they're not sorting correctly. Why?
* I am seeing the number '1' printed next to my links. Why?
* Is there any way to use an image instead of link text?

Answers to these questions may be found here:
http://www.ambrosite.com/plugins/next-previous-page-link-plus-for-wordpress#faq

== Changelog ==

= 1.1 =
* Added 'in_same_meta' parameter.
* Added 'return' parameter to specify what should be returned from the function.
* Added 'date_format' parameter for customizing the %date variable.
* Added %title variable to 'format' parameter.
* Added option to sort on custom fields as integers rather than strings.
* Added new classes to anchor tags, thumbnails, and list items to aid CSS styling.

= 1.0 =
* Initial version.
