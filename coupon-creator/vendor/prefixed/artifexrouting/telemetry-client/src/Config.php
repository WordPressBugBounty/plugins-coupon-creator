<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by Artifex Routing Co using {@see https://github.com/BrianHenryIE/strauss}.
 */
declare( strict_types=1 );

namespace Pngx\Vendor\Artifex\Telemetry;

/**
 * Immutable tracker configuration for one plugin.
 *
 * @since 1.0.0
 */
final class Config {

	public const MARKETING_NONE     = 0;
	public const MARKETING_COMBINED = 1; // Ask for email permission in the opt-in notice.
	public const MARKETING_FOLLOWUP = 2; // Ask in a second notice after opt-in.

	/**
	 * @param string $endpoint             Telemetry server URL (the ?usage_tracker=hello suffix is appended).
	 * @param string $plugin_file          Absolute path to the consumer plugin's main file.
	 * @param string $slug                 Wire slug sent as plugin_slug. Must never change for a shipped
	 *                                     plugin — the server dedupes tracked sites by URL + this value.
	 * @param string[] $options            Names of wp_options to report (each is only sent when the stored
	 *                                     array contains the wisdom_registered_setting flag).
	 * @param bool   $require_optin        Whether the user must opt in before anything is sent.
	 * @param bool   $include_goodbye_form Whether to show the deactivation-reason form.
	 * @param int    $marketing            One of the MARKETING_* constants.
	 * @param string $schedule             Default tracking cadence: daily, weekly or monthly.
	 * @param string|null $secret          Per-product HMAC secret. When set, reports are signed
	 *                                     (sig/sig_ts fields + header) so the server can optionally
	 *                                     authenticate them. Null = unsigned. The secret ships in
	 *                                     plugin code, so it is spray-filtering + rotation, not
	 *                                     strong auth; each product must use its own value.
	 */
	public function __construct(
		public readonly string $endpoint,
		public readonly string $plugin_file,
		public readonly string $slug,
		public readonly array $options = [],
		public readonly bool $require_optin = true,
		public readonly bool $include_goodbye_form = true,
		public readonly int $marketing = self::MARKETING_NONE,
		public readonly string $schedule = 'monthly',
		public readonly ?string $secret = null,
	) {}
}
