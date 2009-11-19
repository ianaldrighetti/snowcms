function runTask()
{
  // Super simple function :P Just runs a task :)
  _.X(base_url + '/index.php?action=tasks', function(data){}, '');
}
runTask();