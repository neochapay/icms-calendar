<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

cpAddPathway('Календарь', '?view=components&do=config&id='.$_REQUEST['id']);

$inCore->loadModel('calendar');
$model = new cms_model_calendar();
$categories = $model->getAllCategories();
echo '<h3>Календарь</h3>';

if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }

$toolmenu = array();

$toolmenu[0]['icon'] = 'save.gif';
$toolmenu[0]['title'] = 'Сохранить';
$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

$toolmenu[1]['icon'] = 'cancel.gif';
$toolmenu[1]['title'] = 'Отмена';
$toolmenu[1]['link'] = '?view=components';

cpToolMenu($toolmenu);

$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';


//LOAD CURRENT CONFIG
$cfg = $model->config;

//CONFIG DEFAULTS
/*
if (!isset($cfg['calendar_view'])) { $cfg['calendar_view'] = 'agendaWeek'; }
if (!isset($cfg['calendar_access'])) { $cfg['calendar_access'] = 'admin'; }
if (!isset($cfg['calendar_image_acces'])) { $cfg['maps_image_acces'] = 'admin'; }
if (!isset($cfg['calendar_image_original'])) { $cfg['maps_image_original'] = '1'; }
if (!isset($cfg['private_bg_color'])) { $cfg['private_bg_color'] = '#3366CC'; }
if (!isset($cfg['private_tx_color'])) { $cfg['private_tx_color'] = '#000000'; }
if (!isset($cfg['public_bg_color'])) { $cfg['public_bg_color'] = '#C3BCB9'; }
if (!isset($cfg['public_tx_color'])) { $cfg['public_tx_color'] = '#000000'; }
if (!isset($cfg['calendar_module'])) { $cfg['calendar_module'] = 'all'; }
if (!isset($cfg['calendar_module_count'])) { $cfg['calendar_module_count'] = '5'; }*/

//SAVE CONFIG
if($opt=='saveconfig'){
    $cfg = array();
    $cfg['calendar_view'] = $inCore->request('calendar_view', 'str');
    $cfg['calendar_access'] = $inCore->request('calendar_access', 'str');
    $cfg['calendar_image_acces'] = $inCore->request('calendar_image_acces', 'str');
    $cfg['calendar_image_original'] = $inCore->request('calendar_image_original', 'int');  
    $cfg['private_bg_color'] = $inCore->request('private_bg_color', 'str');
    $cfg['private_tx_color'] = $inCore->request('private_tx_color', 'str');
    $cfg['public_bg_color'] = $inCore->request('public_bg_color', 'str');
    $cfg['public_tx_color'] = $inCore->request('public_tx_color', 'str');
    $cfg['calendar_module'] = $inCore->request('calendar_module', 'str');
    $cfg['calendar_module_count'] = $inCore->request('calendar_module_count', 'int');
 
//Добавление категорий 
    if($inCore->request('new_name', 'str'))
    {
      $name = $inCore->request('new_name', 'str');
      $bg_color = $inCore->request('new_bg_color', 'str');
      $tx_color = $inCore->request('new_tx_color', 'str');
      $pattern = '/^#(([a-fA-F0-9]{3}$)|([a-fA-F0-9]{6}$))/';
      if(preg_match($pattern,$bg_color))
      {
	$bg = $bg_color;
      }
      
      if(preg_match($pattern,$tx_color))
      {
	$tx = $tx_color;
      }
      
      if($bg and $tx)
      {
	$model->addCategory($name,$bg,$tx);
      }
    }
//Удаление категорий
    foreach($categories as $category)
    {
      $id = $category['id'];
      if($_POST['delete_'.$id] == "on")
      {
	$model->deleteCategory($category['id']);
      }
      else
      {
	$name = $category['name'];
	$bg_color = $inCore->request('bg_color_'.$id, 'str');
	$tx_color = $inCore->request('tx_color_'.$id, 'str');
	$pattern = '/^#(([a-fA-F0-9]{3}$)|([a-fA-F0-9]{6}$))/';
	if(preg_match($pattern,$bg_color))
	{
	  $bg = $bg_color;
	}
      
	if(preg_match($pattern,$tx_color))
	{
	  $tx = $tx_color;
	}
      
	if($bg and $tx and $name)
	{
	  $model->updateCategory($id,$name,$bg,$tx);
	}
	unset($bg);
	unset($bg_color);
	unset($tx);
	unset($tx_color);
	unset($name);
      }
    }
    $inCore->saveComponentConfig('calendar', $cfg);
    $inCore->redirectBack();
}
$msg = cmsUser::sessionGet('calendr_msg');

