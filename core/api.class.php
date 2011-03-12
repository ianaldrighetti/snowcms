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
  Class: API

  This class is a major part of SnowCMS, it allows plugins (or flakes) to
  use hooks in various places which allow the plugins to add new features,
  or change how current features work as well.
*/
class API
{
  # Variable: hooks
  # Contains all the registered hooks to the specified actions.
  private $hooks;

  # Variable: filters
  # Contains all the registered filters to the specified tags.
  private $filters;

  # Variable: count
  # Keeps track of how many times, if any, an action or tag has been
  # called.
  private $count;

  # Variable: events
  # All query string events are kept here.
  private $events;

  # Variable: groups
  # All the registered groups reside here.
  private $groups;

  # Variable: menu
  private $menu;

  # Variable: classes
  # Holds registered classes, which are not located within the core
  # directory.
  private $classes;

  # Variable: objects
  private $objects;

  # Variable: plugins
  # Currently enabled plugins are held in this array.
  private $plugins;

  # Variable: resources
  # A resource is something such as an image, style sheet, or anything, really.
  private $resources;

  /*
    Constructor: __construct

    Sets all attributes to empty arrays and what not.

    Parameters:
      none
  */
  public function __construct()
  {
    # Just set them all to an array...
    $this->hooks = array();
    $this->filters = array();
    $this->count = array(
                     'actions' => array(),
                     'tags' => array(),
                   );
    $this->events = array();
    $this->groups = array(
      'administrator' => 'Administrator',
      'member' => 'Member',
    );
    $this->menu = array(
      'all' => array(),
      'admin' => array(),
    );
    $this->classes = array();
    $this->objects = array();
    $this->plugins = array();
    $this->resources = array();
  }

  /*
    Method: add_hook

    Adds a hook to be ran once the specified action occurs.

    Parameters:
      string $action_name - The action to attach the hook to.
      callback $callback - The callback to have ran once the action occurs.
      int $importance - Allows you to set when your hook is ran, compared
                        to others hooks added to the same action. The
                        smaller the number the more important the hook is.
      int $accepted_args - The number of arguments your hook expects to
                           receive from the action, if any. If you keep
                           this to the default (null), all available
                           arguments will be passed.

    Returns:
      bool - Returns true if the hook was added successfully, false if the
             hook already exists, or if the callback was not callable.

    Note:
      If you supply accepted args as a large number than is actually passed
      by the specified action, the parameters that are "out of range" will
      receive null.
  */
  public function add_hook($action_name, $callback, $importance = 10, $accepted_args = null)
  {
    if(empty($action_name) || !is_callable($callback) || $this->hook_exists($action_name, $callback))
    {
      return false;
    }

    # Is the action not set in the array yet? Let's do it now! ;)
    if(!isset($this->hooks[$action_name]))
    {
      $this->hooks[$action_name] = array();
    }

    # Add the hook, and its now ready to go!
    $this->hooks[$action_name][] = array(
                                     'callback' => $callback,
                                     'importance' => max(intval($importance), 1),
                                     'accepted_args' => empty($accepted_args) ? null : max(intval($accepted_args), 0),
                                   );

    return true;
  }

  /*
    Method: remove_hook

    Removes the hook from the specified action.

    Parameters:
      string $action_name - The name of the action to remove the hook from.
      callback $callback - The callback of the hook to remove from the
                           action.

    Returns:
      bool - Returns true if the hook was removed successfully, false if the
             hook was not found.
  */
  public function remove_hook($action_name, $callback)
  {
    # We can't delete a hook from an action that has no hooks, can we?
    if(empty($action_name) || !is_callable($callback) || !isset($this->hooks[$action_name]) || count($this->hooks[$action_name]) == 0)
    {
      return false;
    }

    foreach($this->hooks[$action_name] as $key => $hook)
    {
      if($hook['callback'] == $callback)
      {
        # We can't just delete it, we need to make sure all the keys are
        # sequential, otherwise, when the action is ran and the hooks
        # sorted, things would get all screwed up, which we don't want! ;)
        $array = array();
        $array_size = count($this->hooks[$action_name]);
        for($i = 0; $i < $array_size; $i++)
        {
          # Do the keys match? Then skip!
          if($key == $i)
          {
            continue;
          }

          # Otherwise, add it to the new array.
          $array[] = $this->hooks[$action_name][$i];
        }

        # Now save our change, before exiting.
        $this->hooks[$action_name] = $array;

        return true;
      }
    }

    # Sorry, didn't find it.
    return false;
  }

  /*
    Method: run_hooks

    Runs the hooks which are registered to the specified action.

    Parameters:
      string $action_name - The action to run.
      mixed $args - Either a single argument, or an array of arguments.

    Returns:
      void - Nothing is returned by this method.

    Note:
      If you want to allow hooks to change the value of a variable, you
      must pass the variable as a reference inside an array, otherwise
      you will receive an E_DEPRECATED error!
  */
  public function run_hooks($action_name, $args = null)
  {
    # Increment the counter, for this action, even if no hooks are ran.
    $this->count['actions'][$action_name] = isset($this->count['actions'][$action_name]) ? $this->count['actions'][$action_name] + 1 : 1;

    # No hooks to run?
    if(!isset($this->hooks[$action_name]) || count($this->hooks[$action_name]) == 0)
    {
      return;
    }

    # Sort the hooks by importance, if there is more than 1!
    if(count($this->hooks[$action_name]) > 1)
    {
      $this->sort($this->hooks[$action_name]);
    }

    # Not an array? I'll make it one!
    if(!is_array($args))
    {
      $args = array($args);
    }

    # No need to count the number of parameters over and over again, right?
    $num_args = count($args);

    # Now run all the hooks.
    foreach($this->hooks[$action_name] as $hook)
    {
      $passed_args = $args;

      # Do you want more than we have..?
      if($hook['accepted_args'] > $num_args)
      {
        for($i = 0; $i < $hook['accepted_args'] - $num_args; $i++)
        {
          $passed_args[] = null;
        }
      }
      elseif($hook['accepted_args'] < $num_args)
      {
        $passed_args = array_slice($passed_args, 0, $hook['accepted_args']);
      }

      call_user_func_array($hook['callback'], $passed_args);
    }
  }

