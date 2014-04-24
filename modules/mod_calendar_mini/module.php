<?php
function  mod_calendar_mini($module_id)
{
  $inCore = cmsCore::getInstance();
  $inUser = cmsUser::getInstance();
  $inPage = cmsPage::getInstance();
  $cfg = $inCore->loadComponentConfig('calendar');

  $inPage->addHeadCSS("modules/mod_calendar_mini/css/minicalendar.css");
  $inPage->addHeadJS("modules/mod_calendar_mini/js/minicalendar.js");

  $inCore->loadModel('calendar');
  $model = new cms_model_calendar();

  $year = date("Y");
  $month = date("m");

  $starttime = strtotime(date("Y-m-01"));
  $endtime = strtotime(date("Y-m-t"));
  $events_raw = $model->getCalendar($starttime, $endtime);

  foreach($events_raw as $item)
  {
    $events[date("d-m-Y",$item['start_time'])][] = $item;
  }

  $dayofmonth = date('t');
  $day_count = 1;
  $num = 0;
  for($i = 0; $i < 7; $i++)
  {
    $dayofweek = date('w', mktime(0, 0, 0, date('m'), $day_count, date('Y')));
    $dayofweek = $dayofweek - 1;
    if($dayofweek == -1)
    {
      $dayofweek = 6;
    }
    
    if($dayofweek == $i)
    {
      $week[$num][$i] = $day_count;
      $day_count++;
    }
    else
    {
      $week[$num][$i] = "";
    }
  }

  while(true)
  {
    $num++;
    for($i = 0; $i < 7; $i++)
    {
      $week[$num][$i] = $day_count;
      $day_count++;
      if($day_count > $dayofmonth)
      {
	break;
      }
    }

    if($day_count > $dayofmonth)
    {
      break;
    }
  }

//   echo '<h3 class="module_calendar_mini">Месяц</h3>';
  echo '<table class="module_calendar_mini_table">';
  for($i = 0; $i < count($week); $i++)
  {
    echo "<tr>";
    for($j = 0; $j < 7; $j++)
    {
      if(!empty($week[$i][$j]))
      {
	if(!empty($events[$week[$i][$j]."-".$month."-".$year]))
	{
	  $subclass="have_event";
	  $event_text = '<div class="eventlist"><ul>';
	  foreach($events[$week[$i][$j]."-".$month."-".$year] as $event)
	  {
	    $event_text .= '<li><a href="/calendar/event'.$event['id'].'.html">'.$event['title'].'</a></li>';
	  }
	  $event_text .= '</ul></div>';
	}
	
        if($j == 5 || $j == 6) 
        {
	  echo '<td class="holyday '.$subclass.'">';
	}
        else
        {
	  echo '<td class="'.$subclass.'">';
	}
	echo '<span class="dayview">'.$week[$i][$j].'</span>';
	echo $event_text;
	unset($event_text);
	unset($subclass);
	echo "</td>";
      }
      else
      {
	echo "<td>&nbsp;</td>";
      }
    }
    echo "</tr>";
  } 
  echo "</table>";
  return TRUE;
}
?>