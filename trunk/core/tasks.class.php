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
  # Variable: queue
  # Holds the tasks which need to be ran, if any!
  private $queue;

  /*
    Constructor: __construct
  */
  public function __construct()
  {
    global $api, $db, $settings;

    $this->queue = array();

    # Queue up the tasks that need running...
    $result = $db->query('
      SELECT
        task_name, last_ran, run_every, file, func, enabled
      FROM {db->prefix}tasks
      WHERE enabled = 1 AND ((last_ran + run_every) < {int:cur_time} OR queued = 1)
      LIMIT {int:max_tasks}',
      array(
        'cur_time' => time_utc(),
        'max_tasks' => $settings->get('max_tasks', 'int', 1),
      ), 'tasks_query');

    if($result->num_rows() > 0)
    {
      # Load'em up!
      $queue_tasks = array();
      while($row = $result->fetch_assoc())
      {
        $this->queue[] = $row;
        $queue_tasks[] = $row['task_name'];
      }

      # Queue them in the database :)
      $db->query('
        UPDATE {db->prefix}tasks
        SET queued = 1
        WHERE task_name IN({string_array:queue_tasks})
        LIMIT {int:num_tasks}',
        array(
          'queue_tasks' => $queue_tasks,
          'num_tasks' => count($queue_tasks),
        ), 'tasks_queue_query');
    }

    # Register an action for running tasks :)
    $api->add_action('tasks', array($this, 'run'));
  }

  /*
    Method: run
  */
}

function init_tasks()
{
  global $api, $settings;

  if($settings->get('enable_tasks', 'bool'))
    $tasks = $api->load_class('Tasks');
}
?>