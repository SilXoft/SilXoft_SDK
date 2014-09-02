/* 
 * 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
  //  console.log(printform_type_toview);
    checkinputobject();
    $('#type').change(function(){
        checkinputobject();
    })
});
 
 var
 
 checkinputobject = function(){
     
     var type = $('#type').val();
     //console.log(type);
     $('form.printform div.control-group:hidden').show();
     if (printform_type_toview.hide[type] !=undefined){
         for(key in printform_type_toview.hide[type]){
             $('#'+printform_type_toview.hide[type][key]).parents('div.control-group').hide();
       //      console.log('show result #'+printform_type_toview.hide[type][key]);
         }
     }
     if (printform_type_toview.label !=undefined){
      //   console.log(printform_type_toview.label);
         for(field in printform_type_toview.label){
          //   console.log('field: '+field);
             if (printform_type_toview.label[field][type]!= undefined){
            // 	console.log('!undefined');
                 if (printform_type_toview.label[field].default == undefined){
                 	//console.log('default == undefined');
                     printform_type_toview.label[field].default =  $('label[for="'+field+'"]').html();  
                 }
                // console.log($('label[for="'+field+'"]'));
                 $('label[for="'+field+'"]').html(printform_type_toview.label[field][type]);
             }else {
                 if (printform_type_toview.label[field].default != undefined){
                    $('label[for="'+field+'"]').html(printform_type_toview.label[field].default);     
                 }
             }
         }
     }
     
 }
