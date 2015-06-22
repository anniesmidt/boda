<?php
	// Image is set?
	if ( $instance['src'] != '' ) {
		$img = '<img class="hwim-image" src="' . $instance['src'] . '" ';

		// Alt for image.
		if ( $instance['alt'] != '' ) {
			$img .= 'alt="' . esc_attr( $instance['alt'] ) . '" title="' .  esc_attr( $instance['alt'] ) . '" ';
		}

		// Compile the style param.
		$style = array();

		if ( $instance['display_size'] == 'responsive' ) {
			$style['max-width'] = '100%';
			if ($instance['fill_width'] === true) {
				$style['height'] = 'auto';
				$style['width'] = '100%';
			}
		} else {
			if ( $instance['display_width'] != '' ) {
				$style['width'] = $instance['display_width'] . 'px';
			}
			if ( $instance['display_height'] != '' ) {
				$style['height'] = $instance['display_height'] . 'px';
			}
		}

		if ( count( $style ) > 0 ) {
			$img .= 'style="';
			foreach ( $style as $key => $value ) {
				$img .= $key . ':' . $value . ';';
			}
			$img .= '" ';
		}

		$img .= '>';
		
		// Linked?
		if ( $instance['url'] != '' ) {
			$a = '<a href="' . esc_attr( $instance['url'] ) . '"';
			if ( $instance['target_option'] == 'other' ) {
				$a .= ' target="' . esc_attr( $instance['target_name'] ) . '"';
			} elseif ( $instance['target_option'] != '' ) {
				$a .= ' target="' . esc_attr( $instance['target_option'] ) . '"';
			}
			if ( $instance['rel_option'] == 'other' ) {
				$a .= ' rel="' . esc_attr( $instance['rel_name'] ) . '"';
			} elseif ( $instance['rel_option'] != '' ) {
				$a .= ' rel="' . esc_attr( $instance['rel_option'] ) . '"';
			}
			$a .= '>';
			$img = $a . $img . '</a>';
		}
		
		echo $img;
	}
	
	if ( $instance['text'] != '' ) {
		echo '<div class="hwim-text">' . $instance['text'] . '</div>';
	}
?>
