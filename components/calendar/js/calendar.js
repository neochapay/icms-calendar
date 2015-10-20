var date = new Date();
var answer;
var cfg;
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
var calendar;

$(document).ready(function(){
    
    $.ajax({
        url:    '/calendar/ajax_get_config',
        success: function(json)
        {
            cfg = jQuery.parseJSON(json);
            calendar = $('#fullcalendar').fullCalendar({
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
                selectable: $("#fullcalendar").data("can-add"),
                selectHelper: $("#fullcalendar").data("can-add"),
                select: function(start, end, allDay) {
                        var start_time = $.fullCalendar.formatDate(start, 'dd-MM-yyyy HH:mm:ss');
                        var end_time   = $.fullCalendar.formatDate(end, 'dd-MM-yyyy HH:mm:ss');
                        
                        $.ajax({
                            url:    	'/calendar/ajax_add_form',
                            data: 	"start="+start_time+"&end="+end_time,
                            type:   	'post',
                            success: function(form)
                            {
                            answer = form;
                            if(form != "error")
                            {
                                $("#configdialog").html(form);
                                $("#configdialog").dialog("open");
                            }
                            }
                        });
                        
                        $("#configdialog").dialog({
                            close: function(event, ui) {
                            var title = $("#title").val();
                            var data  = $("#eventform").serialize();
                            var parent_id = $("#fullcalendar").data('parent-id');
                            if (title) 
                            {
                                $.ajax({
                                url:    	'/calendar/ajax_add',
                                data: 	data+"&parent_id="+parent_id,
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
                                    start: answer.start,
                                    end: answer.end,
                                    allDay: answer.allDay,
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
		eventSources: [{
                    url: '/calendar/ajax_get_event',
                    type: 'POST',
                    data:{
                        category: $("#fullcalendar").data("category-id")
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
            
            if($("#fullcalendar").data("can-add") == 1)
            {
                console.log('OK');
            }
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
