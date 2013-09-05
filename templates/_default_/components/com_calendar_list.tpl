{add_css file="components/calendar/css/calendar.css"}
{foreach key=id item=day from=$events}
  <div class="club_entry">
    <div class="data">
      <div class="title">
	<a>{$day.title}</a>
      </div>
      <div class="details">
	{if $day.events}
	  <ul class="calendar">
	    {foreach key=id item=event from=$day.events}
	      <li style="border-left: 3px solid {if $event.bg}{$event.bg}{else}{$cfg.public_bg_color}{/if}">
		<a href="/calendar/event{$event.id}.html">{$event.title}</a>
		<b>{$event.startdate}</b>
	      </li>
	    {/foreach}
	  </ul>
	{else}
	  Мероприятий в этот день не состоится
	{/if}
      </div>
    </div>
  </div>
{/foreach}