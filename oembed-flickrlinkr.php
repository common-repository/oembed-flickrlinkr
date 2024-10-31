<?php
/*
Plugin Name: oEmbed FlickrLinkr
Plugin URI: http://familypress.net/oembed-flickrlinkr/
Description: This just links oEmbedded Flickr photos to their photo page at Flickr, optionally adding a caption with the title and author.
Version: 0.4
Author: Isaac Wedin
Author URI: http://familypress.net/
*/

/* Copyright 2010 Isaac Wedin (email : isaac@familypress.net)
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. 
http://www.opensource.org/licenses/gpl-license.php

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
*/

// get localized
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'flickrlinkr', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );

function flickrlinkr_options_init() {
	register_setting('flickrlinkroptions_options','flickrlinkr_options','flickrlinkr_sanitize');
}

function flickrlinkr_sanitize($input) {
	flickrlinkr_delete_oembed_cache();
	$input['captions'] = wp_filter_nohtml_kses($input['captions']);
	$input['insclass'] = wp_filter_nohtml_kses($input['insclass']);
	$input['imgclass'] = wp_filter_nohtml_kses($input['imgclass']);
	$input['before'] = wp_filter_post_kses($input['before']);
	$input['between'] = wp_filter_post_kses($input['between']);
	$input['after'] = wp_filter_post_kses($input['after']);
	$input['untitled'] = wp_filter_nohtml_kses($input['untitled']);
	$input['caporder'] = wp_filter_nohtml_kses($input['caporder']);
	return $input;
}

function flickrlinkr_add_options_page() {
	add_options_page('flickrlinkr', 'oEmbed FlickrLinkr', 'manage_options', 'flickrlinkr_options', 'flickrlinkr_options_subpanel');
}

function flickrlinkr_options_subpanel() {
	echo '
   <div class="wrap">
   <h2>' . __('oEmbed FlickrLinkr options','flickrlinkr') . '</h2>
   <form method="post" action="options.php">
   <p class="submit"><input type="submit" name="Submit" value="' . __('Update Options &raquo;','flickrlinkr') . '" /></p>
';
	settings_fields('flickrlinkroptions_options');
	$flickrlinkr_options = get_option('flickrlinkr_options');
	if (empty($flickrlinkr_options['caporder'])) {
		// set defaults for between and untitled text here
		// so users can set them to be empty if they really want to
		$flickrlinkr_options['between'] = ' by ';
		$flickrlinkr_options['untitled'] = '(untitled)';
		$flickrlinkr_options['caporder'] = 'titleauthor';
	}
	echo '
   <fieldset class="options">
   <table class="form-table">
      <tbody>
      <tr>
         <th scope="row">' . __('Captions for Flickr oEmbed photos:','flickrlinkr') . "</th>\n<td>";
	if ($flickrlinkr_options['captions'] == 'simple') {
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="standard" size="5"> ' . __('Standard','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="simple" size="5" checked="checked"> ' . __('Simple','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="none" size="5"> ' . __('None','flickrlinkr') . '</label><br />';
   } elseif ($flickrlinkr_options['captions'] == 'none') {
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="standard" size="5"> ' . __('Standard','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="simple" size="5"> ' . __('Simple','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="none" size="5" checked="checked"> ' . __('None','flickrlinkr') . '</label><br />';
   } else {
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="standard" size="5" checked="checked"> ' . __('Standard','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="simple" size="5"> ' . __('Simple','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[captions]" type="radio" value="none" size="5"> ' . __('None','flickrlinkr') . '</label><br />';
   }
   echo '<span class="description">' . __('Either caption your oEmbedded Flickr photos the standard WordPress way, a simpler way that omits inline style, or not at all. Note that a caption may be required by some photo licenses.','flickrlinkr') . '</span></td>
      </tr>
      <tr>
         <th scope="row">' . __('Class for captioned photos:','flickrlinkr') . '</th>
         <td><input name="flickrlinkr_options[insclass]" type="text" value="' . $flickrlinkr_options['insclass'] . '" size="30"><br />
      <span class="description">' . __('This class is applied to the container <code>div</code> for captioned images. Most themes contain <code>alignleft</code>, <code>alignright</code>, and <code>aligncenter</code> classes that should work, or you could use a custom class for more control.','flickrlinkr') . '</span></td>
      </tr>
      <tr>
         <th scope="row">' . __('Image tag class:','flickrlinkr') . '</th>
         <td><input name="flickrlinkr_options[imgclass]" type="text" value="' . $flickrlinkr_options['imgclass'] . '" size="30"><br />
      <span class="description">' . __('This class is applied to the image tag, which might be useful if you want to add or a border, some padding, etc.','flickrlinkr') . '</span></td>
      </tr>
      <tr>
         <th scope="row">' . __('Before caption text:','flickrlinkr') . '</th>
         <td><input name="flickrlinkr_options[before]" type="text" value="' . htmlentities(wp_kses_stripslashes($flickrlinkr_options['before'])) . '" size="50"><br />
      <span class="description">' . __('Text placed before the caption. HTML tags are allowed.','flickrlinkr') . '</span></td>
      </tr>
      <tr>
         <th scope="row">' . __('Between caption text:','flickrlinkr') . '</th>
         <td><input name="flickrlinkr_options[between]" type="text" value="' . htmlentities(wp_kses_stripslashes($flickrlinkr_options['between'])) . '" size="50"><br />
      <span class="description">' . __('Text placed between the caption parts. HTML tags are allowed.','flickrlinkr') . '</span></td>
      </tr>
      <tr>
         <th scope="row">' . __('After caption text:','flickrlinkr') . '</th>
         <td><input name="flickrlinkr_options[after]" type="text" value="' . htmlentities(wp_kses_stripslashes($flickrlinkr_options['after'])) . '" size="50"><br />
      <span class="description">' . __('Text placed after the caption. HTML tags are allowed.','flickrlinkr') . '</span></td>
      </tr>
      <tr>
         <th scope="row">' . __('Untitled photo text:','flickrlinkr') . '</th>
         <td><input name="flickrlinkr_options[untitled]" type="text" value="' . $flickrlinkr_options['untitled'] . '" size="20"><br />
      <span class="description">' . __('Set the text used to title untitled photos here.','flickrlinkr') . '</span></td>
      </tr>
      <tr>
         <th scope="row">' . __('Caption layout:','flickrlinkr') . "</th>\n<td>";
   if (empty($flickrlinkr_options['caporder']) || ($flickrlinkr_options['caporder'] == 'titleauthor')) {
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="titleauthor" size="5" checked="checked"> ' . __('Title then author','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="authortitle" size="5"> ' . __('Author then title','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="titleonly" size="5"> ' . __('Title only','flickrlinkr') . '</label><br />';
   } elseif ($flickrlinkr_options['caporder'] == 'titleonly') {
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="titleauthor" size="5"> ' . __('Title then author','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="authortitle" size="5"> ' . __('Author then title','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="titleonly" size="5" checked="checked"> ' . __('Title only','flickrlinkr') . '</label><br />';
   } else {
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="titleauthor" size="5"> ' . __('Title then author','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="authortitle" size="5" checked="checked"> ' . __('Author then title','flickrlinkr') . '</label><br />';
      echo '<label><input name="flickrlinkr_options[caporder]" type="radio" value="titleonly" size="5"> ' . __('Title only','flickrlinkr') . '</label><br />';
   }
   echo '<span class="description">' . __('Configure the caption layout here. Note that some photo licenses may require you to display the author.','flickrlinkr') . '</span></td>
      </tr>
      </tbody>
   </table> 
   </fieldset>
   <p class="submit"><input type="submit" name="Submit" value="' . __('Update Options &raquo;','flickrlinkr') . '" /></p>
</form> 
</div>
';
}


