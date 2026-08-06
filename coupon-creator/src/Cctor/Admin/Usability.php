<?php
/**
 * Customer-focused admin usability improvements.
 *
 * @package Coupon_Creator
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Customer-focused admin guidance and coupon actions.
 */
class Cctor__Coupon__Admin__Usability {

	/**
	 * Per-user onboarding dismissal key.
	 */
	const GETTING_STARTED_DISMISSED_KEY = '_cctor_getting_started_dismissed';

	/**
	 * Per-user first-publish success key.
	 */
	const PUBLISH_SUCCESS_KEY = '_cctor_publish_success_coupon';

	/**
	 * Register hooks.
	 */
	public function hook() {
		add_action( 'admin_init', array( $this, 'handle_getting_started_dismissal' ) );
		add_action( 'admin_notices', array( $this, 'display_getting_started' ) );
		add_action( 'admin_notices', array( $this, 'display_publish_success' ) );
		add_action( 'admin_notices', array( $this, 'display_incomplete_warning' ) );
		add_action( 'transition_post_status', array( $this, 'record_first_publish' ), 10, 3 );
		add_filter( 'post_row_actions', array( $this, 'filter_coupon_row_actions' ), 20, 2 );
	}

	/**
	 * Reasons a coupon would render broken or empty for visitors.
	 *
	 * @param int $coupon_id Coupon ID.
	 *
	 * @return string[] Human-readable reasons; empty when the coupon is complete.
	 */
	public static function get_incomplete_reasons( $coupon_id ) {
		$reasons = array();

		$type = get_post_meta( $coupon_id, 'cctor_coupon_type', true );
		$type = $type ? $type : 'default';

		// Classic and the ticket both build their headline from the Deal field.
		if (
			in_array( $type, array( 'default', 'ticket' ), true )
			&& '' === trim( (string) get_post_meta( $coupon_id, 'cctor_amount', true ) )
		) {
			$reasons[] = __( 'The Deal field is empty, so the coupon\'s deal band will display with no text.', 'coupon-creator' );
		}

		if ( 'image' === $type && ! absint( get_post_meta( $coupon_id, 'cctor_image', true ) ) ) {
			$reasons[] = __( 'No coupon image is selected, so this image coupon will not display at all.', 'coupon-creator' );
		}

		/**
		 * Filter the incomplete-coupon reasons shown to editors.
		 *
		 * @param string[] $reasons   Reasons the coupon renders broken/empty.
		 * @param int      $coupon_id Coupon ID.
		 */
		return apply_filters( 'cctor_coupon_incomplete_reasons', $reasons, $coupon_id );
	}

