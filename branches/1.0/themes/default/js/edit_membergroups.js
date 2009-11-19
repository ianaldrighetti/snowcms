var options = new Array(total_groups);

function editMembergroup(group_id, group_order)
{
  // Get the current member group name, and such.
  _.X(base_url + '/index.php?action=interface;sa=editMembergroups', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Yay! No errors...
        var handle_name_plural = _.G('group_name_plural_' + info['group_id']);
        var handle_name_singular = _.G('group_name_singular_' + info['group_id']);
        var handle_color = _.G('group_color_' + info['group_id']);
        var handle_min_posts = _.G('group_min_posts_' + info['group_id']);
        var handle_options = _.G('group_options_' + info['group_id']);

        // Store the options data for safe keeping in a global variable
        options[group_order] = handle_options.innerHTML;

        // Empty their innards! Muahaha!
        handle_name_plural.innerHTML = '';
        handle_name_singular.innerHTML = '';
        handle_color.innerHTML = '';
        handle_min_posts.innerHTML = '';
        handle_options.innerHTML = '';

        // Create an input for our plural name textbox
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'group_name_plural_edit_' + info['group_id'];
        input.value = info['group_name_plural'];

        // Add the input
        handle_name_plural.appendChild(input);
        
        // Another input for our singular name textbox
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'group_name_singular_edit_' + info['group_id'];
        input.value = info['group_name_singular'];

        // Add the input
        handle_name_singular.appendChild(input);

        // Create a checkbox for color textbox
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'group_color_edit_' + info['group_id'];
        input.value = info['group_color'];
        input.style.width = '80px';

        // Add the input
        handle_color.appendChild(input);
        
        // Create a checkbox for minimum posts
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'group_min_posts_edit_' + info['group_id'];
        input.value = info['group_min_posts'];
        input.style.width = '50px';
        
        // Add the input
        handle_min_posts.appendChild(input);
        
        // Make our save button...
        var input = document.createElement('input');
        input.id = 'group_save_' + info['group_id'];
        input.type = 'submit';
        input.value = save_text;

        // Add this one too!
        handle_options.appendChild(input);

        // How about a cancel? =P
        var input = document.createElement('input');
        input.id = 'group_cancel_' + info['group_id'];
        input.type = 'submit';
        input.value = cancel_text;

        // Cancel please :)
        handle_options.appendChild(input);

        // A couple things for on click ;)
        _.on(_.G('group_save_' + info['group_id']), 'click', function() { saveMembergroup(info['group_id'], group_order); });
        _.on(_.G('group_cancel_' + info['group_id']), 'click', function() { cancelMembergroup(info['group_id'], group_order, info['group_name_plural'], info['group_name_singular'], info['group_color'], info['group_min_posts']); });
      }
    }, 'group_id=' + group_id);
}

function saveMembergroup(group_id, group_order)
{
  // Get our new name... If at all...
  var group_name_plural = _.G('group_name_plural_edit_' + group_id).value;
  var group_name_singular = _.G('group_name_singular_edit_' + group_id).value;
  var group_color = _.G('group_color_edit_' + group_id).value;
  var min_posts = _.G('group_min_posts_edit_' + group_id).value;
  
  // Query your site...
  _.X(base_url + '/index.php?action=interface;sa=editMembergroups;save', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Okay... It returned the new link's name and URL...
        _.G('group_name_plural_' + group_id).innerHTML = '<span onclick="editMembergroup(' + group_id + ', ' + group_order + ');">' + info['group_name_plural'] + '</span>';
        _.G('group_name_singular_' + group_id).innerHTML = '<span onclick="editMembergroup(' + group_id + ', ' + group_order + ');">' + info['group_name_singular'] + '</span>';
        _.G('group_color_' + group_id).innerHTML = '<div class="membergroup_color" onclick="editMembergroup(' + group_id + ');" style="background: ' + (info['group_color'] ? info['group_color'] : 'transparent') + ';"></div>';
        _.G('group_min_posts_' + group_id).innerHTML = '<span onclick="editMembergroup(' + group_id + ', ' + group_order + ');">' + (info['min_posts'] != -1 ? info['min_posts'] : '') + '</span>';
        _.G('group_options_' + group_id).innerHTML = options[group_order];
      }
    }, 'group_id=' + group_id + '&group_name_plural=' + encodeURIComponent(group_name_plural) + '&group_name_singular=' + encodeURIComponent(group_name_singular) + '&group_color=' + encodeURIComponent(group_color) + '&min_posts=' + encodeURIComponent(min_posts));
}

function cancelMembergroup(group_id, group_order, group_name_plural, group_name_singular, group_color, group_min_posts)
{
  // Canceling? Fine...
  _.G('group_name_plural_' + group_id).innerHTML = '<span onclick="editMembergroup(' + group_id + ', ' + group_order + ');">' + group_name_plural + '</span>';
  _.G('group_name_singular_' + group_id).innerHTML = '<span onclick="editMembergroup(' + group_id + ', ' + group_order + ');">' + group_name_singular + '</span>';
  _.G('group_color_' + group_id).innerHTML = '<div class="membergroup_color" onclick="editMembergroup(' + group_id + ');" style="background: ' + (group_color ? group_color : 'transparent') + ';"></div>';
  _.G('group_min_posts_' + group_id).innerHTML = '<span onclick="editMembergroup(' + group_id + ', ' + group_min_posts + ');">' + (group_min_posts != -1 ? group_min_posts : '') + '</span>';
  _.G('group_options_' + group_id).innerHTML = options[group_order];
}