  /*
    Method: hook_exists

    Checks to see if the hook is registered on the specified action.

    Parameters:
      string $action_name - The action to search for the hook.
      callback $callback - The callback to find.

    Returns:
      bool - Returns true if the hook is registered to the specified
             action, false if the hook was not found.
  */
  public function hook_exists($action_name, $callback)
  {
    if(empty($action_name) || !isset($this->hooks[$action_name]) || count($this->hooks[$action_name]) == 0)
    {
      return false;
    }

    foreach($this->hooks[$action_name] as $hook)
    {
      if($hook['callback'] == $callback)
      {
        # Here it is!
        return true;
      }
    }

    # Not found, sorry.
    return false;
  }

  /*
    Method: sort

    Sorts the supplied array by the importance key, using the insertion
    using the insertion sort algorithm. Used to sort hooks and filters.

    Parameters:
      array &$array - The array to sort by the importance key.

    Returns:
      void - Nothing is returned by this method.

    Note:
      Original function available at
      <http://mschat.net/forum/index.php?topic=1609.0>.
  */
  private function sort(&$array)
  {
    $array_size = count($array);

    for($comparison = 0; $comparison < ($array_size - 1); $comparison++)
    {
      $address = $comparison;
      $dummy = $array[$address + 1];

      while($address >= 0 && $dummy['importance'] < $array[$address]['importance'])
      {
        $array[$address + 1] = $array[$address];
        $address--;
      }

      $array[$address + 1] = $dummy;
    }
  }

  /*
    Method: add_filter

    Adds a filter to the specified tag, which when the tag is ran, the
    filter callback is passed the tag value.

    Parameters:
      string $tag_name - The tag to add the filter to.
      mixed $callback - The filter callback, however, this can also be a
                        string, integer, double, etc. That way if you do
                        not need to receive the value of the filter, and
                        you just want to set a new value regardless!
      int $importance - The importance of this filter compared to other
                        filters added to the same tag. The lower the number,
                        the sooner the filter is ran.

    Returns:
      bool - Returns true if the filter is added successfully, false if not.
  */
  public function add_filter($tag_name, $callback, $importance = 10)
  {
    if(empty($tag_name) || $this->filter_exists($tag_name, $callback))
    {
      return false;
    }

    # Do we need to create this tags array?
    if(!isset($this->filters[$tag_name]))
    {
      $this->filters[$tag_name] = array();
    }

    # Add it, and we're done.
    $this->filters[$tag_name][] = array(
                                    'callback' => $callback,
                                    'importance' => max(intval($importance), 1),
                                  );

    return true;
  }

  /*
    Method: remove_filter

    Removes the filter from the specified tag.

    Parameters:
      string $tag_name - The tag to remove the filter from.
      mixed $callback - The callback of the filter or the value.

    Returns:
      bool - Returns true if the filter was removed, false if it was not
             found.
  */
  public function remove_filter($tag_name, $callback)
  {
    if(empty($tag_name) || !isset($this->filters[$tag_name]) || count($this->filters[$tag_name]) == 0)
    {
      return false;
    }

    foreach($this->filters as $key => $filter)
    {
      if($filter['callback'] == $callback)
      {
        # If we found it, we need to make a new array, and exclude the one
        # to be removed, otherwise we will have sorting issues ;)
        $array = array();
        $array_size = count($this->filters[$tag_name]);
        for($i = 0; $i < $array_size; $i++)
        {
          if($key == $i)
          {
            # This is the one we don't want, so skip!
            continue;
          }

          $array[] = $this->filters[$tag_name][$i];
        }

        $this->filters[$tag_name] = $array;
        return true;
      }
    }

    # Didn't find it, sorry.
    return false;
  }

  /*
    Method: apply_filters

    Applies all the filters registered to the specified tag.

    Parameters:
      string $tag_name - The tag name to run.
      mixed $value - The value to pass to the filters.

    Returns:
      mixed - Returns the, possibly, filtered value.
  */
  public function apply_filters($tag_name, $value)
  {
    # Increment the counter for this filter, that's one more, after all ;)
    $this->count['tags'][$tag_name] = isset($this->count['tags'][$tag_name]) ? $this->count['tags'][$tag_name] + 1 : 1;

    # No filters? Just return the value.
    if(!isset($this->filters[$tag_name]) || count($this->filters[$tag_name]) == 0)
    {
      return $value;
    }

    # Sort the filters, just maybe.
    if(count($this->filters[$tag_name]) > 1)
    {
      $this->sort($this->filters[$tag_name]);
    }

    foreach($this->filters[$tag_name] as $filter)
    {
      # Simple enough, really ;) Well, unless it isn't a callback!
      if(is_callable($filter['callback']))
      {
        $value = call_user_func($filter['callback'], $value);
      }
      else
      {
        # The callback index is the new value...
        $value = $filter['callback'];
      }
    }

    return $value;
  }

  /*
    Method: filter_exists

    Checks to see if the filter is registered with the specified tag.

    Parameters:
      string $tag_name - The name of the tag to check in.
      mixed $callback - The callback/value to find.

    Returns:
      bool - Returns true if the callback/value was found, false if not.
  */
  public function filter_exists($tag_name, $callback)
  {
    if(empty($tag_name) || !isset($this->filters[$tag_name]) || count($this->filters[$tag_name]) == 0)
    {
      return false;
    }

    foreach($this->filters[$tag_name] as $filter)
    {
      if($filter['callback'] == $callback)
      {
        # Here it is!
        return true;
      }
    }

    # Not found, sorry.
    return false;
  }

