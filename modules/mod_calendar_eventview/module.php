<?php

function  mod_calendar_eventview($module_id)
{
  $inCore = cmsCore::getInstance();
  $inUser = cmsUser::getInstance();
  $DB = cmsDatabase::getInstance();
  $cfg = $inCore->loadComponentConfig('calendar');
  
  if($inUser->id != 0 and $cfg['calendar_module'] == "user")
  {
//Запрос к БД
      $sql = "
	SELECT cms_events.* ,
	cms_events_category.tx,
	cms_events_category.bg
	FROM cms_events 
	LEFT JOIN cms_events_signup ON cms_events.id=cms_events_signup.event_id 
	LEFT JOIN cms_events_category ON cms_events.category_id = cms_events_category.id
	WHERE 
	cms_events.start_time > ".time()." AND 
	cms_events.author_id = ".$inUser->id." OR 
	cms_events_signup.user_id = ".$inUser->id." AND 
	cms_events.author_id <> ".$inUser->id." AND 
	cms_events.start_time > ".time()." 
	GROUP BY cms_events.id ORDER BY cms_events.start_time ASC LIMIT 5";
  }
  else
  {
    $sql = "SELECT cms_events.*,
	    cms_events_category.tx,
	    cms_events_category.bg
	    FROM cms_events
	    LEFT JOIN cms_events_category ON cms_events.category_id = cms_events_category.id	    
	    WHERE cms_events.`type` = 'public' 
	    AND cms_events.start_time > ".time()." 
	    ORDER BY cms_events.start_time ASC LIMIT 5";    
  }

  $result = $DB->query($sql);
    
  if ($DB->error()) 
  { 
      return false;
  }
    
  $events = array();
  while ($event = $DB->fetch_assoc($result))
  {
    $sql1 = "SELECT * FROM cms_fotolib WHERE `type`='calendar' AND `photo_id` = ".$event['id']." LIMIT 1";
    $result1 = $DB->query($sql1);
    $foto = $DB->fetch_assoc($result1);
    if($foto['name'] != "")
    {
      $event['image'] = $foto['name'].".jpg";
    }
    unset($foto);

    $event['url'] = "/calendar/event".$event['id'].".html";
    $event['description'] = $event['content'];
    $events[] = $event;
  }

  $smarty = $inCore->initSmarty('modules', 'mod_calendar_eventview.tpl');
  $smarty->assign('articles', $events);
  $smarty->display('mod_calendar_eventview.tpl');  
  return true;
}
?>
