<?php
/*
Plugin Name: Ambrosite Next/Previous Page Link Plus
Plugin URI: http://www.ambrosite.com/plugins
Description: Creates two new template tags for generating next/previous page navigation links.
Version: 1.1
Author: J. Michael Ambrosio
Author URI: http://www.ambrosite.com
License: GPL2
*/

/**
 * Retrieve adjacent page link. Can either be next or previous page link.
 * The function only retrieves pages that have the same post_parent,
 * and are on the same level of the page hierarchy, as the current page.
 *
 * @param array $r Arguments.
 * @param bool $previous Optional. Whether to retrieve previous page.
 * @return array of post objects.
 */
function get_adjacent_page_plus($r, $previous = true ) {
	global $post, $wpdb;

	extract( $r, EXTR_SKIP );

	if ( empty( $post ) )
		return null;

//	Sanitize $order_by, since we are going to use it in the SQL query. Default to 'post_title'.
	if ( in_array($order_by, array('post_date', 'post_title', 'post_excerpt', 'post_name', 'post_modified')) ) {
		$order_format = '%s';
	} elseif ( in_array($order_by, array('ID', 'post_author', 'menu_order', 'comment_count')) ) {
		$order_format = '%d';
	} elseif ( $order_by == 'custom' && !empty($meta_key) ) { // Don't allow a custom sort if meta_key is empty.
		$order_format = '%s';
	} elseif ( $order_by == 'numeric' && !empty($meta_key) ) {
		$order_format = '%d';
	} else {
		$order_by = 'post_title';
		$order_format = '%s';
	}
	
//	Sanitize $order_2nd. Only columns containing unique values are allowed here. Default to 'post_title'.
	if ( in_array($order_2nd, array('post_date', 'post_title', 'post_modified')) ) {
		$order_format2 = '%s';
	} elseif ( in_array($order_2nd, array('ID')) ) {
		$order_format2 = '%d';
	} else {
		$order_2nd = 'post_title';
		$order_format2 = '%s';
	}
	
//	Sanitize num_results (non-integer or negative values trigger SQL errors)
	$num_results = intval($num_results) < 2 ? 1 : intval($num_results);

//	Queries involving custom fields require an extra table join
	if ( $order_by == 'custom' || $order_by == 'numeric' ) {
		$current_page = get_post_meta($post->ID, $meta_key, TRUE);
		$order_by = ($order_by === 'numeric') ? 'm.meta_value+0' : 'm.meta_value';
		$meta_join = $wpdb->prepare(" INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id AND m.meta_key = %s", $meta_key );
	} elseif ( $in_same_meta ) {
		$current_page = $post->$order_by;
		$order_by = 'p.' . $order_by;
		$meta_join = $wpdb->prepare(" INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id AND m.meta_key = %s", $in_same_meta );
	} else {
		$current_page = $post->$order_by;
		$order_by = 'p.' . $order_by;
		$meta_join = '';
	}

//	Get the current page value for the second sort column
	$current_page2 = $post->$order_2nd;
	$order_2nd = 'p.' . $order_2nd;
	
//	Put this section in a do-while loop to enable the loop-to-first-page option
	do {
		$join = $meta_join;
		$excluded_pages = $ex_pages;
		$included_pages = $in_pages;
		$in_same_parent_sql = $in_same_author_sql = $in_same_meta_sql = $ex_pages_sql = $in_pages_sql = '';

//		Optionally restrict next/previous links to same parent page		
		if ( $in_same_parent )
			$in_same_parent_sql = $wpdb->prepare("AND p.post_parent = %d", $post->post_parent );

//		Optionally restrict next/previous links to same author		
		if ( $in_same_author )
			$in_same_author_sql = $wpdb->prepare("AND p.post_author = %d", $post->post_author );

//		Optionally restrict next/previous links to same meta value
		if ( $in_same_meta && $r['order_by'] != 'custom' && $r['order_by'] != 'numeric' )
			$in_same_meta_sql = $wpdb->prepare("AND m.meta_value = %s", get_post_meta($post->ID, $in_same_meta, TRUE) );

//		Optionally exclude individual page IDs
		if ( !empty($excluded_pages) ) {
			$excluded_pages = array_map( 'intval', explode(',', $excluded_pages) );
			$ex_pages_sql = " AND p.ID NOT IN (" . implode(',', $excluded_pages) . ")";
		}
		
//		Optionally include individual page IDs
		if ( !empty($included_pages) ) {
			$included_pages = array_map( 'intval', explode(',', $included_pages) );
			$in_pages_sql = " AND p.ID IN (" . implode(',', $included_pages) . ")";
		}

		$adjacent = $previous ? 'previous' : 'next';
		$order = $previous ? 'DESC' : 'ASC';
		$op = $previous ? '<' : '>';

//		Optionally get the first/last page. Disable looping and return only one result.
		if ( $end_page ) {
			$order = $previous ? 'ASC' : 'DESC';
			$num_results = 1;
			$loop = false;
			if ( $end_page === 'fixed' ) // display the end page link even when it is the current page
				$op = $previous ? '<=' : '>=';
		}

//		If there is no next/previous page, loop back around to the first/last page.		
		if ( $loop && isset($result) ) {
			$op = $previous ? '>=' : '<=';
			$loop = false; // prevent an infinite loop if no first/last page is found
		}
		
//		In case the value in the $order_by column is not unique, select pages based on the $order_2nd column as well.
//		This prevents pages from being skipped when they have, for example, the same menu_order.
		$where = $wpdb->prepare("WHERE ( $order_by $op $order_format OR $order_2nd $op $order_format2 AND $order_by = $order_format ) AND p.post_type = 'page' AND p.post_status = 'publish' $in_same_parent_sql $in_same_author_sql $in_same_meta_sql $ex_pages_sql $in_pages_sql", $current_page, $current_page2, $current_page);

		$sort  = "ORDER BY $order_by $order, $order_2nd $order LIMIT $num_results";

		$query = "SELECT DISTINCT p.* FROM $wpdb->posts AS p $join $where $sort";
		$query_key = 'adjacent_page_plus_' . md5($query);
		$result = wp_cache_get($query_key);
		if ( false !== $result )
			return $result;

//		echo $query . '<br />';

//		Use get_results instead of get_row, in order to retrieve multiple adjacent pages (when $num_results > 1)
		$result = $wpdb->get_results("SELECT DISTINCT p.* FROM $wpdb->posts AS p $join $where $sort");
		if ( null === $result )
			$result = '';

	} while ( !$result && $loop );

	wp_cache_set($query_key, $result);
	return $result;
}

