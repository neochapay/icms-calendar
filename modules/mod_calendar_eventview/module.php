<?php

function  mod_calendar_eventview($module_id, $cfg)
{
  $inCore = cmsCore::getInstance();
  $inCore->loadModel('calendar');
    
  $c_cfg = $inCore->loadComponentConfig('calendar');
  
  $model = new cms_model_calendar();
  
  if(!$cfg['event_rate'])
  {
    $cfg['event_rate'] = 7;
  }
  
  $start = time();
  $end = $start+$cfg['event_rate']*24*60*60;

  $events = $model->getCalendar($start,$end);

  $smarty = cmsPage::initTemplate('modules', 'mod_calendar_eventview.tpl');
  $smarty->assign('events', $events);
  
  $smarty->assign('start', $start);
  $smarty->assign('end', $end);
  
  $smarty->assign('f_start', cmsCore::dateFormat(date('Y-m-d H:i:s',$start),0,0,0));
  $smarty->assign('f_end', cmsCore::dateFormat(date('Y-m-d H:i:s',$end),0,0,0));
  
  $smarty->display('mod_calendar_eventview.tpl');  
  return true;
}
?>
