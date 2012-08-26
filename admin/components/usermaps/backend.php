<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

cpAddPathway('Места', '?view=components&do=config&id='.$_REQUEST['id']);

echo '<h3>Места</h3>';

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
$cfg = $inCore->loadComponentConfig('usermaps');

//CONFIG DEFAULTS
if (!isset($cfg['yandex_key'])) { $cfg['yandex_key'] = ''; }
if (!isset($cfg['maps_engine'])) { $cfg['maps_engine'] = 'HYBRID'; }
if (!isset($cfg['maps_route'])) { $cfg['maps_route'] = '0'; }
if (!isset($cfg['maps_chekin'])) { $cfg['maps_chekin'] = '0'; }
if (!isset($cfg['maps_center'])) { $cfg['maps_center'] = '47.25, 56.13'; }
if ($cfg['maps_center'] == "") { $cfg['maps_center'] = '47.25, 56.13'; }
if (!isset($cfg['maps_traffic'])) { $cfg['maps_traffic'] = '0'; }
if (!isset($cfg['main_zoom'])) { $cfg['main_zoom'] = '13'; }
if (!isset($cfg['point_zoom'])) { $cfg['point_zoom'] = '15'; }
if (!isset($cfg['maps_user_del'])) { $cfg['maps_user_del'] = '0'; }
if (!isset($cfg['maps_chekin_del'])) { $cfg['maps_chekin_del'] = '0'; }
if (!isset($cfg['maps_image_acces'])) { $cfg['maps_image_acces'] = 'admin'; }
if (!isset($cfg['maps_image_original'])) { $cfg['maps_image_original'] = '1'; }
//SAVE CONFIG
if($opt=='saveconfig'){
    $cfg = array();
    $cfg['maps_engine'] = $inCore->request('maps_engine', 'str');
    $cfg['yandex_key']    = $inCore->request('yandex_key', 'str');
    $cfg['maps_route']    = $inCore->request('maps_route', 'int');
    $cfg['maps_chekin']    = $inCore->request('maps_chekin', 'int');
    $cfg['maps_center'] = $inCore->request('maps_center', 'str');
    $cfg['maps_traffic'] = $inCore->request('maps_traffic', 'str');
    $cfg['main_zoom'] = $inCore->request('main_zoom', 'int');
    $cfg['point_zoom'] = $inCore->request('point_zoom', 'int');
    $cfg['maps_user_del'] = $inCore->request('maps_user_del', 'int');
    $cfg['maps_chekin_del'] = $inCore->request('maps_chekin_del', 'int');
    $cfg['maps_image_acces'] = $inCore->request('maps_image_acces', 'str');
    $cfg['maps_image_original'] = $inCore->request('maps_image_original', 'int');
    $inCore->saveComponentConfig('usermaps', $cfg);
    $inCore->redirectBack();
}
$msg = cmsUser::sessionGet('usermaps_msg');

if ($msg) { echo '<p class="success">'.$msg.'</p>'; cmsUser::sessionDel('usermaps_msg'); }
?>
<script src="http://api-maps.yandex.ru/1.1/index.xml?key=<?php echo $cfg['yandex_key'] ?>" type="text/javascript"></script>
<script type="text/javascript">
  var map;
  function coordSend(maps_center)
  {
    var obj=document.optform
    obj.maps_center.value=maps_center
  }

  window.onload = function ()
  {
    map = new YMaps.Map(document.getElementById("YMapsID"));
    map.setCenter(new YMaps.GeoPoint(<?php echo $cfg['maps_center'] ?>), 3);
    map.setType(YMaps.MapType.<?php echo $cfg['maps_engine'] ?>);
    var miniMapPositive = new YMaps.MiniMap(3);
    map.addControl(miniMapPositive);
    var zoomControl = new YMaps.Zoom({noTips: true});
    map.addControl(zoomControl);
    map.addControl(new YMaps.TypeControl());
    var placemark = new YMaps.Placemark(map.getCenter(), {draggable: true ,  hasBalloon: false});
    map.addOverlay(placemark);
    YMaps.Events.observe(placemark, placemark.Events.DragEnd, function (obj)
    {
      obj.update();
      coordSend(obj.getGeoPoint());
    });
  }