  /*
    Method: add_event

    Adds a query string event with a callback to be executed once the
    event occurs.

    Parameters:
      string $query_string - The query string that should be matched in
                             order to execute the supplied callback.
      callback $callback - The callback to associate with the event.
      string $filename - The file which is included before the callback is
                         executed. Not required unless the callback is not
                         currently callable.

    Returns:
      bool - Returns true if the event was added successfully, false if
             the event already exists.

    Note:
      What is an event? Say you want to have doMyAwesomeThing function
      called when someone accesses $base_url/index.php?action=awesome, you
      would supply action=awesome as the query string. Previously, SnowCMS
      had separate methods to do such a thing with actions, sub actions,
      and request parameters, but  now all of that is handled via events.

      You can also specify wild cards, say you want to have
      blog_view_article called when $base_url/index.php?blog={Some ID},
      your query string would be blog=* and whatever the value of blog is,
      it will be passed as a parameter, as is.

      This also allows the ability to do such things as
      action=someExisting&another=action, what will occur is if the whole
      query string is not found, the last part of the query string is
      chopped off (in this case, &another=action) and another check would
      occur with just action=someExisting, and if it was found, the callback
      would be executed.

      All callbacks are expected to return a boolean value, true if
      everything was done as should, false, if for some reason, everything
      was not properly executed. If the function does return false, the
      next callback (the one below it, with the last bit of the query
      string chopped off) would be executed.

      Also note that you cannot add an event such as blog=* and then add
      another event called blog=help, this would cause an already exists
      error. Same goes for the other way around, if blog=help were added,
      then blog=* were added, an error would occur.

      DO NOT URL encode the query string, and also DO NOT use &amp; as a
      separator, you must use just &.

      Query strings are CASE SENSITIVE! so action=something is not the
      same as Action=something!
  */
  public function add_event($query_string, $callback, $filename = null)
  {
    # Is the callback not callable? Does the file not exist? Does the
    # event already exist?
    if(empty($query_string) || (empty($filename) && !is_callable($callback)) || (!empty($filename) && !file_exists($filename)) || $this->event_exists($query_string) || !($query = $this->parse_query($query_string)))
    {
      return false;
    }

    $events = &$this->events;
    $count = count($query);
    $current = 0;

    # Now for the fun part, adding the event to the events array.
    foreach($query as $key => $value)
    {
      # Does the key not exist? Well, we ought to create, shouldn't we..?
      if(!isset($events[$key]))
      {
        # Just an array ;-)
        $events[$key] = array();
      }

      # Are we to where we need to add something..?
      if($current + 1 == $count)
      {
        # Is the value in the array? Or do we have a wildcard..?
        if(isset($events[$key]['*']))
        {
          # Sorry, can't add anything specific when there is a wildcard.
          return false;
        }
        else
        {
          # Doesn't exist yet, at all..?
          if(!isset($events[$key][$value]))
          {
            $events[$key][$value] = array(
                                      'callback' => $callback,
                                      'filename' => $filename,
                                      'children' => array(),
                                    );
          }
          else
          {
            # Don't mess anything up ;)
            $events[$key][$value]['callback'] = $callback;
            $events[$key][$value]['filename'] = $filename;
          }

          # Added, we are done!
          return true;
        }
      }

      # The next place doesn't exist? So sad.
      if(!isset($events[$key][$value]))
      {
        $events[$key][$value] = array(
                                  'callback' => false,
                                  'filename' => null,
                                  'children' => array(),
                                );
      }

      $events = &$events[$key][$value]['children'];
      $current++;
    }

    return false;
  }

  /*
    Method: remove_event

    Removes the specified event.

    Parameters:
      string $query_string -The query string to remove.

    Returns:
      bool - Returns true if the query string was removed, false if it was
             not found.
  */
  public function remove_event($query_string)
  {
    # Bad query? Then their ain't nothin' we can do for ya!
    if(!($query = $this->parse_query($query_string)))
    {
      return false;
    }

    $events = &$this->events;
    $count = count($query);
    $current = 0;

    # Traverse through the array, fun!
    foreach($query as $key => $value)
    {
      # Are we there yet? :P
      if($current + 1 == $count)
      {
        # Is it set, not empty, I mean... If it isn't it doesn't exist.
        # BREAK!
        if(empty($events[$key][$value]['callback']))
        {
          break;
        }

        # Found it, delete it, done!
        unset($events[$key][$value]);
        return true;
      }
      else
      {
        # Nope.
        $events = &$events[$key][$value]['children'];
      }

      $current++;
    }

    # Did you get out here? Then it didn't exist, so it wasn't deleted!
    return false;
  }

  /*
    Method: return_event

    Returns a registered events information according to the query string
    supplied. The best matching event will be returned.

    Parameters:
      string $query_string - The query string to get the event of.

    Returns:
      array - Returns an array containing the callback, false on failure
              to find a match.
  */
  public function return_event($query_string)
  {
    if(!($query = $this->parse_query($query_string)))
    {
      return false;
    }

    # Keep track of the last known working event, right now, nothing!
    $event = null;

    $events = &$this->events;
    foreach($query as $key => $value)
    {
      # Does this have a working callback? (Or a wildcard)
      $wildcard = isset($events[$key]['*']);
      if(($found = !empty($events[$key][$value]['callback'])) || $wildcard)
      {
        $event = $events[$key][!empty($wildcard) ? '*' : $value];
      }

      # Move on to the next... Maybe.
      if(!isset($events[$key][!empty($wildcard) ? '*' : $value]['children']))
      {
        break;
      }

      $events = &$events[$key][!empty($wildcard) ? '*' : $value]['children'];
    }

    # Is the event not null? Then we found one! Otherwise, nope.
    return !empty($event) ? $event : false;
  }

