<?php
function info_module_mod_calendar_eventview(){

        //
        // �������� ������
        //

        //��������� (�� �����)
        $_module['title']        = '��������� �������';

        //�������� (� �������)
        $_module['name']         = '��������� �������';
        //��������
        $_module['description']  = '���������� ����������� ������� ������������';
        //������ (�������������)
        $_module['link']         = 'mod_calendar_eventview';
        //�������
        $_module['position']     = 'sidebar';
        //�����
        $_module['author']       = 'NeoChapay';
        //������� ������
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
