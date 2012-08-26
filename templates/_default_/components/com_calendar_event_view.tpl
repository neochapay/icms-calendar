{add_css file="components/calendar/css/calendar.css"}
{add_css file="components/calendar/js/fancybox/jquery.fancybox-1.3.4.css"}

{add_js file="components/calendar/js/fancybox/jquery.fancybox-1.3.4.js"}
{add_js file="components/calendar/js/fancybox/jquery.easing-1.3.pack.js"}

{literal}
<script type="text/javascript">
  window.onload = function ()
  { 
    $("a.inline").fancybox({
        'autoScale'     	: 'true' ,
        'transitionIn'		: 'none',
	'transitionOut'		: 'none',
	'type'			: 'inline',
	'opacity'		: 'true',
	'centerOnScroll'	: 'true',
	'padding'		: 0,
	'scrolling'		: 'no'
    });
  }
 </script>
{/literal}

<h1 class="con_heading">{$event.title}</h1>
<div class="action_entry">
  <div class="action_date">{$status}</div>
  <div class="action_title">
    {$content}
    <ul>
      <li>Начало: {$start_time}</li>
      <li>Окончание: {$end_time}</li>
      {if $event.category_name}
	<li>Категория: {$event.category_name}</li>
      {else}
	{if $event.type == "private"}
	  <li>Это приватное сообщение и видно только Вам.</li>
	{else}
	  <li>Без категории.</li>
	{/if}
      {/if}
    </ul>
  {if $singups_user}
    <h3>Участники встречи</h3>
    <ul>
    {foreach key=id item=user from=$singups_user}
      <li><a href="/users/{$user.login}">{$user.nickname}</a></li>
    {/foreach}
    </ul>
  {/if}

  </div>
  {if $admin}
    <a href="/calendar/delete{$event.id}.html">удалить</a> | <a href="/calendar/edit{$event.id}.html">редактировать</a> | <a href="/calendar/add_parent{$event.id}.html">Добавить волженое событие</a>
  {/if}
  {if $event.type == "public" and $status != "Прошедшее событие"}
    {if $issngnup}
	<div class="signup"><a href="/calendar/signup{$event.id}.html">Отказаться от участия</a></div>
    {else}
	<div class="signup"><a href="/calendar/signup{$event.id}.html">Присоединиться</a></a>
    {/if}
  {/if}
</div>  
 <!-- FOTOLIB -->
{if $images or $allow_add_foto}
<div class="arround" id="fotolib_img">
  <h1>Галерея:</h1>
{/if}
<!-- Сами фото -->
{if $images}
  <ul>
  {foreach key=id item=image from=$images}
    <li>
      <a href="#{$image.name}" rel="group1" class="inline">
	<img src="/images/fotolib/L_{$image.name}.jpg">
      </a>
      <div style="display:none" class="fbinline">
	<div id="{$image.name}">
	  <img src="/images/fotolib/S_{$image.name}.jpg" class="mainimage">
	  <div id="fancybox-title" class="fancybox-title-over" style="width: 100%; display: block; ">
	    {if $image.user_id == $user.id or $is_author == "1"}
	    <span id="fancybox-title-over">
	      <div align="center">
		<a href="/usermaps/rotate/left/{$image.id}.html"><img src="/components/usermaps/images/object-rotate-left.png"></a>
		<a href="/usermaps/imagedelete/{$image.id}.html"><img src="/components/usermaps/images/window-close.png"></a>
		<a href="/usermaps/rotate/right/{$image.id}.html"><img src="/components/usermaps/images/object-rotate-right.png"></a>
	      </div>
	    </span>
	    {/if}
	  </div>
	</div>
      </div>
    </li>
  {/foreach}  
  </ul>
{/if}
<!-- Форма добавления -->
{if $allow_add_foto}
    <form action="" method="POST" enctype="multipart/form-data">
      <fieldset>
	<legend>Загрузить изображение</legend>
	<div id="inputs">
	  <input type="file" name="file_0"><br />
	</div>
	<a onClick="addFile()">[+]</a><br/>
	<input type="submit" value="Отправить">
    </form>
{/if}
{if $images or $allow_add_foto}
</div>
{/if}
{if $parent}
  <h1>Календарь мероприятия:</h1>
  {add_js file="components/calendar/js/jquery-ui-1.8.23.custom.min.js"}
  {add_css file="components/calendar/css/fullcalendar.css"}
  {add_js file="components/calendar/js/fullcalendar.js"}
  <script type='text/javascript'>
  {literal}
    $(document).ready(function() {
		
		$('#calendar').fullCalendar({
			header: {
			  left: 'prev,next today',
			  center: 'title',
			  right: 'month,agendaWeek,agendaDay'
			},
			editable: false,
						buttonText: {
			today:    'сегодня',
			month:    'месяц',
			week:     'неделя',
			day:      'день'
			},
			year: {/literal}{$year}{literal},
			month: {/literal}{$month}{literal},
			date: {/literal}{$day}{literal},
			monthNamesShort: ['Янв', 'Февр', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
			monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
			dayNamesShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
			dayNames:['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
			allDayText: 'весь день',
			axisFormat: 'H:mm',
			defaultView: '{/literal}{$calendar_view}{literal}',
			firstDay: '1',
			timeFormat: 'H:mm',
			events:[
			  {/literal}{$events_string}{literal}
			]
		      });
		   });
  {/literal}
  </script>
  <div id="calendar"></div>
{/if}
