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
  This class is a major part of SnowCMS, it allows plugins (or flakes) to use hooks in various places
  which allow the plugins to add new features, or change how current features work as well.
*/
class API
{
  # Variable: hooked
  # An array containing callbacks that have hooked themselves through the API...
  private $hooked;

  # Variable: hooked_actions
  # Another array containing actions plugins have registered (allows ?action=REGISTERED_ACTION)
  private $hooked_actions;

  # Variable: hooked_subactions
  # Yup, thats right, an array containing sub-actions plugins can register on actions
  # Please note that sub-actions only work if the plugin that registered the action
  # uses this sub-action API
  private $hooked_subactions;

  # Variable: groups
  # Registered groups, permission hook, in other words.
  private $groups;

  # Variable: objects
  # Holds objects which have been loaded :)
  private $objects;

  # Variable: request_params
  # An array containing registered request parameters (Like page for ?page=)
  private $request_params;

  # Variable: filters
  # Holds registered filter callbacks...
  private $filters;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    # Just turn all our attributes into empty arrays.
    $this->hooked = array();
    $this->hooked_actions = array();
    $this->hooked_subactions = array();
    $this->groups = array();
    $this->objects = array();
    $this->request_params = array();
    $this->filters = array();
  }

  /*
    Method: add_hook

    Adds a callback on the specified hook name. The callback is called on when the hook is ran.

    Parameters:
      string $hook_name - The name of the hook you want to have the callback associated with.
      callback $callback - The callback (like a function name or create_function()) to be called
                           when the system runs the specified $hook_name.
      int $importance - The importance (or priority) of the callback supplied. The lower the number
                        the sooner it will be executed over others hooking into the same hook.
      int $args - The number of arguments that your callback expects, default is null, which means
                  that as many arguments as the hook at run time has, that is as many as your callback
                  will receive. If you specify more arguments than the hook supplies, null will be supplied
                  in the place of unspecified arguments.

    Returns:
     bool - TRUE if your hook was registered successfully, FALSE on failure, which means that
            a hook has already been registered with that callback in that hook group.

    Note:
       You can view a list of available hooks at Google Code <http://code.google.com/p/snowcms/wiki/Hooks>

  */
  public function add_hook($hook_name, $callback, $importance = 10, $args = null)
  {
    # Hook not in our hooked array?
    if(!isset($this->hooked[$hook_name]))
      $this->hooked[$hook_name] = array();

    # Not callable or the callback already registered?
    if(!is_callable($callback) || $this->hook_registered($hook_name, $callback))
      return false;

    # Your hook is now registered :)
    $this->hooked[$hook_name][] = array(
                                    'callback' => $callback,
                                    'importance' => max((int)$importance, 1),
                                    'args' => empty($args) ? null : max((int)$args, 0),
                                  );

    return true;
  }

  /*
    Method: remove_hook

    Removes the specified callback from the specified hook.

    Parameters:
      string $hook_name - The hook name to remove the specified callback from.
      string $callback - The callback to remove from the specified hook.

    Returns:
      bool - TRUE if the callback was removed, FALSE if the callback wasn't found.
  */
  public function remove_hook($hook_name, $callback)
  {
    # Can't remove a hook if it doesn't exist, right?
    if(!isset($this->hooked[$hook_name]) || count($this->hooked[$hook_name]))
      return false;

    # Let's try to find it, shall we?
    foreach($this->hooked[$hook_name] as $key => $hook)
      if($hook['callback'] == $callback)
      {
        unset($this->hooked[$hook_name][$key]);
        return true;
      }

    return false;
  }

  /*
    Method: hook_registered

    Allows you to check if a callback is already registered in the specified hook name.

    Parameters:
      string $hook_name - The hook name to search for $callback in.
      callback $callback - The callback to search for in $hook_name.

    Returns:
     bool - Returns TRUE if the callback is already registered in the specified hook,
                   FALSE if not.

  */
  public function hook_registered($hook_name, $callback)
  {
    # That hook not even made in the hooked array? Definitely a no!
    if(!isset($this->hooked[$hook_name]) || count($this->hooked[$hook_name]) == 0)
      return false;

    foreach($this->hooked[$hook_name] as $key => $hook)
      if($hook['callback'] == $callback)
        return true;

    return false;
  }

  /*
    Method: run_hook

    Runs the specified hook, along with running the registered callbacks of the hook.

    Parameters:
      string $hook_name - The name of the hook you are executing.
      mixed $args - Either a single argument or an array of arguments.

    Returns:
     void - Nothing is returned.

    Note:
       If you want to allow hooks to change something variable wise, pass the variable
          as a reference parameter (&$var) INSIDE an array! Otherwise you will get a
          E_DEPRECATED error!

  */
  public function run_hook($hook_name, $args = null)
  {
    # No registered callbacks for this hook? Don't waste the time! :)
    if(!isset($this->hooked[$hook_name]) || count($this->hooked[$hook_name]) == 0)
      return;

    # Sort the hooks array by importance :) That is, if there is more than 1!
    if(count($this->hooked[$hook_name]) > 1)
      $this->sort($this->hooked[$hook_name]);

    if(!is_array($args))
      $args = array($args);

    # No need to count parameters over and over and over again, is there?
    $num_args = count($args);

    # Now run all the hooks!
    foreach($this->hooked[$hook_name] as $hook)
    {
      # All parameters?
      if($hook['args'] === null)
        call_user_func_array($hook['callback'], $args);
      # More parameters than we have?
      elseif($hook['args'] > $num_args)
        call_user_func_array($hook['callback'], array_merge($args, array_fill($num_args, $num_args - $hook['args'], null)));
      else
        call_user_func_array($hook['callback'], array_slice($args, 0, $hook['args']));
    }
  }

  /*
    Method: sort

    Used to sort the hooked array in order of importance.

    Parameters:
      array $array - The array to sort by the index 'importance'

    Returns:
     array - Returns the sorted array

    Note:
       Original function available at <http://mschat.net/forum/index.php?topic=1609.0>

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
    Method: add_action

    Adds an action through the API, adding an action allows plugins to add actions accessible through index.php.
    For example, say you add the action help, callback of view_help and the file help.php, whenever someone would
    access the page index.php?action=help the file help.php would be included and the function view_help would be
    executed.

    Parameters:
      string $action_name - The action name you want to register.
      callback $callback - The callback to be called (duh :P) when the action is accessed.
      string $file - The file to include before executing the callback, if null is supplied, no
                     file is executed.

    Returns:
     bool - TRUE is returned if the action was successfully added, FALSE if not, which means that
                   that action is already registered (check out the method remove_action) or that
                   the supplied callback was not valid.

  */
  public function add_action($action_name, $callback, $file = null)
  {
    # You can't register an action if it already exists, silly pants! :P and that the callback is,
    # well, callable and that the file exists (if any...)
    if($this->action_registered($action_name) || (empty($file) && !is_callable($callback)) || (!empty($file) && !file_exists($file)))
      return false;

    # Everything appears to be in order.
    $this->hooked_actions[$action_name] = array($callback, $file);
    return true;
  }

  /*
    Method: remove_action

    Removes the specified action.

    Parameters:
      string $action_name - The action to remove.

    Returns:
     bool - TRUE if the action was successfully removed, FALSE on failure, meaning that
                   the action wasn't registered.

  */
  public function remove_action($action_name)
  {
    # Can't remove something that isn't there, either.
    if(!$this->action_registered($action_name))
      return false;

    unset($this->hooked_actions[$action_name]);
    return true;
  }

  /*
    Method: action_registered

    Checks to see if the specified action is registered.

    Parameters:
      string $action_name - The action name to check.

    Returns:
     bool - TRUE if the action is registered, FALSE if not.

  */
  public function action_registered($action_name)
  {
    return isset($this->hooked_actions[$action_name]);
  }

  /*
    Method: return_action

    Returns the requested action, the first index contains the callback, the second contains the file
    to be included before calling on the callback, unless it is null. However, if you leave the action
    name parameter blank, all registered actions are returned.

    Parameters:
      string $action_name - The action name to return information about.

    Returns:
     array - Returns the array containing a callback and file to include before calling on the
                    the callback, however, FALSE is returned if the action does not exist.

  */
  public function return_action($action_name = null)
  {
    if(empty($action_name))
      return $this->hooked_actions;
    else
      return isset($this->hooked_actions[$action_name]) ? $this->hooked_actions[$action_name] : false;
  }

  /*
    Method: add_subaction

    Registers the specified sub-action to the action.

    Parameters:
      string $action_name - The action name you want to register the subaction with.
      string $subaction_name - The subaction name you want to register.
      callback $callback - The callback to be called (duh :P) when the subaction is accessed.
      string $file - The file to include before executing the callback, if null is supplied, no
                     file is executed.

    Returns:
     bool - TRUE is returned if the subaction was successfully added, FALSE if not, which means that
                   that subaction is already registered (check out the method remove_subaction).

  */
  public function add_subaction($action_name, $subaction_name, $callback, $file = null)
  {
    # Sub-action already registered?
    if($this->subaction_registered($action_name, $subaction_name))
      return false;

    $this->hooked_subactions[$action_name][$subaction_name] = array($callback, $file);
    return true;
  }

  /*
    Method: remove_subaction

    Removes the specified sub-action from the action.

    Parameters:
      string $action_name - The action from which you want to remove.
      string $subaction_name - The sub-action you want to remove.

    Returns:
     bool - TRUE if the sub-action was successfully removed, FALSE on failure, meaning that
                   the sub-action wasn't registered.

  */
  public function remove_subaction($action_name, $subaction_name)
  {
    if(!$this->subaction_registered($action_name, $subaction_name))
      return false;

    unset($this->hooked_subactions[$action_name][$subaction_name]);
    return true;
  }

  /*
    Method: subaction_registered

    Checks to see if the specified sub-action is registered in the action.

    Parameters:
      string $action_name - The action name to check within.
      string $subaction_name - The sub-action to check to see if it is registered.

    Returns:
     bool - TRUE if the subaction is registered, FALSE if not.

  */
  public function subaction_registered($action_name, $subaction_name)
  {
    return isset($this->hooked_subactions[$action_name][$subaction_name]);
  }

  /*
    Method: return_subaction

    Returns the requested subaction, the first index contains the callback, the second contains the file
    to be included before calling on the callback, unless it is null. However, if you leave the subaction
    name parameter blank, all registered subactions of action are returned.

    Parameters:
      string $action_name - The action name to return retrieve subaction information about.
      string $subaction_name - The subaction to return information about.

    Returns:
     array - Returns the array containing a callback and file to include before calling on the
                    the callback, however, FALSE is returned if the subaction does not exist.

  */
  public function return_subaction($action_name, $subaction_name = null)
  {
    if(empty($subaction_name))
      return $this->hooked_subactions[$action_name];
    else
      return isset($this->hooked_subactions[$action_name][$subaction_name]) ? $this->hooked_subactions[$action_name][$subaction_name] : false;
  }

  /*
    Method: add_group

    Allows plugins to add (register) a group that members can have assigned to them, that way these plugins
    can use permissions for any features they may add.

    Parameters:
      string $group_identifier - This is the identifier which is saved in the member_groups column of the table.
                                 Say you had a group named Page Manager, a good group identifier would be
                                 page_manager. When checking to see if the member was assigned that group you
                                 would simply do $member->is_a('page_manager')
      string $group_name - The actual display name of this group, such as Page Manager. You should pass this name
                            through the l() function before passing it on to this method.

    Returns:
     bool - Returns TRUE if the group was added successfully, FALSE if the group is already registered or if for
            some reason the group identifier or group name are not strings.

  */
  public function add_group($group_identifier, $group_name)
  {
    # Can't add a group over another, nor can you have information which aren't strings! :P
    if($this->group_registered($group_identifier) || !is_string($group_identifier) || !is_string($group_name))
      return false;

    $this->groups[strtolower($group_identifier)] = $group_name;
    return true;
  }

  /*
    Method: remove_group

    Removes the specified group identifier from the registered groups.

    Parameters:
      string $group_identifier - The group to remove.

    Returns:
     bool - Returns TRUE if the group was successfully removed, FALSE on failure.

  */
  public function remove_group($group_identifier)
  {
    # You can't remove the group administrator or member...
    if(!$this->group_registered($group_identifier) || in_array(strtolower($group_identifier), array('administrator', 'member')))
      return false;

    unset($this->groups[strtolower($group_identifier)]);
    return true;
  }

  /*
    Method: group_registered

    Checks to see if the specified group is registered.

    Parameters:
      string $group_identifier - The group identifier to check.

    Returns:
     bool - Returns TRUE if the group is registered, FALSE if not.

  */
  public function group_registered($group_identifier)
  {
    # Did I mention that you can't register the group administrator or member? Silly me!
    return !in_array(strtolower($group_identifier), array('administrator', 'member')) ? isset($this->groups[strtolower($group_identifier)]) : true;
  }

  /*
    Method: return_group

    Returns the specified group name, or if no group identifier is supplied, all
    groups are returned.

    Parameters:
      string $group_identifier - The group identifier.

    Returns:
     mixed - An array is returned (containing all registered groups), a string containing
                    the groups name, or FALSE if the group is not registered.

  */
  public function return_group($group_identifier = null)
  {
    if(empty($group_identifier))
      return asort($this->groups, SORT_STRING);
    elseif(!$this->group_registered($group_identifier))
      return false;
    else
      return $this->groups[strtolower($group_identifier)];
  }

  /*
    Method: load_class

    Parameters:
      string $class_name - The name of the class you want to load. If $filename is not
                           specified $core_dir/lower($class_name).class.php is attempted to be
                           opened.
      array $params - An array of parameters you want to pass during the construction of
                      $class_name. (The class must have __construct defined)
      string $filename - The file where $class_name exists. Defaults to null.
      bool $new - If set to true, the object returned will NOT be saved to the objects attribute
                  and won't be taken from that attribute if the same object has already been
                  instantiated on this page load. If FALSE, you will obtain a globally accessible
                  object (Recommended for classes such as Messages, Members, etc).

    Returns:
      Object - Returns the instantiated Object of $class_name, however, if the file was
               not found or the class did not exist, FALSE is returned.
  */
  public function load_class($class_name, $params = array(), $filename = null, $new = false)
  {
    global $core_dir;

    # If you don't want a new object, and one exists, we will just return the one we have :)
    if(empty($new) && isset($this->objects[strtolower(basename($class_name))]))
      return $this->objects[strtolower(basename($class_name))];

    # Does the class not exist? No file? We will assume it is in {CLASS_NAME}.class.php
    if(!class_exists($class_name) && empty($filename))
      $filename = $core_dir. '/'. strtolower(basename($class_name)). '.class.php';

    # Does the class not exist, and the file doesn't either?!
    if(!class_exists($class_name) && !file_exists($filename))
      return false;

    # Only include the file if the class doesn't exist, otherwise we might have a problem.
    if(!class_exists($class_name))
      require_once($filename);

    # Wow, class STILL not exist? Nothing we can do...
    if(!class_exists($class_name))
      return false;

    # Declare and instantiate! Woo!
    $obj = new $class_name();

    # Any parameters, perhaps?
    if(count($params) > 0 && is_callable(array($obj, '__construct')))
      call_user_func_array(array($obj, '__construct'), $params);
    elseif(count($params) > 0 && !is_callable(array($obj, '__construct')))
      return false;

    # Did you want this globally accessible? If so, save it.
    if(empty($new))
      $this->objects[strtolower(basename($class_name))] = $obj;

    return $obj;
  }

  /*
    Method: add_request_param

    Adds a request parameter to watch for. Allows for URL's such as ?page=PAGE_ID
    or ?topic=TOPIC_ID and such.

    Parameters:
      string $request_param - The name of the request parameter to watch for.
      callback $callback - The callback to call when this request parameter is used
                           in a URL.
      string $file - The file to include before calling $callback, if any.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.

    Note:
      Please note that if the action parameter is found in the URL, that any registered
      request parameters won't be checked for. For example, doing index.php?topic=1&action=delete
      will ignore the topic=1 parameter and view it as an action request.
  */
  public function add_request_param($request_param, $callback, $file = null)
  {
    if(empty($request_param) || !is_string($request_param) || (!empty($file) && !file_exists($file)) || $this->request_param_registered($request_param))
      return false;

    $this->request_params[$request_param] = array($callback, $file);
    return true;
  }

  /*
    Method: remove_request_param

    Parameters:
      string $request_param - The name of the request parameter to remove.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function remove_request_param($request_param)
  {
    if(!$this->request_param_registered($request_param))
      return false;

    unset($this->request_params[$request_param]);
    return true;
  }

  /*
    Method: request_param_registered

    Checks to see if the supplied request parameter exists.

    Parameters:
      string $request_param - The name of the request parameter to check.

    Returns:
      bool - Returns TRUE if the request parameter exists, FALSE if not.
  */
  public function request_param_registered($request_param)
  {
    return is_string($request_param) ? isset($this->request_params[$request_param]) : false;
  }

  /*
    Method: return_request_param

    Parameters:
      string $request_param - The request parameter to return the information about.
                              If nothing is supplied, all the registered request
                              parameters are returned.

    Returns:
      array - An array containing the callback and file (if any) of the request parameter.
  */
  public function return_request_param($request_param = null)
  {
    if(empty($request_param))
      return $this->request_params;
    else
      return $this->request_param_registered($request_param) ? $this->request_params[$request_param] : false;
  }

  /*
    Method: add_filter

    Much like hooks, filters work in almost the same way, except they just modify
    the value of a string.

    Parameters:
      string $filter_name - The name of the filter to add the callback to.
      callback $callback - The callback to have called when the filter name
                           is applied.
      int $importance - The importance (or priority) of the callback supplied. The lower the number
                        the sooner it will be executed over others hooking into the same hook.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.

    Note:
      All callbacks are expected to accept 1 parameter, the value they are applying a filter to,
      the callback is also expected to return the value that was changed (if at all) in the callback.
  */
  public function add_filter($filter_name, $callback, $importance = 10)
  {
    # Can't add your callback if it is already registered, or invalid, for that matter.
    if($this->filter_registered($filter_name, $callback) || !is_string($filter_name) || !is_callable($callback))
      return false;
    elseif(!isset($this->filters[$filter_name]))
      $this->filters[$filter_name] = array();

    $this->filters[$filter_name][] = array(
                                       'callback' => $callback,
                                       'importance' => $importance,
                                     );

    return true;
  }

  /*
    Method: remove_filter

    Removes the specified filter callback from the filter name.

    Parameters:
      string $filter_name - The name of the filter.
      callback $callback - The callback to have removed.

    Returns:
      bool - Returns TRUE on success, FALSE on failure.
  */
  public function remove_filter($filter_name, $callback)
  {
    # This filter not even have its own array? Or nothing in it? Then nothing can be in it!
    if(!isset($this->filters[$filter_name]) || count($this->filters[$filter_name]) == 0)
      return false;

    foreach($this->filters[$filter_name] as $key => $filter)
      if($filter['callback'] == $callback)
      {
        unset($this->filters[$filter_name][$key]);
        return true;
      }

    return false;
  }

  /*
    Method: filter_registered

    Checks to see if the supplied callback is already registered with the filter name.

    Parameters:
      string $filter_name - The name of the filter the callback is registered to.
      callback $callback - The callback to check.

    Returns:
      bool - Returns TRUE if the callback is registered to the supplied filter name,
             FALSE if not.
  */
  public function filter_registered($filter_name, $callback)
  {
    # Not even set, or nothing in it? Definitely can't be registered :P
    if(!isset($this->filters[$filter_name]) || count($this->filters[$filter_name]) == 0)
      return false;

    foreach($this->filters[$filter_name] as $filter)
      if($filter['callback'] == $callback)
        return true;

    # Out of the loop and nothing? Then nothing...
    return false;
  }

  /*
    Method: apply_filter

    Applies all the registered filters to the supplied string.

    Parameters:
      string $filter_name - The name of the filter to apply to the value.
      string $value - The string to have filters applied to.

    Returns:
      string - Returns the value with the filters applied.
  */
  public function apply_filter($filter_name, $value)
  {
    # No filters? Well then, take your dang value! :P
    if(!isset($this->filters[$filter_name]) || count($this->filters[$filter_name]) == 0)
      return $value;

    # Needs sorting?
    if(count($this->filters[$filter_name]) > 1)
      $this->sort($this->filters[$filter_name]);

    # Apply all them filters!
    foreach($this->filters[$filter_name] as $filter)
      $value = call_user_func($filter['callback'], $value);

    # Now return it :)
    return $value;
  }
}

