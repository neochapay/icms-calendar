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
{else}
  <div class="con_heading">Создать событие</div>
{/if}
<form style="margin-top:15px" action="" method="post" name="addform">
  <div style="background-color:#EBEBEB;padding:10px;width:880px">
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
	      <option value="00" {if $start_hour == 00} selected {/if}>00</option>
	      <option value="01" {if $start_hour == 01} selected {/if}>01</option>
	      <option value="02" {if $start_hour == 02} selected {/if}>02</option>
	      <option value="03" {if $start_hour == 03} selected {/if}>03</option>
	      <option value="04" {if $start_hour == 04} selected {/if}>04</option>
	      <option value="05" {if $start_hour == 05} selected {/if}>05</option>
	      <option value="06" {if $start_hour == 06} selected {/if}>06</option>
	      <option value="07" {if $start_hour == 07} selected {/if}>07</option>
	      <option value="08" {if $start_hour == 08} selected {/if}>08</option>
	      <option value="09" {if $start_hour == 09} selected {/if}>09</option>
	      <option value="10" {if $start_hour == 10} selected {/if}>10</option>
	      <option value="11" {if $start_hour == 11} selected {/if}>11</option>
	      <option value="12" {if $start_hour == 12} selected {/if}>12</option>
	      <option value="13" {if $start_hour == 13} selected {/if}>13</option>
	      <option value="14" {if $start_hour == 14} selected {/if}>14</option>
	      <option value="15" {if $start_hour == 15} selected {/if}>15</option>
	      <option value="16" {if $start_hour == 16} selected {/if}>16</option>
	      <option value="17" {if $start_hour == 17} selected {/if}>17</option>
	      <option value="18" {if $start_hour == 18} selected {/if}>18</option>
	      <option value="19" {if $start_hour == 19} selected {/if}>19</option>
	      <option value="20" {if $start_hour == 20} selected {/if}>20</option>
	      <option value="21" {if $start_hour == 21} selected {/if}>21</option>
	      <option value="22" {if $start_hour == 22} selected {/if}>22</option>
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
	      <option value="00" {if $end_hour == 00} selected {/if}>00</option>
	      <option value="01" {if $end_hour == 01} selected {/if}>01</option>
	      <option value="02" {if $end_hour == 02} selected {/if}>02</option>
	      <option value="03" {if $end_hour == 03} selected {/if}>03</option>
	      <option value="04" {if $end_hour == 04} selected {/if}>04</option>
	      <option value="05" {if $end_hour == 05} selected {/if}>05</option>
	      <option value="06" {if $end_hour == 06} selected {/if}>06</option>
	      <option value="07" {if $end_hour == 07} selected {/if}>07</option>
	      <option value="08" {if $end_hour == 08} selected {/if}>08</option>
	      <option value="09" {if $end_hour == 09} selected {/if}>09</option>
	      <option value="10" {if $end_hour == 10} selected {/if}>10</option>
	      <option value="11" {if $end_hour == 11} selected {/if}>11</option>
	      <option value="12" {if $end_hour == 12} selected {/if}>12</option>
	      <option value="13" {if $end_hour == 13} selected {/if}>13</option>
	      <option value="14" {if $end_hour == 14} selected {/if}>14</option>
	      <option value="15" {if $end_hour == 15} selected {/if}>15</option>
	      <option value="16" {if $end_hour == 16} selected {/if}>16</option>
	      <option value="17" {if $end_hour == 17} selected {/if}>17</option>
	      <option value="18" {if $end_hour == 18} selected {/if}>18</option>
	      <option value="19" {if $end_hour == 19} selected {/if}>19</option>
	      <option value="20" {if $end_hour == 20} selected {/if}>20</option>
	      <option value="21" {if $end_hour == 21} selected {/if}>21</option>
	      <option value="22" {if $end_hour == 22} selected {/if}>22</option>
	      <option value="23" {if $end_hour == 23} selected {/if}>23</option>
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
  <input type="submit" value="Сохранить запись">
</form>
</div>