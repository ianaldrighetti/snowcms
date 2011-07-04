<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
  die('Nice try...');
}

/*
  Class: Tokens

  Allows the registration and validation of registered form data, or any
  other kinda of data you expect to get from the end-user. This class is
  included with SnowCMS in the hopes that developers will use this tool
  to help prevent CSRF (Cross-Site Request Forgery).
*/
class Tokens
{
  // Variable: tokens
  // Contains the forms which are registered for the current member/guest.
  private $tokens;

  /*
    Constructor: __construct

    Parameters:
      none
  */
  public function __construct()
  {
    $this->tokens = array();

    // Let's see if they have any registered forms :)
    // and of course, only if they aren't older than 1 day.
    $result = db()->query('
      SELECT
        token_name, token, token_registered
      FROM {db->prefix}tokens
      WHERE session_id = {string:session_id} AND token_registered >= {int:timeout}',
      array(
        'session_id' => member()->is_logged() ? 'member_id-'. member()->id() : 'ip'. member()->ip(),
        'timeout' => time_utc() - 86400,
      ), 'token_load_registered_query');

    if($result->num_rows() > 0)
    {
      while($row = $result->fetch_assoc())
      {
        $this->tokens[$row['token_name']] = array(
                                            'token' => $row['token'],
                                            'registered' => $row['token_registered'],
                                            'is_new' => false,
                                            'deleted' => false,
                                          );
			}
    }

    // Save the registered tokens right before exit...
    api()->add_hook('snow_exit', create_function('', '
      $token = api()->load_class(\'Tokens\');

      $token->save();

      // Maybe we should remove expired ones? But not every page load :P
      // (Why 79? Because that\'s that Wolfram|Alpha answered to the query \'random number between 1 and 100\' :P)
      if(mt_rand(1, 100) == 79)
      {
        db()->query(\'
          DELETE FROM {db->prefix}tokens
          WHERE token_registered < {int:timeout}\',
          array(
            \'timeout\' => time_utc() - 86400,
          ), \'token_delete_expired\');
      }'));
  }

  /*
    Method: add

    Associates the specified token with the form name.

    Parameters:
      string $name - The name of the token.
      string $token - The token to associate with the form
                      name, make sure it is random, however,
                      you can leave this parameter blank, and
                      one will be generated for you.

    Returns:
      string - Returns a string which is the forms token.
  */
  public function add($name, $token = null)
  {
    // Empty token? That's alright, we can fix that :)
    if(empty($token))
    {
      $members = api()->load_class('Members');
      $token = $members->rand_str(16);
    }

    // Add it to the current forms.
    $this->tokens[$name] = array(
                            'token' => $token,
                            'registered' => time_utc(),
                            'is_new' => true,
                            'deleted' => false,
                          );

    return $token;
  }

  /*
    Method: exists

    Parameters:
      string $name - The name of the token.

    Returns:
      bool - Returns TRUE if the form exists, FALSE if not.
  */
  public function exists($name)
  {
    return isset($this->tokens[$name]) && !$this->tokens[$name]['deleted'];
  }

  /*
    Method: is_valid

    Checks to see if the supplied token matches the one with the token name.

    Parameters:
      string $name - The name of the token.
      string $token - The token to check the validity of.
      int $max_age - The maximum age of the token, in seconds. Defaults to
                     86400 seconds (1 day).

    Returns:
      bool - Returns TRUE if the token is correct, FALSE if not.

    Note:
      Just incase :P If the token is not valid, depending upon the scenario,
      don't get rid of the information (such as a page editing, forum post, etc.)
      just say that the form token was incorrect and have them resubmit the data,
      if it is theirs of course :P.
  */
  public function is_valid($name, $token, $max_age = 86400)
  {
    return $this->exists($name) && $this->tokens[$name]['token'] == $token && ($this->tokens[$name]['registered'] + $max_age) >= time_utc();
  }

  /*
    Method: token

    Returns the token associated with the specified form name.

    Parameters:
      string $token_name - The form name of which you want to retrieve the token of.

    Returns:
      string - Returns the token of the specified form name, an empty string if
               there was no form name found.
  */
  public function token($token_name)
  {
    return $this->exists($token_name) ? $this->tokens[$token_name]['token'] : '';
  }

  /*
    Method: delete

    Deletes the specified token.

    Parameters:
      string $name - The name of the token.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function delete($name)
  {
    if($this->exists($name))
    {
      $this->tokens[$name]['deleted'] = true;
      return true;
    }
    else
    {
      return false;
		}
	}

  /*
    Method: clear

    Marks all tokens for deletion.

    Parameters:
      string $session_id - The session ID to clear all the forms of,
                           by default, it will do the current end-users,
                           but you can supply a custom one.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function clear($session_id = null)
  {
    // Is it the current session? Just mark them for deletion.
    if(empty($session_id) || $session_id == (member()->is_logged() ? 'member_id-'. member()->id() : 'ip'. member()->ip()))
    {
      if(count($this->tokens))
      {
        foreach($this->tokens as $token_name => $form)
        {
          $this->delete($token_name);
        }
      }

      return true;
    }
    else
    {
      // It is a different session ID than the current, so do it RIGHT NOW! :P
      $result = db()->query('
        DELETE FROM {db->prefix}tokens
        WHERE session_id = {string:session_id}',
        array(
          'sessiond_id' => $session_id,
        ), 'token_clear_query');

      return $result->success();
    }
  }

  /*
    Method: save

    Saves any new information about the tokens in the database, such as
    adding new tokens, updating current ones or deleting, well, deleted ones!

    Parameters:
      none

    Returns:
      void - Nothing is returned by this method.

    Note:
      You should not call on this method, as it will be done automatically ;)
  */
  public function save()
  {
    if(count($this->tokens) > 0)
    {
      $deleted = array();
      $changed = array();
      foreach($this->tokens as $token_name => $form)
      {
        // Is it marked for deletion?
        if($form['deleted'])
        {
          $deleted[] = $token_name;
        }
        // Maybe it is updated/new?
        elseif($form['is_new'])
        {
          $changed[] = array(member()->is_logged() ? 'member_id-'. member()->id() : 'ip'. member()->ip(), $token_name, $form['token'], $form['registered']);
				}
      }

      // Any deleted?
      if(count($deleted) > 0)
      {
        db()->query('
          DELETE FROM {db->prefix}tokens
          WHERE token_name IN({string_array:deleted})',
          array(
            'deleted' => $deleted,
          ), 'token_save_delete_query');
			}

      // So do any need adding, or deletion?
      if(count($changed) > 0)
      {
        db()->insert('replace', '{db->prefix}tokens',
          array(
            'session_id' => 'string', 'token_name' => 'string-100', 'token' => 'string-255',
            'token_registered' => 'int',
          ),
          $changed,
          array('sessiond_id', 'token_name'), 'token_save_replace_query');
			}
    }
  }
}
?>