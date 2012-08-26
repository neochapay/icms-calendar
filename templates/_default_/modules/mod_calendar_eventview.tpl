{add_css file="components/calendar/css/calendar.css"}
{if $has_event}

{foreach key=id item=event from=$events}
<div class="action_entry act_add_wall"><div class="action_title"><a href="/calendar/event{$event.id}.html">{$event.title}</a></div></div>
{/foreach}

{else}
Событий не обнаружено. <a href="/calendar"> Добавить </a>
{/if}