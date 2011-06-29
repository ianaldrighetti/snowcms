<?php
      echo '
      <h1>', l('Password Required'), '</h1>
      <p>', l('For security purposes, please enter your account password below. This is done to help make sure that you are who you say you are.'), '</p>
      <script type="text/javascript">
        s.onload(function() { document.getElementById(\'admin_prompt_form_admin_verification_password_input\').focus(); });
      </script>';

      api()->context['form']->show('admin_prompt_form');
?>