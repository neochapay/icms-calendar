{add_css file="components/calendar/css/calendar.css"}
{add_css file="components/calendar/js/fancybox/jquery.fancybox.css"}
{add_js file="components/calendar/js/fancybox/jquery.fancybox.pack.js"}

{if $parent}
    {add_js file="components/calendar/js/jquery-ui-1.8.23.custom.min.js"}
    {add_css file="components/calendar/css/fullcalendar.css"}
    {add_js file="components/calendar/js/fullcalendar.js"}
    {add_js file="components/calendar/js/calendar.js"}
{/if}

{literal}
<script type="text/javascript">
  window.onload = function ()
  { 
    $("a.inline").fancybox({
        'autoScale'     	: 'true' ,
        'transitionIn'		: 'none',
	'transitionOut'		: 'none',
	'type'			: 'inline',
	'opacity'		: 'true',
	'centerOnScroll'	: 'true',
	'padding'		: 0,
	'scrolling'		: 'no'
    });
  }
 </script>
{/literal}

<h1 class="con_heading">{$event.title}</h1>
<table class="club_full_entry" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" class="left">
      <div class="members_list">
	<div class="title">
	  Организатор:
	</div>
	<div class="list" style="text-align: center;">
	  <a href="/users/{$event.login}">
	    <img src="/images/users/avatars/{$event.imageurl}"><br/>
	    {$event.nickname}
	  </a>
	</div>
      </div>
      {if $event.type == "public" and $status != "Прошедшее событие"}
	{if $issngnup}
	  <div class="signup"><a href="/calendar/signup{$event.id}.html">Отказаться</a></div>
	{else}
	  <div class="signup"><a href="/calendar/signup{$event.id}.html">Присоединиться</a></div>
	{/if}
      {/if}
      {if $singups_user}
	<div class="members_list">
	  <div class="title">Участники встречи:</div>
	  <div class="list singups">
	    {foreach key=id item=user from=$singups_user}
	      <a href="/users/{$user.login}">
		<img src="/images/users/avatars/small/{$user.imageurl}"><br/>
		{$user.nickname}
	      </a>
	    {/foreach}
	  </div>
	</div>
      {/if}
      {if $admin}
	<ul>
	  <li><a href="/calendar/delete{$event.id}.html">удалить</a></li>
	  <li><a href="/calendar/edit{$event.id}.html">редактировать</a></li>
	  <li><a href="/calendar/add_parent{$event.id}.html">Добавить волженое событие</a></li>
	</ul>
      {/if}
    </td>
    <td valign="top">
      <div class="data">
	<div class="details">
	  <span class="date">{$status}</span>
	</div>
	<div class="description">
	  {if $content}
	    {$content}
	    <br /><br />
	  {/if}
	  <ul>
	    <li>Начало: {$start_time}</li>
	    <li>Окончание: {$end_time}</li>
	    {if $event.category_name}
	      <li>Категория: <a href="/calendar/category{$event.category_id}.html">{$event.category_name}</a></li>
	    {else}
	      {if $event.type == "private"}
		<li>Это приватное сообщение и видно только Вам.</li>
	      {else}
		<li>Без категории.</li>
	      {/if}
	    {/if}
	  </ul>
	</div>
      </div>
      	<div class="clubcontent">
	  <div class="album">
	    <div class="title"><a>Фотографии</a></div>
	  </div>
	  <div class="content">
	  <!-- FOTOLIB -->
	    {if $images or $allow_add_foto}
	      <div class="arround" id="fotolib_img">
	    {/if}
	    <!-- Сами фото -->
	    {if $images}
	      <ul class="fotolib">
		{foreach key=id item=image from=$images}
		  <li>
		    <a href="#{$image.name}" rel="group1" class="inline">
		      <img src="/upload/userfiles/L_{$image.name}.jpg">
		    </a>
		    <div style="display:none" class="fbinline">
		      <div id="{$image.name}">
			<img src="/upload/userfiles/S_{$image.name}.jpg" class="mainimage">
			<div id="fancybox-title" class="fancybox-title-over" style="width: 100%; display: block; ">
			{if $image.user_id == $user.id or $admin}
			  <span id="fancybox-title-over">
			    <div align="center">
			      <a href="/calendar/rotate/left/{$image.id}.html"><img src="/components/calendar/images/object-rotate-left.png"></a>
			      <a href="/calendar/imagedelete/{$image.id}.html"><img src="/components/calendar/images/window-close.png"></a>
			      <a href="/calendar/rotate/right/{$image.id}.html"><img src="/components/calendar/images/object-rotate-right.png"></a>
			    </div>
			  </span>
			{/if}
		      </div>
		    </div>
		  </div>
		</li>
	      {/foreach}
	    </ul>
	  {/if}
	  <!-- Форма добавления -->
	  {if $allow_add_foto or $admin}
	    <form action="" method="POST" enctype="multipart/form-data">
	      <legend>Загрузить изображение</legend>
	      <div id="inputs">
		<input type="file" name="file_0"><br />
	      </div>
	      <a onClick="addFile()">[+]</a><br/>
	      <input type="submit" value="Отправить">
	    </form>
	  {/if}
	  {if $images or $allow_add_foto}
	    </div>
	  {/if}
	</div>
	<br />
	{if $parent}
	  <div class="album">
	    <div class="title"><a>Календарь мероприятия</a></div>
	  </div>
	  <div class="content">
	    <div id='fullcalendar' {if $can_add}class="manage"{/if} data-category-id="{$category}" data-can-add="{if $can_add}1{else}0{/if}" data-parent-id="{$event.id}"></div>
	    {if $can_add}
                <div id="configdialog" title="Добавить мероприятие">
                    <i>Секундочку...</i>
                </div>
            {/if}
	  </div>
	{/if}
      </div>
    </td>
  </tr>
</table>

