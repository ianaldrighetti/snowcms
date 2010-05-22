<?php
#########################################################################
#                             SnowCMS v2.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#                  Released under the GNU GPL v3 License                #
#                     www.gnu.org/licenses/gpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#                SnowCMS v2.0 began in November 2009                    #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 2.0                         #
#########################################################################

if(!defined('IN_SNOW'))
  die;

# Title: Control Panel - Themes

if(!function_exists('admin_themes'))
{
  /*
    Function: admin_themes

    Provides an interface for the selecting and uploading/downloading of themes.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_themes()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_dir, $theme_url;

    $api->run_hooks('admin_themes');

    # Can you view the error log? Don't try and be sneaky now!
    if(!$member->can('manage_themes'))
    {
      # Get out of here!!!
      admin_access_denied();
    }

    # Time for a Form, awesomeness!!!
    admin_themes_generate_form();
    $form = $api->load_class('Form');

    if(isset($_POST['install_theme_form']))
    {
      $form->process('install_theme_form');
    }

    # A couple things could happen :P
    # So let's just group them.
    if((!empty($_GET['set']) || !empty($_GET['delete'])) && verify_request('get'))
    {
      if(!empty($_GET['set']))
      {
        # Pretty simple to change the current theme ;-)
        $new_theme = basename($_GET['set']);

        # Check to see if the theme exists.
        if(file_exists($theme_dir. '/'. $new_theme) && theme_load($theme_dir. '/'. $new_theme) !== false)
        {
          # Simple enough, set the theme.
          $settings->set('theme', $new_theme, 'string');
        }
      }
      elseif(!empty($_GET['delete']))
      {
        # Deleting, are we?
        $delete_theme = basename($_GET['delete']);

        # Make sure it isn't the current theme.
        if($settings->get('theme', 'string', 'default') != $delete_theme && theme_load($theme_dir. '/'. $delete_theme) !== false)
        {
          # It's not, so we can delete it.
          # Which is simply a recursive delete.
          recursive_unlink($theme_dir. '/'. $delete_theme);
        }
      }

      # Let's get you out of here now :-)
      redirect($base_url. '/index.php?action=admin&sa=themes');
    }

    $theme->set_current_area('manage_themes');

    $theme->set_title(l('Manage themes'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/manage_themes-small.png" alt="" /> ', l('Manage themes'), '</h1>
  <p style="margin-bottom: 20px;">', l('Here you can set the sites theme and also install themes as well.'), '</p>';

    # Get a listing of all the themes :-).
    $themes = theme_list();

    # Now load the information of the current theme.
    $current_theme = theme_load($theme_dir. '/'. $settings->get('theme', 'string', 'default'));

    echo '
  <div style="float: left; width: 200px;">
    <img src="', $theme_url, '/', $settings->get('theme', 'string', 'default'), '/image.png" alt="" title="', $current_theme['name'], '" />
  </div>
  <div style="float: right; width: 590px;">
    <h1 style="margin-top: 0px;">', l('Current theme: %s', $current_theme['name']), '</h1>
    <h3 style="margin-top: 0px;">', l('By %s', (!empty($current_theme['website']) ? '<a href="'. $current_theme['website']. '">' : ''). $current_theme['author']. (!empty($current_theme['website']) ? '</a>' : '')), '</h3>
    <p>', $current_theme['description'], '</p>
  </div>
  <div class="break">
  </div>
  <h1 style="margin-top: 20px;">', l('Available themes'), '</h1>
  <table class="theme_list">
    <tr>';

    # List all the themes ;-)
    $length = count($themes);
    for($i = 0; $i < $length; $i++)
    {
      $theme_info = theme_load($themes[$i]);

      if(($i + 1) % 3 == 1)
      {
        echo '
    </tr>
  </table>
  <table class="theme_list">
    <tr>';
      }

      echo '
      <td', (basename($theme_info['path']) == $settings->get('theme', 'string', 'default') ? ' class="selected"' : ''), '><a href="', $base_url, '/index.php?action=admin&amp;sa=themes&amp;set=', urlencode(basename($theme_info['path'])), '&amp;sid=', $member->session_id(), '" title="', l('Set as site theme'), '"><img src="', $theme_url, '/', basename($theme_info['path']), '/image.png" alt="" title="', $theme_info['description'], '" /><br />', $theme_info['name'], ' </a><br /><a href="', $base_url, '/index.php?action=admin&amp;sa=themes&amp;delete=', urlencode(basename($theme_info['path'])), '&amp;sid=', $member->session_id(), '" title="', l('Delete %s', $theme_info['name']), '" onclick="', ($settings->get('theme', 'string', 'default') == basename($theme_info['path']) ? 'alert(\''. l('You cannot delete the current theme.'). '\'); return false;' : 'return confirm(\''. l('Are you sure you want to delete this theme?\r\nThis cannot be undone!'). '\');"'), '" class="delete">[', l('Delete'), ']</a></td>';
    }

    echo '
    </tr>
  </table>

  <h1>', l('Install a theme'), '</h1>
  <p>', l('Below you can specify a file to upload or a URL at which to download a theme (tarballs and gzipped tarballs only).'), '</p>';

    $form->show('install_theme_form');

    $theme->footer();
  }
}

if(!function_exists('admin_themes_generate_form'))
{
  /*
    Function: admin_themes_generate_form

    Generates the form which allows themes to be installed.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_themes_generate_form()
  {
    global $api, $base_url;

    $form = $api->load_class('Form');

    $form->add('install_theme_form', array(
                                       'action' => $base_url. '/index.php?action=admin&amp;sa=themes',
                                       'method' => 'post',
                                       'callback' => 'admin_themes_handle',
                                       'submit' => l('Install theme'),
                                     ));

    $form->add_field('install_theme_form', 'theme_file', array(
                                                           'type' => 'file',
                                                           'label' => l('From a file:'),
                                                           'subtext' => l('Select the theme file you want to install as a theme.'),
                                                         ));

    $form->add_field('install_theme_form', 'theme_url', array(
                                                          'type' => 'string',
                                                          'label' => l('From a URL:'),
                                                          'subtext' => l('Enter the URL of the theme you want to download and install.'),
                                                          'value' => !empty($_POST['theme_url']) ? $_POST['theme_url'] : 'http://',
                                                        ));
  }
}

if(!function_exists('admin_themes_handle'))
{
  /*
    Function: admin_themes_handle

    Handles the installation of the theme.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      This function is overloadable.
  */
  function admin_themes_handle($data, &$errors = array())
  {
    global $api, $theme_dir;

    # Did you want to upload a theme?
    if(!empty($data['theme_file']) && is_array($data['theme_file']))
    {
      # Looks like you uploaded something, let's see what we can do!
      # First make a temporary file name.
      $filename = $theme_dir. '/'. uniqid('theme_'). '.tmp';

      # Now attempt to move the file.
      if(move_uploaded_file($data['theme_file']['tmp_name'], $filename))
      {
        # It ought to be a tarball ;-)
        $tar = $api->load_class('Tar');
        if($tar->open($filename))
        {
          if($tar->is_gzipped())
          {
            $tar->ungzip();
          }
        }

        # Use the original file name to extract the theme too.
        $name = $data['theme_file']['name'];

        # Remove any file extension.
        if(strpos($name, '.') !== false)
        {
          $tmp = explode('.', $name);

          # Remove the last part.
          array_pop($tmp);

          # Maybe even another part if it is tar.
          if(strtolower($tmp[count($tmp) - 1]) == 'tar')
          {
            array_pop($tmp);
          }

          $name = implode('.', $tmp);
        }

        if($tar->extract($theme_dir. '/'. $name))
        {
          # You would think we would be done, but we are not! Now let's check to make sure it
          # is actually a valid, theme, because if it is not, delete it!
          if(theme_load($theme_dir. '/'. $name) !== false)
          {
            # theme_load will only return an array if the supplied directory
            # contains a valid theme (implemented_theme.class.php and such).
            $api->add_filter('install_theme_form_message', create_function('$value', '
                                                             return l(\'The theme was installed successfully.\');'));

            return true;
          }
          else
          {
            # Nope, it doesn't have the right stuff, so delete it!
            recursive_unlink($theme_dir. '/'. $name);

            # And send an error!
            $errors[] = l('The uploaded theme was not valid.');
          }
        }
        else
        {
          $errors[] = l('Failed to extract the theme.');
        }

        # We are done with it, delete it!
        $tar->close();
        @unlink($filename);
      }
      else
      {
        # Uh oh! Didn't work!
        $errors[] = l('Failed to move the uploaded theme to the theme directory.');
      }
    }
    elseif(!empty($data['theme_url']) && strtolower($data['theme_url']) != 'http://')
    {

    }
    else
    {
      $errors[] = l('No file or URL specified.');
    }

    return false;
  }
}
?>