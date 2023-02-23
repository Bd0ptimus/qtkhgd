/*=========================================================================================
  File Name: footer.js
  Description: Template footer js.
  ----------------------------------------------------------------------------------------
  Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

//Check to see if the window is top if not then display button
$(document).ready(function(){
    $(window).scroll(function(){
        if ($(this).scrollTop() > 400) {
            $('.scroll-top').fadeIn();
        } else {
            $('.scroll-top').fadeOut();
        }
    });

    //Click event to scroll to top
    $('.scroll-top').click(function(){
        $('html, body').animate({scrollTop : 0},1000);
    });

    $('.import-form').on('click', function(){
        if($('#file_upload')[0].files.length != 0){
          $(this).prop('disabled', true);
          $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Vui lòng đợi trọng giây lát...`);
          $('.form-horizontal').submit();
        }
    });
});