$(function(){
  require(['dojo','dojo/date/locale','app/desk'],function(dojo,locale){
    var desk= new app.desk({
      size:[20,20],
      id: 'desk'
    })
    var name= get_name()
    if(name){
      $.post('/ajax/load',{name:name},function(res){
        desk.load(res)
      })
    }
    $('#save_batton').click(function(){
      if(desk.name&& desk.loaded){
        $('#name').val(desk.name)
      }
      $('#submit_message').hide();
      show_popup('set_name');
    })
    $('#submit').click(function(){
      if(desk.name!= $('#name').val()){
        desk.loaded= false
      }
      desk.set_name($('#name').val())
      desk.save(function(res){
        if(res.res== 1){
          close_popup('set_name');
        }else{
          $('#submit_message').show();
        }
      })
    })
    $('#cancel').click(function(){
      close_popup('set_name');
    })
  })
})
function get_name(){
  var name= location.href.match(/name=([^&]+)/)
  if(name)return name[1]
}
function close_popup(name){
  $("#popup_"+ name).dialog('close')
}
function show_popup(name){
  $("#popup_"+ name).dialog({
    _allowInteraction: function(){
      // console.log('_allowInteraction');
    },
    width:515,
    modal: true
  })
  $('.ui-widget-overlay').click(function(){
    close_popup(name)
  })
}
