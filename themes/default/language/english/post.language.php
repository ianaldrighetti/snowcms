<?php
#
#  Post English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

$l['post_new_topic'] = 'Post a new topic - '. $settings['site_name'];
$l['post_new_topic_header'] = 'Post a new topic';
$l['posting_in'] = 'You are currently posting a new topic in %s.';

$l['post_reply'] = 'Post a reply - '. $settings['site_name'];
$l['post_reply_header'] = 'Post a reply';
$l['posting_reply_to'] = 'You are currently posting a reply to %s.';

# Poll language variables.
$l['question'] = 'Question:';
$l['option'] = 'Option %u:';
$l['add_option'] = 'Add option';
$l['poll_settings'] = 'Poll settings:';
$l['votes_per_user'] = 'Votes allowed per user:';
$l['poll_expires'] = 'Poll expires in how many days?';
$l['poll_expires_subtext'] = '0 for never expires';
$l['allow_vote_change'] = 'Can users to change their vote?';
$l['yes'] = 'Yes';
$l['results_anyone'] = 'Results viewable by anyone.';
$l['results_after_vote'] = 'Results viewable after vote is cast.';
$l['results_after_expired'] = 'Results viewable only after poll has expired.';

$l['subject'] = 'Subject:';
$l['loading'] = 'Loading...';

$l['re'] = 'Re:';

# BBCode titles...
$l['bold'] = 'Bold';
$l['italic'] = 'Italic';
$l['underline'] = 'Underline';
$l['strikethrough'] = 'Strikethrough';

$l['align_pre'] = 'Preformatted text';
$l['align_left'] = 'Left align';
$l['align_center'] = 'Center align';
$l['align_right'] = 'Right align';

$l['select_font'] = 'Select a font';
$l['select_size'] = 'Select font size';

$l['image'] = 'Insert image';
$l['link'] = 'Insert a link';
$l['email'] = 'Insert an email link';

$l['superscript'] = 'Insert superscript';
$l['subscript'] = 'Insert subscript';

$l['code'] = 'Insert code';
$l['quote'] = 'Insert quote';

# Buttons! Get your buttons here! XD.
$l['post'] = 'Post';
$l['edit_post'] = 'Edit post';
$l['preview'] = 'Preview';
$l['hide_preview'] = 'Hide Preview';

# Additional options...
$l['additional_options'] = 'Additional options';
$l['return_to_message'] = 'Return to this message';
$l['dont_parse_bbc'] = 'Don\'t parse BBCode';
$l['dont_parse_smileys'] = 'Don\'t parse smileys';
$l['sticky_topic'] = 'Sticky this topic';
$l['lock_topic'] = 'Lock this topic';
$l['lock_message'] = 'Lock message from further author edits';

# Preview post XD.
$l['preview_error'] = 'The forum is disabled, so previewing has also been disabled.';

# Some posting errors
$l['question_empty'] = 'The question was left empty.';
$l['options_empty'] = 'No options have been filled out.';
$l['options_to_many'] = 'Sorry, but you can only have 256 poll options.';
$l['invalid_votes_per_user'] = 'Invalid number of votes per user supplied.';
$l['to_many_votes_per_user'] = 'To few options and to many votes per user set.';
$l['invalid_poll_expires'] = 'Invalid time for poll expiration entered.';
$l['invalid_results_access'] = 'Invalid results access option chosen.';
$l['subject_empty'] = 'The subject was left empty.';
$l['message_empty'] = 'The message body was left empty.';

# Actually saving the post errors :P
$l['invalid_post_token'] = 'Sorry, but your session timed out. Please re-submit your post.';
$l['new_posts_please_revise'] = '%u new posts have been made since you attempted to post your, please edit your post accordingly.';

# Last post listing vars.
$l['last_posts'] = 'Showing the last %u posts.';
$l['posted_by'] = 'Posted by';
$l['on'] = 'on';

# Permission check failed
$l['error_screen_title'] = 'An error has occurred';
$l['error_screen_header'] = 'An Error has Occurred';
$l['error_screen_desc'] = 'Sorry, but you are not allowed to view whatever you are attempting to access.';
?>