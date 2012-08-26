<?php
    function info_component_calendar(){
        $_component['title']        = '���������';
        $_component['description']  = '���������';
        $_component['link']         = 'calendar';
        $_component['author']       = '������ �������� (NeoChapay)';
        $_component['internal']     = '0';
        $_component['version']      = '0.4.0';
        $inCore = cmsCore::getInstance();
        $inCore->loadModel('calendar');
        $_component['config'] = cms_model_calendar::getDefaultConfig();
        return $_component;
    }


    function install_component_calendar(){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
        $inConf = cmsConfig::getInstance();
        include(PATH.'/includes/dbimport.inc.php');
        dbRunSQL(PATH.'/components/calendar/install.sql', $inConf->db_prefix);

        if(!cmsActions::getAction('add_event'))
	{
	  cmsActions::registerAction('calendar',
          array(
	    'name'=>'add_event',
            'title'=>'���������� �������',
            'message'=>'��������� %s| %s'
	    )
          );
        }

        if(!cmsActions::getAction('add_signup'))
	{
	  cmsActions::registerAction('calendar',
          array(
	    'name'=>'add_signup',
            'title'=>'������������� � �������',
            'message'=>'����� ������������ � %s|'
            )
          );
        }

        if(!cmsActions::getAction('del_signup'))
	{
	  cmsActions::registerAction('calendar',
          array(
	    'name'=>'del_signup',
            'title'=>'����� �� ������� �� �������',
            'message'=>'�� ����� ������������ � %s|'
            )
          );
        }

        return true;

    }

    function upgrade_component_calendar(){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        include(PATH.'/includes/dbimport.inc.php');
        dbRunSQL(PATH.'/components/calendar/update.sql', $inConf->db_prefix);
        return true;
    }

?>
