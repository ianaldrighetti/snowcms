function table_onchange()
{
  if(s.id('admin_members_manage_table_option').value == 'delete')
  {
    if(!confirm(delete_confirm))
      return false;
  }

  return true;
}

s.onload(function() { s.id('admin_members_manage_table_submit').onclick = table_onchange; });