function flickrlinkr($return,$data,$url) {
	$options = get_option('flickrlinkr_options');
	// if caporder is empty they have not set any options, so set necessary defaults
	if (empty($options['caporder'])) {
		$before = ' by ';
	} else {
		$before = wp_kses_stripslashes($options['before']);
	}
	$between = wp_kses_stripslashes($options['between']);
	$after = wp_kses_stripslashes($options['after']);
	if (!empty($data->title)) {
		$title = $data->title;
	} elseif (!empty($flickpress_options['untitled'])) {
		$title = $flickpress_options['untitled'];
	} else {
		$title = '(untitled)';
	}
	if (empty($options['imgclass'])) {
		$imgclass = 'alignnone';
	} else {
		$imgclass = $options['imgclass'];
	}
	$linkedimg = '<a href="' . $url . '"><img src="' . esc_attr( clean_url( $data->url ) ) . '" alt="' . esc_attr($title) . '" width="' . esc_attr($data->width) . '" height="' . esc_attr($data->height) . '" class="' . $imgclass . '" /></a>';
	if (empty($options['insclass'])) {
		$divclass = 'alignnone';
	} else {
		$divclass = $options['insclass'];
	}
	if ($options['caporder'] == 'authortitle') {
		$caption = $before . '<a href="' . $data->author_url . '">' . $data->author_name . '</a>' . $between . '<a href="' . $url . '">' . $data->title . '</a>' . $after;
	} elseif ($options['caporder'] == 'titleonly') {
		$caption = $before . '<a href="' . $url . '">' . $data->title . '</a>' . $after;
	} else { // default to title-author
		$caption = $before . '<a href="' . $url . '">' . $data->title . '</a>' . $between . '<a href="' . $data->author_url . '">' . $data->author_name . '</a>' . $after;
	}
	if (($data->type == 'photo') && (strstr($data->provider_url,'flickr'))) {
		if ($options['captions'] == 'none') {
			$return = $linkedimg;
		} elseif ($options['captions'] == 'simple') {
			$return = '<div class="' . $divclass . '">' . $linkedimg . '<p>' . $caption . '</p></div>';
		} else {
			$divwidth = $data->width + 10;
			$return = '<div class="wp-caption ' . $divclass . '" style="width: ' . $divwidth . 'px;">' . $linkedimg . '<p class="wp-caption-text">' . $caption . '</p></div>';
		}
	}
	return $return;
}

// stolen from http://core.trac.wordpress.org/attachment/ticket/10337/10337.10.patch
function flickrlinkr_delete_oembed_cache() { 
	// Based on delete_post_meta_by_key() 
	global $wpdb; 
	$post_ids = $wpdb->get_col( "SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key LIKE '_oembed_%'" ); 
	if ( $post_ids ) { 
		$postmetaids = $wpdb->get_col( "SELECT meta_id FROM $wpdb->postmeta WHERE meta_key LIKE '_oembed_%'" ); 
		$in = implode( ',', array_fill( 1, count($postmetaids), '%d' ) );  
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_id IN($in)", $postmetaids ) );  
		foreach ( $post_ids as $post_id ) 
			wp_cache_delete( $post_id, 'post_meta' ); 
		return true; 
	} 
	return false; 
} 

add_filter('oembed_dataparse','flickrlinkr',10,3);
add_action('admin_init','flickrlinkr_options_init');
add_action('admin_menu', 'flickrlinkr_add_options_page');

?>
