<?php
      echo '
      <h1>', l('Registration Successful'), '</h1>
      <p>', l('Thank you for registering %s.', api()->context['member_info']['name']), ' ', (api()->context['member_info']['is_activated'] ? l('You may now proceed to <a href="%s">log in to your account</a>.', baseurl. '/index.php?action=login') : (settings()->get('registration_type') == 1 ? l('The site requires an administrator to activate new accounts. You will receive an email once your account has been activated.') : (settings()->get('registration_type') == 2 ? l('The site requires you to activate your account via email, so check you email (%s) for your activation link.', api()->context['member_info']['email']) : api()->apply_filters('registration_message_other', '')))), '</p>';
?>