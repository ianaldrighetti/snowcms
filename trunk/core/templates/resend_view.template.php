<?php
    echo '
      <h1>', l('Resend your activation email'), '</h1>
      <p>', l('If for some reason you didn\'t receive your activation email, you can request to have it resent by entering your username below.'), '</p>';

    if(strlen(api()->apply_filters('resend_message', '')) > 0)
    {
      echo '
      <div id="', api()->apply_filters('resend_message_id', 'resend_success'), '">
        ', api()->apply_filters('resend_message', ''), '
      </div>';
    }

    api()->context['form']->show('resend_form');
?>