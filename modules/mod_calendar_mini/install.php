<?php
function info_module_mod_calendar_mini()
{
  $_module['title']        = 'Календарь событий';
  $_module['name']         = 'Мини календарь на текущий месяц';
  $_module['description']  = 'Показывает события в виде мини календаря';
  $_module['link']         = 'mod_calendar_mini';
  $_module['position']     = 'sidebar';
  $_module['author']       = 'NeoChapay';
  $_module['version']      = '0.4';
  return $_module;
}

function install_module_mod_calendar_mini()
{
  return true;
}

function upgrade_module_mod_calendar_mini()
{
  return true;
}
?>
