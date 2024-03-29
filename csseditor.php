<?php
/*
Plugin Name: Css Editor for Lyceum & wordpress
Plugin URI: http://code.google.com/p/csseditorforlyceum/
Description: Easy way to modify an old css,or add a new css on the fly.
Author: whatup
Version: 0.1
Author URI: http://blog.twkang.net
*/ 

add_action('admin_menu', 'csseditor_add_theme_page');
add_action('wp_head','list_all_css_file',1000);


function csseditor_add_theme_page()
{
 add_theme_page('Css Editor', 'Css Editor', 'edit_themes', basename(__FILE__), 'theme_page');
}


function list_all_css_file()
{
  global $blog;
  $css = get_option('cssfile');
  foreach($css as $key=>$value)
  {
    if(SUBDOMAINS)
    echo '<link rel="stylesheet" type="text/css" href="/wp-content/plugins/csseditorforlyceum/css.php?cssid='.$key.'" />';
    else
    echo '<link rel="stylesheet" type="text/css" href="../wp-content/plugins/csseditorforlyceum/css.php?cssid='.$key.'&b='.$blog.'" />';
    echo "\n";
  }
}



function theme_page()
{

if(isset($_POST['editCss']) && $_POST['editCss'] == "Edit" && $_POST['cssId'] != -1)
{
   $css = get_option('cssfile');
   $csstitle = strip_string($css[$_POST['cssId']]['title']);
   $cssstr = strip_string($css[$_POST['cssId']]['csscontent']);
}

if(isset($_POST['deleteCss']) && $_POST['deleteCss'] == "Delete" && $_POST['cssId'] != -1)
{
  echo '<div style="background-color: rgb(240, 248, 255);" id="message" class="updated fade">
        <p>Delete '.delete_css_file().'!</p>
        </div>';
}

if(isset($_POST['catch']) && $_POST['catch'] == "Catch")
{
  $cssstr = download_css_file();
  if(isset($cssstr))
  {
    $csstitle = explode("/",$_POST['cssfile']);
    $csstitle = array_pop($csstitle);
  }
}

if(isset($_POST['saveCss']) && $_POST['saveCss'] == "Save")
{
  if($_POST['title'] != Null)
  {
    $csstitle = strip_string($_POST['title']);
  } 
  else
  {
     echo '<div style="background-color: rgb(240, 248, 255);" id="message" class="updated fade">
        <p>You must give it a title!</p>
        </div>';
      $nosave = 1;
  }
  if($_POST['cssText']!= Null)
  {
    $cssstr = strip_string($_POST['cssText']);
  }
  else
  {
      echo '<div style="background-color: rgb(240, 248, 255);" id="message" class="updated fade">
        <p>You must have css content!</p>
        </div>';
      $nosave = 1;
  }
  if(!$nosave)  {
    save_css_file();
     echo '<div style="background-color: rgb(240, 248, 255);" id="message" class="updated fade">
        <p>Update Css File ok!</p>
        </div>';
  }
}
	?>

<?php show_css_list(); ?>
<div class='wrap'>
<form method="POST">
	<fieldset class="options"> 
		<legend>Catch the Css file:</legend>
    <div id="nonJsForm">
	    <lebel>CSS File:</label><input type=text name=cssfile id=cssfile SIZE=50/>
		  <input type="submit" name="catch" value="Catch">
	  </div>
	</fieldset>
	<fieldset class="options">
	  <legend>Css file Editor:</legend>
	  <div id="nonJsForm">
	  <lebel>CSS Name:</label>
	  
    <input type=text name=title id=title SIZE=50 value="<?php 
      if(isset($csstitle)) echo $csstitle; 
    ?>"/>
    
    <input type="submit" name="saveCss" value="Save"><br/>
    
	  <textarea COLS=120 ROWS=50 name="cssText"><?php 
      if(isset($cssstr)) echo $cssstr; 
    ?></textarea>
    
    </div>
	 </fieldset>
</form>
</div>
	<?
}

function download_css_file()
{
  if(!isset($_POST['cssfile'])) return ;
  $handle = fopen($_POST['cssfile'], "r");
  
  $data = fread($handle, 1000000);
  return $data;
}

function save_css_file()
{
  $css = get_option('cssfile');
  foreach($css as $key=>$value)
  {
    if($value['title'] == $_POST['title'])
    {
        $css[$key] = array("title"=>$_POST['title'],"csscontent"=>$_POST['cssText']);
        $update = 1;
        break;
    }        
  }
  if(!$update)
  {
    $css[] = array("title"=>$_POST['title'],"csscontent"=>$_POST['cssText']);
  }
  update_option('cssfile',$css);
}
function delete_css_file()
{
  $css = get_option('cssfile');
  $title = $css[$_POST['cssId']]['title'];
  unset($css[$_POST['cssId']]);
  update_option('cssfile',$css);
  return $title;
}
function show_css_list()
{
  $css = get_option('cssfile');
  ?>
<div class="wrap">
  <form name="cats" method="post" action="">
  <table width="75%" cellpadding="3" cellspacing="3">
  <tr>
    <td colspan="3">Css File List:</td>
  </tr>
  <tr>
    <td><select name="cssId">
			<option value="-1">Please choice a css file.</option>
			<?php
			   foreach($css as $key=>$value)
			     echo '<option value="'.$key.'">'.$value['title']."</option>\n";
			    
			?>
		  </select>
		</td></td>
    <td><input type="submit" name="editCss" value="Edit"></td>
    <td><input type="submit" name="deleteCss" value="Delete"></td>
  </tr>
</table>
</form>
</div>
  <?php
}
function strip_string($string)
{

if (get_magic_quotes_gpc()) {
  $string = stripcslashes($string);
}
return $string;
}
?>
