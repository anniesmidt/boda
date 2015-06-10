(function($){
  $.fn.featureCarouselslider = function (options) {
    // Adds support for multiple carousels on one page.
    if (this.length > 1) {
      this.each(function() {
        $(this).featureCarouselslider(options);
      });
      return this;
    }

    // override the default options with user defined options
    options = $.extend({}, $.fn.featureCarouselslider.defaults, options || {});

    /* These are univeral values that are used throughout the plugin. Do not modify them
     * unless you know what you're doing. Most of them feed off the options
     * so most customization can be achieved by modifying the options values 
    */
    var pluginData = {      
     
      containerWidth:       0,
      containerHeight:      0,
      largeFeatureWidth:    0,
      largeFeatureHeight:   0,
      smallFeatureHeight:   0,
      smallFeatureWidth:    0,       
      totalFeatureCount:    $(this).children().first().children().first().children("span").length,
      featuresContainer:    $(this),
      featuresArray:        [],
      containerIDTag:       "." + $(this).attr("id"),
      timeoutVar:           null,
      rotationsRemaining:   0,
      itemsToAnimate:       0,
      borderWidth:		      0,     
      autoTime:             0   
    };

    /**
     * Function to preload the images in the carousel if desired.
     * This is not recommended if there are a lot of images in the carousel because
     * it may take a while. Functionality does not depend on preloading the images 
     */ 
    var preload = function(callback) {
      callback(); 
    }
 
    // Gets the feature container based on the number
    var getContainer = function(featureNum) {
      return pluginData.featuresArray[featureNum-1];
    }

    // get previous feature number
    var getPreviousNum = function(num) {
      if ((num - 1) == 0) {
        return pluginData.totalFeatureCount;
      } else {
        return num - 1;
      }
    }

    // get next feature number
    var getNextNum = function(num) {
      if ((num + 1) > pluginData.totalFeatureCount) {
        return 1;
      } else {
        return num + 1;
      }
    }

    /**
     * Because there are several options the user can set for the width and height
     * of the feature images, this function is used to determine which options were set
     * and to set the appropriate dimensions used for a small and large feature	
     */
    var setupFeatureDimensions = function() {
      // Set the height and width of the entire carousel container
      options.containerWidth = pluginData.featuresContainer.width();
      options.containerHeight = pluginData.featuresContainer.height();
    
      // Grab the first image for reference
      var $firstFeatureImage = $(pluginData.containerIDTag).find(".wds_slider_car_image" + options.wds_number + ":first");
 
      // Large Feature Width
      if (options.largeFeatureWidth > 1) {
        pluginData.largeFeatureWidth = options.largeFeatureWidth;
      }
	    else if (options.largeFeatureWidth > 0 && options.largeFeatureWidth < 1) {
        pluginData.largeFeatureWidth = $firstFeatureImage.width() * options.largeFeatureWidth;
      }
      else {
        pluginData.largeFeatureWidth = $firstFeatureImage.outerWidth();
      }
      // Large Feature Height
      if (options.largeFeatureHeight > 1) {
        pluginData.largeFeatureHeight = options.largeFeatureHeight;
      }
      else if (options.largeFeatureHeight > 0 && options.largeFeatureHeight < 1) {
        pluginData.largeFeatureHeight = $firstFeatureImage.height() * options.largeFeatureHeight;
      }
      else {
        pluginData.largeFeatureHeight = $firstFeatureImage.outerHeight();
      }
    }

    /**
     * Function to take care of setting up various aspects of the carousel,
     * most importantly the default positions for the features	
     */
    var setupCarousel = function() {
      // Set the total feature count to the amount the user wanted to cutoff
      if (options.displayCutoff > 0 && options.displayCutoff < pluginData.totalFeatureCount) {
        pluginData.totalFeatureCount = options.displayCutoff;
      }

      // Fill in the features array .
      pluginData.featuresContainer.find(".wds_slider_car_image" + options.wds_number).each(function (index) {
        if (index < pluginData.totalFeatureCount) {
          pluginData.featuresArray[index] = $(this);
        }
      });
 
      // Determine the total border width around the feature if there is one.
      if (pluginData.featuresContainer.find(".wds_slider_car_image" + options.wds_number).first().css("borderLeftWidth") != "medium") {
        pluginData.borderWidth = parseInt(pluginData.featuresContainer.find(".wds_slider_car_image" + options.wds_number).first().css("borderLeftWidth"))*2;
      }
      // Place all the features in a center hidden position to start off.
      if (options.imagecount != 1) {
        pluginData.featuresContainer
        .find(".wds_slider_car_image" + options.wds_number).each(function () {
          // Center all the features in the middle and hide them.
          $(this).css({
            'left': (options.containerWidth / 2) - (options.largeFeatureWidth * options.smallFeaturePar / 2) - (pluginData.borderWidth / 2) - options.parf,
            'width': options.largeFeatureWidth * options.smallFeaturePar * options.parametr / 100,
            'height': options.largeFeatureHeight * options.smallFeaturePar * options.parametr / 100,
            'top': options.smallFeatureOffset + options.topPadding + options.parf,
            'opacity': 0,
            'filter': 'Alpha(opacity=0)'
          });
        });
      } 
      else {
        pluginData.featuresContainer
        .find(".wds_slider_car_image" + options.wds_number).each(function () {
          // Center all the features in the middle and hide them.
          $(this).css({
            'left': 0,
            'width': options.largeFeatureWidth * options.smallFeaturePar,
            'height': options.largeFeatureHeight * options.smallFeaturePar,
            'top': 0,
            'opacity': 0,
            'filter': 'Alpha(opacity=0)'
          });
        });
      }        
      // Set position to relative of captions if displaying below image.
      if (options.captionBelow) {
        pluginData.featuresContainer.find(".wds_slider_car_image" + options.wds_number).css('position','absolute');
      }

      // Figure out number of items that will rotate each time.
      if (pluginData.totalFeatureCount < options.imagecount) {
        pluginData.itemsToAnimate = pluginData.totalFeatureCount;  
      }  else {
        pluginData.itemsToAnimate = options.imagecount + 2 ;
      }       
    }

    /**
     * Here all the position data is set for the features.
     * This is an important part of the carousel to keep track of where
     * each feature within the carousel is	 
     */
    var setupFeaturePositions = function() {
      // give all features a set number that won't change so they remember their
      // original order 
      $.each(pluginData.featuresArray, function (i) {
        $(this).data('setPosition', i + 1);
      });

      // Go back one - This is done because we call the move function right away, which
      // shifts everything to the right. So we set the current center back one, so that
      // it displays in the center when that happens
      var oneBeforeStarting = getPreviousNum(window["wds_currentCenterNum" +options.wds_number]);

      window["wds_currentCenterNum" +options.wds_number] = oneBeforeStarting;

      // Center feature will be position 1
      var $centerFeature = getContainer(oneBeforeStarting);
      $centerFeature.data('position', 1);

      // Everything before that center feature...
      var $prevFeatures = $centerFeature.prevAll();
      $prevFeatures.each(function (i) {
        $(this).data('position', (pluginData.totalFeatureCount - i));
      });
        
      // And everything after that center feature...
      var $nextFeatures = $centerFeature.nextAll();
      $nextFeatures.each(function (i) {
        if ($(this).data('setPosition') != undefined) {
          $(this).data('position',(i + 2));
        }
      });
    }

    /**
     * This function will set the autoplay for the carousel to
     * automatically rotate it given the time in the options
     * pass in TRUE to just clear the timer
     */
    var setTimer = function(stop) {
      return;
      // clear the timeout var if it exists
      clearTimeout(pluginData.timeoutVar);

      // set interval for moving if autoplay is set
      if (!stop && options.autoPlay != 0) {
        var autoTime = (Math.abs(options.autoPlay) < options.carouselSpeed) ? options.carouselSpeed : Math.abs(options.autoPlay);
        pluginData.timeoutVar = setTimeout(function () {
          (options.autoPlay > 0) ? initiateMove(true,1) : initiateMove(false,1);
        }, autoTime);
      }
    }
 
    // This is a helper function for the animateFeature function that
    // will update the positions of all the features based on the direction
    var rotatePositions = function(direction) {
      $.each(pluginData.featuresArray, function () {
        var newPos;
        if (direction == false) {
          newPos = getNextNum($(this).data().position);
        } else {
          newPos = getPreviousNum($(this).data().position);
        }

        $(this).data('position',newPos);
      });
    }

    /* Animate the given feature to the given location. Valid locations are "left", "right", "center", "hidden".*/
    var animateFeature = function($feature, direction, onload) {
      if (typeof onload == "undefined") {
        var onload = 0;
      }
      $feature.find(".wds_slideshow_image_" + options.wds_number).children().each(function() {
        jQuery(this).hide();
      });
      $feature.find(".wds_video_hide" + options.wds_number).each(function() {
        jQuery(this).show();
      });
      var new_width, new_height, new_top, new_left, new_zindex, new_padding, new_fade, new_fade1, new_left1;
      // Determine the old and new positions of the feature.
      var oldPosition = $feature.data('position');
      var newPosition;
      if (direction == true) {
        newPosition = getPreviousNum(oldPosition);
      }
      else {
        newPosition = getNextNum(oldPosition);
      }
      // Callback for moving out of center pos.
      if (oldPosition == 1) {
        options.leavingCenter($feature);
      }
      // Caculate new new css values depending on where the feature will be located center.
      if (newPosition == 1) {
        new_width = pluginData.largeFeatureWidth;        
        new_height = pluginData.largeFeatureHeight;
        new_top =(options.containerHeight / 2 - new_height / 2 );
        new_zindex = $feature.css("z-index");
        new_left = (options.containerWidth / 2) - (pluginData.largeFeatureWidth / 2) - (pluginData.borderWidth / 2);
        new_fade = 1.0;
        new_fade1 = 100;
        $feature.find(".wds_slideshow_image_" + options.wds_number).children().each(function() {
          jQuery(this).show();
        });
        $feature.find(".wds_video_hide" + options.wds_number).each(function() {
          jQuery(this).hide();
        });
      }
      else {
        // Left.
        if (newPosition <= pluginData.totalFeatureCount && newPosition > pluginData.totalFeatureCount - options.imagecount / 2 + 1) {
          new_width = (pluginData.largeFeatureWidth * Math.pow(options.smallFeaturePar, pluginData.totalFeatureCount - newPosition + 1));      
          new_height = (pluginData.largeFeatureHeight * Math.pow(options.smallFeaturePar, pluginData.totalFeatureCount - newPosition + 1));
          new_top = (options.containerHeight/ 2  - new_height / 2 ) ;         
          new_fade = 1;           
          new_fade1 = 100;          
          if(options.fit_containerWidth == false && options.smallFeaturePar < 1){
            new_left = (options.containerWidth / 2 - pluginData.largeFeatureWidth / 2)-(pluginData.largeFeatureWidth * (1 - options.smallFeaturePar) * options.smallFeaturePar * (pluginData.totalFeatureCount - newPosition + 1));             
          } else if(options.fit_containerWidth == false && options.smallFeaturePar == 1){
            new_left = (options.containerWidth / 2 - pluginData.largeFeatureWidth / 2)-(pluginData.largeFeatureWidth * ( options.smallFeaturePar) * options.smallFeaturePar * (pluginData.totalFeatureCount - newPosition + 1));             
          } else {
              new_left1 = (options.containerWidth / 2 - pluginData.largeFeatureWidth / 2) / (options.imagecount / 2 - 0.5);
              new_left =  ((options.containerWidth / 2) - (pluginData.largeFeatureWidth / 2) - (pluginData.borderWidth / 2)) - (new_left1 * (pluginData.totalFeatureCount - newPosition+1  ));             
          }                        
        }
        // Right.
        else if (newPosition >= 2 && newPosition <= options.imagecount / 2 + 1) {
          new_width = (pluginData.largeFeatureWidth * Math.pow(options.smallFeaturePar,newPosition - 1));
          new_height =( pluginData.largeFeatureHeight * Math.pow(options.smallFeaturePar, newPosition - 1));
          new_top = ( options.containerHeight / 2 - new_height / 2);
          new_fade = 1;
          new_fade1 = 100;
          if (options.fit_containerWidth == false && options.smallFeaturePar < 1) {
            new_left = (options.containerWidth / 2 + pluginData.largeFeatureWidth / 2) + (pluginData.largeFeatureWidth * options.smallFeaturePar * (1 - options.smallFeaturePar) * (newPosition - 1)) - new_width;
          } else if (options.fit_containerWidth == false && options.smallFeaturePar == 1) {
            new_left = (options.containerWidth / 2 + pluginData.largeFeatureWidth / 2) + (pluginData.largeFeatureWidth * options.smallFeaturePar * (options.smallFeaturePar) * (newPosition - 1)) - new_width;
          } else {
            new_left1 = ( (options.containerWidth / 2 -pluginData.largeFeatureWidth / 2)) / (options.imagecount / 2 - 0.5);
            new_left = (options.containerWidth / 2 + pluginData.largeFeatureWidth / 2) - new_width + (new_left1 * (newPosition - 1));
          }
        }
        // Hidden.
        else {
          new_left = (options.containerWidth / 2) - (pluginData.largeFeatureWidth * options.smallFeaturePar / 2) - (pluginData.borderWidth / 2);
          new_fade = 0;
          new_fade1 = 0;
        }
      }
      // This code block takes care of hiding the feature information if the feature is leaving the center.
      if (oldPosition == 1) {
        // Slide up the story information.
        $feature.find(".wds_slider_car_image" + options.wds_number).hide();
      }

      // Animate the feature div to its new location.
      $feature
        .animate(
          {
            width: new_width,
            height: new_height,
            top: new_top,
            left: new_left,                      
            opacity: new_fade,
            filter: new_fade1
          },
          onload ? 0 : options.carouselSpeed,
          options.animationEasing,
          function () {
            // Take feature info out of hiding if new position is center
            if (newPosition == 1) {
              // need to set the height to auto to accomodate caption if displayed below image
              if (options.captionBelow)
               $feature.css('height','auto');
              // fade in the feature information
              $feature.find(".wds_slider_car_image" + options.wds_number)
                .fadeTo("fast",0.85);
              // callback for moved to center
              options.movedToCenter($feature);
            }
            // decrement the animation queue
            pluginData.rotationsRemaining = pluginData.rotationsRemaining - 1;
            // have to change the z-index after the animation is done
            $feature.css("z-index", new_zindex);
            // change trackers if using them
            if (options.trackerIndividual || options.trackerSummation) {
              // just update the tracker once; once the new center feature has arrived in center
              if (newPosition == 1) {
                // figure out what item was just in the center, and what item is now in the center
                var newCenterItemNum = pluginData.featuresContainer.find(".wds_slider_car_image" + options.wds_number).index($feature) + 1;
                var oldCenterItemNum;
                if (direction == false) {
                  oldCenterItemNum = getNextNum(newCenterItemNum);
                }
                else {
                  oldCenterItemNum = getPreviousNum(newCenterItemNum);
                }
              }
            }

            // did all the the animations finish yet?
            var divide = pluginData.rotationsRemaining / pluginData.itemsToAnimate;
            if (divide % 1 == 0) {
              // Set moving to false.
              window["wds_currentlyMoving" +options.wds_number]   = false;
              // Change positions for all items.
              rotatePositions(direction);

              // Move carousel again if queue is not empty.
              if (pluginData.rotationsRemaining > 0)
                move(direction);
            }
            // Reset timer and auto rotate again.
            setTimer(false); 
          }
        )
        .end();
    }

    /* Move the carousel to the left or to the right. The features that
     * will move into the four positions are calculated and then animated
     * rotate to the RIGHT when direction is TRUE and
     * rotate to the LEFT when direction is FALSE.
     */
    var move = function(direction, onload) {
      // Set the carousel to currently moving
      window["wds_currentlyMoving" + options.wds_number]  = true;
      // Obtain the new feature positions based on the direction that the carousel is moving
      var $newCenter, $newLefts, $newRights, $newHidden, $curNum;

      if (direction == true) {
        // Shift features to the left
        $newCenter = getContainer(getNextNum(window["wds_currentCenterNum" +options.wds_number]));
        $newLefts = [];
        $curNum = window["wds_currentCenterNum" +options.wds_number];        
        for (var i = 1; i <= options.imagecount / 2 + 1; ++i) {
          $newLefts.push(getContainer($curNum));
          $curNum = getPreviousNum($curNum);
        }
        $newRights = [];
        $curNum = getNextNum(window["wds_currentCenterNum" + options.wds_number]);
        for (var i = 1; i <= options.imagecount / 2 + 1; ++i) {
          $curNum = getNextNum($curNum);
          $newRights.push(getContainer($curNum));
        }
        window["wds_currentCenterNum" + options.wds_number] = getNextNum(window["wds_currentCenterNum" + options.wds_number]);
      }
      else {
        $newCenter = getContainer(getPreviousNum(window["wds_currentCenterNum" +options.wds_number]));
        $newLefts = [];
        $curNum = getPreviousNum(window["wds_currentCenterNum" +options.wds_number]);        
        for (var i = 1; i <= options.imagecount / 2 +1; ++i) {
          $curNum = getPreviousNum($curNum);   
          $newLefts.push(getContainer($curNum));
        }
        $newRights = [];
        $curNum = window["wds_currentCenterNum" + options.wds_number];
        for (var i = 1; i <= options.imagecount / 2 + 1; ++i) {
          $newRights.push(getContainer($curNum));
          $curNum = getNextNum($curNum);
        }
        window["wds_currentCenterNum" +options.wds_number] = getPreviousNum(window["wds_currentCenterNum" + options.wds_number]);
      }
      // Animate the features into their new positions.
      for (i = 0; i < $newLefts.length; i++) {
        $newLefts[i].css("z-index", $newLefts.length - i + 3);
        animateFeature($newLefts[i], direction, onload);
      }
      $newCenter.css("z-index", Math.max($newLefts.length, $newRights.length) + 4);
      animateFeature($newCenter, direction, onload);
      for (i = 0; i < $newRights.length; i++) {
        $newRights[i].css("z-index", $newRights.length - i + 3);
        animateFeature($newRights[i], direction, onload);
      }
    }

    // This is used to relegate carousel movement throughout the plugin.
    // It will only initiate a move if the carousel isn't currently moving.
    // It will set the animation queue to the number of rotations given.
    var initiateMove = function(direction, rotations, onload) {
      if (typeof onload == "undefined") {
        var onload = 0;
      }
      if (window["wds_currentlyMoving" +options.wds_number]  == false) {
        var queue = rotations * pluginData.itemsToAnimate ;
        pluginData.rotationsRemaining = queue ;      
        window["wds_set_filmstrip_class_" + options.wds_number]();
        window["wds_set_dots_class_" + options.wds_number]();
        move(direction, onload);
      }
    }

    // Add event listener to all clicks within the features container
    // This is done to disable any links that aren't within the center feature
    $("a", pluginData.containerIDTag).on("click", function (event) {
      // travel up to the container
      var $parents = $(this).parentsUntil(pluginData.containerIDTag);
      // now check each of the feature divs within it
      $parents.each(function () {
        var position = $(this).data('position');
        // if there are more than just feature divs within the container, they will
        // not have a position and it may come back as undefined. Throw these out
        if (position != undefined) {
          // if any of the links on a feature OTHER THAN the center feature were clicked,
          // initiate a carousel move but then throw the link action away
          if (position != 1) {
            if (position == pluginData.totalFeatureCount) {
              initiateMove(false,1);
            } else if (position == 2) {
              initiateMove(true, 1);
            }
            event.preventDefault();
            return false;
          // if the position WAS the center (i.e. 1), fire callback
          } else {
            options.clickedCenter($(this));
          }
        }
      });
    });

    // Public functions.
    this.initialize = function () {
      // Call the preloader and pass in our callback, which is just a slew of function calls
      // that should only be executed after the images have been loaded
      preload(function () {
        setupFeatureDimensions();
        setupCarousel();
        if (window["wds_currentlyMoving" +options.wds_number]  == false ) {
          setupFeaturePositions();
          initiateMove(true, 1, 1);
        }
      }); 
      return this;
    };

    this.next = function() {
      initiateMove(true, 1);
    }
    this.prev = function () {
      initiateMove(false, 1);
    }
    this.shift = function (that) {
      var position = $(that).data('position');
      if (position == 1) {
        return;
      }
      if (position > pluginData.totalFeatureCount / 2 + 1) {
        initiateMove(false, pluginData.totalFeatureCount - position + 1);
        options.carouselSpeed = 400;
        options.animationEasing;
      }
      else {
        initiateMove(true, position - 1);
        options.carouselSpeed = 400;
        options.animationEasing;
      }
    }
    this.pause = function () {
      options.autoPlay = false;
      setTimer(true);
    }
    this.start = function () {
      options.autoPlay = options.interval;
      setTimer(false);
    }
    // Initialize the plugin.
    return this.initialize();
  };

  $.fn.featureCarouselslider.defaults = {
    // If zero, take original width and height of image
    // If between 0 and 1, multiply by original width and height (acts as a percentage)
    // If greater than one, use as a forced width/height for all of the images
    largeFeatureWidth :   0,
    largeFeatureHeight:		0,
    containerWidth:       0,
    containerHeight:      0,
    fit_containerWidth:   0,
    wds_number:           0,
    // how much to pad the top of the carousel
    topPadding:           20,
    // spacing between the sides of the container
    sidePadding:          50,
    // the additional offset to pad the side features from the top of the carousel
    smallFeatureOffset:		50,
    //smallFeature parameters
    smallFeaturePar:      0,
    // indicates which feature to start the carousel at
    startingFeature:      1,
    // speed in milliseconds it takes to rotate the carousel
    carouselSpeed:        400,
    // time in milliseconds to set interval to autorotate the carousel
    // set to zero to disable it, negative to go left
    autoPlay:             4000,
    interval:             true,
    //imagecounts
    imagecount:           7,
    // with autoplay enabled, set this option to true to have the carousel pause rotating
    // when a user hovers over any feature
    pauseOnHover:         true,
    // with autoplay enabled, set this option to completely stop the autorotate functionality
    stopOnHover:          false,
    // numbered blips can appear and be used to track the currently centered feature, as well as 
    // allow the user to click a number to move to that feature. Set to false to not process these at all
    // and true to process and display them//and true to process and display them
    trackerIndividual:    true,
    // a summation of the features can also be used to display an "x Of y" style of tracking
    // this can be combined with the above option as well
    trackerSummation:     true,
    // Will only display this many features in the carousel
    // set to zero to disable
    displayCutoff:        0,
    data:                 "",
    // an easing can be specified for the animation of the carousel
    animationEasing:      'swing',
    // display captions below the image instead of on top
    captionBelow:         false,
    parf:                 0,
    // callback function for when a feature has animated to the center
    movedToCenter:        $.noop,
    // callback function for when feature left center
    leavingCenter:        $.noop,
    // callback function for when center feature was clicked
    clickedCenter:        $.noop
  };
})(jQuery);