<?php

class WDSModelWDSExport {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct() {
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////

  public function export_one() {
    global $wpdb;
    $slider_id = WDW_S_Library::get('current_id', 0);
    $sliders_to_export = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wdsslider where id="%d"', $slider_id));
    foreach ($sliders_to_export as $slider) {
      $slides = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wdsslide WHERE slider_id="%d"', $slider->id));
      if ($slides) {
        foreach ($slides as $slide) {
          $slidelayers = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wdslayer WHERE slide_id="%d"', $slide->id));
          $slide->slidelayers = $slidelayers;
        }
      }
      $slider->slides = $slides;
    }
    return $sliders_to_export;
  }

  public function export_full() {
    global $wpdb;
    $slider_ids_string = WDW_S_Library::get('slider_ids', 0);
    $slider_ids_string = rtrim($slider_ids_string, ",");
    $slider_ids = explode(',', $slider_ids_string);
    $sliders_to_export = array();
    if ($slider_ids_string == 'all') {
      $sliders = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'wdsslider');
    }
    else {
      $sliders = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'wdsslider WHERE id IN (' . $slider_ids_string . ')');
    }
    foreach ($sliders as $slider) {
      array_push($sliders_to_export, $slider);
    }
    foreach ($sliders_to_export as $slider) {
      $slides = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wdsslide WHERE slider_id="%d"', $slider->id));
      if ($slides) {
        foreach ($slides as $slide) {
          $slidelayers = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wdslayer WHERE slide_id="%d"', $slide->id));
          $slide->slidelayers = $slidelayers;
        }
      }
      $slider->slides = $slides;
    }
    return $sliders_to_export;
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