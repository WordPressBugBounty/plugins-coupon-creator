<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by Artifex Routing Co using {@see https://github.com/BrianHenryIE/strauss}.
 */
declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\Telemetry;

/**
 * Deactivation-reason form shown from the Plugins screen deactivate link.
 *
 * @since 1.0.0
 */
class Goodbye_Form {

	public const OPT_REASON_PREFIX   = 'artifex_telemetry_deactivation_reason_';
	public const OPT_DETAILS_PREFIX  = 'artifex_telemetry_deactivation_details_';
	public const OPT_FOLLOWUP_PREFIX = 'artifex_telemetry_deactivation_followup_';

	private const AJAX_ACTION  = 'artifex_telemetry_goodbye';
	private const NONCE_ACTION = 'artifex_telemetry_goodbye';

	public function __construct(
		private readonly Config $config,
		private readonly Consent $consent,
		private readonly Tracker $tracker,
	) {}

	public function register(): void {
		if ( ! $this->config->include_goodbye_form ) {
			return;
		}

		add_filter( 'plugin_action_links_' . plugin_basename( $this->config->plugin_file ), [ $this, 'filter_action_links' ] );
		add_action( 'admin_footer-plugins.php', [ $this, 'render_form' ] );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'ajax_callback' ] );
	}

	/**
	 * Intercept the deactivate link so the form can show first.
	 *
	 * @param array<string, string> $links
	 * @return array<string, string>
	 */
	public function filter_action_links( $links ) {
		if ( ! $this->consent->is_tracking_allowed() ) {
			return $links;
		}

		if ( isset( $links['deactivate'] ) ) {
			$slug = esc_attr( $this->config->slug );

			// Add the hook the form binds to, and nothing else.
			//
			// This used to also stamp onclick="javascript:event.preventDefault();" onto the
			// anchor, which made the link inert the moment WordPress rendered it and relied
			// on render_form()'s inline jQuery to give it a purpose again. Anything that kept
			// that script from running -- a JS error from another plugin earlier on the page,
			// jQuery missing, scripting switched off -- left Deactivate permanently dead, with
			// no way to switch the plugin off from the Plugins screen at all. The handler now
			// calls preventDefault() itself, so a link that never gets enhanced stays a link
			// that deactivates.
			$links['deactivate'] = str_replace(
				'<a ',
				'<div class="atx-telemetry-goodbye-wrapper"><span class="atx-telemetry-goodbye-form" id="atx-telemetry-goodbye-form-' . $slug . '"></span></div><a id="atx-telemetry-goodbye-link-' . $slug . '" ',
				$links['deactivate']
			);
		}

		return $links;
	}

	/**
	 * Default form strings; filterable per plugin.
	 *
	 * Since 1.2.0 `options` is keyed: stable reason key => display label. The
	 * KEY is what goes over the wire in `deactivation_reason` (the server's
	 * canonical set lives in artifex-telemetry includes/reason-keys.php —
	 * keep in sync); the label is display-only and safe to translate in a
	 * consumer's form-text filter. `followup` maps a reason key to a short
	 * question revealed when that reason is checked; answers ship as the
	 * additive `deactivation_followup` field. `links` maps a reason key to a
	 * deflection link ({label, url}) shown when that reason is checked.
	 *
	 * @return array{heading: string, body: string, options: array<string, string>, followup: array<string, string>, links: array<string, array{label: string, url: string}>, details: string}
	 */
	public function form_default_text(): array {
		return [
			'heading' => 'Sorry to see you go',
			'body'    => 'Before you deactivate the plugin, would you quickly give us your reason for doing so?',
			'options' => [
				'setup-difficult'    => 'Set up is too difficult',
				'lack-documentation' => 'Lack of documentation',
				'missing-features'   => 'Not the features I wanted',
				'better-plugin'      => 'Found a better plugin',
				'installed-mistake'  => 'Installed by mistake',
				'temporary'          => 'Only required temporarily',
				'broken'             => 'Didn\'t work',
			],
			'followup' => [
				'better-plugin'    => 'Which plugin did you choose?',
				'missing-features' => 'What feature were you missing?',
			],
			'links'   => [],
			'details' => 'Details (optional)',
		];
	}

	/**
	 * Filtered + validated form config.
	 *
	 * Back-compat: a consumer filter written against <=1.1.0 returns a plain
	 * LIST of label strings. In that legacy mode the label stays the checkbox
	 * value (byte-identical wire behavior — the server's label map covers
	 * it) and the keyed-only features (followup, links) are disabled.
	 *
	 * @return array{heading: string, body: string, options: array<string, string>, followup: array<string, string>, links: array<string, array{label: string, url: string}>, details: string}
	 */
	public function form_text(): array {
		$form = apply_filters( 'artifex_telemetry_form_text_' . $this->config->slug, $this->form_default_text() );

		if (
			! is_array( $form )
			|| ! isset( $form['heading'], $form['body'], $form['options'], $form['details'] )
			|| ! is_array( $form['options'] )
			|| [] === $form['options']
		) {
			$form = $this->form_default_text();
		}

		if ( array_is_list( $form['options'] ) ) {
			$labels          = array_values( array_filter( $form['options'], 'is_string' ) );
			$form['options'] = array_combine( $labels, $labels );
			$form['followup'] = [];
			$form['links']    = [];

			return $form;
		}

		$options = [];
		foreach ( $form['options'] as $key => $label ) {
			$key = sanitize_key( (string) $key );
			if ( '' !== $key && is_string( $label ) && '' !== $label ) {
				$options[ $key ] = $label;
			}
		}
		$form['options'] = $options;

		// Malformed followup/link entries are dropped individually rather
		// than failing the whole form back to defaults.
		$followup = [];
		if ( isset( $form['followup'] ) && is_array( $form['followup'] ) ) {
			foreach ( $form['followup'] as $key => $question ) {
				if ( isset( $options[ $key ] ) && is_string( $question ) && '' !== $question ) {
					$followup[ $key ] = $question;
				}
			}
		}
		$form['followup'] = $followup;

		$links = [];
		if ( isset( $form['links'] ) && is_array( $form['links'] ) ) {
			foreach ( $form['links'] as $key => $link ) {
				if (
					isset( $options[ $key ] )
					&& is_array( $link )
					&& isset( $link['label'], $link['url'] )
					&& is_string( $link['label'] ) && '' !== $link['label']
					&& is_string( $link['url'] ) && '' !== $link['url']
				) {
					$links[ $key ] = [ 'label' => $link['label'], 'url' => $link['url'] ];
				}
			}
		}
		$form['links'] = $links;

		return $form;
	}

	/**
	 * Print the form markup + assets on the Plugins screen footer.
	 */
	public function render_form(): void {
		if ( ! $this->consent->is_tracking_allowed() ) {
			return;
		}

		$form = $this->form_text();
		$slug = esc_attr( $this->config->slug );

		$html = '<div class="atx-telemetry-goodbye-head"><strong>' . esc_html( $form['heading'] ) . '</strong></div>';
		$html .= '<div class="atx-telemetry-goodbye-body"><p>' . esc_html( $form['body'] ) . '</p>';
		$html .= '<div class="atx-telemetry-goodbye-options"><p>';
		foreach ( $form['options'] as $key => $label ) {
			// Keyed mode sends the stable key over the wire; legacy mode
			// (form_text() collapsed a label list) sends the label itself.
			$id    = $slug . '-' . sanitize_title( (string) $key );
			$html .= '<input type="checkbox" name="atx-telemetry-goodbye-options[]" id="' . esc_attr( $id ) . '" value="' . esc_attr( (string) $key ) . '"> <label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label><br>';
			if ( isset( $form['followup'][ $key ] ) ) {
				$html .= '<span class="atx-telemetry-goodbye-extra" id="' . esc_attr( $id ) . '-extra" style="display:none;">';
				$html .= '<input type="text" name="atx-telemetry-goodbye-followup[' . esc_attr( (string) $key ) . ']" placeholder="' . esc_attr( $form['followup'][ $key ] ) . '" maxlength="250" style="width:100%;margin:2px 0 6px;">';
				$html .= '</span>';
			} elseif ( isset( $form['links'][ $key ] ) ) {
				$html .= '<span class="atx-telemetry-goodbye-extra" id="' . esc_attr( $id ) . '-extra" style="display:none;margin:2px 0 6px;">';
				$html .= '<a href="' . esc_url( $form['links'][ $key ]['url'] ) . '" target="_blank" rel="noopener">' . esc_html( $form['links'][ $key ]['label'] ) . '</a><br>';
				$html .= '</span>';
			}
		}
		$html .= '</p><label for="atx-telemetry-goodbye-details">' . esc_html( $form['details'] ) . '</label><textarea name="atx-telemetry-goodbye-details" id="atx-telemetry-goodbye-details" rows="2" style="width:100%"></textarea>';
		$html .= '</div></div>';
		$html .= '<p class="atx-telemetry-spinner"><span class="spinner"></span> ' . esc_html( 'Submitting form' ) . '</p>';
		?>
		<div class="atx-telemetry-goodbye-bg"></div>
		<style>
			.atx-telemetry-form-active .atx-telemetry-goodbye-bg {
				background: rgba( 0, 0, 0, .5 );
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}
			.atx-telemetry-goodbye-wrapper {
				position: relative;
				z-index: 999;
				display: none;
			}
			.atx-telemetry-form-active .atx-telemetry-goodbye-wrapper {
				display: block;
			}
			.atx-telemetry-goodbye-form {
				display: none;
			}
			.atx-telemetry-form-active .atx-telemetry-goodbye-form {
				position: absolute;
				bottom: 30px;
				left: 0;
				max-width: 400px;
				background: #fff;
				white-space: normal;
			}
			.atx-telemetry-goodbye-head {
				background: #0073aa;
				color: #fff;
				padding: 8px 18px;
			}
			.atx-telemetry-goodbye-body {
				padding: 8px 18px;
				color: #444;
			}
			.atx-telemetry-spinner {
				display: none;
			}
			.atx-telemetry-spinner .spinner {
				float: none;
				margin: 4px 4px 0 18px;
				vertical-align: bottom;
				visibility: visible;
			}
			.atx-telemetry-goodbye-footer {
				padding: 8px 18px;
			}
		</style>
		<script>
			jQuery( function ( $ ) {
				$( "#atx-telemetry-goodbye-link-<?php echo $slug; ?>" ).on( "click", function ( e ) {
					// Held here rather than in an onclick attribute on the link itself: if this
					// script never runs, Deactivate has to keep working.
					e.preventDefault();
					var url = document.getElementById( "atx-telemetry-goodbye-link-<?php echo $slug; ?>" );
					$( 'body' ).toggleClass( 'atx-telemetry-form-active' );
					$( "#atx-telemetry-goodbye-form-<?php echo $slug; ?>" ).fadeIn();
					$( "#atx-telemetry-goodbye-form-<?php echo $slug; ?>" ).html( <?php echo wp_json_encode( $html ); ?> + '<div class="atx-telemetry-goodbye-footer"><p><a id="atx-telemetry-submit-<?php echo $slug; ?>" class="button primary" href="#"><?php echo esc_js( 'Submit and Deactivate' ); ?></a>&nbsp;<a class="secondary button" href="' + url + '"><?php echo esc_js( 'Just Deactivate' ); ?></a></p></div>' );
					// Reveal a reason's follow-up question / deflection link when
					// its checkbox is ticked.
					$( "#atx-telemetry-goodbye-form-<?php echo $slug; ?>" ).find( "input[name='atx-telemetry-goodbye-options[]']" ).on( 'change', function () {
						$( '#' + $( this ).attr( 'id' ) + '-extra' )[ this.checked ? 'fadeIn' : 'fadeOut' ]();
					} );
					$( '#atx-telemetry-submit-<?php echo $slug; ?>' ).on( 'click', function ( e ) {
						e.preventDefault();
						var $form = $( "#atx-telemetry-goodbye-form-<?php echo $slug; ?>" );
						$form.find( ".atx-telemetry-goodbye-body, .atx-telemetry-goodbye-footer" ).fadeOut();
						$form.find( ".atx-telemetry-spinner" ).fadeIn();
						var values = [];
						$form.find( "input[name='atx-telemetry-goodbye-options[]']:checked" ).each( function () {
							values.push( $( this ).val() );
						} );
						// Follow-up answers, only for checked reasons with text.
						var followup = {};
						$form.find( "input[name='atx-telemetry-goodbye-options[]']:checked" ).each( function () {
							var extra = $form.find( '#' + $( this ).attr( 'id' ) + '-extra input' );
							if ( extra.length && $.trim( extra.val() ) !== '' ) {
								followup[ $( this ).val() ] = extra.val();
							}
						} );
						$.post(
							ajaxurl,
							{
								action: '<?php echo self::AJAX_ACTION; ?>',
								values: values,
								followup: followup,
								details: $form.find( '#atx-telemetry-goodbye-details' ).val(),
								security: '<?php echo wp_create_nonce( self::NONCE_ACTION ); ?>'
							},
							function () {
								window.location.href = url;
							}
						);
					} );
					$( '.atx-telemetry-goodbye-bg' ).on( 'click', function () {
						$( "#atx-telemetry-goodbye-form-<?php echo $slug; ?>" ).fadeOut();
						$( 'body' ).removeClass( 'atx-telemetry-form-active' );
					} );
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Store the submitted reason and send the goodbye payload.
	 */
	public function ajax_callback(): void {
		check_ajax_referer( self::NONCE_ACTION, 'security' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_die( '', '', 403 );
		}

		if ( isset( $_POST['values'] ) && is_array( $_POST['values'] ) ) {
			$values = array_map( 'sanitize_text_field', wp_unslash( $_POST['values'] ) );
			update_option( self::OPT_REASON_PREFIX . $this->config->slug, wp_json_encode( $values ) );
		}
		if ( isset( $_POST['details'] ) ) {
			update_option( self::OPT_DETAILS_PREFIX . $this->config->slug, sanitize_text_field( wp_unslash( $_POST['details'] ) ) );
		}
		if ( isset( $_POST['followup'] ) && is_array( $_POST['followup'] ) ) {
			$followup = [];
			foreach ( wp_unslash( $_POST['followup'] ) as $key => $answer ) {
				$key = sanitize_key( (string) $key );
				if ( '' === $key || ! is_string( $answer ) ) {
					continue;
				}
				$answer = substr( sanitize_text_field( $answer ), 0, 250 );
				if ( '' !== $answer ) {
					$followup[ $key ] = $answer;
				}
			}
			if ( [] !== $followup ) {
				update_option( self::OPT_FOLLOWUP_PREFIX . $this->config->slug, wp_json_encode( $followup ) );
			} else {
				delete_option( self::OPT_FOLLOWUP_PREFIX . $this->config->slug );
			}
		}

		// Forced: the unforced call was cadence-gated, so the survey ping
		// usually no-oped and the data only left on the deactivation hook.
		$this->tracker->do_tracking( true );

		wp_die( 'success' );
	}
}
