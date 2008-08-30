<?php
// default/Register.template.php by SnowCMS Dev's

if(!defined('Snow'))
  die("Hacking Attempt...");
  
function Main() {
global $cmsurl, $settings, $l, $user;
  echo '
  <h1>'.$l['register_header'].'</h1>
  
  <p>'.$l['register_details'].'</p>
  <script type="text/javascript" src="includes/jquery.js"></script>
  <script type="text/javascript" src="includes/jquery-pstrength.js"></script>
  <script type="text/javascript">$(function() {$(".password").pstrength();});</script>'
  /*
  This causes a notice to appear.
  */ .
  '<form action="'.$cmsurl.'index.php?action=register" method="post">
    <p><input type="hidden" name="register" value="true" /></p>
    <fieldset>
      <table>';
      if(count($settings['page']['error'])>0) {
      echo '
      <tr>
        <td><p class="error">';  
        foreach($settings['page']['error'] as $error) {
          echo $error.'<br />';
        }
      echo '
        </p></td>
      </tr>';
      }
      echo '  
        <tr>
          <td>'.$l['register_username'].'</td><td><input name="username" type="text" value="'.@$_REQUEST['username'].'"/></td>
        </tr>
        <tr>
          <td>'.$l['register_password'].'</td><td><input class="password" name="password" type="password" /></td>
        </tr>
        <tr>
          <td>'.$l['register_verify_password'].'</td><td><input name="vpassword" type="password" /></td>
        </tr>
        <tr>
          <td>'.$l['register_email'].'</td><td><input name="email" type="text" value="'.@$_REQUEST['email'].'" /></td>
        </tr>
        <tr>
          <td>'.$l['register_captcha'].'</td><td><input name="captcha" type="text"/></td>
        </tr>
        <tr>
          <td>&nbsp;</td><td colspan="2"><img src="'.$cmsurl.'image.php" alt="CAPTCHA" /></td>
        </tr>
        ';
      if ($settings['enable_tos'])
        echo '<tr>
          <td colspan="2"><input type="checkbox" name="tos" id="tos" /> <label for="tos">'.
          str_replace('%site%',$settings['site_name'],
          str_replace('%link%','<a href="'.$cmsurl.'index.php?action=tos" onclick="window.open(this.href); return false;">',
          str_replace('%/link%','</a>',
          $l['register_tos']))).'</label></td>
        </tr>';
      echo '<tr>
          <td colspan="2"><input type="submit" value="'.$l['register_button'].'"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}
function Success() {
global $cmsurl, $settings, $l, $user;
  // Woo! You registered, you can now login!
  echo '
  <h1>'.$l['register_header'].'</h1>
  
  <p>'.str_replace('%username%',$settings['page']['username'],$l['register_success']).'</p>';
}

function SuccessBut1() {
global $cmsurl, $settings, $l, $user;
  // Dang, you got to get it email activated =|
  echo '<h1>'.$l['register_header'].'</h1>
    ';
  
  if(empty($settings['page']['error'])) {
    echo '<p>'.$l['register_successbut1'].'</p>
    
    <form action="'.$cmsurl.'index.php?action=register3" method="post">
    <p><input name="email" value="'.$settings['register']['email'].'" /> <input type="submit" value="'.$l['register_resend'].'"></p>
    </form>';
  }
  else {
    echo '<p>'.$l['register_failed'].' Error: '.$settings['page']['error'].'</p>';
  }
}

function SuccessBut2() {
global $cmsurl, $settings, $l, $user;
  // Admins must activate your account before you can login
  echo '<h1>'.$l['register_header'].'</h1>
  
  <p>'.$l['register_successbut2'].'</p>';
}

function AForm() {
global $cmsurl, $settings, $l, $user;
  // This is the activation form, so of course, you can activate your account via email
  echo '
  <h1>'.$l['register_header'].'</h1>
  
  <p>', $l['activate_desc'], '</p>
  <form action="', $cmsurl, 'index.php?action=activate" method="post">
    <fieldset>
      <table>';
        if(count($settings['errors'])) {
        echo '
        <tr>
          <td colspan="2"><p class="error">';
          foreach($settings['errors'] as $error)
            echo $error . '<br />';
        echo '
          </p></td>
        </tr>';
        }
      echo '        
        <tr>
          <td>', $l['activate_username'], '</td><td><input name="u" type="text" value="', $settings['user'], '"/></td>
        </tr>
        <tr>
          <td>', $l['activate_acode'], '</td><td><input name="acode" type="text" value="', $settings['acode'], '"/></td>
        </tr>
        <tr>
          <td colspan="2"><input name="activate" type="submit" value="', $l['activate_button'], '"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}

function ASuccess() {
global $cmsurl, $settings, $l, $user;
  // Email Activation was a Success!
  echo '
  <h1>'.$l['register_header'].'</h1>
  
  <p>', $l['activate_account_activated'], '</p>';
}

function Failure() {
global $l;
  // Dang, it failed =[
  echo '
  <h1>'.$l['register_header'].'</h1>
  
  <p>'.$l['register_error_activation_email'].'</p>';
}
?>