  /*
    Method: event_exists

    Checks to see if the specified event exists.

    Parameters:
      string $query_string - The query string which would trigger the
                             event.

    Returns:
      bool - Returns true if the event exists, false if not.
  */
  public function event_exists($query_string)
  {
    if(!($query = $this->parse_query($query_string)))
    {
      return true;
    }

    $events = &$this->events;
    $count = count($query);
    $current = 0;

    # Traverse through the array, fun!
    foreach($query as $key => $value)
    {
      # Are we there yet? :P
      if($current + 1 == $count)
      {
        # Is it set, not empty, I mean... If it isn't it doesn't exist. So
        # you can set it.
        if(empty($events[$key][$value]['callback']) && empty($events[$key]['*']['callback']))
        {
          return false;
        }

        # Nope, it exists, you can't set it.
        return true;
      }
      else
      {
        # Nope.
        $events = &$events[$key][$value]['children'];
      }

      $current++;
    }

    # Didn't find it, so have fun!
    return false;
  }

  /*
    Method: parse_query

    Parses the specified query into an array.

    Parameters:
      string $query_string - The query string to parse.

    Returns:
      array - Returns the parsed query on success, false on failure.
  */
  private function parse_query($query_string)
  {
    if(empty($query_string) || strpos($query_string, '=') === false)
    {
      return false;
    }

    // Separate by the ampersands (funny word, huh? But so is any word if
    // you repeat it enough times, go ahead, try it! ... Weird, right?)
    // first...
    $queries = explode('&', $query_string);
    $parsed = array();
    foreach($queries as $query)
    {
      // Now by the equals sign. Maybe.
      if(strpos($query, '=') !== false)
      {
        list($key, $value) = explode('=', $query, 2);
      }
      else
      {
        // This is still valid, but the value will be set to true.
        $key = $query;
        $value = true;
      }

      // Save it!
      $parsed[$key] = $value;
    }

    // Empty? Not good either!
    if(count($parsed) == 0)
    {
      return false;
    }
    else
    {
      return $parsed;
    }
  }

  /*
    Method: add_group

    Adds a group which can be assigned to members, which can be used by
    plugins for permission checking with $member->is_a('group_identifier');

    Parameters:
      string $group_identifier - The groups identifier, which is stored in
                                 the members database, an example for a
                                 page manager would be page_manager.
      string $group_name - The label for the group. Such as Page manager,
                           which should be passed through the l() function
                           before using it in this method.

    Returns:
      bool - Returns true if the group was added successfully, false if not.
  */
  public function add_group($group_identifier, $group_name)
  {
    # Just make sure you don't have anything extra...
    $group_identifier = trim($group_identifier);

    # Does the group already exist? Too bad!
    if($this->group_exists($group_identifier) || !is_string($group_identifier) || !is_string($group_name))
    {
      return false;
    }

    $this->groups[strtolower($group_identifier)] = $group_name;
    return true;
  }

  /*
    Method: remove_group

    Removes the specified group from the list of registered groups.

    Parameters:
      string $group_identifier - The group identifier to remove.

    Returns:
      bool - Returns true if the group was removed successfully, false if
             not.
  */
  public function remove_group($group_identifier)
  {
    $group_identifier = strtolower($group_identifier);

    # Does it not exist? Then we can't remove it! Nor can you remove the
    # member or administrator group, silly!
    if(!$this->group_exists($group_identifier) || $group_identifier == 'administrator' || $group_identifier == 'member')
    {
      return false;
    }

    # Simply unset it!
    unset($this->groups[$group_identifier]);
    return true;
  }

  /*
    Method: group_exists

    Checks to see if the specified group exists.

    Parameters:
      string $group_identifier - The group identifier to check.

    Returns:
      bool - Returns true if the group exists, false if not.
  */
  public function group_exists($group_identifier)
  {
    # Simple enough, right?
    return isset($this->groups[strtolower($group_identifier)]);
  }

  /*
    Method: return_group

    Returns either the group name, or an array of all the registered groups.

    Parameters:
      string $group_identifier - The group identifier to have the name
                                 returned.

    Returns:
      mixed - Returns a string containing the groups name if the group
              identifier was specified, and false if the group identifier
              was not found. If the group identifier was omitted, then all
              groups, in an associative array
              (group_identifier => group_name) is returned.
  */
  public function return_group($group_identifier = null)
  {
    # No group specified? Then all groups will be returned!
    if(empty($group_identifier))
    {
      asort($this->groups, SORT_STRING);
      return $this->groups;
    }
    # How about a specific group?
    elseif($this->group_exists($group_identifier))
    {
      return $this->groups[strtolower($group_identifier)];
    }
    # The group doesn't exist!
    else
    {
      return false;
    }
  }

