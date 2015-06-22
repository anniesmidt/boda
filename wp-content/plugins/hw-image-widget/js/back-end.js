jQuery(document).ready(function() {
	
	hwim = {
		firstImageOpen: true,
		
		calcAspectRatio: function(id, axis) {
			if (jQuery(id + ' .keep-aspect-ratio').prop('checked')) {
				var display_width = jQuery(id + ' .display-width').attr('value');
				var display_height = jQuery(id + ' .display-height').attr('value');
				var original_width = jQuery(id + ' .original-width').attr('value');
				var original_height = jQuery(id + ' .original-height').attr('value');
				var aspect_ratio = 0;

				if (this.isValidInt(display_width) && axis == 'x') {
					aspect_ratio = original_width / original_height;
					jQuery(id + ' .display-height').attr('value', Math.round(display_width / aspect_ratio));
				}

				if (this.isValidInt(display_height) && axis == 'y') {
					aspect_ratio = original_height / original_width;
					jQuery(id + ' .display-width').attr('value', Math.round(display_height / aspect_ratio));
				}

				if (display_width == '' && axis == 'x') {
					jQuery(id + ' .display-height').attr('value', '');
				}

				if (display_height == '' && axis == 'y') {
					jQuery(id + ' .display-width').attr('value', '');
				}
			}
		},

		closeTextEditor: function(evt) {
			evt.preventDefault();
			
			if (evt.data.save == true) {
				
				if (jQuery('#wp-hwim-tmce-wrap').hasClass('tmce-active')) {
					content = tinyMCE.get('hwim-tmce').getContent();
				} else {
					content = window.switchEditors.wpautop(tinyMCE.DOM.get('hwim-tmce').value);
				}
				
				jQuery(hwim.editorForId + ' .text').attr('value', content);
				jQuery(hwim.editorForId + ' .text-preview').html(content.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, ' '));
				hwim.updateCustomizer(hwim.editorForId);
			}
			
			jQuery('#hwim-te-backdrop').css('display', 'none');
			jQuery('#hwim-te').css('display', 'none');
		},
		
		displaySize: function(id, val) {
			if (val == 'fixed') {
				jQuery(id + ' div.relative-size').slideUp(100, function() {
					jQuery(id + ' div.fixed-size').slideDown(200);
				});
			} else {
				jQuery(id + ' div.fixed-size').slideUp(100, function() {
					jQuery(id + ' div.relative-size').slideDown(200);
				});
			}
		},
		
		imageEmbeded: function(id, image) {
			jQuery(id + ' .display-width').attr('value', image.width);
			jQuery(id + ' .display-height').attr('value', image.height);
			jQuery(id + ' .original-width').attr('value', image.width);
			jQuery(id + ' .original-height').attr('value', image.height);
			jQuery(id + ' .remove-image-link').show();
			jQuery(id + ' .img-thumb').html('<img src="' + image.url + '" style="max-width: 100%;">');
			jQuery(id + ' .src').attr('value', image.url);
			jQuery(id + ' .alt').attr('value', image.alt);
		},
		
		imageSelected: function(id, selectedSize, image) {
			imageSize = image.sizes[selectedSize];
			
			jQuery(id + ' .remove-image-link').show();
			jQuery(id + ' .img-thumb').html('<img src="' + imageSize.url + '" style="max-width: 100%;">');
			jQuery(id + ' .src').attr('value', imageSize.url);
			jQuery(id + ' .display-width').attr('value', imageSize.width);
			jQuery(id + ' .display-height').attr('value', imageSize.height);
			jQuery(id + ' .original-width').attr('value', imageSize.width);
			jQuery(id + ' .original-height').attr('value', imageSize.height);
			jQuery(id + ' .alt').attr('value', image.alt);

			if (image.title != '' && jQuery(id + ' .title').attr('value') == '') {
				jQuery(id + ' .title').attr('value', image.title);
			}
		},
		
		init: function() {
			// Bind editor buttons.
			jQuery('#hwim-te-backdrop').bind('click', {save: false}, this.closeTextEditor);
			jQuery('.hwim-te-close').bind('click', {save: false}, this.closeTextEditor);
			jQuery('.hwim-te-btn-discard').bind('click', {save: false}, this.closeTextEditor);
			jQuery('.hwim-te-btn-save').bind('click', {save: true}, this.closeTextEditor);
		},
		
		isValidInt: function(val) {
			var intRegex = /^\d+$/;
			return intRegex.test(val);
		},
		
		// Keeps track of aspect ratio checkbox.
		keepAspectRatio: function(id) {
			if (jQuery(id + ' .keep-aspect-ratio').prop('checked')) {
				this.calcAspectRatio(id, 'x');
			}
		},
		
		mediaClose: function() {
			// Restore original functions.
			wp.media.editor.send.attachment = hwim.insert;
			wp.media.string.image = hwim.embed;
		},
		
		openTextEditor: function(id) {
			hwim.editorForId = id;
			
			// Ugly way of switching to WYSIWYG view before showing the editor (don't see a way to set HTML content manually).
			if (jQuery('#wp-hwim-tmce-wrap').hasClass('html-active')) {
				jQuery('#hwim-tmce-tmce').click();
			}
			
			// Set data to tmce.
			tinyMCE.get('hwim-tmce').setContent(jQuery(id + ' .text').attr('value'));
			
			// Display editor.
			jQuery('#hwim-te').css('display', 'block');
			jQuery('#hwim-te-backdrop').css('display', 'block');
		},
		
		removeImage: function(id) {
			jQuery(id + ' .remove-image-link').hide();
			jQuery(id + ' .img-thumb').html('');
			jQuery(id + ' .src').attr('value', '');
			jQuery(id + ' .display-width').attr('value', '');
			jQuery(id + ' .display-height').attr('value', '');
			jQuery(id + ' .original-width').attr('value', '');
			jQuery(id + ' .original-height').attr('value', '');
			hwim.updateCustomizer(id);
		},
		
		selectImage: function(id) {
			
			// Backup original functions.
			hwim.insert = wp.media.editor.send.attachment;
			hwim.embed = wp.media.string.image;
						
			// Open insert media lightbox.
			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
				wp.media.editor.open(id, {multiple: false, title: 'HW Image Widget', type: 'image'});
			}

			// Image was selected from Media Library.
			wp.media.editor.send.attachment = function(selection, image) {
				hwim.imageSelected(id, selection.size, image)
				hwim.mediaClose();
				hwim.updateCustomizer(id);
			};

			// Image was selected by URL.
			wp.media.string.image = function (image) {
				hwim.imageEmbeded(id, image);
				hwim.mediaClose();
				hwim.updateCustomizer(id);
			}
			
			// Lightbox was closed, make sure to restore backed up functions.
			if (hwim.firstImageOpen) {
				wp.media.frame.on('escape', function() {
					hwim.mediaClose();
				});
			}
			
			hwim.firstImageOpen = false;
		},
		
		target: function(id) {
			if (jQuery(id + ' .target-option').val() != 'other') {
				jQuery(id + ' .target-name').hide();
			} else {
				jQuery(id + ' .target-name').show();
			}
		},
		
		rel: function(id) {
			if (jQuery(id + ' .rel-option').val() != 'other') {
				jQuery(id + ' .rel-name').hide();
			} else {
				jQuery(id + ' .rel-name').show();
			}
		},
		
		updateCustomizer: function(id) {
			jQuery(id + ' .title').trigger('change');
		}
	};
	hwim.init();
});
