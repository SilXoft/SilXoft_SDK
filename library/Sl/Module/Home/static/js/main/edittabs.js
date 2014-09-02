$(function () {

   

 $('a[data-toggle="tab"]').on('shown', function (e) {
  e.target // activated tab
  e.relatedTarget // previous tab
})       
$('.myTab a:first').tab('show');  
});