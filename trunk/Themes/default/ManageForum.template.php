<?php
// ManageForum.template.php by the SnowCMS Team
if(!defined('Snow'))
  die("Hacking Attempt..");

function Main() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo '
  <h2>', $l['manageforum_header'], '</h2>
  <p>', $l['manageforum_desc'], '</p>
  <br />
  <div class="acp_left">
    <p class="main"><a href="', $cmsurl, 'index.php?action=admin&sa=forum&fa=categories" title="', $l['mf_link_cats'], '">', $l['mf_link_cats'], '</a></p>
    <p class="desc">', $l['mf_link_cats_desc'], '</p>
  </div>
  <div class="acp_right">
    <p class="main"><a href="', $cmsurl, 'index.php?action=admin&sa=forum&fa=boards" title="', $l['mf_link_boards'], '">', $l['mf_link_boards'], '</a></p>
    <p class="desc">', $l['mf_link_boards_desc'], '</p>  
  </div>';
}

function AddCat() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo ' 
  <h2>', $l['managecats_add_header'], '</h2>
  <br />
  <form action="', $cmsurl, 'index.php?action=admin&sa=forum&fa=categories" method="post">
    <fieldset>
      <table>
        <tr>
          <td>Category Name:</td><td><input name="cat_name" type="text" value="', $settings['cat_name'], '"/></td>
        </tr>
        <tr>
          <td>Category Position:</td><td><select name="placement">
                                           <option value="0">Before...</option>
                                           <option value="1">After...</option>
                                         </select>
                                         <select name="category">';
                                         if(count($settings['cats'])) {
                                           foreach($settings['cats'] as $cat) {
                                             echo '<option value="', $cat['id'], '">', $cat['name'], '</option>';
                                           }
                                         }
                                         echo '
                                         </select>
                                     </td>
        </tr>
        <tr>
          <td></td><td><input name="add_cat" type="submit" value="Add Category"/></td>
        </tr>
      </table>
    </fieldset>
  </form>';
}

function ShowCats() {
global $cmsurl, $db_prefix, $l, $settings, $user;
  echo '<h2>Manage Categories</h2>
        <p>Here you can edit, delete and create categories</p>
        <table id="mc">
          <tr>
            <td>Category Name</td><td>Order</td><td>Delete</td>
          </tr>';
        foreach($settings['cats'] as $cat) {
          echo '
          <tr>
            <td><input name="cat_name[', $cat['id'], ']" type="text" class="name" value="', $cat['name'], '"/></td><td><input name="cat_order[', $cat['id'], ']" type="text" class="order" value="', $cat['order'], '"/></td><td></td>
          </tr>';
        }
        echo '
        </table>';
}
?>