	/**
	 * Warn on the editor screen when a published coupon will render broken.
	 *
	 * Computed from live meta on every render, so it clears itself once the
	 * fields are filled. Drafts are allowed to be incomplete.
	 */
	public function display_incomplete_warning() {
		$screen = get_current_screen();
		if ( ! $screen || 'cctor_coupon' !== $screen->id ) {
			return;
		}

		$post = get_post();
		if (
			! $post ||
			Cctor__Coupon__Main::POSTTYPE !== $post->post_type ||
			'publish' !== $post->post_status ||
			! current_user_can( 'edit_post', $post->ID )
		) {
			return;
		}

		$reasons = self::get_incomplete_reasons( $post->ID );
		if ( empty( $reasons ) ) {
			return;
		}
		?>
		<div class="notice notice-warning cctor-incomplete-warning">
			<p><strong><?php esc_html_e( 'This published coupon is incomplete.', 'coupon-creator' ); ?></strong></p>
			<ul>
				<?php foreach ( $reasons as $reason ) : ?>
					<li><?php echo esc_html( $reason ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Return the canonical shortcode shown throughout wp-admin.
	 *
	 * The name attribute is retained for backward compatibility, even though
	 * coupon rendering is keyed by coupon ID.
	 *
	 * @param WP_Post|int $coupon Coupon post or ID.
	 *
	 * @return string
	 */
	public static function get_coupon_shortcode( $coupon ) {
		$coupon = get_post( $coupon );

		if ( ! $coupon || Cctor__Coupon__Main::POSTTYPE !== $coupon->post_type ) {
			return '';
		}

		$title = str_replace( '"', "'", wp_strip_all_tags( $coupon->post_title ) );

		return sprintf(
			'[coupon couponid="%1$d" name="%2$s"]',
			absint( $coupon->ID ),
			$title
		);
	}

	/**
	 * Whether the current screen should show first-use guidance.
	 *
	 * @return bool
	 */
	public function should_show_getting_started() {
		if ( ! current_user_can( 'edit_cctor_coupons' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'edit-cctor_coupon' !== $screen->id ) {
			return false;
		}

		if ( get_user_meta( get_current_user_id(), self::GETTING_STARTED_DISMISSED_KEY, true ) ) {
			return false;
		}

		// The activation-created sample coupon doesn't count as the user's own.
		$exclude = array();
		if ( self::get_sample_coupon() ) {
			$exclude[] = absint( get_option( 'cctor_sample_coupon_id' ) );
		}

		$existing = get_posts(
			array(
				'post_type'   => Cctor__Coupon__Main::POSTTYPE,
				'post_status' => 'any',
				'numberposts' => 1,
				'fields'      => 'ids',
				'exclude'     => $exclude,
			)
		);

		return empty( $existing );
	}

	/**
	 * Get the activation-created sample coupon, if it still exists.
	 *
	 * @return WP_Post|null
	 */
	public static function get_sample_coupon() {
		$sample_id = absint( get_option( 'cctor_sample_coupon_id' ) );
		if ( ! $sample_id ) {
			return null;
		}

		$sample = get_post( $sample_id );
		if (
			! $sample ||
			Cctor__Coupon__Main::POSTTYPE !== $sample->post_type ||
			'trash' === $sample->post_status
		) {
			return null;
		}

		return $sample;
	}

	/**
	 * Display the empty-state getting-started panel.
	 */
	public function display_getting_started() {
		if ( ! $this->should_show_getting_started() ) {
			return;
		}

		$create_url  = admin_url( 'post-new.php?post_type=cctor_coupon' );
		$sample      = self::get_sample_coupon();
		$sample_url  = $sample ? get_edit_post_link( $sample->ID, 'url' ) : '';
		$dismiss_url = wp_nonce_url(
			add_query_arg(
				array(
					'post_type'                     => Cctor__Coupon__Main::POSTTYPE,
					'cctor_dismiss_getting_started' => 1,
				),
				admin_url( 'edit.php' )
			),
			'cctor_dismiss_getting_started'
		);
		?>
		<div class="notice notice-info cctor-getting-started">
			<p class="cctor-getting-started__descriptor"><strong><?php esc_html_e( 'Coupon Creator, by Artifex Routing Co', 'coupon-creator' ); ?></strong></p>
			<h2><?php esc_html_e( 'Your promo looks like a promo people clip and use, not a line of code they scroll past.', 'coupon-creator' ); ?></h2>
			<p><?php esc_html_e( 'Build a designed, printable coupon in WordPress or upload your own image, then display it with a block or shortcode.', 'coupon-creator' ); ?></p>
			<p class="cctor-getting-started__proof"><strong><?php esc_html_e( 'Maintained and shipping on WordPress since 2014.', 'coupon-creator' ); ?></strong></p>
			<ol class="cctor-getting-started__steps">
				<li><strong><?php esc_html_e( 'Create', 'coupon-creator' ); ?></strong>: <?php esc_html_e( 'add the deal, terms, and expiration.', 'coupon-creator' ); ?></li>
				<li><strong><?php esc_html_e( 'Display', 'coupon-creator' ); ?></strong>: <?php esc_html_e( 'copy its shortcode or select it in the Coupon Creator block.', 'coupon-creator' ); ?></li>
				<li><strong><?php esc_html_e( 'Print', 'coupon-creator' ); ?></strong>: <?php esc_html_e( 'open the print view and test it before sharing.', 'coupon-creator' ); ?></li>
			</ol>
			<p class="cctor-getting-started__scope"><strong><?php esc_html_e( 'Good to know:', 'coupon-creator' ); ?></strong> <?php esc_html_e( 'Coupon Creator makes designed promo coupons visitors can print — it does not create store discount codes on its own. Running WooCommerce? The Add-ons create a matching WooCommerce coupon from the same editor, so the code on your coupon works at checkout.', 'coupon-creator' ); ?></p>
			<p class="cctor-getting-started__tips"><strong><?php esc_html_e( 'Three things people miss:', 'coupon-creator' ); ?></strong> <?php esc_html_e( 'you can upload an image of an existing coupon (a newspaper ad works) and give it an expiration date, for free. Click-to-reveal and click-to-copy codes come with the Add-ons. And every coupon already has a built-in print view.', 'coupon-creator' ); ?></p>
			<p class="cctor-getting-started__actions">
				<?php if ( $sample_url ) : ?>
					<a class="button button-primary" href="<?php echo esc_url( $sample_url ); ?>"><?php esc_html_e( 'Open the Sample Coupon', 'coupon-creator' ); ?></a>
					<a class="button" href="<?php echo esc_url( $create_url ); ?>"><?php esc_html_e( 'Create Your First Coupon', 'coupon-creator' ); ?></a>
				<?php else : ?>
					<a class="button button-primary" href="<?php echo esc_url( $create_url ); ?>"><?php esc_html_e( 'Create Your First Coupon', 'coupon-creator' ); ?></a>
				<?php endif; ?>
				<a class="button" href="<?php echo esc_url( $this->get_getting_started_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View Getting Started Guide', 'coupon-creator' ); ?></a>
				<a class="cctor-getting-started__dismiss" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Dismiss', 'coupon-creator' ); ?></a>
			</p>
			<p class="cctor-getting-started__publisher"><?php esc_html_e( 'Part of Artifex Routing Co. WordPress plugins that are still here in ten years.', 'coupon-creator' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Persist a user's onboarding dismissal.
	 */
	public function handle_getting_started_dismissal() {
		if ( ! isset( $_GET['cctor_dismiss_getting_started'], $_GET['_wpnonce'] ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_cctor_coupons' ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'cctor_dismiss_getting_started' ) ) {
			return;
		}

		update_user_meta( get_current_user_id(), self::GETTING_STARTED_DISMISSED_KEY, 1 );

		wp_safe_redirect(
			remove_query_arg(
				array( 'cctor_dismiss_getting_started', '_wpnonce' )
			)
		);
		exit;
	}

	/**
	 * Remember a coupon's first transition to published for the current user.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Previous post status.
	 * @param WP_Post $post       Post being transitioned.
	 */
	public function record_first_publish( $new_status, $old_status, $post ) {
		if (
			'publish' !== $new_status ||
			'publish' === $old_status ||
			Cctor__Coupon__Main::POSTTYPE !== $post->post_type ||
			! get_current_user_id() ||
			! current_user_can( 'edit_post', $post->ID )
		) {
			return;
		}

		update_user_meta( get_current_user_id(), self::PUBLISH_SUCCESS_KEY, $post->ID );
	}

	/**
	 * Display the one-time success handoff after first publish.
	 */
	public function display_publish_success() {
		$user_id   = get_current_user_id();
		$coupon_id = absint( get_user_meta( $user_id, self::PUBLISH_SUCCESS_KEY, true ) );

		if ( ! $coupon_id ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'cctor_coupon' !== $screen->id || ! current_user_can( 'edit_post', $coupon_id ) ) {
			return;
		}

		delete_user_meta( $user_id, self::PUBLISH_SUCCESS_KEY );

		$coupon    = get_post( $coupon_id );
		$shortcode = self::get_coupon_shortcode( $coupon );
		if ( ! $coupon || ! $shortcode ) {
			return;
		}

		$style_was_enqueued = wp_style_is( 'coupon_creator_css', 'enqueued' );
		$preview            = do_shortcode( '[coupon couponid="' . absint( $coupon_id ) . '"]' );

		// The admin stylesheet contains a scoped copy of the coupon rules for
		// this preview. Do not let the shortcode's front-end enqueue leak into
		// the editor and restyle fields that reuse coupon classes.
		if ( ! $style_was_enqueued ) {
			wp_dequeue_style( 'coupon_creator_css' );
		}
		?>
		<div class="notice notice-success is-dismissible cctor-publish-success">
			<div class="cctor-publish-success__content">
				<div>
					<h2><?php esc_html_e( 'Your coupon is published', 'coupon-creator' ); ?></h2>
					<p><?php esc_html_e( 'Copy its shortcode to display it, or open the print view for a final check.', 'coupon-creator' ); ?></p>
					<?php $this->display_coupon_actions( $coupon, true ); ?>
				</div>
				<div class="cctor-publish-success__preview" aria-label="<?php esc_attr_e( 'Coupon preview', 'coupon-creator' ); ?>">
					<?php echo wp_kses_post( $preview ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display reusable shortcode and print-view actions.
	 *
	 * @param WP_Post $coupon      Coupon post.
	 * @param bool    $show_code   Whether to display the shortcode text.
	 */
	public function display_coupon_actions( $coupon, $show_code = false ) {
		$shortcode = self::get_coupon_shortcode( $coupon );
		if ( ! $shortcode ) {
			return;
		}
		?>
		<div class="cctor-coupon-actions">
			<?php if ( $show_code ) : ?>
				<code class="cctor-coupon-actions__shortcode"><?php echo esc_html( $shortcode ); ?></code>
			<?php endif; ?>
			<button
				type="button"
				class="button cctor-copy-shortcode"
				data-cctor-copy-text="<?php echo esc_attr( $shortcode ); ?>"
				data-cctor-copy-label="<?php esc_attr_e( 'Copy Shortcode', 'coupon-creator' ); ?>"
				data-cctor-copied-label="<?php esc_attr_e( 'Copied!', 'coupon-creator' ); ?>"
			>
				<?php esc_html_e( 'Copy Shortcode', 'coupon-creator' ); ?>
			</button>
			<a class="button" href="<?php echo esc_url( get_permalink( $coupon ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open Print View', 'coupon-creator' ); ?></a>
			<span class="screen-reader-text cctor-copy-status" aria-live="polite"></span>
		</div>
		<?php
	}

	/**
	 * Make the print preview action clear on coupon rows.
	 *
	 * @param array   $actions Row actions.
	 * @param WP_Post $post    Current post.
	 *
	 * @return array
	 */
	public function filter_coupon_row_actions( $actions, $post ) {
		if ( Cctor__Coupon__Main::POSTTYPE !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		if ( 'publish' === $post->post_status ) {
			$actions['view'] = sprintf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( get_permalink( $post ) ),
				esc_html__( 'Preview Print View', 'coupon-creator' )
			);
		}

		return $actions;
	}

	/**
	 * Get the current getting-started guide URL.
	 *
	 * @return string
	 */
	protected function get_getting_started_url() {
		/**
		 * Filter the getting-started guide URL used in wp-admin.
		 *
		 * @param string $url Getting-started guide URL.
		 */
		return apply_filters( 'cctor_getting_started_url', cctor_guide_url( 'coupon-creator-getting-started' ) );
	}
}
