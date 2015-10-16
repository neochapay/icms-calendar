<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
 
class cms_model_calendar
{
  function __construct()
  {
    $this->inDB = cmsDatabase::getInstance();
    $this->config = cmsCore::getInstance()->loadComponentConfig('calendar');
  }

    public static function getDefaultConfig() {

        $cfg = array (
		'calendar_view'=> 'agendaWeek',
		'calendar_access' => 'admin',
		'maps_image_acces' => 'admin',
		'maps_image_original' => '1',
		'private_bg_color' => '#3366CC',
		'private_tx_color' => '#000000',
		'public_bg_color' => '#C3BCB9',
		'public_tx_color' => '#000000',
		'calendar_module' => 'all',
		'calendar_module_count' => '5',
		'calendar_firstHour' => '6',
		'calendar_minTime' => '0',
		'calendar_maxTime' => '24'
		);

        return $cfg;

    }

  function addEvent($author_id,$type,$category_id,$start_time,$end_time,$title,$content,$parent_id = 0)
  {
    $sql = "INSERT INTO cms_events (author_id , type, category_id, start_time, end_time, title, content, parent_id) VALUES
				   ('{$author_id}', '{$type}', '{$category_id}', '{$start_time}','{$end_time}','{$title}','{$content}','{$parent_id}')";
    $this->inDB->query($sql);

    if ($this->inDB->error()) 
    { 
      return false; 
    }
    else
    {
	return $this->inDB->get_last_id('cms_events');
    }
  }

  function updateEvent($event_id,$type,$category_id,$start_time,$end_time,$title,$content)
  {
    $sql = "UPDATE cms_events SET 
    type = '$type', 
    category_id = '$category_id', 
    start_time = '$start_time', 
    end_time = '$end_time', 
    title = '$title', 
    content = '$content' WHERE id = '$event_id'";

    $this->inDB->query($sql);

    if ($this->inDB->error()) 
    { 
      return false; 
    }
    else
    {
      return true;
    }
  }

  function getEvent($event_id)
  {
    $sql = "SELECT cms_events.* ,
    cms_events_category.name as category_name,
    cms_events_category.bg,
    cms_events_category.tx,
    cms_events_category.id as category_id,
    cms_users.login,
    cms_users.nickname,
    cms_user_profiles.imageurl
    FROM cms_events 
    LEFT JOIN cms_events_category ON cms_events.category_id = cms_events_category.id
    INNER JOIN cms_users ON cms_events.author_id = cms_users.id
    INNER JOIN cms_user_profiles ON cms_events.author_id = cms_user_profiles.user_id
    WHERE cms_events.id = {$event_id}";
    
    $result = $this->inDB->query($sql);
    if ($this->inDB->error()) 
    { 
      return false; 
    }
    if (!$this->inDB->num_rows($result)) 
    { 
      return false; 
    }
    
    $event = $this->inDB->fetch_assoc($result);
    //Проверяем на подчинённые события 
    $sql = "SELECT * FROM cms_events WHERE parent_id = {$event_id}";
    $result = $this->inDB->query($sql);
    if($this->inDB->num_rows($result) != 0)
    {
      $event['parent'] = array();
      while ($parent = $this->inDB->fetch_assoc($result))
      {
	$event['parent'][] = $parent;
      }
    }
    return $event;
  }

  function deleteEvent($event_id)
  {
    $inUser = cmsUser::getInstance();
    if($inUser->is_admin)
    {
      $sql = "DELETE FROM cms_events WHERE `id` = '{$event_id}' LIMIT 1";
      $this->inDB->query($sql);
      $sql = "DELETE FROM cms_events WHERE `parent_id` = '{$event_id}' LIMIT 1";
      $this->inDB->query($sql);
    }
    else
    {
      $sql = "DELETE FROM cms_events WHERE `id` = '{$event_id}' AND `author_id` = '".$inUser->id."' LIMIT 1";
      $this->inDB->query($sql);
      $sql = "DELETE FROM cms_events WHERE `parent_id` = '{$event_id}' AND `author_id` = '".$inUser->id."' LIMIT 1";
      $this->inDB->query($sql);
    }
    $sql = "SELECT id FROM cms_actions WHERE name = 'add_event'";
    $result = $this->inDB->query($sql);
    $act = $this->inDB->fetch_assoc($result);
    $sql = "DELETE FROM cms_actions_log WHERE object_id = '$event_id' AND action_id = '".$act['id']."'";
    
    if ($this->inDB->error()) 
    { 
      return false; 
    }
    return true;
  }

  function getCalendar($start_time, $end_time, $category_id,$parent_id)
  {
    if($start_time and $end_time)
    {
      $sql = "SELECT cms_events.*,
      cms_events_category.name as category_name,
      cms_events_category.bg,
      cms_events_category.tx,
      cms_events_category.id as category_id
      FROM cms_events 
      LEFT JOIN cms_events_category ON cms_events.category_id = cms_events_category.id
      WHERE cms_events.parent_id = '{$parent_id}' 
      AND cms_events.start_time > '{$start_time}'
      AND cms_events.end_time < '{$end_time}'";
    }
    else
    {
      $sql = "SELECT cms_events.*,
      cms_events_category.name as category_name,
      cms_events_category.bg,
      cms_events_category.tx,
      cms_events_category.id as category_id
      FROM cms_events 
      LEFT JOIN cms_events_category ON cms_events.category_id = cms_events_category.id";
    }
      
    if($category_id)
    {
      $sql .= " AND category_id = $category_id";
    }    
    
    $sql .= " ORDER BY cms_events.start_time ASC";
    
    $result = $this->inDB->query($sql);
    
    if ($this->inDB->error()) 
    { 
      print mysql_error();
      return false; 
    }
      
    if (!$this->inDB->num_rows($result)) 
    { 
      return false; 
    }
      
    $output = array();
    while ($row = $this->inDB->fetch_assoc($result))
    {
      $row['start_date'] = date("Y-m-d H:i",$row['start_time']);
      $row['end_date'] = date("Y-m-d H:i",$row['end_time']);
      $output[] = $row;
    }
    return $output;
  }