</script>

<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
<div id="config_tabs" style="margin-top:12px;">
    <ul id="tabs">
        <li><a href="#basic"><span>Общие</span></a></li>
	<li><a href="#maps_center"><span>Центр карты</span></a></li>
	<li><a href="#maps_private"><span>Приватность</span></a></li>
	<li><a href="#maps_image"><span>Изображения</span></a></li>	
	<li><a href="#advansed"><span>Дополнительно</span></a></li>
    </ul>
    <div id="basic">
        <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
               <tr>
                    <td width="250">
                        <strong>Тип карт: </strong><br/>
                        <span class="hinttext">
                            Если Ваш город не очерчен картой то рекомендуется тип Народная карта:Схема
                        </span>
                    </td>
                    <td valign="top">
                        <select name="maps_engine" id="maps_engine" style="width:245px">
                            <option value="MAP" <?php if ($cfg['maps_engine']=='MAP'){?>selected="selected"<?php } ?>>Схема</option>
                            <option value="SATELLITE" <?php if ($cfg['maps_engine']=='SATELLITE'){?>selected="selected"<?php } ?>>Спутник</option>
                            <option value="HYBRID" <?php if ($cfg['maps_engine']=='HYBRID'){?>selected="selected"<?php } ?>>Гибрид</option>
			    <option value="PMAP" <?php if ($cfg['maps_engine']=='PMAP'){?>selected="selected"<?php } ?>>Народная карта:Схема</option>
			    <option value="PHYBRID" <?php if ($cfg['maps_engine']=='PHYBRID'){?>selected="selected"<?php } ?>>Народная карта:Гибрид</option>
                        </select>
                    </td>
                </tr>
                <tr id="ya_tr">
                    <td>
                        <strong>Ключ API Яндекс: </strong><br/>
                        <span class="hinttext">
                            Необходим для использования Яндекс.Карт
                        </span>
                    </td>
                    <td valign="top">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td><input name="yandex_key" type="text" id="yandex_key" value="<?php echo @$cfg['yandex_key'];?>" style="width:240px"/></td>
                                <td style="padding-left:10px;"><a href="http://api.yandex.ru/maps/form.xml" target="_blank">Получить ключ</a></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr id="main_zoom">
                    <td>
                        <strong>Зум по умолчанию: </strong><br/>
                        <span class="hinttext">
                            используется для настройки приблежения общей карты
                        </span>
                    </td>
                    <td valign="top">
		      <select name="main_zoom" id="main_zoom" style="width:245px">
                        <option value="1" <?php if ($cfg['main_zoom']=='1'){?>selected="selected"<?php } ?>>1 - мелко</option>
                        <option value="2" <?php if ($cfg['main_zoom']=='2'){?>selected="selected"<?php } ?>>2</option>
                        <option value="3" <?php if ($cfg['main_zoom']=='3'){?>selected="selected"<?php } ?>>3</option>
                        <option value="4" <?php if ($cfg['main_zoom']=='4'){?>selected="selected"<?php } ?>>4</option>
                        <option value="5" <?php if ($cfg['main_zoom']=='5'){?>selected="selected"<?php } ?>>5</option>
                        <option value="6" <?php if ($cfg['main_zoom']=='6'){?>selected="selected"<?php } ?>>6</option>
                        <option value="7" <?php if ($cfg['main_zoom']=='7'){?>selected="selected"<?php } ?>>7</option>
                        <option value="8" <?php if ($cfg['main_zoom']=='8'){?>selected="selected"<?php } ?>>8</option>
                        <option value="9" <?php if ($cfg['main_zoom']=='9'){?>selected="selected"<?php } ?>>9</option>
                        <option value="10" <?php if ($cfg['main_zoom']=='10'){?>selected="selected"<?php } ?>>10</option>
                        <option value="11" <?php if ($cfg['main_zoom']=='11'){?>selected="selected"<?php } ?>>11</option>
                        <option value="12" <?php if ($cfg['main_zoom']=='12'){?>selected="selected"<?php } ?>>12</option>
                        <option value="13" <?php if ($cfg['main_zoom']=='13'){?>selected="selected"<?php } ?>>13</option>
                        <option value="14" <?php if ($cfg['main_zoom']=='14'){?>selected="selected"<?php } ?>>14</option>
                        <option value="15" <?php if ($cfg['main_zoom']=='15'){?>selected="selected"<?php } ?>>15</option>
                        <option value="16" <?php if ($cfg['main_zoom']=='16'){?>selected="selected"<?php } ?>>16 - крупно</option>
		      </select>
                    </td>
                </tr>
                <tr id="point_zoom">
                    <td>
                        <strong>Зум для точек: </strong><br/>
                        <span class="hinttext">
                            используется при просмотре точек
                        </span>
                    </td>
                    <td valign="top">
		      <select name="point_zoom" id="point_zoom" style="width:245px">
                        <option value="1" <?php if ($cfg['point_zoom']=='1'){?>selected="selected"<?php } ?>>1 - мелко</option>
                        <option value="2" <?php if ($cfg['point_zoom']=='2'){?>selected="selected"<?php } ?>>2</option>
                        <option value="3" <?php if ($cfg['point_zoom']=='3'){?>selected="selected"<?php } ?>>3</option>
                        <option value="4" <?php if ($cfg['point_zoom']=='4'){?>selected="selected"<?php } ?>>4</option>
                        <option value="5" <?php if ($cfg['point_zoom']=='5'){?>selected="selected"<?php } ?>>5</option>
                        <option value="6" <?php if ($cfg['point_zoom']=='6'){?>selected="selected"<?php } ?>>6</option>
                        <option value="7" <?php if ($cfg['point_zoom']=='7'){?>selected="selected"<?php } ?>>7</option>
                        <option value="8" <?php if ($cfg['point_zoom']=='8'){?>selected="selected"<?php } ?>>8</option>
                        <option value="9" <?php if ($cfg['point_zoom']=='9'){?>selected="selected"<?php } ?>>9</option>
                        <option value="10" <?php if ($cfg['point_zoom']=='10'){?>selected="selected"<?php } ?>>10</option>
                        <option value="11" <?php if ($cfg['point_zoom']=='11'){?>selected="selected"<?php } ?>>11</option>
                        <option value="12" <?php if ($cfg['point_zoom']=='12'){?>selected="selected"<?php } ?>>12</option>
                        <option value="13" <?php if ($cfg['point_zoom']=='13'){?>selected="selected"<?php } ?>>13</option>
                        <option value="14" <?php if ($cfg['point_zoom']=='14'){?>selected="selected"<?php } ?>>14</option>
                        <option value="15" <?php if ($cfg['point_zoom']=='15'){?>selected="selected"<?php } ?>>15</option>
                        <option value="16" <?php if ($cfg['point_zoom']=='16'){?>selected="selected"<?php } ?>>16 - крупно</option>
		      </select>
                    </td>
                </tr>
        </table>
    </div>
    <div id="maps_center">
      <h2>Центр карты</h2>
      <div id="YMapsID" style="width:680px;height:300px;"></div>
      <input type='hidden' name='maps_center' value="<?php echo $cfg['maps_center'] ?>">
    </div>
