dojo.declare('app.desk',[],{
  color_white: 'rgb(255, 255, 255)',
  color_red: 'rgb(255, 0, 0)',
  color_yello: 'rgb(255, 255, 0)',
  color_green: 'rgb(0, 255, 0)',
  save_url: '/ajax/save',
  constructor: function(param){
    this.set_size(param)
    this.set_id(param)
    this.create()
    this.start_timer()
    this.timer_old= 0
  },
  set_size: function(param){
    this.table_row_count= 20;
    this.table_col_count= 20;
    if(param&& typeof param== 'object'){
      if(param.size instanceof Array){
        this.table_row_count= param.size[0]
        this.table_col_count= param.size[1]
      }
    }
  },
  set_id: function(param){
    this.id= 'desk';
    if(param&& typeof param== 'object'){
      if(typeof param.id== 'string'){
        this.id= param.id
      }
    }
  },
  create: function(){
    this.index= []
    var table= $('<table class="tesk_table">')
    $('#'+this.id).append(table)
    for(var row_num=0; row_num<this.table_row_count; row_num++){
      var row= $('<tr>')
      var row_index= []
      this.index.push(row_index)
      table.append(row)
      for(var col_num=0; col_num<this.table_col_count; col_num++){
        var col= $('<td>')
        var item= $('<div class="desk_item">')
        row.append(col)
        col.append(item)
        row_index.push(item)
        item.addr= [row_num,col_num]
        item.css('background-color',this.color_white)
        var this_= this;
        (function(item){
          item.click(function(e){
            this_.item_click(item)
          })
        })(item)
      }
    }
  },
  get_item: function(row,col){
    if(row instanceof Array){
      col= row[1]
      row= row[0]
    }
    return this.index[row][col]
  },
  item_click: function(item){
    var state= this.get_item_state(item,state);
    switch(state){
      case this.color_white:
        this.set_item_state(item,this.color_red);
        break;
      case this.color_red:
        this.set_item_state(item,this.color_yello);
        break;
      case this.color_yello:
        this.set_item_state(item,this.color_green);
        break;
      case this.color_green:
        this.set_item_state(item,this.color_white);
        break;
      default:

    }
    // this.set_item_state(item,state);
  },
  get_item_state: function(item){
    return item.css('background-color')
  },
  set_item_state: function(item,state){
    item.css('background-color',state)
  },
  start_timer: function(){
    this.timer= (new Date).getTime()
  },
  get_timer: function(){
    return (new Date).getTime()- this.timer
  },
  get_name: function(){
    return this.name
  },
  set_name: function(name){
    this.name= name
  },
  save: function(callback){
    var this_= this
    var time_create= this.time_create? this.time_create: this.timer
    var time_last_modify= this.timer+ this.get_timer()
    var data= {
      loaded: this.loaded,
      name: this.name,
      time_create: time_create,
      time_last_modify: time_last_modify,
      state_array: this.get_state_array(),
      timer: this.get_timer()+ (this.timer_old? this.timer_old: 0)
    }
    $.post(this.save_url,{data:JSON.stringify(data)},function(res){
      if(typeof callback== 'function'){
        if(res.res== 1){
          this_.loaded= true
        }
        callback(res)
      }
    })
  },
  load: function(data){
    this.time_create= data.time_create
    this.timer_old= data.timer*1
    this.name= data.name
    this.set_state_array(data.state_array)
    this.loaded= true
  },
  get_state_array: function(){
    var state_array= []
    for(var row_num=0; row_num<this.table_row_count; row_num++){
      var row= []
      state_array.push(row)
      for(var col_num=0; col_num<this.table_col_count; col_num++){
        var item= this.get_item(row_num,col_num)
        var state= this.get_item_state(item)
        row.push(state)
      }
    }
    return state_array
  },
  set_state_array: function(state_array){
    // var state_array= []
    for(var row_num=0; row_num<state_array.length; row_num++){
      for(var col_num=0; col_num<state_array[row_num].length; col_num++){
        var item= this.get_item(row_num,col_num)
        this.set_item_state(item,state_array[row_num][col_num])
      }
    }
  },

  a00000000000000: function(){}
})