  /*
    Method: add_menu_item

    Adds a link to the menu.

    Parameters:
      string $category - The category of where the link will be in.
      array $options - An array containing the links options.

    Returns:
      bool - Returns true if the link is added successfully, false
             if not.

    Note:
      The following indexes are supported for the $options parameter:
        href - The destination (URL) of the link.

        name - The name of the anchor to link to (if href supplied, this is
               ignored, and vice versa).

        rel - Specifies the relationship between the current document
              and the one it is linking to. For example, if nofollow was
              supplied, then bots (Google, Bing, etc.) will not follow
              the link (well, they will, but it won't help them).

        class - Specifies a CSS class name(s) for the link.

        id - A unique HTML id for the tag.

        style - Inline styling.

        title - A mouseover text.

        content - The actual content between the tags which is linked.

        extra - Any extra information (which could be a string, array, etc).

        position - A number (starting at zero), specifying at which
                   position the link should be inserted at in the list. If
                   0 is supplied, the link will be placed in the front, if
                   none supplied, the link will be added to the end.
                   Please note that when links are retrieved they are not
                   sorted by this number, they are sorted once it is added.
                   So if you add two links at position 0, the first link
                   will be second and the next will be first.

      By the way. In order to add a link to the admin menu, simply set the
      category as action=admin. To put the link into a category, simply
      supply the categories label in the extra field as a string, if it
      doesn't exist, it will be created.
  */
  public function add_menu_item($category, $options)
  {
    # No category? No options..? No href or name? No content?
    if(empty($category) || !is_array($options) || count($options) == 0 || (!isset($options['href']) && !isset($options['name'])) || empty($options['content']))
    {
      # Then I can't add the link!
      return false;
    }

    # If you have an href and a name, the name goes buh bye!
    if(isset($options['href']) && isset($options['name']))
    {
      unset($options['name']);
    }

    # Only allow certain attributes, delete the rest.
    $allowed_indexes = array('href', 'name', 'rel', 'class', 'id', 'style', 'title', 'content', 'extra', 'position');
    foreach($options as $key => $value)
    {
      if(!in_array($key, $allowed_indexes))
      {
        # Not allowed, so simply delete it.
        unset($options[$key]);
      }
    }

    # Is the category not yet created? Then do so!
    if(!isset($this->menu[$category]))
    {
      $this->menu[$category] = array();
    }

    # Are you going to make my life easy..? :)
    if(!isset($options['position']) || (string)$options['position'] != (string)(int)$options['position'] || (int)$options['position'] < 0 || count($this->menu[$category]) == 0)
    {
      # Yes, thank you!!!
      $this->menu[$category][] = $options;
    }
    else
    {
      $position = (int)$options['position'];

      # We don't need that index anymore...
      unset($options['position']);

      # If the position you want to put it at is bigger than the array,
      # just place it in the back!
      if($position >= count($this->menu[$category]))
      {
        $this->menu[$category][] = $options;
      }
      else
      {
        # Move them all over to a temporary array!
        $menu = array();
        $length = count($this->menu[$category]);

        for($i = 0; $i < $length; $i++)
        {
          # Is this where you want it to be placed?
          if($i == $position)
          {
            $menu[] = $options;
          }

          $menu[] = $this->menu[$category][$i];
        }

        # Copy the new one over.
        $this->menu[$category] = $menu;
      }
    }

    # We're done!
    return true;
  }

  /*
    Method: remove_menu_item

    Removes the specified menu item(s) by key.

    Parameters:
      string $search - The value of which you are searching for,
                       if found, it will be deleted.
      string $category - The category to search. If none supplied,
                         all categories will be searched.
      string $index - The index you want to search by, either href,
                      name, rel, class, id, style, title or content.
                      Defaults to content.
      bool $case_sensitive - Whether or not the search is case sensitive.

    Returns:
      int - Returns the number of menu items removed.
  */
  public function remove_menu_item($search, $category = null, $index = 'content', $case_sensitive = false)
  {
    global $func;

    # Make sure the index you are searching by is allowed.
    $index = strtolower($index);
    if(!in_array($index, array('href', 'name', 'rel', 'class', 'id', 'style', 'title', 'content')))
    {
      # None deleted! :P
      return 0;
    }

    # All categories?
    if(empty($category))
    {
      # We will keep track of the number of deleted menu items here.
      $deleted = 0;

      if(count($this->menu) > 0)
      {
        foreach($this->menu as $key => $value)
        {
          # We will let this method do the stuff :P
          $deleted += $this->remove_menu_item($search, $key, $index, !empty($case_sensitive));
        }
      }

      return $deleted;
    }
    else
    {
      # Does this category even exist?
      if(!isset($this->menu[$category]))
      {
        return 0;
      }

      $deleted = 0;
      if(count($this->menu[$category]) > 0)
      {
        # Make this a tad easier.
        $compare_func = !empty($case_sensitive) ? create_function('$value', 'return $value;') : $func['strtolower'];

        # No need to continually do this, right?
        $search = $compare_func($search);

        foreach($this->menu[$category] as $key => $value)
        {
          if($search == $compare_func($value[$index]))
          {
            # Remove it!
            unset($this->menu[$category][$key]);
            $deleted++;
          }
        }

        # If any were deleted, we may need to tidy up the indexing order :)
        if($deleted > 0)
        {
          $menu = array();
          foreach($this->menu[$category] as $value)
          {
            $menu[] = $value;
          }

          $this->menu[$category] = $menu;
        }
      }

      return $deleted;
    }
  }

  /*
    Method: return_menu_items

    Returns the menu items requested.

    Parameters:
      string $category - The category of links to return, if any.

    Returns:
      array - Returns an array containing the links, false if the
              category does not exist.

    Note:
      If the $category parameter is not supplied (null), then the
      whole menu attribute is returned, containing an array who's
      initial indexes specify the category the links are in, however,
      if false, all the links will be congregated into one array.
  */
  public function return_menu_items($category = null)
  {
    # Just return them all? Alright.
    if($category === null)
    {
      return $this->menu;
    }
    elseif($category === false)
    {
      $menu = array();

      # Loop through them all! If any!
      if(count($this->menu) > 0)
      {
        foreach($this->menu as $value)
        {
          if(count($value) > 0)
          {
            foreach($value as $sub_value)
            {
              $menu[] = $sub_value;
            }
          }
        }
      }

      return $menu;
    }
    else
    {
      return isset($this->menu[$category]) ? $this->menu[$category] : false;
    }
  }

  /*
    Method: add_class

    Registers a class in the API to allow the class to be loaded with
    <API::load_class> without needing to specify the classes file location
    if the class file is not located within the core directory.

    Parameters:
      string $class - The name of the class in the file.
      string $filename - The name of the file the class is located in.

    Returns:
      bool - Returns true on success, false if the class already exists.
  */
  public function add_class($class, $filename)
  {
    if($this->class_exists($class) || !file_exists($filename) || !is_file($filename))
    {
      return false;
    }

    $this->classes[strtolower($class)] = $filename;
    return true;
  }

