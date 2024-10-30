<?php

add_action('admin_menu', 'callisto_admin');

// adds the callisto admin page to the wordpress settings menu
function callisto_admin() {
	add_options_page('Callisto Analytics Options', 'Callisto Analytics', 'manage_options', 'callisto', 'callisto_options', 'icon.png');
}

// builds the callisto admin page
function callisto_options() {

  // check that the current user has the necessary permissions
	if (!current_user_can('manage_options' ))  {
		wp_die(__('You do not have sufficient permissions to access this page.' ));
	}

  // if the form has been submitted...
  if ($_POST) {

    $enabled = $_POST['callisto_enabled'] ? 'true' : 'false';
    $domain  = trim($_POST['callisto_domain']);

    if (!callisto_valid_top_level_path($domain)) {
      $domain_error = 'Domain is not valid.';
    } else {
      update_option('callisto_domain', $domain);
    }

    update_option('callisto_enabled', $enabled);

  }

  // if the form has not yet been submitted...
  else {

    // get the plugin's settings
    $enabled = get_option('callisto_enabled');
    $domain  = get_option('callisto_domain');

    // if the "enabled" setting is empty or does not exist, set it to "true"
    if (!$enabled) {
      $enabled = 'true';
      update_option('callisto_enabled', $domain);
    }

    // if the domain setting is empty or does not exist, set it to the current domain
    if (!$domain) {
      $domain = parse_url(get_option('siteurl'), PHP_URL_HOST);
      update_option('callisto_domain', $domain);
    }

  }

  // build the html
  ?>

  <div class="wrap">
    <div id="icon-callisto" class="icon32" style="background: transparent url(<?php echo plugin_dir_url(__FILE__) . 'icon.png'; ?>) no-repeat -4px -4px;"><br></div>
    <h2>Callisto.fm Analytics Settings</h2>
    <form method="post" action="options-general.php?page=callisto">
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">Enable/Disable: </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>Enable Tracking?</span></legend>
                <label for="callisto_enabled">
                  <input name="callisto_enabled" type="checkbox" id="callisto_enabled" value="1"<?php echo $enabled == 'true' ? ' checked' : ''; ?>>
                  Enable Callisto.fm media analytics tracking
                </label>
              </fieldset>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><label for="blogname">Domain to track: </label></th>
            <td>
              <input name="callisto_domain" type="text" id="callisto_domain" value="<?php echo $domain; ?>" class="regular-text">
              <?php if ($domain_error): ?>
                <p style="color:#c00;"><?php echo $domain_error; ?></p>
              <?php endif; ?>
            </td>
          </tr>
        </tbody>
      </table>
      <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
    </form>
  </div>

  <?php

}

function callisto_valid_top_level_path($string) {
  $parts = explode('.', $string);
  foreach ($parts as $part) {
    if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $part) || preg_match('/-$/', $part)) {
      return false;
    }
  }
  return true;
}
