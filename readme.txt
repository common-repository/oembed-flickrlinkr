=== oEmbed FlickrLinkr ===
Contributors: isaacwedin
Donate link: http://familypress.net/oembed-flickrlinkr/
Tags: images, photos, flickr
Requires at least: 2.9
Tested up to: 3.0
Stable tag: 0.4

This plugin links oEmbedded Flickr photos to their photo page at Flickr, optionally adding a caption with the photo title and author.

== Description ==

oEmbed is a convenient way to add a variety of external content to WordPress posts and pages. By default WordPress only insert the image for Flickr oEmbeds, without a link back to the photo page or any other information. This plugin uses the oEmbed output that WordPress already collects, linking the Flickr image using the supplied URL and adding a caption with title and author if desired.

== Installation ==

1. Extract the plugin archive in your `/wp-content/plugins/` directory, creating an 'oembed-flickrlinkr' folder there.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Configuration ==

By default the plugin inserts standard captioned photos, with the photo title and author below the photo. This includes a snippet of inline style, which some people don't like.

To configure the captions, go to Settings:oEmbed FlickrLinkr.

The Standard caption option displays the oEmbedded Flickr photo just like a standard WordPress captioned image, using exactly the same CSS classes and inline style. The Simple caption option just gets rid of the inline style and wp-caption classes from the inserted code. You can also remove the caption entirely, just leaving a linked photo. Note that just linking may not satisfy the attribution requirements of some photo licenses.

For Standard and Simple captions you can specify a class for the container div.

You can specify a class for the image itself, useful if you want to add a border, padding, etc.

Updating your settings clears the oEmbed cache, so all of your existing Flickr oEmbeds will change to the new setting.

== Changelog ==

= 0.4 =
* Added some caption customization options.

= 0.3 =
* Fixed caption class to match WP default.

= 0.2 =
* Added Viper007Bond's oEmbed cache flush function on settings save.

= 0.1 =
* Initial release.
