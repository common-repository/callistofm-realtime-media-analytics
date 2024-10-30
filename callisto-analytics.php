<?php

/*

  Plugin Name: Callisto.fm - Realtime Media Analytics
  Plugin URI:  
  Description: Integrates Callisto.fm's realtime media analytics tracking.
  Author:      Callisto.fm, Inc.
  Version:     1.0
  Author URI:  http://callisto.fm/
  License:     

*/

require('callisto-analytics-admin.php');

add_filter('wp_head', 'callisto_autodetect', 10, 0);

function callisto_autodetect() {

  // get the plugin's settings
  $enabled = get_option('callisto_enabled');
  $domain  = get_option('callisto_domain');

  // if the "enabled" setting is empty or does not exist, set it to "true"
  if (!$enabled) {
    $enabled = 'true';
    update_option('callisto_enabled', $enabled);
  }

  // if the domain setting is empty or does not exist, set it to the current domain
  if (!$domain) {
    $domain = parse_url(get_option('siteurl'), PHP_URL_HOST);
    update_option('callisto_domain', $domain);
  }

  // continue to insert callisto's javascript snippet only if callisto is enabled
  if ($enabled !== 'true') {
    return true;
  }

  // build the javascript snippet that tracks media engagements
  ?>

    <!-- Begin Callisto Analytics -->
      <script>window.jQuery || document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"><\/script>');</script>
      <script src="//assets.callisto.fm/code/v2/callisto.min.js"></script>
      <script src="//assets.callisto.fm/code/v2/autodetect.callisto.min.js"></script>
      <script>
        jQuery(document).ready(function() {
          Callisto.AutoDetect(['<?php echo $domain; ?>']);
        });
      </script>
    <!-- End Callisto Analytics -->

  <?

  return true;

}
