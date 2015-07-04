<?php
if(!defined('NEXTENDACCORDIONMENULITE')){

    add_action('admin_menu', 'nextend_accordion_menu_update_page');
    
    function nextend_accordion_menu_update_page() {
    	add_submenu_page('edit.php?post_type=accordion_menu', 'Nextend Accordion Menu License', 'License', 'manage_options', __FILE__, 'nextend_accordion_menu_settings_page');
    }
    
    function nextend_accordion_menu_settings_page() {
    ?>
    <div>
    <h2>Nextend Accordion Menu</h2>
    <?php
      if(isset($_POST['nextend_accordion_menu_license'])){
          $_POST['nextend_accordion_menu_license'] = trim($_POST['nextend_accordion_menu_license']);
          if(base64_encode(base64_decode($_POST['nextend_accordion_menu_license'])) === $_POST['nextend_accordion_menu_license']){
              update_option('nextend_accordion_menu_license', $_POST['nextend_accordion_menu_license']);
          }else{
             echo '<div class="error"><strong><p>The validity of license key failed</p></strong></div>';
          }
      }
    ?>
    
    <form method="post" action="<?php echo admin_url("edit.php?post_type=accordion_menu&page=nextend-accordion-menu/update.php"); ?>">
    <p>If you fill out the license key field, you will be able to use the the WordPress plugin updater with Nextend Accordion Menu.</p>
    <p>You can get your license key in the <a target="_blank" href="http://www.nextendweb.com/availabledownloads/">Download area</a> at Nextendweb.com</p>
        <table class="form-table">
            <tr valign="top">
            <th scope="row">License key</th>
            <td><input type="text" style="width: 400px;" name="nextend_accordion_menu_license" value="<?php echo get_option('nextend_accordion_menu_license', ''); ?>" /></td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    
    </form>
    </div>
    <?php 
    } 
}