<!-- Приватность -->
    <div id="maps_private">
            <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
               <tr>
                    <td width="250">
                        <strong>Удаление пользовательской метки: </strong><br/>
                        <span class="hinttext">
                            Позволять удалять пользователям себя с карты
                        </span>
                    </td>
                    <td valign="top">
                        <select name="maps_user_del" id="maps_user_del" style="width:245px">
                            <option value="1" <?php if ($cfg['maps_user_del']=='1'){?>selected="selected"<?php } ?>>Разрешить</option>
                            <option value="0" <?php if ($cfg['maps_user_del']=='0'){?>selected="selected"<?php } ?>>Запретить</option>
                        </select>
                    </td>
                </tr>
                <tr id="maps_chekin_del">
                    <td>
                        <strong>Удаление истории chek-in: </strong><br/>
                        <span class="hinttext">
                            Позволять пользователям удалять историю отметок Chek-in
                        </span>
                    </td>
                    <td valign="top">
		      <select name="maps_chekin_del" id="maps_chekin_del" style="width:245px">
                        <option value="1" <?php if ($cfg['maps_chekin_del']=='1'){?>selected="selected"<?php } ?>>Разрешить</option>
                        <option value="0" <?php if ($cfg['maps_chekin_del']=='0'){?>selected="selected"<?php } ?>>Запретить</option>
		      </select>
                    </td>
                </tr>
        </table>
    </div>
