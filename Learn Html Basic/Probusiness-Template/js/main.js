(function($){
    $(document).ready(function(){


        (function() {
            // Fixed Navigation Bar
           // $('#menu-bar').scrollToFixed();

           // Moving Logo from Logo-Bar to Navbar-header on Tab size of 768px or Minimum
           $(window).on("load resize orientationchange",function(e){
                if($(window).width() < 768){
                    $("#logo").detach().appendTo($(".navbar-static-top"));
                }
                else{
                    $("#logo").detach().appendTo('#menu-bar .container .col-sm-3')
                }

               // Dynamic Header Height Set in Home Page
              $(".home #header").height($("#header-top").height() +  $(".slider_block").height());

            });



         })();

        var abc = $('.vertical-tab  .nav-tabs').width();
        $('.vertical-tab  .tab-content').css("margin-left", abc-1);

        /*----------------------------------------------------*/
        /*	Sticky Header
         /*----------------------------------------------------*/
        if(jQuery.isFunction(jQuery.fn.sticky)){
            $(window).load(function(){
                $("#menu-bar").sticky({ topSpacing: 0 });
            });
        }
        /*----------------------------------------------------*/
        /*	Same Height Div's
         /*----------------------------------------------------*/
        if(jQuery.isFunction(jQuery.fn.matchHeight)){
            $('.same-height').matchHeight();
        }

        /*--------------------------------------------------
                      Search-Icon
        * ----------------------------------------------------*/
        $(function(){
            $(".search-label .search-button").on("click", function(e){
                e.preventDefault();
                $("html").addClass("search-exp");
                $(".search-input").focus();
            });
            $(".search-input").blur(function(){
                // Do not hide input if contains text
                if($(".search-input").val() === ""){
                    $("html").removeClass("search-exp");
                }
            });
        });

        /*----------------------------------------------------*/
        /*	FlexSlider
         /*----------------------------------------------------*/
        if(jQuery.isFunction(jQuery.fn.fractionSlider)){
            $('.flexslider.top_slider').flexslider({
                animation: "fade",
                controlNav: false,
                directionNav: true,
                prevText: "&larr;",
                nextText: "&rarr;"
            });
        }

        /*----------------------------------------------------*/
        /*	EasyPie Chart Effects
         /*----------------------------------------------------*/
        if(jQuery.isFunction(jQuery.fn.easyPieChart)){

            // Big EasyPie Chart Effects;
            $('.circular-chart-big').easyPieChart({
                animate : 2000,
                barColor: "#727CB6",
                trackColor : '#f9f9f9',
                scaleColor: false,
                lineWidth: 11,
                size: 180,
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent)+'%');
                }
            });

            // Small EasyPie Chart Effects;
            $('.circular-chart-small').easyPieChart({
                size : 120,
                animate : 2000,
                lineWidth : 7,
                lineCap : 'square',
                barColor : '#727CB6',
                trackColor : '#f9f9f9',
                scaleColor : false,
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent)+'%');
                }
            });
        }

        /*----------------------------------------------------*/
        /*	CountTo
         /*----------------------------------------------------*/
        if(jQuery.isFunction(jQuery.fn.countTo)){
            $('.timer').countTo();
        }

        /*----------------------------------------------------*/
        /*	Owl Carousel
         /*----------------------------------------------------*/
        if(jQuery.isFunction(jQuery.fn.owlCarousel)){

            // Recent Work Slider
            $("#recent-work-slider").owlCarousel({
                navigation : true,
                pagination : false,
                items : 4,
                itemsDesktop:[1199,4],
                itemsTablet : [768, 3],
                itemsDesktopSmall : [992, 3],
                itemsMobile : [480,1],
                navigationText : ["",""]
            });

            // Post News Slider
            $("#post-slider").owlCarousel({
                navigation : true,
                pagination : false,
                items : 4,
                itemsDesktop:[1199,3],
                itemsDesktopSmall:[980,2],
                itemsMobile : [479,1],
                navigationText : ["",""]
            });
        }

        $("body").tooltip({
            selector: '[data-toggle="tooltip"]'
        });


        //  ============================
        //  = Scroll event function =
        //  ===========================
        var goScrolling = function(elem) {
            var docViewTop = $(window).scrollTop();
            var docViewBottom = docViewTop + $(window).height();
            var elemTop = elem.offset().top;
            var elemBottom = elemTop + elem.height();
            return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
        };


        //  =======================
        //  = Progress bars =
        //  =======================
        $('.progress_skill .bar').data('width', $(this).width()).css({
            width : 0,
            height:0
        });
        $(window).scroll(function() {
            $('.progress_skill .bar').each(function() {
                if (goScrolling($(this))) {
                    $(this).css({
                        width : $(this).attr('data-value') + '%',
                        height : $(this).attr('data-height') + '%'
                    });
                }
            });
        });


        //  ===================
        //  = Flickr Gallery =
        //  ===================
        $('#flickrFeed').jflickrfeed({
            limit: 9,
            qstrings: {
                //id: '124787947@N07' our id //
                id: '124787947@N07'
            },
            itemTemplate: '<li><a class="mfp-gallery" title="{{title}}" href="{{image_b}}"><i class="fa fa-search"></i><div class="hover"></div></a><img src="{{image_s}}" alt="{{title}}" /></li>'
        });

        /*===========================================================*/
        /*	Isotope Posrtfolio
         /*===========================================================*/
        if(jQuery.isFunction(jQuery.fn.isotope)){
            jQuery('.portfolio_list').isotope({
                itemSelector : '.list_item',
                layoutMode : 'fitRows',
                animationEngine : 'jquery'
            });

            /* ---- Filtering ----- */
            jQuery('#filter li').click(function(){
                var $this = jQuery(this);
                if ( $this.hasClass('selected') ) {
                    return false;
                } else {
                    jQuery('#filter .selected').removeClass('selected');
                    var selector = $this.attr('data-filter');
                    $this.parent().next().isotope({ filter: selector });
                    $this.addClass('selected');
                    return false;
                }
            });
        }


        /*----------------------------------------------------*/
        /*	Magnific Popup
         /*----------------------------------------------------*/
        $('body').magnificPopup({
            type: 'image',
            delegate: 'a.mfp-gallery',
            fixedContentPos: true,
            fixedBgPos: true,
            overflowY: 'auto',
            closeBtnInside: true,
            preloader: true,
            removalDelay: 0,
            mainClass: 'mfp-fade',
            gallery:{enabled:true},
            callbacks: {
                buildControls: function() {
                    console.log('inside'); this.contentContainer.append(this.arrowLeft.add(this.arrowRight));
                }
            }
        });

        $('.mfp-image').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            mainClass: 'mfp-fade',
            image: {
                verticalFit: true
            }
        });

        $('.mfp-youtube, .mfp-vimeo, .mfp-gmaps').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 0,
            preloader: false,
            fixedContentPos: false
        });

        /*----------------------------------------------------*/
        /*	Swipe Slider
         /*----------------------------------------------------*/
        window.mySwipe = new Swipe(document.getElementById('slider'), {
            startSlide: 2,
            speed: 400,
            auto: 3000,
            continuous: true,
            disableScroll: false,
            stopPropagation: false,
            callback: function(index, elem) {},
            transitionEnd: function(index, elem) {}
        });

        /*----------------------------------------------------*/
        /*	Accordians
         /*----------------------------------------------------*/
        $('.accordion').on('shown.bs.collapse', function (e) {
            $(e.target).parent().addClass('active_acc');
            $(e.target).prev().find('.switch').removeClass('fa-plus');
            $(e.target).prev().find('.switch').addClass('fa-minus');
        });
        $('.accordion').on('hidden.bs.collapse', function (e) {
            $(e.target).parent().removeClass('active_acc');
            $(e.target).prev().find('.switch').addClass('fa-plus');
            $(e.target).prev().find('.switch').removeClass('fa-minus');
        });


        /*----------------------------------------------------*/
        /*	Toggles
         /*----------------------------------------------------*/
        $('.toggle').on('shown.bs.collapse', function (e) {
            $(e.target).parent().addClass('active_acc');
            $(e.target).prev().find('.switch').removeClass('fa-plus');
            $(e.target).prev().find('.switch').addClass('fa-minus');
        });
        $('.toggle').on('hidden.bs.collapse', function (e) {
            $(e.target).parent().removeClass('active_acc');
            $(e.target).prev().find('.switch').addClass('fa-plus');
            $(e.target).prev().find('.switch').removeClass('fa-minus');
        });

        /* ------------------ End Document ------------------ */
    });
})(this.jQuery);

