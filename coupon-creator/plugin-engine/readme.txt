=== Plugin Engine ===

== Changelog ==
= 4.1.0 May 13th 2024 =

* Feature - Add V2 fields repeater field support.
* Tweak - Chanage analytics sanitization to allow for GA4 format.
= 4.0.0 February 21st 2024 =

* Feature - Add V2 fields support.
* Fix - Update bootstrap logic to make sure plugin engine will correctly load completely in the context of plugin activations requests, thanks Lucatume for the fix.
- TODO add automated tests from https://github.com/the-events-calendar/tribe-common/pull/1734/files

= 3.3.0 March 9th, 2023 =

* Feature - Add template and cache classes to more feature support.
* Fix - Update Di52 to latest version to prevent conflicts.

= 3.2.2 April 4th, 2022 =

* Fix - Update Di52 to prevent fatal errors with other plugins using it.

= 3.2.1 January 14th, 2021 =

* Fix - Change duplicate feature's meta field copy to improve security.
* Fix - Fatal error that can happen in the admin on newer versions of WordPress.

= 3.2 March 8th, 2021 =

* Fix - Updates to support jQuery change in WordPress 5.7.

= 3.1.1 August 25th, 2020 =

* Fix - Modify the security check on saving meta fields.

= 3.2 TBD =

* Fix - Updates to support jQuery change in WordPress 5.7.

= 3.1.1 August 25th, 2020 =

* Fix - Modify the security check on saving meta fields.

= [3.1.0] August 11th, 2020 =

* Feature - Add Duplicate Class to duplicate post types and all content, taxonimies, and custom fields.
* Tweak - Update wp-color-picker-alpha to 2.1.4.
* Tweak - Extended support for namespaced classes in the Autoloader.

= 3.0.1 January 28th, 2020 =
* Tweak - Update lucatume/di52 to 2.0.12 to prevent conflicts with The Events Calendar 5.0

= 3.0 August 14th, 2019 =
* Add - Plugin Dependency Checker to prevent incompatible versions from loading
* Tweak - Increase minimum PHP version to 5.6 and WordPress 4.9

= 2.5.6 March 11th, 2019 =
* Add - A filter on coupon content to use to modify the allowed tags