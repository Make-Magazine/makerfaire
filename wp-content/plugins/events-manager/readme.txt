=== Events Manager ===
Contributors: msykes, pxlite, nutsmuggler, netweblogic
Donate link: https://wp-events-plugin.com
Tags: bookings, calendar, tickets, events, buddypress, event management, google maps, maps, locations, registration, zoom
Text Domain: events-manager
Requires at least: 5.2
Tested up to: 6.4.1
Stable tag: 6.4.6.4
Requires PHP: 5.3

Fully featured event registration management including recurring events, locations management, calendar, Google map integration, booking management

== Description ==

Events Manager is a full-featured event registration plugin for WordPress based on the principles of flexibility, reliability and powerful features!

*Version 6 is the biggest update to date, with a complete overhaul of our calendars, lists, searches and other front-end items. Check it out and enjoy!*

* [Demo](https://eventsmanager.site)
* [Documentation](http://wp-events-plugin.com/documentation/)
* [Tutorials](http://wp-events-plugin.com/tutorials/)

= Main Features =

* Beautiful calendars, search pages, lists, grids and booking forms to enhance your site events.
* Easy event registration (single day with start/end times)
* Recurring and long (multi-day) event registration
* Bookings Management (including approval/rejections, export CVS, and more!)
* Multiple Tickets
* *(New)* Fully-featured graph and statistics including bar/line/pie with comparison and stacking
* MultiSite Support
* Multiple Location Types
 * Physical Locations
 * Online Events (URLs)
 * [Zoom Webinars/Meetings Integration](https://wordpress.org/plugins/events-manager-zoom/)
* BuddyPress Support
 * Submit Events
 * Group Events
 * Personal Events
 * Activity Stream
 * more on the way
* Guest/Member Event submissions
* Assign event locations and view events by location
* Event categories
* Easily create custom event attributes (e.g. dress code)
* Google Maps [(see our API usage recommendations)](https://wp-events-plugin.com/documentation/google-maps/api-usage/?utm_source=repo&utm_medium=readme&utm_campaign=gmaps-api)
* Advanced permissions - restrict user management of events and locations.
* Widgets for Events, Locations and Calendars
* Fine grained control of how every aspect of your events are shown on your site, easily modify templates from the settings pages and template files
* iCal Feed (single and all events)
* Add to Google Calendar buttons
* RSS Feeds
* Compatible with SEO plugins
* Timezone Support - create events in different timezones 
* Plenty of template tags and shortcodes for use in your posts and pages
* Actively maintained and supported
* Lots of documentation and tutorials
* And much more!

= Data Privacy and GDPR Compliance =
We provide the tools to [help you be GDPR compliant](http://wp-events-plugin.com/documentation/data-privacy-gdpr-compliance/), including:

* export/erasure of data via the WordPress Privacy Tools, including booking, event and location data
* consent checkboxes on our booking, event and location forms on the frontend
* settings to control what can be exported/erased as well as where/when to place consent requests
* sample text for your site privacy policy describing what Events Manager does with personal data

= Premium Features =

*NEW* Check out our [WooCommerce add-on](https://wp-events-plugin.com/woocommerce/)! Sell bookings directly with WooCommerce.

We have a premium [Pro add-on for Events Manager](http://eventsmanagerpro.com/gopro/) which not only demonstrates the flexibility of Events Manager, but also adds some important features including but not limited to:

* WooCommerce integration included FREE (limited-time offer)
* PayPal, Stripe, Authorize.net and Offline Payments
* Custom booking forms
* Individual Attendee custom forms
* Printable Invoices and Tickets
* Send PDF tickets/invoices by email automatically
* Check In/Out
* QR Scanning
 * Manage bookings on your phone
 * Check In/Out users
* Waitlists
* Automation (NEW!) - ultimate flexibility in automation!
 * Triggers:
  * X time before/after events start
  * When a booking status changes
  * When a booking was booked x time ago
 * Actions
  * Send Webhook (Zapier, MS Automation and many other services)
  * Send Email
* Coupon Codes
* Custom booking email per event and gateway
* Faster support via private Pro forums

For more information or to go pro, [visit our plugin website](http://wp-events-plugin.com/features/).

= Additional Plugin Integrations =

Whilst there's many third party integrations with our own plugin, here's some we've integrated ourselves!

* Included in Events Manager (automatic integration)
 * [BuddyPress](https://wordpress.org/plugins/buddypress/)
 * [WP FullCalendar](https://wordpress.org/plugins/wp-fullcalendar/)
 * [Thrive Automator](https://wordpress.org/plugins/thrive-automator/)
* Additional Add-Ons
 * [Zoom](https://wordpress.org/plugins/events-manager-zoom/)
 * [WPML Multilingual Sites](https://wordpress.org/plugins/events-manager-wpml/)

== Installation ==

Events Manager works like any standard Wordpress plugin, and requires little configuration to start managing events. If you get stuck, visit the our documentation and support forums.

Whenever installing or upgrading any plugin, or even Wordpress itself, it is always recommended you back up your database first!

= Installing =

1. If installing, go to Plugins > Add New in the admin area, and search for events manager.
2. Click install, once installed, activate and you're done!

Once installed, you can start adding events straight away, although you may want to visit the plugin site documentation and learn how to unleash the full power of Events Manager.

= Upgrading =

1. When upgrading, visit the plugins page in your admin area, scroll down to events manager and click upgrade.
2. Wordpress will help you upgrade automatically.

= Upgrading from version 4 to 5 =

Please [read these instructions](http://wp-events-plugin.com/updating-to-v5/).

== Upgrade Notice ==

For those upgrading from version 4 to 5, please [read these instructions](http://wp-events-plugin.com/updating-to-v5/).

== Frequently Asked Questions ==

See our [FAQ](http://wp-events-plugin.com/documentation/faq/) page, which is updated more regularly.

== Screenshots ==

1. Innovative responsive calendar with rings to show eventful dates, colored by category, clickable to expand more event information.
2. Completely customizable event widgets/blocks and shortcodes.
3. Beautiful event pages which can be completely customized via our settings page.
4. Share your events to popular calendar clients.
5. Display information about your location in widgets, blocks and shortcodes too.
6. Full-featured statistics and insights into your ticket sales via multiple graph types, including comparison and stacking.
7. Easily skip to the future with our new calendar navigation and search filters.
8. Easy-to-use search filters, whether on the phone or desktop.
9. Search for events and locations within a search radius using Google geo searches.
10. Intuitive search UI for your visitors.
11. QUickly switch between search views.
12. View your events on a Google Map, filter with searches.
13. Multiple calendar styles, fully responsive according to the width of the calendar.
14. Responsivve way for mini-calendar to intuitively show dates with many events within a quick glance.
15. Responsive lists that adapt to the size of its containing content.
16. Clean forms for submitting and managing events, as well as booking events for users.
17. Dashboard graph widget for quick review of your event bookings with tons of meaningful data views.
18. Grid view for displaying your upcoming events at a glance

== Changelog ==
= 6.4.6.4 =
* Fixed issues with multiple bookings and anonymous bookings that could rewrite the user name of the account making the booking with the latest user information.
* Fixed setting `is_available` to `EM_Ticket` not having any effect due to protected visibility, which can cause unpredictable behavior.
* Fixed form `.em-ajax-form` not outputting the correct notice box on success.
* Added JS for `em-cancel` button to include custom data into AJAX via the `data-` attribute.
* Added `em_my_bookings_booking_action_links` filter allowing for array insertion of action links for my booking page bookings.

= 6.4.6.3 =
* Fixed telephone field auto-enabling and ignoring EM_PHONE_INTL_ENABLED constant introduced in 6.4.6

= 6.4.6.2 =
* Fixed booking summary showing for free events despite setting set to no.
* Fixed edge cases where bookings do not get deleted properly due to unloaded ticket data.
* Fixed various PHP warnings.

= 6.4.6.1 =
* Fixed array meta key retrieval and saving issues introduced in version 6.4.6.
* Fixed first-time installation PHP errors and errant update notices.
* Added `em_booking_form_js_fields_change_match` filter allowing for programmatic listening of custom field changes to update the booking form summary section.
* Fixed front-end display issues of bookings containing two ticket types.
* Fixed search form button not enabling after changing search params when advanced search is disabled.

= 6.4.6 =
* fixed fatal error caused by use of Pro function in settings page when Pro isn't activated, bug introduced in EM 6.4.5
* fixed XSS vulnerability, disclosure to be followed
* fixed minor security vulnerability allowing anauthorized logged-in users to dismiss EM admin notices
* fixed OAuth errors (such as with PayPa) due to implementations not requring a scope credential
* fixed PHP Warnings in event editor frontend,
* fixed EM Notices appearing twice on booking forms in backend for manual bookings in Pro,
* fixed datetime offset issue when migrating old EM versions without timezones
* fixed file loading issues in some server edge case environments by providing absolute include paths in events-manager.php
* fixed multiple PHP Deprecated dynamic variables warning for PHP 8.2 by adding a $fields_shortcuts static map for short variable names, as well as storing unknown 'dynamic' variables into a dynamic_variables protected property array via __set() and __get() in EM_Object
* changed preference for $shortnames map of field shortcuts for $fields_shortcuts although still supported in EM_Object but preferable to use one static storage for performance improvements
* fixed issues with storage of booking and attendee/spaces meta data
* developers should be aware that storage of array meta data going forwards takes the format of _key|subkey for associative or _key| for sequential arrays, if you stored array keys with an underscore, or subkeys with underscores, please review EM_Object::process_meta() and EM_Booking::process_meta() and the function comments for a better understanding of what to do, as a future update may involve an SQL migration script to migrate old meta fields into the new format
* added sorting option on event search forms - enabled by default on first install, disabled for existing sites updating,
* added international phone number picker - BETA - requires activation via define('EM_PHONE_INTL_ENABLED', true); in wp-config
* fixed PHP 8.2 warnings in tickets
* fixed ordering issues in attendee booking editor displays
* added ical output filters em_ical_output_content_description and em_ical_event_output_content

= 6.4.5 =
* added RSVP functionality (re-confirming a booking)
* add uncancel option so users can undo a cancellation if spaces still available
* switched my bookings page action links to a button dropdown rather than loose links
* fixed class static binding issues with EM_OAuth libraries causing problems in Zoom-enabled bookings
* fixed tippy dropdown button width issues

= 6.4.4 =
* fixed session wakeup issues for the EM_Booking object due to recent atomic tickets update,
* fixed EM_Tickets_Bookings and EM_Ticket_Booking possibly returning erroneous booking property
* added JS booking form helper functions em_booking_form_unhide_success, em_booking_form_enable_button, em_booking_form_disable_button,
* added backwards compatibility for booking ajax responses including the 'result' property rather than the new 'success' property
* fixed calendar navigation issues showing default calendar size according to responsive sizing even when using forced calendar_size="large"
* fixed advanced filters button in calendar not working when search forms disabled in settings page
* moved advanced search trigger button html/php into separate template
* fixed view of calendar changing to default events list format if default search forms disabled in settings
* fixed error messages when updating Events Manager
* moved default view setting to events formatting section, outside of search form options
* fixed booking forms not always auto-hiding after submission is complete,
* fixed scrolling overflow issues for skeleton loaders on booking form

= 6.4.3 =
* changed EM_Booking::set_status() parameters so $email_args is 4th overloaded function to prevent compile errors in Pro 3.2.2 or earlier with PHP 8
* fixed cancelled events not triggering booking cancellations

= 6.4.2 =
* fixed google maps JS errors with embedded Google maps
* fixed booking form issues and added fallbacks for settings and template overrides where booking intents are not present on initial booking form load
* added fallback polyfill function to add booking_intent to booking forms overriden in template files which still uses em_booking_form_footer
* fixed compatiblity issues with other plugins using reCaptcha breaking booking form AJAX process and causing "Network Error" error messages
* added further fallbacks to handle non-network issues triggering  caused by unhandled thrown errors preventing booking form from fully processing
* changed JS use of response.result to response.success in bookingform.js
* fixed fatal PHP error when deleting an event on front-end admin
* fixed fatal error when 0 views are selected/saved to settings or if dbem_search_form_views is saved incorrectly
* added PHP $email_args parameter to EM_Booking status change functions which is passed onto the email() function for limiting admin/attendee sending
* added event status feature including active and cancelled states along with options to email booked users about a cancellation
* added is_cancelled and is_active conditional placeholders
* added #_EVENTSTATUS placeholder
* added cancelled (bool), active (bool) and active_status (int) search arguments for PHP and shortcode arguments
* (minor) fixed PHP 8 error in ticket editor
* fixed booking form summary showing all ticket (non-required) minimum spaces as if booked upon initial form load
* fixed ticket columns not showing on booking admin summary tables
* added multiple ticket support for columns showing single ticket data
* added support for tippy html content in regular tooltip constructor html
* fixed pagination issues with grouped event lists
* fixed calendar advanced search popup not appearing when clicking trigger
* fixed calendar size view change upon a calendar advanced search

= 6.4.1 =
* fixed data privacy consent checkbox not showing on booking form since
* fixed 'undefined' button error
* added back-compat fallback for deleted actions on booking form

= 6.4 =
* revamped booking form html structure (backwards compatible) and added a dynamic booking price summary
* moved booking button into own template
* rewritten booking form JS to remove dependence on jQuery entirely
* added booking intent architecture
* added skeleton loader CSS for more intuitive UI AJAX laoding
* split email logic in EM_Booking->email into email_attendee and email_admins functions
* added EM_Booking->get_currency() for later multi-currency integration possibilities
* changed EM_Booking->boking_status back to public scope
* fixed bug where ticket_id isn't added to EM_Ticket_Booking if supplied via $ticket_data
* fixed calendar links not going through if tooltip and modal previews are disabled
* added minified js files
* split up events-manager.js into separate src files and compiled/minified by a grunt project in src folder
* added min main JS and flatpickr l10n file loading if WP_DEBUG, SCRIPT_DEBUG or EM_DEBUG defined to true
* fixed n/a booking field values when field ID is excessively long
* added migration for copying previously used uuids in booking meta and making it the booking_uuid (without dashes) value given it is still not 'officiallly in use'
* fixed shortcode events_list not recognizing the view argument such as [events_list view="grid"]
* fixed PHP warning on graph dashboard
* added/fixed search form support for inline advanced search and additional options to show/hide fields in both main and advanced search sections,
* added individual settings for search/geo/scope search input fields in both main/advanced search forms
* split up search form templates avoiding redundant data,
* fixed disappearing input fields in responsive forms when search text field disabled,
* fixed/improved logic for calculating when advanced fields and main search bar should/not be shown,
* added view selection to advanced fields when main search bar is not shown
* added fixed ids to view wrappers for fixed pages including event/calendar lists, calendar day lists, location lists, category/tag pages, event/location/tag/category single pages making it easier to use search forms placed on other parts of the page using shortcode
* added em_template_before_{$template_name} and em_template_after_{$template_name} actions when auto-loading via em_locate_template
* added additional verification and revokation methods for tokens in oauth api class if access token is not passed within the url
* added better handling of errors upon OAuth requests
* added em_search_form_footer action and moved hidden input fields for pre-defined search params without selectable fields into action function
* fixed issue with misaligned event image placeholder date in widgets
* tweaked EM_Options::remove to allow deleting entire array options
* moved privacy checkbox to bottom of booking form user/form fields, above summary and buttons
* fixed outdated coupon trigger not firing in jQuery for updated booking form

= 6.3 =
* fixed mobile display issue for selecting list views from search toolbar
* added grid view for events and locations
* tweaked code to run view logic through functions and filters making it easier to add new views
* fixed pagination issues when in a different than default view

= 6.2.1 =
* bugfix for graphs comparing time periods showing same period for both datasets

= 6.2 =
* added booking statistic charts to WP dashboard, bookings dashboard and event bookings manage view

= 6.1.6 =
* fixed bookings admin table pagination and action link issues
* moved AJAX actions from init to wp_loaded
* fixed potential em_booking_save filter bug still mailing emails if short-circuited with a false result,
* added em_booking_added to EM_Booking::save() which should now be used for any new booking being added rather than em_booking_save or em_bookings_add
* added Thrive Automator integration

= 6.1.5 =
* fixed calendar width display issues due to aspect ratios by shifting aspect min-height calculations into JS
* added PHP echo functions EM_Notices::display() and EM_Object::json_encode_e()
* revamped bookings admin tables with support for sorting, name searching, bulk editing
* fixed some issues with term conditional placeholders when categories or tags disabled
* added #_EVENTSTARTDATE placeholder

= 6.1.4 =
* added modal admin messages
* fixed issue where booking button tries to book first ticket irrespective of availability and results in an error
* fixed JS issues presented with internlinked date-range pickers when a value is present
* fixed aesthetic issues for event location editing section when only one location type is permitted
* fixed login URL issues
* added em_login_footer action
* fixed JS bug with calendars displayed in other languages and advanced search disabled
* fixed calendar advanced search modal not showing if general settings disables advanced searches on search forms

= 6.1.3 =
* fixed datepicker range JS issues
* added ability to override email formats (currently only upon installation for event approval email template)
* added is_recurring and not_recurring conditional event placeholders
* added option to include or exclude tags/categories listed in the search filter options, this is available in settings page and as tags_/categories_include or tags_/categories_exclude for shortcode
* fixed potential errors in EM_Ticiket->is_available_to() and changed how EM_Ticket->is_availble() can check availability against a supplied user.
* fixed EM_Ticket_Bookings->validate() so availability of tickets are validated against user booking belongs to rather than current user (fixes permission issues in manual bookings)
* fixed datetime issues when an invalid date is stored and setTimezone is invoked, creating a 1970 date rather than blank value
* added #[\ReturnTypeWillChange] to relevant functions to supress PHP 8.1 deprecated messages
* removed datetimezone and datetime php8.1 alt files in favour of above supression method for now
* (in-dev) fixed issues in 6.1.2.3 dev version related to add/edit recurring events JS errors
* changed booking form headers so they're handled by settings page and can be disabled with blank values
* added option to add Reply-To event owner for booking emails,
* added em_booking_email_admin_args filter enabling extra mail args to be added,
* added $args array param for EM_Mailer::send() so that future enhancements like reply-to can be supplied
* tweaked heading sizes of booking form headings so they are slightly smaller,
* fixed template loading of templates/calendar/calendar.php in calendar-full.php and calendar-small.php templates so they are dynamic and overridable
* added EM_CHECK_THE_LOOP constant to add an additional in_the_loop() check for themes/plugins that mey use em_content outside the loop
* fixed booking conditional placeholder wiping out event conditional placeholders
* changed em_booking_output_condition filter so it's only fired when a matching conditional is found for EM_Booking,
* added em_booking_output_show_condition filter to help determine whether to show a condition
* fixed search results coming back with incorrect styling layout when in responsive/mobile mode
* added output methods for APIs via array for JSON for main objects (events, locations, bookings)
* removed erroneous testing data for filtering categories search option (ids 150 and 151)
* fixed some display issues for selectize particularly in the dashboard area

= 6.1.2.1 =
* fixed php 8.1 fatal error

= 6.1.2 =
* changed EM_Tickets, EM_Bookings and EM_Ticket so event object is stored as referece so get_event() all reference the same parent object,
* changed em_bookings_ticket_exists hook for when tickets don't exist, $EM_Ticket is pssed as a blank EM_Ticket with the requested $ticket_id
* removed erroneous em_boking_save action in em-actions.php before anything is actually saved
* changed the booking_resend_email action to allow AJAX requests
* changed PHP use of get_class() checks in if statements to instanceof so that extended classes pass,
* added $event_ids array passed to em_bookings_deleted and em_bookings_delete actions/filters in EM_Bookings::delete() mass deletion,
* added $force_refresh option to EM_Bookings->get_available_spaces()
* added $ignore_spaces parameter to EM_Ticket->is_available()
* added EM_Ticket->is_available_to() function
* fixed availability issue for unavailable tickets when overriding as an admin
* added extra action hooks to booking admin area
* added reserved 'waitlist' booking status numbers 6-8 (if you use custom status numbers, we advise using above 100 for sake of avoiding core collision)
* fixed some instances where EM_Booking::$disable_restrictions doesn't fully disable every restriction
* added em_event_load_postdata_other_attributes filter allowing for custom event attributes/meta to be loaded with less code
* [template change] moved booking form status notices from template into action-based output, default output of these now reside in the classes/em-event.php file
* fixed some rare instances where EM_Ticket->get_event() doesn't work as intended
* tweaked em_ticket_is_available to fire even with EM_Bookings::$disable_restrictions enabled
* added conditionial custom placeholder recursion support for EM_Ticket_Booking objects
* added JS support for general AJAX forms without need for specific JS code to work using vanilla JS
* added JS EM_Alert function for generating alerts via modal
* added $status and $EM_Booking vars to em_booking_button class
* added code editor features to formats admin section for larger text boxes
* added em-notice alert box css
* added cancellation policy allowing cancellation up to x hours before events begin
* added event_attribute filters allowing for quicker/easier adding/saving custom attributes
* fixed split booking by ticket exports not working since EM 6.1
* fixed single ticket mode booking form not displaying as per settings
* added retroactive fix of calendar templates using old HTML structure (lack of ID in em-calendar enclosing element)
* enabled text input for datepickers
* moved loading of selectize CSS and JS directly into events-manager files instead of separate loading
* fixed has_location_venue conditional displaying for non-physical events
* fixed critical error if registration data is duplicated in error in bookings_meta table
* added fix for add to calendar button when used on themes forcing line breaks within content
* tweaked normalize.css to prevent themes forcing select boxes to display:block (causing flatpickr year select to disappear out of view)
* fixed issues with serialization/wakeup in sessions for booking objects using new atomic tickets structure (fixes issues with Pro Multiple Booking checkouts)
* fixed upgrade failures due to emoji data in bookings,
* fixed issues with older PHP code looping EM_Bookings_Tickets and getting pricing resulting in single-ticket pricing rather than grouped when calling get_price_with_taxes()
* fixed taxonomy event list pagination issues as well as forced AJAX on non-ajax pagination
* fixed formatting typo displaying PHP code rather than translated output
* fixed modal y-axis overflow for advanced search,
* tweaked modal hidden mode to prevent click/touch blocking
* fixed duplicate rows output in CSV split by ticket type
* fixed templates/forms/event-editor.php to show notices within container so styling is inherited
* added duplicate data removal script for updates that somehow duplicated data within the wp_em_bookings table
* fixed PHP warning when no events in grouped listing - changed em_events_output_grouped so first argument returns all events from EM_Events::get(), use em_events_output_grouped_events_dates for split-by-dates array
* fixed login form redirect issues in MultiSite where site_url is different from home_url
* fixed timezone UTC offsets with minutes not saving properly
* fixed incorrect label id for single ticket mode spaces select
* fixed booking data duplication issues when saving personal data in booking admin
* added backwards-compatible measure to support PHP 8.1 and return type requirements in DateTime and DateTimeZone by including duplicate file with return types which are loaded only with 8.1 and greater
* added update retrigger if previous update is detected to not have gone through fully
* fixed login text still showing even if disable login form option set to yes in settings
* added option to use double-date datepicker for event dates
* tweaked event date range so that ticket start/end datepickers are linked
* fixed issue where dates for new event form default to 1970 before creation
* fixed mobile responsive issues with taxonmy and location lists
* fixed location placeholders not working when location and event not published yet
* fixed/removed errant closing p tag in single location default HTML

= 6.1.1 =
* tweaked installation SQL so it's MariaDB-compatible
* fixed php warning when no location permalink provided

= 6.1 =
* WARNING! MAJOR architecture change for how tickets are stored. This should be backward compatible and a well-tested migration process, but (as you should always do anyway) back up before upgrading!
* fixed CSS issues in the bookings admin dashboard
* fixed issues with jQuery UI dialogs losing their styling
* fixed styling issues with the booking form,
* fixed duplicate 'events span x days' field in recurring events editor
* moved em_booking_form_footer action further down the booking form to ensure greater backwards compatibility with Pro updates
* removed upgrade/migration options for EM v4
* added split/atomic tickets so each space booked has its own ticket reference and uuid (backwards compatible)
* added atomic booking meta with dedicated em_bookings_meta table and transition out of the old serialized booking (bookings_meta will still be synced for backwards compatibility for now)
* added uuid v4 to ticket booking records and entire bookings (for later use such as retrieving booking info anonymously)
* improved logic/architecture of booking ticket retrieval of post and validation by compartmentalizing each post/validation to specificly relevant objects
* added new EM_Ticket_Bookings object to group individual ticket bookings of the same ticket within a booking
* added ArrayAccess implementation to EM_Tickets_Bookings and EM_Ticket_Bookings for more sensible traversing for developers
* added __debugInfo() to ticket objects to prevent irrelevant recursion whilst debugging print_r and var_dump
* added improvements to installation process in the event that upgrade routines terminate early or time out
* added multiple new actions for outputting ticket booking information on the booking admin
* added default email attachment array so that all other hooks add to it rather than replace the array
* added output() functionality for individual ticket bookings so that plugins can add placeholders
* changed post handling of ticket booking objects so that new ticket bookings are assigned a uuid immmediately before calling actions/filters

= 6.0.1 =
* breaking changes to templates for those upgrading from 6.0, included warning/nudge for users upgrading from 6.0
* added more fine-grained settings to the Settings > Styling Options section allowing to disable styling elements throughout the plugin
* fixed modal transition CSS issues
* added advanced mode enabling users to directly use some, all or none our plugin template files for formats rather than the settings page
* improved reset option for all overridable placeholder formats in settings allowing for users to reload default formats into individual text boxes
* added ability to override plugin template files via wp-content/plugin-templates/events-manager/
* added styling options to allow the use of theme font family, width, height and weight
* maded default styling options to use theme properties when upgrading from EM < v6.0.1

= 6.0 =
* MAJOR UI overhaul inclusding complete rewrite of calendars, lists, search
* Large overhaul of booking forms, but even more to come here!
* new base CSS framework to offer maximum theme consistency
* partial overhaul of front-end editors to add responsive support (more to come!)
* minor tweaks to dashboard editor meta boxes (more to come)
* responsive support for all front-end display items
* new datepicker and autocomplete modules
* added limitation to only future events in event calendar
* added search views, switch between maps, lists and calendars from the search
* mucn much more new UI related upgrades
* fixed bug in single ticket mode not updating "available until" time upon saving
* fixed bookings="user" not working in 5.12 update
* fixed issue where site language is respected over profile language in admin area for EM content
* tweaked EM_Calendar::get() sorting to order by start datetime,
* tweaked EM_Calendar::get() to consider multi-day events as all-day events for purposes of $event_ts_marker indexes,
* fixed calendar day links containing the limit query param,
* added EM_Event::get_event_colors() for category color meta
* added action em_booking_status_changed, em_bookings_table_output_table_filters
* changed booking_status DB field to allow double-digit status numbers
* fixed display issues on EM-generated pages wtih block themes

= 5.12.1 =
* fixed issue with scope sanitization preventing search form searching by date range
* added limitation to prevent JS loading on non-EM post type editors to potentially help prevent 3rd party conflicts
* added support for event location displaying url or other custom info for the ical LOCATION property

= 5.12 =
* changed EM_Booking::$disable_restricions so that all ticket restrictions can ignored for admin manual bookings (pro feature) including spaces, roles and dates
* added taxonomy filters for Meta Tag Manager compatibility on overriden taxonomy pages
* added filters to Event_Locations\Event_Location and Event_Locations\URL
* added filters to EM_Events::output_grouped()
* fixed #_EVENTDATES_LOCAL and #_24HHTIMES_LOCAL showing time/date range even if time/dates are the same
* fixed XSS security vulnerability reported by/via WP Plugins team

= 5.11 =
* fixed booking-related php errors deriving from accessing $EM_Booking->people directly since 5.10
* added 'week', 'this-week' and 'this-month' scopes

= 5.10 =
* fixed a minor PHPMailer PHP warning tiggered when mailing errors occur
* fixed date validation errors restting available from/until times to 12AM for tickets
* fixed WP Fullcalendar style tweak for tip image
* removed loading of EM_Person upon construct of EM_Booking to prevent unecessary pre-loading
* fixed bookings closed text not being translatable by ML plugins
* fixed multilingual translation failures when triggering actions like ML booking emails as an admin
* added em_ml_swiitch_locale and em_ml_restore_locale actions for ML plugins that don't hook into WP's switch_locale
* fixed potential ML translation issues when switching translated event objects in a booking
* optimized translated event switching in booking objects
* added em_pre_taxonomy_template, em_pre_{taxonomy_name}_taxonomy_template and em_taxonomy_template
* added temporary fix for compatibility issue with/caused by BuddyBoss
* fixed PHP warnings caused by dev version update checks, also removed em_org_dev_version_slugs filter in place of em_org_dev_versions
* added em_bookings_single_details_footer filter
* added extra email trimming to prevent mail errors due to errant whitespaces
* added em_ticket_get_price_without_tax filter
* fixed #@_{...} placeholders still showing if end date is the same
* fixed email fatal errors when password reset is disabled by other plugins or custom code
* fixed caching issues with custom code hooking into em_event by firing the action before caching occurs
* fixed reported issues with Elementor by writing $content into event and location object post_content properties (kudos to @martinneumannat)
* fixed CZY currency symbols not being included
* fixed dbem_maps_text_format and potentially other formats retrieved via AJAX not being overridable using our em_formats_filter filter
* fixed bookings="0" not returning non-bookable events in shortcodes
* fixed google ical link using http://
* fixed EM_Person::get_summary() returning mixed up array key/values (props @duisterdenhaag)
* removed outline style rules from CSS to help with WCAG compliance

= 5.9.11.3 =
* fixed issues with WooCommerce add-on circumventing currency formatting
* changed various AJAX/Admin actions to fire on wp_loaded rather than init action to allow better plugin compatibility
* added $where param to EM_Admin_Notice constructor
* added __sleep and __wakeup functions to EM_Booking and EM_Ticket_Booking for more optimal serialization
* fixed timepicker conflict issues with other plugins due to shared common function names

= 5.9.11  =
* updated JS to remove any deprecated functionality in jQuery v3.5,
* changed timepicker library to (timepicker)[https://github.com/jonthornton/jquery-timepicker/]
* added $target to Event_Location->output() function parameters
* fixed EM_Event->output() errors causing booking emails to fail if containing #_EVENTLOCATION placeholders for non-virtual events
* added $send_email and $ignore_spaces params to EM_Bookings::set_status()
* added em_booking_admin action to top of single booking admin area
* added update_meta() function to EM_Booking,
* fixed some minor PHP warnings,
* added ticket availability re-check during EM_Booking::validate() for bookings stored in session before saving

= 5.9.10 =
* fixed event locations not getting synced with translations (WPML) and requires re-saving events to take effect
* fixed incorrect dates in calendars for some specific server setups - props to @davidkryzaniak
* fixed multilingual event duplication issues
* tweaked dev updates checker to allow other .org plugins to be checked if added to array passed by em_org_dev_version_slugs filter
* fixed a PHP 8.0 deprecated line of code

= 5.9.9 =
* updated EM_DateTime and EM_DateTimeZone to remove PHP <5.3 backports and also make use of UTC offset support since PHP 5.5.10 (since WP now is requiring 5.6 as a minimum)
* fixed location dropdown not showing option for no location or a placeholder if physical locations are the only location types enabled
* fixed front-end event editor showing events list rather than submission form when there's a validation error
* fixed bug where re-saving multilingual recurring events sets parent ids out of sync and causing booking linking issues with translated events
* fixed potential minor CSRF vulnerability in oAuth base class where state could be ommitted when verifying/authorizing account, props to [@Lenon](https://lenonleite.com.br/en/2020/09/19/explorando-vulnerabilidade-em-operadores-logicos-isset-algumacoisa/)
* fixed event times output on bookings table not showing time according to formatting settings
* added 5th argument with array of segmented placeholder meta to _output_placeholder filters of events, locations, bookings and taxonomies
* tweaked placeholder regex to allow underscores in placeholder names e.g. #_PREVIOUSLY_INVALID_PLACEHOLDER
* added _SITE time-related placeholder variations which display event time relative to website timezone,
* added _LOCAL time-related placeholder variations which display event time relative to user's browser timezone (based on JS),
* fixed EM_Event::output_times() not using custom $all_day_message
* fixed booking table custom fields with accented names not being displayed or exported properly
* updated jQuery UI CSS theme
* updated PHPMailer loading location due to breaks in 5.5
* added #_EVENTDATES_LOCAL
* updated placeholder docs
* fixed save and status change filters firing before emails are sent which can cause irregular behaviour in add-ons (e.g. Zoom join links)
* optimized EM_Bookings::has_booking() function to prevent unecessary loading of bookings to event
* fixed fatal errors on ical generation if mb_string extension disabled in PHP
* added em_bp_menu_sub_nav and em_bp_menu_main_nav filters to allow easy BuddyPress/BuddyBoss menu overriding
* added data deletion and warning of deletion when switching location types,
* moved JS out of location selection template into main JS file
* changed Event_Location data handling so that event location data is kept within the object EM_Event->event_location rather than in EM_Event->event_location_data
* fixed Event_Locations::is_enabled() creating fatal errors if add-on event location plugins get deactivated whilst event location is still 'enabled' in settings
* fixed deletion of previous event location data occurring on get_post() rather than save() and added a temporary event_location_deleted property to EM_Event to contain deletable event location
* changed recurring table field to require a non-null value and default to 0 to prevent erratic behaviour if recurrences get saved with NULL in random edge cases
* removed some old migration update code for v4 > v5 update (more to be removed at a later date)
* added em_event_save_events_exclude_update_meta_keys allowing add-ons to prevent overwriting meta keys when updating recurrences
* added docs for event location types and a filter for adding custom placeholder docs to the help page
* fixed dates and possibly other placeholders not showing in site locale if admin triggers emails with a different user locale
* fixed scope search issues on search form when default scope is not 'future' and search dates left blank
* fixed template location typo preventing em_get_events_list_grouped() from working
* fixed admin columns not showing as per screen reader preferences when all cols are chosen to be displayed
* fixed bp showing booking tab/info when bookings are disabled (props @raruto)
* fixed display issues on static home page (other than EM events page) containing shortcode showing paginated events/locations/taxonomies
* fixed broken link for datepicker formatting docs on settings page
* fixed PHP errors on BuddyPress "my group events" template
* added #_BOOKINGADMINURL and #_BOOKINGADMINLINK placeholders to booking email templates
* added alt attribute to event thumbnails in MS Globals mode
* fixed Polish zloty missing symbols
* fixed 'no location' option not showing if dropdown option for physical locations enabled
* fixed booking cutoff times defaulting to 12am rather than event start time
* fixed booking cutoff times not getting set upon initial recurring event creation
* fixed trashed events showing up on front-end event editor as pending
* fixed JS issues causing ticket editor not reflecting changes during edit process before saving
* fixed Yoast SEO deprecated log warning for usage of WPSEO_Utils::get_title_separator() in > v15.2
* changed default time to WP format upon installation
* added my/all events links to front-end events admin for those with admin capabilities
* updated dev update checker to allow EM add-ons
* fixed duplication issues for custom event locations due to lack of cloning the event location within the cloned event
* added organizer property to ical feed and em_locate_template_args_... filter to enable it with (see https://pastebin.com/d2y5qXPy)
* added better booking button error feedback and a em_booking_button_response JS event
* changed EM Pro update warning to be dismissible site-wide and persist only on admin/network settings page
* removed use of date_i18n() in place of EM_DateTime->format() i.e. wp_date()

= 5.9.8.1 =
* fixed html structure error in location template breaking editor if location dropdowns are enabled

= 5.9.8 =
* added Location Types including URL and (via external free add-on) Zoom support!
* added native OAuth support for third party integrations (e.g. Zoom)
* added $EM_Event object to booking form template actions
* changed $EM_Booking->booking_status to protected so that status returns 1 even if approvals are disabled
* fixed XSS vulnerability (kudos to Jakob Wierzba)
* fixed potential SQL injection vulnerability (kudos to Antony Garand from Godaddy)
* fixed fatal errors in BuddyPress if notifications are disabled
* fixed minor PHP warning
* fixed Yoast SEO 14.0 conflict

= 5.9.7.3 =
* minor JS conflict fix for those overriding the tickets admin template (or those incorrectly overriding ALL EM templates) in their theme with older HTML structure

= 5.9.7.2 =
* fixed CSV injection vulnerability which can allow malicious text to be exported to CSV files and parsed by Spreadsheet
* fixed #_BOOKINGCUTOFF text date formats not getting translated correctly
* added ability to programatically add attachments to booking emails for future features
* fixed/updated casing of functions in phpMailer function calls (prefiously backward compatible),
* added reply-to headers for wp_mail emails circumventing some plugins forcing from email address fields,
* fixed email testing function ignoring sender name, encryption and autotls options
* fixed ical apple structure breaking parsing in google (and possible others)
* updated events-manager.js to replace deprecated use of delegate/bind with on/off equivalents
* added ticket ordering
* fixed editing booking tickets in admin causing validation errors on 0 values
* fixed PHP Warning generated when adding Booking Notes which prevent a redirect with WP_DEBUG enabled
* fixed Events tab on profile pages stripping last character with the BuddyPress Nouveau theme
* changed blank value to 'no location' when viewing the event bookings admin page for locationless events
* changed EM_Notices by making them JsonSerializable
* fixed (hopefully!) the elusive and hard to reproduce "variable mismatch" error when submitting new form in some rare circumstances

= 5.9.7.1 =
* fixed minor typo in new email setting description
* fixed CSV booking export files turning out blank due to change in EM_Bookings::__isset() in 5.9.7

= 5.9.7 =
* fixed google calendar add-to link not including location town/zip/state
* fixed minor PHP warnings
* added timestamps for keys to $post_ids array in em_event_save_events filter
* tweaked deleting of recurrences to reduce possibility of mistken circumvention by other filters
* added parameter option to include country to EM_Location->get_full_address()
* added em_ticket_delete and em_tickets_delete filters
* added language definition for events and locations allowing for faster multilingual searches where supported
* added parent definition for events, tickets and locations paving the way for various hierarchical applications
* added language search attribute for events and locations
* fixed potential search inconsistencies when using 'recurrence' search attribute for finding recurrences by recurring event ID
* added multilingual support for recurring events (requires update to any compatibility plugins)
* added ability for event recurrences to assign corresponding parent ticket ids to recurrence tickets via ticket_parent db field
* fixed potentially conflict-causing permalink rules for the calendar page (fixes WPML-EM conflict on these pages)
* added support for location address translation in multilingual environments
* removed filter pointer (previously used to fix ACF conflicts) in EM_ML_IO::event_save_meta() since WP now has fixed pointer issues for nested filter triggers
* fixed EM_Event::is_recurrence() returning false when event not saved for first time
* fixed delete_events not returning true to filter when events are actually deleted
* fixed JS warning preventing address update on map when updating locations
* added various precautionary data sanitization for security enhancement
* replaced all uses of wp_redirect() with wp_safe_redirect() as a security precaution
* added encryption and autotls options to email settings
* added em_is_event() and em_is_location() object checking functions
* added native primary handling of finding translations of events and locations before passing it onto filters where translation plugins can intervene
* added native searching of events and locations based on new language/parent fields
* added native support for searching eventful and eventless arguments in multilingual context
* added native support for listing untranslated events and locations in original language when listing a second language in multilingual context
* changed EM_Event and EM_Location blog_id to default to 0 rather than null
* added shortname property names for common EM_Event and EM_Location fields language, parent, translation, id (event/location id), slug, status, name and owner
* added get_parent() function for EM_Event and EM_Location classes
* added $context peroperty to EM_Events and EM_Locations classes which leverage late static binding in PHP 5.3
* added 'language' search argument for events and locations which can be used in multilingual contexts
* moved table joining decision logic in EM_Locations into its own function, enabling overriding via filters
* added em_booking_email_before_send, em_booking_email_after_send and em_booking_output_pre actions in EM_Booking
* added em_booking_email filter in EM_Booking
* added name/description shortname properties and modified ticket_name/description access in EM_Ticket to enable dynamic translation of ticket names
* added price,id,spaces shortnames for EM_Ticket_Booking
* added em_ticket_booking_get_ticket filter in EM_Ticket_Booking
* added locale switching/restoring functions in EM_ML as well as syncing with the WP locale switcher
* fixed booking emails not getting correctly translated in ML modes
* fixed data privacy consent validation errors when booking in custom modes under certain setting configurations
* removed load_plugin_textdomain call in favour of letting WordPress.org automatically handle language translation files
* changed boolean database fields into tinyint(1) unsigned types

= 5.9.6 =
* fixed monthly recurrence pattern issues with PHP <5.6
* fixed multiple minor PHP Warnings including Countable errors when saving a booking in PHP 7.2+
* added em_bookings_table_get_bookings_args filter, allowing ordering and more for bookings admin tables
* added Faroe Islands to countries list
* added custom dataset variable for EM_Options, added ability to save dbem_data array options in settings page
* fixed publishing issues with long multibyte character post titles in recurring events
* added em_mailer_before_send action to EM_Mailer->send() just before a mail is sent
* added em_javascript_loaded jQuery trigger
* added ability to send attachments via WP_Mail in EM_Mailer::send(), provided the transport used by it allows
* added recurring event placeholders #_RECURRINGDATERANGE, #_RECURRINGPATTERN, #_RECURRINGID
* fixed incorrectly named #_EDITLOCATIONURL and #_EDITLOCATIONLINK which were previouly (and still are) working under #_LOCATIONEDITLINK and #_LOCATIONEDITURL
* fixed pagination for calendar day archives when formatting is disabled
* added custom tabs hooks to settings page (including multisite which now has a default single tab)
* added id property to each EM_Admin_Notice that is output to screen
* optimized EM_Admin_Notices::add() so that strings are assumed to be hooks and no extra logic necessary to register the notice initially
* added em_booking_pre_calculate_price hook
* fixed potential PHP warning when editing other CPTs with galleries
* fixed some incorrect ticket end times related to saving rsvp end dates for recurring events in single ticket mode with blank end dates/times
* fixed inconsistent rsvp_end __set() compared to end and start properties,
* fixed event rsvp ending at 12:00 instead of event time when no date is supplied in single ticket mode,
* unified PHP get_post logic of start/end date/time recurrence info for tickets to reduce redundant code
* fixed invalid rsvp end date persisting unless changed when first saving an event with blank start date
* **potentially-breaking for custom code email filters** changed $output_type/$target method param in EM_Booking::email() calls to EM_Booking::output() function calls to 'email', use get_option('dbem_smtp_html') to determine whether to use HTML or not if using that format
* changed event category/tag and location lists to not include pagination in emails due to lack of predictability for pagination links
* added css tweaks to to admin single booking view and modify booking form in dashboard area
* fixed lack or confirm-redirect for no-user booking personal details editor when viewing a single booking
* fixed edit/delete links showing in post admin lists to users without the right caps (clicking these links had no result)
* added some strict_type future-proofing to EM_DateTime class
* added em_event_detach and em_event_attach filters
* fixed location coordinates not being supplied for examct Gmap pin placement when auto-complete attaching a location to an event,
* fixed double google API call when choosing an existing location via auto-complete search
* changed #_LOCATIONMAP so no 'map unavailable' messgage is shown if google maps are disabled
* fixed google maps location bubble CSS formatting issues in admin area
* changed datepicker to show relative year range dropdown selector according to currently selected date, allowing for flexible year selection
* fixed potential stored XSS vulnerability, many thanks to Tobias Fink of SBA-Research for a good eye and responsible (pending) disclosure
* fixed lack of validation when submitting tickets with higher min spaces than max spaces allowed in the ticket
* added username to exportable/viewable field columns in booking admin
* fixed embedded maps display errors when there is an & character in name or address
* fixed certain recurrence patterns with "last x of the month" skipping months
* fixed recurring events showing location meta boxes and columns when locations are disabled
* fixed event count not showing up in event profile tab
* fixed events admin table front-end showing location column when locations disabled,
* fixed location placeholders getting processed if locations are disabled
* fixed yearly recurring events not working for any value other than 1 yearly interval
* fixed precision inconsistencies between location coordinates in wp_postmeta and wp_em_locations by changing mysql FLOAT to DECIMAL and rounding numbers on $_POST retrieval
* fixed ticket prices input/display in event admin not accepting localized decimal separators
* fixed minor CSS validation errors in events_manager.css
* fixed #_BOOKINGDATE not working on first automated sent email
* fixed port assignation errors in SMTP mail settings
* pre-emptively hardened some lines of code as recommended by the wordpress security team

= 5.9.5 =
* added new Google Maps display options to help prevent cost increases
* fixed booking status emails getting resent when attempting to change status to same status
* fixed potential consent issues with editing/validating bookings made by other registered users
* fixed broken #_LATT custom field attributes for locations
* fixed #_ATT placeholders with dropdown options not selecting default option if not defined
* added em_locate_template_default filter to allow for further template overriding
* fixed certain unsanitized permalink output on admin settings page
* fixed weekly and daily recurrence creation inconsistencies when traversing DST change dates

= 5.9.4 =
* added em_rewrite_rules_array filter for final permalink rule manipulation
* fixed privacy consent blocking certain actions such as single booking button and admin-side submissions
* fixed fatal errors when showing the consent checkbox in WordPress 5.9.5 and earlier
* fixed the quick booking button not working in ajax search results
* fixed privacy policy consent form label not being translatable for multilingual sites
* fixed inconsistent date headers in certain situations with UTC manual offset dates
* fixed incorrect link to .eot dashicon file for IE11
* added anonymous submitter data to locations for new event submissions and integrated this with GDPR export/erase tools
* fixed location slug blanks when directly published from front-end via submitting an event
* added default ical and rss feed limits to avoid overloading as number of events grow
* corrected docs to include 'recurrences' search attribute
* added timezone pre-formatting to functions that produced incorrect output for timezone date placeholders
* fixed default categories not being applied for events with no categories
* fixed locations being selectable for events in other blogs within a multisite global mode when locations don't all reside on main blog

= 5.9.3 =
* added Data Privacy and GDPR features
* fixed user deletion not properly deleting events and not deleting locations if content is set to be deleted not reassigned
* added location attributes array to em_get_attributes filter
* fixed EM_MB_ICAL_WORDWRAP incorrectly not applying multibyte wordwraps if set to true
* added 'not_all_day' conditional placeholder
* made EM_Taxonomy_Terms objects countable
* fixed tag placeholders not getting parsed in event format such as #_TAGIMAGE

= 5.9.2 =
* fixed some instances where PHP 5.2 outputs incorrect times due to other plugins changing server timezones
* fixed scope issues with PHP 5.2 when calculating start/end of month dates
* fixed potential issues with manual offsets when other plugins change server timezones whilst saving events, particularly in PHP 5.2
* added EM_CACHE constant which if defined as false will disable caching
* fixed issues when changing times of an EM_DateTime object with large manual offset timezones may cause incorrect dates (fixes some weekly recurrence pattern issues)
* added notice when viewing bookings made in another language
* added booking admin table column for language used in booking
* fixed some minor PHP notices preventing event submissions/edits with a new location if display_errors are enabled
* updated EM_Notices to use new class names for notices output in WP Dashboard
* added filters for all post type and custom taxonomy arrays used in initial post type and custom taxonomy registration functions (see em-posts.php)

= 5.9.1 =
* fixed the & operator in category search attribute not working correctly in MultiSite Global Tables mode
* added fix/workaround for any code that changes the timezone from WP's UTC timezone during runtime
* fixed multisite upgrade issues with timezones
* added option to reset timezones for all blogs on multisite and also reset each blog to its individual WP timezone
* changed default subscriber capabilities so they can't submit events by default
* added option to resume failed timezone resets on multisite blogs
* added upgrade warning for multisite users who upgraded to 5.9 and had timezone update errors
* fixed (deprecated with backward compatibility) shorthand EM_Booking object properties such as 'status' not having values after 5.9 update
* fixed recurrence pattern date miscalculations on sites where a plugin/theme changes the default server timezone after WP sets it to UTC
* added gutenberg support with define('EM_GUTENBERG', true); defined in wp-config.php
* fixed new tickets not being added to previously created recurring event where bookings were previously disabled
* added tweaked EM_DateTime functionality so functions that might return false in DateTime set valid property to false before returning object for chaining

= 5.9 =
* fixes applied to 5.8.1.2 merged into dev version
* added timezone support
* added new admin notice interface objects
* added new EM_Options option storage class
* fixed some PHP strict warnings regarding parent/child class method signatures
* fixed taxonomy pagination issues in AJAX (affecting tags specifically)
* fixed shortcode not getting parsed properly due to fix in 5.8.1.1 attempting to parse shortcode with supplied formats
* fixed non-standard YYYY-M-D date formats not being accepted in scope arguments
* improved multisite blog update interface
* added modular update logic to admin pages for future features
* further hardening against XSS strings
* fixed non-standard YYYY-M-D date formats not being accepted in scope arguments
* fixed duplicate slug issues when duplicating events
* removed all references of create_function for php 7.2 compatibility
* fixed documented 'postcode' search attribute not filtering results by that value
* fixed event duplication not copying other plugin meta data, such as ACF
* fixed EM_Bookings::get() producing SQL error if passing an array of booking IDs
* added em_event_post_scope_meta_query filter for admin-side custom scopes
* fixed bug where blank start/end times of tickets and booking cut-offs default to 12AM instead of event start time
* fixed preview mode not reflecting new event description
* fixed broken ical permalinks for individual subcategories
* changed recurrence warning strips from green to orange/blue at top of admin pages
* added 'ticket total' column to bookings export and split 'ticket price' from 'total' booking price when exporting split by ticket type
* fixed issues with caching not getting updated other plugins data such as ACF and possibly others
* fixed issue where logged out user didn't see login feedback message on booking form under certain setting combinations
* added EM_ML::restore_current_lang and EM_ML::switch_to_lang functions
* fixed language detection of bookings not being done early enough

= 5.8.1.3 =
* fixed escaping error in bookings admin table introduced in 5.8.1.3

= 5.8.1.2 =
* security release fixing a potential stored XSS vulnerability (disclosure pending, props to Luigi Gubello for responsible disclosure)
* fixed scrolling issues on Google Maps
* fixed duplicate value attribute in custom fields/attributes front-end editor
* fixed shortcodes getting parsed after formats resulting in preparsed formats within shortcode
* fixed lack of booking validation filter on single booking button
* fixed minor PHP warning
* fixed duplication errors for events without bookings enabled

= 5.8.1.1 =
* fixed a bug that arose in 5.8.1 where tag/category pages using formatting will still show the tags/categories list page instead

= 5.8.1 =
* fixed recurring events booking cut-off dates not being taken into consideration when creating/editing recurrences
* fixes various issues that arose in 5.8 update:
 * removed use of is_main_query() and in_the_loop() within the_content filter of em_the_content due to conflicts with certain theme builders
 * fixed locations without attached events not showing up in autocomplete and dropdowns for creating/editing events
 * fixed multisite issues loading single event pages in MS Global mode
 * fixed dropdown custom #_ATT fields not saving properly

= 5.8.0.1 =
* fixed bug introduced in 5.8 which did not correctly return content in get_the_excerpt filters

= 5.8 =
* fixed category color picker and image uploader problems
* created base classes for EM taxonomies to make adding custom EM taxonomies even easier in the future,
* added tag image and color settings/data
* added sortable option for date columns of events and recurring events in the admin area
* fixed saving an event recreating ticket_meta and wiping out ML settings
* fixed multilingual translations of event in WPML not hiding/showing right meta boxes
* fixed saving multilingual translation of event overwriting original language ticket names
* changed admin headers to use new and accessible WP inline HTML structure
* fixed serialization problems for deprecated event/location attributes and attributes with multiple post meta entries for one post
* removed deprecated attributes editor from admin area as this is confusing and error prone when combined with other plugins manipulating custom fields
* fixed #_EVENTEXCERPT without arguments stripping HTML since 5.7
* added ability to list multiple categories/tags in conditional placeholders e.g. {has_tag_123,1234,tagname}...
* added #_EVENTPRICEMINALL and #_EVENTPRICEMAXALL to show prices of unavailable tickets as well
* fixed JS issues with MS Global mode and JS file limiting when displaying subsite single event pages on main blog
* fixed single initial abbreviation issues in Chinese calendars
* fixed duplicate events not being published to social networks via jetpack publicize (kudos @gnaag)
* fixed potential incompatibilities with other plugins using wp_query_reset() on category pages, preventing our page formats from showing
* fixed taxonomy archive pages returning zero results if taxonomy formatting is disabled and events are excluded from searches (WP Bug workaround)
* fixed inconsistent line ending causing warnings with PHP compatibility checker
* unified Tag and Category class functions into sets of parent class functions
* fixed PHP fatal error with BP when disabling notifications
* fixed calendar day links being incorrect if another plugin adds querystring params to permalinks
* added groupby, groupby_orderby and groupby_order arguments allowing grouping in search results for events and locations
* improved validation and sanitization of orderby arguments to avoid ambiguous field SQL errors
* added optimization to optionally join event/location tables when needed for grouped searches or if EM_DISABLE_OPTIONAL_JOINS is defined and set to true
* moved condition of when argument 'bookings' = 'user' to EM_Object so it's accessible by EM_Location too
* changed default 'recurring' argument to null so that non EM_Events searches can by default avoid the recurring conditional being generated
* added grouping to AJAX response for event_map shortcode and function resulting in performance improvements
* fixed use of get_terms via deprecated two argument method
* fixed recreation of tickets for a recurring event having wrong start/end ticket dates
* fixed custom fields with a 0 value getting deleted instead of saved
* fixed/changed - shortened SQL search conditions when filtering by array or comma list of event/location ids
* fixed escaping of apostrophes on search term placeholder
* added optimized result counts (EM_Events::$num_rows and EM_Events::$num_rows_total) in EM_Events::get() to reduce number of SQL calls
* added optimized result counts (EM_Locations::$num_rows and EM_Locations::$num_rows_total) in EM_Locations::get() to reduce number of SQL calls
* changed as a result of the above two additions above we tweaked various event list areas to half number of SQL queries run per list
* added no_results_msg argument to EM_Events::output() functions for custom 'no events found'
* added location_status and event_status search arguments to event and location searches respectively
* fixed column alignment issues when using quick edit for events in wp dashboard
* tweaked excerpt filters and reduced redundant calls to our the_content filters (reducing overhead)
* changed event page the_content loading so it only shows when in_the_loop() and is_main_query() is true
* fixed issues with WordPress SEO plugin breaking the wp editor front-end
* added event list default sorting options to locations, tags and categories
* added em_wp_is_super_admin() replacing is_super_admin() in anticipation of its deprecation 
* changed and optimized postmeta saving process to reduce number of rows by up to 80% per event,
* optimized events table to save NULL values when possible
* changed/removed unused event_category_id field from events table
* added em_object_can_manage filter
* fixed nested attributes such as {cond}#_ATT{name}{/cond} not being parsed properly
* fixed some installation SQL PHP errors/warnings
* fixed capability mapping problems if revisions enabled for events or locations
* added WEBCAL placeholders to events, locations, categories and tags (e.g. #_EVENTICALLINK > #_EVENTWEBCALLINK)
* removed unnecessary SQL queries for undeclared wp_options reducing number of queries made per page load
* tweaked settings page textarea elements to be wider
* added (possibly temporary) EM_FORCE_RECURRENCES_SAVE constant forcing status save for when recurrences change status,
* fixed EM_Event::set_status_events() which was not working correctly and now also runs a em_event_set_status_events filter
* fixed recurring events warning text not appearing in front-end editor if first submission produced validation errors
* performance improvements to EM_Bookings and EM_Ticket objects by preventing pre-loading of all bookings on instantiation and when querying availability
* added cache optimization for event and location loading,
* improved optimized loading of EM_Location via em_get_location() by checking globals first,
* changed bookings/tickets template for event editor to avoid loading all bookings for counting purposes
* added $include_adjustments argument to get_price_pre_taxes and get_price_post_taxes for calculation of prices without discounts/surcharges (those making use of the em_booking_get_price_post_taxes and em_booking_get_price_pre_taxes filters should check for this passed argument in their custom code)
* fixed bug where event categories saved via quick or bulk edit in multisite global mode aren't reflected in subsite event lists filtering by that category
* fixed events created before changing to multisite and enabling global mode not displaying images via placeholders
* fixed bug in filter em_ticket_get_post where function argument $post was not passed on
* improved taxonomy color retrieval so it makes use of caching
* improved/optimized deletion of multiple bookings and tickets at once if belonging to an event
* fixed my_bookings shortcode outputting content at top of content irrespective of where it is located within content
* fixed invalid recurrence ticket start/end cut-off date/times if both are not set for the recurring event
* fixed bug where ticket start/end dates cannot be unset in recurring events

= 5.7.3 =
* fixed previous meta not getting deleted from recurrences resulting in non-saved information such as a changed featured image or location
* fixed newly submitted recurrences first saved in draft or pending mode not creating new events without a reschedule
* fixed export settings button not working in MultiSite
* fixed CSS issue in search form advanced field dropdowns on firefox
* fixed PHP warning when displaying locations and events map via shortcode (kudos @Beee) 
* fixed minor PHP warning on category admin pages where image isn't defined (kudos @Beee)
* fixed booking admin 'Add Note' button not being translatable
* added fifth day of month in recurrences pattern
* added ability to change event times on recurring events without recreating recurrences

= 5.7.2 =
* fixed subsite events in MS Global mode not being able to save new events and resulting in orphaned entries in wp_em_events table
* added admin tool to allow removal of orphaned events in wp_em_events database table
* improved detection and handling of orphaned events when being displayed

= 5.7.1 =
* fix for wierd headers PHP error due to corrupted <?php on line 1 of admin/em-ms-options.php
* fixed minor PHP warning on settings page

= 5.7 =
* added em_events_admin_args filter allowing modification of event search arguments
* removed jQuery UI localize files for the datepicker in favor of WP 4.6 native localization
* fixed potential bug where modified ticket spaces aren't saving correctly when editing a booking
* added is_user_attendee, not_user_attendee and variations of both proceeding with _0 or another number representing booking status
* added edit/view shortcut links for each event location in events list admin area
* fixed conflict with BuddyPress Group Extras and potentially other plugins when one or more of our list pages aren't assigned a page (e.g. locations page) resulting in a blank page
* fixed ical feed not serving wrapped content lines,
* added EM_ICAL_APPLE_STRUCT constant to allow ommitting of X-APPLE-STRUCTURED-LOCATION lines in the ical file
* fixed formatting issues with ical X-APPLE-STRUCTURED-LOCATION line
* fixed instances where locations or events automatically creating a post slug if needed when published
* fixed location image display issues on other blogs when in MultiSite Global mode
* corrected and updated docs
* fixed switch_to_blog() being used instead of restore_current_blog() in buddypress/bp-em-core.php (props @mikegillihan)
* fixed search form CSS inconsistencies for Twenty Seventeen theme
* fixed locations map breaking when provided with object versions of JSON response (such as with WPML compat add-on)
* updated maps google library to always use most stable version
* fixed issues when creating MS Global Events programmatically
* moved some logic out of the EM_Event->output() function into their own function for easier future use in templates
* fixed google calendar links not including full location address
* added an import/export settings option in settings page
* added ability to use post_id in calendar shortcode and PHP functions
* added has_location and no_location to event search attributes
* added [events_map] shortcode which searches by event rather than location
* changed behaviour of zooming when mouse is over a map so a click is required first to avoid catching whilst scrolling the page
* fixed all errors and warnings for PHP 7 and others highlighted by PHP Compatibility Checker plugin
* added decimal parameter to get_tax_rate functions which allows fixing of minor PHP warning when tax is set to 0
* fixed filter typos and _get_sql positions in EM_Bookings::get() and EM_Locations::get()
* removed unnecessary storage of _post_id in postmeta for recurring events
* fixed bug where tickets aren't deleted with event
* fixed incompatibility with WP Fastest Cache for bookings on cached pages
* fixed EM_Categories not removing categories if none are selected when resaving an event
* added em_get_events_admin_url() function
* added major improvements to recurring events including ability to duplicate templates and also make changes without deleting previous recurrences,
* changed various event editor templates to accommodate new recurring event changes
* changed no-user bookings feature to not require a dummy user
* changed no-user bookings so previous bookings appear correctly regardless of whether NU bookings is enabled or disabled
* tweaked PHP Mail sending method to go through WP PHPMailer class for better encoding
* fixed overly aggressive filtering of get_the_date and get_the_time filters (kudos @boonebgorges)
* fixed missing map markers array variable to be supplied in JS hook em_maps_locations_hook (kudos @raruto)
* changed booking discount php functions into price adjustment functions which will now allow for surcharges to be implemented
* fixed guest submissions with bookings enabled producing false permission errors instead of a confirmation
* fixed/updated media uploader JS interface for category image
* added more booking price filters to individual functions
* fixed minor nuance where upcoming events scope filter not selectable on mobile view
* fixed JS not being loaded automatically on events list when JS performance loading enabled
* fixed regex matching preventing use of a #_LATT attribute within an #_ATT default value bracket
* fixed confirmation message not showing for event duplication
* added duplicate shortcut link on dashboard edit event page
* fixed issues with selection of Sunday on monthly recurrences and the generated pattern summary
* changed/fixed events_list_grouped using date format for sub-section titles from settings page if no date_format supplied regardless of grouping mode
* added em_scripts_and_styles_public_enqueue_pages filter for custom pages to automatically include EM script/styles when limiting for performance is enabled
* fixed PHP non-object notices on certain non-EM dashboard pages
* changed booking form scrolling JS to scroll to booking message feedback element and added EM_BOOKING_MSG_JS_OFFSET constant to allow adjusting the offset in wp-config.php
* fixed redirect link on booking form login not taking into account SSL pages
* added #_EVENTEXCERPTCUT to allow cutting of both excerpts and post content
* changed event all day times to start at 00:00:00 and end at 23:59:59 when saved (kudos @cliffpaulick)
* changed default search form settings behaviour to ignore country filtering if search option is not enabled
* fixed scheduled recurring events not publishing recurrences
* improved google maps JS for multiple events/locations maps and auto-zooming of included locations (kudos @benklocek)
* fixed minor php warning on user profile page
* tweaked booking JS to allow a redirect variable passed on with a successful response
* fixed 'Bookings' header not being translated when formats are disabled and booking form is added just after content
* fixed ticket availability status indicator showing false to admins when ticket is restricted to guests
* added get_total_paid function (mainly for hooking purposes in Pro add-ons)
* added support for Google Maps styles via services like the Google Maps Style Wizard or Snazzy Maps

= 5.6.6.1 =
* fixed search forms disappearing in latest update when Styling Options for search forms are turned off (props @factchecker)

= 5.6.6 =
* improvements to ical formatting, including static/unique UIDs, more accurate locations with geo coordinates, categories and featured image
* replaced code using stripslashes() with wp_unslash() (kudos @webaware)
* removed use of jQuery.live() on settings page
* tweaked event search form elements and events list table to be ADA compliant
* added $EM_Ticket_Booking to em_tickets_bookings_add filter arguments
* removed translations of weekdays within EM and using WP translations instead,
* changed calendar templates to stop using ucfirst() to uppercase month first letters since it breaks some languages and the languages that need it don't capitalize their months anyway 
* fixed anonymous event submitter info not showing in recurring event admin area
* fixed wrong nav id in BuddyPress (kudos @lyevalley)
* fixed 'no location' checkbox not remaining checked if event submission returns a validation error
* tweaked templates/forms/event/location.php and added some extra output sanitization
* fixed 404 errors in calendar links to eventful day list for a specific location
* fixed syncing of tables when WP uses utf8mb4 collation which causes errors when saving emojis in post content
* fixed WP 4.6 PHP warnings and featured image problems when using a theme that limits specific CPTs to use thumbnails
* fixed action typo in EM_Ticket::get_post() from em_location_get_post_pre to em_ticket_get_post_pre
* fixed location not showing up in admin area within dropdown if previously assigned to an event but not available to user due to permission changes

= 5.6.5 =
* added option to add Google Maps API key
* fixed category image uploader not working properly on some specific setups
* fixed display issues of first/last names mixing middle names in booking admin tables under no-user mode
* fixed #_TAGSLUG not being parsed
* fixed blank space in email triggering validation errors when booking
* fixed 'view bookings' ticket link pointing to back-end when on front-end

= 5.6.4 = 
* fixed WP FullCalendar (versions using FC 2.x library) not showing events outside current month
* fixed long events not showing on last day in WP FullCalendar
* fixed event category and tag pages 404ing when slugs match taxonomy slugs and these pages aren't parents of events page
* fixed image upload buttons not working properly on category add/edit pages

= 5.6.3 = 
* fixed events disappearing from calendar with WP FullCalendar plugin
* fixed PHP warning for delete booking when a user can't manage booking
* removed our EM_PHPMailer class and started using the one shipped with WordPress
* added %passwordurl% placeholder for new user registration email template
* added check for whether categories are enabled in many areas of code potentially avoiding a array_map PHP notice
* fixed preview mode duplicating tickets
* fixed widget formats stripping certain HTML elements
* fixed erratic date picker range behaviour when adjusting a start date later than the end date 
* fixed original image getting deleted when modifying duplicated event image
* fixed orderby not including event_date_created and event_date_modified since 5.6.2
* fixed PHP warning when calling #_ATT to a non-existent attribute
* changed event debug meta box to display when WP_DEBUG_DISPLAY is also true rather than just WP_DEBUG
* fixed front-end submission false validation errors when submitting events with bookings enabled
* changed headers to new h1 standard on WP Dashboard pages
* fixed bookings admin viewer table not showing specific ticket bookings on front-end
* fixed ML hooking into em_event_save_meta and messing up the internal hook pointer by triggering it again
* fixed translated options PHP fatal error in rare occasions/setups
* fixed deprecated get_currentuserinfo notice in WP 4.5
* fixed PHP 7 division by zero warning
* fixed PHP 7 "array to string" notice
* fixed PHP 7 issues with EM_Ticket validation
* fixed grouped events list not showing long events on each group provided limit=0 is also supplied
* fixed apostrophes not passing email validation
* fixed buddypress fatal error when booking with notifications disabled,
* fixed buddypress activity stream items being created twice for new bookings
* fixed booking admin notes not being added in the front-end
* updated google maps api version and removed deprecated sensor parameter
* fixed searches not working for search terms containing apostrophes
* fixed blank settings pages due to 4.5 code changes to wp_get_referer()
* added em_bookings_deleted action which will execute when one or more bookings are deleted
* added em_bookings_delete filter for when a group of bookings are deleted with event(s)
* fixed EM_Bookings->delete() not deleting bookings properly
* deprecated use of EM_Event->delete_bookings() and EM_Event->delete_tickets() in favor of EM_Event->EM_Bookings->delete()

= 5.6.2 =
* changed translation gettext domain from dbem to events-manager inline with new wordpress.org translation features
* fixed EM_CSV_DELIMITER not being included in headers, added filter em_csv_delimiter to override EM_CSV_DELIMITER
* added wpfc-more class to allow hiding of time on 'more' link for WP FullCalendar
* fixed booking cut-off time not working if cut-off days is 0 or empty
* fixed front-end event submission form permission issues for new recurring events when publish cap is enabled but not edit_others and delete_others
* fixed Norwegian incorrectly translated placeholders
* fixed custom decimal separator not used in tax rate display
* minor js fix which fixes a grecaptcha related error notice
* fixed recurrence_byday db value saving as null for weekly Sunday events (kudos @giventofly76)
* removed _event_date_created and _event_date_modified from the postmeta table as these are inaccurate, use the records from the wp_em_meta table or directly via EM_Event->event_date_created or EM_Event->event_date_modified
* changed settings/admin capability from list_users to manage_options
* added EM_DISABLE_AUTO_BOOKINGSFORM which prevents booking form showing below post if format overriding disabled
* fixed WP 4.4 error on front-end event submission form
* fixed PHP notices in WP 4.4 when categories are disabled
* fixed Multiple Booking Mode bug in Pro introduced by WP 4.4 allowing NULL db values
* fixed like_escape debug warning on search form
* fixed twenty fifteen/sixteen CSS conflict hiding confirmation messages
* added bookings closed message
* updated all languages included typo fixes and added Arabic, Australian and Canadian English

= 5.6.1 =
* fixed no arguments being passed onto em_get_post_meta_pre and em_get_post_pre
* fixed minor PHP warning when viewing settings in paged tab mode
* fixed em_event_save_pre and em_location_save_pre firing before the get_post and validate functions/filters via the save_post action in the WP dashboard
* fixed EM loading jQuery UI css when already loaded by another theme (if they enqueue it with id jquery-ui)
* fixed bug introduced in 5.6 where some error/confirmation notices aren't showing up on page loads
* fixed mistaken update of Hungarian language files with German
* fixed EM_Location::has_events() providing false negatives
* added has_events and no_locations conditional location placeholders
* fixed actions column throwing off CSV column spacing since v5.6
* fixed orderby request parameter being ignored in EM_Bookings_Table
* fixed booking_date not being a valid orderby value for EM_Bookings
* fixed recurring events booking cut-off time resetting to 12AM when no days value is given

= 5.6 =
* fixed potential XSS and a potential code injection vulnerabilities (props to the team at bit9.com)
* updated Czech, Danish, Dutch, English (UK), Spanish, Estonian, French, German, Hungarian, Italian, Japanese, Norwegian, Persian, Polish, Portuguese (Brazil), Russian and Slovenian language files
* added Greek and Slovak languages
* added MultiLingual abstract layer rewritten and various modifications throughout EM to enable multilingual interfacing with plugins like WPML
 * created abstract multilingual layer classes, a complete rewrite and overhaul
 * fixed em_events_get_bookings filter not allowing replacement of EM_Booking object
 * changed booking form placeholder template to reference EM_Bookings->event_id rather than EM_Event->event_id
 * fixed wp_insert_post_data not correctly loading/validating the event associated with the actual post id supplied
 * changed ML globe icon to use the globe dashicon
 * fixed attributes not being translatable as well as porting new attribute values added to original being added to other language if no translation exists
 * added single-tab view for options page if number of ML languages add more than 1000 fields to submit
* fixed some minor PHP warnings for edge cases
* fixed bp deprecated warning in debug mode
* added long_events default option to small and full calendar sizes on settings page
* fixed erroneous month returned when using functions and the 12th month (kudos @althie) 
* removed a console.log function from the JS delete ticket function
* removed the forced 50px width/height of the full calendar day cells which breaks the calendar in twenty fifteen
* fixed global $EM_Event not loading before add_admin_boxes in admin area
* improved/simplified code for front-end image uploads handling which now uses media_handle_upload
* fixed #_CONTACTNAME showing anonymous events user rather than the guest submitter name
* fixed MS Global recurrences not saving global category info
* fixed recurring tickets triggering errors if only start date is defined
* fixed private locations searches not including eventless private locations
* changed some deprecated is_main_blog() functions to is_main_site()
* fixed JS DOM conflict by naming submit buttons 'submit'
* fixed 'Email Sent' being shown when no emails sent during booking status change
* fixed em_wp_the_title() unnecessarily filtering all the_title instances on event, location and EM pages rather than just the main title
* fixed long_events=0 not being considered for the WP Fullcalendar plugin
* fixed issue where when saving via WP admin the global $EM_Event object doesn't refresh all data before passing onto save_event filter
* added em_rewrite_rules_array_events filter for permalink rewrites
* fixed long events argument not taking in WP FullCalendar
* fixed filters em_bookings_table_rows_col and em_bookings_table_rows_col_{col_name} not supplying $object which could be an EM_Ticket_Booking object
* modified EM_Bookings_Table::get_row() to apply em_bookings_table_rows_col for every column
* fixed minor admin page bug where JS can clash with uses of pseudo URLs when certain plugins activated
* fixed EM_Calendar links clashing with WPML and potentially other plugins that manipulate urls
* added some esc_ functions to calendar links and templates an extra safety precaution
* added EM_CSV_DELIMITER constant check to allow overriding CSV delimiters
* added em_booking_output_event filter
* improved booking email sending so contact and extra admin emails sent in one go rather than in separate send functions
* added em_booking_js hook with intention of replacing em_gateway_js hook in the distant future
* fixed booking price rounding issues in EM_Booking::get_price()
* fixed event save_post hook issues when a new location CPT is created within it, which fixes ACF plugin saving issues
* fixed WPLANG php notice on installation/update
* fixed 'Manage Persons Bookings' page not filtering properly for non-admins
* fixed some false positive/negative results using EM_Object::can_manage()
* added additional db indexes for performance improvements
* fixed EM_Events::get() returning blank events when supplied an array of event IDs
* fixed advanced search & ticket editor advanced options toggle conflicts with twitter bootstrap-based themes
* updated classes and widgets to use PHP 5 constructor methods in lieu of WP 4.3 update
* fixed BP event duplication errors for non-owner group admins
* changed and cleaned up some table layout issues in templates/buddypress/group-events.php
* fixed current view/scope link not being bold in front-end events editor
* fixed saving recurrences not deleting currently trashed recurrences
* fixed location archives not adhering to sorting defined in settings page
* fixed users without the manage_booking capability being able to create events with bookings in the admin areas
* removed auto-insert of ul tags if no format_header or format_footer supplied in taxonomy list shortcode
* fixed some format, format_header and format_footer attributes not being output for custom value combinations
* fixed misspelt action em_front_event_form_guest (em_font_event_form_guest depricated, will remove in future) 
* fixed custom taxonomies not being considered as default search args to make search form integration easier
* changed templates/templates/search/categories.php so default value is 0 rather than -1
* changed incorrect em_event_get_post filter applied for EM_Event::get_post_meta() which is now em_event_get_post_meta
* added em_event_get_post_meta_pre and em_event_get_post_pre actions
* fixed SQL error when searching non-existent taxonomy values or taxonomy names containing a hyphen
* fixed change of event ownership to anonymously submitted event not showing new owner contact information
* fixed soon-to-be deprecated templates/forms/ticket-form.php not including overridden forms/event/bookings-ticket-form.php file
* added em_timepicker_options JS trigger for altering time picker options
* fixed strict PHP error in templates/forms/bookingform/ticket-single.php
* added em_enqueue_admin_styles action and moved em_enqueue_admin_scripts action after em script is added
* added Russian Ruble to currency list
* fixed errors with some booking edit links on the front end
* changed use of deprecated function bp_core_add_notification to bp_notifications_add_notification
* added EM_Event::duplicate_url and changed templates with duplicate urls to use this so ML events will duplicate original translation
* fixed some potential validation errors when hooking into em_location_get_post_meta
* added admin global $EM_Location so it now becomes global during add_meta_boxes action
* added em_category_taxonomy_template and em_tag_taxonomy_template actions
* updated jQuery UI CSS files

= 5.5.7.1 =
* fixed minor DOM XSS vulnerability

= 5.5.7 =
* fixed some more XSS vulnerabilities

= 5.5.6 =
* fixed a security vulnerability

= 5.5.5 =
* fixed booking cut-off time not being editable after event is saved
* fixed taxonomy search arguments not being applied on calendars and ajax lists,
* changed taxonomy search argument - not cleaned or converted to array during get_default_search() to prevent pagination link errors,
* fixed calendar pagination link argument comparison issues due to loose type comparison,
* changed calendar default arguments so scope=false and limit=settings_value (should have no effect - mainly future feature-proofing)
* fixed issue with weekly sunday only recurrences not validating
* fixed BP unregistered actions in admin area
* added new admin booking status emails to prevent confusion when pending/confirmed statuses are changed 
* updated POT file

= 5.5.4 =
* fixed no events message for #_LOCATIONNEXTEVENTS and similar showing header/footer formats for an events list
* updated all language files including POT file, added Norwegian, Persian and Turkish
* fixed/improved pricing to include 4 decimal precision for better tax rounding with large numbers,
* fixed/changed incorrect filter em_event_validate to em_ticket_booking_validate in classes/em-ticket-booking.php
* added new jQuery hooks for altering map and marker options
* fixed search form using default country even if advanced search settings are disabled
* fixed code to prevent vaultpress false-positive security warning
* fixed duplicate event meta on duplication of event
* increased pricing to 6 decimal precision,
* added get_price_with_taxes function to EM_Ticket_Booking
* fixed multisite issues when creating events with conflicting post IDs on other sites
* fixed MS Global Mode permalink issues with cross-site locations and optimized cross-site event permalink creation
* fixed preview mode duplicating tickets whilst in draft status
* added event and ticket cut-off date support to recurring events
* fixed SSL/JS MIME related issues when cross-AJAX calls are made between HTTP/HTTPS
* fixed same-day event ordering issues in WP FullCalendar integration
* added admin menu EM dashicon
* added multi-level taxonomy condition searching so seperating ids/slugs by & will force an AND search as well as the traditional comma for OR searches
* fixed custom taxonomies not being saved from recurring events to recurrences
* added page_queryvar search attribute which allows for independent paginated shortcode lists via a custom page number id
* fixed HTML entities breaking CSV output in booking exports
* fixed recurrences validation when no days are supplied in weekly pattern
* improved event time validation to catch variations without help from the timepicker
* fixed taxonomy theme templates not overriding pages due to our formats
* fixed some typos on settings page
* improved styling of options page to make option sets clearer to distinguish
* added excerpt formats to events and locations and appropriate settings
* fixed BP private group admins being able to directly publish private group events whether or not they have publish caps
* changed minimum characters to autosuggest location from auto-completer on 'near' search to 2 characters
* removed usage of TimThumb
* changed thumbnail generation to optionally allow for full-sized images scaled by HTML attributes and adding querystring arguments for use with Photon by JetPack image scaling
* fixed typo for single ticket mode setting explanation
* fixed em_events_list_grouped not returning a string for output
* fixed order attribute not accepting lowercase asc/desc options
* fixed pagination issues when changing row limits on bookings table
* added distance options to search form on settings page,
* fixed distance not searching in km
* fixed DST miscalculations with google add-to-calendar link
* fixed ical descriptions not accepting \n characters
* improved translation text for event and location form submit buttons
* improved events and locations widgets so li wrapping tags can contain classes as well as adding sanitization, cleaning up etc.
* fixed 'awaiting payment' statuses not showing edit/approve links in EM Free
* fixed trashing blank recurrence creating unexpected results
* fixed escaping issues on punctuations for text searches on some servers
* fixed template templates/tables/locations.php to not show delete options if user doesn't have the capability
* improved country search attribute by adding ability to search multiple countries at once by using comma-delimited strings
* fixed ticket price formatting resulting in thousand separator triggering validation errors when updating event
* fixed private events showing up on global locations map
* improved loading of EM_Booking SQL to initially exclude em_meta and join the table
* fixed booking notes not being deleted in em_meta table along with booking
* changed booking email notifications so admins get an email for every action performed
* fixed duplication of event not copying over data stored in the em_meta table

= 5.5.3.1 =
* added #_TAGNOTES
* fixed forced owner event/location searches when logged in as subscribers and in AJAX requests where owner attribute is false
* fixed minor php warning

= 5.5.3 =
* changed scopes of various functions to static and various other related adjustments to fix PHP Strict errors
* increaded minimum required WP version to 3.5
* WP_Mail is now default mail transport method
* updated Swedish
* fixed issues with editing no-user name with pro custom forms
* fixed bug preventing redirection back to current page from the booking form login form
* fixed has_tag and has_category conditionals not being regognized when dashes are used
* added default color for categories
* fixed default color problem for FullCalendar
* fixed submit form not showing success message in FireFox
* fixed potential error in EM_Ticket::is_available() not checking event cut-off date,
* fixed single ticket mode new admin UI not showing ticket end date if cut-off date already exists
* fixed events_list_grouped shortcode always displaying pagination links even if disabled
* fixed update from 5.4.1 not creating new user email template in settings
* fixed "test email settings" button using saved settings rather than newly entered test settings
* fixed minor php warning if no attachment info supplied to EM_Mailer
* removed get_current_blog_id() function as this was for < WP 3.1 support
* fixed buddypress subnav menu items showing logged-in user links rather than displayed user (props to Maxime Lafontaine)
* added *_get_url*_get_ical_url and _get_rss_url for em_category em_tag em_location and em_event object functions
* fixed filter em_booking_calculate_price not assigning filtered value to booking_price property
* fixed problems due to lack of late static binding in get_post_search and get_pagination_links functions
* changed object get_post_search functions to accept all values from get_search_defaults
* fixed pagination problems when searching grouped events lists and ajax disabled
* changed - temporarily silenced 'scope' when 'eventless' is used on location list arguments as no results appear with both used at once
* changed search html input field to use placeholder attribute if available in browser like with Geo search
* added number_of_weeks argument to EM_Calendar to allow fixed week tables
* fixed WP FullCalendar not showing long events in following months
* fixed WP FullCalendar not showing all events in 6th week if current month ends 5th week
* added RSS feed ordering and scope options in settings page
* added #_BOOKINGSCUTOFF #_BOOKINGSCUTOFFDATE and #_BOOKINGSCUTOFFTIME placeholders
* fixed no cut-off date assigned to event when in single ticket mode and no end date/time defined
* fixed JS/BuddyPress bug when clicking to delete an event within the BP profile area
* fixed default events AJAX search not using default event list scope if no scope search supplied
* updated POT file along with Swedish and Polish translations
* fixed saving recurrence tempate not saving ticket role restrictions to recurrences
* fixed PHP error when tags are deactivated
* added functionality for searching multiple event/location owners in search attribute 'owner',
* fixed bug with pagination showing
* fixed quick edit not updating location author index table
* fixed closed message showing instead of login message to guests if registered-user tickets exist for event,
* fixed ticket availability calculation issue with member or guest only tickets when displaying tickets to user
* replaced usage of archive_template and category_template filters with taxonomy_template for both taxonomies, props to @avir673 and @greenshady
* added option on settings page and a search attribute 'header_format' for formatting groupby headers
* changed checkboxes html so text and box is wrapped in a label field
* fixed js date picker so change now triggered for end date fields when selected start date changes the end date value
* fixed display of duplicate events and incorrect cross-site events when in MS Global mode
* fixed incorrect page counts in some MultiSite Global instances by making use of SQL_CALC_FOUND_ROWS instead of COUNT()
* fixed rogue closing div on front-end events and locations admin table
* fixed "no location" dropdown problem in editor when using ddms with a default location
* added fail-safe compatibility check with Pro version to prevent known fatal errors on upgrades
* fixed BP 1.9+ warning for using bp_core_delete_notifications_by_type
* added status search parameter to EM_Person->get_bookings()
* changed BP events profile page to paginate events list and only show confirmed upcoming event bookings
* fixed language localization domain of migrated WP FullCalendar admin options to dbem
* fixed calendar more links linking to event if limit is set to 1 and direct links are enabled
* fixed recurring events not showing as pending or draft in front-end editor
* fixed saving of recurring events to pending from published status resulting in draft status
* fixed various PHP warnings
* fixed tickets not saving when submitting event anonymously
* fixed all-day events resetting booking cut-off time to midnight when saved
* fixed some warnings triggered by bookings with no real event (such as Pro MB bookings)
* (minor) added parameter to EM_Notices so that instances don't try to set/use cookie data
* changed use constant EM_FORCE_REGISTRATION in favour of EM_Bookings::$force_registration flag and EM_Bookings::is_registration_forced (backwards compatible)
* fixed PHP capability warning deriving from creating EM_Person instances with invalid user IDs
* fixed bug where extra admin emails added via hooks don't get sent if admin emails on settings page is blank
* fixed potential non-existent custom booking columns offsetting booking admin table pages and exports data by one cell

= 5.5.2 =
* fixed (rare) looping problem with calendar generation
* fixed permalink rule problems for events page children with url-encoded slugs
* changed country name 'Libyan Arab Jamahiriya' to just 'Libya'
* fixed BP Group selection dropdown being limited to 20 groups
* updated Hungarian and Polish
* fixed event results bug for certain months in WP FullCalendar
* fixed duplication error when tags are disabled
* fixed single ticket mode cutting off event bookings on event start (requires resave of affected events)
* fixed duplicate location problem and potentially fixed duplicate/shadow event problem,
* fixed booking cut-off times using submitted event time if no cut-off time submitted and all-day event is checked
* fixed php warning on taxonomy pages on some installs when overriding with formatting
* updated German and French language files
* fixed location still appearing when trashed
* fixed long page loads on settings page with many users or locations on blog (now requests location/user IDs instead of a dropdown)
* fixed bug preventing pro manual bookings for new users when guest bookings disabled
* fixed turkish datepicker not translating
* fixed issues caused by 5.5 update breaking filters using *_output_pagination (now requires PHP 5.3 to work, otherwise use em_object_get_pagination_links)
* fixed #_BOOKINGATTENDEES not working when 'reserve unconfirmed spaces' is set to no
* added new default search arguments for search form
* changed default country to 'none' by default,
* moved location search form html into new templates/search/location.php template
* changed location search fields to be shown if country search field is hidden
* fixed typo of option dbem_seRAch_form_submit
* added default country option specifically for search form
* improved search form arguments which allows fine-grained control via shortcode as well
* added events_search and locations_search shortcode names
* improved search UX by hiding location options if geo search used
* added geolocation unit and distance options to search form
* fixed XSS vulnerability in booking form and preventitive measures taken in other areas (no template fixes necessary)
* fixed feeds not working when no events page defined
* fixed booking_date using mysql instead of blog timezone
* fixed maps not showing on front-end edit locations page
* fixed pagination querystring affecting other event/location/category/tag lists without pagination enabled
* fixed events RSS feed taking over blog feed if events page is homepage
* changed RSS feed url ending to feed/ to avoid unnecessary redirects
* fixed bbPress deactivation erasing all EM capabilities for all roles
* added removal button for category images
* added JS for hiding event/location images when marked for deletion on public forms
* fixed ticket cut-off time not being accurately respected
* fixed 'all' status search not returning drafts
* added 'everything' status which includes trashed events/locations
* fixed BP CSS conflict with wp_editor (https://buddypress.trac.wordpress.org/ticket/5167)
* changed order of when recurrences get saved if in admin area so other plugin post meta data gets properly copied over
* added missing translatable strings for ML (WPML) mode in settings page
* fixed MS Global location link problem when viewing location on different blog,
* fixed AJAX searches not including subsite items in MS Global mode
* improved RSS linking and overriding logic
* added RSS feeds for individual locations/categories/tags
* added iCal feeds for individual locations/categories/tags and any custom taxonomy attached to locations or events
* fixed category base slug not being editable in MultiSite (kudos Maxime Lafontaine)

= 5.5.1 =
* fixed init code not executing WP FullCalendar integration in time
* changed generation of WP_Rewrite rules for children of event post type to query the database directly rather than use get_posts
* updated French translation
* fixed mysql warning/error when creating events with a new location
* changed default events widget format to use #_EVENTDATES
* fixed google maps problem
* fixed 5.5 bug where search attributes being included in pagination links when not necessary
* fixed calendar not using ordering as per settings
* fixed chronological ordering on calendars with long and all-day events
* fixed geo.bak.js not being overridable an overridable template file
* fixed limit=0 being ignored in shortcodes and functions
* fixed pagination being forced for categories_list shortcode regardless of attribute

= 5.5 =
* fixed different versions of http/https maps js libraries being called
* added performance improvements to calendar queries and generation, speeding up calendars with many events per month
* added Reunion to countries list
* added more meaningful text to settings page to clarify booking pages
* added link to profile page to avoid confusion with admin test emails
* fixed php warning in my bookings page bottom pagination
* changed booking ticket editor forms and removed the jQuery UI Dialog in favour of inline forms
* added guest-only ticket option 
* added member role restriction options to tickets 
* changed jQuery datepicker to have a -100 +10 year range
* added numeric check to ticket spaces during get_post
* added public class flags for event/location/booking admin and my bookings pages,
* changed - consolidated generation of all "my bookings" pages to go via em-template-tags.php functions
* removed my bookings page being generated via events page if no page defined (you should now define a page or use the shortcode)
* changed all listing shortcdodes and template tags to use relevant list template if no specific format supplied
* improved event list/search ajax JS
* added AJAX capabilities to search forms and event/location/category/tag lists with pagination
* added data attribute support for pagination links when AJAX is enabled
* revamped the search form styling and structure
* added location page search form and shortcode/template tags
* fixed pagination bug when searching events/locations with text
* fixed EM_Mailer fatal error bug
* fixed strpos() warning on em_get_locations
* improved efficiency of grouped events list
* added bookings_open and bookings_closed conditional event placeholders
* added CSS flags to templates for future-stying possibilities to event/location pages and lists, tag lists, category lists, booking forms and front-end admin areas
* changed search form and pagination AJAX php to use wp_ajax_ actions
* moved pagination functionality into extendable EM_Object::get_pagination_links for use in *::output() functions
* moved EM_Events::get_post_search() into EM_Object::get_post_search() to allow sharing with other objects using search forms 
* moved display code for em_get_events_list_grouped function contents into EM_Events::output_grouped()
* changed EM_Events and EM_Locations into 'static'-only classes (function scoping will follow eventually)
* changed scopes of EM_Object::get_post_search and EM_Object::get_pagination_links to static
* added near, near_unit and near_distance search arguments for coordinate-based searches
* added geographical searching and Google places autocomplete service
* added more search form options to settings page
* removed page number and list number definitions from within list templates
* modified various templates, detailed below:
* changed booking ticket template by unifying forms/ticket-form(s).php into forms/event/bookings-ticket-form.php 
* changed forms/bookingform/tickets-list.php to account for displaying guest and role-restricted tickets
* removed inline JS from forms/event/bookings.php template
* removed inclusion of forms/tickets-form.php from forms/event-editor.php
* added inline js from forms/event-editor.php to JS file
* moved em-tickets-form div ID to cover just tickets and event-rsvp-options covers the rest, 
* unbolded labels and made headings h4 in bookings section of forms/event/bookings.php
* changed h4 to h3 in event-editor.php and location-editor.php templates
* removed search form inclusion on templates/events-list.php and is now included separately
* added wrapper class to templates/events-list.php layout for future styling
* moved grouped events out of events-list.php into events-list-grouped.php template
* fixed/added sanitization to EM_Ticket::get_post to prevent potential xss,
* fixed/added escaping to booking form template
* fixed recurrences not updating category in main site of MS Global installs
* fixed overriding the_content for valid items in a loop when on tag and category pages
* removed global $EM_Location from locations admin list template
* fixed the em_maps_location_hook js hook not being triggered event/location admin pages
* fixed php warning when creating events with no location info
* fixed some untranslated text
* added parsing of placeholders on single event and location page titles (useful in SEO plugins)
* lowered priority of wp_footer js to 20 (from 10)
* moved WP FullCalendar integration code into EM (and loaded only if needed)
* updated Swedish, German and French language files
* added Chinese Simplified (Taiwan) 
* updated POT file
* changed - simplified inclusion of event post type in search results
* changed - set priority of save_post for events/locations to 1
* added preemptive validation during wp_insert_post_data which immediately makes it a draft before any saving is done
* fixed php warning in bookings admin table
* added per-event tax rate function and corresponding em_event_get_tax_rate filter
* improved map loading JS and now can be used in ajax searches
* changed templates/map-global.php - moved coordinates div into container div
* added js em_maps_loaded and em_ajax_search events
* updated jQuery - changed instances of .attr('checked') to .prop('checked') or .is(':checked')
* changed location forms to have consistent id and class structure (id to be deprecated)
* changed CSS for location forms to uses classes not ids
* added templates/forms/map-container.php
* changed location form templates to call a separate/single map template
* fixed inconsistencies with set_status functions in EM_Events and EM_Location objects
* added deregistration of em script on BP messages page to avoid autocompleter clashes
* fixed WPDB::escape() usage and replaced with esc_sql()
* fixed duplicating events not copying excerpt and tags
* fixed capabilities not being saved for bbPress 2.2+ roles
* fixed featured image on event/location submission - now only showing delete checkbox if image exists
* changed #_ATTENDEESPENDINGLIST so it includes any booking statuses that reserve a space

= 5.4.4 =
* updated Spanish, Polish and POT translations
* fixed search attribute owner so 'me' will show no events to guest users
* added optional $email_attendee parameter to EM_Booking::email() function
* updated lightness css theme to support jQuery UI 1.10.x (used in WP 3.6)
* fixed JS errors related to jQuery 1.10.x (used in WP 3.6)
* moved em_template_my_bookings_footer action so only called if logged in,
* fixed event_date_created not being saved in events table
* fixed "Email Sent" message showing up if no emails sent,
* added em_booking_admin_emails filter
* added bookings table default ordering by date of booking
* fixed CSS for event editor location fields and maps not appearing on same row
* fixed links on MS subsites in global mode to other blogs being incorrect if direct linking disabled
* fixed eventful locations search showing trashed/pending locations
* fixed problems with previous_status flags in event and location objects
* fixed problems with approval email notifications not going out
* added responsive resizing for location google maps
* fixed read_others_locations not being enforced to location dropdown on event editor
* fixed bookings cut-off time not being saved on all-day events
* fixed php warning on category page
* fixed some words within formats not being translated on first install
* fixed various missing translation domains from gettext functions

= 5.4.3 =
* minor JS mod for some rare IE9 conflicts by moving global load_ui_css variable into jQuery.ready()
* fixed booking form showing tickets twice
* fixed issues with placeholders not converting if immediately preceded by a conditional opening/closing tag
* fixed overriden emails/new-user.php templates not being respected anymore since this was added to settings
* fixed timepicker compatibility with jQuery 1.9.1
* fixed google maps js being loaded if previously loaded by another plugin
* fixed translation domain missing for some text in settings page
* fixed ical infinite loop problem when limit is 0
* fixed MS Global Tables bug when filtering by categories for sub-site events
* added RSS events limit option
* removed location page title format option from settings if not in MS Global mode
* modified location search attribute for events with no locations

= 5.4.2 =
* improved the handling of orphaned events and locations, they now show information and can be deleted front-end
* fixed deleting of drafts on front-end,
* fixed display/deleting of trashed events and locations on front-end
* changed status of trashed events and locations to -1 (old trashed events may still show up front-end)
* added escaping to search input field in front-end events admin,
* front-end events admin search form now works in all event statuses
* added draft/pending/publish as possible values to event/location status search attribute,
* took out unnecessary update of post_name in EM_Event::set_status()
* fixed untranslated dates when showing post meta
* updated German and Russian translations
* changed recurrence form to say 'each event SPANS x days' instead of LASTS
* changed SQL statement to use of EM_Event::set_status() in EM_Event::save()
* location shortcode and functions can now search status by text name (trash/pending/publish/draft)
* fixed em_bookings_get_tickets filter being applied in wrong function instead of em_bookings_get_available_tickets
* fixed email subjects showing escaped entities
* fixed events with members only tickets showing bookings closed to guests
* added option to show member-only tickets to guests
* modified booking form and tickets list templates to achieve the above two changes
* fixed invalid username characters such as + in emails causing registration errors
* fixed tickets admin JS where deleting default ticket prevents new ticket being added without page reload
* added #_BOOKINGSUMMARY placeholder
* added registration email templates to settings page
* added location and description format options to ical settings,
* added description of event to ical feed,
* merged single event ical template ical-event.php (now deprecated) with main ical.php feed
* fixed is_past conditional not considering if current events are set to be past events,
* added is_current conditional placeholder
* improved excerpt handling by balancing html tags when more tag is used
* added word limiting options to #_EVENTEXCERPT and #_LOCATIONEXCERPT,
* added formatting filters to excerpt (applicable when not using word limiting)
* improved/refined search form AJAX to include state/region/town lists if country is defined as well as omitting null values
* added month formatting option in settings page for calendars
* moved em_events_get_sql filter above/before count function executes sql
* added #_BOOKINGDATE #_BOOKINGTIME and #_BOOKINGDATETIME placeholders
* improved EM_Category object - now can be created with slug or name
* improved taxonomy search arguments, now capable of automatically searching any taxonomy registered with events or locations
* updated POT file and German language
* added EM_Walker_Category class and hierarchical category checkboxes in event admin MS Global subsites
* modified JS (minor) - checks EM.ui_css is set before loading jQuery UI CSS
* improved Google Maps - now capable of being responsive via placeholders, shortcode and settings page
* added excel 'hack' to support UTF-8 characters

= 5.4.1.1 =
* fixed sortable collumns not working on export bookings function

= 5.4.1 =
* fixed EM crash when legacy tax rate is 0 and free events are booked
* updated German and Russian

= 5.4 =
* fixed drag/drop in bookings admin not always recognizing the change
* improved location search and reset ui logic
* fixed precision of long/lat values in database
* fixed taxonomy filtering bug in admin area
* added possibility to override timthumb via wp-content/em-timthumb-config.php
* fixed some php warnings
* updated French, Russian, Czech translations and POT file
* changed templates/tables/events.php to use $EM_Event instead of $event
* fixed #_BOOKINGBUTTON JS jumps when clicked
* revamped pricing calculations to account for taxes applied at time of booking (see http://em.cm/v5-4)
* added a standardized discounts system pre/post tax
* fixed EM overridden pages showing despite password-protection
* fixed some settings typos
* fixed month scope filter skipping last day of month events in admin events list area

= 5.3.9 =
* fixed XSS vulnerability in search form field
* fixed php warnings in events-list.php
* 'mail sent' messages not shown if no mails actually sent without errors, changed wording of 'mail' to 'email'
* updated French and German languages
* added links to category page on #_EVENTCATEGORYIMAGES images
* reordered search form template variable definitions for future splitting up of fields into individual reusable templates
* changed htmlspecialchars to esc_attr, added esc_attr to various input fields
* improved sanitization of front-end form title submission to prevent entity conversion in db records
* fixed missing EM js variables on public edit/submit events page

= 5.3.8 =
* fixed timthumb issues due to new local fetching modification and bad file inclusion path
* fixed time/date separators in settings page not being used
* added yarpp support to post types
* fixed some tag and category warnings when using assigned tags/categories pages
* fixed various PHP warnings
* updated French 

= 5.3.7 =
* fixed extra paragraphs being added to #_EVENTNOTES
* fixed location 'no events' message format not using header/footer formats
* updated Finnish, German and POT files
* added customizable event and location page templates
* improved em_options_select() now can create optgroup groups if an array value is supplied
* fixed clashes with JetPack Tiled Galleries
* fixed timthumb thumbnails issues with MultiSite and virtual links
* fixed multisite global problems when fetching and saving main blog events previously saved with NULL blog_id
* fixed bug with categories search attribute not working if any spaces exist such as "1, 2"
* improved offset calculations in ical for some servers
* improved ical generation to avoid memory limit problems with very large numbers of events
* fixed RSS feed pubdate format
* improved generation of RSS feed to avoid memory_limit errors on very large feeds
* changed em_rss_pubdate wp_option to em_last_modified which is now a timestamp
* improved em_get_wp_users() function to fix overload caused by http://core.trac.wordpress.org/ticket/23609
* changed default events aren't created anymore
* changed public events admin table template to allow viewing of draft events (duplicates) and modified view linking formats
* changed duplicates so they now become drafts by default not published
* improved settings page tab/section loading UX
* added event archives scope
* improved MultiSite Global so unnecessary tables aren't created for every blog
* fixed outdated group tip on event form (you can detach group events)
* added #_CONTACTURL
* fixed tags not being added to recurrences if no categories assigned too
* fixed datepicker problem on search pages when scope not defined and switching pages
* fixed wp_em_events post_content not being updated if description is removed
* added wp_title filter to widgets
* fixed incorrect number of events per day shown in full calendar according to settings
* fixed the_title usage to check the post id supplied in second parameter
* changed/fixed action where rules are rewritten on settings changes causing 404s for CPTs created in theme functions.php file
* fixed bug with W3TC repeating first no-user booking name for all others in admin table views
* fixed 'email exists' errors in no-user bookings mode
* added option for no-user registration with registered emails
* added #_CATEGORYNEXTEVENT, added formatting options for location/category next event placeholders
* improved event, location and category timthumb thumbnails so they accept 0 as a width/height to prevent cropping
* added ical scope option
* fixed category placeholders not being replaced in alphabetical order (so #_CATEGORYNEXTEVENT cannot overwrite #_CATEGORYNEXTEVENTS)
* added #_TAGNEXTEVENT and formatting options
* fixed problem with pagination not highlighting first page number when doing searches
* changed maps js to close other infowindows for locations map when marker is clicked
* added locations search attribute
* fixed autoembed and embed shortcode support for event/category/location placeholders for descriptions
* fixed duplicates triggering 'published' actions on duplications such as tweeting via WP to Twitter
* fixed author not being changed on quick edit
* fixed conflicts with various plugins which add custom registration validation (e.g. SI Captcha, Theme My Login, etc.)
* fixed bug where #_LATT fields not appearing in public location editor if event attributes aren't enabled
* added booking links to edit event booking stats meta box even if no bookings made
* added em_bookings_filtered and em_locations_autocomplete_selected jQuery events
* fixed links pointing to admin on public booking admin tables after pagination clicks or multiple ajax calls
* updated German translation
* added em_calendar_get_args filter
* improved EM_Category::has() - now also checks category name too
* removed redundant functions in EM_Category
* improved default ordering of events in categories page applied to EM_Category::get_default_search() rather than just category pages,
* improved category taxonomy when overriding with formats when using an assigned categories page (particularly breadcrumbs)
* added specific tweaks for Yoast WP SEO plugin for breadcrumbs when using an assigned categories page
* added a tags page and template
* created EM_Tags class - very similar to EM_Categories
* fixed lack of pagination on tag placeholders showing related events 
* fixed private locations turned public not appearing in public listings
* fixed today/tomorrow scope not working properly in wp-admin
* fixed pagination variables overriding shortcodes with fixed page attribute
* improved - minor adjustment to location autocomplete ui tip text behaviour
* added em_map_loaded js trigger for location admin map
* updated German

= 5.3.6.1 =
* fixed some XSS vulnerabilites in the booking form/admin areas

= 5.3.6 =
* added is_free, is_free_now, not_free and not_free_now conditional placeholders 
* modified EM_Event::is_free() so it can also check if event is free at that moment, 
* added do_shortcode filter to dbem_notes so shortcode is parsed outside single event pages
* fixed category filtering when using negative/positive combinations
* fixed category filtering in MS Global mode
* fixed missing menus items for normal blog admins in MS without plugin caps
* updated italian and swedish
* added finish countries translations
* added sorting for countries lists (previously sorted by country code)
* updated POT file
* updated French, updated German (unfuzzied loads of strings, may need some corrections)
* booking meta now uses maybe_unserialize on instantiation
* separated booking validation from save function
* moved user registration logic during bookings into a reusable function
* cleaned up the email admin setting panels for submissions and booking templates
* booking email messages array now generated in separate function
* added em_get_location and em_get_event filters for event/location object retrieval functions
* added em_get_booking getter function with corresponding filter and changed all used of new EM_Booking()
* added em_bookings_admin_page action at start of booking admin pages
* added taxes functions to booking and object classes
* added get_admin_url function for booking object
* added get_price_taxes function to booking object
* added bookings filter condition to exclude bookings with event_id of 0 by default (for Pro Multi-Bookings)
* added data response as second argument to jQuery em_maps_locations_hook event
* fixed bookings form showing on password-protected events
* fixed MS Global blog switching issues when saving
* fixed $EM_Booking->get_tickets() returning all tickets rather than tickets in specific booking
* fixed hidden BP groups not showing to admins
* fixed some German translation file inconsistencies
* fixed blank datepickers if date format is left blank in settings
* fixed tags filter searching multiple tags returning no events
* fixed some MultiSite PHP warnings when adding/deleting sites
* fixed booking button not showing cancel if event is fully booked
* changed booking button so only one booking can be made
* fixed attributes not showing on event submission forms if categories are disabled (option name typo)
* added editable form, so no-user bookings user data can also be edited
* changed booking form JS enqueueing by moving it into EM_Bookings object as a function 
* changed booking JS to use on() event delegation for more AJAX compatibility
* fixed some booking form CSS field styling inconsistencies
* fixed issues with locations on sub-blogs when in MultiSite Global mode with locations restricted to main blog
* fixed duplicate confirmations/warnings on MultiSite location admin pages
* changed (improved) EM_Object::can_manage method to avoid extra calls and potential warnings
* changed csv export of single event so the file name = the event name

= 5.3.5 =
* fixed bug in placeholder formatting

= 5.3.4 =
* fixed Multilingual settings not saving default language setting if other than english
* fixed typo in performance optimization settings
* fixed warning of undefined ID on archive pages when enqueuing scripts
* fixed special characters being converted to entities in non-html emails
* fixed typo in options for category/location event list placeholders
* corrected Slovak translation, thanks to Julius Pastierik 
* added British translation, thanks to Jeff Cole
* added some code to booking form js to prevent JS conflicts with JetPack's reCaptcha
* added base64 encoding/decoding to em_notice cookies for improved compatibility
* fixed potential php warning in EM_Tickets class
* event spaces show as blank rather than 0 on input form (aesthetic change in line with the field help text)
* added alphabetical ordering to category and countries ddms in search form and admin event categories ddm
* fixed XSS vulnerabilities - http://em.cm/xss
* fixed em_is_category_page() and added check for specific categories (like is_tax() second parameter), added em_is_tag_page() with checks for specific tags
* added #_EVENTPRICERANGEALL and fixed #_EVENTPRICERANGE showing if booking closed but unavailable tickets set to true (docs need revising)
* improved speed of event shortcode by adding global event object
* added ordering of locations by name and other location table fields in event queries such as events_list shortcode
* added some missing classes to event form 'when' section 

= 5.3.3 =
* changed taxonomy pages to use is_tax() to check whether page is a taxonomy page, rather than checking the $wp_query object
* fixed booked spaces being off if approvals are disabled and booking has status 0 from when approvals were enabled
* added #_CATEGORYSLUG
* fixed bad usage of wpdb::prepare in classes/em-object.php
* added em_actions_locations_search_cond for autocompleter
* added Bermuda and Jersey to countries list
* fixed title rewriting warning when $sep is blank
* fixed BP 'add event' link appearing to all users on event profile pages
* fixed loading of unused post meta overwriting post fields/properties
* added multilingual capability
* added WPML add-on warning
* simplified enqueue of styles and scripts, now uses wp_enqueue_scripts action
* added performance optimization options for CSS and JS files
* removed usage of PHP sessions in exchange for temporary cookies
* added possiblility to use wp thumbnails rather than timthumb (category images need resaving via uploader)
* added #_LOCATIONLONGITUDE and #_LOCATIONLATITUDE placeholders
* added em-pagination span wrapper and em-tablenav-pagination classnames to normal and table pagination sections
* fixed location searches not returning results admin-side in MS global tables with locations on main blog
* updated JS for location map to update when location name changed
* added pagination and limits to location and category event list placeholders.
* updated finnish language
* fixed minor php warning when deleting a user with no events in MultiSite
* fixed php warnings on category pages using formats, major change to how formats are overriden, now rewrites WP_Query completely
* added em_options_page_panel_admin_tools action to admin tools section of options page
* fixed #_BOOKINGPRICE and other booking price placeholders not using currency formatting option
* fixed permissions issue with groups plugin (thx itthinx)
* added Slovak translation, thanks to Branco
* added em_bookings_table_row_booking_price_ticket filter (needed for Pro ticket exports w/coupons)
* added default rows/cols attributes to booking form textarea field for valid html
* fixed conflict with caching plugins and booking forms due to cached wpnonces
* added #_CATEGORYDESCRIPTION to default EM filters of content placeholders
* updated timthumb.php to version 2.8.11

= 5.3.2.1 =
* added is_singular to $wp_query on format overriden category pages, fixes conflict with WooThemes Canvas theme

= 5.3.2 =
* fixed blog switching compatability in MS Global mode due to switch_blog improvements in 3.5 release

= 5.3.1 =
* Updated russian translation, thanks to Alexander Tsinkalov
* improved how EM hooks into the_content and similar template tag hooks to improve compatability with other plugins incorrectly using this outside the loop
* fixed rsvp cut-off time not being considered
* fixed missing $ in admin_capability variable of EM_Object::can_manage()
* added recurrences search attribute
* corrected some typos in category event form class, recurrence description
* prevented spaces in comma-delimited email lists failing email validations
* fixed bp event profile page urls breaking when event slugs contain slashes
* updated [events] and [location] to default to use globals if no ids passed on, so it can be used in location/event descriptions
* added resubmitted event confirmation messages
* Updated Spanish translation - thanks to Ponç Joan Llaneras

= 5.3 =
* corrected date_format/dbem_date_format typo on templates/templates/my-bookings.php
* fixed calendar links with extra search args if using non-permalinks
* fixed some non-translated strings
* updated pot file
* added em_booking_add_registration_result filter
* fixed add_user_to_blog on MS bookings not providing a default role
* fixed navbar issue for blogs with events page assigned to homepage
* updated Dutch translation, thanks to Arnout Eykelhoff
* partially-updated Russian translation, thanks to Evgeniy Efimenko
* fixed #_EVENTNOTES being converted before other placeholders, which potentially caused problems if using shortcodes with formats
* updated Chinese thanks to Leo Losoviz
* added #_ATTENDEESPENDINGLIST
* improved JS compatability with booking form (spinner and jumping up to errors/confirmation feedback)
* fixed reserved pending spaces not being approvable if event is full
* fixed categories and other plugin postmeta not being duplicated with event
* fixed admin emails not going out if setting of admin emails contains spaces
* fixed blog search filter not allowing comma seperated values e.g. 1,2,3
* improvements to listings, edit pages and admin linking of locations on MS Global setups, especially if locations are restricted only to main blog
* adjustment to title rewriting, fixing issues such as calendar day pages not having custom titles
* event end time js not failing validation if event dates span
* fixed locations not publishing on anonymous submission even if publish_locations is correctly enabled for assigned anon user
* added second em_booking_form_ticket_spaces action to templates/forms/bookingform/ticket-single.php fixes attendee field not showing for single/last ticket/space bookings
* reverted to previous use of global $wp_query in parse_query filters with additional fix to prevent clash with Pods framework

= 5.2.9 =
* added spaces to comma seperators of location on locations admin table
* improved user deletion hook, bookings now get deleted along with user
* replaced depreciated placeholders in default widget values
* added booking form anchor to action url
* updated POT file and Swedish
* added Japanese, kudos to Kenta Sakamoto
* fixed minimum ticket space value not allowing 0 spaces even if ticket not required
* added em_event_save_events_slug filter
* added $EM_Event to all do_action parameters on templates/placeholders/bookingform.php
* changed #_MAP to #_LOCATIONMAP on installation defaults
* wrapped bookings dashboard sections into divs with class names
* fixed formatting links/texts in settings page tips
* added em_options_booking_form_options action and save button on settings page > bookings > booking form options
* fixed use of global $wp_query when passed within parse_query action - thx Scott @ Pods framework plugin
* added wp_dropdown_pages instead of manual page select generation
* fixed useage of outdated user_firstname/user_lastname property for EM_Person::get_name()
* improved ticket minimum calculation (takes into account if ticket is only ticket in event, therefore required)
* fixed EM_Tickets and EM_Tickets_Bookings not storing ticket/ticket_bookings array key by ticket id
* locations list format header and footer now install ul by default, if blank no wrapped ul is used (which previously contained an 'a' outside an li)
* small fix to em_content_categories_args, now applied to EM_Categories::get() and EM_Categories::output() in templates/categories-list.php
* shortened gcal link some more to prevent some (rare) "404 url too long" errors
* added login redirection to same page in login link of my bookings page

= 5.2.8 =
* fixed js bug arsing from 5.2.7 js 'fix' for datepickers
* fixed categories not showing up on single category pages
* fixed php warning when quick-saving

= 5.2.7 =
* fixed min ticket space number calculation issues in booking forms
* fixed multiple admin emails for event submission by members with publish rights
* added em_bookings_ajax_complete/error jquery events/hooks
* updated/added Swedish, Chinese, and Finnish translations, kudos to Tommy Wahlund, Leonardo Losoviz and @Daedalon respectively
* fixed mailer charset problem in SMTP mails
* added dbem to some __ functions
* added ticket-price class to single ticket booking form html
* fixed datepicker breaking on themes inserting br tags into search/booking forms
* fixed html email setting not working in MultiSite
* added Guadaloupe to countries list
* added em_csv_header_output action (e.g. for Excel hack)
* changed/prevented registration email going out until after booking is completely saved
* added filter em_object_json_encode_pre
* fixed country searches on events page/search form
* added conditional placeholders is_private not_private is_recurrence not_recurrence
* fixed #_EVENTEXCERPT not having formatting filters applied
* changed the_content run at priority 9 for category pages
* fixed private location autocomplete/search issues
* fixed recurrences not being deleted when done from front-end
* fixed edit user link on booking admin area
* fixed edit location link showing to all
* fixed typo in map balloon hint on settings page
* removed default contact person setting (used in < v5.0, now uses author)
* added width/height property to thumbnail img html
* fixed deleted MS subsites not deleting events/locations from global tables
* fixed maps showing undefined fields on first load of edit event with location ddm enabled
* fixed non-registered attendees not being included in no-user mode for #_ATTENDEE and #_ATTENDEELIST
* fixed front-end location admin pagination
* reduced sql statements for county my/all locations on front-end locations admin page
* fixed select all ui problem (thx @Daedalon)
* fixed array_key_exists php warning in EM_Object::can_manage
* changed is_main_blog to is_main_site
* added grandchildren detection when generating permalink rules for events page
* added auto br to emails option in email settings

= 5.2.6 =
* changed validation order for bookings (no validation done in EM_Event::get_post())
* EM_Tickets_Bookings::$tickets_bookings now an associative array, keys are ticket id
* EM_Notices now accepts 2 level arrays for nested errors
* added em_bookings_table_export_options, em_bookings_admin_ticket_row actions 
* added em_bookings_table_get_headers filter 
* admins can now manually approve bookings regardless of whether events are fully booked and overbooking enabled
* fixed search page bugs
* removed some unecessary validations on get_post functions, assumed these are only run on validate() and save(), eventually it'll just be validate()
* fixed js issues when updating ticket options with checkboxes
* hooked into the_excerpt_rss filter to allow overriding event formats on normal rss feed
* fixed recurring event not correctly saving timestamps
* fixed minimum spaces problem on booking form, added 'required' tickets option to allow more possibilities
* fixed js incompatability
* fixed link on single booking admin page if user is a guest in no-user mode
* updated German, French, Hebrew, Dutch, added partial Chinese translation
* hid some unecessary localized JS strings depending on what features are enabled (bookings/recurrences)
* fixed negative non-existant category id searches showning no events instead of all events
* fixed pagination problem on templates/calendar-day.php
* added js triggers em_booking_error and em_booking_complete 
* fixed event price placeholders not accounting for unavailable tickets

= 5.2.5 =
* fixed long google calendar link issue
* fixed and improved duplication function
* allowed cancel link for offline pending bookings status
* fixed MS bug with featured images in global mode on different main/sub sites
* fixed duplicate booking activity posts when event belongs to a group
* fixed template templates/events-list.php for grouped events list
* fixed dots in usernames breaking booking activity feed links
* added booking comment collumn to booking admin tables
* changed some localized edit %s strings
* fixed booking links pointing to admin/front-side oppositely on ajax calls
* fixed tag and category searches showing all events if categories/tags don't exist

= 5.2.4 =
* fixed single ticket template and name of spaces being hard coded
* added em_booking_form_ticket_spaces action
* closed date and time picker inits with functions
* fixed warnings on options page
* fixed potential 0 spaces being available on tickets with a min requirement
* fixed ticket warning
* fixed some permalink issues for homepage and child pages of events pages
* fixed rare fatal error due to undefined object in admin/em-bookings.php

= 5.2.3 =
* improvement to ical all-day time format
* fixed grouped events shortcode not using formats supplied
* removed 'remove from page lists' options for category/event/location pages, see http://em.cm/rplo
* added calendar day abbriviations (useful for some languages e.g. Catalan, Welsh)
* moved events page calendar options to 'pages' tab, event list/archives panel
* fixed attributes bug using default value even when custom value entered (arose in 5.2.2)
* added search button custom text setting
* removed duplicate confirm/error notices in bookings admin area (dashboard-side)
* fixed #_LOCATIONPOSTID showing location id instead

= 5.2.2 =
* added member-only tickets
* added em_bookings_added action
* fixed broken grouped events titles
* fixed some timezone nuances in the ical, simpler and hopefully more reliable dealing with dst
* fixed pesky recurrence bug not ignoring the new booking cut-off dates intended single events, as a result all recurring event cut-off times will be reset to null to prevent prematurely closed booking forms.
* simplified some tab and panel names in the settings page, moved location map balloon settings to maps section
* booking forms will now show under event content if override with formats is set to 'no'
* fixed some event/location attribute nuances with regards to defaults overriding each other, will document
* made attributes, categories and featured image sections in public event/location editors their own template parts
* removed links to person summary page if in no-user booking mode and user isn't registered
* fixed bad action url in booking form when under subdirectory installs (although not actually used by ajax)
* fixed MS queries defaulting to global searches, including transactions
* fixed MS network blog tables not being deleted by WP along with rest of blog

= 5.2.1 =
* quick fix for fatal error on front-side event submit forms when bp is disabled

= 5.2 =
* reversed order of settings link in plugins page
* updated French, Czech, Dutch and pot language files
* fixed category page display problems on some themes (e.g. Thesis) and for some plugins using the loop on the same page
* added rss pubdate to individual events
* removed cut-off time for now from recurring events, since it copies same date over to every recurrence
* fixed bug in bookings table ajax
* updated to PHPMailer 5.2.1, added attachment capabilities and the em_mailer or em_mailer_sent actions
* fixed magnified google maps logo size
* fixed admin emails not going out on event submission under some configurations
* improvements to permalinks for home page loading
* added first/last name collumn to bookings admin table
* added conditional placeholders for location image has_loc_image and no_loc_image
* fixed cancelled/rejected mixup in bookings admin table
* single booking button will show 'Sold Out' if full
* booking cancel link will not show to rejected, cancelled, pending online payment statuses
* fixed yearly recurrences bug
* added em_get_currency_formatted filter 
* added event_category shortcode
* fixed search bug not showing long events outside start/end parameters
* fixed event form not showing submitted data on validation fail during second event submit
* added event re-submission and re-approval custom emails
* added has_category_x, no_category_x, has_tag_x, and no_tag
* my-bookings auto permalink won't happen if bookings disabled
* added israeli translation
* added choice of initial length for calendar day names
* template modification: passed on $EM_Event to em_booking_form_custom action, may prevent some bugs in certain situations
* fixed ical modified timestamp issue
* fixed scheduled events not being published
* fixed incorrect event links to cross blog events in MultiSite global mode
* added $post_ids to em_event_save_events filter
* updated booking button, made button text customizable and moved the js out of the template file
* updated forms/event/groups.php template to allow for site admins to see all bp groups
* updated ui-lightness.css jQuery UI CSS Framework file to v1.8.22
* fixed templates/templates/events-search.php to prevent showing unapproved locations in dropdowns
* fixed admin CSS and JS locations using WP_PLUGIN_URL instead of plugins_url()
* fixed global events and locations not showing up in MS subsites
* fixed dev-mod updating in MS network admin
* added a test email settings button/feature
* fixed grouping by date fomrat option in settings page not being used
* tidied up location autocompleter formatting
* fixed potential timepicker bug when themes add extra br tags between timepickers
* fixed maps not loading when updating events
* fixed ical description formatting issue escaping &
* fixed default location not showing on second event form update
* fixed booking table links going to admin area on public pages after ajax calls
* fixed ical time and time placeholder output issues
* fixed event form breaking when BP groups are deactivated
* fixed printable booking report showing non-approved bookings
* fixed widgets not allowing blank titles and disappearing widget headers
* removed weekly recurring events days checked by default
* removed some optional properties to the ical files, including X-WR-CALNAME
* fixed weekly recurrence bug skipping first event on some instances
* fixed rejected bookings counting as double bookings
* removed unapprove link showing up for rejected/cancelled events
* added bookings link to actions on the wp users admin page

= 5.1.8.5 =
* fixed bug with bookings being open/closed due to changes in 5.1.8.5

= 5.1.8.4 =
* fixed some issues in the EM blog updater and EM use of table constants
* improved BP member link generation in activity stream, uses bp_core_bet_user_domain now
* fixed cancellation link disappearing after booking cut-off date, even if event hasn't started
* fixed use of some get_price style filters and supplying pre-formatted currency numbers
* fixed pagination issues in shortcodes
* fixed booking table ajax issues
* fixed location auto-completer not working when maps are disabled
* fixed ical all-day event issues when offsets come into play
* fixed single day ical offset problems

= 5.1.8.3 =
* fixed bookings being closed if booking cut-off date/time not specificed in new events

= 5.1.8.2 =
* added booking cut-off times
* fixed events with bookings table ajax
* fixed bp group events list not showing location info
* fixed calendar day pages showing 'past' events if option is set not to

= 5.1.8.1 =
* important - Modified template files? See this http://em.cm/templates-5181
* fixed date ranges not working properly
* fixed pagination issues

= 5.1.8 =
* important - Modified template files? See this http://em.cm/datetime
* fixed jigoshop session_start conflict
* removed some group metabox php warnings
* fixed slashes added to location/event name in db table
* fixed/improved multisite capability management (see network admin settings)
* events with >1 ticket will show multi-ticket editor regardless of single ticket mode setting
* updated Brazilian language, added Catalan and fixed a few language datepicker oddities
* fixed RSS validation fails for some special characters
* fixed cancellations being possible after event bookings close
* fixed admin-side search-by-category
* fixed manage_others_bookings not allowing access to bookings without other caps
* fixed calendar widgets taking on day link search arguments from other parts of the page
* fixed admin email problems when in auto-approve mode and using alternate status numbers e.g. 5
* added force-approve booking flag in EM_Booking::set_status
* fixed ical locations and apostrophes and single ical file time offsets
* fixed gallery shortcode for recurring events
* fixed guest event submission auto-complete
* refactored booking form JS to allow multiple forms on one page
* fixed some booking js ui animations
* fixed booking table overlay issues
* added more js vars for translation purposes
* improved placeholder replacement logic
* added em_event_submission_login filter
* simplified timepicker and datepicker JS and html strucuture for re-use
* EM_Notices behaviour changed so errors are printed and not deleted, only at start/end of script

= 5.1.7 =
* added excludeable categories (use negative numbers instead)
* clarified some of the field tips of "other pages" in options
* fixed thumbnail issue in MS (again)
* added event dates and times as sortable booking collumns
* fixed multisite duplicate post id bug in global mode
* simplified meaning of EM_Bookings::get_booked_spaces, so it's just booked spaces, not pending. get_available_spaces() should be used for reserved seats instead.
* replaced old default date formats with #_EVENTDATES and #_EVENTTIMES
* fixed some datepicker problems in single ticket mode with start/end date tickets
* removed jQuery datepicker and autocomplete libraries, now using WP's internal scripts instead
* improved the reliability of returned json data in booking form
* fixed categories not editable in front-end,
* added email not sent flag to booking object
* fixed tags not working for slug searches
* fixed dst issues in ical calendars
* added name/slug search fall back for tags search
* added datepicker custom date formatting
* fixed non registered user problem for failed JS submissions
* fixed some rsvp conditional and gcal placeholders
* added jquery-ui-css id to jquery ui css loader to promote compatability with others
* you can now add a custom functions.php file within yourtheme/plugins/events-manager/
* improved title rewriting compatibility
* added hierarchies to category dropdowns
* fixed an object reference error in em-object.php send_mail()
* added jQuery em_booking_success event to document
* fixed tickets not showing start/end dates in admin after editing
* fully booked message now shown rather than closed message
* location description won't take event description in public submission forms
* re-added get_date_format for backwards compatability with overriding templates
* fixed pagination issue in my events page on front-end
* fixed potential security xss exploit in json call links
* fixed default country overriding all country search option on search pages
* fixed pagination issue on my events page on the front-end

= 5.1.6 =
* fixed multiple admin emails not going out
* updated timthumb to v2.8.10
* updated placeholder outputting to avoid overwriting longer variations of similarly named placeholders
* fixed #_BOOKINGTICKETNAME not working
* single location and event pages will still use location-single.php and event-single.php templates
* fixed thumbnail image links on multisite to work with timthumb, thanks BinaryMoon for the tut!
* Reduced sql calls for booking object instantiation. $EM_Booking->custom doesn't exist anymore, and notes must be loaded first with $EM_Booking->get_notes().
* EM_Category->has_events() depreciated, returns false always
* More wp_rewrite tweaks to improve compatability
* fixed 24 hour formatting setting being ignored in timepicker
* fixed bad datepickers in single ticket mode
* fixed locations not being auto-approved if submitted via front-end and event is auto-approved.

= 5.1.5 =
* rewritten booking email function, simpler, less error-prone, overriedable and yet same effect
* fixed tax not showing on booking table totals
* fixed booking objects get_price filters, removed em_booking_get_prices from em-ticket-booking.php in place of em_ticket_booking_get_price
* changed filter name em_tickets_bookings_get_prices to em_tickets_bookings_get_price (bad name according to convention)
* admin email can be sent to multiple emails (comma delimited in settings)
* added booking status message filter
* added custom no events message in events widget
* fixed ical not working in non-permalinks mode ( must have /?ical=1 at end of homt url )
* removed original CSV export link in place of booking table exporter, unless users made a custom template
* BuddyPress private group or normal private event info are now not shown in site activity.
* fixed some php warnings
* fixed certain languages breaking date formats
* added #_EVENTCATEGORIESIMAGES
* added yearly recurrences
* added a cut-off date for bookings, so bookings can take place past event start dates
* fixed some issues with dev mode checks
* fixed booking button and multiple bookings at once bug
* fixed ticket spaces export bug
* fixed rss pubdate format
* improved CSS for booking tables front-end
* edit event locations dropdown shown to users if they can read events (previously only if could edit)
* updated the POT file and Swedish translations
* added #_CONTACTMETA placeholder
* cleaned up the RSS filters so HTML now is allowed in feed

= 5.1.4.3 =
* fixed bp group hidden events not going private
* fixed countries list not working for certain langauges

= 5.1.4.1 =
* fixed wp rewrite issue when assigned events page slug = events slug
* fixed minimum ticket price placheholder problems
* added dev version auto-updater
* improved performance in events with booking widget listings
* improved performance in date range searches (e.g. calendar)
* reverted to using .delegate() instead of jQuery 1.7+ .on() listener for compatibility
* slightly improved mail options logic and layout (plus php mail can send html emails now)
* fixed buddypress conflict if groups component is disabled
* fixed event spaces not overriding displayed values in booking stats pages

= 5.1.4 =
* pinpoint your location with dragable map markers!
* sortable booking table collumns and additional collumns
* seriously improved CSV exporting options with sortable booking collumns
* buddypress private group events now only shown to group members
* added extra ID collumns to event/location/category admin lists
* resend emails, change booking status, and modify booking form information as well as ticket numbers
* any aspect of a booking can now be edited front-end with or without BuddyPress
* customizable cancel booking message and confirmation
* 24 hour option for time pickers possible in admin area
* wp archive options visible now regardless of event/location page options
* further fixes to post thumbnail compatability (hopefully fixed for good!)
* added "Add New" location and recurring events to admin bar
* blank pending status corrected in my bookings pages
* fixed event categories when event is a subsite event shown on main site
* added Swedish datepicker translations
* fixed pagination issue
* updated Danish, Dutch, Swedish languange files
* small fix for duplicating plugins that keep the event id when saving dupe (WPML fix)
* added ticket name field in single ticket mode
* fixed pre 2k events showing in future events list in admin area
* changed calendar linking so it works on all/most themes without JS
* fixed bad translation of JS calendar days in Italian
* hard-coded country names for currently translated languages
* fixed some incompatibilities with Yoast Breadcrumbs
* modified date fixed in ical
* single event ical endpoint detection improved
* fixed reserved spaces miscalculation bug
* invalid taxonomy ids now stripped from searches
* improvements to wp rewrite compatability by registering events and locations in varied order
* fixed recurrence bug for single day events ending next early morning
* depreciated attributes deleted if blank and resaved
* single-event.php now overrides EM regardless of page settings
* front-end deleted events now trashed (if available) rather than deleted
* fixed errors on recurring event creation with bad ticket data
* admin localized js variables (messages) hidden to public
* php and wp mail functions called directly instead of via em/phpmailer
* depreciated js .live() calls and used jQuery 1.7's .on() function, hence min wp 3.3 version

= 5.1.3 =
* added is_past and is_future conditionals
* corrected conditional regex to allow multiple duplicate conditionals and nesting
* fixed some wp_rewrite irregularities due to slug combinations/conflicts
* fixed template format files not overriding


= 5.1.2 =
* fixed auto-delete bug where auto-draft recurring events deletes all events
* fixed recurrence pattern bug
* calendar ajax links are now SE and non JS friendly
* rss link fixed in non-permalinks mode
* added sorting options to my bookings page
* updated de_DE, cs_CZ, dk_DK
* fixed admin email not going out if booking approvals disabled

= 5.1.1 =
* fixed search JS bug, preventing searchings being made

= 5.1 =
* revised booking form template files (future-feature-proofing), simplified booking JS
* added readme files on updating templates with docs and commented further on templates
* improved booking button (allows cancellation)
* added disable email if subject is blank
* bookings can now have a overall spaces cap which override individual ticket spaces.
* improved compatibility with themes not supporting featured images
* improved the booking form ajax table to allow more customizability.
* calendars now will by default direct single event days directly to event page
* added various price related and location placeholders
* fixed post_id not being used in shortcode attributes
* fixed italian datepicker problem
* quick edit now reflects publish status
* GBP sign format corrected (encoding issue)
* fixed admin cross-post search function conflicts
* fixed some public side search issues
* fixed notes placeholders not formatting on some instances (e.g. categories)
* bp activity feed now reports cancelled bookings again
* location form now prefills region when loading prev. location
* fixed event sorting order for same-day events on archives (requires resaving the events).
* made times displayed on post tables use WP general time settings for consistency
* fixed empty tr in calendars
* fixed missing category meta box in MS global mode
* added  em_wp_localize_script filter for hooking into localization of js
* moved anonymous event submissions back into blog settings in MS mode
* added "email exists" customizable message

= 5.0.51 =
* fixed limit issue for calendars when no limit is set
* bookings feedback messages showing properly again
* manage my bookings page link functioning correctly now
* GBP pound sign fixed
* fixed location atts setting box not being checked for placeholders

= 5.0.50 =
* bookings table now within a unified ajax table
* added location attributes
* added currency formatting
* added "all day" format setting for #_EVENTTIMES
* added calendar ordering an limits (full-sized calendar)
* moved 'add new' button next to event lists outside wp-admin
* fixed some redirection issues for some themes
* small date formatting fixes for international english
* ticket bookings now deleted with overall booking
* categories now copied correctly when duplicating events
* tickets can now just be shown to logged out users
* added is_long, not_long, logged_in, not_logged_in, fully_booked and has_spaces conditionals
* fixed a preg issue with date formatting
* booking modifications possible again (ticket numbers)
* pending space counting in emails corrected

= 5.0.42 =
* changed csv booking time to 24 hr format
* fixed EM_URI error
* added logged_in not_logged_in conditional placeholders
* buddypress does not override pages defined in settings > pages > other pages
* default events install properly now
* title seperator problem fixed
* fixed add theme support for thumbnail function not firing early enough

= 5.0.41 =
* fixed fatal error on install (bug since 5.0.4)
* italian js translation added to prevent js error
* CVS title bug fix

= 5.0.4 =
* added installation throttle, to prevent double event imports
* pending events migrate as pending now
* index auto-correction for non-indexed events/locations on save
* wp_rewrite theme compat hack for some offending themes
* guest submission now showing success message
* added events_gcal shortcode for google calendar
* fixed bad BP links in edit event/location tables
* added single event ical endpoint
* added import to google event placeholder
* fixed em_content_pre problem
* group event locations label showing
* empty attributes are saved when previously filled

= 5.0.3 =
* searching from/to without one or another date works as intended
* fixed various old-named properties (and refreshed old properties after object save)
* fixed overriding front-end edit links from within admin area
* fixed #_EDITEVENTURL
* fixed location placeholder output/filter function
* fixed missing php in opening tag of search template

= 5.0.2 =
* fixed new booking id not being saved and passed to filters
* fixed booking placeholders not showing
* single category placeholders working for event formats
* search form has options section with configurable texts

= 5.0.1 =
* js correction preventing maps loading

= 5.0 =
* Events and Locations are now custom post types
* categories are now custom taxonomies
* events can have tags
* new placeholders, conditionals and search attributes
* BuddyPress module rewritten using 1.5 BP_Component api
* list pages split up, assign a page for each list
* extended page formatting options
* new time picker and improved datepicker
* various bugs fixed
* streamlined templates and consolidated varoius list templates
* event and location editors revamped, consolidated and basic CSS added for front-end forms
* Locations are now optional (if chosen)
* more capabilities added for finer permission control
* all day events possible

= 4.305 =
* fixed my-bookings.php template for pagination errors
* fixed duplicate tickets produced in buddypress editor
* removed console.log from js
* fixed owner=0 when admins create ownerless events
* fixed bp activity posting of member links in 1.5

= 4.304 =
* added pubdate to rss feed
* fixed datepickers in single ticket mode not showing saved dates
* fixed bookings view/edit link on second pages of ajax navigator
* added Aruba to countries list
* corrected login error string not being overriden
* removed bogus event settings page in BP (for now)
* attendees list now ommitting unconfirmed bookings
* booking addon pages should now override correctly
* improved buddypress activity notification (supports pro stuses and only one activity if a group event)

= 4.303 =
* fixed PHPMailer conflict when in wp_mail mode
* added html support in emails (if using smtp)
* new event owner now auto-selected
* tickets now duplicated along with event
* blank ticket price validation error fixed
* fixed 'available spaces' bug when only one remaining reserved/pending space is confirmed

= 4.302 =
* group events show proper links to event pages, not edit pages
* PHPMailer updated to v5.2.0
* can_manage bug in MS Mode fixed
* added more actions to ticket forms/tables
* events_calendar shortcode now filtering location search attributes as expected

= 4.301 =
* saving event tickets will now validate properly with meaningful errors
* my-bookings page will show/hide bookings depending on multisite settings (MS only)
* adjusted EM_Events output to use old preg from 4.212 but process custom atts beforehand

= 4.300 =
* more calendar css cleanup
* timepicker now working for public event forms
* user cancellation of bookings now an option in settings
* tax percentage option
* fixed compatability with yoast seo plugin (EM overwrites the title)
* custom attributes breaking when nested in conditional tags fixed
* BP child themes should work properly now without the plugins.php file
* ical DQUOTES problem removed
* events now receive approval confirmation by email
* optional user registration

= 4.212 =
* removed JS entirely from booking form template, still included in footer (overriden templates should remove JS to avoid errors)
* fixed booking status name mixup when approvals are disabled
* added option to enable/disable user booking cancellation
* booking search attribute can use 'user' to show events booked by logged in user

= 4.211 =
* best explained here: http://wordpress.org/support/topic/tagging-new-plugin-version-issues?replies=14#post-2376253

= 4.2 =
* forced update to correct wordpress repository db update notification
* jquery CSS loaded by js if needed for the datepicker
* small css tweaks/fixes to the calendar
* fixed js captcha warning
* fixed warnings
* event_form time entry now using the js time entry script
* double booking now working as expected
* hid some event booking info/links for admin viewers in group event pages
* buddypress my bookings screen fixed

= 4.18 =
* corrected bad HTML in default category page format
* added booking form JS to the wp_footer area for more theme compatability
* added em_admin_paginate filter
* added em_bookings_{action} action for bookings page
* updated placeholder docs
* new tickets ordering setting
* ticket name and descriptions accept images and link html
* fixed bug when using whole year searches in shortcode
* edit bookings link placeholders won't show to users without permission
* booking form will not show when an event is fully booked
* fixed #_BOOKINGTICKETS placeholder showing incorrect space numbers
* cancel booking link re-appearing in my-bookings section
* improved display of group events
* fixed double seperator in title
* scope now being correctly saved in event widgets
* removed unnecesary filtering in email content, causing html entities in plaintext
* updated pot and German/Sweedish translations

= 4.171 =
* tagged 4.17 in the repo as 4.171 due to premature release

= 4.17 =
* delete category/location/event image option added
* added some escaping functions to outputs in calendar
* fixed table duplicate index problem
* added customizable booking form notices
* RSS now passes the W3 Validator
* tested and passed for BuddyPress 1.5
* fixed various warnings
* updated sweedish translation
* categories page title now working in disable title rewrite
* fixed the WP title seperator bug
* updated help placeholder list
* fixed badly named category events placeholders and added backward compatability
* locations_map shortcode now accepts location search attributes
* added option to prevent double bookings for one event
* changed rsvp search attribute into bookings and added backward compatability
* attribute dropdowns don't show a 'no value' since the first option should be default

= 4.16 =
* image thumbnails added
* phone and further email booking problems fixed
* added ical feed settings
* added category events list formatting options (previously taken from location settings)
* split up booking form template file into template parts
* single ticket events with only one space (due to limits or availability) won't show selection box
* added some widget filters for search arguments
* booking notes bug in 4.15 fixed
* calendar widget fix for eventful today links not showing
* event, location and category slugs can now be changed via the wp-config.php file

= 4.15 =
* single events can now be converted to recurring
* booking approval issue fixed
* group Event activities now included group wall
* fixed recurrence bug, where rescheduled past events aren't deleted
* dbem_phone fix
* initial password will now allow users to log in (bug in 4.14 only)
* fixed google map balloon centering and IE8 incompatability
* added the UK nations
* changed usernames shown in booking areas to full name (if available)

= 4.14 =
* Admin-editable bookings/tickets
* BP menu items do not show if a user doesn't have the relevant capabilities
* Member/Guest submit forms improved (still in beta due to pending template changes, but functionality is there)
* Updated the help pages with new placeholders
* Fixed a register-before-booking bug
* CSV event bookings export now an overridable template
* Cleaned up some ical formatting problems
* Countries list updated
* Fixed datepicker js issue in tickete
* Calendar headings have mb_ support for multi-byte characters.
* Various smaller bugfixes and warning removals

= 4.13 =
* events now allow 10 digit booking prices, if you have an event that costs more than this, call me :)
* fixed confirmation emails not firing from paid bookings
* fixed #_BOOKEDSPACES not including the confirmed booking in the total
* event details aren't copied by mistake to a location
* fixed booking notes
* added em_event_owner_dropdown_users filter
* added category selection in calendar widget

= 4.12 =
* fixed JS problem in admin area when WPLANG is set
* fixed confirmation email bug for pro users
* added belize to countries list

= 4.11 =
* fixed conflict of default category/event widget
* added/fixed some gettext domains
* removed some php warnings
* corrected filter misspelling of em_booking_get_prices to em_booking_get_price
* added a few new filters
* fixed initial notification emails not going out to event contact on pro payments

= 4.1 =
* nothing, just trying to get WP to recognize a new update

= 4.0.9 =
* added various google/user translated languages and updated pot file
* fixed various gettext domain errors
* search form defaults and behaviour fixed
* added dates to buddypress group events template
* improved the google maps js insertion (updated with google's new recommended code)
* no pending approvals when switching from auto-approval to approval mode
* added new "within month" scope
* various other nuances fixed

= 4.0.83 =
* added option to remove booking login form
* fixed login issues when guest bookings is disabled
* registration email is optional
* added option to show ticket table even in single ticket mode
* fixed search defaulting to default country when all countries selected
* fixed ical timezone issue
* corrected some typos
* added Jamaica and Bolivia to countries list
* added guest event and member submissions with [event_form]
* fixed location search ownership issue
* added new template tags for page type detection
* fixed some ticket display issues
* added search filter in event
* updated the docs (although needs a thourough revision once more)

= 4.0.82 =
* fixed bookings missing in non-approval mode

= 4.0.81 =
* fixed events not editing due to new location js
* fixed pro notification
* fixed calendar ajax year switching issue

= 4.0.8 =
* just made settings page expanded
* added some update notifications for pro user

= 4.0.7 =
* minium WP version is now 3.1
* prevented JS loading in non-EM admin screens again
* updated jQuery ui objects to use the 1.8.x core
* removed dependency on ajaxForm javascript
* new booking ticket placeholders for emails
* images now saving in recurrence mode
* images now saving in multisite global/local modes
* LOADS of bugfixes in buddypress
* removed user list showing for normal location editors
* cleaning up the attributes e.g. apostrophes
* images kept when detaching recurrent event
* location and categories now have slug choice and get properly cleaned
* added and corrected some countries (Syria, Peru, corrected Panama code)
* added option to disable registration emails going out
* tickets now accept digits, e.g $1.50
* location form in event more intuitive when using previous locations
* location form and map degrade more gracefully with small screens now.
* removed various php warnings

= 4.0.6 =
* removed more php warnings
* fixed recurrence issue
* improved default values of country/state/region in search forms
* fixed ticketing issues with recurrences
* added workaround for IIS users with 404 issues
* fixed global maps not working in some instances
* made notice collisions when saving in sessions less likely
* fixed MS recurrence issue

= 4.0.5 =
* removed various php warnings
* added explanation for incorrect recurrences
* fixed RSS title/desc not using html entities
* fixed event widget scope problem
* MultiSite superadmins can manage all

= 4.0.4 =
* Fixed the 404 problem
* added Peru to countries, fixed broken accented characters in country lists
* added ticket description to booking form.
* reordered the search form to make more sense

= 4.0.3 =
* Fixed the update method for good now
* fixed booking pending email discrepency
* other minor booking bugs
* ics file formatting fix
* buddypress group events working as expected again
* booking form and rsvps showing fixed
* fixes to search form

= 4.0.2 =
* updated default formats and event options on install
* fixed title meta location problem
* added town/country/state/region search attributes for locations
* added extra linking formatting for calendars (minor tweak for bug report)
* datepicker locale now matches WPLANG setting (if applicable)
* fixed recurrence and category issues
* changed version update mechanism

= 4.0.1 =
* fixed recurrence slug and creation issue
* fixed created/modified dates which weren't always updating
* added bvi and greenland to countries list
* got rid of known warnings to date
* fixed various issues with the search form ajax and loaded values
* added extra location info to columns
* location placeholders fixed
* attribute now working properly as intended

= 4.0 =
* see http://wp-events-plugin.com/news/events-manager-4-0-released/

= 3.0.97 =
* Restoring stable version

= 3.0.96 =
* fixed js hook bug, you must now bind your function to the document's custom em_maps_locations_hook and em_maps_location_hook event triggers using jquery
* fixed tinymce bug with linking which cropped up in 3.1 due to new WP linking window.
* event_date_modified now properly updated

= 3.0.95 =
* removed some php warnings
* fixed blank widget defaults (resave current widgets to replace blanks with defaults)
* fixed calendar bug, where old events aren't being shown
* fixed calendar css for events on the current day
* unapproval is now reject if pre-approvals are turned off
* delete bookings working again
* booking emails working as expected without pre-approvals
* added js hook for maps
* fixed qtranslate conflict, delayed mo file loading for better compatability with wpml

= 3.0.94 =
* Fixed missing events, locations etc. due to permissions
* Fixed location widget bug
* fixed broken global map js

= 3.0.93 =
* Fixed bug with ownership and widgets
* Resolved 2.9 incompatibility
* Fixed rss ownership bug
* Fixed calendar bug where pre/post dates don't show events
* Fixed calendar, now showing today correctly
* Categories blank page fix
* fixed page nav conflicts with role scoper
* added shortcut to manage bookings on event list


= 3.0.92 =
* Fixed permission issue
* Fixed category not saving
* Fixed location saving issue


= 3.0.91 =
* Documentation finally up to date now!
* widget bug fixed
* added event permissions, so users can manage their own events/locations/categories
* improved event booking UI and management tools
* export CSV of bookings
* booking approvals added
* bookings can have individual notes
* calendar widget shows selected month if clicked on
* custom attributes field, for atts that don't need to be in a template (e.g. pdf file url)
* time limit for main events list and events widget (e.g. show events that occur within x months)
* default location
* default category
* added extra validation so event start date/times can't be after end date/time
* calendar navigation will pass on all arguments for following month (e.g. category, etc)
* small map balloon fix for some rare js conflicts
* fixed location gui editor

= 3.0.9 =
* Fixed small calendar discrepancies
* added event and location single shortcodes
* shortcodes now accept html within format attribute or within the shortcode tags [like]<p>this</p>[/like]
* fixed pagination functionality (or lack thereof) in shortcodes
* improved user experience when navigating/editing events in admin area
* added #_CONTACTAVATAR placeholder - avatar for contact person
* ajax loading spinner graphic added to calendars
* internal wp_mail support added
* added "all events" link to events widget
* fixed date translations
* cleaned up the settings page documentation and added placeholder docs on help page.
* fixed "enable notification emails" option in settings
* added admin email option that would be send every event booking to admin

= 3.0.81 =
* Fixed pagination bugs
* Global locations map won't show locations with 0-0 coords
* Fixed bug in recurrence description
* Removed most (if not all) php warnings
* Fixed booked seats calculation errors
* Removed dependence on php calendar

= 3.0.8 =
* Event lists now have pagination links for both admin and public areas!
* Fixed time zone issue with calendars, now taking time from WP settings, not server
* Added option to show long events if showing a calendar of events page.
* Multiple maps on one page will now show up.
* Modified styling of map balloons to not use #content (if you modded your theme, look at the CSS to override).
* Media uploads in GUI now working as expected
* Orderby ordering in events widget

= 3.0.7 =
* Renaming a few functions/shortcodes for consistency
* Fixing #_LOCATIONPAGEURL issue
* Fixed ordering issue again
* New template tags
* First filter

= 3.0.6 =
* Added revised German translation
* Fixed ordering issue
* Fixed old template tag attributes not being read
* Changed map balloon wrapper id to class

= 3.0.5 =
* Fixed 12pm bug
* Re-added #_LOCATIONPAGEURL (although officially it's depreciated)
* Added default order by settings in options page
* Added default event list limits in options page
* Added orderby attribute for shortcode
* scope attribute now also allows searching between dates, e.g. "2010-01-01,2010-01-31"
* Fixed booking email reporting bug

= 3.0.4 =
* Title rewriting workaround for themes where main menus are broken on events pages
* Added option to show lists on calendar days regardless of whether there is only one event on that day.
* added Spanish translation
* fixed rsvp deletion issue
* fixed potential phpmailer conflicts
* CSS issue with maps fixed
* optimized placeholders, adding new standard placeholders

= 3.0.3 =
* RSS Showing up again
* Fixed some reported fatal errors
* Added locations widget
* Adding location widget
* optimizing EM_Locations and removing redundant code across objects
* fixed locations_map shortcode attributes
* harmonized search attributes for locations and events
* rewrote recurrence code from scratch
* got rid of most php notices

= 3.0.2 =
* Recruccence bugfix

= 3.0.1 =
* Fixed spelling typos
* Fixed warnings for bad location image uploads (e.g. too big etc.)
* Fixed error for #_EXCERPT not showing

= 3.0 =
* Refactored all the underlying architecture, to make it object oriented. Now classes and templates are separate.
* Merged the events and recurrences tables
* Tables migration from dbem to em (to provide a fallback in case the previous merge goes wrong)
* Bugfix: 127 limit increased (got rid of tinyint types)
* Bugfix: fixed all major php bugs preventing the use with Wordpress 3.0
* Bugfix: fixed all major js bugs preventing the use with Wordpress 3.0
* Restyling of the Settings page
* Added a setting to revert to 2.2
* optimizing EM_Locations and removing redundant code across objects

For changelog of 2.x and lower, see the readme.txt file of version 2.2.2