=== Simple Paid Listings ===
Contributors: mikejolley
Requires at least: 3.8
Tested up to: 4.0
Stable tag: 1.1.15
License: GNU General Public License v3.0

Add paid listing functionality. Set a price per listing and take payment via Stripe or PayPal before the listing becomes published.

= Documentation =

Usage instructions for this plugin can be found on the wiki: [https://github.com/mikejolley/WP-Job-Manager/wiki/Simple-Paid-Listings](https://github.com/mikejolley/WP-Job-Manager/wiki/Simple-Paid-Listings).

= Support Policy =

I will happily patch any confirmed bugs with this plugin, however, I will not offer support for:

1. Customisations of this plugin or any plugins it relies upon
2. Conflicts with "premium" themes from ThemeForest and similar marketplaces (due to bad practice and not being readily available to test)
3. CSS Styling (this is customisation work)

If you need help with customisation you will need to find and hire a developer capable of making the changes.

== Installation ==

To install this plugin, please refer to the guide here: [http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Changelog ==

= 1.1.15 =
* Only enqueue stripe scripts when payment needs to be taken.

= 1.1.14 =
* Load translation files from the WP_LANG directory.
* Updated the updater class.

= 1.1.13 =
* Uninstaller.

= 1.1.12 =
* Fix button text when paid listing disabled.

= 1.1.11 =
* Moved self::get_job_listing_cost() so it can be used to disable paid listing.

= 1.1.10 =
* WP_Job_Manager_Simple_Paid_Listings::get_job_listing_cost() and filter.

= 1.1.9 =
* Reset expirey date during renewal.

= 1.1.8 =
* Fire action when payment is complete.

= 1.1.7 =
* Support renewals.

= 1.1.6 =
* Add slash on end of home_url for IPN response.

= 1.1.5 =
* Hide pending payment jobs from 'all' list

= 1.1.4 =
* Added new updater - This requires a licence key which should be emailed to you after purchase. Past customers (via Gumroad) will also be emailed a key - if you don't recieve one, email me.

= 1.1.3 =
* Pass email to stripe

= 1.1.2 =
* Different method for triggering click events
* wp_job_manager_spl_admin_email filter

= 1.1.1 =
* Fix PayPal headers for upcoming API changes

= 1.1.0 =
* Allow payment from job dashboard. Requires Job Manager 1.1.2.

= 1.0.3 =
* Fixed issue where expirey date was not set on new submissions

= 1.0.0 =
* First release.