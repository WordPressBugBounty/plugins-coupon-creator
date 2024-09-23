<?php


/**
 * Class Pngx__Admin__Ajax
 *
 */
class Pngx__Admin__Ajax {
	public function __construct() {
		$this->start();
	}
	protected function start() {
		add_action( 'wp_ajax_pngx_templates', array( $this, 'load_templates' ) );
		add_action( 'wp_ajax_pngx_variety', array( $this, 'load_variety' ) );
		add_action( 'wp_ajax_pngx_repeatable', array( $this, 'save_repeating_items' ) );
	}

	public function load_templates() {
		// End if not the correct action
		if ( ! isset( $_POST['action'] ) || 'pngx_templates' !== $_POST['action'] ) {
			wp_send_json_error( __( 'A Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		// End if not correct nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pngx_admin_' . $_POST['post_id'] ) ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['option'] ) ) {
			wp_send_json_error( __( 'No Template ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		Pngx__Main::instance()->doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		ob_start();

		/**
		 * Filter to Add All Fields for a Plugin
		 */
		$fields = (array) apply_filters( 'pngx_meta_fields', [] );
		foreach ( $fields as $field ) {

			$field_template = isset( $field['template'] ) ? $field['template'] : [];

			if ( $field['type'] && in_array( $_POST['option'], $field_template ) ) {

				// get value of this field if it exists for this post
				$meta = get_post_meta( $_POST['post_id'], $field['id'], true );

				//Wrap Class for Conditionals
				$wrapclass = isset( $field['wrapclass'] ) ? $field['wrapclass'] : '';

				if ( 'wrap-start' === $field['type'] ) {
					?>
                    <div class="pngx-meta-fields-wrap admin-field-wrap <?php echo esc_html( $wrapclass ); ?>" >
					<?php
					continue;

				} elseif ( "wrap-end" === $field['type'] ) {

					if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) {
						echo '<span class="description">' . esc_html( $field['desc'] ) . '</span>';
					}

					// Display admin linked style fields
					Pngx__Admin__Style__Linked::display_styles( $fields, $field, $_POST['post_id'] );
					?>
                    </div>
					<?php
					continue;
				}
				?>

                <div class="pngx-meta-field-wrap field-wrap-<?php echo esc_html( $field['type'] ); ?> field-wrap-<?php echo esc_html( $field['id'] ); ?> <?php echo esc_html( $wrapclass ); ?>"
					<?php echo isset( $field['toggle'] ) ? Pngx__Admin__Fields::toggle( $field['toggle'], esc_attr( $field['id'] ) ) : null; ?> >

                    <div class="pngx-meta-field field-<?php echo esc_attr( $field['type'] ); ?> field-<?php echo esc_attr( $field['id'] ); ?>">

						<?php if ( isset( $field['label'] ) && ! empty( $field['label'] ) ) { ?>
                            <label for="<?php echo esc_attr( $field['id'] ); ?>">
								<?php echo esc_attr( $field['label'] ); ?>
                            </label>
						<?php } ?>

						<?php
						Pngx__Admin__Fields::display_field( $field, false, false, $meta, null );

						// Display admin linked style fields
						Pngx__Admin__Style__Linked::display_styles( $fields, $field, $_POST['post_id'] );

						?>

                    </div>
                    <!-- end .pngx-meta-field.field-<?php echo esc_attr( $field['type'] ); ?>.field-<?php echo esc_attr( $field['id'] ); ?> -->

                </div> <!-- end .pngx-meta-field-wrap.field-wrap-<?php echo esc_attr( $field['type'] ); ?>.field-wrap-<?php echo esc_attr( $field['id'] ); ?>	-->

				<?php
			}
		} // end foreach fields

		$template_fields = ob_get_contents();

		ob_end_clean();

		wp_send_json_success( json_encode( $template_fields ) );
	}

	/**
	 * Load Variety Fields
	 *
	 */
	public function load_variety() {
		//End if not the correct action
		if ( ! isset( $_POST['action'] ) || 'pngx_variety' != $_POST['action'] ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		//End if not correct nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pngx_admin_' . $_POST['post_id'] ) ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['field'] ) ) {
			wp_send_json_error( __( 'No Field ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['option'] ) ) {
			wp_send_json_error( __( 'No Option ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		Pngx__Main::instance()->doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		ob_start();

		/**
		 * Filter to Add All Fields for a Plugin
		 */
		$fields = apply_filters( 'pngx_meta_fields', [] );

		if ( isset( $fields[ $_POST['field'] ]['variety_choices'][ $_POST['option'] ] ) ) {
			foreach ( $fields[ $_POST['field'] ]['variety_choices'][ $_POST['option'] ] as $label ) {

				if ( is_array( $label ) && isset( $label['open'] ) ) {
					?>
                    <div class="<?php echo esc_html( $label['open'] ); ?>">
					<?php
					continue;
				}

				if ( is_array( $label ) && ! empty( $label['label'] ) ) {
					?>
                    <label for="<?php echo esc_attr( $label['label'] ); ?>">
						<?php echo esc_attr( $label['label'] ); ?>
                    </label>
					<?php
					continue;
				}

				if ( is_array( $label ) && isset( $label['description'] ) ) {
					?>
                    <span class="description"><?php echo esc_html( $label['description'] ); ?></span>
					<?php
					continue;
				}

				if ( is_array( $label ) ) {
					continue;
				}

				if ( 'close' === $label ) {
					?>
                    </div>
					<?php
					continue;
				}

				if ( ! isset( $fields[ $label ] ) ) {
					continue;
				}

				$meta = '';
				if ( isset( $_POST['post_id'] ) ) {
					$meta = get_post_meta( $_POST['post_id'], $label, true );
				}

				?>
                <div class="pngx-variety-field <?php echo isset( $fields[ $label ]['class'] ) ? esc_attr( $fields[ $label ]['class'] ) : ''; ?>">
					<?php

					if ( isset( $fields[ $label ]['label'] ) && ! empty( $fields[ $label ]['label'] ) ) { ?>
                        <label for="<?php echo esc_attr( $fields[ $label ]['id'] ); ?>">
							<?php echo esc_attr( $fields[ $label ]['label'] ); ?>
                        </label>
					<?php }

					Pngx__Admin__Fields::display_field( $fields[ $label ], false, false, $meta, null );

					?>
                </div>
				<?php

			}
		}

		$field = ob_get_contents();

		ob_end_clean();

		wp_send_json_success( json_encode( $field ) );
	}

	public function save_repeating_items() {

		//End if not the correct action
		if ( ! isset( $_POST['action'] ) || 'pngx_repeatable' !== $_POST['action'] ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		//End if not correct nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pngx_repeatable_nonce' ) ) {
			wp_send_json_error( __( 'Permission Error has occurred. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['field_value'], $_POST['field_name'] ) ) {
			wp_send_json_error( __( 'No Field to Save. Please try again.', 'plugin-engine' ) );
		}

		if ( ! isset( $_POST['post_id'], $_POST['post_type'] ) ) {
			wp_send_json_error( __( 'No POST ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		Pngx__Main::instance()->doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		$post_id = absint( $_POST['post_id'] );

		if ( empty( $post_id ) || ! empty( $_POST['title_field'] ) ) {

			$menu_item = array(
				'ID'          => absint( $_POST['post_id'] ),
				'post_title'  => ! empty ( $_POST['title_field'] ) ? wp_strip_all_tags( $_POST['field_value'] ) : esc_attr( $_POST['post_title_default'] ),
				'post_status' => 'publish',
				'post_type'   => esc_attr( $_POST['post_type'] ),
				'meta_input'  => array(
					$_POST['field_name'] => $_POST['field_value'],
				)
			);

            $repository = Pngx__Repository__Main::init();
            $post_id = $repository->save( $menu_item );

			if ( is_wp_error( $post_id ) ) {
				wp_send_json_error( $post_id->get_error_message() );
			}

            $saved = array (
                'type' => 'post',
                'ID' => $post_id,
            );

            wp_send_json_success( $saved );

		}

		if ( empty( $post_id ) ) {
			wp_send_json_error( __( 'No POST ID. Please save, reload, and try again.', 'plugin-engine' ) );
		}

		update_post_meta( $post_id, esc_attr( $_POST['field_name'] ), esc_attr( $_POST['field_value'] ) );

        $saved = array (
            'type' => 'meta',
            'ID' => $post_id,
        );

        wp_send_json_success( $saved );

	}

}