  public function getCommentTarget($target, $target_id)
  {
    $result = array();
    switch($target)
    {
      case 'calendar': 
      $result['link']  = "/calendar/event".$target_id.".html";
      $result['title'] = mysql_result(mysql_query("SELECT title FROM cms_events WHERE id = '".$target_id."'"),0);
      break;
    }
    return ($result ? $result : false);
  }

  public function isSignup($event_id)
  {
    $inUser = cmsUser::getInstance();
    $sql = "SELECT * FROM cms_events_signup WHERE event_id = {$event_id} AND user_id = ".$inUser->id;
    /* RATMIR : ADD */
    $result = $this->inDB->query($sql);
    /* RATMIR : ADD END */
    if ($this->inDB->error()) 
    { 
      return false; 
    }
	
    if($this->inDB->num_rows($result))
    {
      return TRUE;
    }
    else
    {
      return FALSE;
    }
  }

  public function numSignup($event_id)
  {
    $sql = "SELECT * FROM cms_events_signup WHERE event_id = {$event_id}";
    $this->inDB->query($sql);
    if ($this->inDB->error()) 
    { 
      return false; 
    }
    return $this->inDB->num_rows($result);
  }
  

  public function deleteSignup($event_id)
  {
    $inUser = cmsUser::getInstance();
    $sql = "DELETE FROM cms_events_signup WHERE event_id = {$event_id} AND user_id = '".$inUser->id."' LIMIT 1";
    $this->inDB->query($sql);
    return;
  }

  public function addSignup($event_id)
  {
    $inUser = cmsUser::getInstance();
    $sql = "INSERT INTO cms_events_signup (`event_id`, `user_id`, `time`) VALUES ('{$event_id}', '".$inUser->id."', '".time()."')";
    $this->inDB->query($sql);
    return;
  }

  public function getSingupsUsers($event_id)
  {
    $sql = "SELECT cms_events_signup.*,
    cms_users.login,
    cms_users.nickname,
    cms_user_profiles.imageurl
    FROM cms_events_signup
    INNER JOIN cms_users ON cms_events_signup.user_id = cms_users.id
    INNER JOIN cms_user_profiles ON cms_events_signup.user_id = cms_user_profiles.user_id
    WHERE cms_events_signup.event_id = {$event_id}";
    $result = $this->inDB->query($sql);
    if ($this->inDB->error()) { return false; }
    if (!$this->inDB->num_rows($result)) { return false; }

    $messages = array();
    while ($message = $this->inDB->fetch_assoc($result))
    {
      $messages[] = $message;
    }
    return $messages;
  }
  
  public function addCategory($name,$bg,$tx)
  {
    $sql = "INSERT INTO cms_events_category (`name`, `bg`, `tx`) VALUES ('$name', '$bg', '$tx')";
    $this->inDB->query($sql);

    if ($this->inDB->error()) 
    { 
      return false; 
    }
    else
    {
	return $this->inDB->get_last_id('cms_events');
    }    
  }
  
  public function getAllCategories()
  {
    $sql ="SELECT * FROM cms_events_category";
    $result = $this->inDB->query($sql);
    
    if ($this->inDB->error()) 
    { 
      return false; 
    }
      
    if (!$this->inDB->num_rows($result)) 
    { 
      return false; 
    }
      
    $output = array();
    while ($row = $this->inDB->fetch_assoc($result))
    {
      $output[] = $row;
    }
    return $output;  
  }

  public function getCategory($id)
  {
    $sql ="SELECT * FROM cms_events_category WHERE id = $id";
    $result = $this->inDB->query($sql);
    
    if ($this->inDB->error()) 
    { 
      return false; 
    }
      
    if (!$this->inDB->num_rows($result)) 
    { 
      return false; 
    }
    return  $this->inDB->fetch_assoc($result);
  }
  
  public function deleteCategory($id)
  {
    $sql = "DELETE FROM cms_events_category WHERE id = {$id} LIMIT 1";
    $this->inDB->query($sql);
    $result = $this->inDB->query($sql);
    
    if ($this->inDB->error()) 
    { 
      return false; 
    }
    return true;  
  }
  
  public function updateCategory($id,$name,$bg,$tx)
  {
    $sql = "UPDATE cms_events_category SET `name` = '$name' , `bg` = '$bg' , `tx` = '$tx' WHERE `id` = '$id' LIMIT 1";
    $result = $this->inDB->query($sql);
    
    if ($this->inDB->error()) 
    { 
      return false; 
    }
    return true;
  }
  
  public function getUserGroups()
  {
    $sql = "SELECT * FROM cms_user_groups";
    $result = $this->inDB->query($sql);
    
    if ($this->inDB->error()) 
    { 
      return false; 
    }
      
    if (!$this->inDB->num_rows($result)) 
    { 
      return false; 
    }
      
    $output = array();
    while ($row = $this->inDB->fetch_assoc($result))
    {
      $output[] = $row;
    }
    return $output;      
  }
}

?>