<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


/**
 * Class Coupon_Admin_Columns
 * Coupon Column Methods for the Coupon CPT
 *
 * @since 2.3
 *
 */
class Cctor__Coupon__Admin__Columns extends WP_List_Table {


	public function __construct() {

		// Remove Coupon Row Actions
		add_filter( 'post_row_actions', array( $this, 'cctor_remove_coupon_row_actions' ), 10, 2 );

		// Add Columns
		add_filter( 'manage_edit-cctor_coupon_columns', array( $this, 'cctor_list_columns' ) );

		//Custom Column Cases
		add_action( 'manage_posts_custom_column', array( $this, 'cctor_column_cases' ), 10, 2 );

		// Coupon list filters (category + expiration mode)
		add_action( 'restrict_manage_posts', array( $this, 'render_list_filters' ) );
		add_action( 'parse_query', array( $this, 'filter_list_query' ) );

		// Artifex list-screen chrome: scope the theme class to the coupon list body
		// and open the screen with the header band above the list table.
		add_filter( 'admin_body_class', array( $this, 'add_list_body_class' ) );
		add_action( 'admin_notices', array( $this, 'render_list_header_band' ) );

	}

	/**
	 * Add the Artifex theme class on the coupon list screen only.
	 *
	 * @param string $classes Space-separated body classes.
	 *
	 * @return string
	 */
	public function add_list_body_class( $classes ) {
		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'edit-cctor_coupon' === $screen->id ) {
			$classes .= ' afx-theme-coupon-creator';
		}

