function editCategory(cat_id)
{
  
  // Get the current category name, and such.
  _.X(base_url + '/index.php?action=interface;sa=editCategory', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Yay! No errors...
        var handle = _.G('category_' + cat_id);

        // Empty its innards! Muahaha!
        handle.innerHTML = '';

        // Create an input for our text box...
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'category_edit_' + info['cat_id'];
        input.value = info['cat_name'];

        // Add the input
        handle.appendChild(input);

        // Collapsible?
        var input = document.createElement('input');
        input.type = 'checkbox';
        input.id = 'category_collapse_' + info['cat_id'];
        input.value = 1;
        input.title = allow_collapse;
        handle.appendChild(input);
        if(info['is_collapsible'])
          input.checked = 'checked';

        // Make our save button...
        var input = document.createElement('input');
        input.id = 'category_save_' + info['cat_id'];
        input.type = 'submit';
        input.value = save_text;

        // Add this one too!
        handle.appendChild(input);

        // How about a cancel? =P
        var input = document.createElement('input');
        input.id = 'category_cancel_' + info['cat_id'];
        input.type = 'submit';
        input.value = cancel_text;

        // Cancel please :)
        handle.appendChild(input);

        // A couple things for on click ;)
        _.on(_.G('category_save_' + info['cat_id']), 'click', function() { saveCategory(info['cat_id']); });
        _.on(_.G('category_cancel_' + info['cat_id']), 'click', function() { cancelCategory(info['cat_id'], info['cat_name']); });
      }
    }, 'cat_id=' + cat_id);
}

function saveCategory(cat_id)
{
  // Get our new name... If at all...
  var cat_name = _.G('category_edit_' + cat_id).value;
  var collapsible = _.G('category_collapse_' + cat_id).checked ? 1 : 0;

  // Query your site...
  _.X(base_url + '/index.php?action=interface;sa=editCategory;save', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Okay... It returned the new categories name...
        _.G('category_' + cat_id).innerHTML = '<span class="hand_cursor" onclick="editCategory(' + cat_id + ');">' + info['cat_name'] + '</span>';
      }
    }, 'cat_id=' + cat_id + '&cat_name=' + encodeURIComponent(cat_name) + '&is_collapsible=' + collapsible);
}

function cancelCategory(cat_id, cat_name)
{
  // Canceling? Fine...
  _.G('category_' + cat_id).innerHTML = '<span class="hand_cursor" onclick="editCategory(' + cat_id + ');">' + cat_name + '</span>';
}

function editBoard(board_id, is_child)
{
  
  // Get the current board name, and such.
  _.X(base_url + '/index.php?action=interface;sa=editBoard', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Yay! No errors...
        var handle = _.G('board_' + board_id);

        // Empty its innards! Muahaha!
        handle.innerHTML = is_child ? '&nbsp;&nbsp;' : '';

        // Create an input for our text box...
        var input = document.createElement('input');
        input.type = 'text';
        input.id = 'board_edit_' + info['board_id'];
        input.value = info['board_name'];

        // Add the input
        handle.appendChild(input);

        // Make our save button...
        var input = document.createElement('input');
        input.id = 'board_save_' + info['board_id'];
        input.type = 'submit';
        input.value = save_text;

        // Add this one too!
        handle.appendChild(input);

        // How about a cancel? =P
        var input = document.createElement('input');
        input.id = 'board_cancel_' + info['board_id'];
        input.type = 'submit';
        input.value = cancel_text;

        // Cancel please :)
        handle.appendChild(input);

        // A couple things for on click ;)
        _.on(_.G('board_save_' + info['board_id']), 'click', function() { saveBoard(info['board_id'], is_child); });
        _.on(_.G('board_cancel_' + info['board_id']), 'click', function() { cancelBoard(info['board_id'], info['board_name'], is_child); });
      }
    }, 'board_id=' + board_id);
}

function saveBoard(board_id, is_child)
{
  // Get our new name... If at all...
  var board_name = _.G('board_edit_' + board_id).value;

  // Query your site...
  _.X(base_url + '/index.php?action=interface;sa=editBoard;save', function(data)
    {
      var info = _.S(data, true);

      // Any errors?
      if(info['error'].length > 0)
        alert(info['error']);
      else
      {
        // Okay... It returned the new categories name...
        _.G('board_' + board_id).innerHTML = (is_child ? '&nbsp;&nbsp;' : '') + '<span class="hand_cursor" onclick="editBoard(' + board_id + ', ' + (is_child ? true : false) + ');">' + info['board_name'] + '</span>';
      }
    }, 'board_id=' + board_id + '&board_name=' + encodeURIComponent(board_name));
}

function cancelBoard(board_id, board_name, is_child)
{
  // Canceling? Fine...
  _.G('board_' + board_id).innerHTML = (is_child ? '&nbsp;&nbsp' : '') + '<span class="hand_cursor" onclick="editBoard(' + board_id + ', ' + (is_child ? true : false) + ');">' + board_name + '</span>';
}