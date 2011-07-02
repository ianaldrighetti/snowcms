<?php
	echo '
  <div id="sidebar">
    <h3 style="margin-top: 0px !important;">', l('Notifications'), '</h3>';

    // If you would like to add a notification, simply add a filter to admin_notifications and
    // add an array to the passed array containing a subject, title (optional), and/or href (optional).
    $notifications = api()->apply_filters('admin_notifications', array());

    if(is_array($notifications) && count($notifications))
    {
      foreach($notifications as $notification)
      {
        echo '
      <p class="notification">', (!empty($notification['href']) ? '<a href="'. $notification['href']. '"'. (!empty($notification['title']) ? ' title="'. $notification['title']. '"' : ''). '>' : (!empty($notification['title']) ? '<span title="'. $notification['title']. '">' : '')), $notification['subject'], (!empty($notification['href']) ? '</a>' : (!empty($notification['title']) ? '</span>' : '')), '</p>';
      }
    }
    else
    {
      echo '
    <p class="notification">', l('No notifications.'), '</p>';
    }

    echo '
    <h3>News from <a href="http://www.snowcms.com/" target="_blank" title="SnowCMS">SnowCMS</a>:</h3>';

    // Loop through them all, if any, anyways.
    if(count(api()->context['current_news']) > 0)
    {
      foreach(api()->context['current_news'] as $news)
      {
        echo '
      <p class="news_subject" title="', $news['date'], '">', (!empty($news['href']) ? '<a href="'. $news['href']. '" target="_blank">' : ''), $news['subject'], (!empty($news['href']) ? '</a>' : ''), '</p>
      <p class="news_content">', $news['content'], '</p>';
      }
    }
    else
    {
      echo '
    <p>', l('No news to display.'), '</p>';
    }

    echo '
  </div>
  <div id="main">';

    if(is_array($GLOBALS['icons']) && count($GLOBALS['icons']) > 0)
    {
      $first = true;
      foreach($GLOBALS['icons'] as $header => $icon)
      {
        echo '
    <h1', (!empty($first) ? ' style="margin-top: 0px !important;"' : ''), '>', $header, '</h1>
    <table class="icons">
      <tr>';

        // Time to show the actual icons.
        $length = count($icon);
        for($i = 0; $i < $length; $i++)
        {
          echo '
        <td><a href="', $icon[$i]['href'], '" title="', $icon[$i]['title'], '"><img src="', $icon[$i]['src'], '" alt="" title="', $icon[$i]['title'], '" /><br />', $icon[$i]['label'], '</a></td>';

          if(($i + 1) % 6 == 0 && isset($icon[$i + 1]))
          {
            echo '
      </tr>
    </table>
    <table class="icons">
      <tr>';
          }
        }

        echo '
      </tr>
    </table>';

        $first = false;
      }
    }
    else
    {
      echo '
    <h1 style="margin-top: 0px !important;">', l('Error'), '</h1>
    <p>', l('Sorry, but it appears that the icons have been malformed.'), '</p>';
    }

    echo '
  </div>
  <div class="break">
  </div>';
?>