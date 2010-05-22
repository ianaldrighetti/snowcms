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

# Title: Control Panel - Plugins - Settings

if(!function_exists('admin_plugins_settings'))
{
  /*
    Function: admin_plugins_settings

    This is meant for plugins to add their various settings to, plugins
    can of course create their own settings pages and such in the control
    panel, however, their are certain plugins which really don't need
    their own, so this is for them!

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_settings()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_url;

    $api->run_hooks('admin_plugins_settings');

    # Can you manage plugin settings?
    if(!$member->can('manage_plugin_settings'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    # We will need the form for this ;)
    admin_plugins_settings_generate_form();
    $form = $api->load_class('Form');

    # Submitting the form? Alright.
    if(!empty($_POST['admin_plugins_settings_form']))
    {
      # We shall process it! But through AJAX?
      if(isset($_GET['ajax']))
      {
        echo $form->json_process('admin_plugins_settings_form');
        exit;
      }
      else
      {
        # Just regular ol' submitting ;)
        $form->process('admin_plugins_settings_form');
      }
    }

    $theme->set_current_area('plugins_settings');

    $theme->set_title(l('Plugin settings'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_settings-small.png" alt="" /> ', l('Manage plugin settings'), '</h1>
  <p>', l('Various plugin settings can be managed here.'), '</p>';

    # Gotta run those hooks, in order to know the actual number of fields...
    $form->run_hooks('admin_plugins_settings_form');

    # Are there even any settings?
    # Of course there is one field, which is the form token...
    if($form->num_fields('admin_plugins_settings_form') > 1)
    {
      # Yup, there are!
      $form->show('admin_plugins_settings_form');
    }
    else
    {
      # Nope, there is not.
      echo '
  <p style="margin-top: 10px; font-weight: bold; text-align: center;">', l('There are currently no plugin settings.'), '</p>';
    }

    $theme->footer();
  }
}

if(!function_exists('admin_plugins_settings_generate_form'))
{
  /*
    Function: admin_plugins_settings_generate_form

    Generates the form which plugins can add their various settings to.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_settings_generate_form()
  {
    global $api, $base_url, $db, $settings;

    # We need the Form class, that's for sure!
    $form = $api->load_class('Form');

    $form->add('admin_plugins_settings_form', array(
                                                'action' => $base_url. '/index.php?action=admin&sa=plugins_settings',
                                                'ajax_submit' => true,
                                                'callback' => 'admin_plugins_settings_handle',
                                                'submit' => l('Save settings'),
                                              ));

    # There is actually nothing to add, lol... It's all for the plugins ;)
  }
}

if(!function_exists('admin_plugins_settings_handle'))
{
  /*
    Function: admin_plugins_settings_handle

    Handles the saving of the settings from the plugins settings form.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns true on success, false on failure.

    Note:
      This function is overloadable.
  */
  function admin_plugins_settings_handle($data, &$errors = array())
  {
    global $api, $settings;

    # We will need to update the values so we don't have to redirect.
    $form = $api->load_class('Form');

    # Loop through all the settings and save them!
    foreach($data as $variable => $value)
    {
      # Set it :)
      $settings->set($variable, $value, 'string');

      # Update the value, otherwise we would need to refresh, which is unrequired.
      $form->edit_field('admin_plugins_settings_form', $variable, array(
                                                                    'value' => $value,
                                                                  ));
    }

    $api->add_filter('admin_plugins_settings_form_message', create_function('$value', '
                                                              return l(\'Plugin settings have been successfully updated.\');'));

    return true;
  }
}
?>