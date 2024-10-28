=== bbPress Advanced Statistics ===
Contributors: GeekServe
Donate link: http://thegeek.info
Tags: bbpress, statistics, users, online, users online, forum statistics
Requires at least: 3.9
Requires PHP: 5.3.0
Tested up to: 4.8.1
Stable tag: 1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

phpBB/vBulletin-esque statistics for your bbPress Forum - show off how active your forum is with additional statistics and user tracking!

== Description ==

> **This plugin has been tested with bbPress 2.6**
> This plugin has been tested and works with the upcoming version of bbPress, version 2.6

The statistical functionality within core bbPress is limited, with this plugin, you can achieve phpBB / vBulletin-esque statistics for your bbPress Forum, you can opt to use the widget provided with the plugin, or, you can use the options provided within the customisation tab of the plugin.

= What does this plugin provide? =

 * Members currently active
 * Members active within a set period of time
 * **NEW!** Guests current active
 * Listed users, with links to their profile pages
 * Record number of active members tracking
 * Ability to restrict what shortcodes/bbcodes are used within posts
 * Multisite Compatible

= Rate us & Submit your website using our plugin! =

There are over 800 websites actively using bbPress Advanced Statistics, and I want to see your site! If you would like it to be featured below, pop a post in the forum showing off your site or pop us a rating!

 * [Nintenderos](http://www.nintenderos.com/foro-nintendo/) - A nintendo fansite using bbPress that is very active
 * [RockstarWire](http://rockstarwire.net) - Rockstar Games Fansite
 * [You Talk Trash](http://youtalktrash.com/forum/) - A popular forum for YouTuber Trash Talking

*The websites above were found using some google trickery. To make my life easier, please make me aware of your site when using the plugin if you'd like to be featured!*

== Installation ==

Installing "bbPress Advanced Statistics" can be done either by searching for "bbPress Advanced Statistics" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
2. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

From there, you should now have an option under "Forums" called "bbPress Advanced Statistics". Here, you can set various important parts of the plugin, such as the locations where the statistics are displayed.

Alternatively, you can enable a widget that will display the statistics for you in any sidebars you have setup.

== Screenshots ==

1. The plugin in action, screen depicts the plugin in use on a website
2. Standard Options available within the WordPress Admin Page
3. Some customisation options, with more to come in pending updates
4. Additional customisation with the extras tab

== Frequently Asked Questions ==

= What tags are available to customise with? =
With Version 1.5, I have implemented more customisation within the plugin which means there is now additional tags to utilise. These tags will work in any of the text strings available to you, so ensure you are using the correct one!

- `%MINS%` - The amount of minutes to pass before a user is assumed offline
- `%HOURS%` - The number of hours to pass before a user is not displayed within the plugin
- `%COUNT_ACTIVE_USERS%` - A count of all currently active, logged in users
- `%COUNT_ALL_USERS%` - A count of all users active within the hours timeframe
- `%COUNT_ALL_GUSERS%` - A count of all guest users active within the hours timeframe
- `%COUNT_ACTIVE_GUSERS%` - A count of all guest users active within the mins timeframe
- `%USER_USERS%` - Conditional text (user or users) based on amount of users active within the mins timeframe
- `%ALL_USER_USERS%` - Conditional text (user or users) based on amount of users active within the hours timeframe
- `%GUEST_GUESTS%` - Conditional text (guest or guests) based on amount of guests active within the mins timeframe
- `%ALL_GUEST_GUESTS%` - Conditional text (guest or guests) based on amount of guests active within the hours timeframe
- `%USER_RECORD%` - Record number of users online
- `%USER_RECORD_DATE%` - Date of the record, uses the date format set within WordPress
- `%USER_RECORD_TIME%` - Time of the record, uses the time format set within WordPress
- `%LATEST_USER%` - Link to the latest user to join your forum

Any of those can be used across all of the customisation boxes provided within this plugin. 

= Can I apply any style to this plugin? =
Yes, you can - I am working on creating some documentation as WordPress have changed the plugin repository layout. For the time being, notes can be found [here](http://thegeek.info/bbpas-notes.html)

= Are there any hooks or filters for me to use? =
I am currently working on the documentation for CSS styling, hooks and filters. Until then, this is a list of all hooks/filters currently available.

**Filters**
- bbpas_replacement_tags
- bbpas_section_bbpress_stats
- bbpas_section_rolekey
- bbpas_bbpress_stats

**Actions**
- bbpas_init
- bbpas_loaded
- bbpas_section_builder
- bbpas_after_stats_hook
- bbpas_before_stats_hook

Hopefully they are self-explanatory, if you get stuck or have some suggestions for further action/filters, please do let me know in the forum

= What are the requirements for bbPress Advanced Statistics? =
The current requirements are as follows:

- PHP 5.9.0 or above
- bbPress installed

If either of those are not met, bbPress Advanced Statistics will not work on your site.

= Does this work for previously logged in users? =
Unfortunately, WordPress nor bbPress provide a "user is online" functionality out of the box - we had to add that ourselves, thus - data will only be displayed in this plugin after it has been installed as users log in to your site. 

= Are there any settings I can change? =
Plenty of options are waiting to be tinkered with! Under the "Forums" menu item, you should see "bbPress Advanced Statistics"

= Does this plugin have a widget? =
Yes, it does! Simply activate it as you would with any other widget.

= How do I create / submit a Translation? =
You have two options, first and foremost - I am using GlotPress primarily for this. Reason being, it is free and makes crowdsourced-translations a quick and pain-free process.
I will not be providing any translated files in the plugin itself, instead - GlotPress will automatically generate the files that have reached the threshold.

If you'd like to contribute to the translation of this plugin, please [click here](https://translate.wordpress.org/projects/wp-plugins/bbpress-improved-statistics-users-online)

With that being said, users are still able to add translations they have made manually. See below for further details.

We have made it super easy to create translations for this plugin, you simply need to grab the original
POT file (found within the plugin directory) and create translation files based off of that.

You can use [Poedit](https://poedit.net/) to create your translation.

 * [WPLang Tutorial](http://wplang.org/translate-theme-plugin/)
 * [ZaneMatthew](http://zanematthew.com/translate-a-wordpress-plugin-using-poedit/)
 
> **Please Note:** The filename **must be** correct in order for your Translation to work. The naming convention is as follows:
>
>
> * `bbpress-advanced-statistics-LOCALE.mo`
> * `bbpress-advanced-statistics-LOCALE.po`
>
> Where LOCALE is the code for your language, e.g German would be bbpress-advanced-statistics-de_DE.mo
>
> You can find the correct code for your locale [here](https://make.wordpress.org/polyglots/teams/) 

Once you are happy with your Translation, drop it into `/wp-content/languages/bbpress-advanced-statistics/` and it should be instantly activated.

> **Don't forget:** The filename **has to be identical** to that WordPress expects, else your language pack will not be used! If you need some assistance with this, post in our support forums.

Alternatively, you can [help translate in general](https://translate.wordpress.org/projects/wp-plugins/bbpress-improved-statistics-users-online) (This is a much better option and helps us build up the translation library for all locales!)

== Changelog ==

> The changes listed here are organised as Enhance, Bug Fix and Feature. 
>
> In each release, I try to fit in as many Enhancements and Features as I can,
> Bug Fixes are prioritised and are usually the reason a new version is developed.
>
> * **Enhancements** are simply code adjustments to make existing features better
> * **Features** are brand new additions to the plugin
> * **Bug Fixes** are usually issues reported by users and fixed

= 1.5 - 17th September, 2017 =
 * **Feature:** Guests can now be tracked, this is enabled by default [Please note: this is not retroactive]
 * **Feature:** Lots of new tags, such as pluralised text and number of guests online
 * **Feature:** Additional customisable strings added, giving you more control of the plugin
 * **Enhance:** Plugin displays persistent admin message when installed without reaching required dependencies
 * **Enhance:** Complete overhaul of the statistics widget display
 * **Enhance:** Removed before/after hook options, please use the provided actions instead
 * **Enhance:** `shortcode_activity()` is deprecated, notice is displayed if used. Update your code to use `build_html()` instead.
 * **Enhance:** Various code modifications to speed up the plugin
 * **Bug Fix:** Upgrade function ensures the correct prefix is used within a multi-site environment
 * **Bug Fix:** Widget heading index error

= 1.4.5 - 6th August, 2017 =
 * **Enhance:** Updated respository page and supported version of WordPress
 * **Bug Fix:** PHP Error (undefined index) when no users have been inactive corrected

= 1.4.4.1 - 2nd January, 2017 =
 * **Enhance:** Small improvements to uninstall procedure, added some comments
 * **Enhance:** Plugin now actively checks to see if bbPress is installed - will deactivate if it isn't 
 * **Bug Fix:** Support for periods in the posts/topics and user counts

= 1.4.4 - 1st January, 2017 =
 * **Enhance:** Plugin Information page now displays more information for debugging purposes
 * **Enhance:** Worked on cleaning up some of the code, sorting out comments and so on
 * **Bug Fix:** Corrected an issue with multisites where the parent db prefix was being selected all the time when getting online users
 * **Bug Fix:** Corrected an issue with multisites db prefix when the plugin is running upgrades

= 1.4.3 - 26th December, 2016 =
 * **Feature:** WordPress Multisite/Networks now fully supported
 * **Feature:** 'Plugin Information' tab added, this page will be expanded overtime to assist with debugging.
 * **Enhance:** Admin-related files will only load when in an admin page
 * **Enhance:** Data will now be automatically uninstalled when the plugin is deleted, the user is now explicitly required to check the option to prevent data being deleted
 * **Enhance:** Uninstall procedure is generally much more safe now
 * **Bug Fix:** An issue in which users were unable to uninstall the plugin should now be resolved
 * **Bug Fix:** Corrected an issue in which the statistics were incorrect for boards with more than 10000 posts. Thanks to [tronix-ex](https://wordpress.org/support/users/tronix-ex/) for assistance with this.
 
= 1.4.2 - 18th August, 2016 =
 * **Bug Fix:** Users Currently Online count updated

= 1.4.1 - 18th August, 2016 =
 * **Bug Fix:** Corrected installation issues
 * **Bug Fix:** Corrected upgrade issue

= 1.4 - 17th August, 2016 =
 * Full WordPress 4.6 Support!
 * **Feature:** Added 'Statistics to Display' option, allowing the user to define which online statistics should be displayed
 * **Feature:** Added 'User Display Limit' option, allowing the user to define how many users should be displayed before stopping (ideal for larger boards)
 * **Feature:** Added 'User Display Limit Page' option, allowing the user to define a page to be displayed should the count of users go over the limit you set
 * **Feature:** New Information Page added to the Admin Screen. This displays useful information for debugging purposes
 * **Enhance:** Updated the CSS for the admin side, cleared up some bad formatting.
 * **Enhance:** Moved Plugin options around
 * **Enhance:** Old Database Structure (prior to version 1.3) has now been removed
 * **Enhance:** Added default whitelisted codes to support popular BBCode Plugins (e.g GD bbPress Tools)
 * **Enhance:** More styling options added to the statistics, see Other Notes for further info
 * **Enhance:** Plugin Upgrade has been given some love!
 * **Enhance:** Assets folder cleaned up, removed unnecessary files
 * **Enhance:** Added translation support for widget
 * **Enhance:** Admin Data Callback added for general validation of user input
 * **Enhance:** PHP Notices cleared up

= 1.3.13 - 7th May, 2016 =
 * **Feature:** Addition of Usergroup Key. This supports custom groups, and will modify the colour based on your stylesheet rules
 * **Enhance:** Spaces are supported in user groups for CSS styling (replaced with hyphens)
 * **Enhance:** Updated the way bbPress stats are collected
 * **Enhance:** Hyperlink IDs for each user moved into the main element
 * **Enhance:** Various general code improvements

= 1.3.12 - 1st May, 2016 =
 * **Bug Fix:** Corrected Uninstall procedure table name

= 1.3.11 - 26th April, 2016 =
 * **Bug Fix:** Made `userid` a unique column to prevent duplicates from appearing
 * **Bug Fix:** More fixes for the upgrade procedure

= 1.3.1 - 23rd April, 2016 =
 * **Bug Fix:** Corrected database issues that occurred after upgrading
 * **Bug Fix:** Fixed installation for sites using different db prefixes
 * **Bug Fix:** Corrected PHP Notice in admin page

= 1.3.0 - 22nd April, 2016 =
 * **Feature:** Addition of a usable widget! With this being said, shortcodes in widgets is no longer enabled by default for **new installations**. See 'Other Notes' for further details
 * **Feature:** 'Most Users Ever Online' has been added to the plugin! **This does not take previous data into account, it takes effect from upgrade/plugin installation**
 * **Enhance:** Plugin Installation has been drastically improved.
 * **Enhance:** Plugin Deactivation has been improved, now features option to delete all options and database upon uninstallation. Further changes in future updates
 * **Enhance:** User Activity is now stored in a custom DB table, some performance increase should be noted on larger sites. Data has been migrated, and the old structure will be removed in a future update.
 * **Enhance:** Files removed that are no longer required for the plugin (assets/js)
 * **Enhance:** Further Translation Support added for Admin Pages (radio and checkboxes), removed obsolete/pointless translations
 * **Bug Fix:** Resolved an issue where extra options would be active regardless of being enabled

= 1.2.2 - 5th April, 2016 =
 * **Enhance:** CSS updated, more classes added for easier theme compatibility!
 * **Enhance:** Additional CSS added by default to the provided CSS file
 * **Enhance:** Added ability to disable CSS file
 * **Bug Fix:** Few languages fixes throughout the plugin

= 1.2.1 - 3rd April, 2016 =
 * **Bug Fix:** 'User is Online' persistently appearing in user profile. Thanks to [Anticosti](https://wordpress.org/support/topic/persistant-user-is-online-in-profile-page) 
 * **Enhance:** Updated language text for 'Welcome to our newest member' as it was causing issues. [Thanks to cooljojo](https://wordpress.org/support/topic/about-languages)

= 1.2 - 24th March, 2016 =
 * **Feature:** Extras tab has been added to host additional functionality for the plugin
 * **Feature:** BBCode/Shortcode Whitelist added - you can now set which BBCodes/Shortcodes to exclusively enable
 * **Feature:** Shortcodes within widgets are no longer enabled by default, users can now switch this on or off!
 * **Enhance:** Full glotpress support! Translating is much easier, [Help translate](https://translate.wordpress.org/projects/wp-plugins/bbpress-improved-statistics-users-online) 
 * **Enhance:** Admin Page API updated to include new pages
 * **Enhance:** Better Code Commenting for navigation around the plugin
 * **Bug Fix:** Resolved PHP Notices/Errors around the plugin, thanks to [ripteh1337 for reporting!](https://wordpress.org/support/topic/php-error-169)
 * **Bug Fix:** Saving settings on a non-existant tab wipes no longer wipes your settings

= 1.1.3 - 9th January 2016 =
 * **Feature:** Ability to choose between Usernames or Display names within the settings, [requested by IILLC](https://wordpress.org/support/topic/feature-request-display-name-instead-of-login-name)  
 * **Bug Fix:** Array clearing within a loop (rookie mistake!)

= 1.1.2 - 10th October 2015 =
 * **Enhance:** Formatting clear up within some files
 * **Enhance:** Some code clean-up and identified other areas for improvement in future releases
 * **Bug Fix:** Statistics now appear within the Topics Index (/topics) - [thanks to UVScott for the bug report!](https://wordpress.org/support/topic/bug-report-stats-not-displaying-on-topics-index?)

= 1.1.1 - 12th July, 2015 =
 * **Feature:** Threads and Posts can now be combined, bbPress Statistics do not count the first post of a thread as a post, this can be toggled within the settings.
 * **Enhance:** Language packs can be overrided now, any packs loaded in /wp-content/languages/bbpress-advanced-statistics/ will override those packaged as part of the plugin
 * **Enhance:** Translation String added for username hover over, "ago"
 * **Enhance:** Minor code clean up & bug fixes
 * **Bug Fix:** Removal of duplicate "bbPress Statistics" option

= 1.1.0 - 4th July, 2015 =
 * **Feature:** WordPress "textdomain" language files are now supported, new translations can be added into the /lang/ folder!
 * **Feature:** Hover text added to users within the Forum Statistics section
 * **Enhance:** Added additional localisation strings

= 1.0.3 - 25th May, 2015 =
 * **Feature:** Count parameters: %COUNT_ACTIVE_USERS% and %COUNT_ALL_USERS% to display count of users active recently & inactive
 * **Feature:** Minutes parameter: %MINS% to display the option "User Active Time" value
 * **Enhance:** No longer grabbing unnecessary data from the database
 * **Enhance:** Removed unused code and variables, fixed up some incorrect code comments
 * **Bug Fix:** Time logic within the Currently Active Users portion fixed, now correctly displays the currently active users regardless of what option is set
 * **Bug Fix:** User Active Time option not working - incorrect variable used within the options page, options will require a resave
 * **Bug Fix:** Default options are now saved when the user first installs the plugin

= 1.0.2.1 - 23rd May, 2015 =
 * **Bug Fix:** PHP error when installing v1.0.2 (sorry about that)
 * **Bug Fix:** No longer time-travelling the release!

= 1.0.2 - 22nd May, 2015 =
 * **Feature:** New options added to display the statistics within bbPress without widgets, [see here](https://wordpress.org/support/topic/in-forum-display) 
 * **Enhance:** Updated the way options are saved in the Database and removed some redundant code
 * **Bug Fix:** Fixed "an error has occurred" message when no users were online / active within the past 24 hours
 * **Bug Fix:** Fixed a PHP warning when no options were set for checkboxes

= 1.0.1.1 - 12th May, 2015 =
 * **Feature:** Addition of shortcode activation with HTML widget
 * **Enhance:** SVN clean up, moving screenshots to the assets folder
 * **Bug Fix:** Dependency error for PHP, [see here](https://wordpress.org/support/topic/error-message-421)  

= 1.0.1 - 11th May, 2015 =
 * **Bug Fix:** Logic bug with users last online, it now correctly works out how many users were online in the past x hours

= 1.0.0 - 10th May, 2015 =
* Initial release

== Upgrade Notice ==

= 1.5 =
Guests are now included in your statistics! Lots of bug fixes, big rewrite. No database migrations required.