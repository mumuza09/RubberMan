(function ($) {

    $(".Modern-Slider").slick({
    autoplay:true,
    autoplaySpeed:10000,
    speed:600,
    slidesToShow:1,
    slidesToScroll:1,
    pauseOnHover:false,
    dots:true,
    pauseOnDotsHover:true,
    cssEase:'linear',
    // fade:true,
    draggable:false,
    prevArrow:'<button class="PrevArrow"></button>',
    nextArrow:'<button class="NextArrow"></button>',
});

$('.3column-layout .product-slider-wrap').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    speed: 500,
    arrows: false,
    responsive: [
        {
            breakpoint: 990,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});


$('.4column-layout .product-slider-wrap').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    speed: 500,
    arrows: false,
    responsive: [
        {
            breakpoint: 990,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});


$('.banner-box-slider').slick({
    slidesToShow: 2,
    slidesToScroll: 1,
    speed: 500,
    arrows: false,
    responsive: [
        {
            breakpoint: 990,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});

  /*****Load function start*****/
$(window).load(function(){




    $('#instagram-slider').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        speed: 500,
        arrows: false,
        responsive: [
            {
                breakpoint: 990,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    infinite: true,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 320,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
});



$('.product-cat-slider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    speed: 500,
    autoplay: true,
    arrows: false,
});




$('.single-product-slider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    speed: 500,
    autoplay: true,
    arrows: false,
});

$('.testimonial-slider-wrap').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    speed: 500,
    autoplay: true,
    arrows: false,
});

// Menu dropdown on hover
extendNav();
function extendNav() {
  jQuery('.nav-wrapper .dropdown').hover(function() {
    jQuery(this).children('.dropdown-menu').stop(true, true).show().addClass('animated-fast slfadeInDown');
    jQuery(this).toggleClass('open');
  }, function() {
    jQuery(this).children('.dropdown-menu').stop(true, true).hide().removeClass('animated-fast slfadeInDown');
    jQuery(this).toggleClass('open');
  });
}

$('[data-toggle="tooltip"]').tooltip();


 (function() {
  if(jQuery("#slideshow").length != 0) {
    document.documentElement.className = 'js';
    var slideshow = new CircleSlideshow(document.getElementById('slideshow'));
  }
})();

})(jQuery);

