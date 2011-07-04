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
  Class: Tasks

  This is another tool which is available to developers. With this tool
  plugins can add/remove/edit tasks which can be set to be processed on
  a regular basis.

  Note:
    Please realize that the scheduled tasks may not always occur at the
    time you expect them too! These tasks are ran when a visitor comes to
    a page, and if their are any tasks which need to be done, then they
    are ran, but not always at the time you want! Also realize that admins
    can disable tasks altogether.
*/
class Tasks
{
  // Variable: tasks
  // The currently registered tasks array.
  private $tasks;

  // Variable: queue
  // Holds the tasks which need to be ran, if any!
  private $queue;

  /*
    Constructor: __construct

    Loads up the tasks which need to be ran and all the others, too.
  */
  public function __construct()
  {
    if(settings()->get('enable_tasks', 'bool'))
    {
      // Want to do this? Less work for me! ;)
      $handled = false;
      api()->run_hooks('tasks_construct', array(&$handled));

      if(empty($handled))
      {
        $this->queue = array();
        $this->tasks = array();

        // Load up all the tasks then queue the ones that need queueing.
        $result = db()->query('
          SELECT
            *
          FROM {db->prefix}tasks
          ORDER BY queued DESC',
          array(), 'tasks_query');

        // Any tasks, at all?
        if($result->num_rows() > 0)
        {
          // We use this to mark tasks which are ready to be ran!
          // (Which have a higher priority, if they are older ;))
          $queue_tasks = array();

          // There is a limit (maybe) on how many tasks to run at one time.
          $queued_tasks = 0;

          while($row = $result->fetch_assoc())
          {
            // Add a few things to the tasks information which we will use to our advantage later!
            $this->tasks[$row['task_name']] = array_merge($row, array('added' => false, 'updated' => false, 'deleted' => false));

            // Does it need queueing/running?
            if(!empty($row['enabled']) && (($row['last_ran'] + $row['run_every']) <= time_utc() || !empty($row['queued'])) && $queued_tasks < settings()->get('max_tasks', 'int', 1))
            {
              $this->queue[$row['task_name']] = $row;

              // Queue it if it isn't already...
              if(empty($row['queued']))
              {
                $queue_tasks[] = $row['task_name'];
                $this->tasks[$row['task_name']]['queued'] = 1;
              }

              $queued_tasks++;
            }
          }

          // Any newly queued tasks, perhaps?
          if(count($queue_tasks) > 0)
          {
            db()->query('
              UPDATE {db->prefix}tasks
              SET queued = 1
              WHERE task_name IN({array_string:queue_tasks})',
              array(
                'queue_tasks' => $queue_tasks,
              ), 'tasks_queue_query');
          }
        }

        // Register an action for running tasks :)
        api()->add_event('action=tasks', array($this, 'run'));

        // Register the save method to be called before shutdown ;)
        api()->add_hook('snow_exit', array($this, 'save'));

        // If we have any tasks which are in need of running, add a JavaScript file to the theme.
        if(count($this->queue) > 0)
        {
          api()->add_hook('post_init_theme', create_function('', '
                                              theme()->add_js_file(array(\'src\' => themeurl. \'/default/js/tasks.js\', \'defer\' => \'defer\'));'));
        }
      }
    }
  }

  /*
    Method: add

    Adds a task to the task system.

    Parameters:
      string $name - The name of the task (the handle).
      int $run_every - How often, in seconds, to run the task.
      callback $func - The function to call on when the task is ran.
      string $file - The file to include before calling on $func,
                     if any.
      string $location - The location of the specified file, if any,
                         such as plugin_dir (and plugindir will be
                         prepended when it is ran) or core_dir, etc.
      bool $enabled - Whether or not the task is enabled.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function add($name, $run_every, $func, $file = null, $location = null, $enabled = true)
  {
    global $func;

    if(!settings()->get('enable_tasks', 'bool', false))
    {
      return false;
    }

    $handled = null;
    api()->run_hooks('tasks_add', array(&$handled, $name, $run_every, $func, $file, $location, $enabled));

    // Did you do the deed?
    if($handled === null)
    {
      // Does this task already exist? In which case, you can't add it!
      if(empty($name) || $this->exists($name) || $run_every < 1)
      {
        return false;
      }

      // We don't do anything with the database right now, SO YEAH!!!
      $this->tasks[$func['strtolower']($name)] = array(
                                                   'task_name' => $func['strtolower']($name),
                                                   'last_ran' => 0,
                                                   'run_every' => (int)$run_every,
                                                   'file' => $file,
                                                   'location' => $location,
                                                   'func' => $func,
                                                   'queued' => 0,
                                                   'enabled' => !empty($enabled) ? 1 : 0,
                                                   'added' => true,
                                                   'updated' => false,
                                                   'deleted' => false,
                                                 );
      return true;
    }

    return !empty($handled);
  }

  /*
    Method: update

    Updates the specified task.

    Parameters:
      string $name - The name of the task (the handle).
      array $options - An array of options.

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function update($name, $options)
  {
    global $func;

    if(!settings()->get('enable_tasks', 'bool'))
    {
      return false;
    }

    // Can't update something that does not exist, that's for sure!
    if(!$this->exists($name))
    {
      return false;
    }

    $handled = null;
    api()->run_hooks('tasks_update', array(&$handled, $name, $options));

    if($handled === null)
    {
      $name = $func['strtolower']($name);

      // So, yeah, update the task, one option at a time!
      if(!empty($options['last_ran']) && $options['last_ran'] > 0)
      {
        $this->tasks[$name]['last_ran'] = (int)$options['last_ran'];
      }

      if(!empty($options['run_every']) && $options['run_every'] > 0)
      {
        $this->tasks[$name]['run_every'] = (int)$options['run_every'];
      }

      if(isset($options['file']))
      {
        $this->tasks[$name]['file'] = $options['file'];
      }

      if(isset($options['location']))
      {
        $this->tasks[$name]['location'] = $options['location'];
      }

      if(isset($options['func']))
      {
        $this->tasks[$name]['func'] = $options['func'];
      }

      if(isset($options['enabled']))
      {
        $this->tasks[$name]['enabled'] = !empty($options['enabled']) ? 1 : 0;
      }

      if(isset($options['queued']))
      {
        $this->tasks[$name]['queued'] = !empty($options['queued']) ? 1 : 0;
      }

      // Mark it as updated :)
      $this->tasks[$name]['updated'] = true;

      return true;
    }

    return !empty($handled);
  }

  /*
    Method: exists

    Checks to see if the specified task name (the handle) exists.

    Parameters:
      string $name - The name of the task (the handle).

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function exists($name)
  {
    global $func;

    if(!settings()->get('enable_tasks', 'bool', false))
    {
      return false;
    }

    $handled = null;
    api()->run_hooks('tasks_exists', array(&$handled, $name));

    if($handled === null)
    {
      // Simple check, really.
      return isset($this->tasks[$func['strtolower']($name)]) && !$this->tasks[$func['strtolower']($name)]['deleted'];
    }

    return !empty($handled);
  }

  /*
    Method: delete

    Marks the specified task for deletion.

    Parameters:
      string $name - The name of the task (the handle).

    Returns:
      bool - Returns true on success, false on failure.
  */
  public function delete($name)
  {
    global $func;

    if(!settings()->get('enable_tasks', 'bool', false))
    {
      return false;
    }

    $handled = null;
    api()->run_hooks('tasks_delete', array(&$handled, $name));

    if($handled === null)
    {
      // Simply mark it for deletion...
      $this->tasks[$func['strtolower']($name)]['deleted'] = true;

      return true;
    }

    return !empty($handled);
  }

  /*
    Method: run

    Runs the tasks which are currently in the queue.

    Parameters:
      bool $output_image - If set to true, a transparent pixel will
                           be displayed. It just makes some browsers happy :)

    Returns:
      void - This method returns nothing.
  */
  public function run($output_image = true)
  {
    if(!settings()->get('enable_tasks', 'bool', false))
    {
      return;
    }

    $handled = null;
    api()->run_hooks('tasks_run', array(&$handled, $output_image));

    if($handled === null)
    {
      // Do we have anything in the queue, for that matter?
      if(count($this->queue) == 0)
      {
        return;
      }

      // No going back now, that's for sure!
      ignore_user_abort(true);
      @set_time_limit(0);

      // Just incase ;-)
      define('IN_TASK', true);

      // Time to do those tasks, sweet!
      foreach($this->queue as $name => $task)
      {
        // Any file to include?
        if(!empty($task['file']))
        {
          // Is it an absolute path or..?
          if(realpath($task['file']) !== false)
          {
            // It is, not recommended, though.
            $include_file = realpath($task['file']);
          }
          elseif($task['location'] == 'core_dir' || empty($task['location']))
          {
            // Prepend core_dir to it.
            $include_file = realpath(coredir. '/'. $task['file']);
          }
          elseif($task['location'] == 'base_dir')
          {
            $include_file = realpath(basedir. '/'. $task['file']);
          }
          elseif($task['location'] == 'plugin_dir')
          {
            $include_file = realpath(plugindir. '/'. $task['file']);
          }
          elseif($task['location'] == 'theme_dir')
          {
            $include_file = realpath(plugindir. '/'. $task['file']);
          }

          // Does it exist..?
          if(isset($include_file) && !file_exists($include_file))
          {
            // No, it does not.
            unset($include_file);

            errors_handler('plugin', 'The file "'. htmlchars($task['file']). '" does not exist. Task name: "'. htmlchars($name). '"');

            // We will just skip this, okay? ;)
            continue;
          }

          // Include away!
          require_once($include_file);
          unset($include_file);
        }

        // How about a function to call?
        if(!empty($task['func']))
        {
          // Is it callable? Needs to be!
          if(!is_callable($task['func']))
          {
            errors_handler('plugin', 'The function "'. htmlchars($task['func']). '" is undefined. Task name: "'. htmlchars($name). '"');

            // Skip!!!
            continue;
          }

          // Call it!
          $task['func']();
        }

        // If we got here, then the task was ran successfully, and we can make it no longer queued.
        $this->update($name, array(
                               'last_ran' => time_utc(),
                               'queued' => 0
                             ));
      }

      // So, dsisplay a transparent pixel?
      if(!empty($output_image))
      {
        if(ob_get_length() > 0)
        {
          @ob_clean();
        }

        header('Pragma: no-cache');
        header('Expires: -1');
        header('Content-Type: image/png');

        echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9oBHwckJmzBiU4AAAAIdEVYdENvbW1lbnQA9syWvwAAAA1JREFUCNdjYGBgYAAAAAUAAV7zKjoAAAAASUVORK5CYII=');
        exit;
      }

      return true;
    }

    return !empty($handled);
  }

  /*
    Method: save

    Saves all the changes made to the tasks. Should not be called
    on by anybody! This is done automatically.

    Parameters:
      none

    Returns:
      void - This method returns nothing.
  */
  public function save()
  {
    if(settings()->get('enable_tasks', 'bool', false))
    {
      $handled = null;
      api()->run_hooks('tasks_destruct', array(&$handled));

      if($handled === null && count($this->tasks) > 0)
      {
        // Arrays of goodness!
        $deleted = array();
        $changed = array();

        foreach($this->tasks as $name => $task)
        {
          // Deleted? As there is no point changing something that will be deleted, right?
          if(!empty($task['deleted']))
          {
            $deleted[] = $name;
          }
          elseif(!empty($task['added']) || !empty($task['updated']))
          {
            // Doesn't matter whether it has been added or changed...
            $changed[] = array(
                           $name, $task['last_ran'], $task['run_every'],
                           $task['file'], $task['location'], $task['func'],
                           $task['queued'], $task['enabled'],
                         );
          }
        }

        // Anything need to be deleted? Do so!
        if(count($deleted) > 0)
        {
          db()->query('
            DELETE FROM {db->prefix}tasks
            WHERE task_name IN({string_array:deleted})
            LIMIT {int:num_deleted}',
            array(
              'deleted' => $deleted,
              'num_deleted' => count($deleted),
            ), 'tasks_destruct_delete_query');
        }

        // How about changed?
        if(count($changed) > 0)
        {
          db()->insert('replace', '{db->prefix}tasks',
            array(
              'task_name' => 'string', 'last_ran' => 'int', 'run_every' => 'int',
              'file' => 'string', 'location' => 'string', 'func' => 'string',
              'queued' => 'int', 'enabled' => 'int',
            ),
            $changed,
            array('task_name'), 'tasks_destruct_changed_query');
        }
      }
    }
  }
}

/*
	Function: tasks

	Returns the current instance of the <Tasks> object, which can be used to
	add and remove tasks. If no instance of the <Tasks> class exists, one will
	be automatically created.

	Parameters:
		none

	Returns:
		object
*/
function tasks()
{
	if(!isset($GLOBALS['tasks']))
	{
		$GLOBALS['tasks'] = api()->load_class('Tasks');

		api()->run_hooks('post_init_tasks');
	}

  return $GLOBALS['tasks'];
}

/*
	Functions: tasks_run

	Runs tasks when accessed via index.php?action=tasks.

	Parameters:
		none

	Returns:
		void

	Note:
		This function should not be called directly, and will be called when a
		user's browser accesses index.php?action=tasks, which is loaded by
		tasks.js. This function will display a transparent pixel.
*/
function tasks_run()
{
	api()->run_hooks('pre_run_tasks');

	// Run those tasks... If there are any.
	tasks()->run();

	api()->run_hooks('post_run_tasks');
}
?>