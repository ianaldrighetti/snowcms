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
# Tasks are a new feature in the SnowCMS v1.0 line, these Tasks act like
# crons like a *nix system. These tasks are periodically done and you
# can add, remove, disable and enable tasks in the Admin CP and you can
# have it set to how often you want the task to be ran. You give it a file
# and if you want a function to be required and called on and therefore ran
# which is great :P BUT the thing is, you cannot depend on these for the sole
# reason that people who visit your SnowCMS powered site technically run these
# as they are queued and then called on by a JS function in the header of
# your theme file without your users knowing they are helping out. However
# if you have a fairly active site, the chances are that your tasks will be
# ran hopefully about the time you want, BUT NO GUARANTEE!
#
# void tasks_run();
#   - Accessed by ?action=tasks which is accessed (or should be) by an AJAX
#     call
#   - This function will only run as many tasks at a time as you set in the
#     Admin Control Panel
#   - Only tasks that are queued and enabled are actually ran.
#
# void tasks_tables();
#   - This is one of the premade SnowCMS Tasks, this is setup to optimize
#     all tables in your SnowCMS website every so often, in the default
#     installation it will do this every 12 hours, but you can change that
#     of course.
#
# void tasks_files();
#   - Fetches the latest news and version from the SnowCMS website and saves
#     it in the database... We want don't want to have your Admin CP page take
#     forever to load if the SnowCMS Official site is down ;) Aren't you glad
#     we care so much? ^__^.
#

function tasks_run()
{
  global $db, $page, $settings, $source_dir;

  # So now... We can only do this if their Session is flagged :P
  if(!empty($_SESSION['run_task']))
  {
    # No stopping now.
    ignore_user_abort(true);

    # Ok, before we go on, we want to unflag them again.
    $_SESSION['run_task'] = false;

    # Now select tasks out, with of course the amount they want.
    # If its 0, well then, unlimited =P
    $num_tasks = $settings['num_tasks'] ? (int)$settings['num_tasks'] : false;

    # Now get them out.
    $result = $db->query("
      SELECT
        tsk.task_id, tsk.file, tsk.call_func, tsk.queued, tsk.enabled
      FROM {$db->prefix}tasks AS tsk
      WHERE tsk.queued = 1 AND tsk.enabled = 1
      %limit_clause",
      array(
        'limit_clause' => array('raw', (!empty($num_tasks) ? 'LIMIT '. $num_tasks : '')),
      ));

    # We won't run them quite yet, lets get them first...
    $tasks = array();
    $task_ids = array();

    # Now loop :)
    while($row = $db->fetch_assoc($result))
    {
      # Save the task ids and information...
      $task_ids[] = $row['task_id'];
      $tasks[] = array('id' => $row['task_id'], 'file' => $row['file'], 'func' => $row['call_func']);
    }

    # Before we run the tasks, we need to say the last time they
    # ran and that they are not queued. The reason this is done
    # before they are actually ran is because otherwise the task
    # might be ran more then once by more then one person if the task
    # isn't something done in a couple seconds.
    $db->query("
      UPDATE {$db->prefix}tasks
      SET last_ran = %last_ran, queued = 0
      WHERE task_id IN(%task_ids)",
      array(
        'last_ran' => array('int', time_utc()),
        'task_ids' => array('int_array', $task_ids)
      ));

    # Now its time to run the tasks.
    # Lets give it some time.
    @set_time_limit(0);

    # Oh yeah, if you want, you could do the if(!defined('InSnowTask;)) die;
    # thing if you don't want direct access to your task file depending
    # upon how you have set it up.
    define('InSnowTask', true);

    foreach($tasks as $task)
    {
      # So does the file exist..?
      if(file_exists($task['file']) || file_exists($source_dir. '/'. $task['file']))
      {
        # Okay, that means we can get it.
        require_once(file_exists($task['file']) ? $task['file'] : $source_dir. '/'. $task['file']);

        # A function need to be called?
        if(!empty($task['func']) && function_exists($task['func']))
          $task['func']();
      }
      # Still no error logging for tasks, soon!
    }
  }
  # Now just, die... :P
  die;
}

function tasks_tables()
{
  global $db, $db_name;

  # Only through tasks ;)
  if(!defined('InSnowTask'))
    die;

  # So just optimize all the tables...
  $db->optimize_table(true);
}

function tasks_files()
{
  global $db;

  # Go away!
  if(!defined('InSnowTask'))
    die;

  # Lets try to get the current version...
  $current_version = web_fetch('http://download.snowcms.com/news/v1.x-line/latest.php');

  # Now the latest news ^___^
  $current_news = web_fetch('http://download.snowcms.com/news/v1.x-line/news.php');

  # Update them if they aren't completely blank ;)
  $updates = array();
  if(!empty($current_version))
    $updates['current_version'] = $current_version;

  if(!empty($current_news))
    $updates['current_news'] = $current_news;

  # Now update them settings :P
  update_settings($updates);
}
?>