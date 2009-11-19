function collection_click()
{
  document.getElementById('avatar_collection').checked = true;
  collection_change();
}

function collection_change()
{
  collection = document.getElementById('collection');
  if(collection.selectedIndex != -1)
    change_avatar(base_url + '/index.php?action=avatar;collection=' + collection.options[collection.selectedIndex].value);
  else
    document.getElementById('avatar_preview').style.visibility = 'hidden';
}

function url_click()
{
  document.getElementById('avatar-url').checked = true;
  url_change();
}

function url_change()
{
  change_avatar(document.getElementById('url').value);
}

function upload_click()
{
  document.getElementById('avatar_upload').checked = true;
  upload_change();
}

function upload_change()
{
  if(upload)
    change_avatar(avatar_image);
  else
    document.getElementById('avatar_preview').style.visibility = 'hidden';
}

function change_avatar(src)
{
  var avatar = new Image();
  document.getElementById('avatar_preview').style.visibility = 'hidden';
  _.E(avatar, 'load', function()
    {
      document.getElementById('avatar_preview').src = this.src;
      document.getElementById('avatar_preview').style.visibility = 'visible';
    });
  _.E(avatar, 'error', function()
    {
      document.getElementById('avatar_preview').style.visibility = 'hidden';
    });
  avatar.src = src;
}