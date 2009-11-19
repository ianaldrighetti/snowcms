<?php
#########################################################################
#                             SnowCMS v1.0                              #
#                          By the SnowCMS Team                          #
#                            www.snowcms.com                            #
#               Released under the GNU Lesser GPL v3 License            #
#                    www.gnu.org/licenses/lgpl-3.0.txt                  #
#########################################################################
#                                                                       #
# SnowCMS originally pawned by soren121 started some time in early 2008 #
#                                                                       #
#########################################################################
#                                                                       #
#  SnowCMS v1.0 began in November 2008 by Myles, aldo and antimatter15  #
#                       aka the SnowCMS Dev Team                        #
#                                                                       #
#########################################################################
#                     File version: SnowCMS 1.0                         #
#########################################################################

# No Direct access please ^^
if(!defined('InSnow'))
  die;

#
# Managing members is what goes on here.
#
# void members_manage();
#   - Handle the list of members.
#
# void members_register();
#   - Register a new member.
#
# void member_options();
#   - Options fo member registration.
#

function members_manage()
{
  global $base_url, $db, $l, $page;
  
  # Get the language
  language_load('admin_members');
  
  # Define possible things to sort by
  $sorts = array(
      'id' => 'member_id',
      'username' => 'displayName',
      'email' => 'email',
      'ip' => 'last_ip',
      'posts' => 'num_posts',
    );
  
  # Get the sort URL stuff
  $page['sort'] = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $_GET['sort'] : 'username';
  $page['sort'] .= isset($_GET['desc']) ? ';desc' : '';
  
  # Get the sort SQL stuff
  $sort_sql = isset($_GET['sort']) && in_array($_GET['sort'],array_keys($sorts)) ? $sorts[$_GET['sort']] : 'displayName';
  $sort_sql .= isset($_GET['desc']) ? ' DESC' : '';
  
  # Get the total members
  $num_members = $db->query("
    SELECT
      COUNT(*)
    FROM {$db->prefix}members",
    array());
  @list($num_members) = $db->fetch_row($num_members);
  
  # Deal with the pagination stuff
  $page_num = isset($_GET['page']) ? (int)$_GET['page'] : 0;
  $page['pagination'] = pagination_create($base_url. '/index.php?action=admin;sa=members;area=list;sort='. $page['sort'], $page_num, $num_members);
  
  # Get the members from the database
  $result = $db->query("
    SELECT
      member_id, displayName, email, last_ip, num_posts
    FROM {$db->prefix}members
    ORDER BY $sort_sql
    LIMIT $page_num, 10",
    array());
  
  # Format the members in a 2D array
  while($row = $db->fetch_assoc($result))
    $page['members'][] = $row;
  
  # Set the title
  $page['title'] = $l['admin_members_list_title'];
  
  # Load the theme
  theme_load('admin_members', 'members_manage_show');
}

