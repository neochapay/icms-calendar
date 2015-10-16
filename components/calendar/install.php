<?php
    function info_component_calendar(){
        $_component['title']        = 'Календарь';
        $_component['description']  = 'Календарь';
        $_component['link']         = 'calendar';
        $_component['author']       = 'Сергей Игоревич (NeoChapay)';
        $_component['internal']     = '0';
        $_component['version']      = '0.4.2';
        $inCore = cmsCore::getInstance();
        $inCore->loadModel('calendar');
        $_component['config'] = cms_model_calendar::getDefaultConfig();
        return $_component;
    }


    function install_component_calendar(){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
        $inConf = cmsConfig::getInstance();
	$inDB->query("CREATE TABLE `cms_events` (
		      `id` int(11) NOT NULL AUTO_INCREMENT,
		      `author_id` int(11) NOT NULL,
		      `type` varchar(128) NOT NULL,
		      `category_id` int(11) NOT NULL,
		      `start_time` int(11) NOT NULL,
		      `end_time` int(11) NOT NULL,
		      `title` varchar(128) NOT NULL,
		      `content` longtext NOT NULL,
		      `parent_id` int(11) NOT NULL,
		      PRIMARY KEY (`id`)
		      ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	$inDB->query("CREATE TABLE `cms_events_category` (
		      `id` int(11) NOT NULL AUTO_INCREMENT,
		      `name` text NOT NULL,
		      `bg` text NOT NULL,
		      `tx` text NOT NULL,
		      PRIMARY KEY (`id`)
		      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	$inDB->query("CREATE TABLE `cms_events_signup` (
		      `event_id` int(11) NOT NULL,
		      `user_id` int(11) NOT NULL,
		      `time` int(11) NOT NULL
		      ) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COLLATE=utf8");
		      
	$inDB->query("CREATE TABLE IF NOT EXISTS `cms_fotolib` (
		      `id` int(11) NOT NULL AUTO_INCREMENT,
		      `user_id` int(11) NOT NULL,
		      `type` text NOT NULL,
		      `photo_id` int(11) NOT NULL,
		      `name` text NOT NULL,
		      `time` text NOT NULL,
		      PRIMARY KEY (`id`)
		      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
        
        $inDB->query("INSERT INTO cms_comment_targets (target, component, title)
		      VALUES ('calendar', 'calendar', 'Календарь')");

        if(!cmsActions::getAction('add_event'))
	{
	  cmsActions::registerAction('calendar',
          array(
	    'name'=>'add_event',
            'title'=>'Добавление события',
            'message'=>'добавляет %s| %s'
	    )
          );
        }

        if(!cmsActions::getAction('add_signup'))
	{
	  cmsActions::registerAction('calendar',
          array(
	    'name'=>'add_signup',
            'title'=>'Присоединение к встрече',
            'message'=>'будет учавствовать в %s|'
            )
          );
        }

        if(!cmsActions::getAction('del_signup'))
	{
	  cmsActions::registerAction('calendar',
          array(
	    'name'=>'del_signup',
            'title'=>'Отказ от участия во встрече',
            'message'=>'не будет учавствовать в %s|'
            )
          );
        }

        return true;

    }

    function upgrade_component_calendar(){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        return true;
    }

?>