  /*
    Method: remove_class

    Removes the specified class from the list of registered classes.

    Parameters:
      string $class - The name of the class to remove.

    Returns:
      bool - Returns true if the class was removed, false if the class was
             not registered.
  */
  public function remove_class($class)
  {
    # A bit hard to remove a class which isn't registered.
    if(!$this->class_exists($class))
    {
      return false;
    }

    unset($this->classes[strtolower($class)]);
    return true;
  }

  /*
    Method: class_exists

    Checks to see whether or not the specified class is registered.

    Parameters:
      string $class - The name of the class.

    Returns:
      bool - Returns true if the class is registered, false if not.
  */
  public function class_exists($class)
  {
    return isset($this->classes[strtolower($class)]);
  }

  /*
    Method: load_class

    Loads the specified class and returns the object. If the new parameter
    is set to false, then the same object can be obtained through loading
    the same class by calling on this method again.

    Parameters:
      string $class_name - The name of the class to load.
      array $params - An array of parameters you want to pass to the
                      __construct method once the class has been
                      instantiated.
      string $filename - The name of the where the class is defined, if the
                         file is not specified, then
                         $core_dir/lower($class_name).class.php is assumed.
      bool $new - If set to true, a new and private object will be returned,
                  if false, a reference will be stored in the API class
                  which can be obtained later by loading the same class.

    Returns:
      object - Returns the instantiated object of the specified class,
               however, if the file was not found or the class did not
               exist, false is returned.
  */
  public function load_class($class_name, $params = array(), $filename = null, $new = false)
  {
    global $core_dir;

    # Don't want a new object? Does it already exist? Great! You can have this one :)
    if(empty($new) && isset($this->objects[strtolower($class_name)]))
    {
      return $this->objects[strtolower($class_name)];
    }

    # Does the class not exist already? Then load up the file.
    if(!class_exists($class_name))
    {
      # Hmm, is this a registered class?
      if($this->class_exists($class_name))
      {
        # Use the file name from the registered class.
        $filename = $this->classes[strtolower($class_name)];
      }

      # Is the file name not specified?
      if(empty($filename))
      {
        $filename = $core_dir. '/'. strtolower($class_name). '.class.php';
      }

      # Does the file not exist..?!
      if(!file_exists($filename))
      {
        return false;
      }
      else
      {
        require_once($filename);
      }

      # The class still doesn't exist? Tisk tisk!
      if(!class_exists($class_name))
      {
        return false;
      }
    }

    # Instantiate that class.
    $obj = new $class_name();

    # Any parameters?
    if(count($params) > 0 && is_callable(array($obj, '__construct')))
    {
      call_user_func_array(array($obj, '__construct'), $params);
    }
    elseif(count($params) > 0)
    {
      return false;
    }

    # Not your own "private" object? Then we shall store it!
    if(empty($new))
    {
      $this->objects[strtolower($class_name)] = $obj;
    }

    # Now we're done!
    return $obj;
  }

  /*
    Method: add_plugin

    Adds an enabled plugin to the list of, you guessed it, enabled plugins!

    Parameters:
      string $dependency_name - The plugins dependency name.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function add_plugin($dependency_name)
  {
    # Empty dependency name? Or is the plugin already added?
    if(empty($dependency_name) || $this->plugin_exists($dependency_name))
    {
      return false;
    }

    # Simply add the dependency name ;)
    $this->plugins[] = strtolower($dependency_name);
    return true;
  }

  /*
    Method: remove_plugin

    Removes the specified plugin from the list of enabled plugins.

    Parameters:
      string $dependency_name - The plugins dependency name.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function remove_plugin($dependency_name)
  {
    if(empty($dependency_name) || !$this->plugin_exists($dependency_name))
    {
      return false;
    }

    foreach($this->plugins as $key => $name)
    {
      # Is it the right one?
      if($name == strtolower($dependency_name))
      {
        unset($this->plugins[$key]);
        return true;
      }
    }

    # Technically speaking, this shouldn't happen...
    return false;
  }

  /*
    Method: plugin_exists

    Checks to see if the specified plugin is enabled.

    Parameters:
      string $dependency_name - The plugins dependency name.

    Returns:
      bool - Returns true if the plugin is enabled, false if not.
  */
  public function plugin_exists($dependency_name)
  {
    if(empty($dependency_name))
    {
      # Sorry, we need a name ;)
      return false;
    }

    return in_array(strtolower($dependency_name), $this->plugins);
  }

  /*
    Method: add_resource

    Adds a resource which will then become accessible to those browsing
    the current website. The idea of a resource is to allow plugins to
    have images, style sheets, JavaScript files, etc. and then instead of
    the plugins needing to figure out their location on the server in
    order to serve that resource directly, the API can handle that for
    them.

    Parameters:
      string $area_name - The area under which to add the resource to,
                          so say your plugin is a blog, you would, ideally,
                          supply the area name as blog, or something close
                          to that.
      string $resource_id - An identifier for the resource, for example, if
                            you are adding an icon which symbolizes adding
                            a new blog post, ideally, the resource
                            identifier would be blog_add or so. This can
                            be left blank only if the location parameter
                            is a callback, which would allow you to handle
                            resource viewing yourself.
      mixed $location - This can either be a callback, in which case the
                        viewing of resources will be handled by the
                        callback, and the callback will be invoked when
                        the area is requested. However, this can also be a
                        path to a file, of any kind, such as an image. The
                        supplied path must exist, otherwise adding the
                        resource will fail.

    Returns:
      bool - Returns true if the resource was added successfully, false if
             not.

    Note:
      Resources can be accessed via:

        {$base_url}/index.php?action=resource&area={$area_name}&id={$resource_id}

      That is of course if a resource identifier is supplied, if not then
      the "&id={$resource_id}" can be changed to anything.

      Also note that area names are internally lowercased, however resource
      identifiers are not!
  */
  public function add_resource($area_name, $resource_id, $location)
  {
    # Does the resource already exist?
    if(empty($area_name) || (empty($resource_id) && !is_callable($location)) || (!empty($resource_id) && (!is_file($location) || !is_readable($location))) || $this->resource_exists($area_name, $resource_id))
    {
      return false;
    }
    # Trying to overwrite everything? To bad!
    elseif(is_callable($location) && is_array($this->resources[strtolower($area_name)]) && count($this->resources[strtolower($area_name)]) > 0)
    {
      return false;
    }

    $area_name = strtolower($area_name);

    if(!isset($this->resources[$area_name]))
    {
      $this->resources[$area_name] = array();
    }

    # Do you want to handle all the resource requests yourself? Fine by me!
    if(is_callable($location))
    {
      # In that case, we just need a callback.
      $this->resources[$area_name] = $location;
    }
    else
    {
      $this->resources[$area_name][$resource_id] = realpath($location);
    }

    return true;
  }