function members_register()
{
  global $db, $user, $page, $settings, $l, $source_dir;
  
  # Get the language
  language_load('admin_members');
  
  # Set default field values to empty (To potentially be changed later by registration error)
  $page['username'] = $page['membergroup'] = $page['email'] = '';
  
  # No errors... yet
  $page['errors'] = array();
  
  # If processing the registration
  if(isset($_GET['process']))
  {
    # Include register.php, we need register_validate_emb_send_mail() and register_user()
    require_once($source_dir. '/register.php');
    
    # Get the entered data in URL form, in case of an error
    $data = '';
    $data .= $_POST['username'] ? ';username='. $_POST['username'] : '';
    $data .= $_POST['membergroup'] ? ';membergroup='. $_POST['membergroup'] : '';
    $data .= $_POST['email'] ? ';email='. $_POST['email'] : '';
    
    # Verify data
    # Username empty?
    if(empty($_POST['username']))
      $page['errors'][] = $l['admin_members_register_error_username_empty'];
    # Username too long or short?
    elseif(mb_strlen($_POST['username']) < 3 || mb_strlen($_POST['username']) > 80)
      $page['errors'][] = $l['admin_members_register_error_username_length'];
    # Username taken or reserved?
    elseif(!register_validate_name($_POST['username']))
      $page['errors'][] = $l['admin_members_register_error_username_taken'];
    
    # Password empty?
    if(empty($_POST['passwrd']))
      $page['errors'][] = $l['admin_members_register_error_password_empty'];
    # Password less than four characters long?
    elseif(mb_strlen($_POST['passwrd']) < 4)
      $page['errors'][] = $l['admin_members_register_error_password_length'];
    # Password verification incorrect?
    elseif(!empty($_POST['passwrd']) && mb_strlen($_POST['passwrd']) > 3 && (empty($_POST['vPasswrd']) || $_POST['passwrd'] != $_POST['vPasswrd']))
      $page['errors'][] = $l['admin_members_register_error_password_verify'];
    
    # Email empty?
    if(empty($_POST['email']))
      $page['errors'][] = $l['admin_members_register_error_email_empty'];
    # Email disallowed
    elseif(!register_validate_email($_POST['email']))
      $page['errors'][] = $l['admin_members_register_error_email_disallowed'];
    # Invalid email
    elseif(!preg_match('/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i', $_POST['email']))
      $page['errors'][] = $l['admin_members_register_error_email_invalid'];
    
    # If there are no errors
    if(!count($page['errors']))
    {
      # Get the member's data in a nice array
      $userOptions = array(
          'loginName' => $_POST['username'],
          'password' => $_POST['passwrd'],
          'email' => $_POST['email'],
          'displayName' => $_POST['username'],
          'reg_time' => time_utc(),
          'reg_ip' => $user['ip'],
          'group_id' => (int)$_POST['membergroup'],
          'birthdate' => null,
          'language' => '',
          'timezone' => $settings['timezone'],
          'site_name' => '',
          'site_url' => '',
          'show_email' => !empty($_POST['show_email']),
          'icq' => '',
          'aim' => '',
          'msn' => '',
          'yim' => '',
          'gtalk' => '',
          'override_activation' => true,
          'check_username' => false,
          'check_email' => false,
        );
      
      # And make a function in register.php do the rest
      register_user($userOptions);
      
      # Redirect them
      redirect('index.php?action=admin;sa=members;area=register;registered');
    }
    # If there are errors
    else
    {
      # Get already filled in information for theme to use
      $page['username'] = isset($_POST['username']) ? htmlentities($_POST['username'],ENT_QUOTES) : '';
      $page['membergroup'] = isset($_POST['membergroup']) ? htmlentities($_POST['membergroup'],ENT_QUOTES) : '';
      $page['email'] = isset($_POST['email']) ? htmlentities($_POST['email'],ENT_QUOTES) : '';
    }
  }
  
  # Check for registration success
  $page['registration_success'] = isset($_GET['registered']);
  
  # Get the member groups
  $result = $db->query("
    SELECT
      group_id, group_name
    FROM {$db->prefix}membergroups",
    array());
  
  # Format member groups in a 2D array
  while($row = $db->fetch_assoc($result))
    $page['membergroups'][] = $row;
  
  # Set the title
  $page['title'] = $l['admin_members_register_title'];
  
  # Load the theme
  theme_load('admin_members', 'members_register_show');
}

function members_options()
{
  global $base_url, $db, $settings, $page, $l, $source_dir;
  
  # Get the language file
  language_load('admin_members');
  
  # Get all the member groups out of the database
  $result = $db->query("
    SELECT
      group_id, group_name
    FROM {$db->prefix}membergroups",
    array()
  );
  
  while($row = $db->fetch_assoc($result))
    $membergroups[$row['group_id']] = $row['group_name'];
  
  # Get the settings
  $registration_settings = array(
    array(
      'variable' => 'registration_enabled',
      'type' => 'checkbox',
      'subtext' => true,
    ),
    array(
      'variable' => 'account_activation',
      'type' => 'select',
      'options' => array($l['setting_account_activation_none'], $l['setting_account_activation_email'], $l['setting_account_activation_approval']),
      'subtext' => true,
    ),
    array(
      'variable' => 'registration_group',
      'type' => 'select',
      'options' => $membergroups,
      'subtext' => true,
    ),
  );
  
  $values = $settings;
  
  # Are we processing the saving of settings?
  if(isset($_GET['save']))
  {
    # Handle the saving with a function from admin_settings.php
    settings_save($registration_settings, 'update_settings');
    
    # Redirect them back
    redirect('index.php?action=admin;sa=members;area=registration');
  }
  
  # Get the settings ready for the theme
  $page['settings'] = settings_prepare($registration_settings, $values);
  
  # The URL used for submitting of the form
  $page['submit_url'] = $base_url. '/index.php?action=admin;sa=members;area=registration;save';
  
  # Set the title
  $page['title'] = $l['admin_members_registration_title'];
  
  # Load the theme
  theme_load('admin_members', 'members_options_show');
}
?>