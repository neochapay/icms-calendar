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

$groups = $model->getUserGroups();

//SAVE CONFIG
if($opt=='saveconfig'){
    $cfg = array();
    $cfg['calendar_view'] = $inCore->request('calendar_view', 'str');
    $cfg['calendar_access'] = $inCore->request('calendar_access', 'str');
    $cfg['calendar_firstHour'] = $inCore->request('calendar_firstHour', 'int');
    $cfg['calendar_minTime'] = $inCore->request('calendar_minTime', 'int');
    $cfg['calendar_maxTime'] = $inCore->request('calendar_maxTime', 'int');
    $cfg['calendar_image_acces'] = $inCore->request('calendar_image_acces', 'str');
    $cfg['calendar_image_original'] = $inCore->request('calendar_image_original', 'int');  
    $cfg['private_bg_color'] = $inCore->request('private_bg_color', 'str');
    $cfg['private_tx_color'] = $inCore->request('private_tx_color', 'str');
    $cfg['public_bg_color'] = $inCore->request('public_bg_color', 'str');
    $cfg['public_tx_color'] = $inCore->request('public_tx_color', 'str');
    $cfg['calendar_module'] = $inCore->request('calendar_module', 'str');
    $cfg['calendar_module_count'] = $inCore->request('calendar_module_count', 'int');
    
//Проверяем чтобы максимальное время было больше минимального
    if($cfg['calendar_maxTime'] < $cfg['calendar_minTime'])
    {
      $cfg['calendar_maxTime'] = $cfg['calendar_minTime']+1;
    }
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
//Группы пользователей без модерации
    foreach($groups as $group)
    {
      $id = $group['id'];
      if($_POST['group_'.$id] == "on")
      {
	$cfg['group_'.$id] = TRUE;
      }
      else
      {
	$cfg['group_'.$id] = FALSE;      
      }
    }
    
//Группы пользователей с модерацией
    foreach($groups as $group)
    {
      $id = $group['id'];
      if($_POST['m_group_'.$id] == "on")
      {
	$cfg['m_group_'.$id] = TRUE;
      }
      else
      {
	$cfg['m_group_'.$id] = FALSE;      
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
  <div id="config_tabs" style="margin-top:12px;" class="uitabs">
    <ul id="tabs">
        <li><a href="#basic"><span>Общие</span></a></li>
        <li><a href="#calendar_category"><span>Категории</span></a></li>
	<li><a href="#calendar_image"><span>Изображения</span></a></li>
	<li><a href="#calendar_module"><span>Модуль</span></a></li>
    </ul>
    <div id="basic">
        <table width="661" border="1" cellpadding="10" cellspacing="0" class="proptable">
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
                            <option value="afisha" <?php if ($cfg['calendar_view']=='afisha'){?>selected="selected"<?php } ?>>Афиша</option>
                        </select>
                    </td>
                </tr>
               <tr>
                    <td width="250">
                        <strong>Прямой доступ: </strong><br/>
                        <span class="hinttext">
                            Кому можно добавлять редактировать события без модерации
                        </span>
                    </td>
                    <td valign="top">
                        <?php
			      foreach ($groups as $group)
			      {
				if($cfg['group_'.$group['id']])
				{
				  $apx = 'checked="checked"';
				}
				print '<input type="checkbox" name="group_'.$group['id'].'" '.$apx.'>'.$group['title'].'<br />';
				unset($apx);
			      }
                          ?>
                    </td>
                </tr>
               <tr>
                    <td width="250">
                        <strong>Модерируемый доступ: </strong><br/>
                        <span class="hinttext">
                            Кому можно добавлять события на модерацию
                        </span>
                    </td>
                    <td valign="top">
                        <?php
			      foreach ($groups as $group)
			      {
				if($cfg['m_group_'.$group['id']])
				{
				  $apx = 'checked="checked"';
				}
				print '<input type="checkbox" name="m_group_'.$group['id'].'" '.$apx.'>'.$group['title'].'<br />';
				unset($apx);
			      }
                          ?>
                    </td>
                </tr>
               <tr>
                    <td width="250">
                        <strong>Начало дня: </strong><br/>
                        <span class="hinttext">
                            Первый час с которого показываться календарь в режиме недели
                        </span>
                    </td>
                    <td valign="top">
                        <select name="calendar_firstHour" id="calendar" style="width:245px">
                            <option value="0" <?php if ($cfg['calendar_firstHour']=='0'){?>selected="selected"<?php } ?>>0</option>
                            <option value="1" <?php if ($cfg['calendar_firstHour']=='1'){?>selected="selected"<?php } ?>>1</option>
                            <option value="2" <?php if ($cfg['calendar_firstHour']=='2'){?>selected="selected"<?php } ?>>2</option>
                            <option value="3" <?php if ($cfg['calendar_firstHour']=='3'){?>selected="selected"<?php } ?>>3</option>
                            <option value="4" <?php if ($cfg['calendar_firstHour']=='4'){?>selected="selected"<?php } ?>>4</option>
                            <option value="5" <?php if ($cfg['calendar_firstHour']=='5'){?>selected="selected"<?php } ?>>5</option>
                            <option value="6" <?php if ($cfg['calendar_firstHour']=='6'){?>selected="selected"<?php } ?>>6</option>
                            <option value="7" <?php if ($cfg['calendar_firstHour']=='7'){?>selected="selected"<?php } ?>>7</option>
                            <option value="8" <?php if ($cfg['calendar_firstHour']=='8'){?>selected="selected"<?php } ?>>8</option>
                            <option value="9" <?php if ($cfg['calendar_firstHour']=='9'){?>selected="selected"<?php } ?>>9</option>
                            <option value="10" <?php if ($cfg['calendar_firstHour']=='10'){?>selected="selected"<?php } ?>>10</option>
                            <option value="11" <?php if ($cfg['calendar_firstHour']=='11'){?>selected="selected"<?php } ?>>11</option>
                            <option value="12" <?php if ($cfg['calendar_firstHour']=='12'){?>selected="selected"<?php } ?>>12</option>
                            <option value="13" <?php if ($cfg['calendar_firstHour']=='13'){?>selected="selected"<?php } ?>>13</option>
                            <option value="14" <?php if ($cfg['calendar_firstHour']=='14'){?>selected="selected"<?php } ?>>14</option>
                            <option value="15" <?php if ($cfg['calendar_firstHour']=='15'){?>selected="selected"<?php } ?>>15</option>
                            <option value="16" <?php if ($cfg['calendar_firstHour']=='16'){?>selected="selected"<?php } ?>>16</option>
                            <option value="17" <?php if ($cfg['calendar_firstHour']=='17'){?>selected="selected"<?php } ?>>17</option>
                            <option value="18" <?php if ($cfg['calendar_firstHour']=='18'){?>selected="selected"<?php } ?>>18</option>
                            <option value="19" <?php if ($cfg['calendar_firstHour']=='19'){?>selected="selected"<?php } ?>>19</option>
                            <option value="20" <?php if ($cfg['calendar_firstHour']=='20'){?>selected="selected"<?php } ?>>20</option>
                            <option value="21" <?php if ($cfg['calendar_firstHour']=='21'){?>selected="selected"<?php } ?>>21</option>
                            <option value="22" <?php if ($cfg['calendar_firstHour']=='22'){?>selected="selected"<?php } ?>>22</option>
                            <option value="23" <?php if ($cfg['calendar_firstHour']=='23'){?>selected="selected"<?php } ?>>23</option>
                        </select>
                    </td>
                </tr>
               <tr>
                    <td width="250">
                        <strong>Минимальное время: </strong><br/>
                        <span class="hinttext">
                            Начальное время дня с которого можно добавлять мероприятия
                        </span>
                    </td>
                    <td valign="top">
                        <select name="calendar_minTime" id="calendar" style="width:245px">
                            <option value="0" <?php if ($cfg['calendar_minTime']=='0'){?>selected="selected"<?php } ?>>0</option>
                            <option value="1" <?php if ($cfg['calendar_minTime']=='1'){?>selected="selected"<?php } ?>>1</option>
                            <option value="2" <?php if ($cfg['calendar_minTime']=='2'){?>selected="selected"<?php } ?>>2</option>
                            <option value="3" <?php if ($cfg['calendar_minTime']=='3'){?>selected="selected"<?php } ?>>3</option>
                            <option value="4" <?php if ($cfg['calendar_minTime']=='4'){?>selected="selected"<?php } ?>>4</option>
                            <option value="5" <?php if ($cfg['calendar_minTime']=='5'){?>selected="selected"<?php } ?>>5</option>
                            <option value="6" <?php if ($cfg['calendar_minTime']=='6'){?>selected="selected"<?php } ?>>6</option>
                            <option value="7" <?php if ($cfg['calendar_minTime']=='7'){?>selected="selected"<?php } ?>>7</option>
                            <option value="8" <?php if ($cfg['calendar_minTime']=='8'){?>selected="selected"<?php } ?>>8</option>
                            <option value="9" <?php if ($cfg['calendar_minTime']=='9'){?>selected="selected"<?php } ?>>9</option>
                            <option value="10" <?php if ($cfg['calendar_minTime']=='10'){?>selected="selected"<?php } ?>>10</option>
                            <option value="11" <?php if ($cfg['calendar_minTime']=='11'){?>selected="selected"<?php } ?>>11</option>
                            <option value="12" <?php if ($cfg['calendar_minTime']=='12'){?>selected="selected"<?php } ?>>12</option>
                            <option value="13" <?php if ($cfg['calendar_minTime']=='13'){?>selected="selected"<?php } ?>>13</option>
                            <option value="14" <?php if ($cfg['calendar_minTime']=='14'){?>selected="selected"<?php } ?>>14</option>
                            <option value="15" <?php if ($cfg['calendar_minTime']=='15'){?>selected="selected"<?php } ?>>15</option>
                            <option value="16" <?php if ($cfg['calendar_minTime']=='16'){?>selected="selected"<?php } ?>>16</option>
                            <option value="17" <?php if ($cfg['calendar_minTime']=='17'){?>selected="selected"<?php } ?>>17</option>
                            <option value="18" <?php if ($cfg['calendar_minTime']=='18'){?>selected="selected"<?php } ?>>18</option>
                            <option value="19" <?php if ($cfg['calendar_minTime']=='19'){?>selected="selected"<?php } ?>>19</option>
                            <option value="20" <?php if ($cfg['calendar_minTime']=='20'){?>selected="selected"<?php } ?>>20</option>
                            <option value="21" <?php if ($cfg['calendar_minTime']=='21'){?>selected="selected"<?php } ?>>21</option>
                            <option value="22" <?php if ($cfg['calendar_minTime']=='22'){?>selected="selected"<?php } ?>>22</option>
                            <option value="23" <?php if ($cfg['calendar_minTime']=='23'){?>selected="selected"<?php } ?>>23</option>
                        </select>
                    </td>
                </tr>                
               <tr>
                    <td width="250">
                        <strong>Максимальное время: </strong><br/>
                        <span class="hinttext">
                            Конечное время дня до которого можно добавлять мероприятия
                        </span>
                    </td>
                    <td valign="top">
                        <select name="calendar_maxTime" id="calendar" style="width:245px">
                            <option value="1" <?php if ($cfg['calendar_maxTime']=='1'){?>selected="selected"<?php } ?>>1</option>
                            <option value="2" <?php if ($cfg['calendar_maxTime']=='2'){?>selected="selected"<?php } ?>>2</option>
                            <option value="3" <?php if ($cfg['calendar_maxTime']=='3'){?>selected="selected"<?php } ?>>3</option>
                            <option value="4" <?php if ($cfg['calendar_maxTime']=='4'){?>selected="selected"<?php } ?>>4</option>
                            <option value="5" <?php if ($cfg['calendar_maxTime']=='5'){?>selected="selected"<?php } ?>>5</option>
                            <option value="6" <?php if ($cfg['calendar_maxTime']=='6'){?>selected="selected"<?php } ?>>6</option>
                            <option value="7" <?php if ($cfg['calendar_maxTime']=='7'){?>selected="selected"<?php } ?>>7</option>
                            <option value="8" <?php if ($cfg['calendar_maxTime']=='8'){?>selected="selected"<?php } ?>>8</option>
                            <option value="9" <?php if ($cfg['calendar_maxTime']=='9'){?>selected="selected"<?php } ?>>9</option>
                            <option value="10" <?php if ($cfg['calendar_maxTime']=='10'){?>selected="selected"<?php } ?>>10</option>
                            <option value="11" <?php if ($cfg['calendar_maxTime']=='11'){?>selected="selected"<?php } ?>>11</option>
                            <option value="12" <?php if ($cfg['calendar_maxTime']=='12'){?>selected="selected"<?php } ?>>12</option>
                            <option value="13" <?php if ($cfg['calendar_maxTime']=='13'){?>selected="selected"<?php } ?>>13</option>
                            <option value="14" <?php if ($cfg['calendar_maxTime']=='14'){?>selected="selected"<?php } ?>>14</option>
                            <option value="15" <?php if ($cfg['calendar_maxTime']=='15'){?>selected="selected"<?php } ?>>15</option>
                            <option value="16" <?php if ($cfg['calendar_maxTime']=='16'){?>selected="selected"<?php } ?>>16</option>
                            <option value="17" <?php if ($cfg['calendar_maxTime']=='17'){?>selected="selected"<?php } ?>>17</option>
                            <option value="18" <?php if ($cfg['calendar_maxTime']=='18'){?>selected="selected"<?php } ?>>18</option>
                            <option value="19" <?php if ($cfg['calendar_maxTime']=='19'){?>selected="selected"<?php } ?>>19</option>
                            <option value="20" <?php if ($cfg['calendar_maxTime']=='20'){?>selected="selected"<?php } ?>>20</option>
                            <option value="21" <?php if ($cfg['calendar_maxTime']=='21'){?>selected="selected"<?php } ?>>21</option>
                            <option value="22" <?php if ($cfg['calendar_maxTime']=='22'){?>selected="selected"<?php } ?>>22</option>
                            <option value="23" <?php if ($cfg['calendar_maxTime']=='23'){?>selected="selected"<?php } ?>>23</option>
                            <option value="24" <?php if ($cfg['calendar_maxTime']=='24'){?>selected="selected"<?php } ?>>24</option>
                        </select>
                    </td>
                </tr>                 
         </table>
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