/**
 * Display previous page link that is adjacent to the current page.
 *
 * @param array|string $args Optional. Override default arguments.
 * @return bool True if previous page link is found, otherwise false.
 */
function previous_page_link_plus($args = '') {
	return adjacent_page_link_plus($args, '&laquo; %link', true);
}

/**
 * Display next page link that is adjacent to the current page.
 *
 * @param array|string $args Optional. Override default arguments.
 * @return bool True if next page link is found, otherwise false.
 */
function next_page_link_plus($args = '') {
	return adjacent_page_link_plus($args, '%link &raquo;', false);
}

/**
 * Display adjacent page link. Can be either next page link or previous.
 *
 * @param array|string $args Optional. Override default arguments.
 * @param bool $previous Optional, default is true. Whether display link to previous page.
 * @return bool True if next/previous page is found, otherwise false.
 */
function adjacent_page_link_plus($args = '', $format = '%link &raquo;', $previous = true) {
	$defaults = array(
		'order_by' => 'post_title', 'order_2nd' => 'post_title', 'meta_key' => '',
		'loop' => false, 'end_page' => false, 'thumb' => false, 'max_length' => 0,
		'format' => '', 'link' => '%title', 'date_format' => '', 'tooltip' => '%title',
		'in_same_parent' => false, 'in_same_author' => false, 'in_same_meta' => false,
		'ex_pages' => '', 'in_pages' => '',
		'before' => '', 'after' => '', 'num_results' => 1, 'return' => false, 'echo' => true
	);

	$r = wp_parse_args( $args, $defaults );
	if ( empty($r['format']) )
		$r['format'] = $format;
	if ( empty($r['date_format']) )
		$r['date_format'] = get_option('date_format');

	if ( $previous && is_attachment() ) {
		$pages = array();
		$pages[] = & get_post($GLOBALS['post']->post_parent);
	} else
		$pages = get_adjacent_page_plus($r, $previous);

//	If there is no next/previous page, return false so themes may conditionally display inactive link text.
	if ( !$pages )
		return false;

//	If sorting by date, display pages in reverse chronological order. Otherwise display in alpha/numeric order.
	if ( ($previous && $r['order_by'] != 'post_date') || (!$previous && $r['order_by'] == 'post_date') )
		$pages = array_reverse( $pages, true );

//	Option to return something other than the formatted link		
	if ( $r['return'] ) {
		if ( $r['num_results'] == 1 ) {
			reset($pages);
			$page = current($pages);
			if ( $r['return'] === 'id')
				return $page->ID;
			if ( $r['return'] === 'href')
				return get_permalink($page);
			if ( $r['return'] === 'object')
				return $page;
			if ( $r['return'] === 'title')
				return $page->post_title;
			if ( $r['return'] === 'date')
				return mysql2date($r['date_format'], $page->post_date);
		} elseif ( $r['return'] === 'object')
			return $pages;
	}

	$output = $r['before'];

//	When num_results > 1, multiple adjacent pages may be returned. Use foreach to display each adjacent page.
	foreach ( $pages as $page ) {
		$title = $page->post_title;
		if ( empty($page->post_title) )
			$title = $previous ? __('Previous Page') : __('Next Page');

		$title = apply_filters('the_title', $title, $page->ID);
		$date = mysql2date($r['date_format'], $page->post_date);
		$author = get_the_author_meta('display_name', $page->post_author);
	
//		Set anchor title attribute to long post title or custom tooltip text. Supports variable replacement in custom tooltip.
		if ( $r['tooltip'] ) {
			$tooltip = str_replace('%title', $title, $r['tooltip']);
			$tooltip = str_replace('%date', $date, $tooltip);
			$tooltip = str_replace('%author', $author, $tooltip);
			$tooltip = ' title="' . esc_attr($tooltip) . '"';
		} else
			$tooltip = '';

//		Truncate the link title to nearest whole word under the length specified.
		$max_length = intval($r['max_length']) < 1 ? 9999 : intval($r['max_length']);
		if ( strlen($title) > $max_length )
			$title = substr( $title, 0, strrpos(substr($title, 0, $max_length), ' ') ) . '...';
	
		$rel = $previous ? 'prev' : 'next';

		$anchor = '<a class="' . $rel . '-page-anchor" href="'.get_permalink($page).'" rel="'.$rel.'"'.$tooltip.'>';
		$link = str_replace('%title', $title, $r['link']);
		$link = str_replace('%date', $date, $link);
		$link = $anchor . $link . '</a>';
	
		$format = str_replace('%link', $link, $r['format']);
		$format = str_replace('%title', $title, $format);
		$format = str_replace('%date', $date, $format);
		$format = str_replace('%author', $author, $format);
		if ( ($r['order_by'] == 'custom' || $r['order_by'] == 'numeric') && !empty($r['meta_key']) ) {
			$meta = get_post_meta($page->ID, $r['meta_key'], true);
			$format = str_replace('%meta', $meta, $format);
		} elseif ( $r['in_same_meta'] ) {
			$meta = get_post_meta($page->ID, $r['in_same_meta'], true);
			$format = str_replace('%meta', $meta, $format);
		}

//		Optionally add the post thumbnail to the link. Wrap the link in a span to aid CSS styling.
		if ( $r['thumb'] && has_post_thumbnail($page->ID) ) {
			if ( $r['thumb'] === true ) // use 'post-thumbnail' as the default size
				$r['thumb'] = 'post-thumbnail';
			$thumbnail = '<a class="' . $rel . '-page-thumbnail page-thumbnail" href="'.get_permalink($page).'" rel="'.$rel.'"'.$tooltip.'>' . get_the_post_thumbnail( $page->ID, $r['thumb'] ) . '</a>';
			$format = $thumbnail . '<span class="' . $rel . '-page-link page-link">' . $format . '</span>';
		}

//		If more than one link is returned, wrap them in <li> tags		
		if ( intval($r['num_results']) > 1 )
			$format = '<li class="' . $rel . '-page-item">' . $format . '</li>';
		
		$output .= $format;
	}

	$output .= $r['after'];

	//	If echo is false, don't display anything. Return the link as a PHP string.
	if ( !$r['echo'] || $r['return'] === 'output' )
		return $output;

	$adjacent = $previous ? 'previous' : 'next';
	echo $output;

	return true;
}
?>