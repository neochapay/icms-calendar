{if $events}
  {foreach key=id item=event from=$events}
    <div class="club_entry">
      <div class="data">
	<div class="title">
	  {if $event.category_id}<a href="category{$event.category_id}.html">{$event.category_name}</a> - {/if}<a href="/calendar/event{$event.id}.html" class="public" >{$event.title}</a>
	</div>
	<div class="details">
	  <span>{$event.start_date} - {$event.end_date}</span><br />
	  {$event.content}
	</div>
      </div>
    </div>
  {/foreach}
{else}
  Встречи отсутствуют
{/if}