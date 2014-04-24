$(document).ready(function(){
  $(".have_event").click(function(){
    console.log('CLICK');
    $(".eventlist").hide();
    $(this).find(".eventlist").show();
    console.log(this);
  })
  
  $(".eventlist").mouseleave(function(){
    $(this).hide();
  });
})