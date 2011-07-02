<?php
    echo '
      <h1>', l('Request a password reset'), '</h1>
      <p>', l('Did you forget your password? No problem! Just enter your username into the form below, and you can then start the process of resetting your password. Due to how the passwords are stored, we cannot give you your currently stored password.'), '</p>';

    if(strlen(api()->apply_filters('reminder_message', '')) > 0)
    {
      echo '
      <div id="', api()->apply_filters('reminder_message_id', 'reminder_success'), '">
        ', api()->apply_filters('reminder_message', ''), '
      </div>';
    }

    api()->context['form']->show('reminder_form');
?>