		return $classes;
	}

	/**
	 * Render the Artifex header band above the coupon list table.
	 *
	 * Hooked to admin_notices so it prints inside .wrap (after the native title
	 * row) and ahead of the list table. Mirrors the Options page band.
	 */
	public function render_list_header_band() {
		$screen = get_current_screen();

		if ( ! isset( $screen->id ) || 'edit-cctor_coupon' !== $screen->id ) {
			return;
		}

		$mark_url      = pngx( 'cctor' )->resource_url . 'images/coupon-creator-mark.svg';
		$docs_url      = cctor_guide_url( 'coupon-creator' );
		$version_parts = apply_filters( 'cctor_options_header_versions', array( 'v' . Cctor__Coupon__Main::VERSION_NUM ) );
		$version_line  = implode( ' · ', array_filter( (array) $version_parts ) );

		echo '<div class="afx-header">
			<div class="afx-header__brand">
				<img class="afx-header__mark" src="' . esc_url( $mark_url ) . '" alt="" />
				<span class="afx-header__names">
					<span class="afx-header__wordmark">' . esc_html__( 'Coupon Creator', 'coupon-creator' ) . '</span>
					<span class="afx-header__promise">' . esc_html__( 'Put a real coupon on your site.', 'coupon-creator' ) . '</span>
					<span class="afx-header__endorse">' . esc_html__( 'By Artifex Routing Co', 'coupon-creator' ) . '</span>
				</span>
			</div>
			<div class="afx-header__meta">';
		if ( $version_line ) {
			echo '<span class="afx-header__version">' . esc_html( $version_line ) . '</span>';
		}
		echo '<a class="afx-header__docs" href="' . esc_url( $docs_url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Docs', 'coupon-creator' ) . ' &#8599;</a>
			</div>
		</div>';
	}

	/***************************************************************************/

	/*
	* Remove Coupon Row Actions if user does not have permision to manage
	* @version 1.90
	* @param array $actions, $post
	*/
	public function cctor_remove_coupon_row_actions( $actions, $post ) {
		global $current_screen, $current_user;

		if ( is_object( $current_screen ) && $current_screen->post_type != 'cctor_coupon' ) {
			return $actions;
		}

		if ( ! current_user_can( 'edit_others_cctor_coupons', $post->ID ) && ( $post->post_author != $current_user->ID ) ) {
			unset( $actions['edit'] );
			unset( $actions['view'] );
			unset( $actions['trash'] );
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/***************************************************************************/

	/*
	* Setup Custom Columns
	* @version 2.0
	* @param array $columns
	*/
	public function cctor_list_columns( $columns ) {
		$cctor_columns = array();

		if ( isset( $columns['cb'] ) ) {
			$cctor_columns['cb'] = $columns['cb'];
		}

		if ( isset( $columns['title'] ) ) {
			$cctor_columns['title'] = __( 'Coupon Title', 'coupon-creator' );
		}

		if ( isset( $columns['author'] ) ) {
			$cctor_columns['author'] = $columns['author'];
		}

		$cctor_columns['cctor_showing'] = __( 'Coupon is ', 'coupon-creator' );

		$cctor_columns['cctor_shortcode'] = __( 'Shortcode', 'coupon-creator' );

		$cctor_columns['cctor_expiration_date'] = __( 'Expiration', 'coupon-creator' );

		$cctor_columns['cctor_category'] = __( 'Coupon Category', 'coupon-creator' );


		if ( isset( $columns['date'] ) ) {
			$cctor_columns['date'] = $columns['date'];
		}

		//Filter Columns
		if ( has_filter( 'cctor_filter_coupon_list_columns' ) ) {

			/**
			 * Filter the Admin Coupon List Columns Headers
			 *
			 * @param array $cctor_columns an array of column headers.
			 *
			 */
			$cctor_columns = apply_filters( 'cctor_filter_coupon_list_columns', $cctor_columns, $columns );
		}

		return $cctor_columns;
	}

	/**
	 * Add Custom Meta Data to Columns
	 *
	 * @since 2.0
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function cctor_column_cases( $column, $post_id ) {

		global $post;

		if ( class_exists( 'Cctor__Coupon__Pro__Expiration' ) ) {
			$coupon_expiration = new Cctor__Coupon__Pro__Expiration();
		} else {
			$coupon_expiration = new Cctor__Coupon__Expiration();
		}

		switch ( $column ) {
			case 'cctor_showing':

				echo $coupon_expiration->get_admin_list_coupon_showing();

				if ( 'publish' === get_post_status( $post_id ) ) {
					$reasons = Cctor__Coupon__Admin__Usability::get_incomplete_reasons( $post_id );
					if ( ! empty( $reasons ) ) {
						echo '<p style="color:#dd3d36;" title="' . esc_attr( implode( ' ', $reasons ) ) . '">' . esc_html__( 'Incomplete', 'coupon-creator' ) . '</p>';
					}
				}

				break;
			case 'cctor_shortcode':
				$shortcode = Cctor__Coupon__Admin__Usability::get_coupon_shortcode( $post_id );
				if ( $shortcode ) {
					?>
					<button
						type="button"
						class="button button-small cctor-copy-shortcode"
						data-cctor-copy-text="<?php echo esc_attr( $shortcode ); ?>"
						data-cctor-copy-label="<?php esc_attr_e( 'Copy Shortcode', 'coupon-creator' ); ?>"
						data-cctor-copied-label="<?php esc_attr_e( 'Copied!', 'coupon-creator' ); ?>"
					>
						<?php esc_html_e( 'Copy Shortcode', 'coupon-creator' ); ?>
					</button>
					<span class="screen-reader-text cctor-copy-status" aria-live="polite"></span>
					<?php
				}

				break;
			case 'cctor_expiration_date':

				if ( 1 == $coupon_expiration->get_expiration_option() ) {
					esc_html_e( 'No expiration', 'coupon-creator' );
				} else {
					$display_expiration = $coupon_expiration->get_display_expiration();
					echo $display_expiration ? $display_expiration : '&mdash;';
				}

				break;

			case 'cctor_category':

				$terms = get_the_terms( $post_id, 'cctor_coupon_category' );

				if ( ! empty( $terms ) ) {

					$out = array();

					foreach ( $terms as $term ) {
						$out[] = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array(
							'post_type'             => $post->post_type,
							'cctor_coupon_category' => $term->slug
						), 'edit.php' ) ), esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'genre', 'display' ) ) );
					}

					echo join( ', ', $out );
				} else {
					_e( 'No Categories', 'coupon-creator' );
				}

				break;
		}

		if ( has_filter( 'cctor_filter_column_cases' ) ) {

			/**
			 * Filter the Admin Coupon List Columns Information per Coupon
			 *
			 * @since 1.80
			 *
			 * @param string $column            a string of data to display in the admin columns.
			 * @param int    $post_id           an integer of the coupon post
			 * @param object $coupon_expiration the expiration object.
			 *
			 */
			apply_filters( 'cctor_filter_column_cases', $column, $post_id, $coupon_expiration );
		}
	}

	/**
	 * Render the category and expiration-mode dropdowns above the coupon list.
	 *
	 * @param string $post_type Current list-table post type.
	 */
	public function render_list_filters( $post_type ) {
		if ( 'cctor_coupon' !== $post_type ) {
			return;
		}

		$selected_category = isset( $_GET['cctor_coupon_category'] ) ? sanitize_text_field( wp_unslash( $_GET['cctor_coupon_category'] ) ) : '';

		wp_dropdown_categories(
			array(
				'taxonomy'        => 'cctor_coupon_category',
				'name'            => 'cctor_coupon_category',
				'value_field'     => 'slug',
				'selected'        => $selected_category,
				'show_option_all' => __( 'All Coupon Categories', 'coupon-creator' ),
				'hide_empty'      => true,
				'hierarchical'    => true,
			)
		);

		$selected_mode = isset( $_GET['cctor_expiration_mode'] ) ? sanitize_text_field( wp_unslash( $_GET['cctor_expiration_mode'] ) ) : '';
		?>
		<select name="cctor_expiration_mode">
			<option value=""><?php esc_html_e( 'All Expiration Settings', 'coupon-creator' ); ?></option>
			<option value="ignore" <?php selected( $selected_mode, 'ignore' ); ?>><?php esc_html_e( 'No expiration', 'coupon-creator' ); ?></option>
			<option value="date" <?php selected( $selected_mode, 'date' ); ?>><?php esc_html_e( 'Uses a date', 'coupon-creator' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Apply the expiration-mode dropdown to the coupon list query.
	 *
	 * The category dropdown needs no handling: the taxonomy registers a query
	 * var, so core's edit.php resolves it.
	 *
	 * @param WP_Query $query Current query.
	 */
	public function filter_list_query( $query ) {
		global $pagenow;

		if (
			! is_admin() ||
			'edit.php' !== $pagenow ||
			! $query->is_main_query() ||
			'cctor_coupon' !== $query->get( 'post_type' ) ||
			empty( $_GET['cctor_expiration_mode'] )
		) {
			return;
		}

		$mode = sanitize_text_field( wp_unslash( $_GET['cctor_expiration_mode'] ) );

		if ( 'ignore' === $mode ) {
			$query->set(
				'meta_query',
				array(
					array(
						'key'   => 'cctor_expiration_option',
						'value' => '1',
					),
				)
			);
		} elseif ( 'date' === $mode ) {
			// Coupons saved before the option existed have no meta row but use a date.
			$query->set(
				'meta_query',
				array(
					'relation' => 'OR',
					array(
						'key'     => 'cctor_expiration_option',
						'value'   => '1',
						'compare' => '!=',
					),
					array(
						'key'     => 'cctor_expiration_option',
						'compare' => 'NOT EXISTS',
					),
				)
			);
		}
	}

	/***************************************************************************/
}
