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
  Class: Messages

  This is another one of SnowCMS's API's available to developers. With this
  class it allows for plugins to store and retrieve messages made by members
  or guests, that way each plugin doesn't have to setup a message storing
  system independently.
*/
class Messages
{
  # Variable: loaded
  # This array contains the currently loaded messages.
  private $loaded;

  # Variable: page
  # Contains the current "page" of the loaded messages.
  private $page;

  # Variable: count
  # Contains the total messages in the loaded attribute.
  private $count;

  /*
    Constructor: __construct

    Just sets some stuff up ;)
  */
  public function __construct()
  {
    $this->loaded = array();
    $this->page = 0;
    $this->count = 0;
  }

  /*
    Method: load

    Loads the messages from the specified area.

    Parameters:
      string $area_name - The name of the area where the messages are stored (Such as page,
                          profile, etc).
      int $area_id - The unique ID of the area (Such as the pages ID, profile ID, etc.)
      int $cur_page - The current page of comments you are viewing, starting at 1, however
                      if 0 is supplied, then ALL comments for the specified area will be
                      returned.
      int $per_page - The number of comments per page to show.
      string $order - In which order to sort the messages, ASC for older first, and DESC
                      for newer fist.
      array $params - Extra information to refine which messages are loaded.

    Returns:
      int - Returns 1 if the messages were loaded successfully or 2 which means the messages
            were loaded, however, the page number supplied was not valid (too high) in that
            case, the last page of comments is loaded, 0 if not.
  */
  public function load($area_name, $area_id, $cur_page = 0, $per_page = 10, $order = 'asc', $params = array())
  {
    global $api, $db;

    $handled = null;
    $api->run_hook('messages_load', array(&$handled, $area_name, $area_id, $cur_page, $per_page, $order, $params));

    if($handled === null)
    {
      # Current page 0? Then we don't need to do this...
      if($cur_page > 0)
      {
        $offset = (($cur_page - 1) * $per_page);
        $row_count = $per_page;

        # Make sure you aren't inputting a wrong page :P
        $result = $db->query('
          SELECT
            COUNT(*)
          FROM {db->prefix}messages
          WHERE area_name = {string:area_name} AND area_id = {int:area_id}',
          array(
            'area_name' => $area_name,
            'area_id' => $area_id,
          ), 'messages_load_count_query');

        list($num_messages) = $result->fetch_row();
        $total_pages = ceil($num_messages / $per_page);

        # Set the right page...
        $this->page = $cur_page > $total_pages ? $total_pages : $cur_page;
      }
      else
        $this->page = 0;

      # Reset the loaded messages array...
      $this->loaded = array();
      $this->count = 0;

      # All that member information to load! ;)
      $member_ids = array();

      # Now all of the database variables.
      $db_vars = array(
        'area_name' => $area_name,
        'area_id' => $area_id,
        'is_approved' => isset($params['is_approved']) ? $params['is_approved'] : 1,
      );

      if(isset($params['message_type']))
        $db_vars['message_type'] = $params['message_type'];

      # Got any extra data?
      if(isset($params['extra']))
      {
        $i = 0;
        $extra_search = array();
        foreach($params['extra'] as $variable => $value)
        {
          $str = serialize(array($variable => $value));
          $db_vars['extra_'. $i] = substr($str, strpos($str, '{') + 1, -1);
          $extra_search[] = 'extra LIKE \'%{string:extra_'. $i. '}%\'';
          $i++;
        }

        $extra_search = implode(' AND ', $extra_search);
      }

      # A LIMIT clause?
      if($this->page > 0)
      {
        $db_vars['offset'] = $offset;
        $db_vars['row_count'] = $row_count;
      }

      # Query that database!
      $result = $db->query('
        SELECT
          message_id, member_id, member_name, member_email, member_ip, modified_id, modified_name, modified_email,
          modified_ip, subject, poster_time, modified_time, message, message_type, is_approved, extra
        FROM {db->prefix}messages
        WHERE area_name = {string:area_name} AND area_id = {int:area_id} AND is_approved = {int:is_approved}'. (isset($params['message_type']) ? ' AND message_type = {string:message_type}' : ''). (isset($params['extra']) ? $extra_search : ''). '
        ORDER BY poster_time '. (strtoupper($order) == 'ASC' ? 'ASC' : 'DESC'). ($this->page > 0 ? '
        LIMIT {int:offset},{int:row_count}' : ''),
        $db_vars, 'messages_load_query');

      # Was it a success?
      if($result->success())
      {
        while($row = $result->fetch_assoc())
        {
          # Store the current message
          $message = array(
            'id' => $row['message_id'],
            'poster' => array( # Keep in mind that this array will be different upon being returned via the get method
                               # But of course, you can hook into that too ;)
                          'id' => $row['member_id'],
                          'username' => $row['member_name'],
                          'name' => $row['member_name'],
                          'email' => $row['member_email'],
                          'ip' => $row['member_ip'],
                          'time' => $row['poster_time'],
                          'is_guest' => $row['member_id'] == 0, # This is just a quick guess... Of course all member id's of 0 are guests, but that doesn't mean the member which posted hasn't been removed :P
                          'is_member' => $row['member_id'] > 0, # Just a quick guess too!!
                        ),
            'modifier' => array(
                            'id' => $row['modified_id'],
                            'username' => $row['modified_name'],
                            'name' => $row['modified_name'],
                            'email' => $row['modified_email'],
                            'ip' => $row['modified_ip'],
                            'time' => $row['modified_time'],
                            'is_guest' => false, # Guests can't edit their posts :P But of course, that doesn't mean this can never be true (Ex: the modifiers account no longer exists)
                            'is_member' => true,
                          ),
            'subject' => $row['subject'],
            'message' => $row['message'],
            'type' => $row['message_type'],
            'is_approved' => !empty($row['is_approved']),
            'extra' => @unserialize($row['extra']),
          );

          # Add this member (and modifier, maybe!) to the members we need to have loaded...
          $member_ids[] = $row['member_id'];
          $member_ids[] = $row['modified_id'];

          # Do you want to do something? Go ahead!!! (Like, oh say parse message if there is a message_type ;))
          $api->run_hook('messages_load_array', array(&$message, &$member_ids));

          if(!empty($message))
          {
            $this->loaded[] = $message;
            $this->count++;
          }
        }

        $members = $api->load_class('Members');
        $members->load($member_ids);

        # I think we are done here :)
        $handled = $this->page == $cur_page ? 1 : 2;
      }
      else
        $handled = 0;
    }

    return (int)$handled;
  }

