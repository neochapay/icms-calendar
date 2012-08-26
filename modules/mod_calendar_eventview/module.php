<?php

function  mod_calendar_eventview($module_id)
{
  $inCore = cmsCore::getInstance();
  $inUser = cmsUser::getInstance();
  $DB = cmsDatabase::getInstance();
  if($inUser->id != 0)
  {
//Запрос к БД
    $sql = "
	SELECT * FROM cms_events 
	LEFT JOIN cms_events_signup ON cms_events.id=cms_events_signup.event_id 
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
    $sql = "SELECT * FROM cms_events WHERE `type` = 'public' AND start_time > ".time()." ORDER BY start_time ASC LIMIT 5";
  }
    $result = $DB->query($sql);
    
    if ($DB->error()) 
    { 
      print mysql_error();
      return true;
    }

    if (!$DB->num_rows($result)) 
    { 
      $has_event = FALSE; 
    }
    else
    {
      $has_event = TRUE;
    }
    
    $events = array();
    while ($event = $DB->fetch_assoc($result))
    {
      if($event['author_id'] == $inUser->id)
      {
	  $event['time'] == "";
      }
      $events[] = $event;
    }

    $smarty = $inCore->initSmarty('modules', 'mod_calendar_eventview.tpl');
    $smarty->assign('has_event', $has_event);
    $smarty->assign('events', $events);
    $smarty->display('mod_calendar_eventview.tpl');  
    return true;
  
}
?>
