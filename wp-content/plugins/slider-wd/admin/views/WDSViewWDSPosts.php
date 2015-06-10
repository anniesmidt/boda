<?PHP

class WDSViewWDSPosts {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display() {
    $slide_id = WDW_S_Library::get('slide_id', 0);
    if ($slide_id) {
      $single = 1;
    }
    else {
      $single = 0;
    }
    $search_value = ((isset($_POST['search_value'])) ? esc_html(stripslashes($_POST['search_value'])) : '');
    $category_id = ((isset($_POST['category_id'])) ? esc_html(stripslashes($_POST['category_id'])) : '');
    $asc_or_desc = ((isset($_POST['asc_or_desc'])) ? esc_html(stripslashes($_POST['asc_or_desc'])) : 'ASC');
    $order_by = (isset($_POST['order_by']) ? esc_html(stripslashes($_POST['order_by'])) : 'date');
    $count = (isset($_GET['count']) ? esc_html(stripslashes($_GET['count'])) : 0);
    $order_class = 'manage-column column-title sorted ' . $asc_or_desc;

    $datas = $this->model->get_rows_data();
    $rows_data = $datas[0];
    $page_limit = $datas[2];

    wp_print_scripts('jquery');
    ?>
    <link media="all" type="text/css" href="<?php echo get_admin_url(); ?>load-styles.php?c=1&amp;dir=ltr&amp;load=admin-bar,wp-admin,dashicons,buttons,wp-auth-check" rel="stylesheet">
    <?php if (get_bloginfo('version') < '3.9') { ?>
    <link media="all" type="text/css" href="<?php echo get_admin_url(); ?>css/colors<?php echo ((get_bloginfo('version') < '3.8') ? '-fresh' : ''); ?>.min.css" id="colors-css" rel="stylesheet">
    <?php } ?>
    <link media="all" type="text/css" href="<?php echo WD_S_URL . '/css/wds_tables.css'; ?>" rel="stylesheet" />
    <script src="<?php echo WD_S_URL . '/js/wds.js'; ?>" type="text/javascript"></script>
    <form class="wrap wp-core-ui" id="posts_form" method="post" action="<?php echo add_query_arg(array('action' => 'WDSPosts', 'width' => '700', 'height' => '550', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" style="width:99%; margin: 0 auto;">
      <h2 style="float: left;">Posts</h2>
      <input type="button" class="button-primary" title="Add Post" onclick="wds_add_post(jQuery('#ids_string').val(), <?php echo $count; ?>);
                                                                            window.parent.tb_remove();" style="float: right; margin: 9px 0;" value="Add to slider" />
      <div class="tablenav top">
        <?php
        WDW_S_Library::search('Title', $search_value, 'posts_form');
        wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'category_id', 'orderby' => 'name', 'selected' => $category_id, 'hierarchical' => TRUE, 'show_option_none' => 'View all categories', 'class' => 'wds_category_name'));
        WDW_S_Library::html_page_nav($datas[1], $page_limit, 'posts_form');
        ?>
      </div>
      <div class="spider_message"><div class="updated"><p><strong>You can include only posts with featured image.</strong></p></div></div>
      <table class="wp-list-table widefat fixed pages">
        <thead>
          <th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;" /></th>
          <th class="table_large_col">Featured image</th>
          <th class="<?php if ($order_by == 'title') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('order_by', 'title');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'title') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'posts_form')" href="">
              <span>Title</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="<?php if ($order_by == 'author') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('order_by', 'author');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'author') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'posts_form')" href="">
              <span>Author</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="table_small_col">Type</th>
          <th class="<?php if ($order_by == 'date') {echo $order_class;} ?> table_large_col">
            <a onclick="spider_set_input_value('order_by', 'date');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'date') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'posts_form')" href="">
              <span>Date created</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="<?php if ($order_by == 'modified') {echo $order_class;} ?> table_large_col">
            <a onclick="spider_set_input_value('order_by', 'modified');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'modified') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'posts_form')" href="">
              <span>Date modified</span><span class="sorting-indicator"></span>
            </a>
          </th>
        </thead>
        <tbody id="tbody_arr">
          <?php
          $ids_string = '';
          if ($rows_data) {
            foreach ($rows_data as $row_data) {
              $alternate = (!isset($alternate) || $alternate == 'class="alternate"') ? '' : 'class="alternate"';
              ?>
              <tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
                <td class="table_small_col check-column"><input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox" /></td>
                <td class="table_large_col">
                  <img title="<?php echo $row_data->title; ?>" style="border: 1px solid #CCCCCC; max-width: 70px; max-height: 50px;" src="<?php echo $row_data->thumb_url; ?>" />
                </td>
                <td><a onclick="jQuery('#check_<?php echo $row_data->id; ?>').attr('checked', 'checked'); wds_add_post('<?php echo $row_data->id; ?>,', <?php echo $single ?>); window.parent.tb_remove();" id="a_<?php echo $row_data->id; ?>" style="cursor: pointer;"><?php echo $row_data->title; ?></a></td> 
                <td><?php echo $row_data->author; ?></td>
                <td class="table_small_col"><?php echo $row_data->type; ?></td>
                <td class="table_large_col"><?php echo $row_data->date; ?></td>
                <td class="table_large_col"><?php echo $row_data->modified; ?></td>
                <input type="hidden" name="wds_title_<?php echo $row_data->id; ?>" id="wds_title_<?php echo $row_data->id; ?>" value="<?php echo $row_data->title; ?>" />
                <input type="hidden" name="wds_image_url_<?php echo $row_data->id; ?>" id="wds_image_url_<?php echo $row_data->id; ?>" value="<?php echo $row_data->image_url; ?>" />
                <input type="hidden" name="wds_thumb_url_<?php echo $row_data->id; ?>" id="wds_thumb_url_<?php echo $row_data->id; ?>" value="<?php echo $row_data->thumb_url; ?>" />
                <input type="hidden" name="wds_link_<?php echo $row_data->id; ?>" id="wds_link_<?php echo $row_data->id; ?>" value="<?php echo $row_data->link; ?>" />
                <input type="hidden" name="wds_content_<?php echo $row_data->id; ?>" id="wds_content_<?php echo $row_data->id; ?>" value="<?php echo esc_html($row_data->content); ?>" />
              </tr>
              <?php
              $ids_string .= $row_data->id . ',';
            }
          }
          ?>
        </tbody>
      </table>
      <input id="asc_or_desc" name="asc_or_desc" type="hidden" value="<?php echo $asc_or_desc; ?>" />
      <input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>" />
      <input id="slide_id" name="slide_id" type="hidden" value="<?php echo $slide_id; ?>" />
      <input id="ids_string" name="ids_string" type="hidden" value="<?php echo $ids_string; ?>" />
      <input id="task" name="task" type="hidden" value="" />
    </form>
    <style>
      .wds_category_name {
        margin: 3px;
        <?php echo (get_bloginfo('version') > '3.7') ? ' height: 28px;' : ''; ?>
      }
    </style>
    <script>
      jQuery(window).load(function () {
        jQuery(".wds_category_name").change(function () {
          jQuery("#page_number").val(1);
          jQuery("#search_or_not").val("search");
          jQuery("#posts_form").submit();
        });
        <?php
        if ($count) {
          ?>
          jQuery("input[type='checkbox']").on("click", function() {
            jQuery("input[type='checkbox']").attr('checked', false);
            jQuery(this).attr('checked', true);
          });
          <?php
        }
        ?>
      });
    </script>
    <script src="<?php echo get_admin_url(); ?>load-scripts.php?c=1&load%5B%5D=common,admin-bar" type="text/javascript"></script>
    <?php
    die();
  }

  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}