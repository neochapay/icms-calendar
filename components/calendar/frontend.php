<?php
function declension($digit,$expr,$onlyword=false)
{
        if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
        if(empty($expr[2])) $expr[2]=$expr[1];
        $i=preg_replace('/[^0-9]+/s','',$digit)%100; //intval не всегда корректно работает
        if($onlyword) $digit='';
        if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
        else
        {
                $i%=10;
                if($i==1) $res=$digit.' '.$expr[0];
                elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
                else $res=$digit.' '.$expr[2];
        }
        return trim($res);
}

function calendar()
{
  $inCore = cmsCore::getInstance();
  $inPage = cmsPage::getInstance();
  $inUser = cmsUser::getInstance();

  $inCore->loadModel('calendar');

  $model = new cms_model_calendar();

  $do = $inCore->request('do', 'str', 'view');
  $cfg = $inCore->loadComponentConfig('calendar');

  if ($do == 'view')
  {
    $inPage->setTitle("Календарь событий");
    $smarty = $inCore->initSmarty('components', 'com_calendar_view.tpl');

    $guest = TRUE;
    
    if($inUser->id == 0 and $cfg['calendar_access'] == "all")
    {
      $guest = FALSE;
    }
    
    if($inUser->id != 0 and $cfg['calendar_access'] == "users")
    {
      $guest = FALSE;
    }
    
    if($inUser->is_admin)
    {
      $guest = FALSE;
    }

    $catigories = $model->getAllCategories();
    
    if($cfg['calendar_view']=='afisha')
    {
      $inCore->redirect('/calendar/list.html');
    }
    $smarty->assign('guest', $guest);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('catigories', $catigories);
    $smarty->assign('category', "all");
    $smarty->display('com_calendar_view.tpl');
    return;
  }

  if($do == "list")
  {
    $inPage->addPathway("Календарь","/calendar");
    $inPage->addPathway("Афиша");
    
    $per_day = 10; //Количество сообытий в дне максимум
    $display_days = 5; //количество дней которые показывается на странице

    $dayt = 60*60*24; //Продолжительность дня в секундах
    $start_time = strtotime("NOW 00:00:00"); //Определяем утро сегодняшнего дня
    
    $output = array();
    
    for($i=0;$i<$display_days;$i++)
    {
      $start = strtotime(date('Y-m-d',$start_time+$dayt*($i+1))." 00:00:00");
      $n = date("N",$start+1);
      //Определяем заголовок блока дня
      if($i == 0)
      {
	$day['title'] = "Сегодня";
      }
      elseif($i == 1)
      {
	$day['title'] = "Завтра";
      }
      else
      {
	$day['title'] = $inCore->dateFormat(date('Y-m-d H:i:s',$start_time+$dayt*($i+1)));
      }

      $day['events'] = $model->getCalendar($start,$start+86400); //Определяем активные встречи за временной период
      
      $output[] = $day;
    }
  
    $inPage->setTitle("Календарь событий");
    $smarty = $inCore->initSmarty('components', 'com_calendar_list.tpl');
    $smarty->assign('events', $output);
    $smarty->assign('cfg', $cfg);
    $smarty->display('com_calendar_list.tpl');
  }
  
  if($do == "category_view")
  {
    $category_id = $inCore->request('category_id', 'int', 0);
    $smarty = $inCore->initSmarty('components', 'com_calendar_view.tpl');

    $guest = TRUE;
    
    if($inUser->id == 0 and $cfg['calendar_access'] == "all")
    {
      $guest = FALSE;
    }
    
    if($inUser->id != 0 and $cfg['calendar_access'] == "users")
    {
      $guest = FALSE;
    }
    
    if($inUser->is_admin)
    {
      $guest = FALSE;
    }
    $category = $model->getCategory($category_id);
    
    $catigories = $model->getAllCategories();
    
    if(!$category)
    {
      //$inCore->redirect("/calendar");
      print mysql_error();
    }
    
    $inPage->setTitle("Календарь событий:".$category['title']);
    $smarty->assign('guest', $guest);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('catigories', $catigories);
    $smarty->assign('category', $category_id);
    $smarty->display('com_calendar_view.tpl');
    return;    
  }
  
  if ($do == 'add')
  {
    $guest = TRUE;
    
    if($inUser->id == 0 and $cfg['calendar_access'] == "all")
    {
      $guest = FALSE;
    }
    
    if($inUser->id != 0 and $cfg['calendar_access'] == "users")
    {
      $guest = FALSE;
    }
    
    if($inUser->is_admin)
    {
      $guest = FALSE;
    }  
  
  
    if($guest)
    {
      $inCore->redirect('/'); exit;
    }

    $is_send = $inCore->inRequest('title');
    if($is_send)
    {
      $title = $inCore->request('title', 'str');
      $type_raw = $inCore->request('type', 'str');
      $date_start = $inCore->request('date_start', 'str');
      $date_end = $inCore->request('date_end', 'str');
      $hour_start = $inCore->request('hour_start', 'str');
      $hour_end = $inCore->request('hour_end', 'str');
      $min_start = $inCore->request('min_start', 'str');
      $min_end = $inCore->request('min_end', 'str');
      $content = $inCore->request('content', 'str');
      if(!$title || !$type_raw || !$date_start || !$hour_start || !$hour_end || !$min_start || !$min_end  )
      {
      	cmsCore::addSessionMessage('Ой, что то не было заполнено...', 'error');
	$inCore->redirectBack();
	exit;
      }
      else
      {
	$start_time = strtotime($date_start.' '.$hour_start.':'.$min_start);
	$end_time = strtotime($date_end.' '.$hour_end.':'.$min_end);

	if($data_end == "" or $date_end < $date_start)
	{
	  $data_end = $data_start;
	}
	$type_data = explode("_",$type_raw);
	$type = $type_data[0];
	$apx = $type_data[1];
	$event_id = $model->addEvent($inUser->id,$type,$apx,$start_time,$end_time,$title,$content);

	if($event_id)
	{
	  if($type != "private")
	  {
	    cmsActions::log('add_event', array(
                'object' => 'событие',
                'object_url' => '/calendar/event'.$event_id.'.html',
                'object_id'=>$event_id,
                'target' => $title,
                'target_url' => '/calendar/event'.$event_id.'.html',
                'target_id' => '0',
                'description' => $title));
	  }
	  cmsCore::addSessionMessage('Ваше мероприятие добавлено!', 'success');
	}
 	else
 	{
	  cmsCore::addSessionMessage('Ошибка добавления!', 'error');
 	}
	$inCore->redirect('/calendar'); exit;
      }
    }
      if($event['start_time'] == "")
      {
	$event['start_time'] = time();
      }

      if($event['end_time'] == "")
      {
	$event['end_time'] = time();
      }

      $bb_toolbar = cmsPage::getBBCodeToolbar('message',$cfg['img_on'], 'forum');
      $smilies    = cmsPage::getSmilesPanel('message');
      $inPage->setTitle("Добавить событие");
      $smarty = $inCore->initSmarty('components', 'com_calendar_add.tpl');
      $smarty->assign('bb_toolbar', $bb_toolbar);
      $smarty->assign('smilies', $smilies);
      $smarty->assign('title', $event['title']);
      $smarty->assign('content', $event['content']);
      $smarty->assign('type', $event['type']);
      $smarty->assign('start_date', date("d.m.Y", $event['start_time']));
      $smarty->assign('start_hour', date("H", $event['start_time']));
      $smarty->assign('start_min', date("i", $event['start_time']));
      $smarty->assign('end_date', date("d.m.Y", $event['end_time']));
      $smarty->assign('end_hour', date("H", $event['end_time']));
      $smarty->assign('end_min', date("i", $event['end_time']));
      $smarty->display('com_calendar_add.tpl');
      return;
  }

  if($do == "view_event")
  {
    $event_id = $inCore->request('event_id', 'int', 0);
    
/*FOTOLIB*/
    include('fotolib.class.php');
    $foto = new FotoLib();
    //Проверяем можем ли добавлять фото
    $allow_add_foto = $foto->addAcces("calendar");
  
    if($_FILES)
    {
      $foto->uploadFoto($_FILES, "calendar", $event_id);
    }
        
    $images = $foto->loadImages("calendar",$event_id);
/*FOTOLIB*/    
    
    $event = $model->getEvent($event_id);

    if(!$event)
    {
      cmsCore::addSessionMessage('Ошибка запроса'.mysql_error(), 'error');
      $inCore->redirect('/calendar');
      exit;
    }
    else
    {
      if($event['type'] == 'private' and $event['author_id'] != ($inUser->id))
      {
        cmsCore::addSessionMessage('Ошибка запроса', 'error');
	$inCore->redirect('/calendar');
	exit;
      }
      $msg = $inCore->parseSmiles($event['content'], true);

      if(count($event['parent']) != 0)
      {
	$delta = $event['end_time']-$event['start_time'];
	
	$day = date("d", $event['start_time']);
	$month = date("n", $event['start_time'])-1; //Яваскриптики считают с 0
	$year = date("Y", $event['start_time']);
	
	if($delta < 24*60*60)
	{
	  $calendar_view = "agendaDay";
	  if(date("d", $event['start_time']) != date("d", $event['end_time']))
	  {
	    //Если меньше 24 часов но всё же в разные дни включаем недельный вид
	    $calendar_view = "agendaWeek";
	  }
	}
	elseif($delta < 7*24*60*60)
	{
	  $calendar_view = "agendaWeek";
	}
	else
	{
	  $calendar_view = "month";
	}
	
	$events_string = "";
	foreach($event['parent'] as $parent)
	{
	  $events_string .= "{
	  id    : '".$parent['id']."',
	  title : '".str_replace("'",'"',$parent['title'])."',
	  start : '".date("Y-m-d H:i:s", $parent['start_time'])."',
	  end   : '".date("Y-m-d H:i:s", $parent['end_time'])."',
	  url   : '/calendar/event".$parent['id'].".html',";
	
	  if($parent['end_time']-$parent['start_time'] > 60*60*8)
	  {
	    $events_string .= "allDay: true,";
	  }
	  else
	  {
	    $events_string .= "allDay: false,";
	  }
	
	  switch($parent['type'])
	  {
	    case "public" :
	    if($parent['author_id'] == $inUser->id)
	    {
	      $events_string .= "color: '#B9C3BC',\n";
	    }
	    else
	    {
	      $events_string .= "color: '#C3BCB9',\n";
	    }
	    $events_string .= "textColor: '#000000',\n";
	    break;
	    case "private" :
	    $events_string .= "color: '#3366CC'\n";
	  }
	  $events_string .= "},";
	}
      }
      
      $issignup = $model->isSignup($event_id);

      $singups_user = $model->getSingupsUsers($event_id);

      $inPage->setTitle('Просмотр события "'.$event['title'].'"');
      $inPage->addPathway("Календарь", "/calendar"); 
      if($event['parent_id'])
      {
	$parent = $model->getEvent($event['parent_id']);
	$inPage->addPathway($parent['title'], "/calendar/event".$parent['id'].".html"); 
      }
      $inPage->addPathway($event['title'], "/calendar/event".$event_id.".html");
      
      $smarty = $inCore->initSmarty('components', 'com_calendar_event_view.tpl');
      $smarty->assign('event', $event);
      $smarty->assign('content', $msg);
      
      $smarty->assign('parent', $event['parent']);
      $smarty->assign('calendar_view', $calendar_view);
      $smarty->assign('events_string', $events_string);
      
      $smarty->assign('year', $year);
      $smarty->assign('month', $month);
      $smarty->assign('day', $day);
      
      $smarty->assign('start_time', date("d.m.Y H:i", $event['start_time']));
      $smarty->assign('end_time', date("d.m.Y H:i", $event['end_time']));
      $smarty->assign('issngnup', $issignup);
      $smarty->assign('singups_user', $singups_user);
      
      $smarty->assign('images', $images); //fotolib
      $smarty->assign('allow_add_foto', $allow_add_foto); //fotolib    

      if($event['start_time'] - time() < 0)
      {
	$status = "Прошедшее событие";
      }
      else
      {
	if($event['start_time']-time() > 86400)
	{
	  $status = 'Осталось '.declension(round(($event['start_time']-time())/86400), array("день", "дня", "дней"));
	}
	else
	{
	  $status = 'Осталось '.declension(round(($event['start_time']-time())/3600), array("час", "часа", "часов"));
	}
      }
      $smarty->assign('status', $status);
      if($inUser->id == $event['author_id'] or $inUser->is_admin)
      {
	 $smarty->assign('admin', TRUE);
      }
      $smarty->display('com_calendar_event_view.tpl');
      $inCore->includeComments();
      comments('calendar', $event_id);
      return;
    }
  }
  
  if($do == "delete_event")
  {
    if($inUser->id == 0)
    {
      $inCore->redirect('/'); exit;
    }
    $event_id = $inCore->request('event_id', 'int', 0);
    $deleted = $model->deleteEvent($event_id);
    if($deleted)
    {
      cmsCore::addSessionMessage('Ваше мероприятие удалено!', 'success');
      $inCore->redirect('/calendar');
      exit;
    }
    else
    {
      cmsCore::addSessionMessage('Ошибка', 'error');
      $inCore->redirect('/calendar');
      exit;
    }
  }

  if($do == "edit_event")
  {
    if($inUser->id == 0)
    {
      $inCore->redirect('/'); exit;
    }
    
    $event_id = $inCore->request('event_id', 'int', 0);
    $event = $model->getEvent($event_id);

    if($event["author_id"] != $inUser->id or !$inUser->is_admin)
    {
	cmsCore::addSessionMessage('Ошибка доступа', 'error');
	$inCore->redirectBack();
	exit;
    }


    $is_send = $inCore->inRequest('title');
    if($is_send)
    {
      $title = $inCore->request('title', 'str');
      $type = $inCore->request('type', 'str');
      $date_start = $inCore->request('date_start', 'str');
      $date_end = $inCore->request('date_end', 'str');
      $hour_start = $inCore->request('hour_start', 'str');
      $hour_end = $inCore->request('hour_end', 'str');
      $min_start = $inCore->request('min_start', 'str');
      $min_end = $inCore->request('min_end', 'str');
      $content = $inCore->request('content', 'str');
      if(!$title || !$type || !$date_start || !$hour_start || !$hour_end || !$min_start || !$min_end  )
      {
      	cmsCore::addSessionMessage('Ой, что то не было заполнено...', 'error');
	$inCore->redirectBack();
	exit;
      }
      else
      {
	$start_time = strtotime($date_start.' '.$hour_start.':'.$min_start);
	if($data_end == "" or $data_end < $data_start)
	{
	  $data_end = $data_start;
	}
	$end_time = strtotime($date_end.' '.$hour_end.':'.$min_end);
	
	if(!is_numeric($type))
	{
	  $category_id = "0";
	}
	else
	{
	  $category_id = $type;
	  $type = "public";
	}
	
	$update = $model->updateEvent($event_id,$type,$category_id,$start_time,$end_time,$title,$content);
	if($update)
	{
	  cmsCore::addSessionMessage('Ваше мероприятие изменено!', 'success');
	  $inCore->redirect('/calendar/event'.$event_id.'.html');
	  exit;
	}
	else
	{
	  cmsCore::addSessionMessage('Ошибка добавления!', 'error');
	  $inCore->redirect('/calendar/edit'.$event_id.'.html');
	  exit;
	}
      }
    }
    else
    {
      $event = $model->getEvent($event_id);
      if(!$event)
      {
	cmsCore::addSessionMessage('Ошибка запроса', 'error');
	$inCore->redirect('/calendar');
	exit;
      }
      
      if($event['category_id'])
      {
	$event['type'] = $event['category_id'];
      }
      $bb_toolbar = cmsPage::getBBCodeToolbar('calendar',1, 'forum');
      $smilies    = cmsPage::getSmilesPanel('calendar');
//Проверяем присоедиялся ли пользователь ко встрече

      $catigories = $model->getAllCategories();

      $inPage->setTitle("Редактировать событие");
      $smarty = $inCore->initSmarty('components', 'com_calendar_add.tpl');
      $smarty->assign('event', $event);
      $smarty->assign('edit', 1);
      $smarty->assign('cfg', $cfg);
      $smarty->assign('catigories', $catigories);
      $smarty->assign('title', $event['title']);
      $smarty->assign('content', $event['content']);
      $smarty->assign('type', $event['type']);
      $smarty->assign('start_date', date("d.m.Y", $event['start_time']));
      $smarty->assign('start_hour', date("H", $event['start_time']));
      $smarty->assign('start_min', date("i", $event['start_time']));
      $smarty->assign('end_date', date("d.m.Y", $event['end_time']));
      $smarty->assign('end_hour', date("H", $event['end_time']));
      $smarty->assign('end_min', date("i", $event['end_time']));
      $smarty->assign('bb_toolbar', $bb_toolbar);
      $smarty->assign('smilies', $smilies);

      $smarty->display('com_calendar_add.tpl');
      return;
    }
  }

  if($do == "add_parent")
  {
    if($inUser->id == 0)
    {
      $inCore->redirectBack();
      return;
    }
    $event_id = $inCore->request('event_id', 'int', 0);
    $event = $model->getEvent($event_id);
    
    if(!$event)
    {
      $inCore->redirectBack();
      return;    
    }
    
    $is_send = $inCore->inRequest('title');
    if($is_send)
    {
      $title = $inCore->request('title', 'str');
      
      $date_start = $inCore->request('date_start', 'str');
      $date_end = $inCore->request('date_end', 'str');
      $hour_start = $inCore->request('hour_start', 'str');
      $hour_end = $inCore->request('hour_end', 'str');
      $min_start = $inCore->request('min_start', 'str');
      $min_end = $inCore->request('min_end', 'str');
      $content = $inCore->request('content', 'str');
      if(!$title || !$date_start || !$hour_start || !$hour_end || !$min_start || !$min_end  )
      {
      	cmsCore::addSessionMessage('Ой, что то не было заполнено...', 'error');
	$inCore->redirectBack();
	exit;
      }
      else
      {
	$start_time = strtotime($date_start.' '.$hour_start.':'.$min_start);
	$end_time = strtotime($date_end.' '.$hour_end.':'.$min_end);

	if($data_end == "" or $date_end < $date_start)
	{
	  $data_end = $data_start;
	}
	$type = $event['type'];
	$model->addEvent($inUser->id,$type,$apx,$start_time,$end_time,$title,$content,$event['id']);
	$inCore->redirect('/calendar/event'.$event['id'].".html"); exit;
      }
    }

    $bb_toolbar = cmsPage::getBBCodeToolbar('message',$cfg['img_on'], 'forum');
    $smilies    = cmsPage::getSmilesPanel('message');
    $inPage->setTitle("Добавить вложеное событие");
    $smarty = $inCore->initSmarty('components', 'com_calendar_add.tpl');
    $smarty->assign('bb_toolbar', $bb_toolbar);
    $smarty->assign('smilies', $smilies);
    $smarty->assign('parent', "1");
    $smarty->assign('edit', "1");
    $smarty->assign('parent_title', $event['title']);
    $smarty->assign('title', "");
    $smarty->assign('cfg', $cfg);
    $smarty->assign('content', "");
    $smarty->assign('type', $event['type']);
    $smarty->assign('start_date', date("d.m.Y", $event['start_time']));
    $smarty->assign('start_hour', date("H", $event['start_time']));
    $smarty->assign('start_min', date("i", $event['start_time']));
    $smarty->assign('end_date', date("d.m.Y", $event['end_time']));
    $smarty->assign('end_hour', date("H", $event['end_time']));
    $smarty->assign('end_min', date("i", $event['end_time']));
    $smarty->display('com_calendar_add.tpl');
    return;
  }
  
  if($do == "event_signup")
  {
//Если не залогинились то возвращаем обратно
    if($inUser->id == 0)
    {
      $inCore->redirectBack();
      return;
    }
    $event_id = $inCore->request('event_id', 'int', 0);
    $event = $model->getEvent($event_id);
//Если приватное событие то отправляем обратно
    if($event["type"] == "private")
    {
      $inCore->redirectBack();
      return;
    }
//Если мероприятие уже прошло говорим ай-яй-яй
    if($event['start_time'] - time() < 0)
    {
      cmsCore::addSessionMessage('Присоединиться к мероприятию уже невозможно', 'error');
      $inCore->redirectBack();
      exit;
    }
//Проверяем статус присоединения ко встрече
    if($model->isSignup($event_id))
    {
//Если уже присоединялись то удаляем и выводим статус
      $model->deleteSignup($event_id);
      cmsCore::addSessionMessage('Вы отказались от участия в мероприятии '.$event["title"], 'success');
// Добавляем событие в ленту
      cmsActions::log('del_signup', array(
                'object' =>  $event["title"],
                'object_url' => '/calendar/event'.$event_id.'.html',
                'object_id'=> $event_id,
                'target' => '',
                'target_url' => '/calendar/event'.$event_id.'.html',
                'target_id' => '0',
                'description' => ''));

      $inCore->redirect('/calendar/event'.$event_id.'.html');
    }
    else
    {
//Если не присоединялись то добавляем в базу и выводим статус
//Если уже присоединялись то удаляем и выводим статус
      $model->addSignup($event["id"]);
      cmsCore::addSessionMessage('Вы присоединились к событию '.$event["title"], 'success');
// Добавляем событие в ленту
      cmsActions::log('add_signup', array(
                'object' =>  $event["title"],
                'object_url' => '/calendar/event'.$event_id.'.html',
                'object_id'=> $event["id"],
                'target' => '',
                'target_url' => '/calendar/event'.$event_id.'.html',
                'target_id' => '0',
                'description' => ''));
      $inCore->redirect('/calendar/event'.$event_id.'.html');

    }
    return;
  }
//AJAX  
  if($do == "ajax_add")
  {
    $guest = TRUE;
    
    if($inUser->id == 0 and $cfg['calendar_access'] == "all")
    {
      $guest = FALSE;
    }
    
    if($inUser->id != 0 and $cfg['calendar_access'] == "users")
    {
      $guest = FALSE;
    }
    
    if($inUser->is_admin)
    {
      $guest = FALSE;
    }  
  
    if(!$guest)
    {
      $title = $inCore->request('title', 'str');
      $type = $inCore->request('type', 'str');
      $date_start = $inCore->request('date_start', 'str');
      $date_end = $inCore->request('date_end', 'str');
      $hour_start = $inCore->request('hour_start', 'str');
      $hour_end = $inCore->request('hour_end', 'str');
      $min_start = $inCore->request('min_start', 'str');
      $min_end = $inCore->request('min_end', 'str');
      $content = $inCore->request('content', 'str');
      
      $start_time = strtotime($date_start.' '.$hour_start.':'.$min_start);
      $end_time = strtotime($date_end.' '.$hour_end.':'.$min_end);
      
      if($start_hour < $cfg['calendar_minTime'])
      {
	$output['error'] = TRUE;
	$output['errortext'] = "Событие начинается слишком рано";      
      }
      
      if($end_hour > $cfg['calendar_maxTime'])
      {
	$output['error'] = TRUE;
	$output['errortext'] = "Событие заканчивается слишком поздно";            
      }
      
      if($end_hour == $cfg['calendar_maxTime'] and $end_min != 0)
      {
	$output['error'] = TRUE;
	$output['errortext'] = "Событие заканчивается слишком поздно";  
      }
      
      
      if($type == "private")
      {
	$category_id = 0;
	$type = "private";
      }
      else
      {
	if(is_numeric($type))
	{
	  $category_id = $type;
	}
	else
	{
	  $category_id = 0;
	}
	$type = "public";
      }
      
      $event_id = $model->addEvent($inUser->id,$type,$category_id,$start_time,$end_time,$title,$content);
      
      $output = array();
      
      if(!$event_id)
      {
	$output['error'] = TRUE;
	$output['errortext'] = "Ошибка БД";
      }
      else
      {
	$output['error'] = FALSE;
	$event = $model->getEvent($event_id);
	$output['event_id'] = $event_id;
	$output['start'] = $event['start_time'];
	$output['end_time'] = $event['end_time'];
	
	if($event['end_time']-$event['start_time'] > 60*60*8)
	{
	  $output['allDay'] = TRUE;
	}
	else
	{
	  $output['allDay'] = FALSE;
	}
	
	$output['bg'] = $event['bg'];
	$output['tx'] = $event['tx'];
	
	if($type != "private")
	{
	  cmsActions::log('add_event', array(
              'object' => 'событие',
              'object_url' => '/calendar/event'.$event_id.'.html',
              'object_id'=>$event_id,
              'target' => $title,
              'target_url' => '/calendar/event'.$event_id.'.html',
              'target_id' => '0',
              'description' => $title));
	}
      }
    }
    else
    {
      $output['error'] = TRUE;
      $output['errortext'] = "Ошибка доступа";
    }
    print json_encode($output);
    exit;
  }
  
  if($do == "ajax_edit")
  {
    $type_act = $inCore->request('type', 'str');
    $id = $inCore->request('id', 'int');
    $dayDelta = $inCore->request('dayDelta', 'str');
    $minuteDelta = $inCore->request('minuteDelta', 'str');
    
    $event = $model->getEvent($id);
    if($event['author_id'] == $inUser->id)
    {
      $type = $event['type'];
      $category_id = $event['category_id'];
      $start_time = $event['start_time'];
      $end_time = $event['end_time'];
      $title = $event['title'];
      $content = $event['content'];
      
      $delta = $dayDelta*24*60*60+$minuteDelta*60;
      
      if($type_act == "drop")
      {
	$start_time = $start_time+$delta;
	$end_time = $end_time+$delta;
      }
    
      if($type_act == "resize")
      {
	$end_time = $end_time+$delta;
      }
      $model->updateEvent($id,$type,$category_id,$start_time,$end_time,$title,$content);
    }
    else
    {
      print "Ошибка доступа";
    }
    exit;
  }
  
  
  if($do == "ajax_get_event")
  {
    $starttime = $inCore->request('start', 'int');
    $endtime = $inCore->request('end', 'int');
    $parent_id = $inCore->request('parent_id', 'parent_id');
    $category = $inCore->request('category', 'str');
    
    if($category == "all" or !is_numeric($category))
    {
      $category = FALSE;
    }
    
    if(!$parent_id)
    {
      $parent_id = 0;
    }
    
    $events = $model->getCalendar($starttime, $endtime, $category,$parent_id);
    $output = array();
    foreach($events as $data)
    {
      $data['start'] = date("Y-m-d H:i:s",$data["start_time"]);
      $data['end'] = date("Y-m-d H:i:s",$data["end_time"]);
      $data['url'] = "/calendar/event".$data['id'].".html";
      
      if($data["author_id"] == $inUser->id)
      {
	$data['editable'] = true;
      }
      else
      {
	$data['editable'] = false;	
      }
	
      if($data["end_time"]-$data["start_time"] > 60*60*8)
      {
	$data['allDay'] = "true";
      }
	
      if(!$data['category_id'])
      {
	switch($data["type"])
	{
	  case "public" :
	    $data['color'] = $cfg['public_bg_color'];
	    $data['textColor'] = $cfg['public_tx_color'];
	    break;
	  case "private" :
	    $data['color'] = $cfg['private_bg_color'];
	    $data['textColor'] = $cfg['private_tx_color'];
	    break;
	}
      }
      else
      {
	$data['color'] = $data['bg'];
	$data['textColor'] = $data['tx'];
      }

      if($data["type"] == "private" and $data['author_id'] != $inUser->id)
      {
      }
      else
      {
	$output[] = $data;
      }
    }
    
    print json_encode($output);
    exit;
  }
  
  if($do == "isc_calendar")
  {
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename=calendar.ics');
    echo "BEGIN:VCALENDAR\n";
    echo "VERSION:2.0\n";
    echo "PRODID:-//hacksw/handcal//NONSGML v1.0//EN'\n";
    $events = $model->getCalendar(time()-60*60*24*30, time()+60*60*24*30, 0);
    
    foreach($events as $event)
    {
      if($event['type'] == "public")
      {
	$title = $event["title"];

	$dtstart = date("Ymd",$event["start_time"])."T".date("His",$event["start_time"]);
	$dtend = date("Ymd",$event["end_time"])."T".date("His",$event["end_time"]);
	
	echo "BEGIN:VEVENT\n";
	echo "DTSTART:$dtstart\n";
	echo "DTEND:$dtend\n";
	echo "SUMMARY:$title\n";
	echo "END:VEVENT\n";
      }
    }
    echo "END:VCALENDAR\n";
    exit;
  }

  if($do == "ajax_add_form")
  {
    $start = strtotime($inCore->request('start', 'str'));
    $end = strtotime($inCore->request('end', 'str'));
    
    if($start < time())
    {
      echo 'error';
      exit;
    }
    
    if(!$inUser->id)
    {
      echo "error";
      exit;
    }
//Коректность времени добавления
    $start_hour = date("H", $start);
    if($start_hour < $cfg['calendar_minTime'])
    {
      $start_hour = $cfg['calendar_minTime'];
    }
    
    $end_hour = date("H", $end);
    $end_min = date("i", $end);
    
    if($end_hour > $cfg['calendar_maxTime'])
    {
      $end_hour = $cfg['calendar_maxTime'];
      $end_min = "00";
    }
    
    $catigories = $model->getAllCategories();
    $bb_toolbar = cmsPage::getBBCodeToolbar('message',$cfg['img_on'], 'forum');
    $smilies    = cmsPage::getSmilesPanel('message');
      
    $smarty = $inCore->initSmarty('components', 'com_calendar_add.tpl');
    $smarty->assign('catigories', $catigories);
    $smarty->assign('start_date', date("d.m.Y", $start));
    $smarty->assign('start_hour', $start_hour);
    $smarty->assign('start_min', date("i", $start));
    $smarty->assign('end_date', date("d.m.Y", $end));
    $smarty->assign('end_hour', $end_hour);
    $smarty->assign('end_min', $end_min);
    $smarty->assign('bb_toolbar', $bb_toolbar);
    $smarty->assign('smilies', $smilies);
    $smarty->assign('cfg', $cfg);
    $smarty->display('com_calendar_add.tpl');
    exit;
  }
//   FOTOLIB
  if($do == "imagerotate")
  {
    $side = $md5 = $inCore->request('side', 'str');
    $image_id = $inCore->request('image_id', 'int');
    
    include('fotolib.class.php');
    $foto = new FotoLib();
    $foto->Rotate($side,$image_id);
    $inCore->redirectBack();
    exit;
  }
  
  if($do == "imagedelete")
  {
    include('fotolib.class.php');
    $image_id = $inCore->request('image_id', 'int');
    $foto = new FotoLib();
    $foto->Delete($image_id);
    $inCore->redirectBack();
    exit;
  }
}
?>