/*
  Function: load_api
  Instaniates the API class into the $api variable and loads all enabled plugins.
*/
function load_api()
{
  global $api, $db, $plugin_dir;

  # Oh yeah, now we're talkin'! Get the API instantiated! XD
  $api = new API();

  # Get our activated plugins :)
  $result = $db->query('
    SELECT
      dependency_name, dependency_names, dependencies, directory
    FROM {db->prefix}plugins
    WHERE runtime_error = 0 AND is_activated = 1
    ORDER BY dependencies DESC');

  # Any plugins activated, otherwise, don't do this :)
  if($result->num_rows() > 0)
  {
    # Just incase the right file (plugin.php) doesn't exist in the plugins directory,
    # keep it in an array so we can stop those from running next time :P
    $bad_plugins = array();

    # Holds the plugins dependency name and directory... Allows us to check that
    # a plugins dependencies are all met (Because some people are silly and mess
    # with their database!)
    $plugins = array();

    # Get all those plugins going :)
    while($row = $result->fetch_assoc())
    {
      if(!file_exists($plugin_dir. '/'. $row['directory']. '/plugin.php'))
        # Mark it for 'runtime error'
        $bad_plugins[] = $row['dependency_name'];
      else
        # Simply add it for now...
        $plugins[strtolower($row['dependency_name'])] = array($plugin_dir. '/'. $row['directory']. '/plugin.php', explode(',', strtolower($row['dependency_names'])));
    }

    # Any bad plugins?
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

    # Now we can get to business :D
    if(count($plugins))
    {
      # Another bad plugins array, this time if their dependencies weren't met.
      $bad_plugins = array();

      foreach($plugins as $dependency => $plugin)
      {
        # Any dependencies? Make sure they will be met.
        if(count($plugin[1]) > 0)
        {
          $continue = true;

          foreach($plugin[1] as $dependency)
          {
            $dependency = trim($dependency);
            if(empty($dependency))
              continue;
            elseif(!isset($plugins[$dependency]))
            {
              $bad_plugins[] = $dependency;
              $continue = false;
              break;
            }
          }

          if(!$continue)
            continue;
        }

        # Well, if it is all good, load the plugin ;)
        require_once($plugin[0]);
      }

      if(count($bad_plugins) > 0)
      {
        # Mark it as errorsome, this time with a 2, meaning dependencies weren't met :P
        $db->query('
          UPDATE {db->prefix}plugins
          SET runtime_error = 2
          WHERE dependency_name IN({string_array:bad_plugins})',
          array(
            'bad_plugins' => $bad_plugins,
          ));
      }

      # Alright, one of our first hooks! :D Just a simple one that plugins can hook
      # into when all plugins have been included (Really meant for plugins that are
      # depended upon, so they can have hooks and what not, confusing :P)
      $api->run_hook('post_plugin_activation');
    }
  }

  # Simple hook, something you can hook onto if you want to do something right before SnowCMS stops executing.
  register_shutdown_function(create_function('', '
    global $api;

    $api->run_hook(\'snow_exit\');

    '));
}
?>
