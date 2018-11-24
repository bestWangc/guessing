$(function(){
  $("#AllCheck").click(function() {
    if ($(this).prop("checked") == true) {
      $(".goods-check").prop('checked', true); 
      TotalPrice();
    } else {
      $(".goods-check").prop('checked', false);
      TotalPrice();
    }
    $(".shopCheck").change(); 
  });
});