  /*
    Method: remove_resource

    Removes the supplied resource.

    Parameters:
      string $area_name - The area to remove the resource from.
      string $resource_id - The resource to remove, if you want to remove
                            all resources from the supplied area, or if
                            the area is a callback, leave this set to null.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function remove_resource($area_name, $resource_id = null)
  {
    # We can't remove something that doesn't exist can we? Right?
    if(empty($area_name) || !$this->resource_exists($area_name, $resource_id))
    {
      return false;
    }

    if(empty($resource_id))
    {
      unset($this->resources[strtolower($area_name)]);
    }
    else
    {
      unset($this->resources[strtolower($area_name)][$resource_id]);
    }

    return true;
  }

  /*
    Method: resource_exists

    Checks to see if the specified resource (or resource handler) exists.

    Parameters:
      string $area_name - The name of the area to check.
      string $resource_id - The resources identifier to check the existence
                            of.

    Returns:
      bool - Returns true if the resource exists, false if not.
  */
  public function resource_exists($area_name, $resource_id)
  {
    if(empty($area_name))
    {
      return false;
    }

    if(empty($resource_id))
    {
      return isset($this->resources[strtolower($area_name)]) && is_callable($this->resources[strtolower($area_name)]);
    }
    else
    {
      return isset($this->resources[strtolower($area_name)][$resource_id]) && !$this->resource_exists($area_name);
    }
  }

  /*
    Method: return_resource

    Returns the resource information.

    Parameters:
      string $area_name - The name of the area which contains the resource.
      string $resource_id - The identifier for the specific resource.

    Returns:
      mixed - Returns a string containing the path to the resource, or a
              callback which will handle the entire resource area, or an
              array containing all the resources in the specified area if
              the area is not handled by a callback OR false if the area
              or resource identifier does not exist.
  */
  public function return_resource($area_name, $resource_id = null)
  {
    # Returning a specific resource?
    if(!empty($resource_id) && $this->resource_exists($area_name, $resource_id))
    {
      return $this->resources[strtolower($area_name)][$resource_id];
    }
    # Returning the callback for the resource, or all the available resources?
    elseif(isset($this->resources[strtolower($area_name)]) || is_callable($this->resources[strtolower($area_name)]))
    {
      return $this->resources[strtolower($area_name)];
    }
    else
    {
      # Does exist, sorry!
      return false;
    }
  }
}

