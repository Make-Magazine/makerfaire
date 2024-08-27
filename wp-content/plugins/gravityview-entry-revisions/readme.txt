=== GravityRevisions ===
Tags: gravitykit, gravityview, gravity forms, revisions
Requires at least: 5.1
Tested up to: 6.6
Contributors: The GravityKit Team
License: GPL 2
Requires PHP: 7.2.0
Stable Tag: 1.4

Track changes to Gravity Forms entries and forms and restore previous revisions.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
2. Activate the plugin
3. Edit entries in Gravity Forms as normal
4. You'll see a "Revisions" meta box on the entries page. Click the link next to the revision to compare versions, and restore.

== Changelog ==

= 1.4 on July 18, 2024 =

This release adds support for Form Revisions! Track, manage, and restore prior versions of forms—[read the announcement](https://www.gravitykit.com/gravity-forms-version-management/).

#### 🚀 Added

- Form revision support! [Learn how to enable and use Form Revisions](https://docs.gravitykit.com/article/481-track-changes-to-a-gravity-forms-form)

#### 🔧 Updated

* [Foundation](https://www.gravitykit.com/foundation/) version 1.2.15
    - Added shortcut (Alt or Cmd + /) to open the search bar on the Manage Your Kit page
    - Improved speed by caching of the `Helpers\Core::get_plugins()` method response
    - Added a newsletter signup form to the Manage Your Kit page
    - Fixed some product updates not working form the Manage Your Kit screen

= 1.3.0 on April 24, 2024 =

#### 🚀 Added
* Automatic revision creation for entries updated via the Gravity Forms API.

#### 🔧 Updated
* [Foundation](https://www.gravitykit.com/foundation/) and [TrustedLogin](https://www.trustedlogin.com/) to versions 1.2.12 and 1.7.0, respectively.
  - Fixed a bug that hid third-party plugin updates on the Plugins and Updates pages.
  - Resolved a dependency management issue that incorrectly prompted for a Gravity Forms update before activating, installing, or updating GravityKit products.
  - GravityKit product updates are now showing on the Plugins page.
  - Database options that are no longer used are now automatically removed.
  - Transients are no longer autoloaded and work correctly when using object cache plugins.
  - GravityKit products that are already installed can now be activated without a valid license.
  - Fixed PHP warning messages that appeared when deactivating the last active product with Foundation installed.
  - Fixed a JavaScript warning that occurred when deactivating license keys and when viewing products without the necessary permissions.
  - Resolved PHP warning messages on the Plugins page.

= 1.2.11 on December 12, 2023 =

* Updated: The single GravityRevisions setting has moved from Gravity Forms > Settings > Entry Revisions to GravityKit > Settings > GravityEdit
    - This adjustment reflects the setting's exclusive connection to the functionality of the GravityEdit plugin
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.2.6

= 1.2.10 on September 7, 2023 =

* Improved: Support for RTL languages
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.2.2

= 1.2.9 on July 12, 2023 =

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.1.1

= 1.2.8 on June 13, 2023 =

* Fixed: Incompatibility with some plugins/themes that use Laravel components
* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.12

= 1.2.7 on February 20, 2023 =

**Note: GravityRevisions now requires PHP 7.2 or newer**

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.9

= 1.2.6 on January 5, 2023 =

* Updated: [Foundation](https://www.gravitykit.com/foundation/) to version 1.0.8

= 1.2.5 on December 21, 2022 =

* Fixed: PHP 8.1 notices
* Fixed: Fatal error on some hosts due to a conflict with one of the plugin dependencies (psr/log)

= 1.2.4 on December 1, 2022 =

* Fixed: It was not possible to remove an expired license key

= 1.2.3 on November 30, 2022 =

* Fixed: Potential fatal error when Gravity Forms is inactive
* Fixed: "Undefined index" PHP notice

= 1.2.2 on November 14, 2022 =

* Fixed: Fatal error when loading plugin translations
* Fixed: Slow loading times on some hosts
* Fixed: Plugin failing to install on some hosts

= 1.2.1 on October 31, 2022 =

* Fixed: `{entry_revision_diff}` merge tag not working
* Fixed: Plugin was not appearing in the "Add-Ons" section of the Gravity Forms System Status page

= 1.2.0.2 on October 20, 2022 =

* Fixed: Potential error when the plugin tries to log an unsuccessful operation

= 1.2.0.1 on October 19, 2022 =

* Fixed: Error when trying to activate license keys

= 1.2 on October 19, 2022 =

* [GravityView (the company) is now GravityKit](https://www.gravitykit.com/rebrand/) and this plugin is now called GravityRevisions!
* Added: New WordPress admin menu where you can now centrally manage all your GravityKit product licenses and settings ([learn more about the new GravityKit menu](https://www.gravitykit.com/foundation/))
    - Go to the WordPress sidebar and check out the GravityKit menu!
    - We have automatically migrated your existing Entry Revisions license, which was previously entered in the Gravity Forms settings page
    - Request support using the "Grant Support Access" menu item
* Fixed: Notifications were not being sent when creating a revision using GravityEdit

__Developer Updates:__

* Added: Revision entry data is being passed along to `GFAPI::send_notifications`
* Improved: Prevent extra query when processing entry revision merge tags

= 1.1 on January 26, 2022 =

* Added: Entry Revisions now tracks edits made using our [GravityEdit add-on](https://www.gravitykit.com/products/inline-edit/). Tracking revisions is enabled by default. You can change the default setting and override the setting per-form. [Learn how to change these settings.](https://docs.gravitykit.com/article/777-inline-edit-revisions). Requires Gravity Forms 2.5 or newer.

= 1.0.4 on July 22, 2021 =

* Fixed: License field missing when running Gravity Forms 2.5
* Fixed: Column with current revision values was not showing in WP 5.7 and newer

= 1.0.3 on February 19, 2020 =

* Fixed: Error when Gravity Forms is deactivated
* Fixed: Linking to entry revisions from GravityView and [Gravity Forms Calendar](https://www.gravitykit.com/products/calendar/)
* Fixed: PHP warning in Gravity Forms Entry screen

__Developer Updates:__

* Added: `gravityview/entry-revisions/add-revision` Whether to add revisions for the entry

= 1.0.2 on February 6, 2019 =

* Fixed: Minor PHP warnings
* Updated: Translations!
    - Chinese by Edi Weigh
    - Turkish by Süha Karalar
    - Russian by Viktor S
    - Polish by Dariusz Zielonka

__Developer Updates:__

* Added: The `gravityview/entry-revisions/send-notifications` filter, which supplies the changed fields array ([see filter documentation](https://docs.gravitykit.com/article/483-entry-revisions-hooks#gravityview-entry-revisions-send-notifications))

= 1.0.1 on September 17, 2018 =

* Fixed: `{all_fields}` Merge Tag was being replaced with "This entry has no revisions."
* Updated: Polish, Russian, and Turkish (Thank you, @dariusz.zielonka, @awsswa59, and @suhakaralar!)
* Improved: Added an error message when trying to activate a GravityView license key that does not have access to Entry Revisions

= 1.0 =

* Launch!
