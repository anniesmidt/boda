<?php

class WDSViewSlider {
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
  public function display($id, $from_shortcode = 0, $wds = 0) {
    require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
    $slider_row = $this->model->get_slider_row_data($id);
    if (!$slider_row->published) {
      return;
    }
    $resolutions = array(320, 480, 640, 768, 800, 1024, 1366, 1824, 3000);
    if (!$slider_row) {
      echo WDW_S_Library::message(__('There is no slider selected or the slider was deleted.', 'wds'), 'error');
      return;
    }
    $image_right_click = $slider_row->image_right_click;

    $bull_position = $slider_row->bull_position;
    $bull_style_active = str_replace('-o', '', $slider_row->bull_style);
    $bull_style_deactive = $slider_row->bull_style;
    $bull_size_cont = $slider_row->bull_size + $slider_row->bull_margin * 2;

    $slide_rows = $this->model->get_slide_rows_data($id);
    if (!$slide_rows) {
      echo WDW_S_Library::message(__('There are no slides in this slider.', 'wds'), 'error');
    }

    $image_width = $slider_row->width;
    $image_height = $slider_row->height;

    $slides_count = count($slide_rows);
    $slideshow_effect = $slider_row->effect == 'zoomFade' ? 'fade' : $slider_row->effect;
    $slideshow_interval = $slider_row->time_intervval;

    $circle_timer_size = (2 * $slider_row->timer_bar_size - 2) * 2;

    $enable_slideshow_shuffle = $slider_row->shuffle;
    $enable_prev_next_butt = $slider_row->prev_next_butt;
    $enable_play_paus_butt = $slider_row->play_paus_butt;
    if (!$enable_prev_next_butt && !$enable_play_paus_butt) {
      $enable_slideshow_autoplay = 1;
    }
    else {
      $enable_slideshow_autoplay = $slider_row->autoplay;
    }
    if ($enable_slideshow_autoplay && !$enable_play_paus_butt && ($slides_count > 1)) {
      $autoplay = TRUE;
    }
    else {
      $autoplay = FALSE;
    }
    if ($slider_row->navigation == 'always') {
      $navigation = 0;
      $pp_btn_opacity = 1;
    }
    else {
      $navigation = 4000;
      $pp_btn_opacity = 0;
    }
    $enable_slideshow_music = $slider_row->music;
    $slideshow_music_url = $slider_row->music_url;

    $filmstrip_direction = ($slider_row->film_pos == 'right' || $slider_row->film_pos == 'left') ? 'vertical' : 'horizontal';
    $filmstrip_position = $slider_row->film_pos;
    $filmstrip_thumb_margin_hor = 2 * $slider_row->film_tmb_margin;
    if ($filmstrip_position != 'none') {
      if ($filmstrip_direction == 'horizontal') {
        $filmstrip_width = $slider_row->film_thumb_width;
        $filmstrip_height = $slider_row->film_thumb_height;
        $filmstrip_width_in_percent = 0;
      }
      else {
        $filmstrip_width = $slider_row->film_thumb_width;
        $filmstrip_height = $slider_row->film_thumb_height;
        $filmstrip_width_in_percent = 100 * $slider_row->film_thumb_width / $image_width;
      }
    }
    else {
      $filmstrip_width = 0;
      $filmstrip_height = 0;
      $filmstrip_width_in_percent = 0;
    }
    $left_or_top = 'left';
    $width_or_height = 'width';
    $outerWidth_or_outerHeight = 'outerWidth';
    if (!($filmstrip_direction == 'horizontal')) {
      $left_or_top = 'top';
      $width_or_height = 'height';
      $outerWidth_or_outerHeight = 'outerHeight';
    }

    if ($enable_slideshow_shuffle || ($slider_row->start_slide_num == 0)) {
      $slide_ids = array();
      foreach ($slide_rows as $slide_row) {
        $slide_ids[] += $slide_row->id;
      }
      $current_image_id = $slide_ids[array_rand($slide_ids)];
      $start_slide_num = array_search($current_image_id, $slide_ids);
    }
    else {
      if ($slider_row->start_slide_num > 0 && $slider_row->start_slide_num <= $slides_count) {
        $start_slide_num = $slider_row->start_slide_num - 1;
      }
      else {
        $start_slide_num = 0;
      }
      $current_image_id = ($slide_rows ? $slide_rows[$start_slide_num]->id : 0);
    }

    global $wp;
    $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));

    $carousel = $slider_row->carousel;
    $carousel_image_parameters = $slider_row->carousel_image_parameters;
    $carousel_image_counts = $slider_row->carousel_image_counts;
    $carousel_fit_containerWidth = $slider_row->carousel_fit_containerWidth;
    $carousel_width = $slider_row->carousel_width;
    $preload_images = $slider_row->carousel ? FALSE : $slider_row->preload_images;
    ?>
    <style>
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> {
        text-align: <?php echo $slider_row->align; ?>;
        margin: <?php echo $slider_row->glb_margin; ?>px <?php echo $slider_row->full_width ? 0 : ''; ?>;
        visibility: hidden;
        <?php echo $slider_row->full_width ? 'position: relative;' : ''; ?>
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_wrap_<?php echo $wds; ?> * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_wrap_<?php echo $wds; ?> {
        background-color: <?php echo WDW_S_Library::spider_hex2rgba($slider_row->background_color, (100 - $slider_row->background_transparent) / 100); ?>;
        border-width: <?php echo $slider_row->glb_border_width; ?>px;
        border-style: <?php echo $slider_row->glb_border_style; ?>;
        border-color: #<?php echo $slider_row->glb_border_color; ?>;
        border-radius: <?php echo $slider_row->glb_border_radius; ?>;
        border-collapse: collapse;
        display: inline-block;
        position: <?php echo $slider_row->full_width ? 'absolute' : 'relative'; ?>;
        text-align: center;
        width: 100%;
        <?php
        if (!$carousel) {
          ?>
        max-width: <?php echo $image_width; ?>px;
          <?php
        }
        ?>
        box-shadow: <?php echo $slider_row->glb_box_shadow; ?>;
        overflow: hidden;
        z-index: 0;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_<?php echo $wds; ?> {
        padding: 0 !important;
        margin: 0 !important;
        float: none !important;
        vertical-align: middle;
        background-position: center center;
        background-repeat: no-repeat;
        background-size: <?php echo $slider_row->bg_fit; ?>;
        width: 100%;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_container_<?php echo $wds; ?> {
        display: /*table*/block;
        position: absolute;
        text-align: center;
        <?php echo $filmstrip_position; ?>: <?php echo ($filmstrip_direction == 'horizontal' ? $filmstrip_height . 'px' : $filmstrip_width_in_percent . '%'); ?>;
        vertical-align: middle;
        width: <?php echo 100 - $filmstrip_width_in_percent; ?>%;
        height: /*inherit*/100%;
      }

      <?php
      foreach ($resolutions as $key => $resolution) {
        if ($key) {
          $prev_resolution = $resolutions[$key - 1] + 1;
        }
        else {
          $prev_resolution = 0;
        }
        $media_slide_height = ($image_width > $resolution) ? ($image_height * $resolution) / $image_width : $image_height;
        $media_bull_size = ((int) ($resolution / 26) > $slider_row->bull_size) ? $slider_row->bull_size : (int) ($resolution / 26);
        $media_bull_margin = ($slider_row->bull_margin > 2 && $resolution < 481) ? 2 : $slider_row->bull_margin;
        $media_bull_size_cont = $media_bull_size + $media_bull_margin * 2;
        $media_pp_butt_size = ((int) ($resolution / 16) > $slider_row->pp_butt_size) ? $slider_row->pp_butt_size : (int) ($resolution / 16);
        $media_rl_butt_size = ((int) ($resolution / 16) > $slider_row->rl_butt_size) ? $slider_row->rl_butt_size : (int) ($resolution / 16);
        ?>
      @media only screen and (min-width: <?php echo $prev_resolution; ?>px) and (max-width: <?php echo $resolution; ?>px) {
        #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_dots_thumbnails_<?php echo $wds; ?> {
          height: <?php echo $media_bull_size_cont; ?>px;
          width: <?php echo $media_bull_size_cont * $slides_count; ?>px;
        }
        #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_dots_<?php echo $wds; ?> {
          font-size: <?php echo $media_bull_size; ?>px;
          width: <?php echo $media_bull_size; ?>px;
          margin: <?php echo $media_bull_margin; ?>px;
          height: <?php echo $media_bull_size; ?>px;
        }
        #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_pp_btn_cont {  
          font-size: <?php echo $media_pp_butt_size; ?>px;
          height: <?php echo $media_pp_butt_size; ?>px;
          width: <?php echo $media_pp_butt_size; ?>px;
        }
        #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_left_btn_cont,
        #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_right_btn_cont {
          height: <?php echo $media_rl_butt_size; ?>px;
          font-size: <?php echo $media_rl_butt_size; ?>px;
          width: <?php echo $media_rl_butt_size; ?>px;
        }
      }
        <?php
      }
      ?>

      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_video_<?php echo $wds; ?> {
        padding: 0 !important;
        margin: 0 !important;
        float: none !important;
        width: 100%;
        vertical-align: middle;
        display: inline-block;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?> {  
        color: #<?php echo $slider_row->butts_color; ?>;
        cursor: pointer;
        position: relative;
        z-index: 13;
        width: inherit;
        height: inherit;
        font-size: inherit;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?>:hover {  
        color: #<?php echo $slider_row->hover_color; ?>;
        cursor: pointer;
      }
      <?php
      if ($slider_row->play_paus_butt_img_or_not != 'style') {
        ?>
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_play_pause_<?php echo $wds; ?>.fa-pause:before,
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_play_pause_<?php echo $wds; ?>.fa-play:before {
          content: "";
      }
	    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?>.fa-play {
        background-image: url('<?php echo addslashes(htmlspecialchars_decode ($slider_row->play_butt_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-out;
        -ms-transition: background-image 0.2s ease-out;
        -moz-transition: background-image 0.2s ease-out;
        -webkit-transition: background-image 0.2s ease-out;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?>.fa-play:before {
        content: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->play_butt_hov_url, ENT_QUOTES)); ?>');
        width: 0;
        height: 0;
        visibility: hidden;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?>.fa-play:hover {
        background-image: url('<?php echo addslashes(htmlspecialchars_decode ($slider_row->play_butt_hov_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover; 
        transition: background-image 0.2s ease-in;
        -ms-transition: background-image 0.2s ease-in;
        -moz-transition: background-image 0.2s ease-in;
        -webkit-transition: background-image 0.2s ease-in;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?>.fa-pause{
        background-image: url('<?php echo addslashes(htmlspecialchars_decode ($slider_row->paus_butt_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-out;
        -ms-transition: background-image 0.2s ease-out;
        -moz-transition: background-image 0.2s ease-out;
        -webkit-transition: background-image 0.2s ease-out;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?>.fa-pause:before {
        content: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->paus_butt_hov_url, ENT_QUOTES)); ?>');
        width: 0;
        height: 0;
        visibility: hidden;
      }
	    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?>.fa-pause:hover {
        background-image: url('<?php echo addslashes(htmlspecialchars_decode ($slider_row->paus_butt_hov_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-in;
        -ms-transition: background-image 0.2s ease-in;
        -moz-transition: background-image 0.2s ease-in;
        -webkit-transition: background-image 0.2s ease-in;
      }
        <?php
      }
      ?>
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_left-ico_<?php echo $wds; ?>,
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_right-ico_<?php echo $wds; ?> {
        background-color: <?php echo WDW_S_Library::spider_hex2rgba($slider_row->nav_bg_color, (100 - $slider_row->butts_transparent) / 100); ?>;
        border-radius: <?php echo $slider_row->nav_border_radius; ?>;
        border: <?php echo $slider_row->nav_border_width; ?>px <?php echo $slider_row->nav_border_style; ?> #<?php echo $slider_row->nav_border_color; ?>;
        border-collapse: separate;
        color: #<?php echo $slider_row->butts_color; ?>;
        left: 0;
        top: 0;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
        cursor: pointer;
        line-height: 0;
        width: inherit;
        height: inherit;
        font-size: inherit;
        position: absolute;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_left-ico_<?php echo $wds; ?> {
        left: -<?php echo $navigation; ?>px;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_right-ico_<?php echo $wds; ?> {
        left: <?php echo $navigation; ?>px;
      }
      <?php
      if ($slider_row->rl_butt_img_or_not != 'style') {
        ?>
	    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_left-ico_<?php echo $wds; ?> {
        left: -<?php echo $navigation; ?>px;
        background-image: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->left_butt_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-out;
        -ms-transition: background-image 0.2s ease-out;
        -moz-transition: background-image 0.2s ease-out;
        -webkit-transition: background-image 0.2s ease-out;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_left-ico_<?php echo $wds; ?>:before {
        content: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->left_butt_hov_url, ENT_QUOTES)); ?>');
        width: 0;
        height: 0;
        visibility: hidden;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_left-ico_<?php echo $wds; ?>:hover {
        left: -<?php echo $navigation; ?>px;
        background-image: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->left_butt_hov_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover; 
        transition: background-image 0.2s ease-in;
        -ms-transition: background-image 0.2s ease-in;
        -moz-transition: background-image 0.2s ease-in;
        -webkit-transition: background-image 0.2s ease-in;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_right-ico_<?php echo $wds; ?> {
        left: <?php echo $navigation; ?>px;
        background-image: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->right_butt_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-out;
        -ms-transition: background-image 0.2s ease-out;
        -moz-transition: background-image 0.2s ease-out;
        -webkit-transition: background-image 0.2s ease-out;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_right-ico_<?php echo $wds; ?>:before {
        content: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->right_butt_hov_url, ENT_QUOTES)); ?>');
        width: 0;
        height: 0;
        visibility: hidden;
      }
	    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_right-ico_<?php echo $wds; ?>:hover {
        left: <?php echo $navigation; ?>px;
        background-image: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->right_butt_hov_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-in;
        -ms-transition: background-image 0.2s ease-in;
        -moz-transition: background-image 0.2s ease-in;
        -webkit-transition: background-image 0.2s ease-in;
      }
        <?php
      }
      ?>
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #wds_slideshow_play_pause_<?php echo $wds; ?> {
        opacity: <?php echo $pp_btn_opacity; ?>;
        filter: "Alpha(opacity=<?php echo $pp_btn_opacity * 100; ?>)";
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_left-ico_<?php echo $wds; ?>:hover,
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_right-ico_<?php echo $wds; ?>:hover {
        color: #<?php echo $slider_row->hover_color; ?>;
        cursor: pointer;
      }

      /* Filmstrip*/
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_container_<?php echo $wds; ?> {
        background-color: #<?php echo $slider_row->film_bg_color; ?> !important;
        display: <?php echo ($filmstrip_direction == 'horizontal' ? '/*table*/block' : 'block'); ?>;
        height: <?php echo ($filmstrip_direction == 'horizontal' ? $filmstrip_height . 'px' : '100%'); ?>;
        position: absolute;
        width: <?php echo ($filmstrip_direction == 'horizontal' ? '100%' : $filmstrip_width_in_percent . '%'); ?>;
        z-index: 10105;
        <?php echo $filmstrip_position; ?>: 0;
        overflow: hidden;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_<?php echo $wds; ?> {
        overflow: hidden;
        position: absolute;
        <?php echo $width_or_height; ?>: 100%;
        width: 100%;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?> {
        height: <?php echo $filmstrip_direction == 'horizontal' ? 'inherit' : ($filmstrip_height + $filmstrip_thumb_margin_hor) * $slides_count . 'px'; ?>;
        <?php echo $left_or_top; ?>: 0px;
        margin: 0 auto;
        overflow: hidden;
        position: relative;
        width: <?php echo ($filmstrip_direction == 'horizontal' ? ($filmstrip_width + $filmstrip_thumb_margin_hor) * $slides_count . 'px' : '100%'); ?>;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_thumbnail_<?php echo $wds; ?> {
        position: relative;
        background: none;
        cursor: pointer;
        float: left;
        height: <?php echo $filmstrip_direction == 'horizontal' ? 'inherit' : $filmstrip_height . 'px'; ?>;
        margin: <?php echo $filmstrip_direction == 'horizontal' ? '0 ' . $slider_row->film_tmb_margin . 'px' : $slider_row->film_tmb_margin . 'px 0'; ?>;
        width: <?php echo ($filmstrip_direction == 'horizontal' ? $filmstrip_width . 'px' : '100%'); ?>;
        overflow: hidden;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_thumb_active_<?php echo $wds; ?> div {
        opacity: 1;
        filter: Alpha(opacity=100);
        border: <?php echo $slider_row->film_act_border_width; ?>px <?php echo $slider_row->film_act_border_style; ?> #<?php echo $slider_row->film_act_border_color; ?>;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_thumb_deactive_<?php echo $wds; ?> {
        opacity: <?php echo number_format((100 - $slider_row->film_dac_transparent) / 100, 2, ".", ""); ?>;
        filter: Alpha(opacity=<?php echo 100 - $slider_row->film_dac_transparent; ?>);
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_thumbnail_img_<?php echo $wds; ?> {
        display: block;
        opacity: 1;
        filter: Alpha(opacity=100);
        padding: 0 !important;
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        width: 100%;
        height: <?php echo $filmstrip_direction == 'horizontal' ? $filmstrip_height . 'px' : $filmstrip_height . 'px'; ?>;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_left_<?php echo $wds; ?>,
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_right_<?php echo $wds; ?> {
        background-color: rgba(0, 0, 0, 0.5);
        cursor: pointer;
        display: table;
        vertical-align: middle;
        <?php echo $width_or_height; ?>: 20px;
        z-index: 10000;
        position: absolute;
        <?php echo ($filmstrip_direction == 'horizontal' ? 'height: inherit' : 'width: 100%') ?>; 
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_left_<?php echo $wds; ?> {
        <?php echo $left_or_top; ?>: 0;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_right_<?php echo $wds; ?> {
        <?php echo($filmstrip_direction == 'horizontal' ? 'right' : 'bottom') ?>: 0;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_left_<?php echo $wds; ?> i,
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_filmstrip_right_<?php echo $wds; ?> i {
        color: #FFFFFF;
        display: table-cell;
        font-size: 20px;
        vertical-align: middle;
      }

      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_none_selectable_<?php echo $wds; ?> {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slide_container_<?php echo $wds; ?> {
        display: table-cell;
        margin: 0 auto;
        position: absolute;
        vertical-align: middle;
        width: 100%;
        height: 100%;
        overflow: hidden;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slide_bg_<?php echo $wds; ?> {
        margin: 0 auto;
        width: /*inherit*/100%;
        height: /*inherit*/100%;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slider_<?php echo $wds; ?> {
        height: /*inherit*/100%;
        width: /*inherit*/100%;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_spun_<?php echo $wds; ?> {
        width: /*inherit*/100%;
        height: /*inherit*/100%;
        display: table-cell;
        filter: Alpha(opacity=100);
        opacity: 1;
        position: absolute;
        vertical-align: middle;
        z-index: 2;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_second_spun_<?php echo $wds; ?> {
        width: /*inherit*/100%;
        height: /*inherit*/100%;
        display: table-cell;
        filter: Alpha(opacity=0);
        opacity: 0;
        position: absolute;
        vertical-align: middle;
        z-index: 1;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_grid_<?php echo $wds; ?> {
        display: none;
        height: 100%;
        overflow: hidden;
        position: absolute;
        width: 100%;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_gridlet_<?php echo $wds; ?> {
        opacity: 1;
        filter: Alpha(opacity=100);
        position: absolute;
      }

      /* Dots.*/
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_dots_container_<?php echo $wds; ?> {
        display: block;
        overflow: hidden;
        position: absolute;
        width: 100%;
        <?php echo $bull_position; ?>: 0;
        /*z-index: 17;*/
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_dots_thumbnails_<?php echo $wds; ?> {
        left: 0px;
        font-size: 0;
        margin: 0 auto;
        position: relative;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_dots_<?php echo $wds; ?> {
        display: inline-block;
        position: relative;
        color: #<?php echo $slider_row->bull_color; ?>;
        cursor: pointer;
        z-index: 17;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_dots_active_<?php echo $wds; ?> {
        color: #<?php echo $slider_row->bull_act_color; ?>;
        opacity: 1;
        filter: Alpha(opacity=100);
        <?php
        if ($slider_row->bull_butt_img_or_not != 'style') {
          ?>
        display: inline-block;
        background-image: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->bullets_img_main_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-in;
        -ms-transition: background-image 0.2s ease-in;
        -moz-transition: background-image 0.2s ease-in;
        -webkit-transition: background-image 0.2s ease-in;
          <?php
        }
        ?>
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_dots_deactive_<?php echo $wds; ?> {
        <?php
        if ($slider_row->bull_butt_img_or_not != 'style') {
          ?>
        display: inline-block;
        background-image: url('<?php echo addslashes(htmlspecialchars_decode($slider_row->bullets_img_hov_url, ENT_QUOTES)); ?>');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: background-image 0.2s ease-in;
        -ms-transition: background-image 0.2s ease-in;
        -moz-transition: background-image 0.2s ease-in;
        -webkit-transition: background-image 0.2s ease-in;
          <?php
        }
        ?>
      }

      <?php
      if ($slider_row->timer_bar_type == 'top' || $slider_row->timer_bar_type == 'bottom') {
        ?>
      /* Line timer.*/
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_line_timer_container_<?php echo $wds; ?> {
        display: block;
        position: absolute;
        overflow: hidden;
        <?php echo $slider_row->timer_bar_type; ?>: 0;
        z-index: 16;
        width: 100%;
        height: <?php echo $slider_row->timer_bar_size; ?>px;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_line_timer_<?php echo $wds; ?> {
        z-index: 17;
        width: 0;
        height: <?php echo $slider_row->timer_bar_size; ?>px;
        background: #<?php echo $slider_row->timer_bar_color; ?>;
        opacity: <?php echo number_format((100 - $slider_row->timer_bar_transparent) / 100, 2, ".", ""); ?>;
        filter: alpha(opacity=<?php echo 100 - $slider_row->timer_bar_transparent; ?>);
      }
        <?php
      }
      elseif ($slider_row->timer_bar_type != 'none') {
        ?>
      /* Circle timer.*/
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_line_timer_container_<?php echo $wds; ?> {
        display: block;
        position: absolute;
        overflow: hidden;
        <?php echo $slider_row->timer_bar_type; ?>: 0;
        z-index: 16;
        width: 100%;
        height: <?php echo $circle_timer_size; ?>px;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_circle_timer_container_<?php echo $wds; ?> {
        display: block;
        position: absolute;
        overflow: hidden;
        z-index: 16;
        width: 100%;
        <?php switch ($slider_row->timer_bar_type) { 
        case 'circle_top_right': echo 'top: 0px; text-align:right;'; break;
        case 'circle_top_left': echo 'top: 0px; text-align:left;'; break;
        case 'circle_bot_right': echo 'bottom: 0px; text-align:right;'; break;
        case 'circle_bot_left': echo 'bottom: 0px; text-align:left;'; break;
        default: 'top: 0px; text-align:right;';
         } ?>
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_circle_timer_container_<?php echo $wds; ?> .wds_circle_timer_<?php echo $wds; ?> {
        display: inline-block;
        width: <?php echo $circle_timer_size; ?>px;
        height: <?php echo $circle_timer_size; ?>px;
        position: relative;
        opacity: <?php echo number_format((100 - $slider_row->timer_bar_transparent) / 100, 2, ".", ""); ?>;
        filter: alpha(opacity=<?php echo 100 - $slider_row->timer_bar_transparent; ?>);
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_circle_timer_container_<?php echo $wds; ?> .wds_circle_timer_<?php echo $wds; ?> .wds_circle_timer_parts_<?php echo $wds; ?> {
        display: table;
        width: 100%;
        height: 100%;
        border-radius: 100%;
        position: relative;
      }
      .wds_circle_timer_part_<?php echo $wds; ?> {
        display: table-cell;
        width: 50%;
        height: 100%;
        overflow: hidden !important;
      }
      .wds_circle_timer_small_parts_<?php echo $wds; ?> {
        display: block;
        width: 100%;
        height: 50%;
        background: #<?php echo $slider_row->timer_bar_color; ?>;
        position: relative;
      }
      .wds_circle_timer_center_cont_<?php echo $wds; ?> {
        display: table;
        width: <?php echo $circle_timer_size; ?>px;
        height: <?php echo $circle_timer_size; ?>px;
        position: absolute;
        text-align: center;
        top:0px;
        vertical-align:middle;
      }
      .wds_circle_timer_center_<?php echo $wds; ?> {
        display: table-cell;
        width: 100%; 
        height: 100%;
        text-align: center;
        line-height: 0px !important;
        vertical-align: middle;
      }
      .wds_circle_timer_center_<?php echo $wds; ?> div {
        display: inline-block;
        width: <?php echo $circle_timer_size / 2 - 2; ?>px;
        height: <?php echo $circle_timer_size / 2 - 2; ?>px;		
        background-color: #FFFFFF;
        border-radius: 100%;
        z-index: 300;
        position: relative;
      }

        <?php
      }
      ?>
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slide_container_<?php echo $wds; ?> {        
        height: /*inherit*/100%;
      }
     #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_spun1_<?php echo $wds; ?> {
        display: table; 
        width: <?php echo $carousel ? "100%" : "/*inherit*/100%"; ?>;
        height: <?php echo $carousel ? "100%" : "/*inherit*/100%"; ?>;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slideshow_image_spun2_<?php echo $wds; ?> {
        display: table-cell; 
        vertical-align: middle; 
        text-align: center;
        overflow: hidden;
        height: /*inherit*/100%;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_video_layer_frame_<?php echo $wds; ?> {
        max-height: 100%;
        max-width: 100%;
        width: 100%;
        height: 100%;
      }
       #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_video_hide<?php echo $wds; ?> {
        width: 100%;
        height: 100%;
        position:absolute;
      }
      #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_slider_car_image<?php echo $wds; ?> {
        overflow: hidden;
      }
      <?php echo $slider_row->css; ?>
    </style>
    <script>
      var wds_data_<?php echo $wds; ?> = [];
      var wds_event_stack_<?php echo $wds; ?> = [];
      var wds_clear_layers_effects_in_<?php echo $wds; ?> = [];
      var wds_clear_layers_effects_out_<?php echo $wds; ?> = [];
      var wds_clear_layers_effects_out_before_change_<?php echo $wds; ?> = [];
      if (<?php echo $slider_row->layer_out_next; ?>) {
        var wds_duration_for_change_<?php echo $wds; ?> = 500;
        var wds_duration_for_clear_effects_<?php echo $wds; ?> = 530;
      }
      else {
        var wds_duration_for_change_<?php echo $wds; ?> = 0;
        var wds_duration_for_clear_effects_<?php echo $wds; ?> = 0;
      }
      <?php
      foreach ($slide_rows as $key => $slide_row) {
        ?>
        wds_clear_layers_effects_in_<?php echo $wds; ?>["<?php echo $key; ?>"] = [];
        wds_clear_layers_effects_out_<?php echo $wds; ?>["<?php echo $key; ?>"] = [];
        wds_clear_layers_effects_out_before_change_<?php echo $wds; ?>["<?php echo $key; ?>"] = [];
        wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"] = [];
        wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["id"] = "<?php echo $slide_row->id; ?>";
        wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["image_url"] = "<?php echo addslashes(htmlspecialchars_decode ($slide_row->image_url,ENT_QUOTES)); ?>";
        wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["thumb_url"] = "<?php echo addslashes(htmlspecialchars_decode ($slide_row->thumb_url,ENT_QUOTES)); ?>";
        wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["is_video"] = "<?php echo $slide_row->type == "YOUTUBE" || $slide_row->type == "VIMEO"; ?>";
        wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["slide_layers_count"] = 0;
        <?php
        $layers_row = $this->model->get_layers_row_data($slide_row->id);
        if ($layers_row) {
          foreach ($layers_row as $layer_key => $layer) {
            ?>
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_id"] = "<?php echo $layer->id; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_layer_effect_in"] = "<?php echo $layer->layer_effect_in; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_duration_eff_in"] = "<?php echo $layer->duration_eff_in; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_layer_effect_out"] = "<?php echo $layer->layer_effect_out; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_duration_eff_out"] = "<?php echo $layer->duration_eff_out; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_social_button"] = "<?php echo $layer->social_button; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_start"] = "<?php echo $layer->start; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_end"] = "<?php echo $layer->end; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_type"] = "<?php echo $layer->type; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["layer_<?php echo $layer_key; ?>_video_autoplay"] = "<?php echo $layer->image_scale; ?>";
            wds_data_<?php echo $wds; ?>["<?php echo $key; ?>"]["slide_layers_count"] ++;
            <?php
          }
        }		
      }
      ?>    
    </script>
    <div id="wds_container1_<?php echo $wds; ?>">
      <div class="wds_loading">
        <img src="<?php echo WD_S_URL . '/images/ajax_loader.png'; ?>" class="wds_loading_img" style="float: none; width:50px;" />
      </div>
      <div id="wds_container2_<?php echo $wds; ?>">
        <div class="wds_slideshow_image_wrap_<?php echo $wds; ?>">
          <?php
          $current_pos = 0;
          if ($filmstrip_position != 'none' && $slides_count > 1) {
            ?>
          <div class="wds_slideshow_filmstrip_container_<?php echo $wds; ?>">
            <div class="wds_slideshow_filmstrip_left_<?php echo $wds; ?>"><i class="fa <?php echo ($filmstrip_direction == 'horizontal'? 'fa-angle-left' : 'fa-angle-up'); ?>"></i></div>
            <div class="wds_slideshow_filmstrip_<?php echo $wds; ?>">
              <div class="wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>">
                <?php
                foreach ($slide_rows as $key => $slide_row) {
                  if ($slide_row->id == $current_image_id) {
                    $current_pos = $key * (($filmstrip_direction == 'horizontal' ? $filmstrip_width : $filmstrip_height) + $filmstrip_thumb_margin_hor);
                    $current_key = $key;
                  }
                ?>
                <div id="wds_filmstrip_thumbnail_<?php echo $key; ?>_<?php echo $wds; ?>" class="wds_slideshow_filmstrip_thumbnail_<?php echo $wds; ?> <?php echo (($slide_row->id == $current_image_id) ? 'wds_slideshow_thumb_active_' . $wds : 'wds_slideshow_thumb_deactive_' . $wds); ?>">
                  <div onclick="wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), '<?php echo $key; ?>', wds_data_<?php echo $wds; ?>);
                              <?php
                              if ($carousel) {
                                ?>
                                wds_carousel<?php echo $wds; ?>.shift(jQuery('.wds_slider_car_image<?php echo $wds; ?>[image_id=<?php echo $slide_row->id; ?>]'));
                                <?php
                              }
                              ?>"
                       image_id="<?php echo $slide_row->id; ?>"
                       image_key="<?php echo $key; ?>"
                       class="wds_slideshow_filmstrip_thumbnail_img_<?php echo $wds; ?>"
                       style="background-image: url('<?php echo addslashes(htmlspecialchars_decode ($slide_row->thumb_url,ENT_QUOTES)); ?>');"></div>
                </div>
                <?php
                }
                ?>
              </div>
            </div>
            <div class="wds_slideshow_filmstrip_right_<?php echo $wds; ?>"><i class="fa <?php echo ($filmstrip_direction == 'horizontal'? 'fa-angle-right' : 'fa-angle-down'); ?>"></i></div>
          </div>
            <?php
          }
          ?>
          <div id="wds_slideshow_image_container_<?php echo $wds; ?>" class="wds_slideshow_image_container_<?php echo $wds; ?>">
            <?php
              if ($bull_position != 'none' && $slides_count > 1) {
                ?>
              <div class="wds_slideshow_dots_container_<?php echo $wds; ?>">
                <div class="wds_slideshow_dots_thumbnails_<?php echo $wds; ?>">
                  <?php
                  foreach ($slide_rows as $key => $slide_row) {
                    if ($slider_row->bull_butt_img_or_not == 'style') {
                      ?>
                  <i id="wds_dots_<?php echo $key; ?>_<?php echo $wds; ?>"
                     class="wds_slideshow_dots_<?php echo $wds; ?> fa <?php echo (($slide_row->id == $current_image_id) ? $bull_style_active . ' wds_slideshow_dots_active_' . $wds : $bull_style_deactive . ' wds_slideshow_dots_deactive_' . $wds); ?>"
                     onclick="wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), '<?php echo $key; ?>', wds_data_<?php echo $wds; ?>);
                              <?php
                              if ($carousel) {
                                ?>
                                wds_carousel<?php echo $wds; ?>.shift(jQuery('.wds_slider_car_image<?php echo $wds; ?>[image_id=<?php echo $slide_row->id; ?>]'));
                                <?php
                              }
                              ?>"
                     image_id="<?php echo $slide_row->id; ?>"
                     image_key="<?php echo $key; ?>">
                  </i>
                      <?php
                    }
                    else {
                      ?>
                  <span id="wds_dots_<?php echo $key; ?>_<?php echo $wds; ?>"
                        class="wds_slideshow_dots_<?php echo $wds; ?> <?php echo (($slide_row->id == $current_image_id) ? ' wds_slideshow_dots_active_' . $wds : ' wds_slideshow_dots_deactive_' . $wds); ?>"
                        onclick="wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), '<?php echo $key; ?>', wds_data_<?php echo $wds; ?>);
                              <?php
                              if ($carousel) {
                                ?>
                                wds_carousel<?php echo $wds; ?>.shift(jQuery('.wds_slider_car_image<?php echo $wds; ?>[image_id=<?php echo $slide_row->id; ?>]'));
                                <?php
                              }
                              ?>"
                        image_id="<?php echo $slide_row->id; ?>"
                        image_key="<?php echo $key; ?>">
                  </span>
                      <?php
                    }
                  }
                  ?>
                </div>
              </div>
                <?php
              }
              if ($slider_row->timer_bar_type == 'top' ||  $slider_row->timer_bar_type == 'bottom') {
                ?>
                <div class="wds_line_timer_container_<?php echo $wds; ?>"><div class="wds_line_timer_<?php echo $wds; ?>"></div></div>			
                <?php
              }
              elseif ($slider_row->timer_bar_type != 'none') {
                ?>
                <div class="wds_circle_timer_container_<?php echo $wds; ?>">
                  <div class="wds_circle_timer_<?php echo $wds; ?>">
                    <div class="wds_circle_timer_parts_<?php echo $wds; ?>">
                      <div class="wds_circle_timer_part_<?php echo $wds; ?>">
                        <div class="wds_circle_timer_small_parts_<?php echo $wds; ?> animated" style="border-radius:100% 0% 0% 0%;" id="top_left_<?php echo $wds; ?>"></div>
                        <div class="wds_circle_timer_small_parts_<?php echo $wds; ?> animated" style="border-radius:0% 0% 0% 100%;z-index:150;" id="bottom_left_<?php echo $wds; ?>"></div>
                      </div>
                      <div class="wds_circle_timer_part_<?php echo $wds; ?>">
                        <div class="wds_circle_timer_small_parts_<?php echo $wds; ?> animated" style="border-radius:0% 100% 0% 0%;" id="top_right_<?php echo $wds; ?>"></div>
                        <div class="wds_circle_timer_small_parts_<?php echo $wds; ?> animated" style="border-radius:0% 0% 100% 0%;" id="bottom_right_<?php echo $wds; ?>"></div>
                      </div>
                    </div>
                    <div class="wds_circle_timer_center_cont_<?php echo $wds; ?>">
                       <div class="wds_circle_timer_center_<?php echo $wds; ?>">
                        <div></div>
                       </div> 
                    </div>					
                  </div>
                </div>
                <?php
              }
            
            ?>			
            <div class="wds_slide_container_<?php echo $wds; ?>" id="wds_slide_container_<?php echo $wds; ?>">
              <div class="wds_slide_bg_<?php echo $wds; ?>">
                <div class="wds_slider_<?php echo $wds; ?>">
                <?php
                foreach ($slide_rows as $key => $slide_row) {
                  $is_video = $slide_row->type == "YOUTUBE" || $slide_row->type == "VIMEO";
                  if ($slide_row->id == $current_image_id) {
                    if ($is_video) {
                      $play_pause_button_display = 'none';
                    }
                    else {
                      $play_pause_button_display = '';
                    }
                    $current_key = $key;
                    $image_div_num = '';
                  }
                  else {
                    $image_div_num = '_second';
                  }
                  $share_image_url = urlencode($is_video ? $slide_row->thumb_url : $slide_row->image_url);
                  $share_url = add_query_arg(array('action' => 'WDSShare', 'image_id' => $slide_row->id, 'curr_url' => $current_url), admin_url('admin-ajax.php'));
                  ?>
                  <span 
                  <?php
                  if ($carousel) {
                    ?>
                    onclick="wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), '<?php echo $key; ?>', wds_data_<?php echo $wds; ?>); wds_carousel<?php echo $wds; ?>.shift(this);"
                    <?php
                  }
                  ?>
                    class="wds_slider_car_image<?php echo $wds; ?> wds_slideshow_image<?php echo $image_div_num; ?>_spun_<?php echo $wds; ?>" id="wds_image_id_<?php echo $wds; ?>_<?php echo $slide_row->id; ?>"
                    image_id="<?php echo $slide_row->id; ?>">
                    <span class="wds_slideshow_image_spun1_<?php echo $wds; ?>">
                      <span class="wds_slideshow_image_spun2_<?php echo $wds; ?>">
                        <?php 
                        if (!$is_video) {
                          ?>
                        <div img_id="wds_slideshow_image<?php echo $image_div_num; ?>_<?php echo $wds; ?>"
                             class="wds_slideshow_image_<?php echo $wds; ?>"
                             onclick="<?php echo $slide_row->link ? 'window.open(\'' . $slide_row->link . '\', \'' . ($slide_row->target_attr_slide ? '_blank' : '_self') . '\')' : ''; ?>"
                             style="<?php echo $slide_row->link ? 'cursor: pointer;' : ''; ?><?php echo ((!$preload_images || $image_div_num == '') ? "background-image: url('" . addslashes(htmlspecialchars_decode ($slide_row->image_url,ENT_QUOTES)) . "');" : ""); ?>"
                             image_id="<?php echo $slide_row->id; ?>">
                          <?php
                        }
                        else {
                          ?>
                        <div img_id="wds_slideshow_image<?php echo $image_div_num; ?>_<?php echo $wds; ?>" class="wds_slideshow_video_<?php echo $wds; ?>" image_id="<?php echo $slide_row->id; ?>">
                          <?php if ($carousel) { ?>
                          <span class="wds_video_hide<?php echo $wds; ?>"></span>
                          <?php } ?>
                          <iframe class="wds_video_frame_<?php echo $wds; ?>" src="<?php echo ($slide_row->type == "YOUTUBE" ? "//www.youtube.com/embed/" . $slide_row->image_url . "?enablejsapi=1&wmode=transparent" : "//player.vimeo.com/video/" . $slide_row->image_url . "?api=1"); ?>" frameborder="0" allowfullscreen style="width:100%; height:100%;"></iframe>
                          <?php
                        }
                        $layers_row = $this->model->get_layers_row_data($slide_row->id);
                        if ($layers_row) {
                          foreach ($layers_row as $key => $layer) {
                            if ($layer->published) {
                              $prefix = 'wds_' . $wds . '_slide' . $slide_row->id . '_layer' . $layer->id;
                              $left_percent = $slider_row->width ? 100 * $layer->left / $slider_row->width : 0;
                              $top_percent = $slider_row->height ? 100 * $layer->top / $slider_row->height : 0;
                              $video_width_percent = $slider_row->width ? 100 * $layer->image_width / $slider_row->width : 0;
                              $video_height_percent = $slider_row->height ? 100 * $layer->image_height / $slider_row->height : 0;
                              switch ($layer->type) {
                                case 'text': {
                                  ?>
                                  <style>
                                    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #<?php echo $prefix; ?> {
                                      font-size: <?php echo $layer->size; ?>px;
                                      line-height: 1.25em;
                                      padding: <?php echo $layer->padding; ?>;
                                    }
                                    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_layer_<?php echo $layer->id; ?>{
                                      opacity: <?php echo ($layer->layer_effect_in != 'none') ? '0 !important' : '1'; ?>;
                                      filter: "Alpha(opacity=<?php echo ($layer->layer_effect_in != 'none') ? '0' : '100'; ?>)" !important;
                                    }
                                  </style>
                                <span class="wds_layer_<?php echo $layer->id; ?>" id="<?php echo $prefix; ?>" wds_fsize="<?php echo $layer->size; ?>"
                                      style="<?php echo $layer->image_width ? 'width: ' . $layer->image_width . '%; ' : ''; ?>
                                             <?php echo $layer->image_height ? 'height: ' . $layer->image_height . '%; ' : ''; ?>
                                             word-break: <?php echo ($layer->image_scale ? 'keep-all' : 'break-all'); ?>;
                                             text-align: initial; <?php echo $layer->link ? 'cursor: pointer; ' : ''; ?>
                                             opacity: 1;
                                             filter: 'Alpha(opacity=100)';
                                             display: inline-block;
                                             position: absolute;
                                             left: <?php echo $left_percent; ?>%;
                                             top: <?php echo $top_percent; ?>%;
                                             z-index: <?php echo $layer->depth; ?>;
                                             color: #<?php echo $layer->color; ?>;
                                             font-family: <?php echo $layer->ffamily; ?>;
                                             font-weight: <?php echo $layer->fweight; ?>;
                                             background-color: <?php echo WDW_S_Library::spider_hex2rgba($layer->fbgcolor, (100 - $layer->transparent) / 100); ?>;
                                             border: <?php echo $layer->border_width; ?>px <?php echo $layer->border_style; ?> #<?php echo $layer->border_color; ?>;
                                             border-radius: <?php echo $layer->border_radius; ?>;
                                             box-shadow: <?php echo $layer->shadow; ?>"
                                      onclick="<?php echo $layer->link ? 'window.open(\'' . $layer->link . '\', \'' . ($layer->target_attr_layer ? '_blank' : '_self') . '\');' : ''; ?>event.stopPropagation();"><?php echo str_replace(array("\r\n", "\r", "\n"), "<br>", $layer->text); ?></span>
                                  <?php
                                  break;
                                }
                                case 'image': {
                                  ?>
                                  <style>
                                    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_layer_<?php echo $layer->id; ?>{
                                      opacity: <?php echo ($layer->layer_effect_in != 'none') ? '0 !important' : '1'; ?>;
                                      filter: "Alpha(opacity=<?php echo ($layer->layer_effect_in != 'none') ? '0' : '100'; ?>)" !important;
                                    }
                                  </style>
                                <img class="wds_layer_<?php echo $layer->id; ?>" id="<?php echo $prefix; ?>" src="<?php echo $layer->image_url; ?>"
                                     style="<?php echo $layer->link ? 'cursor: pointer; ' : ''; ?>
                                            opacity: <?php echo number_format((100 - $layer->imgtransparent) / 100, 2, ".", ""); ?>;
                                            filter: Alpha(opacity=<?php echo 100 - $layer->imgtransparent; ?>);
                                            position: absolute;
                                            left: <?php echo $left_percent; ?>%;
                                            top: <?php echo $top_percent; ?>%;
                                            z-index: <?php echo $layer->depth; ?>;
                                            border: <?php echo $layer->border_width; ?>px <?php echo $layer->border_style; ?> #<?php echo $layer->border_color; ?>;
                                            border-radius: <?php echo $layer->border_radius; ?>;
                                            box-shadow: <?php echo $layer->shadow; ?>"
                                     onclick="<?php echo $layer->link ? 'window.open(\'' . $layer->link . '\', \'' . ($layer->target_attr_layer ? '_blank' : '_self') . '\');' : ''; ?>event.stopPropagation();"
                                     wds_scale="<?php echo $layer->image_scale; ?>"
                                     wds_image_width="<?php echo $layer->image_width; ?>"
                                     wds_image_height="<?php echo $layer->image_height; ?>"
                                     alt="<?php echo $layer->alt; ?>" 
                                     title="<?php echo $layer->alt; ?>" />
                                  <?php
                                  break;
                                }
                                case 'video': {
                                  ?>
                                  <style>
                                    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_layer_<?php echo $layer->id; ?> {
                                      opacity: <?php echo ($layer->layer_effect_in != 'none') ? '0 !important' : '1'; ?>;
                                      filter: "Alpha(opacity=<?php echo ($layer->layer_effect_in != 'none') ? '0' : '100'; ?>)" !important;
                                    }
                                  </style>
                                <span class="wds_layer_<?php echo $layer->id; ?>" id="<?php echo $prefix; ?>" wds_fsize="<?php echo $layer->size; ?>"
                                      style="<?php echo $layer->image_width ? 'width: ' . $video_width_percent . '%; ' : ''; ?>
                                             <?php echo $layer->image_height ? 'height: ' . $video_height_percent . '%; ' : ''; ?>
                                             position: absolute;
                                             overflow: hidden;
                                             left: <?php echo $left_percent; ?>%;
                                             top: <?php echo $top_percent; ?>%;
                                             z-index: <?php echo $layer->depth; ?>;
                                             border: <?php echo $layer->border_width; ?>px <?php echo $layer->border_style; ?> #<?php echo $layer->border_color; ?>;
                                             border-radius: <?php echo $layer->border_radius; ?>;
                                             box-shadow: <?php echo $layer->shadow; ?>">
                                  <iframe class="wds_video_layer_frame_<?php echo $wds; ?>" src="<?php echo ($layer->alt == "YOUTUBE" ? "//www.youtube.com/embed/" . $layer->link . "?enablejsapi=1&wmode=transparent" : "//player.vimeo.com/video/" . $layer->link . "?api=1"); ?>" frameborder="0" allowfullscreen></iframe>
                                </span>
                                  <?php
                                  break;
                                }
                                case 'social': {
                                  ?>
                                  <style>
                                    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #<?php echo $prefix; ?> {
                                      font-size: <?php echo $layer->size; ?>px;
                                      padding: <?php echo $layer->padding; ?>;
                                    }
                                    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> .wds_layer_<?php echo $layer->id; ?> {
                                      opacity: <?php echo ($layer->layer_effect_in != 'none') ? '0 !important' : '1'; ?>;
                                      filter: "Alpha(opacity=<?php echo ($layer->layer_effect_in != 'none') ? '0' : '100'; ?>)" !important;
                                    }
                                    #wds_container1_<?php echo $wds; ?> #wds_container2_<?php echo $wds; ?> #<?php echo $prefix; ?>:hover {
                                      color: #<?php echo $layer->hover_color; ?> !important;
                                    }
                                  </style>
                                  <?php
                                  switch ($layer->social_button) {
                                    case 'facebook': {
                                      ?>
                                  <a class="wds_share_a" onclick="event.stopPropagation();" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>" target="_blank" title="<?php echo __('Share on Facebook', 'wds'); ?>">
                                      <?php
                                      break;
                                    }
                                    case 'twitter': {
                                      ?>
                                  <a class="wds_share_a" onclick="event.stopPropagation();" href="https://twitter.com/share?url=<?php echo urlencode($share_url); ?>" target="_blank" title="<?php echo __('Share on Twitter', 'wds'); ?>">
                                      <?php
                                      break;
                                    }
                                    case 'google-plus': {
                                      ?>
                                  <a class="wds_share_a" onclick="event.stopPropagation();" href="https://plus.google.com/share?url=<?php echo urlencode($share_url); ?>" target="_blank" title="<?php echo __('Share on Google+', 'wds'); ?>">
                                      <?php
                                      break;
                                    }
                                    case 'pinterest': {
                                      ?>
                                  <a class="wds_share_a" onclick="event.stopPropagation();" href="http://pinterest.com/pin/create/button/?s=100&url=<?php echo urlencode($share_url); ?>&media=<?php echo $share_image_url; ?>&description=<?php echo $slide_row->title; ?>" target="_blank" title="<?php echo __('Share on Pinterest', 'wds'); ?>">
                                      <?php
                                      break;
                                    }
                                    case 'tumblr': {
                                      ?>
                                  <a class="wds_share_a" onclick="event.stopPropagation();" href="https://www.tumblr.com/share/photo?source=<?php echo $share_image_url; ?>&caption=<?php echo urlencode($slide_row->title); ?>&clickthru=<?php echo urlencode($share_url); ?>" target="_blank" title="<?php echo __('Share on Tumblr', 'wds'); ?>">
                                      <?php
                                      break;
                                    }
                                    default: {
                                      ?><a><?php
                                      break;
                                    }
                                  }
                                  ?>
                                    <i id="<?php echo $prefix; ?>" class="wds_layer_<?php echo $layer->id; ?> fa fa-<?php echo $layer->social_button; ?>" wds_fsize="<?php echo $layer->size; ?>"
                                       style="opacity: <?php echo number_format((100 - $layer->transparent) / 100, 2, ".", ""); ?>;
                                              filter: Alpha(opacity=<?php echo 100 - $layer->transparent; ?>);
                                              position: absolute;
                                              left: <?php echo $left_percent; ?>%;
                                              top: <?php echo $top_percent; ?>%;
                                              z-index: <?php echo $layer->depth; ?>;
                                              color: #<?php echo $layer->color; ?>;"></i>
                                  </a>
                                  <?php
                                  break;
                                }
                                default:
                                  break;
                              }
                            }
                          }
                        }
                        ?>
                        </div>
                      </span>
                    </span>
                  </span>
                  <?php
                }
                ?>
                <input type="hidden" id="wds_current_image_key_<?php echo $wds; ?>" value="<?php echo $current_key; ?>" />
                </div>
              </div>
            </div>
            <?php
              if ($enable_prev_next_butt && $slides_count > 1) {
                ?>
              <div class="wds_btn_cont wds_contTableCell">
                <div class="wds_btn_cont wds_contTable">
                  <span class="wds_btn_cont wds_contTableCell" style="position: relative; text-align: left;">
                    <span class="wds_left_btn_cont">
                      <span class="wds_left-ico_<?php echo $wds; ?>" onclick="wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) - iterator_<?php echo $wds; ?>()) >= 0 ? (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) - iterator_<?php echo $wds; ?>()) % wds_data_<?php echo $wds; ?>.length : wds_data_<?php echo $wds; ?>.length - 1, wds_data_<?php echo $wds; ?>); return false;">
                        <?php
                        if ($slider_row->rl_butt_img_or_not == 'style') {
                          ?>
                          <i class="fa <?php echo $slider_row->rl_butt_style; ?>-left"></i>
                          <?php
                        }
                        ?>
                      </span>
                    </span>
                   </span>
                </div>
              </div>
              <div class="wds_btn_cont wds_contTableCell">
                <div class="wds_btn_cont wds_contTable">
                  <span class="wds_btn_cont wds_contTableCell" style="position: relative; text-align: right;">
                    <span class="wds_right_btn_cont">
                      <span class="wds_right-ico_<?php echo $wds; ?>" onclick="wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) + iterator_<?php echo $wds; ?>()) % wds_data_<?php echo $wds; ?>.length, wds_data_<?php echo $wds; ?>); return false;">
                        <?php
                        if ($slider_row->rl_butt_img_or_not == 'style') {
                          ?>
                          <i class="fa <?php echo $slider_row->rl_butt_style; ?>-right"></i>
                          <?php
                        }
                        ?>
                      </span>
                    </span>
                  </span>
                </div>
              </div>
              <?php
              }
              if ($enable_play_paus_butt && $slides_count > 1) {
                ?>
              <div class="wds_btn_cont wds_contTableCell">
                <div class="wds_btn_cont wds_contTable">
                  <span class="wds_btn_cont wds_contTableCell" style="position: relative; text-align: center;">
                    <span class="wds_pp_btn_cont">
                      <span id="wds_slideshow_play_pause_<?php echo $wds; ?>" style="display: <?php echo $play_pause_button_display; ?>;" <?php echo ($slider_row->play_paus_butt_img_or_not != 'style') ? 'class="wds_ctrl_btn_' . $wds . ' wds_slideshow_play_pause_' . $wds . ' fa fa-play"' : ''; ?>>
                        <?php
                        if ($slider_row->play_paus_butt_img_or_not == 'style') {
                          ?>
                        <i class="wds_ctrl_btn_<?php echo $wds; ?> wds_slideshow_play_pause_<?php echo $wds; ?> fa fa-play"></i>
                          <?php
                        }
                        ?>
                      </span>
                    </span>
                  </span>
                </div>
              </div>
              <?php
              }
            ?>
          </div>
          <?php
          if ($enable_slideshow_music) {
            ?>
            <audio id="wds_audio_<?php echo $wds; ?>" src="<?php echo $slideshow_music_url; ?>" loop volume="1.0"></audio>
            <?php 
          }
          ?>
        </div>
      </div>
    </div>

    <script>
      var wds_trans_in_progress_<?php echo $wds; ?> = false;
      var wds_transition_duration_<?php echo $wds; ?> = <?php echo $slider_row->effect_duration; ?>;
      if (<?php echo $slideshow_interval; ?> < 4) {
        if (<?php echo $slideshow_interval; ?> != 0) {
          wds_transition_duration_<?php echo $wds; ?> = (<?php echo $slideshow_interval; ?> * 1000) / 4;
        }
      }
      var wds_playInterval_<?php echo $wds; ?>;
      var progress = 0;
      var bottom_right_deggree_<?php echo $wds; ?>;
      var bottom_left_deggree_<?php echo $wds; ?>;
      var top_left_deggree_<?php echo $wds; ?>;
      var curent_time_deggree_<?php echo $wds; ?> = 0;
      var circle_timer_animate_<?php echo $wds; ?>;
      function circle_timer_<?php echo $wds; ?>(angle) {
        circle_timer_animate_<?php echo $wds; ?> = jQuery({deg: angle}).animate({deg: 360}, {
          duration: <?php echo $slideshow_interval * 1000; ?>,
          step: function(now) {
            curent_time_deggree_<?php echo $wds; ?> = now;
            if (now >= 0) {
              if (now < 271) {
                jQuery('#top_right_<?php echo $wds; ?>').css({
                  '-moz-transform':'rotate('+now+'deg)',
                  '-webkit-transform':'rotate('+now+'deg)',
                  '-o-transform':'rotate('+now+'deg)',
                  '-ms-transform':'rotate('+now+'deg)',
                  'transform':'rotate('+now+'deg)',

                  '-webkit-transform-origin': 'left bottom',
                  '-ms-transform-origin': 'left bottom',
                  '-moz-transform-origin': 'left bottom',
                  'transform-origin': 'left bottom'
                });
              }
            }
            if (now >= 90) {
              if (now < 271) {
                bottom_right_deggree_<?php echo $wds; ?> = now - 90;
                jQuery('#bottom_right_<?php echo $wds; ?>').css({
                  '-moz-transform':'rotate('+bottom_right_deggree_<?php echo $wds; ?> +'deg)',
                '-webkit-transform':'rotate('+bottom_right_deggree_<?php echo $wds; ?> +'deg)',
                '-o-transform':'rotate('+bottom_right_deggree_<?php echo $wds; ?> +'deg)',
                '-ms-transform':'rotate('+bottom_right_deggree_<?php echo $wds; ?> +'deg)',
                'transform':'rotate('+bottom_right_deggree_<?php echo $wds; ?> +'deg)',

                '-webkit-transform-origin': 'left top',
                '-ms-transform-origin': 'left top',
                '-moz-transform-origin': 'left top',
                'transform-origin': 'left top'
                });
              }
            }
            if (now >= 180) {
              if (now < 361) {
                bottom_left_deggree_<?php echo $wds; ?> = now - 180;
                jQuery('#bottom_left_<?php echo $wds; ?>').css({
                  '-moz-transform':'rotate('+bottom_left_deggree_<?php echo $wds; ?> +'deg)',
                  '-webkit-transform':'rotate('+bottom_left_deggree_<?php echo $wds; ?> +'deg)',
                  '-o-transform':'rotate('+bottom_left_deggree_<?php echo $wds; ?> +'deg)',
                  '-ms-transform':'rotate('+bottom_left_deggree_<?php echo $wds; ?> +'deg)',
                  'transform':'rotate('+bottom_left_deggree_<?php echo $wds; ?> +'deg)',

                  '-webkit-transform-origin': 'right top',
                  '-ms-transform-origin': 'right top',
                  '-moz-transform-origin': 'right top',
                  'transform-origin': 'right top'
                });
              }
            }
            if (now >= 270) {
              if (now < 361) {
                top_left_deggree_<?php echo $wds; ?>  = now - 270;
                jQuery('#top_left_<?php echo $wds; ?>').css({
                  '-moz-transform':'rotate('+top_left_deggree_<?php echo $wds; ?> +'deg)',
                  '-webkit-transform':'rotate('+top_left_deggree_<?php echo $wds; ?> +'deg)',
                  '-o-transform':'rotate('+top_left_deggree_<?php echo $wds; ?> +'deg)',
                  '-ms-transform':'rotate('+top_left_deggree_<?php echo $wds; ?> +'deg)',
                  'transform':'rotate('+top_left_deggree_<?php echo $wds; ?> +'deg)',

                  '-webkit-transform-origin': 'right bottom',
                  '-ms-transform-origin': 'right bottom',
                  '-moz-transform-origin': 'right bottom',
                  'transform-origin': 'right bottom'
                });
              }
            }
          }
        });
      }
      /* Stop autoplay.*/
      window.clearInterval(wds_playInterval_<?php echo $wds; ?>);
      var wds_current_key_<?php echo $wds; ?> = '<?php echo (isset($current_key) ? $current_key : ''); ?>';
      var wds_current_filmstrip_pos_<?php echo $wds; ?> = <?php echo $current_pos; ?>;
      /* Set filmstrip initial position.*/
      function wds_set_filmstrip_pos_<?php echo $wds; ?>(filmStripWidth) {
        var selectedImagePos = -wds_current_filmstrip_pos_<?php echo $wds; ?> - (jQuery(".wds_slideshow_filmstrip_thumbnail_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() + <?php echo $filmstrip_thumb_margin_hor; ?>) / 2;
        var imagesContainerLeft = Math.min(0, Math.max(filmStripWidth - jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").<?php echo $width_or_height; ?>(), selectedImagePos + filmStripWidth / 2));
        jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").animate({
            <?php echo $left_or_top; ?>: imagesContainerLeft
          }, {
            duration: 500,
            complete: function () { wds_filmstrip_arrows_<?php echo $wds; ?>(); }
          });
      }
      function wds_move_filmstrip_<?php echo $wds; ?>() {
        var image_left = jQuery(".wds_slideshow_thumb_active_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?>;
        var image_right = jQuery(".wds_slideshow_thumb_active_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> + jQuery(".wds_slideshow_thumb_active_<?php echo $wds; ?>").<?php echo $outerWidth_or_outerHeight; ?>(true);
        var wds_filmstrip_width = jQuery(".wds_slideshow_filmstrip_<?php echo $wds; ?>").<?php echo $outerWidth_or_outerHeight; ?>(true);
        var wds_filmstrip_thumbnails_width = jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").<?php echo $outerWidth_or_outerHeight; ?>(true);
        var long_filmstrip_cont_left = jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?>;
        var long_filmstrip_cont_right = Math.abs(jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?>) + wds_filmstrip_width;
        if (wds_filmstrip_width > wds_filmstrip_thumbnails_width) {
          return;
        }
        if (image_left < Math.abs(long_filmstrip_cont_left)) {
          jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").animate({
            <?php echo $left_or_top; ?>: -image_left
          }, {
            duration: 500,
            complete: function () { wds_filmstrip_arrows_<?php echo $wds; ?>(); }
          });
        }
        else if (image_right > long_filmstrip_cont_right) {
          jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").animate({
            <?php echo $left_or_top; ?>: -(image_right - wds_filmstrip_width)
          }, {
            duration: 500,
            complete: function () { wds_filmstrip_arrows_<?php echo $wds; ?>(); }
          });
        }
      }
      function wds_move_dots_<?php echo $wds; ?>() {
        var image_left = jQuery(".wds_slideshow_dots_active_<?php echo $wds; ?>").position().left;
        var image_right = jQuery(".wds_slideshow_dots_active_<?php echo $wds; ?>").position().left + jQuery(".wds_slideshow_dots_active_<?php echo $wds; ?>").outerWidth(true);
        var wds_dots_width = jQuery(".wds_slideshow_dots_container_<?php echo $wds; ?>").outerWidth(true);
        var wds_dots_thumbnails_width = jQuery(".wds_slideshow_dots_thumbnails_<?php echo $wds; ?>").outerWidth(true);
        var long_filmstrip_cont_left = jQuery(".wds_slideshow_dots_thumbnails_<?php echo $wds; ?>").position().left;
        var long_filmstrip_cont_right = Math.abs(jQuery(".wds_slideshow_dots_thumbnails_<?php echo $wds; ?>").position().left) + wds_dots_width;
        if (wds_dots_width > wds_dots_thumbnails_width) {
          return;
        }
        if (image_left < Math.abs(long_filmstrip_cont_left)) {
          jQuery(".wds_slideshow_dots_thumbnails_<?php echo $wds; ?>").animate({
            left: -image_left
          }, {
            duration: 500
          });
        }
        else if (image_right > long_filmstrip_cont_right) {
          jQuery(".wds_slideshow_dots_thumbnails_<?php echo $wds; ?>").animate({
            left: -(image_right - wds_dots_width)
          }, {
            duration: 500
          });
        }
      }
      /* Show/hide filmstrip arrows.*/
      function wds_filmstrip_arrows_<?php echo $wds; ?>() {
        if (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() < jQuery(".wds_slideshow_filmstrip_<?php echo $wds; ?>").<?php echo $width_or_height; ?>()) {
          jQuery(".wds_slideshow_filmstrip_left_<?php echo $wds; ?>").hide();
          jQuery(".wds_slideshow_filmstrip_right_<?php echo $wds; ?>").hide();
        }
        else {
          jQuery(".wds_slideshow_filmstrip_left_<?php echo $wds; ?>").show();
          jQuery(".wds_slideshow_filmstrip_right_<?php echo $wds; ?>").show();
        }
      }
      function wds_testBrowser_cssTransitions_<?php echo $wds; ?>() {
        return wds_testDom_<?php echo $wds; ?>('Transition');
      }
      function wds_testBrowser_cssTransforms3d_<?php echo $wds; ?>() {
        return wds_testDom_<?php echo $wds; ?>('Perspective');
      }
      function wds_testDom_<?php echo $wds; ?>(prop) {
        /* Browser vendor CSS prefixes.*/
        var browserVendors = ['', '-webkit-', '-moz-', '-ms-', '-o-', '-khtml-'];
        /* Browser vendor DOM prefixes.*/
        var domPrefixes = ['', 'Webkit', 'Moz', 'ms', 'O', 'Khtml'];
        var i = domPrefixes.length;
        while (i--) {
          if (typeof document.body.style[domPrefixes[i] + prop] !== 'undefined') {
            return true;
          }
        }
        return false;
      }
      function wds_set_dots_class_<?php echo $wds; ?>() {
        jQuery(".wds_slideshow_dots_<?php echo $wds; ?>").removeClass("wds_slideshow_dots_active_<?php echo $wds; ?>").addClass("wds_slideshow_dots_deactive_<?php echo $wds; ?>");
        jQuery("#wds_dots_" + wds_current_key_<?php echo $wds; ?> + "_<?php echo $wds; ?>").removeClass("wds_slideshow_dots_deactive_<?php echo $wds; ?>").addClass("wds_slideshow_dots_active_<?php echo $wds; ?>");
        <?php if ($slider_row->bull_butt_img_or_not == 'style') { ?>
        jQuery(".wds_slideshow_dots_<?php echo $wds; ?>").removeClass("<?php echo $bull_style_active; ?>").addClass("<?php echo $bull_style_deactive; ?>");
        jQuery("#wds_dots_" + wds_current_key_<?php echo $wds; ?> + "_<?php echo $wds; ?>").removeClass("<?php echo $bull_style_deactive; ?>").addClass("<?php echo $bull_style_active; ?>");
        <?php } ?>
      }
      function wds_set_filmstrip_class_<?php echo $wds; ?>() {
        jQuery('.wds_slideshow_filmstrip_thumbnail_<?php echo $wds; ?>').removeClass('wds_slideshow_thumb_active_<?php echo $wds; ?>').addClass('wds_slideshow_thumb_deactive_<?php echo $wds; ?>');
        jQuery('#wds_filmstrip_thumbnail_' + wds_current_key_<?php echo $wds; ?> + '_<?php echo $wds; ?>').removeClass('wds_slideshow_thumb_deactive_<?php echo $wds; ?>').addClass('wds_slideshow_thumb_active_<?php echo $wds; ?>');
      }
      function wds_grid3d_<?php echo $wds; ?>(cols, rows, tz, wrx, wry, nty, ntx, nry, nrx, current_image_class, next_image_class, direction, random, easing) {
        /* If browser does not support CSS transitions.*/
        if (!wds_testBrowser_cssTransitions_<?php echo $wds; ?>()) {
          return wds_fallback_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
        }
        wds_trans_in_progress_<?php echo $wds; ?> = true;
        /* Set active thumbnail.*/
        wds_set_filmstrip_class_<?php echo $wds; ?>();
        wds_set_dots_class_<?php echo $wds; ?>();

        /* The time (in ms) added to/subtracted from the delay total for each new gridlet.*/
        var count = (wds_transition_duration_<?php echo $wds; ?>) / (cols + rows);
        /* Gridlet creator (divisions of the image grid, positioned with background-images to replicate the look of an entire slide image when assembled)*/
        function wds_gridlet(width, height, top, img_top, left, img_left, src, src2, imgWidth, imgHeight, c, r) {
          var delay = random ? Math.floor((cols + rows) * count * Math.random()) : (c + r) * count;

          /* Return a gridlet elem with styles for specific transition.*/
          var grid_div = jQuery('<span class="wds_gridlet_<?php echo $wds; ?>" />').css({
            display: "block",
            width : "100%",
            height : "100%",
            transform : 'translateZ(' + tz + 'px)',
            backfaceVisibility : 'hidden',
            overflow: 'hidden'
          }).append(jQuery('<span class="wds_gridlet_<?php echo $wds; ?>" />').css({
            display: "block",
            width : jQuery(".wds_slideshow_image_spun_<?php echo $wds; ?>").width() + "px",
            height : jQuery(".wds_slideshow_image_spun_<?php echo $wds; ?>").height() + "px",
            top : -top,
            left : -left,
            backgroundImage : src,
            backgroundSize: jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css("background-size"),
            backgroundPosition: jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css("background-position"),
            /*backgroundColor: jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>").css("background-color"),*/
            backgroundRepeat: 'no-repeat',
          }));
          var grid_div2 = jQuery('<span class="wds_gridlet_<?php echo $wds; ?>" />').css({
            display: "block",
            width : "100%",
            height : "100%",
            backfaceVisibility : 'hidden',
            transform : 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY('+ nry +'deg) rotateX('+ nrx +'deg)',
            overflow: 'hidden'
          }).append(jQuery('<span class="wds_gridlet_<?php echo $wds; ?>" />').css({
            display: "block",
            width : jQuery(".wds_slideshow_image_spun_<?php echo $wds; ?>").width() + "px",
            height : jQuery(".wds_slideshow_image_spun_<?php echo $wds; ?>").height() + "px",
            top : -top,
            left : -left,
            backgroundImage : src2,
            backgroundSize: jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css("background-size"),
            backgroundPosition: jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css("background-position"),
            /*backgroundColor: jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>").css("background-color"),*/
            backgroundRepeat: 'no-repeat',
          }));
          return jQuery('<span class="wds_gridlet_<?php echo $wds; ?>" />').css({
            display: "block",
            width : width,
            height : height,
            top : top,
            left : left,
            transition : 'all ' + wds_transition_duration_<?php echo $wds; ?> + 'ms ' + easing + ' ' + delay + 'ms',
            transform: 'translateZ(-' + tz + 'px)',
            transformStyle: 'preserve-3d',
          }).append(grid_div).append(grid_div2);
        }
        /* Get the current slide's image.*/
        var cur_img = jQuery(current_image_class).find('div');
        var next_img = jQuery(next_image_class).find('div');
        /* Create a grid to hold the gridlets.*/
        var grid = jQuery('<span style="display: block;" />').addClass('wds_grid_<?php echo $wds; ?>').css('perspective', 1000);
        /* Prepend the grid to the next slide (i.e. so it's above the slide image).*/
        jQuery(current_image_class).prepend(grid);
        /* vars to calculate positioning/size of gridlets*/
        var cont = jQuery(".wds_slide_bg_<?php echo $wds; ?>");
        var imgWidth = cur_img.width();
        var imgHeight = cur_img.height();
        var contWidth = cont.width(),
            contHeight = cont.height(),
            imgSrc = cur_img.css('background-image'),
            imgSrcNext = next_img.css('background-image'),
            colWidth = Math.floor(contWidth / cols),
            rowHeight = Math.floor(contHeight / rows),
            colRemainder = contWidth - (cols * colWidth),
            colAdd = Math.ceil(colRemainder / cols),
            rowRemainder = contHeight - (rows * rowHeight),
            rowAdd = Math.ceil(rowRemainder / rows),
            leftDist = 0,
            img_leftDist = (jQuery(".wds_slide_bg_<?php echo $wds; ?>").width() - cur_img.width()) / 2;
        /* Loop through cols*/
        for (var i = 0; i < cols; i++) {
          var topDist = 0,
              img_topDst = (jQuery(".wds_slide_bg_<?php echo $wds; ?>").height() - cur_img.height()) / 2,
              newColWidth = colWidth;
          /* If imgWidth (px) does not divide cleanly into the specified number of cols, adjust individual col widths to create correct total.*/
          if (colRemainder > 0) {
            var add = colRemainder >= colAdd ? colAdd : colRemainder;
            newColWidth += add;
            colRemainder -= add;
          }
          /* Nested loop to create row gridlets for each col.*/
          for (var j = 0; j < rows; j++)  {
            var newRowHeight = rowHeight,
                newRowRemainder = rowRemainder;
            /* If contHeight (px) does not divide cleanly into the specified number of rows, adjust individual row heights to create correct total.*/
            if (newRowRemainder > 0) {
              add = newRowRemainder >= rowAdd ? rowAdd : rowRemainder;
              newRowHeight += add;
              newRowRemainder -= add;
            }
            /* Create & append gridlet to grid.*/
            grid.append(wds_gridlet(newColWidth, newRowHeight, topDist, img_topDst, leftDist, img_leftDist, imgSrc, imgSrcNext, imgWidth, imgHeight, i, j));
            topDist += newRowHeight;
            img_topDst -= newRowHeight;
          }
          
          img_leftDist -= newColWidth;
          leftDist += newColWidth;
        }
        /* Show grid & hide the image it replaces.*/
        grid.show();
        cur_img.css('opacity', 0);        
        /* Execution steps.*/
        setTimeout(function () {
          grid.children().css({
            transform: 'translateZ(-' + tz + 'px) rotateX('+ wrx +'deg) rotateY('+ wry +'deg)'
          });
        }, 1);
        /* After transition.*/
         var cccount = 0;
         var obshicccount = cols * rows;
         grid.children().one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(wds_after_trans_each));
         function wds_after_trans_each() {
           if (++cccount == obshicccount) {
             wds_after_trans();
           }
         }
         function wds_after_trans() {
          jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
          cur_img.css('opacity', 1);
          grid.remove();
          wds_trans_in_progress_<?php echo $wds; ?> = false;
          if (typeof wds_event_stack_<?php echo $wds; ?> !== 'undefined') {
            if (wds_event_stack_<?php echo $wds; ?>.length > 0) {
              key = wds_event_stack_<?php echo $wds; ?>[0].split("-");
              wds_event_stack_<?php echo $wds; ?>.shift();
              wds_change_image_<?php echo $wds; ?>(key[0], key[1], wds_data_<?php echo $wds; ?>, true);
            }
          }
        }
      }
      function wds_slic3DH_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {         
        var dimension = jQuery(current_image_class).width() / 2;
        if (direction == 'right') {
          wds_grid3d_<?php echo $wds; ?>(1, 5, dimension, 0, -90, 0, dimension, 90, 0, current_image_class, next_image_class, direction, 0, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
        else if (direction == 'left') {
          wds_grid3d_<?php echo $wds; ?>(1, 5, dimension, 0, 90, 0, -dimension, -90, 0, current_image_class, next_image_class, direction, 0, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
      }
      function wds_slic3DV_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        var dimension = jQuery(current_image_class).height() / 2;
        if (direction == 'right') {
          wds_grid3d_<?php echo $wds; ?>(10, 1, dimension, -90, 0, -dimension, 0, 0, 90, current_image_class, next_image_class, direction, 0, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
        else if (direction == 'left') {
          wds_grid3d_<?php echo $wds; ?>(10, 1, dimension, 90, 0, dimension, 0, 0, -90, current_image_class, next_image_class, direction, 0, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
      }
      
      function wds_slicR3DH_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {         
        var dimension = jQuery(current_image_class).width() / 2;
        if (direction == 'right') {
          wds_grid3d_<?php echo $wds; ?>(1, 5, dimension, 0, -90, 0, dimension, 90, 0, current_image_class, next_image_class, direction, 1, 'ease-in-out');
        }
        else if (direction == 'left') {
          wds_grid3d_<?php echo $wds; ?>(1, 5, dimension, 0, 90, 0, -dimension, -90, 0, current_image_class, next_image_class, direction, 1, 'ease-in-out');
        }
      }
      function wds_slicR3DV_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        var dimension = jQuery(current_image_class).height() / 2;
        if (direction == 'right') {
          wds_grid3d_<?php echo $wds; ?>(10, 1, dimension, -90, 0, -dimension, 0, 0, 90, current_image_class, next_image_class, direction, 1, 'ease-in-out');
        }
        else if (direction == 'left') {
          wds_grid3d_<?php echo $wds; ?>(10, 1, dimension, 90, 0, dimension, 0, 0, -90, current_image_class, next_image_class, direction, 1, 'ease-in-out');
        }
      }
      function wds_parallelSlide_<?php echo $wds; ?>(ni_left, ni_top, tx, ty, current_image_class, next_image_class, direction, easing) {
        /* If browser does not support 3d transforms/CSS transitions.*/
        if (!wds_testBrowser_cssTransitions_<?php echo $wds; ?>()) {
          return wds_fallback_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
        }
        if (!wds_testBrowser_cssTransforms3d_<?php echo $wds; ?>()) {
          return wds_fallback3d_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
        }
        wds_trans_in_progress_<?php echo $wds; ?> = true;
        /* Set active thumbnail.*/
        wds_set_filmstrip_class_<?php echo $wds; ?>();
        wds_set_dots_class_<?php echo $wds; ?>();
        jQuery(current_image_class).css({
          position : 'absolute',
          top : '0px',
          left : '0px',
          position : 'absolute',
        });
        jQuery(next_image_class).css({
          position : 'absolute',
          top : ni_top + 'px',
          left : ni_left + 'px',
          'opacity' : 1, 
          filter : 'Alpha(opacity=100)',
          position : 'absolute',
        });
        jQuery(".wds_slider_<?php echo $wds; ?>").css({
          position : 'relative',
          'backface-visibility': 'hidden'
        });
        jQuery(".wds_slide_bg_<?php echo $wds; ?>").css({
          overflow : 'hidden',
        });
        /* Execution steps.*/
        setTimeout(function () {
          jQuery('.wds_slider_<?php echo $wds; ?>').css({
            transition : 'all ' + wds_transition_duration_<?php echo $wds; ?> + 'ms ' + easing,
            transform : 'translateX(' + tx + 'px) translateY(' + ty + 'px)',
          });
        }, 1);
        /* After transition.*/
        jQuery('.wds_slider_<?php echo $wds; ?>').one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(wds_after_trans));
        function wds_after_trans() {
          jQuery(current_image_class).removeAttr('style');
          jQuery(next_image_class).removeAttr('style');
          jQuery(".wds_slider_<?php echo $wds; ?>").removeAttr('style');
          jQuery(".wds_slide_bg_<?php echo $wds; ?>").removeAttr('style');
          jQuery(current_image_class).css({'opacity' : 0, filter: 'Alpha(opacity=0)', 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, filter: 'Alpha(opacity=100)', 'z-index' : 2});
          wds_trans_in_progress_<?php echo $wds; ?> = false;
          if (typeof wds_event_stack_<?php echo $wds; ?> !== 'undefined') {
            if (wds_event_stack_<?php echo $wds; ?>.length > 0) {
              key = wds_event_stack_<?php echo $wds; ?>[0].split("-");
              wds_event_stack_<?php echo $wds; ?>.shift();
              wds_change_image_<?php echo $wds; ?>(key[0], key[1], wds_data_<?php echo $wds; ?>, true);
            }
          }
        }
      }
      function wds_parallelSlideH_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        var width = jQuery(current_image_class).width();
        var height = jQuery(current_image_class).height();
        if (direction == 'right') {
          wds_parallelSlide_<?php echo $wds; ?>(width, 0, -width, 0, current_image_class, next_image_class, direction, 'ease-in-out');
        }
        else if (direction == 'left') {
          wds_parallelSlide_<?php echo $wds; ?>(-width, 0, width, 0, current_image_class, next_image_class, direction, 'ease-in-out');
        }
      }
      function wds_parallelSlideV_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        var width = jQuery(current_image_class).width();
        var height = jQuery(current_image_class).height();
        if (direction == 'right') {
          wds_parallelSlide_<?php echo $wds; ?>(0, height, 0, -height, current_image_class, next_image_class, direction, 'ease-in-out');
        }
        else if (direction == 'left') {
          wds_parallelSlide_<?php echo $wds; ?>(0, -height, 0, height, current_image_class, next_image_class, direction, 'ease-in-out');
        }
      }
      function wds_cube_<?php echo $wds; ?>(tz, ntx, nty, nrx, nry, wrx, wry, current_image_class, next_image_class, direction, easing) {
        /* If browser does not support 3d transforms/CSS transitions.*/
        if (!wds_testBrowser_cssTransitions_<?php echo $wds; ?>()) {
          return wds_fallback_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
        }
        if (!wds_testBrowser_cssTransforms3d_<?php echo $wds; ?>()) {
          return wds_fallback3d_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
        }
        wds_trans_in_progress_<?php echo $wds; ?> = true;
        /* Set active thumbnail.*/
        wds_set_filmstrip_class_<?php echo $wds; ?>();
        wds_set_dots_class_<?php echo $wds; ?>();

        jQuery(".wds_slide_container_<?php echo $wds; ?>").css('overflow', 'visible');
        jQuery(".wds_slideshow_image_spun2_<?php echo $wds; ?>").css('overflow', 'visible');
        jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>").css('overflow', 'visible');
        var filmstrip_position = '<?php echo $filmstrip_position; ?>';
        if (filmstrip_position == 'none') {
          jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-radius', jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>").css('border-radius'));
        }
        else {
          jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-radius', '<?php echo $slider_row->glb_border_radius; ?>');
          jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-radius', '<?php echo $slider_row->glb_border_radius; ?>');
          if (filmstrip_position == 'top') {
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-top-left-radius', 0);
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-top-right-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-bottom-left-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-bottom-right-radius', 0);
          }
          else if (filmstrip_position == 'bottom') {
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-bottom-left-radius', 0);
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-bottom-right-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-top-left-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-top-right-radius', 0);
          }
          else if (filmstrip_position == 'right') {
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-bottom-right-radius', 0);
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-top-right-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-bottom-left-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-top-left-radius', 0);
          }
          else if (filmstrip_position == 'left') {
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-bottom-left-radius', 0);
            jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css('border-top-left-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-bottom-right-radius', 0);
            jQuery(".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").css('border-top-right-radius', 0);
          }
        }

        jQuery(".wds_slide_bg_<?php echo $wds; ?>").css('perspective', 1000);
        jQuery(current_image_class).css({
          transform : 'translateZ(' + tz + 'px)',
          backfaceVisibility : 'hidden'
        });
        jQuery(next_image_class).css({
          opacity : 1,
          filter: 'Alpha(opacity=100)',
          zIndex: 2,
          backfaceVisibility : 'hidden',
          transform : 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY('+ nry +'deg) rotateX('+ nrx +'deg)'
        });
        jQuery(".wds_slider_<?php echo $wds; ?>").css({
          transform: 'translateZ(-' + tz + 'px)',
          transformStyle: 'preserve-3d',
          position: 'absolute'
        });
        /* Execution steps.*/
        setTimeout(function () {
          jQuery(".wds_slider_<?php echo $wds; ?>").css({
            transition: 'all ' + wds_transition_duration_<?php echo $wds; ?> + 'ms ' + easing,
            transform: 'translateZ(-' + tz + 'px) rotateX('+ wrx +'deg) rotateY('+ wry +'deg)'
          });
        }, 20);
        /* After transition.*/
        jQuery(".wds_slider_<?php echo $wds; ?>").one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(wds_after_trans));
        function wds_after_trans() {
          jQuery(current_image_class).removeAttr('style');
          jQuery(next_image_class).removeAttr('style');
          jQuery(".wds_slider_<?php echo $wds; ?>").removeAttr('style');
          jQuery(current_image_class).css({'opacity' : 0, filter: 'Alpha(opacity=0)', 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, filter: 'Alpha(opacity=100)', 'z-index' : 2});
          wds_trans_in_progress_<?php echo $wds; ?> = false;
          if (typeof wds_event_stack_<?php echo $wds; ?> !== 'undefined') {
            if (wds_event_stack_<?php echo $wds; ?>.length > 0) {
              key = wds_event_stack_<?php echo $wds; ?>[0].split("-");
              wds_event_stack_<?php echo $wds; ?>.shift();
              wds_change_image_<?php echo $wds; ?>(key[0], key[1], wds_data_<?php echo $wds; ?>, true);
            }
          }
          jQuery(".wds_slide_container_<?php echo $wds; ?>").css('overflow', 'hidden');
          jQuery(".wds_slideshow_image_spun2_<?php echo $wds; ?>").css('overflow', 'hidden');
          jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>").css('overflow', 'hidden');
          jQuery(".wds_slide_bg_<?php echo $wds; ?>").css('perspective', 'none');
        }
      }
      function wds_cubeR_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        var random_direction = Math.floor(Math.random() * 2);
        var dimension = random_direction ? jQuery(current_image_class).height() / 2 : jQuery(current_image_class).width() / 2;
        if (direction == 'right') {
         if (random_direction) {
           wds_cube_<?php echo $wds; ?>(dimension, 0, -dimension, 90, 0, -90, 0, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
         }
         else {
           wds_cube_<?php echo $wds; ?>(dimension, dimension, 0, 0, 90, 0, -90, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
         }
        }
        else if (direction == 'left') {
         if (random_direction) {
           wds_cube_<?php echo $wds; ?>(dimension, 0, dimension, -90, 0, 90, 0, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
         }
         else {
           wds_cube_<?php echo $wds; ?>(dimension, -dimension, 0, 0, -90, 0, 90, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
         }
        }
      }
      function wds_cubeH_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        /* Set to half of image width.*/
        var dimension = jQuery(current_image_class).width() / 2;
        if (direction == 'right') {
          wds_cube_<?php echo $wds; ?>(dimension, dimension, 0, 0, 90, 0, -90, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
        else if (direction == 'left') {
          wds_cube_<?php echo $wds; ?>(dimension, -dimension, 0, 0, -90, 0, 90, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
      }
      function wds_cubeV_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        /* Set to half of image height.*/
        var dimension = jQuery(current_image_class).height() / 2;
        /* If next slide.*/
        if (direction == 'right') {
          wds_cube_<?php echo $wds; ?>(dimension, 0, -dimension, 90, 0, -90, 0, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
        else if (direction == 'left') {
          wds_cube_<?php echo $wds; ?>(dimension, 0, dimension, -90, 0, 90, 0, current_image_class, next_image_class, direction, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
        }
      }
      /* For browsers that does not support transitions.*/
      function wds_fallback_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_fade_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
      }
      /* For browsers that support transitions, but not 3d transforms (only used if primary transition makes use of 3d-transforms).*/
      function wds_fallback3d_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_sliceV_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
      }
      function wds_none_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
        jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
        /* Set active thumbnail.*/
        wds_set_filmstrip_class_<?php echo $wds; ?>();
        wds_set_dots_class_<?php echo $wds; ?>();
      }
      function wds_fade_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        /* Set active thumbnail.*/
        wds_set_filmstrip_class_<?php echo $wds; ?>();
        wds_set_dots_class_<?php echo $wds; ?>();
        if (wds_testBrowser_cssTransitions_<?php echo $wds; ?>()) {
          jQuery(next_image_class).css('transition', 'opacity ' + wds_transition_duration_<?php echo $wds; ?> + 'ms linear');
          jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
        }
        else {
          jQuery(current_image_class).animate({'opacity' : 0, 'z-index' : 1}, wds_transition_duration_<?php echo $wds; ?>);
          jQuery(next_image_class).animate({
              'opacity' : 1,
              'z-index': 2
            }, {
              duration: wds_transition_duration_<?php echo $wds; ?>,
              complete: function () {  }
            });
          /* For IE.*/
          jQuery(current_image_class).fadeTo(wds_transition_duration_<?php echo $wds; ?>, 0);
          jQuery(next_image_class).fadeTo(wds_transition_duration_<?php echo $wds; ?>, 1);
        }
      }
      function wds_grid_<?php echo $wds; ?>(cols, rows, ro, tx, ty, sc, op, current_image_class, next_image_class, direction, random, roy, easing) {
        /* If browser does not support CSS transitions.*/
        if (!wds_testBrowser_cssTransitions_<?php echo $wds; ?>()) {
          return wds_fallback_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
        }
        wds_trans_in_progress_<?php echo $wds; ?> = true;
        /* Set active thumbnail.*/
        wds_set_filmstrip_class_<?php echo $wds; ?>();
        wds_set_dots_class_<?php echo $wds; ?>();
        /* The time (in ms) added to/subtracted from the delay total for each new gridlet.*/
        var count = (wds_transition_duration_<?php echo $wds; ?>) / (cols + rows);
        /* Gridlet creator (divisions of the image grid, positioned with background-images to replicate the look of an entire slide image when assembled)*/
        function wds_gridlet(width, height, top, img_top, left, img_left, src, imgWidth, imgHeight, c, r) {
          var delay = random ? Math.floor((cols + rows) * count * Math.random()) : (c + r) * count;
          /* Return a gridlet elem with styles for specific transition.*/
          var grid_div = jQuery('<span class="wds_gridlet_<?php echo $wds; ?>" />').css({
            display: "block",
            width : imgWidth,/*"100%"*/
            height : jQuery(".wds_slideshow_image_spun_<?php echo $wds; ?>").height() + "px",
            top : -top,
            left : -left,
            backgroundImage : src,
            backgroundSize: jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css("background-size"),
            backgroundPosition: jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css("background-position"),
            /*backgroundColor: jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>").css("background-color"),*/
            backgroundRepeat: 'no-repeat'
          });
          return jQuery('<span class="wds_gridlet_<?php echo $wds; ?>" />').css({
            display: "block",
            width : width,/*"100%"*/
            height : height,
            top : top,
            left : left,
            backgroundSize : imgWidth + 'px ' + imgHeight + 'px',
            backgroundPosition : img_left + 'px ' + img_top + 'px',
            backgroundRepeat: 'no-repeat',
            overflow: "hidden",
            transition : 'all ' + wds_transition_duration_<?php echo $wds; ?> + 'ms ' + easing + ' ' + delay + 'ms',
            transform : 'none'
          }).append(grid_div);
        }
        /* Get the current slide's image.*/
        var cur_img = jQuery(current_image_class).find('div');
        /* Create a grid to hold the gridlets.*/
        var grid = jQuery('<span style="display: block;" />').addClass('wds_grid_<?php echo $wds; ?>');
        /* Prepend the grid to the next slide (i.e. so it's above the slide image).*/
        jQuery(current_image_class).prepend(grid);
        /* vars to calculate positioning/size of gridlets*/
        var cont = jQuery(".wds_slide_bg_<?php echo $wds; ?>");
        var imgWidth = cur_img.width();
        var imgHeight = cur_img.height();
        var contWidth = cont.width(),
            contHeight = cont.height(),
            imgSrc = cur_img.css('background-image'),/*.replace('/thumb', ''),*/
            colWidth = Math.floor(contWidth / cols),
            rowHeight = Math.floor(contHeight / rows),
            colRemainder = contWidth - (cols * colWidth),
            colAdd = Math.ceil(colRemainder / cols),
            rowRemainder = contHeight - (rows * rowHeight),
            rowAdd = Math.ceil(rowRemainder / rows),
            leftDist = 0,
            img_leftDist = (jQuery(".wds_slide_bg_<?php echo $wds; ?>").width() - cur_img.width()) / 2;
        /* tx/ty args can be passed as 'auto'/'min-auto' (meaning use slide width/height or negative slide width/height).*/
        tx = tx === 'auto' ? contWidth : tx;
        tx = tx === 'min-auto' ? - contWidth : tx;
        ty = ty === 'auto' ? contHeight : ty;
        ty = ty === 'min-auto' ? - contHeight : ty;
        /* Loop through cols*/
        for (var i = 0; i < cols; i++) {
          var topDist = 0,
              img_topDst = (jQuery(".wds_slide_bg_<?php echo $wds; ?>").height() - cur_img.height()) / 2,
              newColWidth = colWidth;
          /* If imgWidth (px) does not divide cleanly into the specified number of cols, adjust individual col widths to create correct total.*/
          if (colRemainder > 0) {
            var add = colRemainder >= colAdd ? colAdd : colRemainder;
            newColWidth += add;
            colRemainder -= add;
          }
          /* Nested loop to create row gridlets for each col.*/
          for (var j = 0; j < rows; j++)  {
            var newRowHeight = rowHeight,
                newRowRemainder = rowRemainder;
            /* If contHeight (px) does not divide cleanly into the specified number of rows, adjust individual row heights to create correct total.*/
            if (newRowRemainder > 0) {
              add = newRowRemainder >= rowAdd ? rowAdd : rowRemainder;
              newRowHeight += add;
              newRowRemainder -= add;
            }
            /* Create & append gridlet to grid.*/
            grid.append(wds_gridlet(newColWidth, newRowHeight, topDist, img_topDst, leftDist, img_leftDist, imgSrc, imgWidth, imgHeight, i, j));
            topDist += newRowHeight;
            img_topDst -= newRowHeight;
          }
          
          img_leftDist -= newColWidth;
          leftDist += newColWidth;
        }
        /* Show grid & hide the image it replaces.*/
        grid.show();
        cur_img.css('opacity', 0);
        /* Add identifying classes to corner gridlets (useful if applying border radius).*/
        grid.children().first().addClass('rs-top-left');
        grid.children().last().addClass('rs-bottom-right');
        grid.children().eq(rows - 1).addClass('rs-bottom-left');
        grid.children().eq(- rows).addClass('rs-top-right');
        /* Execution steps.*/
        setTimeout(function () {
          grid.children().css({
            opacity: op,
            transform: 'rotate('+ ro +'deg) rotateY('+ roy +'deg) translateX('+ tx +'px) translateY('+ ty +'px) scale('+ sc +')'
          });
        }, 1);
        jQuery(next_image_class).css('opacity', 1);
        /* After transition.*/
        var cccount = 0;
        var obshicccount = cols * rows;
        grid.children().one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(wds_after_trans_each));
        function wds_after_trans_each() {
         if (++cccount == obshicccount) {
           wds_after_trans();
         }
        }
        function wds_after_trans() {
          jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
          cur_img.css('opacity', 1);
          grid.remove();
          wds_trans_in_progress_<?php echo $wds; ?> = false;
          if (typeof wds_event_stack_<?php echo $wds; ?> !== 'undefined') {
            if (wds_event_stack_<?php echo $wds; ?>.length > 0) {
              key = wds_event_stack_<?php echo $wds; ?>[0].split("-");
              wds_event_stack_<?php echo $wds; ?>.shift();
              wds_change_image_<?php echo $wds; ?>(key[0], key[1], wds_data_<?php echo $wds; ?>, true);
            }
          }
        }
      }
      function wds_sliceH_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateX = 'min-auto';
        }
        else if (direction == 'left') {
          var translateX = 'auto';
        }
        wds_grid_<?php echo $wds; ?>(1, 8, 0, translateX, 0, 1, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_sliceV_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateY = 'min-auto';
        }
        else if (direction == 'left') {
          var translateY = 'auto';
        }
        wds_grid_<?php echo $wds; ?>(10, 1, 0, 0, translateY, 1, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_slideV_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateY = 'auto';
        }
        else if (direction == 'left') {
          var translateY = 'min-auto';
        }
        wds_grid_<?php echo $wds; ?>(1, 1, 0, 0, translateY, 1, 1, current_image_class, next_image_class, direction, 0, 0, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
      }
      function wds_slideH_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateX = 'min-auto';
        }
        else if (direction == 'left') {
          var translateX = 'auto';
        }
        wds_grid_<?php echo $wds; ?>(1, 1, 0, translateX, 0, 1, 1, current_image_class, next_image_class, direction, 0, 0, 'cubic-bezier(0.785, 0.135, 0.150, 0.860)');
      }
      function wds_scaleOut_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(1, 1, 0, 0, 0, 1.5, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_scaleIn_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(1, 1, 0, 0, 0, 0.5, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_blockScale_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(8, 6, 0, 0, 0, 0.6, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_blockScaleR_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(8, 6, 0, 0, 0, 0.6, 0, current_image_class, next_image_class, direction, 1, 0, 'ease-in-out');
      }
      function wds_blindR_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(8, 1, 0, 0, 0, 1, 1, current_image_class, next_image_class, direction, 1, 90, 'ease-in-out');
      }
      function wds_tilesR_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(8, 8, 0, 0, 0, 1, 1, current_image_class, next_image_class, direction, 1, 90, 'ease-in-out');
      }
      function wds_kaleidoscope_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(10, 8, 0, 0, 0, 1, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_fan_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var rotate = 45;
          var translateX = 100;
        }
        else if (direction == 'left') {
          var rotate = -45;
          var translateX = -100;
        }
        wds_grid_<?php echo $wds; ?>(1, 10, rotate, translateX, 0, 1, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_blindV_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(1, 8, 0, 0, 0, .7, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_blindH_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        wds_grid_<?php echo $wds; ?>(10, 1, 0, 0, 0, .7, 0, current_image_class, next_image_class, direction, 0, 0, 'ease-in-out');
      }
      function wds_random_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        var anims = ['sliceH', 'sliceV', 'slideH', 'slideV', 'scaleOut', 'scaleIn', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV', 'parallelSlideH', 'parallelSlideV'];
        /* Pick a random transition from the anims array.*/
        this["wds_" + anims[Math.floor(Math.random() * anims.length)] + "_<?php echo $wds; ?>"](current_image_class, next_image_class, direction);
      }
      function wds_3Drandom_<?php echo $wds; ?>(current_image_class, next_image_class, direction) {
        var anims = ['cubeH', 'cubeV', 'cubeR', 'slic3DH', 'slic3DV', 'slicR3DH', 'slicR3DV'];
        /* Pick a random transition from the anims array.*/
        this["wds_" + anims[Math.floor(Math.random() * anims.length)] + "_<?php echo $wds; ?>"](current_image_class, next_image_class, direction);
      }
      function iterator_<?php echo $wds; ?>() {
        var iterator = 1;
        if (<?php echo $enable_slideshow_shuffle; ?>) {
          iterator = Math.floor((wds_data_<?php echo $wds; ?>.length - 1) * Math.random() + 1);
        }
        return iterator;
      }
      function wds_change_image_<?php echo $wds; ?>(current_key, key, wds_data_<?php echo $wds; ?>, from_effect) {
        if (<?php echo $carousel; ?>) {
          if (wds_currentlyMoving<?php echo $wds; ?>) {
            return;
          }
        }
        <?php
        if ($slider_row->effect == 'zoomFade') {
          ?>
          wds_genBgPos_<?php echo $wds; ?>();
          <?php
        }
        ?>
        /* Pause videos.*/
        jQuery("#wds_slideshow_image_container_<?php echo $wds; ?>").find("iframe").each(function () {
          jQuery(this)[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
          jQuery(this)[0].contentWindow.postMessage('{ "method": "pause" }', "*");
        });
        /* Pause layer videos.*/
        jQuery(".wds_video_layer_frame_<?php echo $wds; ?>").each(function () {
          jQuery(this)[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
          jQuery(this)[0].contentWindow.postMessage('{ "method": "pause" }', "*");
        });
        if (wds_data_<?php echo $wds; ?>[key]) {
          if (jQuery('.wds_ctrl_btn_<?php echo $wds; ?>').hasClass('fa-pause') || ('<?php echo $autoplay; ?>')) {
            play_<?php echo $wds; ?>();
          }
          if (!from_effect) {
            /* Change image key.*/
            jQuery("#wds_current_image_key_<?php echo $wds; ?>").val(key);
            if (current_key == '-1') { /* Filmstrip.*/
              current_key = jQuery(".wds_slideshow_thumb_active_<?php echo $wds; ?>").children("img").attr("image_key");
            }
            else if (current_key == '-2') { /* Dots.*/
              current_key = jQuery(".wds_slideshow_dots_active_<?php echo $wds; ?>").attr("image_key");
            }
          }
          if (wds_trans_in_progress_<?php echo $wds; ?>) {
            wds_event_stack_<?php echo $wds; ?>.push(current_key + '-' + key);
            return;
          }
          var direction = 'right';
          var int_curr_key = parseInt(wds_current_key_<?php echo $wds; ?>);
          var int_key = parseInt(key);
          var last_pos = wds_data_<?php echo $wds; ?>.length - 1;
          if (int_curr_key > int_key) {
            direction = 'left';
          }
          else if (int_curr_key == int_key) {
            return;
          }
          if (int_key == 0) {
            if (int_curr_key == last_pos) {
              direction = 'right';
            }
          }
          if (int_key == last_pos) {
            if (int_curr_key == 0) {
              direction = 'left';
            }
          }
          /* Set active thumbnail position.*/
          wds_current_filmstrip_pos_<?php echo $wds; ?> = key * (jQuery(".wds_slideshow_filmstrip_thumbnail_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() + 2 + 2 * 0);
          wds_current_key_<?php echo $wds; ?> = key;
          /* Change image id.*/
          jQuery("div[img_id=wds_slideshow_image_<?php echo $wds; ?>]").attr('image_id', wds_data_<?php echo $wds; ?>[key]["id"]);
          var current_image_class = "#wds_image_id_<?php echo $wds; ?>_" + wds_data_<?php echo $wds; ?>[current_key]["id"];
          var next_image_class = "#wds_image_id_<?php echo $wds; ?>_" + wds_data_<?php echo $wds; ?>[key]["id"];
          <?php if ($preload_images && !$carousel) { ?>
          if (!wds_data_<?php echo $wds; ?>[key]["is_video"]) {
            jQuery(next_image_class).find("div").css("background-image", 'url("' + wds_data_<?php echo $wds; ?>[key]["image_url"] + '")');
          }
          <?php } ?>
          var current_slide_layers_count = wds_data_<?php echo $wds; ?>[current_key]["slide_layers_count"];
          var next_slide_layers_count = wds_data_<?php echo $wds; ?>[key]["slide_layers_count"];

          /* Clear layers before image change.*/
          function set_layer_effect_out_before_change(m) {
            wds_clear_layers_effects_out_before_change_<?php echo $wds; ?>[current_key][m] = setTimeout(function() {
              if (wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_type"] != 'social') {
                jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[current_key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_id"]).css('-webkit-animation-duration' , 0.6 + 's').css('animation-duration' , 0.6 + 's');
                jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[current_key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_id"]).removeClass().addClass( wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_layer_effect_out"] + ' animated');
              }
              else {
                jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[current_key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_id"]).css('-webkit-animation-duration' , 0.6 + 's').css('animation-duration' , 0.6 + 's');
                jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[current_key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_id"]).removeClass().addClass( wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_layer_effect_out"] + ' fa fa-' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_social_button"] + ' animated');
              }
            }, 10);
          }
          if (<?php echo $slider_row->layer_out_next; ?>) {
            for (var m = 0; m < current_slide_layers_count; m++) {
              if (jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[current_key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + m + "_id"]).css('opacity') != 0) {
                set_layer_effect_out_before_change(m);
              }
            }
          }
          /* Loop through current slide layers for clear effects.*/
          setTimeout(function() {
            for (var k = 0; k < current_slide_layers_count; k++) {
              clearTimeout(wds_clear_layers_effects_in_<?php echo $wds; ?>[current_key][k]);
              clearTimeout(wds_clear_layers_effects_out_<?php echo $wds; ?>[current_key][k]);
              if (wds_data_<?php echo $wds; ?>[current_key]["layer_" + k + "_type"] != 'social') {
                jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[current_key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + k + "_id"]).removeClass().addClass('wds_layer_'+ wds_data_<?php echo $wds; ?>[current_key]["layer_" + k + "_id"]);
              }
              else {
                jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[current_key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + k + "_id"]).removeClass().addClass('fa fa-' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + k + "_social_button"] + ' wds_layer_' + wds_data_<?php echo $wds; ?>[current_key]["layer_" + k + "_id"]);
              }
            }
          }, wds_duration_for_clear_effects_<?php echo $wds; ?>);
          /* Loop through layers in.*/
          for (var j = 0; j < next_slide_layers_count; j++) {
            wds_set_layer_effect_in_<?php echo $wds; ?>(j, key);
          }
          /* Loop through layers out if pause button not pressed.*/
          for (var i = 0; i < next_slide_layers_count; i++) {
            wds_set_layer_effect_out_<?php echo $wds; ?>(i, key);
          }
          setTimeout(function() {
            if (typeof jQuery().finish !== 'undefined') {
              if (jQuery.isFunction(jQuery().finish)) {
                jQuery(".wds_line_timer_<?php echo $wds; ?>").finish();
              }
            }
            jQuery(".wds_line_timer_<?php echo $wds; ?>").css({width: 0});
            <?php
            if (!$carousel) {
              ?>
            wds_<?php echo $slideshow_effect; ?>_<?php echo $wds; ?>(current_image_class, next_image_class, direction);
              <?php
            }
            ?>
            if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
              if (<?php echo $enable_slideshow_autoplay; ?> || jQuery('.wds_ctrl_btn_<?php echo $wds; ?>').hasClass('fa-pause')) {
                if ('<?php echo $slider_row->timer_bar_type; ?>' == 'top' || '<?php echo $slider_row->timer_bar_type; ?>' == 'bottom') {
                  if (!jQuery(".wds_ctrl_btn_<?php echo $wds; ?>").hasClass("fa-play")) {
                    jQuery(".wds_line_timer_<?php echo $wds; ?>").animate({
                      width: "100%"
                    }, {
                      duration: <?php echo $slideshow_interval * 1000; ?>,
                      specialEasing: {width: "linear"}
                    });
                  }
                }
                else if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
                  if (typeof circle_timer_animate_<?php echo $wds; ?> !== 'undefined') {
                    circle_timer_animate_<?php echo $wds; ?>.stop();
                  }
                  jQuery('#top_right_<?php echo $wds; ?>').css({
                    '-moz-transform':'rotate(0deg)',
                    '-webkit-transform':'rotate(0deg)',
                    '-o-transform':'rotate(0deg)',
                    '-ms-transform':'rotate(0deg)',
                    'transform':'rotate(0deg)',
                     
                    '-webkit-transform-origin': 'left bottom',
                    '-ms-transform-origin': 'left bottom',
                    '-moz-transform-origin': 'left bottom',
                    'transform-origin': 'left bottom'
                  });
                  jQuery('#bottom_right_<?php echo $wds; ?>').css({
                    '-moz-transform':'rotate(0deg)',
                    '-webkit-transform':'rotate(0deg)',
                    '-o-transform':'rotate(0deg)',
                    '-ms-transform':'rotate(0deg)',
                    'transform':'rotate(0deg)',
                   
                    '-webkit-transform-origin': 'left top',
                    '-ms-transform-origin': 'left top',
                    '-moz-transform-origin': 'left top',
                    'transform-origin': 'left top'
                  });
                  jQuery('#bottom_left_<?php echo $wds; ?>').css({
                    '-moz-transform':'rotate(0deg)',
                    '-webkit-transform':'rotate(0deg)',
                    '-o-transform':'rotate(0deg)',
                    '-ms-transform':'rotate(0deg)',
                    'transform':'rotate(0deg)',
                   
                    '-webkit-transform-origin': 'right top',
                    '-ms-transform-origin': 'right top',
                    '-moz-transform-origin': 'right top',
                    'transform-origin': 'right top'
                  });
                  jQuery('#top_left_<?php echo $wds; ?>').css({
                    '-moz-transform':'rotate(0deg)',
                    '-webkit-transform':'rotate(0deg)',
                    '-o-transform':'rotate(0deg)',
                    '-ms-transform':'rotate(0deg)',
                    'transform':'rotate(0deg)',
                   
                    '-webkit-transform-origin': 'right bottom',
                    '-ms-transform-origin': 'right bottom',
                    '-moz-transform-origin': 'right bottom',
                    'transform-origin': 'right bottom'
                  });	
                  if (!jQuery(".wds_ctrl_btn_<?php echo $wds; ?>").hasClass("fa-play")) {
                    /* Begin circle timer on next.*/				  		
                    circle_timer_<?php echo $wds; ?>(0);
                  }
                  else {
                    curent_time_deggree_<?php echo $wds; ?> = 0;
                  }
                }
              }
            }
            <?php
            if ($filmstrip_position != 'none' && $slides_count > 1) {
              ?>
              wds_move_filmstrip_<?php echo $wds; ?>();
              <?php
            }
            if ($bull_position != 'none' && $slides_count > 1) {
              ?>
              wds_move_dots_<?php echo $wds; ?>();
              <?php
            }
            ?>
            if (wds_data_<?php echo $wds; ?>[key]["is_video"]) {
              jQuery("#wds_slideshow_play_pause_<?php echo $wds; ?>").css({display: 'none'});
            }
            else {
              jQuery("#wds_slideshow_play_pause_<?php echo $wds; ?>").css({display: ''});
            }
          }, wds_duration_for_change_<?php echo $wds; ?>);
        }
      }
      function wds_resize_slider_<?php echo $wds; ?>() {
        var slide_orig_width = <?php echo $image_width; ?>;
        var slide_orig_height = <?php echo $image_height; ?>;
        var slide_width = jQuery("#wds_container1_<?php echo $wds; ?>").parent().width();
        var ratio;
        <?php
        if (!$carousel) {
          ?>
        if (slide_width > slide_orig_width) {
          slide_width = slide_orig_width;
        }
        ratio = slide_width / slide_orig_width;
          <?php
        }
        if ($slider_row->full_width) {
          if (!$carousel) {
         ?>
        ratio = jQuery(window).width() / slide_orig_width;
            <?php
          }
          else {
            ?>
        ratio = jQuery(window).width() / slide_orig_width;
            <?php
          }
          ?>
        slide_orig_width = jQuery(window).width() - <?php echo 2 * $slider_row->glb_margin; ?>;
        slide_orig_height = <?php echo $image_height; ?> * slide_orig_width / <?php echo $image_width; ?>;
        slide_width = jQuery(window).width() - <?php echo 2 * $slider_row->glb_margin; ?>;
        wds_full_width_<?php echo $wds; ?>();
          <?php
        }
        if ($carousel) {
          ?>
          if (slide_width < <?php echo $carousel_width; ?>) {
            ratio = slide_width / <?php echo $carousel_width; ?>;
          }
          else {
            ratio = 1;
          }
          <?php
        }
        ?>
        var slide_height = slide_orig_height;
        if (slide_orig_width > slide_width) {
          slide_height = Math.floor(slide_width * slide_orig_height / slide_orig_width);
        }

        jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>, #wds_container2_<?php echo $wds; ?>").height(slide_height + <?php echo ($filmstrip_direction == 'horizontal' ? $filmstrip_height : 0); ?>);
        jQuery(".wds_slideshow_image_container_<?php echo $wds; ?>").height(slide_height);
        /*jQuery(".wds_slideshow_image_<?php echo $wds; ?>").height(slide_height);*/
	      jQuery(".wds_slide_container_<?php echo $wds; ?>").height(slide_height);
        <?php
        if (!$carousel) {
          ?>
        jQuery(".wds_slideshow_image_<?php echo $wds; ?>").height(slide_height);
        jQuery(".wds_slideshow_video_<?php echo $wds; ?>").height(slide_height);
          <?php
        }
        else {
          ?>
        jQuery(".wds_slideshow_image_<?php echo $wds; ?>").height("100%");
        jQuery(".wds_slideshow_video_<?php echo $wds; ?>").height("100%");
          <?php
        }
        ?>
        jQuery(".wds_slideshow_image_<?php echo $wds; ?> img").each(function () {
          var wds_theImage = new Image();
          wds_theImage.src = jQuery(this).attr("src");
          var wds_origWidth = wds_theImage.width;
          var wds_origHeight = wds_theImage.height;
          var wds_imageWidth = jQuery(this).attr("wds_image_width");
          var wds_imageHeight = jQuery(this).attr("wds_image_height");
          var wds_width = wds_imageWidth;
          if (wds_imageWidth > wds_origWidth) {
            wds_width = wds_origWidth;
          }
          var wds_height = wds_imageHeight;
          if (wds_imageHeight > wds_origHeight) {
            wds_height = wds_origHeight;
          }
          jQuery(this).css({
            maxWidth: (parseFloat(wds_imageWidth) * ratio) + "px",
            maxHeight: (parseFloat(wds_imageHeight) * ratio) + "px",
          });
          if (jQuery(this).attr("wds_scale") != "on") {
            jQuery(this).css({
              width: (parseFloat(wds_imageWidth) * ratio) + "px",
              height: (parseFloat(wds_imageHeight) * ratio) + "px"
            });
          }
          else if (wds_imageWidth > wds_origWidth || wds_imageHeight > wds_origHeight) {
            if (wds_origWidth / wds_imageWidth > wds_origHeight / wds_imageHeight) {
              jQuery(this).css({
                width: (parseFloat(wds_imageWidth) * ratio) + "px"
              });
            }
            else {
              jQuery(this).css({
                height: (parseFloat(wds_imageHeight) * ratio) + "px"
              });
            }
          }

        });
        jQuery(".wds_slideshow_image_<?php echo $wds; ?> span, .wds_slideshow_image_<?php echo $wds; ?> i").each(function () {
          jQuery(this).css({
            fontSize: (parseFloat(jQuery(this).attr("wds_fsize")) * ratio) + "px",
            lineHeight: "1.25em",
            paddingLeft: (parseFloat(jQuery(this).attr("wds_fpaddingl")) * ratio) + "px",
            paddingRight: (parseFloat(jQuery(this).attr("wds_fpaddingr")) * ratio) + "px",
            paddingTop: (parseFloat(jQuery(this).attr("wds_fpaddingt")) * ratio) + "px",
            paddingBottom: (parseFloat(jQuery(this).attr("wds_fpaddingb")) * ratio) + "px",
          })
        });
      }
      /* Generate background position for Zoom Fade effect.*/
      function wds_genBgPos_<?php echo $wds; ?>() {
        var bgSizeArray = [0, 70];
        var bgSize = bgSizeArray[Math.floor(Math.random() * bgSizeArray.length)];
        
        var bgPosXArray = ['left', 'right'];
        var bgPosYArray = ['top', 'bottom'];
        var bgPosX = bgPosXArray[Math.floor(Math.random() * bgPosXArray.length)];
        var bgPosY = bgPosYArray[Math.floor(Math.random() * bgPosYArray.length)];
        jQuery(".wds_slideshow_image_<?php echo $wds; ?>").css({
          backgroundPosition: bgPosX + " " + bgPosY,
          backgroundSize : (100 + bgSize) + "%",
          webkitAnimation: '<?php echo $slideshow_interval; ?>s linear 0s alternate infinite wdszoom' + bgSize,
          mozAnimation: '<?php echo $slideshow_interval; ?>s linear 0s alternate infinite wdszoom' + bgSize,
          animation: '<?php echo $slideshow_interval; ?>s linear 0s alternate infinite wdszoom' + bgSize
        });
      }
      jQuery(window).resize(function () {
        wds_resize_slider_<?php echo $wds; ?>();
      });
      function wds_full_width_<?php echo $wds; ?>() {
        var left = jQuery("#wds_container1_<?php echo $wds; ?>").offset().left;
        jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>").css({
          left: (-left + <?php echo $slider_row->glb_margin; ?>) + "px",
          width: (jQuery(window).width() - <?php echo 2 * $slider_row->glb_margin; ?>) + "px",
          maxWidth: "none"
        });
      }
      jQuery(window).load(function () {
        <?php
        if ($enable_slideshow_autoplay && $slider_row->stop_animation) {
          ?>
        jQuery("#wds_container1_<?php echo $wds; ?>").mouseover(function(e) {
          wds_stop_animation_<?php echo $wds; ?>();
        });
        jQuery("#wds_container1_<?php echo $wds; ?>").mouseout(function(e) {
          if (!e) {
            var e = window.event;
          }
          var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
          while (reltg.tagName != 'BODY') {
            if (reltg.id == this.id){
              return;
            }
            reltg = reltg.parentNode;
          }
          wds_play_animation_<?php echo $wds; ?>();
        });
          <?php
        }
        ?>
        jQuery(".wds_slideshow_image_<?php echo $wds; ?> span, .wds_slideshow_image_<?php echo $wds; ?> i").each(function () {
          jQuery(this).attr("wds_fpaddingl", jQuery(this).css("paddingLeft"));
          jQuery(this).attr("wds_fpaddingr", jQuery(this).css("paddingRight"));
          jQuery(this).attr("wds_fpaddingt", jQuery(this).css("paddingTop"));
          jQuery(this).attr("wds_fpaddingb", jQuery(this).css("paddingBottom"));
        });
        if (<?php echo $navigation; ?>) {
          jQuery("#wds_container2_<?php echo $wds; ?>").hover(function () {
            jQuery(".wds_right-ico_<?php echo $wds; ?>").animate({left: 0}, 700, "swing");
            jQuery(".wds_left-ico_<?php echo $wds; ?>").animate({left: 0}, 700, "swing");
            jQuery("#wds_slideshow_play_pause_<?php echo $wds; ?>").animate({opacity: 1, filter: "Alpha(opacity=100)"}, 700, "swing");
          }, function () {
            jQuery(".wds_right-ico_<?php echo $wds; ?>").css({left: 4000});
            jQuery(".wds_left-ico_<?php echo $wds; ?>").css({left: -4000});
            jQuery("#wds_slideshow_play_pause_<?php echo $wds; ?>").css({opacity: 0, filter: "Alpha(opacity=0)"});
          });
        }

        wds_resize_slider_<?php echo $wds; ?>();
        jQuery("#wds_container2_<?php echo $wds; ?>").css({visibility: 'visible'});
        jQuery(".wds_loading").hide();

      	<?php
        if ($slider_row->effect == 'zoomFade') {
          ?>
          wds_genBgPos_<?php echo $wds; ?>();
          <?php
        }
        if ($image_right_click) {
          ?>
          /* Disable right click.*/
          jQuery('div[id^="wds_container"]').bind("contextmenu", function () {
            return false;
          });
          <?php
        }
        ?>
        if (<?php echo $enable_prev_next_butt; ?>) {
          if (typeof jQuery().swiperight !== 'undefined') {
            if (jQuery.isFunction(jQuery().swiperight)) {
              jQuery('#wds_container1_<?php echo $wds; ?>').swiperight(function () {
                if (<?php echo $carousel; ?> == false) {
                  wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) - iterator_<?php echo $wds; ?>()) >= 0 ? (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) - iterator_<?php echo $wds; ?>()) % wds_data_<?php echo $wds; ?>.length : wds_data_<?php echo $wds; ?>.length - 1, wds_data_<?php echo $wds; ?>);
                }
                else {
                  wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) - iterator_<?php echo $wds; ?>()) >= 0 ? (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) - iterator_<?php echo $wds; ?>()) % wds_data_<?php echo $wds; ?>.length : wds_data_<?php echo $wds; ?>.length - 1, wds_data_<?php echo $wds; ?>);
                  wds_carousel<?php echo $wds; ?>.prev();
                }
                return false;
              });
            }
          }
          if (typeof jQuery().swipeleft !== 'undefined') {
            if (jQuery.isFunction(jQuery().swipeleft)) {
              jQuery('#wds_container1_<?php echo $wds; ?>').swipeleft(function () {
                if (<?php echo $carousel; ?> == false) {
                  wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) + iterator_<?php echo $wds; ?>()) % wds_data_<?php echo $wds; ?>.length, wds_data_<?php echo $wds; ?>);
                }
                else {
                  wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) + iterator_<?php echo $wds; ?>()) % wds_data_<?php echo $wds; ?>.length, wds_data_<?php echo $wds; ?>);
                  wds_carousel<?php echo $wds; ?>.next();
                }
                return false;
              });
            }
          }
        }

        var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
        var wds_click = isMobile ? 'touchend' : 'click';

        var mousewheelevt = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel"; /* FF doesn't recognize mousewheel as of FF3.x */
        jQuery('.wds_slideshow_filmstrip_<?php echo $wds; ?>').bind(mousewheelevt, function(e) {
          var evt = window.event || e; /* Equalize event object.*/
          evt = evt.originalEvent ? evt.originalEvent : evt; /* Convert to originalEvent if possible.*/
          var delta = evt.detail ? evt.detail*(-40) : evt.wheelDelta; /* Check for detail first, because it is used by Opera and FF.*/
          if (delta > 0) {
            /* Scroll up.*/
            jQuery(".wds_slideshow_filmstrip_left_<?php echo $wds; ?>").trigger("click");
          }
          else {
            /* Scroll down.*/
            jQuery(".wds_slideshow_filmstrip_right_<?php echo $wds; ?>").trigger("click");
          }
          return false;
        });
        jQuery(".wds_slideshow_filmstrip_right_<?php echo $wds; ?>").on(wds_click, function () {
          if (typeof jQuery().stop !== 'undefined') {
            if (jQuery.isFunction(jQuery().stop)) {
              jQuery( ".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>" ).stop(true, false);
            }
          }
          if (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> >= -(jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() - jQuery(".wds_slideshow_filmstrip_<?php echo $wds; ?>").<?php echo $width_or_height; ?>())) {
            jQuery(".wds_slideshow_filmstrip_left_<?php echo $wds; ?>").css({opacity: 1, filter: "Alpha(opacity=100)"});
            if (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> < -(jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() - jQuery(".wds_slideshow_filmstrip_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() - <?php echo $filmstrip_thumb_margin_hor + $filmstrip_width; ?>)) {
              jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").animate({<?php echo $left_or_top; ?>: -(jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() - jQuery(".wds_slideshow_filmstrip_<?php echo $wds; ?>").<?php echo $width_or_height; ?>())}, 500, 'linear');
            }
            else {
              jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").animate({<?php echo $left_or_top; ?>: (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> - <?php echo $filmstrip_thumb_margin_hor + $filmstrip_width; ?>)}, 500, 'linear');
            }
          }
          /* Disable right arrow.*/
          window.setTimeout(function() {
            if (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> == -(jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").<?php echo $width_or_height; ?>() - jQuery(".wds_slideshow_filmstrip_<?php echo $wds; ?>").<?php echo $width_or_height; ?>())) {
              jQuery(".wds_slideshow_filmstrip_right_<?php echo $wds; ?>").css({opacity: 0.3, filter: "Alpha(opacity=30)"});
            }
          }, 500);
        });
        jQuery(".wds_slideshow_filmstrip_left_<?php echo $wds; ?>").on(wds_click, function () {
          if (typeof jQuery().stop !== 'undefined') {
            if (jQuery.isFunction(jQuery().stop)) {
              jQuery( ".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>" ).stop(true, false);
            }
          }
          if (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> < 0) {
            jQuery(".wds_slideshow_filmstrip_right_<?php echo $wds; ?>").css({opacity: 1, filter: "Alpha(opacity=100)"});
            if (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> > - <?php echo $filmstrip_thumb_margin_hor + $filmstrip_width; ?>) {
              jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").animate({<?php echo $left_or_top; ?>: 0}, 500, 'linear');
            }
            else {
              jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").animate({<?php echo $left_or_top; ?>: (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> + <?php echo $filmstrip_thumb_margin_hor + $filmstrip_width; ?>)}, 500, 'linear');
            }
          }
          /* Disable left arrow.*/
          window.setTimeout(function(){
            if (jQuery(".wds_slideshow_filmstrip_thumbnails_<?php echo $wds; ?>").position().<?php echo $left_or_top; ?> == 0) {
              jQuery(".wds_slideshow_filmstrip_left_<?php echo $wds; ?>").css({opacity: 0.3, filter: "Alpha(opacity=30)"});
            }
          }, 500);
        });
        /* Set filmstrip initial position.*/
        wds_set_filmstrip_pos_<?php echo $wds; ?>(jQuery(".wds_slideshow_filmstrip_<?php echo $wds; ?>").<?php echo $width_or_height; ?>());
        /* Play/pause.*/
        jQuery("#wds_slideshow_play_pause_<?php echo $wds; ?>").on(wds_click, function () {
          if (jQuery(".wds_ctrl_btn_<?php echo $wds; ?>").hasClass("fa-play")) {
            /* Play.*/
            jQuery(".wds_slideshow_play_pause_<?php echo $wds; ?>").attr("title", "<?php echo __('Pause', 'bwg'); ?>");
            jQuery(".wds_slideshow_play_pause_<?php echo $wds; ?>").attr("class", "wds_ctrl_btn_<?php echo $wds; ?> wds_slideshow_play_pause_<?php echo $wds; ?> fa fa-pause");

            /* Finish current animation and begin the other.*/
            if (<?php echo $enable_slideshow_autoplay; ?>) {
              if ('<?php echo $slider_row->timer_bar_type; ?>' != 'top') {
                if ('<?php echo $slider_row->timer_bar_type; ?>' != 'bottom') {
                  if (typeof circle_timer_animate_<?php echo $wds; ?> !== 'undefined') {
                    circle_timer_animate_<?php echo $wds; ?>.stop();
                    if (<?php echo $carousel; ?> == true) {
                      wds_carousel<?php echo $wds; ?>.pause();
                    }
                  }
                  circle_timer_<?php echo $wds; ?>(curent_time_deggree_<?php echo $wds; ?>);
                }
              }
            }
            play_<?php echo $wds; ?>();
            if (<?php echo $enable_slideshow_music ?>) {
              document.getElementById("wds_audio_<?php echo $wds; ?>").play();
            }
            if (<?php echo $carousel; ?> != false) {
              wds_carousel<?php echo $wds; ?>.start();
            }
          }
          else {
            /* Pause.*/
            /* Pause layers out effect.*/
            var current_key = jQuery('#wds_current_image_key_<?php echo $wds; ?>').val();
            var current_slide_layers_count = wds_data_<?php echo $wds; ?>[current_key]["slide_layers_count"];
            setTimeout(function() {
              for (var k = 0; k < current_slide_layers_count; k++) {
                clearTimeout(wds_clear_layers_effects_out_<?php echo $wds; ?>[current_key][k]);
              }
            }, wds_duration_for_clear_effects_<?php echo $wds; ?>);

            window.clearInterval(wds_playInterval_<?php echo $wds; ?>);
            jQuery(".wds_slideshow_play_pause_<?php echo $wds; ?>").attr("title", "<?php echo __('Play', 'bwg'); ?>");
            jQuery(".wds_slideshow_play_pause_<?php echo $wds; ?>").attr("class", "wds_ctrl_btn_<?php echo $wds; ?> wds_slideshow_play_pause_<?php echo $wds; ?> fa fa-play");
            if (<?php echo $enable_slideshow_music ?>) {
              document.getElementById("wds_audio_<?php echo $wds; ?>").pause();
            }
            if (typeof jQuery().stop !== 'undefined') {
              if (jQuery.isFunction(jQuery().stop)) {
                <?php
                if ($slider_row->timer_bar_type == 'top' ||  $slider_row->timer_bar_type == 'bottom') {
                  ?>
                  jQuery(".wds_line_timer_<?php echo $wds; ?>").stop();
                  <?php
                }
                elseif ($slider_row->timer_bar_type != 'none') {
                  ?>
                  /* Pause circle timer.*/
                  if (typeof circle_timer_animate_<?php echo $wds; ?>.stop !== 'undefined') {
                    circle_timer_animate_<?php echo $wds; ?>.stop();
                    if (<?php echo $carousel; ?> == true) {
                      wds_carousel<?php echo $wds; ?>.pause();
                    }
                  }
                  <?php
                }
                ?>
              }
            }
            if (<?php echo $carousel; ?> != false) {
              wds_carousel<?php echo $wds; ?>.pause();
            }
          }
	  
        });
        if (<?php echo $enable_slideshow_autoplay; ?>) {
          play_<?php echo $wds; ?>();

          jQuery(".wds_slideshow_play_pause_<?php echo $wds; ?>").attr("title", "<?php echo __('Pause', 'bwg'); ?>");
          jQuery(".wds_slideshow_play_pause_<?php echo $wds; ?>").attr("class", "wds_ctrl_btn_<?php echo $wds; ?> wds_slideshow_play_pause_<?php echo $wds; ?> fa fa-pause");
          if (<?php echo $enable_slideshow_music ?>) {
            document.getElementById("wds_audio_<?php echo $wds; ?>").play();
          }
          if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
            if ('<?php echo $slider_row->timer_bar_type; ?>' != 'top') {
              if ('<?php echo $slider_row->timer_bar_type; ?>' != 'bottom') {
                circle_timer_<?php echo $wds; ?>(0);
              }
            }
          }
        }
        <?php if ($preload_images) { ?>
        function wds_preload_<?php echo $wds; ?>(preload_key) {
          jQuery("<img/>")
            .load(function() { if (preload_key < wds_data_<?php echo $wds; ?>.length - 1) wds_preload_<?php echo $wds; ?>(preload_key + 1); })
            .error(function() { if (preload_key < wds_data_<?php echo $wds; ?>.length - 1) wds_preload_<?php echo $wds; ?>(preload_key + 1); })
            .attr("src", (!wds_data_<?php echo $wds; ?>[preload_key]["is_video"] ? wds_data_<?php echo $wds; ?>[preload_key]["image_url"] : ""));
        }
        wds_preload_<?php echo $wds; ?>(0);
        <?php } ?>
        var first_slide_layers_count_<?php echo $wds; ?> = wds_data_<?php echo $wds; ?>[<?php echo $start_slide_num; ?>]["slide_layers_count"];
        if (first_slide_layers_count_<?php echo $wds; ?>) {
          /* Loop through layers in.*/
          for (var j = 0; j < first_slide_layers_count_<?php echo $wds; ?>; j++) {
            wds_set_layer_effect_in_<?php echo $wds; ?>(j, <?php echo $start_slide_num; ?>);
          }
          /* Loop through layers out.*/
          for (var i = 0; i < first_slide_layers_count_<?php echo $wds; ?>; i++) {
            wds_set_layer_effect_out_<?php echo $wds; ?>(i, <?php echo $start_slide_num; ?>);
          }
        }
      });
	    function wds_stop_animation_<?php echo $wds; ?>() {
        window.clearInterval(wds_playInterval_<?php echo $wds; ?>);
        /* Pause layers out effect.*/
        var current_key = jQuery('#wds_current_image_key_<?php echo $wds; ?>').val();
        var current_slide_layers_count = wds_data_<?php echo $wds; ?>[current_key]["slide_layers_count"];			
        setTimeout(function() {
          for (var k = 0; k < current_slide_layers_count; k++) {
            clearTimeout(wds_clear_layers_effects_out_<?php echo $wds; ?>[current_key][k]);
          }
        }, wds_duration_for_clear_effects_<?php echo $wds; ?>);
        if (<?php echo $enable_slideshow_music ?>) {
          document.getElementById("wds_audio_<?php echo $wds; ?>").pause();
        }
        if (typeof jQuery().stop !== 'undefined') {
          if (jQuery.isFunction(jQuery().stop)) {
            if ('<?php echo $slider_row->timer_bar_type; ?>' == 'top' || '<?php echo $slider_row->timer_bar_type; ?>' == 'bottom') {
              jQuery(".wds_line_timer_<?php echo $wds; ?>").stop();
              if (<?php echo $carousel; ?> == true) {
                wds_carousel<?php echo $wds; ?>.pause();
              }
            }
            else if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
              circle_timer_animate_<?php echo $wds; ?>.stop();
              if (<?php echo $carousel; ?> == true) {
                wds_carousel<?php echo $wds; ?>.pause();
              }
            }
          }
        }
      }
      function wds_play_animation_<?php echo $wds; ?>() {
        if (jQuery(".wds_ctrl_btn_<?php echo $wds; ?>").hasClass("fa-play")) {
          return;
        }
        play_<?php echo $wds; ?>();
        if (<?php echo $carousel; ?> == true) {
          wds_carousel<?php echo $wds; ?>.start();
        }
        if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
          if ('<?php echo $slider_row->timer_bar_type; ?>' != 'bottom') {
            if ('<?php echo $slider_row->timer_bar_type; ?>' != 'top') {
              if (typeof circle_timer_animate_<?php echo $wds; ?> !== 'undefined') {
                circle_timer_animate_<?php echo $wds; ?>.stop();
                if (<?php echo $carousel; ?> == true) {
                  wds_carousel<?php echo $wds; ?>.pause();
                }
              }
              circle_timer_<?php echo $wds; ?>(curent_time_deggree_<?php echo $wds; ?>);
            }
          }
        }
        if (<?php echo $enable_slideshow_music ?>) {
          document.getElementById("wds_audio_<?php echo $wds; ?>").play();
        }	 
      }
      /* Effects in part.*/		
		  function wds_set_layer_effect_in_<?php echo $wds; ?>(j, key) {
		    wds_clear_layers_effects_in_<?php echo $wds; ?>[key][j] = setTimeout(function(){
          if (wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_type"] != 'social') {
            jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_id"]).css('-webkit-animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_duration_eff_out"] / 1000 + 's').css('animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_duration_eff_out"] / 1000 + 's');			 
		        jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_id"]).removeClass().addClass( wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_layer_effect_in"] + ' animated');
		      }
          else {
            jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_id"]).css('-webkit-animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_duration_eff_out"] / 1000 + 's').css('animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_duration_eff_out"] / 1000 + 's');	
            jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_id"]).removeClass().addClass( wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_layer_effect_in"] + ' fa fa-' + wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_social_button"] + ' animated');
          }
          /* Play video on layer in.*/
          if (wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_type"] == "video") {
            if (wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_video_autoplay"] == "on") {
              jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_id"]).find("iframe").each(function () {
                jQuery(this)[0].contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                jQuery(this)[0].contentWindow.postMessage('{ "method": "play" }', "*");
              });
            }
          }
		    }, wds_data_<?php echo $wds; ?>[key]["layer_" + j + "_start"]);
		  }
      /* Effects out part.*/
		  function wds_set_layer_effect_out_<?php echo $wds; ?>(i, key) {
			  wds_clear_layers_effects_out_<?php echo $wds; ?>[key][i] = setTimeout(function() {
          if (wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_layer_effect_out"] != 'none') {
            if (wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_type"] != 'social') {
              jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_id"]).css('-webkit-animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_duration_eff_out"] / 1000 + 's').css('animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_duration_eff_out"] / 1000 + 's');				 
              jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_id"]).removeClass().addClass( wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_layer_effect_out"] + ' animated');
            }
            else {
              jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_id"]).css('-webkit-animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_duration_eff_out"] / 1000 + 's').css('animation-duration' , wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_duration_eff_out"] / 1000 + 's');
              jQuery('#wds_<?php echo $wds; ?>_slide' + wds_data_<?php echo $wds; ?>[key]["id"] + '_layer' + wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_id"]).removeClass().addClass( wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_layer_effect_out"] + ' fa fa-' + wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_social_button"] + ' animated');
            }
          }
		    }, wds_data_<?php echo $wds; ?>[key]["layer_" + i + "_end"]);
		  }
      function play_<?php echo $wds; ?>() {
        if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
          if (<?php echo $enable_slideshow_autoplay; ?> || jQuery('.wds_ctrl_btn_<?php echo $wds; ?>').hasClass('fa-pause')) {
            jQuery(".wds_line_timer_<?php echo $wds; ?>").animate({
              width: "100%"
            }, {
              duration: <?php echo $slideshow_interval * 1000; ?>,
              specialEasing: {width: "linear"}
            });
          }
        }
        window.clearInterval(wds_playInterval_<?php echo $wds; ?>);
        /* Play.*/
        wds_playInterval_<?php echo $wds; ?> = setInterval(function () {
          var iterator = 1;
          if (<?php echo $enable_slideshow_shuffle; ?>) {
            iterator = Math.floor((wds_data_<?php echo $wds; ?>.length - 1) * Math.random() + 1);
          }
          wds_change_image_<?php echo $wds; ?>(parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()), (parseInt(jQuery('#wds_current_image_key_<?php echo $wds; ?>').val()) + iterator) % wds_data_<?php echo $wds; ?>.length, wds_data_<?php echo $wds; ?>);
          if (<?php echo $carousel; ?> == true) {
            wds_carousel<?php echo $wds; ?>.next();
          }
        }, parseInt('<?php echo ($slideshow_interval * 1000); ?>') + wds_duration_for_change_<?php echo $wds; ?>);
      }
      jQuery(window).focus(function() {
        if (!jQuery(".wds_ctrl_btn_<?php echo $wds; ?>").hasClass("fa-play")) {
          if (<?php echo $enable_slideshow_autoplay; ?>) {
            play_<?php echo $wds; ?>();
            if (<?php echo $carousel; ?> == true) {
              wds_carousel<?php echo $wds; ?>.start();
            }
            if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
              if ('<?php echo $slider_row->timer_bar_type; ?>' != 'top') {
                if ('<?php echo $slider_row->timer_bar_type; ?>' != 'bottom') {
                  if (typeof circle_timer_animate_<?php echo $wds; ?> !== 'undefined') {
                    circle_timer_animate_<?php echo $wds; ?>.stop();
                  }
                  circle_timer_<?php echo $wds; ?>(curent_time_deggree_<?php echo $wds; ?>);
                }
              }
            }
          }
        }
        if (<?php echo $carousel; ?> != true) {
          var i_<?php echo $wds; ?> = 0;
          jQuery(".wds_slider_<?php echo $wds; ?>").children("span").each(function () {
            if (jQuery(this).css('opacity') == 1) {
              jQuery("#wds_current_image_key_<?php echo $wds; ?>").val(i_<?php echo $wds; ?>);
            }
            i_<?php echo $wds; ?>++;
          });
        }
      });
      jQuery(window).blur(function() {
        wds_event_stack_<?php echo $wds; ?> = [];
        window.clearInterval(wds_playInterval_<?php echo $wds; ?>);
        if (typeof jQuery().stop !== 'undefined') {
          if (jQuery.isFunction(jQuery().stop)) {
            if ('<?php echo $slider_row->timer_bar_type; ?>' == 'top' || '<?php echo $slider_row->timer_bar_type; ?>' == 'bottom') {
              jQuery(".wds_line_timer_<?php echo $wds; ?>").stop();
              if (<?php echo $carousel; ?> == true) {
                wds_carousel<?php echo $wds; ?>.pause();
              }
            }
            else if ('<?php echo $slider_row->timer_bar_type; ?>' != 'none') {
              circle_timer_animate_<?php echo $wds; ?>.stop();
              if (<?php echo $carousel; ?> == true) {
                wds_carousel<?php echo $wds; ?>.pause();
              }
            }
          }
        }
      });
    </script>
        <script>
    if (<?php echo $carousel; ?> == true) {
      var wds_currentlyMoving<?php echo $wds; ?>;
      var wds_currentCenterNum<?php echo $wds; ?>;
      var wds_carousel<?php echo $wds; ?>;
      function wds_carousel_params<?php echo $wds; ?>() {
        var width, height;
        var slide_orig_width = <?php echo $image_width; ?>;
        var slide_orig_height = <?php echo $image_height; ?>;
        var slide_width = jQuery("#wds_container1_<?php echo $wds; ?>").parent().width();
        var par = 1, par1 = 1;
        var ratio = slide_width / slide_orig_width;
        <?php
        if ($slider_row->full_width) {
          ?>
       
        ratio = jQuery(window).width() / slide_orig_width;
        slide_orig_width = jQuery(window).width() - <?php echo 2 * $slider_row->glb_margin; ?>;
        slide_orig_height = <?php echo $image_height; ?> * slide_orig_width / <?php echo $image_width; ?>;
        slide_width = jQuery(window).width() - <?php echo 2 * $slider_row->glb_margin; ?>;
        wds_full_width_<?php echo $wds; ?>();
          <?php
        }
        ?>
        var slide_height = slide_orig_height;
        if (slide_orig_width > slide_width) {
          slide_height = Math.floor(slide_width * slide_orig_height / slide_orig_width);
        }

        width = slide_width;
        height = slide_height;    
        var larg_width,img_height,parF=1;
        if (width < <?php echo $carousel_width; ?>) {
          par = width / <?php echo $carousel_width; ?>;
        }
         par1 = <?php echo $image_height; ?> * par / height;
        if (width < <?php echo $carousel_width; ?>) { 
          jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>, #wds_container2_<?php echo $wds; ?>").height(height *  par1+ <?php echo ($filmstrip_direction == 'horizontal' ? $filmstrip_height : 0); ?>);
          jQuery(".wds_slideshow_image_container_<?php echo $wds; ?>").height(height * par1);
          jQuery(".wds_btn_cont wds_contTableCell<?php echo $wds; ?>").height(height * par1  );
          jQuery(".wds_slide_container_<?php echo $wds; ?>").height(height * par1);
        }
        <?php
        if ($slider_row->full_width) {
          ?>
          parF = <?php echo $image_width; ?> *  <?php echo $carousel_image_parameters; ?>;
          jQuery(".wds_slideshow_image_wrap_<?php echo $wds; ?>, #wds_container2_<?php echo $wds; ?>").height(height *par1+<?php echo ($filmstrip_direction == 'horizontal' ? $filmstrip_height : 0); ?>);
          jQuery(".wds_slideshow_image_container_<?php echo $wds; ?>").height(height * par1);
          jQuery(".wds_btn_cont wds_contTableCell<?php echo $wds; ?>").height(height* par1);
          jQuery(".wds_slide_container_<?php echo $wds; ?>").height(height * par1);
         
          <?php
        }
        if ($carousel_image_counts  >  $slides_count ) {
          $carousel_image_counts = $slides_count; 
        }
        if ($carousel_image_parameters > 1) {
          $carousel_image_parameters = 1;
        }
        if($enable_slideshow_autoplay == true){
          $interval = $slideshow_interval;
         
        }
        else {
          $interval = 0; 
        }
          
        ?>         
        var slideshow_filmstrip_container_width = '<?php echo $filmstrip_direction; ?>' == 'horizontal' ? 0 : jQuery( ".wds_slideshow_filmstrip_container_<?php echo $wds; ?>").width();
        jQuery(".wds_slideshow_dots_container_<?php echo $wds; ?>").css({width: (<?php echo $image_width; ?> * par),left:(width -<?php echo $image_width; ?> * par - slideshow_filmstrip_container_width) / 2}); 
        var orig_width = <?php echo $image_width; ?> ; 
        var img_width =  Math.min(larg_width, orig_width);   
        wds_carousel<?php echo $wds; ?> = jQuery(".wds_slide_container_<?php echo $wds; ?>").featureCarouselslider({
          containerWidth:       width,
          containerHeight:      height,
          largeFeatureWidth:    <?php echo $image_width; ?> * par,
          largeFeatureHeight:   <?php echo $image_height; ?> * par, 
          fit_containerWidth:   <?php echo $carousel_fit_containerWidth; ?>, 
          smallFeaturePar:      <?php echo $carousel_image_parameters; ?>,
          featuresArray:        [],          
          timeoutVar:           null,
          rotationsRemaining:   0,
          parametr:             par,
          parf:                 parF,        
          data:                 wds_data_<?php echo $wds; ?>,
          autoPlay:             <?php echo $interval * 1000; ?>,
          interval:             <?php echo $slideshow_interval * 1000; ?>, 
          imagecount:           <?php echo $carousel_image_counts; ?>,
          wds_number:           <?php echo $wds; ?>, 
          startingFeature:      wds_currentCenterNum<?php echo $wds; ?>,
          carouselSpeed:        wds_transition_duration_<?php echo $wds; ?>,          
          borderWidth:		      0
        });        
      }
      jQuery(document).ready(function() {
        wds_currentlyMoving<?php echo $wds; ?> = false;
        wds_currentCenterNum<?php echo $wds; ?> = <?php echo $slider_row->start_slide_num; ?>;
        jQuery(".wds_left-ico_<?php echo $wds; ?>").click(function () {
          wds_carousel<?php echo $wds; ?>.prev();
        });
        jQuery(".wds_right-ico_<?php echo $wds; ?>").click(function () {
          wds_carousel<?php echo $wds; ?>.next();
        });
      });
      jQuery(window).resize(function() {
        wds_carousel<?php echo $wds; ?>.pause();
        wds_carousel_params<?php echo $wds; ?>();
        if (!jQuery(".wds_ctrl_btn_<?php echo $wds; ?>").hasClass("fa-play")) {
          wds_carousel<?php echo $wds; ?>.start();
        }
      });
      jQuery(window).load(function() {
        wds_carousel_params<?php echo $wds; ?>();
      });
    }
    </script>
    <?php
    if ($from_shortcode) {
      return;
    }
    else {
      die();
    }
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