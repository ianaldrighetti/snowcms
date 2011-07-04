<?php
if(!defined('INSNOW'))
{
	die('Nice try...');
}

		echo '
    <h1>', l('Register an account'), '</h1>
    <p>', l('Here you can register an account on %s and get access to certain features that only registered members are allowed to use.', settings()->get('site_name')), '</p>';

    api()->context['form']->show('registration_form');
?>