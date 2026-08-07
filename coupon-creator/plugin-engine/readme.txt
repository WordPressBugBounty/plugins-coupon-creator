=== Plugin Engine ===

== Changelog ==

= 4.0.8 August 7th 2026 =

* Feature - Add a V2 Color field (wp-color-picker) for widget theming.
* Security - Sanitize the store address submitted from the license screen before it is used to contact the licensing server.
* Fix - Resolve the logger through its bound slug. pngx( 'logger' ) was never bound — the logger is registered as 'pngx.logger' — so the container read the slug as a class name and threw a fatal whenever the async-process support probe ran on an AJAX or cron request.
* Fix - Degrade Lazy_CSV_Iterator to an empty iterator when the csv cannot be opened instead of fataling.
* Tweak - Route every licensing and update request through the pngx_update_url filter, so the store address can change without shipping a new release first.
* Tweak - Allow safe HTML in admin field descriptions.
* Tweak - Commit the flatpickr asset like the other vendored libraries.

= 4.0.7 July 25th 2026 =

* Fix - Cast the transient chunk size to int for str_split() on PHP 8.1+.
* Fix - Correct the feature-detection container key in the Cache chunked-transient paths.
* Fix - Stop the csv iterator skipping the first row on PHP 8.

= 4.0.6 July 21st 2026 =

* Security - Harden the dropdown AJAX endpoint and mask saved secrets in the password component.
* Tweak - Serve a11y-dialog from tracked resources.

= 4.0.5 July 18th 2026 =

* Feature - Add a version-drift tripwire so a stale bundled engine is caught early.
* Fix - Replace the deprecated utf8_decode() with a byte-length check in save_split_data().

= 4.0.4 July 15th 2026 =

* Fix - PHP 8.4 implicit-nullable deprecations.
* Fix - Guard substr( null ) in Register_Post_Type.
* Tweak - PHP 8.2 compatibility and saved DB version tracking.
* Tweak - Drop the bundled di52 and monolog and use the prefixed di52 provided by the parent plugin.

= 4.0.3 May 20th 2026 =

* Security - Sanitize exec() output in the support screen.
* Security - Replace SCRIPT_FILENAME direct access checks with ABSPATH.
* Fix - Negative margin-bottom on variety field descriptions.
* Tweak - Prefix the bundled lucatume/di52 under the Pngx\Vendor\ namespace via Strauss to prevent global class collisions with other plugins that ship their own di52.
* Tweak - Update composer dependencies.

= 4.0.2 January 14th 2025 =

* Security - Improved input sanitization and output escaping.
* Tweak - Updated internal dependencies.

= 4.0.1 May 13th 2024 =

* Feature - Add V2 fields repeater field support.
* Tweak - Change analytics sanitization to allow for GA4 format.

= 4.0.0 February 21st 2024 =

* Feature - Add V2 fields support.
* Fix - Update bootstrap logic to make sure plugin engine will correctly load completely in the context of plugin activations requests, thanks Lucatume for the fix.

= 3.3.0 March 9th 2023 =

* Feature - Add template and cache classes to more feature support.
* Fix - Update Di52 to latest version to prevent conflicts.

= 3.2.2 April 4th 2022 =

* Fix - Update Di52 to prevent fatal errors with other plugins using it.

= 3.2.1 January 14th 2022 =

* Fix - Change duplicate feature's meta field copy to improve security.
* Fix - Fatal error that can happen in the admin on newer versions of WordPress.

= 3.2 March 8th 2021 =

* Fix - Updates to support jQuery change in WordPress 5.7.

= 3.1.1 August 25th 2020 =

* Fix - Modify the security check on saving meta fields.

= 3.1.0 August 11th 2020 =

* Feature - Add Duplicate Class to duplicate post types and all content, taxonomies, and custom fields.
* Tweak - Update wp-color-picker-alpha to 2.1.4.
* Tweak - Extended support for namespaced classes in the Autoloader.

= 3.0.1 January 28th 2020 =

* Tweak - Update lucatume/di52 to 2.0.12 to prevent conflicts with The Events Calendar 5.0.

= 3.0 August 14th 2019 =

* Add - Plugin Dependency Checker to prevent incompatible versions from loading.
* Tweak - Increase minimum PHP version to 5.6 and WordPress 4.9.

= 2.5.6 March 11th 2019 =

* Add - A filter on coupon content to use to modify the allowed tags.