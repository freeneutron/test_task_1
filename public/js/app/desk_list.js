$(function(){
  $.post('/ajax/load',{},function(res){
    if(res.length){
      var table= $('<table>')
      $('#desk_list_table').append(table);
      var table_head= create_row([
        'Название',
        'Дата создания',
        'Дата последней модификации',
        'Вемя работы над “рисунком”'
      ],'table_head')
      table.append(table_head)
      for(var i=0; i<res.length; i++){
        var tr= create_row(data_prepare(res[i]),'table_item')
        table.append(tr)
      }
    }
  })
  $('#delete_batton').click(function(){
    var value= get_radio('table_item')
    if(value){
      $.post('/ajax/delete',{name:value},function(res){
        location.href= location.href
      })
    }
  })
  $('#load_batton').click(function(){
    var value= get_radio('table_item')
    if(value){
      location.href= '/draw?name='+value
    }
  })
  $('#close_button').click(function(){
    close_popup('set_name')
  })
})
function get_radio(name){
  var radio= $('input[name="'+ name+ '"]:checked')
  var value= radio.val()
  if(!value){
    show_popup('set_name')
  }
  return value
}
function create_row(data,class_){
  if(!class_)class_= ''
  var tr= $('<tr>')
  var td= $('<td>')
  var select= $('<input type="radio" name='+ class_+ ' value="'+data['name'] +'" class="'+ class_+ '">')
  tr.append(td)
  td.append(select)

  for(var field_name in data){
    if('desk_id'== field_name)continue
    var td= $('<td>')
    var div= $('<div class="'+class_ + '">')
    div.text(data[field_name])
    td.append(div)
    tr.append(td)
  }
  return tr
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
function data_prepare(data){
  var res= {}
  for(var name in data){
    switch(name){
      case'time_create':
        res[name]= (new Date(data[name]*1)).toLocaleString()
        break;
      case'time_last_modify':
        res[name]= (new Date(data[name]*1)).toLocaleString()
        break;
      case'timer':
        res[name]= ''+ Math.round(data[name]/1000)+ ' секунд'
        break;
      default:
        res[name]= data[name]
    }
  }
  return res
}
