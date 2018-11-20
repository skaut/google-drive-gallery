=== Google Drive gallery ===
Contributors: skaut, genabitu, kalich5
Tags: skaut, google drive, google drive gallery, team drive, gallery, multisite, shortcode
Requires at least: 4.9.6
Tested up to: 5.0
Stable tag: 2.4.0
Requires PHP: 5.6
License: MIT
License URI: https://github.com/skaut/skaut-google-drive-gallery/blob/master/license.txt

A WordPress gallery using Google Drive as file storage

== Description ==

Google Drive gallery enables you to insert image galleries into your WordPress site. The galleries are sourced from Google Drive, so that you don't have to store the images on your site, which saves you form having to upload them to the site and have them take up space on your hosting. Also, if you ever decide to reinstall or move your site, simply install this plugin again on the new site and old your old galleries will still be there and working.

To see the plugin in action, head on to our [demo page](https://demo-skaut-google-drive-gallery.skauting.cz/).

=== Features ===
* Display any Google Drive folder as a gallery
* Subfolders are automatically displayed as nested galleries - no need to configure them
* You can restrict the plugin to a certain folder (we call it the "root" folder), making sure that no data outside this root folder will ever be displayed on your site
* Insert a gallery with a shortcode or with a click of a button
* Provides a Gutenberg block, making sure that your galleries will work even in WordPress 5
* Also works with Team Drives

=== Minimal requirements ===
* WordPress 4.9.6 or higher
* PHP 5.6 or higher

=== GitHub ===
[https://github.com/skaut/skaut-google-drive-gallery/](https://github.com/skaut/skaut-google-drive-gallery/)

== Installation ==
1. Download and install the plugin from the WordPress plugin directory or from [GitHub](https://github.com/skaut/skaut-google-drive-gallery/releases)
2. Create a Google app and configure the plugin
3. Select a root directory for the plugin to use
4. Add a gallery

== Frequently Asked Questions ==

= How to configure this plugin? =
See our [documentation](https://napoveda.skaut.cz/dobryweb/en-skaut-google-drive-gallery).

= How do I create a Google app? =
We have a special page in our [documentation](https://napoveda.skaut.cz/dobryweb/en-skaut-google-drive-gallery/en-get-google-application) just about that.

= What are the other options for this plugin? =
For more info about all the options see the [documentation page](https://napoveda.skaut.cz/dobryweb/en-skaut-google-drive-gallery/en-advanced-options) about advanced options.

= How do I create a shortcode? =
To show a Google Drive gallery add the shortcode `[sgdg path="Folder name"]` to the page where "Folder name" is a folder in the root directory of the plugin.
It is also possible to use subdirectories with the shortcode `[sgdg path="Folder name/subfolder/subsubfolder"]`.
If no path is provided, then the root directory itself is used.

== Screenshots ==

1. A simple gallery

2. A gallery with subfolders

3. Subfolder view

4. An open image

5. Basic options

6. Advanced options

== Changelog ==

= 2.4.0 =
* Added the option to override some settings for individual galleries using shortcode attributes or the Gutenberg block
* Fixed loading indicator styling inconsistency

= 2.3.5 =
* Fixed lightbox arrow styling on some templates
* Better error reporting for Root selection, TinyMCE and Gutenberg plugins
* Better loading animation
* Clickable breadcrumbs in Root selection, TinyMCE and Gutenberg plugins

= 2.3.4 =
* Updated basic settings to reflect changes to the Google developer console

= 2.3.3 =
* Fixed error with multiple blocks not working

= 2.3.2 =
* Image ordering by time now uses EXIF DateTime
* Partialy fixed issue with url being overriden when not terminated by a slash
* Fixed imprecise directory item counts
* Fixed issue with other plugins overriding styles

= 2.3.1 =
* Fixed [issue 82](https://github.com/skaut/skaut-google-drive-gallery/issues/82)

= 2.3.0 =
* Fixed potential collision with global composer
* Directory item counts with icons instead of text
* Added more error messages
* Fixed infinite spinner on empty gallery
* Not displaying empty subgalleries

= 2.2.3 =
* Fixed more problems with the justified layout
* Open images now have a unique and permanent address so a link to a particular image can be copied and sent

= 2.2.2 =
* Fixed multiple galleries in one page
* Fixed some aspect ratios getting squished
* Not displaying breadcrumbs when there is nowhere to navigate

= 2.2.1 =
* Fixed an issue with images not being displayed

= 2.2.0 =
* Galleries loading with AJAX
* Dark theme

= 2.1.0 =
* Using Flickr-style justified layout

= 2.0.2 =
* Performance optimisations for subdirectories

= 2.0.1 =
* Fixed missing ordering options

= 2.0.0 =
* Moved settings to 2 top-level pages
* Changed the Google API redirect URI
* Handling unauthorized plugin in Block & Shortcode

= 1.2.1 =
* Fixed error with Gutenberg block overwriting its configuration

= 1.2.0 =
* Added proper support for dynamic columns
* Added a Gutenberg block

= 1.1.0 =
* Added the option to order images and directories (separately) - by time or name, ascending or descending

= 1.0.1 =
* Added graphical editor for TinyMCE

= 1.0.0 =
* The first version
* Required WordPress 4.9.6 and higher
* Required PHP 5.6 and higher

== Upgrade Notice ==

= 2.0.0 =
* Changed the Google API redirect URI. All existing installs need to reconfigure the google app.

= 1.2.1 =
* Fixed an error with Gutenberg blocks. Warning: may break any existing blocks.
