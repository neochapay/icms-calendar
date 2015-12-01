{add_css file="components/calendar/css/calendar.css"}
{add_css file="components/calendar/css/fullcalendar.css"}

{add_js file="components/calendar/js/calendar_module.js"}

<table class="fc-header" style="width:100%">
    <tbody>
        <tr>
            <td class="fc-header-left">
                <span class="fc-button fc-button-prev fc-state-default fc-corner-left" unselectable="on">
                    <span class="fc-text-arrow">‹</span>
                </span>
                <span class="fc-button fc-button-next fc-state-default fc-corner-right" unselectable="on">
                    <span class="fc-text-arrow">›</span>
                </span>
            </td>
            <td class="fc-header-center">
                <span class="fc-header-title">
                    <h2><span id="f_start">{$f_start}</span> — <span id="f_end">{$f_end}</span></h2>
                </span>
            </td>
            <td class="fc-header-right">
            </td>
        </tr>
    </tbody>
</table>
{if $events}
    <ul class="event_list" data-start="{$start}" data-end="{$end}">
        {foreach key=id item=event from=$events}
            <li class="even" data-event-id="{$event.id}" style="background-color: {$event.bg}; color: {$event.tx};">
                <a href="/calendar/event{$event.id}.html" style="color: {$event.tx};">{$event.f_start_date} : {$event.title}</a>
            </li>
        {/foreach}
    </ul>
{else}
  Событий не обнаружено. <a href="/calendar"> Добавить </a>
{/if}