<?php
/**
 * EDD Software Licensing SDK — settings-page license control.
 *
 * The SDK's license UI is Plugins-screen only by design: a "Manage License"
 * action link that opens a JS modal. The modal is driven by a document-level
 * click handler bound to the `edd-sdk__notice__trigger` button class and needs
 * an `.edd-sdk-notice--overlay` element plus the SDK's script/style — which the
 * SDK only bootstraps on `plugins.php`. There is no public API to render it
 * elsewhere (handler instances are private; only Registry::register() is public).
 *
 * This class reproduces that bootstrap so any plugin can host the control on its
 * own settings page. All SDK-internal coupling lives here in one place, so an
 * SDK version bump only needs re-checking against this file. Coupling surface:
 * the trigger CSS classes, the overlay class, the `assets/build/js|css` paths
 * (resolved from the SDK's own Path helper), and the `edd_sdk_notice`
 * localization keys.
 *
 * Framework-agnostic: the caller decides WHEN and WHERE to render (gate on the
 * SDK being active and on the correct admin screen), then drops rows() into its
 * settings markup and calls enqueue()/overlay() on that screen.
 *
 * @package Artifex\License
 *
 * Modified by Artifex Routing Co using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\License;

class Control {

	/**
	 * The SDK's Path helper — sole source of the (version-coordinated) asset URLs.
	 */
	private const SDK_PATH = '\EasyDigitalDownloads\Updater\Utilities\Path';

	/**
	 * Whether the SDK is loaded (so the control can function).
	 */
	public static function is_available(): bool {
		return class_exists( self::SDK_PATH );
	}

	/**
	 * Render the license rows (name + status + Manage License button) for a set
	 * of plugins. Each descriptor: [ 'item_id', 'slug', 'name', 'status_option' ]
	 * (optional 'label' overrides the button text; optional 'checked_option' adds
	 * the "Last checked" line and "Check now" button, labelled by 'check_labels').
	 *
	 * @param array<int, array<string, mixed>> $licenses
	 */
	public static function rows( array $licenses ): string {
		$out = '';
		foreach ( $licenses as $license ) {
			$out .= self::row( $license );
		}

		return '' === $out ? '' : '<div class="artifex-license-controls">' . $out . '</div>';
	}

	/**
	 * A single license row.
	 *
	 * @param array<string, mixed> $license
	 */
	public static function row( array $license ): string {
		if ( empty( $license['slug'] ) || empty( $license['item_id'] ) ) {
			return '';
		}

		$name          = (string) ( $license['name'] ?? $license['slug'] );
		$status_option = (string) ( $license['status_option'] ?? '' );
		$label         = (string) ( $license['label'] ?? 'Manage License' );
		$status_labels = isset( $license['status_labels'] ) && is_array( $license['status_labels'] ) ? $license['status_labels'] : [];

		return '<div class="artifex-license-control" style="margin:0 0 1.5em;padding:0 0 1em;border-bottom:1px solid #e2e4e7;">'
			. '<strong style="display:block;margin-bottom:.25em;">' . self::esc_html( $name ) . '</strong>'
			. '<p style="margin:.25em 0 .75em;">' . self::status( $status_option, $status_labels ) . '</p>'
			. self::check_line( $license )
			. self::button( $license['item_id'], (string) $license['slug'], $name, $label )
			. '</div>';
	}

	/**
	 * The "Last checked" line and "Check now" button for a descriptor that has a
	 * 'checked_option' (the plugin runs a Status_Check), or ''.
	 *
	 * @param array<string, mixed> $license
	 */
	public static function check_line( array $license ): string {
		if ( empty( $license['checked_option'] ) || empty( $license['slug'] ) ) {
			return '';
		}

		$labels = isset( $license['check_labels'] ) && is_array( $license['check_labels'] ) ? $license['check_labels'] : [];

		return '<p style="margin:-.5em 0 .75em;">'
			. self::checked( (string) $license['checked_option'], $labels )
			. ' ' . self::check_button( (string) $license['slug'], $labels )
			. '</p>';
	}

	/**
	 * When the license was last checked, and the error if that check failed.
	 * Reads the option Status_Check writes: [ 'time' => int, 'error' => string ].
	 *
	 * @param array<string, string> $labels Optional overrides: checked (one %s),
	 *        check_failed (%1$s date, %2$s error), never.
	 */
	public static function checked( string $checked_option, array $labels = [] ): string {
		$labels += [
			'checked'      => 'Last checked %s.',
			'check_failed' => 'Last check failed %1$s: %2$s',
			'never'        => 'Not checked yet.',
		];

		$checked = $checked_option ? get_option( $checked_option ) : false;
		if ( ! is_array( $checked ) || empty( $checked['time'] ) ) {
			return '<span class="artifex-license-checked">' . self::esc_html( $labels['never'] ) . '</span>';
		}

		$date  = (string) wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $checked['time'] );
		$error = isset( $checked['error'] ) ? (string) $checked['error'] : '';

		if ( '' === $error ) {
			return '<span class="artifex-license-checked">' . self::esc_html( sprintf( $labels['checked'], $date ) ) . '</span>';
		}

		return '<span class="artifex-license-checked" style="color:#d63638;">' . self::esc_html( sprintf( $labels['check_failed'], $date, $error ) ) . '</span>';
	}

	/**
	 * The "Check now" button. enqueue() adds the script that drives it.
	 *
	 * @param array<string, string> $labels Optional overrides: check_now, checking, error.
	 */
	public static function check_button( string $slug, array $labels = [] ): string {
		$labels += [
			'check_now' => 'Check now',
			'checking'  => 'Checking…',
			'error'     => 'The check could not run. Reload the page and try again.',
		];

		$action = Status_Check::ajax_action( $slug );

		return sprintf(
			'<button type="button" class="button-link artifex-license-check" data-action="%1$s" data-nonce="%2$s" data-label="%3$s" data-checking="%4$s" data-error="%5$s">%3$s</button>'
			. ' <span class="artifex-license-check-result" role="status" aria-live="polite"></span>',
			self::esc_attr( $action ),
			self::esc_attr( wp_create_nonce( $action ) ),
			self::esc_attr( $labels['check_now'] ),
			self::esc_attr( $labels['checking'] ),
			self::esc_attr( $labels['error'] )
		);
	}

	/**
	 * The SDK modal trigger button. Its classes are the SDK JS contract.
	 *
	 * @param int|string $item_id
	 */
	public static function button( $item_id, string $slug, string $name, string $label = 'Manage License' ): string {
		return sprintf(
			'<button type="button" class="button button-secondary edd-sdk__notice__trigger edd-sdk__notice__trigger--ajax" data-id="license-control" data-product="%1$s" data-slug="%2$s" data-name="%3$s">%4$s</button>',
			self::esc_attr( (string) $item_id ),
			self::esc_attr( $slug ),
			self::esc_attr( $name ),
			self::esc_html( $label )
		);
	}

	/**
	 * Best-effort status line read from the SDK's stored status option
	 * (`<id>_license`), an object with `->license` and `->expires`.
	 *
	 * @param array<string, string> $labels Optional overrides:
	 *        active, inactive, expires (a sprintf template with one %s), lifetime.
	 */
	public static function status( string $status_option, array $labels = [] ): string {
		$labels += [
			'active'   => 'Active',
			'inactive' => 'Not activated',
			'expires'  => '(expires %s)',
			'lifetime' => '(lifetime)',
		];

		$status = $status_option ? get_option( $status_option ) : false;
		$label  = $labels['inactive'];
		$color  = '#82878c';

		if ( is_object( $status ) && ! empty( $status->license ) ) {
			if ( 'valid' === $status->license ) {
				$color = '#46b450';
				$label = $labels['active'];
				if ( ! empty( $status->expires ) && 'lifetime' === $status->expires ) {
					$label .= ' ' . $labels['lifetime'];
				} elseif ( ! empty( $status->expires ) ) {
					$expires = strtotime( (string) $status->expires );
					if ( $expires ) {
						$label .= ' ' . sprintf( $labels['expires'], date_i18n( (string) get_option( 'date_format' ), $expires ) );
					}
				}
			} else {
				$color = '#dc3232';
				$label = ucfirst( (string) $status->license );
			}
		}

		return sprintf(
			'<span style="display:inline-block;width:9px;height:9px;border-radius:50%%;background:%1$s;margin-right:6px;vertical-align:middle;"></span>%2$s',
			self::esc_attr( $color ),
			self::esc_html( $label )
		);
	}

	/**
	 * Enqueue the SDK's modal script/style and localize it. Call on the admin
	 * screen that hosts the control (the caller gates the screen).
	 *
	 * @param array<string, string> $strings Optional overrides: activating,
	 *        deactivating, error.
	 */
	public static function enqueue( array $strings = [] ): void {
		if ( ! self::is_available() ) {
			return;
		}

		$strings += [
			'activating'   => 'Activating…',
			'deactivating' => 'Deactivating…',
			'error'        => 'An error occurred, please try again.',
		];

		$path = self::SDK_PATH;
		$url  = $path::get_url();
		$ver  = $path::get_version();

		wp_enqueue_script( 'edd-sdk-notice', $url . 'assets/build/js/edd-sl-sdk.js', [], $ver, true );
		wp_enqueue_style( 'edd-sdk-notice', $url . 'assets/build/css/style-edd-sl-sdk.css', [], $ver );
		wp_localize_script(
			'edd-sdk-notice',
			'edd_sdk_notice',
			[
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'edd_sdk_notice' ),
				'activating'   => $strings['activating'],
				'deactivating' => $strings['deactivating'],
				'error'        => $strings['error'],
			]
		);
		wp_add_inline_script( 'edd-sdk-notice', self::check_script() );
	}

	/**
	 * Script for the "Check now" button: post the check, then reload so the
	 * status and "Last checked" lines show the result.
	 */
	private static function check_script(): string {
		return <<<'JS'
document.addEventListener('click', function (event) {
	var button = event.target.closest ? event.target.closest('.artifex-license-check') : null;
	if (!button || button.disabled) {
		return;
	}
	event.preventDefault();
	var result = button.nextElementSibling;
	var body = new FormData();
	body.append('action', button.getAttribute('data-action'));
	body.append('_ajax_nonce', button.getAttribute('data-nonce'));
	button.disabled = true;
	button.textContent = button.getAttribute('data-checking');
	fetch(window.ajaxurl, { method: 'POST', credentials: 'same-origin', body: body })
		.then(function (response) { return response.json(); })
		.then(function (json) {
			if (json && (json.success || (json.data && json.data.message))) {
				window.location.reload();
				return;
			}
			throw new Error('check failed');
		})
		.catch(function () {
			button.disabled = false;
			button.textContent = button.getAttribute('data-label');
			if (result) {
				result.textContent = button.getAttribute('data-error');
			}
		});
});
JS;
	}

	/**
	 * Output the overlay element the SDK JS looks for. Call once, in the footer
	 * of the screen that hosts the control.
	 */
	public static function overlay(): void {
		if ( self::is_available() ) {
			echo '<div class="edd-sdk-notice--overlay"></div>';
		}
	}

	private static function esc_html( string $text ): string {
		return function_exists( 'esc_html' ) ? esc_html( $text ) : htmlspecialchars( $text, ENT_QUOTES );
	}

	private static function esc_attr( string $text ): string {
		return function_exists( 'esc_attr' ) ? esc_attr( $text ) : htmlspecialchars( $text, ENT_QUOTES );
	}
}
