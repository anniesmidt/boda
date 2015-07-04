<?php
/*
Plugin Name: Nextend Accordion Menu
Plugin URI: http://nextendweb.com/
Description: User-friendly, highly customizable and easy to integrate menu solution to build custom accordion menus from any WordPress menu.
Version: 9.2.9
Author: Nextend
Author URI: http://www.nextendweb.com
License: GPL2
 */

/*  Copyright 2012  Roland Soos - Nextend  (email : roland@nextendweb.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
define('NEXTEND_ACCORDION_MENU', dirname(__FILE__) . DIRECTORY_SEPARATOR );
define('NEXTEND_ACCORDION_MENU_ASSETS', NEXTEND_ACCORDION_MENU . 'library' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR );

register_activation_hook( __FILE__, 'nextend_accordion_menu_activation');
function nextend_accordion_menu_activation(){
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install.php');
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'update.php');

add_action( 'plugins_loaded', 'nextend_accordion_menu_load');
function nextend_accordion_menu_load(){
        
    if(defined('NEXTENDLIBRARY')){
        nextendimport('nextend.wordpress.pluginupdatechecker');
        $updateChecker = new PluginUpdateChecker(
            'http://www.nextendweb.com/update2/wordpress/index.php?action=get_metadata&slug=nextend-accordion-menu&api-key='.get_option('nextend_accordion_menu_license', ''),
            __FILE__
        );
    }
    
    if(is_admin()){
        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'admin.php');
    }
    
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'shortcode.php');
    
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'widget.php');
}