if ($msg) { echo '<p class="success">'.$msg.'</p>'; cmsUser::sessionDel('calendr_msg'); }
?>

<script type="text/javascript" src="/admin/components/calendar/colorpicker/colorpicker.js"></script>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
<div id="config_tabs" style="margin-top:12px;">
    <ul id="tabs">
        <li><a href="#basic"><span>Общие</span></a></li>
        <li><a href="#calendar_category"><span>Категории</span></a></li>
	<li><a href="#calendar_image"><span>Изображения</span></a></li>
	<li><a href="#calendar_module"><span>Модуль</span></a></li>
    </ul>
    <div id="basic">
        <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
               <tr>
                    <td width="250">
                        <strong>Вид: </strong><br/>
                        <span class="hinttext">
                            Как будет показываться календарь
                        </span>
                    </td>
                    <td valign="top">
                        <select name="calendar_view" id="calendar" style="width:245px" onchange="showAPIKeys()">
                            <option value="month" <?php if ($cfg['calendar_view']=='month'){?>selected="selected"<?php } ?>>Месяц</option>
                            <option value="agendaWeek" <?php if ($cfg['calendar_view']=='agendaWeek'){?>selected="selected"<?php } ?>>Неделя</option>
                            <option value="agendaDay" <?php if ($cfg['calendar_view']=='agendaDay'){?>selected="selected"<?php } ?>>День</option>
                        </select>
                    </td>
                </tr>
               <tr>
                    <td width="250">
                        <strong>Доступ: </strong><br/>
                        <span class="hinttext">
                            Кому можно добавлять редактировать события
                        </span>
                    </td>
                    <td valign="top">
                        <select name="calendar_access" id="calendar" style="width:245px" onchange="showAPIKeys()">
                            <option value="all" <?php if ($cfg['calendar_access']=='all'){?>selected="selected"<?php } ?>>Всем</option>
                            <option value="users" <?php if ($cfg['calendar_access']=='users'){?>selected="selected"<?php } ?>>Зарегистрированным пользователям</option>
                            <option value="admin" <?php if ($cfg['calendar_access']=='admin'){?>selected="selected"<?php } ?>>Администраторам</option>
                        </select>
                    </td>
                </tr>
         </table>
    </div>
</div>
<!--Изображения -->
    <div id="calendar_category">
        <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
               <tr>
                    <td width="250">
                        <strong>Личные события: </strong><br/>
                    </td>
                    <td valign="top">
		      <table>
			<tr>
			  <td>
			    Цвет фона: 
			  </td>
			  <td>
			    <input id="private_bg_color" name="private_bg_color" class="iColorPicker" type="text" value="<?php echo $cfg['private_bg_color'] ?>" />
			  </td>
			</tr>
			<tr>
			  <td>
			    Цвет текста:
			  </td>
			  <td>
			    <input id="private_tx_color" name="private_tx_color" class="iColorPicker" type="text" value="<?php echo $cfg['private_tx_color'] ?>" />
			  </td>
			</tr>
		      </table>
                    </td>
                </tr>
               <tr>
               <td width="250">
                  <strong>Публичные события: </strong><br/>
                    </td>
                    <td valign="top">
		      <table>
			<tr>
			  <td>
			    Цвет фона: 
			  </td>
			  <td>
			    <input id="public_bg_color" name="public_bg_color" class="iColorPicker" type="text"  value="<?php echo $cfg['public_bg_color'] ?>" />
			  </td>
			</tr>
			<tr>
			  <td>
			    Цвет текста:
			  </td>
			  <td>
			    <input id="public_tx_color" name="public_tx_color" class="iColorPicker" type="text"  value="<?php echo $cfg['public_tx_color'] ?>" />
			  </td>
			</tr>
		      </table>
                    </td>
                </tr>
                <?php 
		  foreach($categories as $category)
		  {
		?>
               <td width="250">
                  <strong><?php print $category['name']?>: </strong><br/>
                    </td>
                    <td valign="top">
		      <table>
			<tr>
			  <td>
			    Цвет фона: 
			  </td>
			  <td>
			    <input id="bg_color_<?php print $category['id']?>" name="bg_color_<?php print $category['id']?>" class="iColorPicker" type="text"  value="<?php print $category['bg']?>" />
			  </td>
			</tr>
			<tr>
			  <td>
			    Цвет текста:
			  </td>
			  <td>
			    <input id="tx_color_<?php print $category['id']?>" name="tx_color_<?php print $category['id']?>" class="iColorPicker" type="text"  value="<?php print $category['tx']?>" />
			  </td>
			</tr>
			<tr>
			  <td>
			    Удалить категорию:
			  </td>
			  <td>
			    <input id="delete_<?php print $category['id']?>" name="delete_<?php print $category['id']?>" type="checkbox"/>
			  </td>
			</tr>			
		      </table>
                    </td>
                </tr>		
		<?
		  }
                ?>
         </table>
         <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
	  <tr>
	    <td width="250">
	      <strong>Новая категория: </strong><br/>
	      <input name="new_name" type="text"/>
            </td>
            <td valign="top">
	      <table>
		<tr>
		  <td>
		    Цвет фона: 
		  </td>
		  <td>
		    <input id="new_bg_color" name="new_bg_color" class="iColorPicker" type="text" value="" />
		  </td>
		</tr>
		<tr>
		  <td>
		    Цвет текста:
		  </td>
		  <td>
		    <input id="new_tx_color" name="new_tx_color" class="iColorPicker" type="text" value="" />
		  </td>
		</tr>
	      </table>
            </td>
          </tr>
        </table>
    </div>
