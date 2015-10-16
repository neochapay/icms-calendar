var date = new Date();
var answer;
var cfg;
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
    
$(document).ready(function(){
   
    $.ajax({
        url:    '/calendar/ajax_get_config',
        success: function(json)
        {
            cfg = jQuery.parseJSON(json);
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
		defaultView: cfg.calendar_view,
                firstHour: cfg.calendar_firstHour,
		minTime: cfg.calendar_minTime,
		maxTime: cfg.calendar_maxTime,
		allDayDefault: false,
		firstDay: '1',
		eventSources: [{
                    url: '/calendar/ajax_get_event',
                    type: 'POST',
                    data:{
                        //FIXME - необходимо поправить категорию
                        category: 0
                    }
                }],
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
                                return false;
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
			  return false;
			}
		      }
		    })
                },
                timeFormat: 'H:mm'
            });
        }
    })
    
    
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
})
