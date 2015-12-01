$(document).ready(function(){
    var start = parseInt($('.event_list').data('start'));
    var end = parseInt($('.event_list').data('end'));
        
    $('.fc-header .fc-button').click(function(){
        var range = parseInt($('.event_list').data('end'))-parseInt($('.event_list').data('start'));
        if($(this).hasClass('fc-button-next'))
        {
            //next
            start = start+range;
            end = end+range;
        }
        
        if($(this).hasClass('fc-button-prev'))
        {
            //back
            start = start-range;
            end = end-range;
        }
        $('.event_list').attr('data-start',start);
        $('.event_list').attr('data-end',end);

        $.ajax({
            url:        '/calendar/ajax_format_date',
            data:	"time="+start,
            type:	'post',
            success: function(answer)
            {
                $('#f_start').text(answer);
            }
        })
        
        $.ajax({
            url:        '/calendar/ajax_format_date',
            data:	"time="+end,
            type:	'post',
            success: function(answer)
            {
                $('#f_end').text(answer);
            }
        })
        
        $.ajax({
            url:        '/calendar/ajax_get_event',
            data:	"start="+start+"&end="+end,
            type:	'post',
            success: function(json)
            {
                $('UL.event_list LI').remove();
                events = jQuery.parseJSON(json);
                $.each(events,function(){
                    $('UL.event_list').append('<li class="even" data-event-id="'+this.id+'" style="background-color: '+this.bg+'; color: '+this.tx+';"><a href="/calendar/event'+this.id+'.html" style="color: '+this.tx+';">'+this.f_start_date+' : '+this.title+'</a></li>');
                })
            }
        })
    })
})