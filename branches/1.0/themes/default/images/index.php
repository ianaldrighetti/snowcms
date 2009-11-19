<?php
# You shouldn't be in here...
# Lets see if we can get out of here...
if(file_exists('../config.php')) {
  require_once('../config.php');
  # I hope its the right file :P
  if(isset($base_url))
  header('Location: '. $base_url);
}
# Either way, exit!
exit;
?>