/*
  Function: load_api

  Instantiates an instance of the API class, and also loads all enabled
  plugins.

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function load_api()
{
  global $api, $core_dir, $db, $loading_plugins, $plugin_dir;

  ob_start();

  # Register a shutdown function, which calls on a function to see if the
  # error was fatal, if it was, and caused by a plugin, it will be
  # disabled :)
  register_shutdown_function('api_catch_fatal');

  # We are about to load the plugins!
  # (fyi, this is used to help api_catch_fatal, specifically for PHP 5.2.0 <)
  $loading_plugins = true;

  # Instantiate the API class.
  $api = new API();

  # Load up plugin_load and plugin_list (we don't really use it here, but
  #hey it's for other people too!)
  require_once($core_dir. '/plugin.php');

  # Find all activated plugins, that way we can load them up.
  $result = $db->query('
    SELECT
      dependency_name, directory
    FROM {db->prefix}plugins
    WHERE is_activated = 1 AND runtime_error = 0');

  # Are there any activated plugins?
  if($result->num_rows() > 0)
  {
    # Just incase the plugin doesn't actually work right, we will hold
    # them all here, they are considered bad if our check for the
    # plugin.php file fails.
    $bad_plugins = array();

    # The plugins array, on the other hand, is good. This is where all the
    # plugins information, such as dependencies are held.
    $plugins = array();

    while($row = $result->fetch_assoc())
    {
      # Check for that required plugin.php file.
      if(!file_exists($plugin_dir. '/'. $row['directory']. '/plugin.php'))
      {
        # Mark it for a 'runtime error'
        $bad_plugins[] = $row['dependency_name'];
      }
      else
      {
        # Add the plugin, for now.
        $plugins[strtolower($row['dependency_name'])] = $plugin_dir. '/'. $row['directory']. '/plugin.php';
      }
    }

    # Did we find any bad plugins?
    if(count($bad_plugins) > 0)
    {
      $db->query('
        UPDATE {db->prefix}plugins
        SET runtime_error = 1
        WHERE dependency_name IN({string_array:bad_plugins})',
        array(
          'bad_plugins' => $bad_plugins,
        ));
    }

    # Now for the actual loading of the plugins!
    if(count($plugins))
    {
      foreach($plugins as $dependency => $plugin)
      {
        # Well well, load the plugin!
        require_once($plugin);

        # The plugin is now enabled :-)
        $api->add_plugin($dependency);
      }

      # Alright, one of our first hooks! :D Just a simple one that plugins
      # can hook into when all plugins have been included (Really meant for
      # plugins that are depended upon, so they can have hooks and what not,
      # confusing :P)
      $api->run_hooks('post_plugin_activation');
    }
  }

  # We have now loaded all plugins.
  $loading_plugins = false;

  # Simple hook, something you can hook onto if you want to do something
  # right before SnowCMS stops executing.
  register_shutdown_function(create_function('', '
    global $api;

    $api->run_hooks(\'snow_exit\');'));
}

/*
  Function: api_catch_fatal

  Just incase somethings goes horribly wrong with a plugin (a fatal error),
  this function will handle the disabling of the plugin, and refreshing the
  page so it appears as it never happened!

  Parameters:
    none

  Returns:
    void - Nothing is returned by this function.
*/
function api_catch_fatal()
{
  global $db, $core_dir, $loading_plugins, $plugin_dir;

  # Not loading plugins?
  if(empty($loading_plugins))
  {
    # Then don't worry about it!
    return;
  }

  # Only PHP 5.2.0 >= supports error_get_last :/
  if(!function_exists('error_get_last'))
  {
    $last_error = error_get_last();
  }
  else
  {
    # Which means we need to do it in another way!!!
    $error_string = ob_get_contents();

    # Is it a parse error?
    if(stripos($error_string, 'parse error') !== false)
    {
      # Now that we know it is a parse error, try to get the path.
      $last_error = array(
                      'type' => E_PARSE,
                      'file' => null,
                      'message' => $error_string,
                    );

      $path = substr(trim(substr($error_string, stripos($error_string, ' in '))), 3);

      # Remove the on line #, and we got it!
      $path = trim(substr($path, 0, strripos($path, 'on line')));

      # Any HTML? We don't want that!
      if(stripos($path, '<b>') !== false)
      {
        $path = strtr($path, array('<b>' => '', '</b>' => ''));
      }

      $last_error['file'] = $path;
    }
  }

  # Parse error? That's what we are looking for, after all!
  if($last_error['type'] == E_PARSE)
  {
    # Did it come from the plugin directory?
    if(substr($last_error['file'], 0, strlen($plugin_dir)) == realpath($plugin_dir))
    {
      # Yes it did, now we need to obtain the plugins information.
      $path = explode(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '\\' : '/', realpath(dirname($last_error['file'])));
      $length = count($path);

      for($i = $length - 1; $i > 0; $i--)
      {
        # Keep trying to find the plugin.ini file...
        $cur_path = implode(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? '\\' : '/', $path);

        if(file_exists($cur_path. '/plugin.ini'))
        {
          $plugin = parse_ini_file($cur_path. '/plugin.ini', true);

          # Is the dependency name in there?
          if(isset($plugin['plugin']['dependency name']))
          {
            # We do, so disable that dern thing!
            $db->query('
              UPDATE {db->prefix}plugins
              SET runtime_error = 2
              WHERE dependency_name = {string:dependency_name}
              LIMIT 1',
              array(
                'dependency_name' => $plugin['plugin']['dependency name'],
              ));

            # Log the error.
            if(!function_exists('errors_handler'))
            {
              require_once($core_dir. '/errors.php');
            }

            # Well, now log the error.
            errors_handler($last_error['type'], $last_error['message'], $last_error['file'], null);

            # Redirect, maybe.
            if(!isset($_SESSION['last_error_fix']) || ((int)$_SESSION['last_error_fix'] + 10) < time())
            {
              $_SESSION['last_error_fix'] = time();

              ob_clean();
              header('Location: '. $base_url. $_SERVER['REQUEST_URI']);
              exit;
            }
          }

          break;
        }

        unset($path[$i]);
      }
    }
  }
}

if(!function_exists('api_handle_resource'))
{
  /*
    Function: api_handle_resource

    Handles the displaying of registered resources.

    Parameters:
      none

    Returns:
      void - Nothing is returned by this function.

    Note:
      This function is overloadable.
  */
  function api_handle_resource()
  {
    global $api, $settings;

    $api->run_hooks('api_handle_resource');

    # Do we have an area name?
    if(empty($_GET['area']))
    {
      member_access_denied('No area supplied', 'No area of where the resource is located was supplied.');
    }

    # Why, yes, we do!!!
    $area_name = $_GET['area'];

    # Now let's check if their is a callback which will handle these resources.
    if($callback = $api->return_resource($area_name) && is_callable($callback))
    {
      # Yup, there is!!!
      call_user_func($callback);
      exit;
    }

    # Now we need a resource identifier. So get that ;-).
    if(empty($_GET['id']))
    {
      # Nope, none given.
      member_access_denied('No resource supplied', 'No resource identifier was supplied.');
    }

    # Yup, it was...
    $resource_id = $_GET['id'];

    # Now does this resource exist?
    if(!$api->resource_exists($area_name, $resource_id))
    {
      member_access_denied('Resource not found', 'The supplied resource was not found.');
    }

    # Let's get the location, shall we?
    $location = $api->return_resource($area_name, $resource_id);

    # Maybe you want to handle this? Fine with me!
    $handled = false;
    $api->run_hooks('api_handle_resource_location', array(&$handled, $location));

    # Handled?
    if(empty($handled))
    {
      # Not it was not.
      if(ob_get_length() > 0)
      {
        @ob_clean();
      }

      # Time to output, once we get the content type.
      if(function_exists('finfo_file'))
      {
        $ff = finfo_open(FILEINFO_MIME, $settings->get('finfo_magic_file', 'string', substr(PHP_OS, 0, 3) == 'WIN' ? 'C:\Program Files\PHP\magic' : '/usr/share/misc/file/magic.mgc'));
        $mime_type = finfo_file($ff, $location);
        finfo_close($ff);
      }
      else
      {
        # Use the older, alternative.
        $mime_type = mime_content_type($location);
      }

      header('Content-Type: '. $mime_type);
      header('Content-Length: '. filesize($location));

      # Now to output.
      $fp = fopen($location, 'rb');

      while(!feof($fp))
      {
        echo fread($fp, 8192);
        flush();
      }

      # Alright, we are now done!
      fclose($fp);
      exit;
    }
  }
}
?>