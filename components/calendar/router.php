<?php
function routes_calendar()
{
  $routes[] = array(
      '_uri'  => '/^calendar\/view.html$/i',
      'do'    => 'view'
  );
  
  $routes[] = array(
      '_uri'  => '/^calendar\/add.html$/i',
      'do'    => 'add'
  );

  $routes[] = array(
      '_uri'  => '/^calendar\/list.html$/i',
      'do'    => 'list'
  );

  $routes[] = array(
     '_uri'  => '/^calendar\/list([0-9]+).html$/i',
     'do'    => 'list',
     1       => 'page'
  );
  
  $routes[] = array(
      '_uri'  => '/^calendar\/event([0-9]+).html$/i',
      'do'    => 'view_event',
      1       => 'event_id'
  );
  
  $routes[] = array(
      '_uri'  => '/^calendar\/delete([0-9]+).html$/i',
      'do'    => 'delete_event',
      1       => 'event_id'
  );

  $routes[] = array(
      '_uri'  => '/^calendar\/category([0-9]+).html$/i',
      'do'    => 'view',
      1       => 'category_id'
  );  
  
  $routes[] = array(
      '_uri'  => '/^calendar\/edit([0-9]+).html$/i',
      'do'    => 'edit_event',
      1       => 'event_id'
  );

  $routes[] = array(
      '_uri'  => '/^calendar\/signup([0-9]+).html$/i',
      'do'    => 'event_signup',
      1       => 'event_id'
  );

  $routes[] = array(
      '_uri'  => '/^calendar\/add_parent([0-9]+).html$/i',
      'do'    => 'add_parent',
      1       => 'event_id'
  );  
  
  $routes[] = array(
      '_uri'  => '/^calendar\/config.html$/i',
      'do'    => 'config_calendar'
  );
  
  $routes[] = array(
      '_uri'  => '/^calendar\/ajax_add$/i',
      'do'    => 'ajax_add'
  );

  $routes[] = array(
      '_uri'  => '/^calendar\/ajax_add_form$/i',
      'do'    => 'ajax_add_form'
  );
  
  $routes[] = array(
      '_uri'  => '/^calendar\/ajax_edit$/i',
      'do'    => 'ajax_edit'
  );

  $routes[] = array(
      '_uri'  => '/^calendar\/ajax_get_event$/i',
      'do'    => 'ajax_get_event'
  );
  
  $routes[] = array(
      '_uri'  => '/^calendar\/rotate\/([a-zA-Z0-9\-]+)\/([a-zA-Z0-9\-]+).html$/i',
      'do'    => 'imagerotate',
      1	      => 'side',
      2	      => 'image_id'
     );     
  $routes[] = array(
      '_uri'  => '/^calendar\/imagedelete\/([0-9]+).html$/i',
      'do'    => 'imagedelete',
      1	      => 'image_id'
     );

  $routes[] = array(
      '_uri'  => '/^calendar\/calendar.ics$/i',
      'do'    => 'isc_calendar'
  );     
 return $routes;
}
?>