<?php
function info_module_mod_calendar_eventview(){

        //
        // Описание модуля
        //

        //Заголовок (на сайте)
        $_module['title']        = 'Ближайшие события';

        //Название (в админке)
        $_module['name']         = 'Ближайшие события';
        //описание
        $_module['description']  = 'Показывает предстоящие события пользователя';
        //ссылка (идентификатор)
        $_module['link']         = 'mod_calendar_eventview';
        //позиция
        $_module['position']     = 'sidebar';
        //автор
        $_module['author']       = 'NeoChapay';
        //текущая версия
        $_module['version']      = '0.3.0';
        return $_module;
    }

    function install_module_mod_calendar_eventview(){
        return true;
    }

    function upgrade_module_mod_calendar_eventview(){
        return true;
    }


?>
