=== Image and video gallery from Google Drive ===
Contributors: skaut, marekdedic, kalich5, genabitu
Tags: skaut, google drive, google drive gallery, image and video gallery from google drive, team drive, shared drive, image gallery, video gallery, image and video gallery, gallery from google drive, gallery, multisite, shortcode
Requires at least: 4.9.6
Tested up to: 5.4
Stable tag: 2.7.8
Requires PHP: 5.6
License: MIT
License URI: https://github.com/skaut/skaut-google-drive-gallery/blob/master/LICENSE

A WordPress gallery using Google Drive as file storage

== Description ==

*Image and video gallery from Google Drive* is a plugin for WordPress that connects your site to your Google Drive. With this plugin, you can select any folder in your Drive or a Shared drive and the plugin will convert it into a gallery, displaying any photos or videos in a page or a post on your website. Any folders and their subfolders will also be displayed in the gallery as nested galleries.

All the data is sourced from Google Drive with nothing but the plugin configuration being a part of your website. This enables your site to load faster as the images are loaded from Google and not from your hosting. It may also save you costs for your site hosting, as the big files of the gallery are not stored as part of your site. On top of that, if you ever decide to move or reinstall your site, simply install this plugin again on the new site and your old galleries will still be there and working.

Having the photos in Google Drive also gives you a familiar and easy-to-navigate UI for gallery management. You can give individual people or groups granular permissions to the Drive folder, making them able to add photos to a particular gallery without giving them full access to your site. They will then be able to manage the content of the gallery through Google Drive, without having to learn how to work with WordPress. You can also manage all content in your organization with Shared drives owned by your organization's G Suite.

Using this plugin is very straightforward. Once the plugin is installed and configured, you can add a Google Drive gallery to any page or post. If you are using WordPress 5 or newer with the block editor (i. e. Gutenberg), there is a block that you can add that will allow you to choose a folder with a graphical user interface. It also makes it possible to configure each individual gallery very easily, should you want to do that. If you are using an older WordPress version or are still using the classic editor, there is a shortcode you can use instead of the block.

If you want to see how to install, configure and use the plugin, visit our [documentation](https://napoveda.skaut.cz/dobryweb/en-skaut-google-drive-gallery). To see the plugin in action, head on to the [demo page](https://demo-skaut-google-drive-gallery.skauting.cz/).

=== Features ===
* Display any Google Drive folder as a gallery
* Subfolders are automatically displayed as nested galleries - no need to configure them
* You can restrict the plugin to a certain folder (we call it the "root" folder), making sure that no data outside this root folder will ever be displayed on your site
* Insert a gallery with a shortcode or with a click of a button
* Provides a Gutenberg block, making sure that your galleries will work even in WordPress 5
* Also works with Shared drives (formerly known as Team drives)
* Supports videos as well

=== Minimal requirements ===
* WordPress 4.9.6 or higher
* PHP 5.6 or higher

=== GitHub ===
All the sources for the plugin and the build process are detailed in our [Github repo](https://github.com/skaut/skaut-google-drive-gallery/).

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

= Why isn't my video shown? =
The plugin only shows videos that can be played by the browser. Unfortunately, at the moment, different browsers support different video formats. If you want the best support, we recommend using MP4. Detailed information about which formats are supported by each browser can be found on [Wikipedia](https://en.wikipedia.org/wiki/HTML5_video#Browser_support).

= I'm running into issues with Google quotas =
See the [documentation](https://napoveda.skaut.cz/dobryweb/en-skaut-google-drive-gallery#quota-issues) for what you can do about this.

== Screenshots ==

1. A simple gallery

2. A gallery with subfolders

3. Subfolder view

4. An open image

5. Basic options

6. Advanced options

== Changelog ==

= 2.7.8 =
* Fixed fatal error for users without the intl and idn PHP extensions

= 2.7.7 =
* Re-release of the changes from version 2.7.5
* Fixed handling of HTTP request exceptions
* Tested on WordPress 5.4

= 2.7.6 =
* Rollback of version 2.7.5, identical to version 2.7.4

= 2.7.5 =
* Fixed handling of HTTP request exceptions

= 2.7.4 =
* Fixed a bug in ordering options causing some issues primarily when ordering by name

= 2.7.3 =
* Reverted a dependency update causing issues and PHP version incompatibility

= 2.7.2 =
* Checking JS with TypeScript
* JS and CSS is now minified in the plugin, original sources available in the repository
* Fixed issue with TinyMCE plugin table overflowing the thickbox
* Handling Google errors in root selection when changing user account
* Handling Google errors in gallery enqueueing
* Refactored all JS code
* Fixed issue with & in folder name

= 2.7.1 =
* Fixed a bug causing galleries not to load
* Slightly tweaked the grid layout

= 2.7.0 =
* Added video support
* Documented all of the source code

= 2.6.0 =
* Incorporated updates to Google Drive and its API, namely the rebranding of Team Drives as Shared drives
* Added support for image captions sourced from the "description" field in Google Drive
* Added more quality control with more still to follow

= 2.5.0 =
* Added support for pagination of gallery items with configurable page size and optional (enabled by default) autoloading
* Added option to hide a part (a prefix to be more precise) of folder names. This is useful when folders are ordered by name to define own custom ordering
* Fixed an issue with incorrect Authorised JavaScript origin for websites located in a subdirectory
* Added more checks to plugin options to ensure they can't break it
* Enabled support for caching plugins

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
* Partially fixed issue with url being overridden when not terminated by a slash
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

= 2.7.0 =
* Fixed a bug with paging which may break a small percentage of links to particular images.

= 2.0.0 =
* Changed the Google API redirect URI. All existing installs need to reconfigure the google app.

= 1.2.1 =
* Fixed an error with Gutenberg blocks. Warning: may break any existing blocks.
