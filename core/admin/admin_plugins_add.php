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

# Title: Control Panel - Plugins - Add

if(!function_exists('admin_plugins_add'))
{
  /*
    Function: admin_plugins_add

    Handles the downloading and extracting of plugins.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add()
  {
    global $api, $base_url, $member, $settings, $theme, $theme_url;

    $api->run_hooks('admin_plugins_add');

    # Can you add plugins?
    if(!$member->can('add_plugins'))
    {
      # That's what I thought!
      admin_access_denied();
    }

    admin_plugins_add_generate_form();
    $form = $api->load_class('Form');

    $theme->set_current_area('plugins_add');

    $theme->set_title(l('Add plugin'));

    $theme->header();

    echo '
  <h1><img src="', $theme->url(), '/plugins_add-small.png" alt="" /> ', l('Add a new plugin'), '</h1>
  <p>', l('Plugins can be added to your site by entering the plugins dependency name (the address at which the plugins package is downloaded) or by uploading the plugin package.'), '</p>';

    $form->show('add_plugins_form');

    $theme->footer();
  }
}

if(!function_exists('admin_plugins_add_generate_form'))
{
  /*
    Function: admin_plugins_add_generate_form

    Generates the form which allows you to upload or download a plugin.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add_generate_form()
  {
    global $api, $base_url;

    $form = $api->load_class('Form');

    # Let's get to making our form, shall we?
    $form->add('add_plugins_form', array(
                                     'action' => $base_url. '/index.php?action=admin&amp;sa=plugins_add',
                                     'callback' => 'admin_plugins_add_handle',
                                     'method' => 'post',
                                     'submit' => l('Add plugin'),
                                   ));

    # Do you want to upload the plugin?
    $form->add_field('add_plugins_form', 'plugin_file', array(
                                                          'type' => 'file',
                                                          'label' => l('From a file:'),
                                                          'subtext' => l('Select the plugin file you want to install.'),
                                                        ));

    # A URL? Sure!
    $form->add_field('add_plugins_form', 'plugin_url', array(
                                                         'type' => 'string',
                                                         'label' => l('From a URL:'),
                                                         'subtext' => l('Enter the URL of the plugin you want to download and install.'),
                                                         'value' => 'http://',
                                                       ));
  }
}

if(!function_exists('admin_plugins_add_handle'))
{
  /*
    Function: admin_plugins_add_handle

    Handles the form data submitted through the add plugins form.

    Parameters:
      array $data
      array &$errors

    Returns:
      bool - Returns false on failure, the user gets redirected to
             {$base_url}/index.php?action=admin&sa=plugins_add&install={filename}
             where the status of the plugin is checked and then installed.

    Note:
      This function is overloadable.
  */
  function admin_plugins_add_handle($data, &$errors = array())
  {

  }
}
?>