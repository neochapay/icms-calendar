{add_js file="components/calendar/js/jquery-ui.js"}
{add_js file="components/calendar/js/jquery.ui.dialog.js"}
{add_js file="components/calendar/js/fullcalendar.js"}
{add_js file="components/calendar/js/calendar.js"}
{add_css file="components/calendar/css/fullcalendar.css"}
{add_css file="components/calendar/css/calendar.css"}
{add_css file="components/calendar/css/redmond/jquery-ui-1.10.3.custom.css"}
{add_js file="core/js/smiles.js"}

<h1 class="con_heading">Календарь</h1>
<div id='fullcalendar' {if $can_add}class="manage"{/if} data-category-id="{$category}" data-can-add="{if $can_add}1{else}0{/if}" data-parent-id="0"></div>
{if $can_add}
  <div id="configdialog" title="Добавить мероприятие">
    <i>Секундочку...</i>
  </div>
{/if}