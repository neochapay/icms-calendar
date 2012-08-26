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
			  ��������: function() {
			    $("#dialogtitle").val("");
			    $(this).dialog("close");
			  },
			  ���������: function() {
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
			today:    '�������',
			month:    '�����',
			week:     '������',
			day:      '����'
			},
			monthNamesShort: ['���', '����', '����', '���', '���', '����', '����', '���', '���', '���', '���', '���'],
			monthNames: ['������', '�������', '����', '������', '���', '����', '����', '������', '��������', '�������', '������', '�������'],
			dayNamesShort: ['��', '��', '��', '��', '��', '��', '��'],
			dayNames:['�����������', '�����������', '�������', '�����', '�������', '�������', '�������'],
			allDayText: '���� ����',
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
				$("#configdialog").dialog("open");
				$("#configdialog").dialog({
				  close: function(event, ui) {
				    var start_time = $.fullCalendar.formatDate(start, 'dd-MM-yyyy HH:mm:ss');
				    var end_time   = $.fullCalendar.formatDate(end, 'dd-MM-yyyy HH:mm:ss');
				    var category = $("select#dialogcategory :selected").val();
				    var private = false;
				    if($("select#dialogcategory :selected").text() == "���������")
				    {
				      private = true;
				    }
				    
				    var title = $("input#dialogtitle").val();

				    if (title) 
				    {
				      $.ajax({
				      url:    	'/calendar/ajax_add',
				      data: 	"title="+title+"&start="+start_time+"&end="+end_time+"&allDay="+allDay+"&category="+category+"&private="+private,
				      type:   	'post',
				      success: function(json)
				      {
					var answer = jQuery.parseJSON(json);
					if(!answer)
					{
					  alert('�������� �������� ������'); 
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
<h1 class="con_heading">���������</h1>
{if !$guest}
<a href="/calendar/add.html">�������� �������</a>
{/if}
<div id='fullcalendar'></div>
{if $guest != TRUE}
  <div id="configdialog" title="�������� �����������">
    �������� �����������: <input type="text" name="title" id="dialogtitle" style="width: 380px">
    <br />
    ���������: 
    <select name="category" id="dialogcategory">
      <option value="0">��� ���������</option>
      <option value="0">���������</option>
      {if $catigories}
	{foreach key=id item=category from=$catigories}
	  <option value="{$category.id}">{$category.name}</option>
	{/foreach}
      {/if}
    </select>
  </div>
{/if}