<?php
#
# Forum English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Composing email stuff
$l['email_compose_title'] = 'Emailing %s - '. $settings['site_name'];
$l['email_compose_header'] = 'Emailing %s';
$l['email_compose_desc'] = 'Sending an email to %s.';
$l['email_compose_subject'] = 'Subject';
$l['email_compose_message'] = 'Message';
$l['email_compose_submit'] = 'Send';
$l['email_compose_sent'] = 'Email has been sent successfully.';
$l['email_compose_error_subject_none'] = 'You did not enter a subject';
$l['email_compose_error_body_none'] = 'You did not enter a message.';
?>