$(document).ready(function() {

    /*=================
     *	Contact Form
     * #contact
     ===================*/

    try{
        jQuery('#contact').validate({
            submitHandler: function(form) {
                jQuery('#contact .message').hide();
                var ajaxurl = 'contact.php';
                var data = {
                    action: 'contact_us',
                    datas: jQuery(form).serialize()
                };

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    success: function(response){
                        jQuery('#contact .message').text(response.error).css({'display' : 'inline-block'});
                    },
                    dataType: 'json'
                });
                return false;
            },
            rules: {
                c_name: {
                    required: true,
                    minlength: 3
                },
                c_mail: {
                    required: true,
                    email: true
                },
                c_subject: {
                    required: true,
                    minlength: 6
                },
                c_message:{
                    required: true,
                    minlength: 20
                }
            }
        });
    }catch(e){

    }


    /*============
     BUTTON UP
     * ===========*/
    var btnUp = $('<div/>', {'class':'btntoTop'});
    btnUp.appendTo('body');
    $(document)
        .on('click', '.btntoTop', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 700);
        });

    $(window)
        .on('scroll', function() {
            if ($(this).scrollTop() > 200)
                $('.btntoTop').addClass('active');
            else
                $('.btntoTop').removeClass('active');
        });
});


/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch, iPad, and Android mobile phones
 * Common usage: wipe images (left and right to show the previous or next image)
 *
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 */
(function($){$.fn.touchwipe=function(settings){var config={min_move_x:20,min_move_y:20,wipeLeft:function(){},wipeRight:function(){},wipeUp:function(){},wipeDown:function(){},preventDefaultEvents:true};if(settings)$.extend(config,settings);this.each(function(){var startX;var startY;var isMoving=false;function cancelTouch(){this.removeEventListener('touchmove',onTouchMove);startX=null;isMoving=false}function onTouchMove(e){if(config.preventDefaultEvents){e.preventDefault()}if(isMoving){var x=e.touches[0].pageX;var y=e.touches[0].pageY;var dx=startX-x;var dy=startY-y;if(Math.abs(dx)>=config.min_move_x){cancelTouch();if(dx>0){config.wipeLeft()}else{config.wipeRight()}}else if(Math.abs(dy)>=config.min_move_y){cancelTouch();if(dy>0){config.wipeDown()}else{config.wipeUp()}}}}function onTouchStart(e){if(e.touches.length==1){startX=e.touches[0].pageX;startY=e.touches[0].pageY;isMoving=true;this.addEventListener('touchmove',onTouchMove,false)}}if('ontouchstart'in document.documentElement){this.addEventListener('touchstart',onTouchStart,false)}});return this}})(jQuery);



