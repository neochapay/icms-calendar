<link href="/components/calendar/css/calendar.css" rel="stylesheet" type="text/css" />
{if $events}
  {foreach key=id item=event from=$events}
    <div class="even" style="background-color: {$event.bg}; color: {$event.tx};">
      <a href="/calendar/event{$event.id}.html" style="color: {$event.tx};">{$event.title}</a>
    </div>
  {/foreach}
{else}
  ������� �� ����������. <a href="/calendar"> �������� </a>
{/if}