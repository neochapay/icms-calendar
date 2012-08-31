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
	GROUP BY cms_events.id ORDER BY cms_events.start_time ASC LIMIT ".$cfg['calendar_module_count'];
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
	    ORDER BY cms_events.start_time ASC LIMIT ".$cfg['calendar_module_count'];    
  }

  $result = $DB->query($sql);
    
  if ($DB->error()) 
  { 
      return false;
  }
    
  $events = array();
  while ($event = $DB->fetch_assoc($result))
  {
    if($event['author_id'] == $inUser->id)
    {
      $event['time'] == "";
    }

    if($event['category_id'] == 0)
    {
      $event['bg'] = '#C3BCB9';
      $event['tx'] = '#000000';
    }
    
    $events[] = $event;
  }

  $smarty = $inCore->initSmarty('modules', 'mod_calendar_eventview.tpl');
  $smarty->assign('events', $events);
  $smarty->display('mod_calendar_eventview.tpl');  
  return true;
}
?>
