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

#
# This class is a major part of SnowCMS, it allows plugins (or flakes) to use hooks
# in various places which allow the plugins to add new features, or change how current
# features work as well.
#
class API
{
  # An array containing callbacks that have hooked themselves through the API...
  private $hooked = array();

  # Another array containing actions plugins have registered (allows ?action=REGISTERED_ACTION)
  private $hooked_actions = array();

  # Yup, thats right, an array containing sub-actions plugins can register on actions
  # Please note that sub-actions only work if the plugin that registered the action
  # uses this sub-action API
  private $hooked_subactions = array();

  /*

    Adds a callback on the specified hook name. The callback is called on when the hook is ran.

    @method public bool add_hook(string $hook_name, callback $callback[, int $importance = 10[, int $args = null]]);
      string $hook_name - The name of the hook you want to have the callback associated with.
      callback $callback - The callback (like a function name or create_function()) to be called
                           when the system runs the specified $hook_name.
      int $importance - The importance (or priority) of the callback supplied. The lower the number
                        the sooner it will be executed over others hooking into the same hook.
      int $args - The number of arguments that your callback expects, default is null, which means
                  that as many arguments as the hook at run time has, that is as many as your callback
                  will receive. If you specify more arguments than the hook supplies, null will be supplied
                  in the place of unspecified arguments.
    returns bool - TRUE if your hook was registered successfully, FALSE on failure, which means that
                   a hook has already been registered with that callback in that hook group.

    NOTE: You can view a list of available hooks at Google Code <http://code.google.com/p/snowcms/wiki/Hooks>

  */
  public function add_hook($hook_name, $callback, $importance = 10, $args = null)
  {
    # Hook not in our hooked array?
    if(!isset($this->hooked[$hook_name]))
      $this->hooked[$hook_name] = array();

    # Not callable?
    if(!is_callable($callback))
      return false;

    # Is that callback already registered?
    if($this->hook_registered($hook_name, $callback))
      return false;

    # Your hook is now registered :)
    $this->hooked[$hook_name][] = array('callback' => $callback, 'importance' => max((int)$importance, 1), 'args' => empty($args) ? null : max((int)$args, 0));

    return true;
  }

  /*

    Removes the specified callback from the specified hook.

    @method public bool remove_hook(string $hook_name, string $callback);
      string $hook_name - The hook name to remove the specified callback from.
      string $callback - The callback to remove from the specified hook.
    returns bool - TRUE if the callback was removed, FALSE if the callback wasn't found.

  */
  public function remove_hook($hook_name, $callback)
  {
    if(!isset($this->hooked[$hook_name]))
      return false;

    foreach($this->hooked[$hook_name] as $key => $hook)
      if($hook['callback'] == $callback)
      {
        unset($this->hooked[$hook_name][$key]);
        return true;
      }

    return false;
  }

  /*

    Allows you to check if a callback is already registered in the specified hook name.

    @method public bool hook_registered(string $hook_name, callback $callback);
      string $hook_name - The hook name to search for $callback in.
      callback $callback - The callback to search for in $hook_name.
    returns bool - Returns TRUE if the callback is already registered in the specified hook,
                   FALSE if not.

  */
  public function hook_registered($hook_name, $callback)
  {
    # That hook not even made in the hooked array? Definitely a no!
    if(!isset($this->hooked[$hook_name]))
      return false;

    foreach($this->hooked[$hook_name] as $key => $hook)
      if($hook['callback'] == $callback)
        return true;

    return false;
  }

