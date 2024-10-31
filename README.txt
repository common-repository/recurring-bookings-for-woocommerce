=== Recurring Bookings for WooCommerce ===
Contributors: bouncingsprout
Tags: multiple, bookings, WooCommerce, recurring, repeat
Requires at least: 3.0.1
Tested up to: 5.7
Requires PHP: 7.0
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Recurring Bookings for WooCommerce - works with WooCommerce Bookings to create multiple or repeated bookings

== Description ==

This plugin adds an important but missing feature of WooCommerce's Bookings extension, the ability to make multiple or repeated bookings in one go. At the moment, you can only make one booking at a time. This plugin takes that limit away, and does so in a very streamlined, easy-to-use interface.

The plugin includes an extensive error-checking system, that checks the availability of each of the bookings you ask the plugin to make, before actually making the bookings. If it finds a clash, or any other error such as trying to book at an unavailable time, or for too many people, the plugin displays a warning. Before you make a booking, you'll see handy messages reminding you about the product criteria, so you don't have to go back a screen to find out what rules you set.

The Professional Edition really takes the plugin to the next level, by allowing customers to make bookings themselves. Now, you don't need to make bookings for your hirers or clients at all. If you are thinking of upgrading to the professional edition, just consider one thing. How much time will letting customers making their own repeat bookings save? And what is that time worth to you? The Professional Edition also has an enhanced error handling mechanism, that allows you to make the plugin keep booking dates, even if other bookings in that run clashed or failed. Take for example, a hall hire for a monthly village meeting, but you forgot that on one particular date, the hall was already booked for a party. Error handling will take care of this, booking the rest, while allowing you to deal with the clash accordingly. Plus lots more - read below for the full features list.

This really is a must-have plugin for anyone who uses WooCommerce Bookings.

== Features ==

* Assign multiple bookings to any user, or any product.
* Advanced date selection system, so you can recur over days, weeks or months.
* Freestyle (legacy) mode allows you to pick a bunch of random dates to book.
* Familiar interface that enhances the standard 'Add Booking' screen.
* Supports resources.
* Supports persons and person types.
* No information overload - if a product doesn't have resources or persons, you won't be bothered about them on the admin screen.
* Dry-run system and error reporting - before processing the bookings, the plugin will perform a check first to prevent any clashes. See the availability of your dates in real time.
* Order creation - create an order, or even add to an existing one.
* Order costs fully calculated, taking taxes, date/time ranges, persons and resources into account.

== Pro Features ==

* Let customers make bookings themselves.
* Integrates with WooCommerce Deposits, to offer customers payment plans.
* When recurring by month, select different days, such as the last day of the month, or every second Tuesday.
* Integrates with WooCommerce Subscriptions so customers can make bookings only if they have an active subscription.
* Active Subscriptions can also provide a discount on individual booking prices, or make bookings free (making the subscription the income stream).
* Let customers use intervals - where the booking recurs in a non-consecutive way. E.g. every OTHER month.
* Use a date to specify the end of a set of recurrences, rather than a number.
* Enhanced error handling - the free version stops processing if it detects clashes. The pro version can keep booking any dates that are available, meaning you only have to reschedule the one or two that failed.

== Frequently Asked Questions ==

= I can't see my bookings =

In the vast majority of cases, this is because you have tried to make bookings for dates or times the specified product can't handle. An easy way to check is to see if you can make the same booking on the public side. If you can't there, you can't with this plugin either. If you are sure your booking dates and times are correct, let us know on the support forum.

= I have the following need (enter use case here) in my business. Can your plugin do that? =

If you have a particular need within your own business environment, please let us know. If it is likely to be something that we feel might benefit others, we'll add it to the roadmap. If not, we can let you know other ways we can help.

== Screenshots ==

1. The admin screen to create bookings
2. An example booking list full of recurring bookings
3. A sample order created by the plugin

== Installation ==

Install the plugin via the 'Add plugin' screen, or manually, by downloading the Zip file.

Once activated, please opt-in to the Freemius system so we can provide faster user support, and collect some non-sensitive diagnostic information to help us deal with any bugs.

== Changelog ==

= 2.0.0 =
* Complete overhaul

= 1.5 =
* Update contact information
* Update Freemius library
* Add support for latest WooCommerce version
* Increase required PHP version to 7.0 in line with WooCommerce requirement
* Tested with WooCommerce Bookings 1.15.15

= 1.4 =
* Fix bug that limited products to 10 (thanks to Kees@Klokwerk!)
* Tested up to 5.2.3

= 1.3 =
* Update links to support page
* Testing to 5.2.2
* Upgrade date input method
* Move freestyle mode to free tier (yay!)
* Improve responsiveness of admin screen
* Add dry run system and error reporting
* Add debug information for enhanced bug reporting
* Rewrite instructional text
* Update Freemius

= 1.2 =
* Add support for products with resources

= 1.1 =
* Upgrade time input method.
* Rewrite information block.
