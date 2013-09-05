{add_js file="components/calendar/js/jquery.ui.core.js "}
{add_js file="components/calendar/js/jquery.ui.datepicker.js "}
{add_js file="components/calendar/js/jquery.ui.datepicker-ru.js "}
{add_css file="components/calendar/css/redmond/jquery-ui-1.8.23.custom.css"}

{literal}
<script>
	$(function() {
		$( ".datepicker" ).datepicker( $.datepicker.regional[ "ru" ] );
		});
</script>
{/literal}
{if $edit}
  <div class="con_heading">Редактировать событие</div>
{/if}
<form style="margin-top:15px" action="" method="post" name="addform" id="eventform">
  <div style="padding-top:10px; padding-left:10px; padding-bottom:10px;width:100%;">
  {if $parent == 1}
    Вы создаёте вложеное событие для <b>{$parent_title}</b>. Если вложеное событие выходит за рамки основного, то его время будет автоматически изменено.
  {/if}
  <table border="0" cellspacing="0" cellpadding="4">
	<tr>
	  <td width="180"><strong>Заголовок: </strong></td>
	  <td><input name="title" type="text" id="title" size="40" value="{$title}"/></td>
	</tr>
	{if $parent != 1}
	<tr>
	  <td><strong>Тип события: </strong></td>
	  <td>
	  	  <select name="type" id="ownertype">
		    <option value="public" {if $type == "public"}selected{/if}>Публичное</option>
		    <option value="private" {if $type == "private"}selected{/if}>Личное</option>
		    {if $catigories}
		      {foreach key=id item=category from=$catigories}
			<option value="{$category.id}" {if $type == $category.id}selected{/if}>{$category.name}</option>
		      {/foreach}
		    {/if}
		  </select>
	  </td>
	<tr>
	{/if}
	<tr>
	  <td><strong>Начало:</strong></td>
	  <td>
	    <input id="date_start" name="date_start" class="datepicker" value="{$start_date}" onChange="$('#date_end').val($('#date_start').val())"/>
	    <select name="hour_start">
	      {if $cfg.calendar_minTime == 0}<option value="00" {if $start_hour == 00} selected {/if}>00</option>{/if}
	      {if $cfg.calendar_minTime <= 1}<option value="01" {if $start_hour == 01} selected {/if}>01</option>{/if}
	      {if $cfg.calendar_minTime <= 2}<option value="02" {if $start_hour == 02} selected {/if}>02</option>{/if}
	      {if $cfg.calendar_minTime <= 3}<option value="03" {if $start_hour == 03} selected {/if}>03</option>{/if}
	      {if $cfg.calendar_minTime <= 4}<option value="04" {if $start_hour == 04} selected {/if}>04</option>{/if}
	      {if $cfg.calendar_minTime <= 5}<option value="05" {if $start_hour == 05} selected {/if}>05</option>{/if}
	      {if $cfg.calendar_minTime <= 6}<option value="06" {if $start_hour == 06} selected {/if}>06</option>{/if}
	      {if $cfg.calendar_minTime <= 7}<option value="07" {if $start_hour == 07} selected {/if}>07</option>{/if}
	      {if $cfg.calendar_minTime <= 8}<option value="08" {if $start_hour == 08} selected {/if}>08</option>{/if}
	      {if $cfg.calendar_minTime <= 9}<option value="09" {if $start_hour == 09} selected {/if}>09</option>{/if}
	      {if $cfg.calendar_minTime <= 10}<option value="10" {if $start_hour == 10} selected {/if}>10</option>{/if}
	      {if $cfg.calendar_minTime <= 11}<option value="11" {if $start_hour == 11} selected {/if}>11</option>{/if}
	      {if $cfg.calendar_minTime <= 12}<option value="12" {if $start_hour == 12} selected {/if}>12</option>{/if}
	      {if $cfg.calendar_minTime <= 13}<option value="13" {if $start_hour == 13} selected {/if}>13</option>{/if}
	      {if $cfg.calendar_minTime <= 14}<option value="14" {if $start_hour == 14} selected {/if}>14</option>{/if}
	      {if $cfg.calendar_minTime <= 15}<option value="15" {if $start_hour == 15} selected {/if}>15</option>{/if}
	      {if $cfg.calendar_minTime <= 16}<option value="16" {if $start_hour == 16} selected {/if}>16</option>{/if}
	      {if $cfg.calendar_minTime <= 17}<option value="17" {if $start_hour == 17} selected {/if}>17</option>{/if}
	      {if $cfg.calendar_minTime <= 18}<option value="18" {if $start_hour == 18} selected {/if}>18</option>{/if}
	      {if $cfg.calendar_minTime <= 19}<option value="19" {if $start_hour == 19} selected {/if}>19</option>{/if}
	      {if $cfg.calendar_minTime <= 20}<option value="20" {if $start_hour == 20} selected {/if}>20</option>{/if}
	      {if $cfg.calendar_minTime <= 21}<option value="21" {if $start_hour == 21} selected {/if}>21</option>{/if}
	      {if $cfg.calendar_minTime <= 22}<option value="22" {if $start_hour == 22} selected {/if}>22</option>{/if}
	      <option value="23" {if $start_hour == 23} selected {/if}>23</option>
	    </select>
	    :
	    <select name="min_start">
	      <option value="00" {if $start_min == 00} selected {/if}>00</option>
	      <option value="15" {if $start_min == 15} selected {/if}>15</option>
	      <option value="30" {if $start_min == 30} selected {/if}>30</option>
	      <option value="45" {if $start_min == 45} selected {/if}>45</option>
	    </select>
	</td>
	</tr>
	<tr>
	  <td><strong>Конец:</strong></td>
	  <td>
	    <input id="date_end" name="date_end" class="datepicker"  value="{$end_date}"/>
	    <select name="hour_end">
	      {if $cfg.calendar_maxTime == 24}<option value="00" {if $end_hour == 00} selected {/if}>00</option>{/if}
	      {if $cfg.calendar_maxTime >= 1}<option value="01" {if $end_hour == 01} selected {/if}>01</option>{/if}
	      {if $cfg.calendar_maxTime >= 2}<option value="02" {if $end_hour == 02} selected {/if}>02</option>{/if}
	      {if $cfg.calendar_maxTime >= 3}<option value="03" {if $end_hour == 03} selected {/if}>03</option>{/if}
	      {if $cfg.calendar_maxTime >= 4}<option value="04" {if $end_hour == 04} selected {/if}>04</option>{/if}
	      {if $cfg.calendar_maxTime >= 5}<option value="05" {if $end_hour == 05} selected {/if}>05</option>{/if}
	      {if $cfg.calendar_maxTime >= 6}<option value="06" {if $end_hour == 06} selected {/if}>06</option>{/if}
	      {if $cfg.calendar_maxTime >= 7}<option value="07" {if $end_hour == 07} selected {/if}>07</option>{/if}
	      {if $cfg.calendar_maxTime >= 8}<option value="08" {if $end_hour == 08} selected {/if}>08</option>{/if}
	      {if $cfg.calendar_maxTime >= 9}<option value="09" {if $end_hour == 09} selected {/if}>09</option>{/if}
	      {if $cfg.calendar_maxTime >= 10}<option value="10" {if $end_hour == 10} selected {/if}>10</option>{/if}
	      {if $cfg.calendar_maxTime >= 11}<option value="11" {if $end_hour == 11} selected {/if}>11</option>{/if}
	      {if $cfg.calendar_maxTime >= 12}<option value="12" {if $end_hour == 12} selected {/if}>12</option>{/if}
	      {if $cfg.calendar_maxTime >= 13}<option value="13" {if $end_hour == 13} selected {/if}>13</option>{/if}
	      {if $cfg.calendar_maxTime >= 14}<option value="14" {if $end_hour == 14} selected {/if}>14</option>{/if}
	      {if $cfg.calendar_maxTime >= 15}<option value="15" {if $end_hour == 15} selected {/if}>15</option>{/if}
	      {if $cfg.calendar_maxTime >= 16}<option value="16" {if $end_hour == 16} selected {/if}>16</option>{/if}
	      {if $cfg.calendar_maxTime >= 17}<option value="17" {if $end_hour == 17} selected {/if}>17</option>{/if}
	      {if $cfg.calendar_maxTime >= 18}<option value="18" {if $end_hour == 18} selected {/if}>18</option>{/if}
	      {if $cfg.calendar_maxTime >= 19}<option value="19" {if $end_hour == 19} selected {/if}>19</option>{/if}
	      {if $cfg.calendar_maxTime >= 20}<option value="20" {if $end_hour == 20} selected {/if}>20</option>{/if}
	      {if $cfg.calendar_maxTime >= 21}<option value="21" {if $end_hour == 21} selected {/if}>21</option>{/if}
	      {if $cfg.calendar_maxTime >= 22}<option value="22" {if $end_hour == 22} selected {/if}>22</option>{/if}
	      {if $cfg.calendar_maxTime >= 23}<option value="23" {if $end_hour == 23} selected {/if}>23</option>{/if}
	    </select>
	    :
	    <select name="min_end">
	      <option value="00" {if $end_min == 00} selected {/if}>00</option>
	      <option value="15" {if $end_min == 15} selected {/if}>15</option>
	      <option value="30" {if $end_min == 30} selected {/if}>30</option>
	      <option value="45" {if $end_min == 45} selected {/if}>45</option>
	    </select>
	  </td>
	</tr>
	<tr>
		<td colspan="2">
		    <div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
		    {$smilies}
		    {$autogrow}
		    <textarea class="ajax_autogrowarea" name="content" id="message">{$content}</textarea>
		</td>
	</tr>
  </table>
  <br />
  {if $edit}
    <input type="submit" value="Сохранить запись">
  {/if}
</form>
</div>