  /*

    Runs the specified hook, along with running the registered callbacks of the hook.

    @method public void run_hook(string $hook_name[, mixed $args = null]);
      string $hook_name - The name of the hook you are executing.
      mixed $args - Either a single argument or an array of arguments.
    returns void - Nothing is returned.

    NOTE: If you want to allow hooks to change something variable wise, pass the variable
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
print_r($this->hooked[$hook_name]);
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

    Used to sort the hooked array in order of importance.

    @method private array sort(array $array);
      array $array - The array to sort by the index 'importance'
    returns array - Returns the sorted array

    NOTE: Original function available at <http://mschat.net/forum/index.php?topic=1609.0>

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

    Adds an action through the API, adding an action allows plugins to add actions accessible through index.php.
    For example, say you add the action help, callback of view_help and the file help.php, whenever someone would
    access the page index.php?action=help the file help.php would be included and the function view_help would be
    executed.

    @method public bool add_action(string $action_name, callback $callback[, string $file = null]);
      string $action_name - The action name you want to register.
      callback $callback - The callback to be called (duh :P) when the action is accessed.
      string $file - The file to include before executing the callback, if null is supplied, no
                     file is executed.
    returns bool - TRUE is returned if the action was successfully added, FALSE if not, which means that
                   that action is already registered (check out the method remove_action).

  */
  public function add_action($action_name, $callback, $file = null)
  {
    if($this->action_registered($action_name))
      return false;

    $this->hooked_actions[$action_name] = array($callback, $file);
    return true;
  }

  /*

    Removes the specified action.

    @method public bool remove_action(string $action_name);
      string $action_name - The action to remove.
    returns bool - TRUE if the action was successfully removed, FALSE on failure, meaning that
                   the action wasn't registered.

  */
  public function remove_action($action_name)
  {
    if(!$this->action_registered($action_name))
      return false;

    unset($this->hooked_actions[$action_name]);
    return true;
  }

  /*

    Checks to see if the specified action is registered.

    @method public bool action_registered(string $action_name);
      string $action_name - The action name to check.
    returns bool - TRUE if the action is registered, FALSE if not.

  */
  public function action_registered($action_name)
  {
    return isset($this->hooked_actions[$action_name]);
  }

  /*

    Returns the requested action, the first index contains the callback, the second contains the file
    to be included before calling on the callback, unless it is null. However, if you leave the action
    name parameter blank, all registered actions are returned.

    @method public array return_action([string $action_name = null]);
      string $action_name - The action name to return information about.
    returns array - Returns the array containing a callback and file to include before calling on the
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

    Registers the specified sub-action to the action.

    @method public bool add_subaction(string $action_name, string $subaction_name, callback $callback[, string $file = null]);
      string $action_name - The action name you want to register the subaction with.
      string $subaction_name - The subaction name you want to register.
      callback $callback - The callback to be called (duh :P) when the subaction is accessed.
      string $file - The file to include before executing the callback, if null is supplied, no
                     file is executed.
    returns bool - TRUE is returned if the subaction was successfully added, FALSE if not, which means that
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

    Removes the specified sub-action from the action.

    @method public bool remove_subaction(string $action_name, string $subaction_name);
      string $action_name - The action from which you want to remove.
      string $subaction_name - The sub-action you want to remove.
    returns bool - TRUE if the sub-action was successfully removed, FALSE on failure, meaning that
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

    Checks to see if the specified sub-action is registered in the action.

    @method public bool subaction_registered(string $action_name, string $subaction_name);
      string $action_name - The action name to check within.
      string $subaction_name - The sub-action to check to see if it is registered.
    returns bool - TRUE if the subaction is registered, FALSE if not.

  */
  public function subaction_registered($action_name, $subaction_name)
  {
    return isset($this->hooked_subactions[$action_name][$subaction_name]);
  }

  /*

    Returns the requested subaction, the first index contains the callback, the second contains the file
    to be included before calling on the callback, unless it is null. However, if you leave the subaction
    name parameter blank, all registered subactions of action are returned.

    @method public array return_subaction(string $action_name[, string $subaction_name = null]);
      string $action_name - The action name to return retrieve subaction information about.
      string $subaction_name - The subaction to return information about.
    returns array - Returns the array containing a callback and file to include before calling on the
                    the callback, however, FALSE is returned if the subaction does not exist.

  */
  public function return_subaction($action_name, $subaction_name = null)
  {
    if(empty($subaction_name))
      return $this->hooked_subactions[$action_name];
    else
      return isset($this->hooked_subactions[$action_name][$subaction_name]) ? $this->hooked_subactions[$action_name][$subaction_name] : false;
  }
}

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
    WHERE runtime_error > 0
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
}
?>