<?php
#
# Forum English file for SnowCMS
# Created by the SnowCMS Dev Team
#       www.snowcms.com
#

if(!defined('InSnow'))
  die;

global $settings;

# Main Forum language vars
$l['forum_title'] = 'Forum Index - '. $settings['site_name'];
$l['forum_header'] = 'Forum Index';
$l['forum_desc'] = 'All of the site\'s forum boards.';

# No boards defined? D:
$l['forum_no_boards'] = 'Sorry, but either there are currently no boards or you are not allowed to access any of them.';

# Board stuffs...
$l['forum_board_old'] = 'No new posts';
$l['forum_board_new'] = 'New posts';
$l['posts'] = 'Posts';
$l['topics'] = 'Topics';
$l['last_post_by'] = 'Last post by';
$l['in'] = 'in';
$l['by'] = 'by';
$l['posted_at'] = 'Posted at';
$l['forum_board_no_posts'] = 'No posts';
$l['you_posted'] = 'Contains posts by you';
$l['last_post'] = 'Go to last post';
$l['read_times'] = 'Read %s times.';

# Board options
$l['forum_post_topic'] = 'New topic';
$l['forum_post_topic_title'] = 'Start a new topic';
$l['forum_post_poll'] = 'New poll';
$l['forum_post_poll_title'] = 'Start a new poll topic';

# Specific board variables
$l['forum_board_title'] = '%s - '. $settings['site_name'];

# Recent posts stuff
$l['recent_posts_title'] = 'Recent Posts - '. $settings['site_name'];
$l['recent_posts_header'] = 'Recent Posts';
$l['recent_posts_desc'] = 'Here is a list of the most recently made posts.';

# Forum error language vars
$l['forum_error_title'] = 'An error has occurred! - '. $settings['site_name'];

# Quick reply! Cool-e-o!
$l['topic_quick_reply'] = 'Quick reply';
$l['post'] = 'Post';
$l['preview'] = 'Preview';

$l['topic_who_viewing'] = 'Currently viewing this topic: (%u members and %u guests)';
$l['post_reply'] = 'Post reply';
?>