=== wp-tutorial-maker ===
Contributors: barzik
Tags: tutorial, category,
Requires at least: 3.5.1
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP tutorial maker will make your selected categories into tutorial style categories.
The posts inside the categories will include previous and next links to other tutorials articles.

== Description ==

WP Tutorial Maker allow you to select some categories in your site and makes them into tutorial categories.

1. The category will be ordered by the creation date in ascending order.
2. You can choose if each post in the category will have navigational to the next post and to the last post.
You can also choose the position of those links - before or after the content.
3. You can choose to add a link back to the category and choose the text of the link and a text that will appear next to
the link.

After activating WP Tutorial Maker, you can go to any category in your WordPress and designate it as 'Tutorial Category'.

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'wp-tutorial-maker'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `wp-tutorial-maker.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `wp-tutorial-maker.zip`
2. Extract the `wp-tutorial-maker` directory to your computer
3. Upload the `wp-tutorial-maker` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard



== Screenshots ==

1. Edit\Add new Category
2. All of the options that available when choosing to activate the tutorial on single category.

== Changelog ==

= 1.5 =
* Fixing security breaches in admin interface
* Adding a lot more automated tests
* Adding test coverage report

= 1.4 =
* Adding TravisCi support

= 1.3 =
* Fixed bad link back to tutorial category
* fixed i18n po-mo
* CSS improvements

= 1.2 =
* Major issue fix - Insert deactivate clearing function

= 1.1 =
* Major issue fix - Removing single_activate calls and change plugin description.

= 1.0 =
* Initial version
