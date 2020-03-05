<?php
	/**
	 * Common functions for outputting form fields, sanitizing data, and parsing default arguments.
	 */

	if ( ! function_exists( 'form_input' ) ) {
		function form_input( $args ) {
		    $id = '';
		    $label = '';
			$description = '';
			$placeholder = '';
			$value = '';
			$disabled = '';
			$type = '';
			$tabindex = '';

			$defaults = array(
				'id'          => 'input_' . md5( implode( $args ) ),
				'label'       => 'Label',
				'description' => '',
				'placeholder' => '',
				'value'       => '',
				'disabled'    => '',
				'type'        => 'text'
			);

			$args = wp_parse_args( $args, $defaults );
			extract( $args );


			$description = ! empty( $description ) ? '<small>' . esc_html( $description ) . '</small>' : '';

			$disable_input_row = '';
			if ( ! empty( $disabled ) && true === $disabled ) {
				$disabled          = 'disabled="disabled"';
				$disable_input_row = 'disable-input-row';

			} ?>

            <div class="input-row <?php echo $disable_input_row; ?>">
                <label for="<?php echo esc_html( $id ); ?>"><?php echo esc_html( $label ); ?></label>
                <input type="<?php echo esc_html( $type ); ?>"
                       class="form-control"
                       size="20"
                       name="<?php echo esc_html( $id ); ?>"
                       id="<?php echo esc_html( $id ); ?>"
                       aria-describedby="<?php echo esc_html( $description ); ?>"
                       placeholder="<?php echo esc_html( $placeholder ); ?>"
                       value="<?php echo esc_html( $value ); ?>"
                       tabindex="<?php echo esc_html( $tabindex ); ?>" <?php echo $disabled; ?>/>
                <p><?php echo $description ?></p>
            </div>
			<?php
		}
	}

	if ( ! function_exists( 'text_area_input' ) ) {
		function text_area_input( $args ) {
			$id = '';
			$label = '';
			$value = '';
			$tabindex = '';
            $rows = '';
            $cols = '';

			$defaults = array(
				'id'          => 'input_' . md5( implode( $args ) ),
				'label'       => 'Label',
				'description' => 'Description',
				'value'       => 'Value',
				'cols'        => 35,
				'rows'        => 3
			);

			$args = wp_parse_args( $args, $defaults );
			extract( $args );
			?>
            <div class="input-row input-textarea">
                <label for="<?php echo esc_html( $id ); ?>">
					<?php echo esc_html( $label ); ?>
                </label>
                <textarea name="<?php echo esc_html( $id ); ?>" id="<?php echo esc_html( $id ); ?>"
                          tabindex="<?php echo esc_html( $tabindex ); ?>"
                          rows="<?php echo esc_html( $rows ); ?>"
                          cols="<?php echo esc_html( $cols ); ?>"
                          class="form-control"><?php echo esc_html( $value ); ?>

            </textarea>
            </div>
			<?php
		}
	}

	/**
	 * @param string $search
	 *
	 * @return string Prepared for PDO query.
	 */

	if( ! function_exists( 'prepare_search_string' ) ) {
		function prepare_search_string( $search = '' ) {
			$search = esc_html( strtolower( urldecode( $search ) ) );
			$search = implode( '%', explode( ' ', $search ) );

			return '%' . $search . '%';
		}
	}

