{add_js file="components/calendar/js/jquery-ui-1.8.23.custom.min.js"}
{add_js file="components/calendar/js/jquery.ui.dialog.js"}
{add_js file="components/calendar/js/fullcalendar.js"}
{add_css file="components/calendar/css/fullcalendar.css"}
{add_css file="components/calendar/css/redmond/jquery-ui-1.8.23.custom.css"}

{literal}
<script type="text/javascript">
$(document).ready(function() {
	
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$("#configdialog").dialog({
			width: 600,
			modal: true,
			autoOpen: false,
			buttons: {
			  Отменить: function() {
			    $("#dialogtitle").val("");
			    $(this).dialog("close");
			  },
			  Сохранить: function() {
			    $(this).dialog("close");
			  }			  
			}
		});
		
		var calendar = $('#fullcalendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			buttonText: {
			today:    'сегодня',
			month:    'месяц',
			week:     'неделя',
			day:      'день'
			},
			monthNamesShort: ['Янв', 'Февр', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
			monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
			dayNamesShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
			dayNames:['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
			allDayText: 'весь день',
			axisFormat: 'H:mm',
			defaultView: '{/literal}{$cfg.calendar_view}{literal}',
			allDayDefault: false,
			firstDay: '1',
{/literal}
{if $guest != TRUE}
{literal}
			selectable: true,
			selectHelper: true,
			select: function(start, end, allDay) {
				var start_time = $.fullCalendar.formatDate(start, 'dd-MM-yyyy HH:mm:ss');
				var end_time   = $.fullCalendar.formatDate(end, 'dd-MM-yyyy HH:mm:ss');
				
				$.ajax({
				  url:    	'/calendar/ajax_add_form',
				  data: 	"start="+start_time+"&end="+end_time,
				  type:   	'post',
				  success: function(form)
				  {
				    $("#configdialog").html(form);
				  }
				});
				
				$("#configdialog").dialog("open");
				$("#configdialog").dialog({
				  close: function(event, ui) {
				    var category = $("select#dialogcategory :selected").val();
				    var private = false;
				    if($("select#dialogcategory :selected").text() == "Приватное")
				    {
				      private = true;
				    }
				    
				    var title = $("#title").val();
				    var data  = $("#eventform").serialize();
				    if (title) 
				    {
				      $.ajax({
				      url:    	'/calendar/ajax_add',
				      data: 	data,
				      type:   	'post',
				      success: function(json)
				      {
					var answer = jQuery.parseJSON(json);
					if(!answer)
					{
					  alert('Получены неверные данные'); 
					}
					var id = answer.event_id;
					if(answer.error)
					{
					  alert(answer.errortext);
					}
					else
					{
					  calendar.fullCalendar('renderEvent',
					  {
					    id: id,
					    title: title,
					    start: start,
					    end: end,
					    allDay: allDay,
					    editable: true,
					    color: answer.bg,
					    textColor: answer.tx,
					    url   : '/calendar/event'+id+'.html'
					  },
					  true // make the event "stick"
					  );
					}
				      }
				    })
				  };
				  $("#configdialog").html("<i>Минуточку...</i>");
				}
			      })
			    calendar.fullCalendar('unselect');
			},
{/literal}
{/if}
{literal}
			eventSources: [
			{
			  url: '/calendar/ajax_get_event',
			  type: 'POST'
			}
			],
		  eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
		    $.ajax({
		      url:	'/calendar/ajax_edit',
		      data:	"type=resize&id="+event.id+"&dayDelta="+dayDelta+"&minuteDelta="+minuteDelta,
		      type:	'post',
		      success: function(answer)
		      {
			if(answer != "")
			{
			  alert(answer);
			}
		      }
		    })
		  },
		  
		  eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
		    $.ajax({
		      url:	'/calendar/ajax_edit',
		      data:	"type=drop&id="+event.id+"&dayDelta="+dayDelta+"&minuteDelta="+minuteDelta,
		      type:	'post',
		      success: function(answer)
		      {
			if(answer != "")
			{
			  alert(answer);
			}
		      }
		    })
		  },
		 timeFormat: 'H:mm'
		});
		
	});
</script>
{/literal}
<h1 class="con_heading">Календарь</h1>
{if !$guest}
  <style>
    {literal}
    #fullcalendar{
      cursor: url('/components/calendar/images/list-add.png'),crosshair;
    }
    {/literal}
  </style>
{/if}
<div id='fullcalendar'></div>
{if $guest != TRUE}
  <div id="configdialog" title="Добавить мероприятие">
    <i>Секундочку...</i>
  </div>
{/if}