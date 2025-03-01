=== Tapfiliate ===
Contributors: Tapfiliate
Tags: javascript, Tapfiliate, affiliate, marketing
Requires at least: 4.4
Tested up to: 6.3
Stable tag: 3.2.1
License: MIT License
Requires PHP: 8.0

Create, track and grow your affiliate program. Tapfiliate helps businesses and influencers grow together, through affiliate marketing. [Learn more about our features here](https://tapfiliate.com/).

== Description ==

Easily integrate Tapfiliate with WordPress and add tracking codes to any page.

Tapfiliate allows you to easily create, track and manage your own affiliate marketing and referral programs. Our affiliate tracking software integrates seamlessly with Wordpress, WooCommerce, WooCommerce Subscriptions and WP Easy Cart, so you can begin using affiliate marketing to grow your business in just minutes.

For a complete guide for how to use Tapfiliate, [visit Tapfiliate’s Developer Docs](https://tapfiliate.com/docs/).

= Some Key Features =
* Automatic integration with Wordpress
* Easy management with automated workflows and triggers
* Individual affiliate portals with branded dashboard
* One-Click social media sharing with images, deeplinks, banners, or video
* Customizable commissions and bonus structures
* White labelled affiliate pages that match your brand and domain
* And great customer support

== Frequently Asked Questions ==

= Where can I find Tapfiliate Documentation? =

Learn about Tapfiliate with our [Docs](https://tapfiliate.com/docs/) and [Support Center](https://support.tapfiliate.com).

= Where can I find API documentation? =

Tapfiliate's API lets you go beyond the options offered out of the box and create customized integrations that wouldn't be otherwise possible. You can find the documentation for the [API here](https://tapfiliate.com/docs/rest/).

= Does Tapfiliate offer any other language options? =

The Affiliate portal is offered in the following languages: English, French, German, Spanish, Dutch, (Brazilian) Portuguese. The Admin side of the platform is only available in English.

= Which Tapfiliate features are supported by this plugin? =

WooCommerce:
* Order id and order amount tracking
* Coupon code tracking
* Customer tracking
* Automated lifetime commissions
* Automatic refund and dispute handling

WooCommerce Subscriptions:
* Automated recurring commissions

Wordpress:
* External id and order amount tracking (from url parameters)
* Custom Commission Types

== Installation ==

The Tapfiliate Plugin can be installed from the WordPress Plugin Directory.

For manual installations, follow the following steps:

1. Upload `tapfiliate` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add your Tapfiliate account id to the settings (Admin > Settings > Tapfiliate)

== Screenshots ==

1. Dashboard
2. Reporting

== Changelog ==

= 2.1.2 =
* First version on the WordPress plugin directory

= 2.2 =
* Implement Tapfilate coupons functionality: Track conversions by coupon.

= 2.3 =
* Added filters to allow manipulation of the tracking snippet and its arguments

= 2.4 =
* Fix faulty function name

= 2.5 =
* Fix coupon code serialization

= 3.0.0 =
* Breaking changes: program group functionality removed
* Added support for Tapfiliate's Customer functionality
* WooCommerce: Added ability to add commission types per product and category
* WooCommerce: Added WooCommerce Subscriptions support
* WooCommerce: Added support to connect WooCommerce to Tapfiliate for automatic handling of order payment confirmation, (partial) refunds, subscription updates, and lifetime commissions
* WordPress: Added shortcodes for adding the Tapfiliate code to specific pages

= 3.0.1 =
* Fix bug where existence of WooCommerce was not properly checked before using WooCommerce specific functionality

= 3.0.2 =
* Remove unnecessary admin notice

= 3.0.3 =
* Improved placement of version check

= 3.0.4 =
* Prevent false positives for "WooCommerce enabled" on options page

= 3.0.5 =
* Fix plugin upgrade bug
* Add settings link to plugin page

= 3.0.6 =
* Updated application base url

= 3.0.7 =
* Add commission and coupon source data to webhook payloads for better handling of recurring/lifetime commissions

= 3.0.8 =
* Layout improvements
* Added FAQ

= 3.0.11 =
* Fixed layout

= 3.0.12 =
* Tested up to Wordpress 6.1

= 3.0.13 =
* Tested up to Wordpress 6.1.1
* Fix to prevent XSS has been added

= 3.0.14 =
* Issue with versions has been fixed

= 3.0.15 =
* Tested up to Wordpress 6.2

= 3.0.16 =
* Tested up to Wordpress 6.3

= 3.0.17 =
* Git SVN fix

= 3.0.18 =
* Customer Id issue has been fixed

= 3.1.0 =
* HPOS functionality has been implemented

= 3.1.1 =
* Small shortcode fix

= 3.1.2 =
* Codestyle fix

= 3.1.3 =
* Arguments casts fix

= 3.2.0 =
* WP Easy Cart functionality has been fixed

= 3.2.1 =
* Small fix to prevent error when enable plugin

== Upgrade Notice ==

= 3.0.0 =
Added support for Customers, WooCommerce Subscriptions, per product / category commissions and more!
