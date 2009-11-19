<?php
#
# Forum English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Personal messages stuff
$l['pm_title'] = 'Personal Messages - '. $settings['site_name'];
$l['pm_header'] = 'Personal Messages';
$l['pm_desc'] = 'Viewing your personal messages in your %s folder.';
$l['pm_compose'] = 'Compose';
$l['pm_folder_inbox'] = 'Inbox';
$l['pm_folder_outbox'] = 'Outbox';
$l['pm_folder_archive'] = 'Archive';
$l['pm_folder_deleted'] = 'Recycle Bin';
$l['pm_space_used'] = 'Space Used';
$l['pm_subject'] = 'Subject';
$l['pm_recipients'] = 'Recipients';
$l['pm_sender'] = 'Sender';
$l['pm_sent'] = 'Time Sent';
$l['pm_flagged'] = 'Flagged';
$l['pm_unflagged'] = 'Unflagged';
$l['pm_archive'] = 'Archive';
$l['pm_unarchive'] = 'Remove From Archive';
$l['pm_delete_recycle'] = 'Delete to Recycle Bin';
$l['pm_delete_permanently'] = 'Delete Permanently';
$l['pm_undelete'] = 'Remove From Recycle Bin';
$l['pm_folder_empty_title'] = 'Personal Messages - '. $settings['site_name'];
$l['pm_folder_empty_inbox'] = 'Your inbox contains no personal messages.';
$l['pm_folder_empty_outbox'] = 'Your outbox contains no personal messages.';
$l['pm_folder_empty_archive'] = 'Your archive contains no personal messages.';
$l['pm_folder_empty_deleted'] = 'Your recycle bin contains no personal messages.';
$l['pm_read_receipt_subject'] = 'Read: %s';
$l['pm_read_receipt_body'] = 'The PM you sent to %s has been read.';
$l['personalmessages_folder_threaded_avatar'] = '%s\'s avatar';

# Composing PM stuff
$l['pm_compose_title'] = 'Compose a PM - '. $settings['site_name'];
$l['pm_compose_header'] = 'Compose a PM';
$l['pm_compose_desc'] = 'Compose a new personal message.';
$l['pm_compose_recipients'] = 'Recipients';
$l['pm_compose_sub_recipients'] = 'Separate multiple usernames with commas.';
$l['pm_compose_subject'] = 'Subject';
$l['pm_compose_message'] = 'Message';
$l['pm_compose_outbox'] = 'Save in outbox';
$l['pm_compose_sub_outbox'] = 'Save a copy of message in your outbox.';
$l['pm_compose_read_receipt'] = 'Request read receipt';
$l['pm_compose_sub_read_receipt'] = 'Receive a message when PM is read.';
$l['pm_compose_send'] = 'Send';
$l['pm_compose_sent'] = 'Message has been sent successfully.';

# Replying to PM stuff
$l['pm_reply_title'] = 'Replying to a PM - '. $settings['site_name'];
$l['pm_reply_header'] = 'Replying to a PM';
$l['pm_reply_desc'] = 'Reply to a personal message.';
$l['pm_reply_recipients'] = 'Recipients';
$l['pm_reply_subject'] = 'Subject';
$l['pm_reply_re'] = 'Re: ';
$l['pm_reply_message'] = 'Message';
$l['pm_reply_outbox'] = 'Save in outbox';
$l['pm_reply_sub_outbox'] = 'Save a copy of message in your outbox.';
$l['pm_reply_read_receipt'] = 'Request read receipt';
$l['pm_reply_sub_read_receipt'] = 'Receive a message when PM is read.';
$l['pm_reply_send'] = 'Reply';
$l['pm_reply_sent'] = 'Message has been sent successfully.';

# Replying to self
$l['pm_reply_self_title'] = 'Replying to a PM - '. $settings['site_name'];
$l['pm_reply_self_header'] = 'Replying to a PM';
$l['pm_reply_self_desc'] = 'Reply to a personal message.';
$l['pm_reply_self_error'] = 'You cannot reply to yourself.';

# Forwarding PM stuff
$l['pm_forward_title'] = 'Forwarding a PM - '. $settings['site_name'];
$l['pm_forward_header'] = 'Forwarding a PM';
$l['pm_forward_desc'] = 'Reply to a personal message.';
$l['pm_forward_recipients'] = 'Recipients';
$l['pm_forward_subject'] = 'Subject';
$l['pm_forward_fwd'] = 'Fwd: ';
$l['pm_forward_message'] = 'Message';
$l['pm_forward_outbox'] = 'Save in outbox';
$l['pm_forward_sub_outbox'] = 'Save a copy of message in your outbox.';
$l['pm_forward_read_receipt'] = 'Request read receipt';
$l['pm_forward_sub_read_receipt'] = 'Receive a message when PM is read.';
$l['pm_forward_send'] = 'Reply';
$l['pm_forward_sent'] = 'Message has been sent successfully.';

# Viewing a PM stuff
$l['pm_view_title'] = 'Viewing a Message - '. $settings['site_name'];
$l['pm_view_header'] = 'Viewing a Message';
$l['pm_view_desc'] = 'Viewing one of your personal messages.';
$l['pm_view_subject'] = 'Subject';
$l['pm_view_recipients'] = 'Recipients';
$l['pm_view_sender'] = 'Sender';
$l['pm_view_read_receipt'] = 'A read receipt has been requested by %s.';
$l['pm_view_read_receipt_send'] = 'Send';
$l['pm_view_read_receipt_deny'] = 'Deny';
$l['pm_view_reply'] = 'Reply';
$l['pm_view_reply_all'] = 'Reply All';
$l['pm_view_forward'] = 'Forward';
$l['pm_view_delete'] = 'Delete';
$l['pm_view_unread'] = 'Mark Unread';

# Show not allowed stuff
$l['pm_not_allowed_title'] = 'Personal Messages - '. $settings['site_name'];
$l['pm_not_allowed_header'] = 'Personal Messages';
$l['pm_not_allowed_desc'] = 'You are not allowed to use the PM system.';

# Composing PM errors
$l['pm_compose_error_recipient_size_male'] = 'Failed to send to %s, his inbox is full.';
$l['pm_compose_error_recipient_size_female'] = 'Failed to send to %s, her inbox is full.';
$l['pm_compose_error_recipient_size_unknown'] = 'Failed to send to %s, their inbox is full.';
$l['pm_compose_error_recipients_none'] = 'You didn\'t enter any recipients.';
$l['pm_compose_error_recipients_invalid'] = 'One or more recipients were invalid.';
$l['pm_compose_error_recipients_self'] = 'You can\'t send the PM to yourself.';
$l['pm_compose_error_subject'] = 'Your subject is too short.';
$l['pm_compose_error_body'] = 'Your message is too short.';
$l['pm_compose_error_outbox'] = 'Insufficient space to save in your outbox.';
?>