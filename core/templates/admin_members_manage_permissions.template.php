<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

    echo '
  <h1><img src="', theme()->url(), '/style/images/members_permissions-small.png" alt="" /> ', l('Manage permissions'), '</h1>
  <p>', l('The permissions of member groups can all be modified here. Simply click on the member group below to edit their permissions.'), '</p>';


    echo '
  <h3>', l('Member groups'), '</h3>
  <p>', implode(', ', api()->context['group_list']), '</p>';
?>