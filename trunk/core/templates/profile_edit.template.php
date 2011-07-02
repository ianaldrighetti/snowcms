<?php
    echo '
      <h1>', l('Editing %s\'s profile', api()->context['member_info']['name']), '</h1>
      <p>', l('You are currently editing %s\'s profile. <a href="%s" title="Back to profile">Back to profile</a>.', api()->context['member_info']['name'], baseurl. '/index.php?action=profile'. (api()->context['member_info']['id'] != member()->id() ? '&amp;id='. api()->context['member_info']['id'] : '')), '</p>';

    api()->context['form']->show('member_edit_'. api()->context['member_info']['id']);
?>