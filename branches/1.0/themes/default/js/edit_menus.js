var options = new Array(total_links);

function editLink(link_id, link_order)
{
  // Get the current link name, and such.
  _.X(base_url + '/index.php?action=interface;sa=edit_menus', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Yay! No errors...
        var handle_name = _.G('link_name_' + link_id);
        var handle_href = _.G('link_href_' + link_id);
        var handle_target = _.G('link_target_' + link_id);
        var handle_follow = _.G('link_follow_' + link_id);
        var handle_options = _.G('link_options_' + link_id);

        // Store the options data for safe keeping in a global variable
        options[link_order] = handle_options.innerHTML;

        // Empty their innards! Muahaha!
        handle_name.innerHTML = '';
        handle_href.innerHTML = '';
        handle_target.innerHTML = '';
        handle_follow.innerHTML = '';
        handle_options.innerHTML = '';

        // Create an input for our name text box
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'link_name_edit_' + info['link_id'];
        input.value = info['link_name'];

        // Add the input
        handle_name.appendChild(input);
        
        // Another input for our URL text box
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'link_href_edit_' + info['link_id'];
        input.value = info['link_href'];

        // Add the input
        handle_href.appendChild(input);

        // Creat a checkbox for new/same window
        var input = document.createElement('input');
        input.type = 'checkbox';
        input.id = 'link_target_edit_' + info['link_id'];

        // Add the input
        handle_target.appendChild(input);
        
        if(info['link_target'] == 1)
          input.checked = 'checked';
        
        // Creat a checkbox for search engine followingness
        var input = document.createElement('input');
        input.type = 'checkbox';
        input.id = 'link_follow_edit_' + info['link_id'];
        
        // Add the input
        handle_follow.appendChild(input);
        
        if(info['link_follow'] == 1)
          input.checked = 'checked';

        // Make our save button...
        var input = document.createElement('input');
        input.id = 'link_save_' + info['link_id'];
        input.type = 'submit';
        input.value = save_text;

        // Add this one too!
        handle_options.appendChild(input);

        // How about a cancel? =P
        var input = document.createElement('input');
        input.id = 'link_cancel_' + info['link_id'];
        input.type = 'submit';
        input.value = cancel_text;

        // Cancel please :)
        handle_options.appendChild(input);

        // A couple things for on click ;)
        _.on(_.G('link_save_' + info['link_id']), 'click', function() { saveLink(info['link_id'], link_order); });
        _.on(_.G('link_cancel_' + info['link_id']), 'click', function() { cancelLink(info['link_id'], link_order, info['link_name'], info['link_href'], info['link_target'], info['link_follow']); });
      }
    }, 'link_id=' + link_id);
}

function saveLink(link_id, link_order)
{
  // Get our new name... If at all...
  var link_name = _.G('link_name_edit_' + link_id).value;
  var link_href = _.G('link_href_edit_' + link_id).value;
  var link_target = _.G('link_target_edit_' + link_id).checked;
  var link_follow = _.G('link_follow_edit_' + link_id).checked;
  
  // Query your site...
  _.X(base_url + '/index.php?action=interface;sa=edit_menus;save', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Okay... It returned the new link's name and URL...
        _.G('link_name_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + info['link_name'] + '</span>';
        _.G('link_href_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + info['link_href'] + '</span>';
  _.G('link_target_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + (link_target == true ? new_text : same_text) + '</span>';
  _.G('link_follow_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + (link_follow == true ? yes_text : no_text) + '</span>';
        _.G('link_options_' + link_id).innerHTML = options[link_order];
      }
    }, 'link_id=' + link_id + '&link_name=' + encodeURIComponent(link_name) + '&link_href=' + encodeURIComponent(link_href) + '&link_target=' + Number(link_target) + '&link_follow=' + Number(link_follow));
}

function cancelLink(link_id, link_order, link_name, link_href, link_target, link_follow)
{
  // Canceling? Fine...
  _.G('link_name_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + link_name + '</span>';
  _.G('link_href_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + link_href + '</span>';
  _.G('link_target_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + (link_target == true ? new_text : same_text) + '</span>';
  _.G('link_follow_' + link_id).innerHTML = '<span onclick="editLink(' + link_id + ', ' + link_order + ');">' + (link_follow == true ? yes_text : no_text) + '</span>';
  _.G('link_options_' + link_id).innerHTML = options[link_order];
}