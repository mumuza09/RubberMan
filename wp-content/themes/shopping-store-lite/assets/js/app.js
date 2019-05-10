(function ($) {

    $('.nav-wrapper').stickMe({
        transitionDuration: 500,
        shadow: true,
        shadowOpacity: 0.6,
    });

    jQuery(document).ready(function() {
        if( jQuery(window).width() < 767) {
            jQuery('.header-top-right .dropdown').click(function() {
                jQuery(this).children('.dropdown-menu').stop(true, true).show().addClass('animated-fast slfadeInDown');
                jQuery(this).toggleClass('open');
            }, function() {
                jQuery(this).children('.dropdown-menu').stop(true, true).hide().removeClass('animated-fast slfadeInDown');
                jQuery(this).toggleClass('open');
            });
        }
        else{
            jQuery('.header-top-right .dropdown').hover(function() {
                jQuery(this).children('.dropdown-menu').stop(true, true).show().addClass('animated-fast slfadeInDown');
                jQuery(this).toggleClass('open');
            }, function() {
                jQuery(this).children('.dropdown-menu').stop(true, true).hide().removeClass('animated-fast slfadeInDown');
                jQuery(this).toggleClass('open');
            });
        }
    });

    //gallery
    $('.post-format-gallery').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: 500,
        autoplay: true,
        arrows: true
    });

    $modal = $('.modal-frame');
    function enterNewConvo() {
        $('.create-chat-input').focus();
    }

    function closeModal() {
        $modal.removeClass('active');
        $modal.addClass('leave');
    }

    $('.modal-popup').click(function (e) {
        e.preventDefault();
        $('.modal-content').html('');
        var getId = $(this).data('id');
        $.ajax({
            url: woocommerce_product.ajaxurl,
            type: 'post',
            data: {
                'action': 'product_action',
                'post_id': getId
            },
            beforeSend: function () {
                $('.modal-frame').addClass('active');
                jQuery('.modal-body .loading').css('display', 'block');
            },
            success: function (response) {
                jQuery('.modal-body .loading').css('display', 'none');
                $('.modal-content').html(response);
            },
        });
        $('.modal-frame').removeClass('leave');
        enterNewConvo();
    });


    $('.modal-overlay').click(function () {
        closeModal();
    });

    $('#close').click(function () {
        closeModal();
    });

    $(document).keyup(function (e) {
        if (e.which === 27) {
            closeModal();
        }
    });

    $('.compare').click(function () {
        checkIframe();
    });
    function checkIframe(){
        if(!$('.cboxIframe').length){
            setTimeout(checkIframe, 3000);
        }
        else{
            // $('.cboxIframe').
            // $('iframe head').append($("<style type='text/css'>.ajax_add_to_cart{background: #e23e57;border: 2px solid #e23e57;color: #fff;border-radius: 4px;text-transform: uppercase;font-family: Poppins;font-size: 16px;padding: 13px 26px;outline: 0;-moz-transition: 0.3s;-o-transition: 0.3s;-webkit-transition: 0.3s;transition: 0.3s;}  </style>"));
            $('iframe').addClas('testclass');
        }
    }

    $(".tab-pane:first").show();

// Menu dropdown on hover
    extendNav();
    function extendNav() {
        jQuery(document).ready(function() {
            if( jQuery(window).width() < 767) {
                jQuery('.nav-wrapper .dropdown').click(function () {
                    jQuery(this).children('.dropdown-menu').stop(true, true).show().addClass('animated-fast slfadeInDown');
                    jQuery(this).toggleClass('open');
                }, function () {
                    jQuery(this).children('.dropdown-menu').stop(true, true).hide().removeClass('animated-fast slfadeInDown');
                    jQuery(this).toggleClass('open');
                });
            }
            else{
                jQuery('.nav-wrapper .dropdown').hover(function () {
                    jQuery(this).children('.dropdown-menu').stop(true, true).show().addClass('animated-fast slfadeInDown');
                    jQuery(this).toggleClass('open');
                }, function () {
                    jQuery(this).children('.dropdown-menu').stop(true, true).hide().removeClass('animated-fast slfadeInDown');
                    jQuery(this).toggleClass('open');
                });
            }
        });
    }

    $('.modal-popup').click(function (e) {
        e.preventDefault();
        $('.modal-content').html('');
        var getId = $(this).data('id');
        $.ajax({
            url: woocommerce_product.ajaxurl,
            type: 'post',
            data: {
                'action': 'product_action',
                'post_id': getId
            },
            beforeSend: function () {
                $('.frontend-ajaxfrontend-ajax').addClass('active');
                jQuery('.modal-body .loading').css('display', 'block');
            },
            success: function (response) {
                jQuery('.modal-body .loading').css('display', 'none');
                $('.modal-content').html(response);
            },
        });
        $('.modal-frame').removeClass('leave');
        enterNewConvo();
    });


    function enterNewConvo() {
        $('.create-chat-input').focus();
    }




    $('.add-to-cart').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: woocommerce_product.ajaxurl,
            type: 'post',
            data: {
                'action': 'mini_cart',
            },
            success: function (response) {
                var countNumber = parseInt(response)+1;
                $('.cart-count').html(countNumber);
                navopen();

            },
        });

    });

    function cartCount(){
        $.ajax({
            url: woocommerce_product.ajaxurl,
            type: 'post',
            data: {
                'action': 'mini_cart',
            },
            success: function (response) {
                $('.cart-count').html(parseInt(response));
            },
        });
    }


    $('.slide-nav,.closebtn').on('click', function (e) {
        e.preventDefault();
        navopen();
    });
    $('.closebtn').on('click', function (e) {
        e.preventDefault();
        cartCount();
    });
//
    $('#main').on('click', function (e) {
        e.preventDefault();
        if ($(e.target).hasClass('nav-open')) {
            navopen();
            cartCount();
        }
    });
    function navopen(){
        $('body').toggleClass('nav-open');
        $('#mySidenav').toggleClass('nav-open');
        $('#main').toggleClass('nav-open');
    }

    if(jQuery("#slideshow").length != 0) {
        document.documentElement.className = 'js';
        var slideshow = new CircleSlideshow(document.getElementById('slideshow'));
    }

})(jQuery);