  /*
    Method: get

    Provides access to the loaded messages, if any.

    Parameters:
      int $loaded_id - The index number of which to have the comment returned.
                       What is meant that is if you want to first loaded comment
                       returned you would supply 0, 1 would be the second comment,
                       just like an array! If left as NULL, then ALL comments are
                       returned, not exactly recommended, though.

    Returns:
      array - Returns an array containing the specified message, FALSE if the message(s)
              do not exist.
  */
  public function get($loaded_id = null)
  {
    global $api;

    $handled = null;
    $api->run_hook('messages_get', array(&$handled, $loaded_id));

    if($handled === null)
    {
      # Any specific id given..?
      if($loaded_id !== null)
      {
        # Now before we get too far, is this id valid?
        if(!isset($this->loaded[$loaded_id]))
          # Nope...
          return false;

        $members = $api->load_class('Members');

        # Let's get that specific message, shall we?
        $message = $this->loaded[$loaded_id];

        # Maybe we need to update the member information?
        if($members->get($message['poster']['id']) !== false)
          $message['poster'] = array_merge($message['poster'], $members->get($message['poster']['id']));
        else
          $message['poster'] = array_merge($message['poster'], array('is_guest' => true, 'is_member' => false));

        # Modifier too!
        if($members->get($message['modifier']['id']) !== false)
          $message['modifier'] = array_merge($message['modifier'], $members->get($message['modifier']['id']));
        else
          $message['modifier'] = array_merge($message['modifier'], array('is_guest' => true, 'is_member' => false));

        # Do you have some weird thing to do before we give this message out?
        $api->run_hook('messages_get_array', array(&$message));

        $handled = $message;
      }
      else
      {
        $handled = array();

        # Sweet and simple!!!
        for($i = 0; $i < $this->count; $i++)
          $handled[$i] = $this->get($i);
      }
    }

    return is_array($handled) ? $handled : false;
  }

  /*
    Method: page

    Parameters:
      none

    Returns:
      int - Returns the current page of the messages.
  */
  public function page()
  {
    return $this->page;
  }

  /*
    Method: count

    Parameters:
      none

    Returns:
      int - Returns the total amount of messages loaded.
  */
  public function count()
  {
    return $this->count;
  }

  /*
    Method: add

    Adds a message with all the specified information.

    Parameters:
      string $area_name - The area name to have the message put under.
      string $area_id - The area identifier to have the message put under.
      string $subject - The subject of the message.
      string $message - The actual contents of the message.
      array $options - Other information, such as member information. If
                       no member information is supplied, the current member's
                       information will be used.

    Returns:
      int - Returns the message id on success, FALSE on failure.

    Note:
      If no message_type is supplied in the options parameter, the message parameter
      will have its contents htmlspecialchars encoded!
  */
  public function add($area_name, $area_id, $subject, $message, $options = array())
  {
    global $api, $db, $member;

    $handled = null;
    $api->run_hook('messages_add', array(&$handled, $area_name, $area_id, $subject, $message, $options));

    if($handled === null)
    {
      # Let's construct the array which will be used to insert the comment :)
      $columns = array(
        'area_name' => 'string',
        'area_id' => 'int',
        'member_id' => 'int',
        'member_name' => 'string',
        'member_email' => 'string',
        'member_ip' => 'string',
        'subject' => 'string',
        'poster_time' => 'int',
        'message' => 'string',
        'message_type' => 'string',
        'is_approved' => 'int',
        'extra' => 'string',
      );

      $data = array(
        'area_name' => $area_name,
        'area_id' => $area_id,
        'member_id' => isset($options['member_id']) ? $options['member_id'] : $member->id(),
        'member_name' => isset($options['member_name']) ? $options['member_name'] : $member->name(),
        'member_email' => isset($options['member_email']) ? $options['member_email'] : $member->email(),
        'member_ip' => isset($options['member_ip']) ? $options['member_ip'] : $member->ip(),
        'subject' => $subject,
        'poster_time' => isset($options['poster_time']) ? $options['poster_time'] : time(),
        'message' => !empty($options['message_type']) && !empty($options['dont_htmlchars_message']) ? $message : htmlchars($message),
        'message_type' => !empty($options['message_type']) ? $options['message_type'] : '',
        'is_approved' => isset($options['is_approved']) ? $options['is_approved'] : 1,
        'extra' => isset($options['extra']) && is_array($options['extra']) ? $options['extra'] : array(),
      );

      # Maybe you wanted to add something?
      $api->run_hook('messages_add_data', array(&$columns, &$data, $options));

      # Now insert that comment! :D
      $result = $db->insert('insert', '{db->prefix}messages',
                  $columns,
                  array_values($data),
                  array(), 'messages_add_query');

      $handled = $result->success() ? $result->last_id() : false;
    }

    return (string)(int)$handled == (string)$handled ? (int)$handled : false;
  }

  /*
    Method: update
  */
  public function update()
  {

  }

  /*
    Method: delete
  */
}
?>