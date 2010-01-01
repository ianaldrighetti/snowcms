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

/*
  Class: Form

  Allows the registration and validation of registered form data, or any
  other kinda of data you expect to get from the end-user. This class is
  included with SnowCMS in the hopes that developers will use this tool
  to help prevent CSRF (Cross-Site Request Forgery).
*/
class Form
{
  # Variable: forms
  # Contains the forms which are registered for the current member/guest.
  private $forms;

  /*
    Constructor: __construct

    Parameters:
      none
  */
  public function __construct()
  {
    global $api, $db, $member;

    $this->forms = array();

    # Let's see if they have any registered forms :)
    # and of course, only if they aren't older than 1 day.
    $result = $db->query('
      SELECT
        form_name, form_token, form_registered
      FROM {db->prefix}forms
      WHERE session_id = {string:session_id} AND form_registered >= {int:timeout}',
      array(
        'session_id' => $member->is_logged() ? 'member_id-'. $member->id() : 'ip'. $member->ip(),
        'timeout' => time() - 86400,
      ), 'form_load_registered_query');

    if($result->num_rows() > 0)
    {
      while($row = $result->fetch_assoc())
        $this->forms[$row['form_name']] = array(
                                            'token' => $row['form_token'],
                                            'registered' => $row['form_registered'],
                                            'is_new' => false,
                                            'deleted' => false,
                                          );
    }

    # Save the registered forms right before exit...
    $api->add_hook('snow_exit', create_function('', '
      global $api;

      $form = $api->load_class(\'Form\');

      $form->save();'));
  }

  /*
    Method: add

    Associates the specified token with the form name.

    Parameters:
      string $name - The name of the form.
      string $token - The token to associate with the form
                      name, make sure it is random, however,
                      you can leave this parameter blank, and
                      one will be generated for you.

    Returns:
      string - Returns a string which is the forms token.
  */
  public function add($name, $token = null)
  {
    global $api;

    # Empty token? That's alright, we can fix that :)
    if(empty($token))
    {
      $members = $api->load_class('Members');
      $token = $members->rand_str(16);
    }

    # Add it to the current forms.
    $this->forms[$name] = array(
                            'token' => $token,
                            'registered' => time(),
                            'is_new' => true,
                            'deleted' => false,
                          );

    return $token;
  }

  /*
    Method: exists

    Parameters:
      string $name - The name of the form.

    Returns:
      bool - Returns TRUE if the form exists, FALSE if not.
  */
  public function exists($name)
  {
    return isset($this->forms[$name]) && !$this->forms[$name]['deleted'];
  }

  /*
    Method: is_valid

    Checks to see if the supplied token matches the one with the form name.

    Parameters:
      string $name - The name of the form.
      string $token - The token to check the validity of.

    Returns:
      bool - Returns TRUE if the token is correct, FALSE if not.

    Note:
      Just incase :P If the token is not valid, depending upon the scenario,
      don't get rid of the information (such as a page editing, forum post, etc.)
      just say that the form token was incorrect and have them resubmit the data,
      if it is theirs of course :P.
  */
  public function is_valid($name, $token)
  {
    return $this->exists($name) && $this->forms[$name]['token'] == $token;
  }

  /*
    Method: delete

    Deletes the specified form.

    Parameters:
      string $name - The name of the form.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function delete($name)
  {
    if($this->exists($name))
    {
      $this->forms[$name]['deleted'] = true;
      return true;
    }
    else
      return false;
  }

  /*
    Method: clear

    Marks all forms for deletion.

    Parameters:
      string $session_id - The session ID to clear all the forms of,
                           by default, it will do the current end-users,
                           but you can supply a custom one.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function clear($session_id = null)
  {
    global $db, $member;

    # Is it the current session? Just mark them for deletion.
    if(empty($session_id) || $session_id == ($member->is_logged() ? 'member_id-'. $member->id() : 'ip'. $member->ip()))
    {
      if(count($this->forms))
        foreach($this->forms as $form_name => $form)
          $this->delete($form_name);

      return true;
    }
    else
    {
      # It is a different session ID than the current, so do it RIGHT NOW! :P
      $result = $db->query('
        DELETE FROM {db->prefix}forms
        WHERE session_id = {string:session_id}',
        array(
          'sessiond_id' => $session_id,
        ), 'form_clear_query');

      return $result->success();
    }
  }

  /*
    Method: save

    Saves any new information about the forms in the database, such as
    adding new forms, updating current ones or deleting, well, deleted ones!

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.

    Note:
      You should not call on this method, as it will be done automatically ;)
  */
  public function save()
  {
    global $db, $member;

    if(count($this->forms) > 0)
    {
      $deleted = array();
      $changed = array();
      foreach($this->forms as $form_name => $form)
      {
        # Is it marked for deletion?
        if($form['deleted'])
          $deleted[] = $form_name;
        # Maybe it is updated/new?
        elseif($form['is_new'])
          $changed[] = array($member->is_logged() ? 'member_id-'. $member->id() : 'ip'. $member->ip(), $form_name, $form['token'], $form['registered']);

      }

      # Any deleted?
      if(count($deleted) > 0)
        $db->query('
          DELETE FROM {db->prefix}forms
          WHERE form_name IN({string_array:deleted})',
          array(
            'deleted' => $deleted,
          ), 'forms_save_delete_query');

      # So do any need adding, or deletion?
      if(count($changed) > 0)
        $db->insert('replace', '{db->prefix}forms',
          array(
            'session_id' => 'string', 'form_name' => 'string-100', 'form_token' => 'string-255',
            'form_registered' => 'int',
          ),
          $changed,
          array('sessiond_id', 'form_name'), 'forms_save_replace_query');
    }
  }
}
?>