<!--Изображения -->
    <div id="calendar_image">
      <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
	<tr id="ya_route">
	  <td width="250">
	    <strong>Доступ: </strong><br/>
	    <span class="hinttext">
	    Кто может добавлять фотографии к мероприятиям
	    </span>
	  </td>
	  <td valign="top">
	    <select name="calendar_image_acces" id="calendar_image_acces" style="width:245px">
	      <option value="author" <?php if ($cfg['calendar_image_acces']=='author'){?>selected="selected"<?php } ?>>Только автор</option>
	      <option value="admin" <?php if ($cfg['calendar_image_acces']=='admin'){?>selected="selected"<?php } ?>>Только администратор</option>
	      <option value="all" <?php if ($cfg['calendar_image_acces']=='all'){?>selected="selected"<?php } ?>>Все пользователи</option>
	      <option value="none" <?php if ($cfg['calendar_image_acces']=='none'){?>selected="selected"<?php } ?>>Отключено</option>
	    </select>
	  </td>
	</tr>
	<tr id="calendar_image_original">
	  <td width="250">
	    <strong>Сохранять оригинал: </strong><br/>
	    <span class="hinttext">
	    Сохранять оригинал изображения при загрузке
	    </span>
	  </td>
	  <td valign="top">
	    <select name="calendar_image_original" id="calendar_image_original" style="width:245px">
	      <option value="1" <?php if ($cfg['calendar_image_original']=='1'){?>selected="selected"<?php } ?>>Да</option>
	      <option value="0" <?php if ($cfg['calendar_image_original']=='0'){?>selected="selected"<?php } ?>>Нет</option>
	    </select>
	  </td>
	</tr>
      </table>
    </div>
    <div id="calendar_module">
      <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
	<tr>
	  <td width="250">
	    <strong>Вид: </strong><br/>
            <span class="hinttext">
	      Режим показа
	    </span>
          </td>
          <td valign="top">
	    <select name="calendar_module" id="calendar_module" style="width:245px">
	      <option value="all" <?php if ($cfg['calendar_module']=='all'){?>selected="selected"<?php } ?>>Все события календаря</option>
              <option value="user" <?php if ($cfg['calendar_module']=='user'){?>selected="selected"<?php } ?>>Только события пользователя</option>
            </select>
          </td>
        </tr>
	<tr>
	  <td width="250">
	    <strong>Количество строк: </strong><br/>
          </td>
          <td valign="top">
	    <input name="calendar_module_count" size="2" maxlength="2" value="<?php print $cfg['calendar_module_count'];?>">
          </td>
        </tr>        
      </table>
    </div>
<p>
    <input name="opt" type="hidden" value="saveconfig" />
    <input name="save" type="submit" id="save" value="Сохранить" />
    <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
</p>
</form>

<script type="text/javascript">
  $('#config_tabs > ul#tabs').tabs();
</script>