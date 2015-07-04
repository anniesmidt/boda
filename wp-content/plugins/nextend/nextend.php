<?php
/*
Plugin Name: Nextend
Plugin URI: http://www.nextendweb.com
Description: Nextend Library for Accordion Menu and future plugins.
Version: 1.3.6
Author: Nextend
Author URI: http://www.nextendweb.com
License: GPL2
*/

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'wp-library.php');

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'wordpress' . DIRECTORY_SEPARATOR . 'pluginupdatechecker.php');

$updateChecker = new PluginUpdateChecker(
    'http://www.nextendweb.com/update2/wordpress/index.php?action=get_metadata&slug=nextend&api-key='.md5(get_bloginfo('url')),
    __FILE__
);

?>