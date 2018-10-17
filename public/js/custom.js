
 /* jQuery Pre loader
  -----------------------------------------------*/
$(window).on('load', function(){
    $('#preloader').remove(); // set duration in brackets
});


$(document).ready(function() {
  /* Smoothscroll js
  -----------------------------------------------*/
    $(function() {
        $('.navbar-default a').bind('click', function(event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top - 49
            }, 1000);
            event.preventDefault();
        });
    });

  /* wow
  -------------------------------*/
  new WOW({ mobile: false }).init();

  });