<!--Изображения -->
    <div id="maps_image">
      <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
	<tr id="ya_route">
	  <td width="250">
	    <strong>Доступ: </strong><br/>
	    <span class="hinttext">
	    Кто может добавлять фотографии к точкам
	    </span>
	  </td>
	  <td valign="top">
	    <select name="maps_image_acces" id="maps_image_acces" style="width:245px">
	      <option value="author" <?php if ($cfg['maps_image_acces']=='author'){?>selected="selected"<?php } ?>>Только автор</option>
	      <option value="admin" <?php if ($cfg['maps_image_acces']=='admin'){?>selected="selected"<?php } ?>>Только администратор</option>
	      <option value="all" <?php if ($cfg['maps_image_acces']=='all'){?>selected="selected"<?php } ?>>Все пользователи</option>
	      <option value="none" <?php if ($cfg['maps_image_acces']=='none'){?>selected="selected"<?php } ?>>Отключено</option>
	    </select>
	  </td>
	</tr>
	<tr id="maps_image_original">
	  <td width="250">
	    <strong>Сохранять оригинал: </strong><br/>
	    <span class="hinttext">
	    Сохранять оригинал изображения при загрузке
	    </span>
	  </td>
	  <td valign="top">
	    <select name="maps_image_original" id="maps_image_original" style="width:245px">
	      <option value="1" <?php if ($cfg['maps_image_original']=='1'){?>selected="selected"<?php } ?>>Да</option>
	      <option value="0" <?php if ($cfg['maps_image_original']=='0'){?>selected="selected"<?php } ?>>Нет</option>
	    </select>
	  </td>
	</tr>
      </table>
    </div>
    <div id="advansed">
      <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
	<tr id="ya_route">
	  <td width="250">
	    <strong>Прокладывать маршруты: </strong><br/>
	    <span class="hinttext">
	    Прокладывать маршруты до точек POI
	    </span>
	  </td>
	  <td valign="top">
	    <select name="maps_route" id="maps_route" style="width:245px">
	      <option value="1" <?php if ($cfg['maps_route']=='1'){?>selected="selected"<?php } ?>>Да</option>
	      <option value="0" <?php if ($cfg['maps_route']=='0'){?>selected="selected"<?php } ?>>Нет</option>
	    </select>
	  </td>
	</tr>
	<tr id="ya_traffic">
	  <td width="250">
	    <strong>Проказывать пробки: </strong><br/>
	    <span class="hinttext">
	    Показывать пробки на картах
	    </span>
	  </td>
	  <td valign="top">
	    <select name="maps_traffic" id="maps_traffic" style="width:245px">
	      <option value="1" <?php if ($cfg['maps_traffic']=='1'){?>selected="selected"<?php } ?>>Да</option>
	      <option value="0" <?php if ($cfg['maps_traffic']=='0'){?>selected="selected"<?php } ?>>Нет</option>
	    </select>
	  </td>
	</tr>
	<tr id="ya_chekin">
	  <td>
	    <strong>Включить сервис Chek-In: </strong>
	  </td>
	  <td valign="top">
	    <select name="maps_chekin" id="maps_chekin" style="width:245px">
	      <option value="1" <?php if ($cfg['maps_chekin']=='1'){?>selected="selected"<?php } ?>>Да</option>
	      <option value="0" <?php if ($cfg['maps_chekin']=='0'){?>selected="selected"<?php } ?>>Нет</option>
	    </select>
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
$('#config_tabs > ul#tabs').bind('tabsshow', function(event, ui) {
  var selectedTab = $("#config_tabs > ul#tabs").tabs().data("selected.tabs");
  if(selectedTab == 1)
  {
    map.redraw();
  }
});
</script>