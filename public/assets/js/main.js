(function($) {
    "use strict";
    $(document).ready(function() {
        $('#mobile-menu').meanmenu({
            meanMenuContainer: '.mobile-menu',
            meanScreenWidth: "1199",
            meanExpand: ['<i class="far fa-plus"></i>'],
        });
        $(".offcanvas__close,.offcanvas__overlay").on("click", function() {
            $(".offcanvas__info").removeClass("info-open");
            $(".offcanvas__overlay").removeClass("overlay-open")
        });
        $(".sidebar__toggle").on("click", function() {
            $(".offcanvas__info").addClass("info-open");
            $(".offcanvas__overlay").addClass("overlay-open")
        });
        $(".body-overlay").on("click", function() {
            $(".offcanvas__area").removeClass("offcanvas-opened");
            $(".df-search-area").removeClass("opened");
            $(".body-overlay").removeClass("opened")
        });
        const sliderActive2 = ".hero-slider-3";
        const sliderInit2 = new Swiper(sliderActive2,{
            loop: !0,
            slidesPerView: 1,
            effect: "fade",
            speed: 3000,
            autoplay: {
                delay: 3000,
                disableOnInteraction: !1,
            },
            navigation: {
                prevEl: ".array-prevs",
                nextEl: ".array-nexts",
            },
            pagination: {
                el: ".dot",
                clickable: !0,
            },
        });
        function animated_swiper(selector, init) {
            const animated = function animated() {
                $(selector + " [data-animation]").each(function() {
                    let anim = $(this).data("animation");
                    let delay = $(this).data("delay");
                    let duration = $(this).data("duration");
                    $(this).removeClass("anim" + anim).addClass(anim + " animated").css({
                        webkitAnimationDelay: delay,
                        animationDelay: delay,
                        webkitAnimationDuration: duration,
                        animationDuration: duration,
                    }).one("animationend", function() {
                        $(this).removeClass(anim + " animated")
                    })
                })
            };
            animated();
            init.on("slideChange", function() {
                $(sliderActive2 + " [data-animation]").removeClass("animated")
            });
            init.on("slideChange", animated)
        }
        animated_swiper(sliderActive2, sliderInit2);
        $(window).on("scroll", function() {
            if ($(this).scrollTop() > 250) {
                $("#header-sticky").addClass("sticky")
            } else {
                $("#header-sticky").removeClass("sticky")
            }
        });
        if ($.fn.counterUp && $('.counter-number').length) {
            try {
                $('.counter-number').counterUp({
                    delay: 10,
                    time: 1000,
                })
            } catch (error) {
                console.error("Error initializing counterUp:", error)
            }
        }
        new WOW().init();
        if ($("[data-bg-src]").length > 0) {
            $("[data-bg-src]").each(function() {
                var src = $(this).attr("data-bg-src");
                $(this).css("background-image", "url(" + src + ")");
                $(this).removeAttr("data-bg-src").addClass("background-image")
            })
        }
        if ($('[data-mask-src]').length > 0) {
            $('[data-mask-src]').each(function() {
                var mask = $(this).attr('data-mask-src');
                $(this).css({
                    'mask-image': 'url(' + mask + ')',
                    '-webkit-mask-image': 'url(' + mask + ')'
                });
                $(this).addClass('bg-mask');
                $(this).removeAttr('data-mask-src')
            })
        }
        ;const caseStudiesItems = document.querySelectorAll(".case-studies-items");
        function followImageCursor(event, caseStudiesItems) {
            const contentBox = caseStudiesItems.getBoundingClientRect();
            const dx = event.clientX - contentBox.x;
            const dy = event.clientY - contentBox.y;
            caseStudiesItems.children[2].style.transform = `translate(${dx}px, ${dy}px) rotate(-12.317deg)`
        }
        caseStudiesItems.forEach( (item, i) => {
            item.addEventListener("mousemove", (event) => {
                setInterval(followImageCursor(event, item), 1000)
            }
            )
        }
        );
        function applyAnimationProperties() {
            $('[data-ani]').each(function() {
                var animationClass = $(this).data('ani');
                $(this).addClass(animationClass)
            });
            $('[data-ani-delay]').each(function() {
                var delay = $(this).data('ani-delay');
                $(this).css('animation-delay', delay)
            })
        }
        applyAnimationProperties();
        function initializeSwiper(sliderContainer) {
            var sliderOptions = sliderContainer.data('slider-options');
            console.log("Slider options: ", sliderOptions);
            var previousArrow = sliderContainer.find('.slider-prev');
            var nextArrow = sliderContainer.find('.slider-next');
            var paginationElement = sliderContainer.find('.slider-pagination');
            var numberedPagination = sliderContainer.find('.slider-pagination.pagi-number');
            var paginationStyle = sliderOptions.paginationType || 'bullets';
            var autoplaySettings = sliderOptions.autoplay || {
                delay: 6000,
                disableOnInteraction: !1
            };
            var defaultSwiperConfig = {
                slidesPerView: 1,
                spaceBetween: sliderOptions.spaceBetween || 24,
                loop: sliderOptions.loop !== !1,
                speed: sliderOptions.speed || 1000,
                initialSlide: sliderOptions.initialSlide || 0,
                centeredSlides: !!sliderOptions.centeredSlides,
                effect: sliderOptions.effect || 'slide',
                fadeEffect: {
                    crossFade: !0
                },
                autoplay: autoplaySettings,
                navigation: {
                    nextEl: nextArrow.length ? nextArrow.get(0) : null,
                    prevEl: previousArrow.length ? previousArrow.get(0) : null,
                },
                pagination: {
                    el: paginationElement.length ? paginationElement.get(0) : null,
                    type: paginationStyle,
                    clickable: !0,
                    renderBullet: function(index, className) {
                        var bulletNumber = index + 1;
                        var formattedNumber = bulletNumber < 10 ? '0' + bulletNumber : bulletNumber;
                        if (numberedPagination.length) {
                            return '<span class="' + className + ' number">' + formattedNumber + '</span>'
                        } else {
                            return '<span class="' + className + '" aria-label="Go to Slide ' + formattedNumber + '"></span>'
                        }
                    },
                },
                on: {
                    slideChange: function() {
                        setTimeout(function() {
                            this.params.mousewheel.releaseOnEdges = !1
                        }
                        .bind(this), 500)
                    },
                    reachEnd: function() {
                        setTimeout(function() {
                            this.params.mousewheel.releaseOnEdges = !0
                        }
                        .bind(this), 750)
                    }
                }
            };
            var finalConfig = $.extend({}, defaultSwiperConfig, sliderOptions);
            console.log("Complete Swiper options: ", finalConfig);
            return new Swiper(sliderContainer.get(0),finalConfig)
        }


        var swiperInstances = [];
        $('.gt-slider').each(function() {
            var sliderContainer = $(this);
            var swiperInstance = initializeSwiper(sliderContainer);
            swiperInstances.push(swiperInstance)
        });


        var swiperInstances = [];
        $('.hero-service-slider').each(function() {
            var sliderContainer = $(this);
            var swiperInstance = initializeSwiper(sliderContainer);
            swiperInstances.push(swiperInstance)
        });


        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var targetTab = $(e.target).attr('href');
            $(targetTab).find('.et-slider').each(function() {
                var sliderContainer = $(this);
                if (!sliderContainer[0].swiper) {
                    initializeSwiper(sliderContainer)
                } else {
                    sliderContainer[0].swiper.update()
                }
            })
        });
        $('[data-slider-prev], [data-slider-next]').on('click', function() {
            var targetSliderSelector = $(this).data('slider-prev') || $(this).data('slider-next');
            var targetSlider = $(targetSliderSelector);
            if (targetSlider.length) {
                var swiper = targetSlider[0].swiper;
                if (swiper) {
                    if ($(this).data('slider-prev')) {
                        swiper.slidePrev()
                    } else {
                        swiper.slideNext()
                    }
                }
            }
        });
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 20) {
                $("#back-top").addClass("show")
            } else {
                $("#back-top").removeClass("show")
            }
        });
        $(document).on('click', '#back-top', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
            return !1
        });
        $(".popup-video").magnificPopup({
            type: "iframe",
            removalDelay: 260,
            mainClass: 'mfp-zoom-in',
        });
        $(".img-popup").magnificPopup({
            type: "image",
            gallery: {
                enabled: !0,
            },
        });
        if ($('.blog-slider').length > 0) {
            const newsSlider = new Swiper(".blog-slider",{
                spaceBetween: 30,
                speed: 2000,
                loop: !0,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: !1,
                },
                navigation: {
                    nextEl: ".array-prev",
                    prevEl: ".array-next",
                },
            })
        }
        if ($('.single-select').length) {
            $('.single-select').niceSelect()
        }
        function mousecursor() {
            if ($("body")) {
                const e = document.querySelector(".cursor-inner")
                  , t = document.querySelector(".cursor-outer");
                let n, i = 0, o = !1;
                (window.onmousemove = function(s) {
                    o || (t.style.transform = "translate(" + s.clientX + "px, " + s.clientY + "px)"),
                    (e.style.transform = "translate(" + s.clientX + "px, " + s.clientY + "px)"),
                    (n = s.clientY),
                    (i = s.clientX)
                }
                ),
                $("body").on("mouseenter", "a, .cursor-pointer", function() {
                    e.classList.add("cursor-hover");
                    t.classList.add("cursor-hover")
                }),
                $("body").on("mouseleave", "a, .cursor-pointer", function() {
                    ($(this).is("a") && $(this).closest(".cursor-pointer").length) || (e.classList.remove("cursor-hover"),
                    t.classList.remove("cursor-hover"))
                }),
                (e.style.visibility = "visible"),
                (t.style.visibility = "visible")
            }
        }
        $(function() {
            mousecursor()
        });
        $('.progress-bar').each(function() {
            var $this = $(this);
            var progressWidth = $this.attr('style').match(/width:\s*(\d+)%/)[1] + '%';
            $this.waypoint(function() {
                $this.css({
                    '--progress-width': progressWidth,
                    'animation': 'animate-positive 1.8s forwards',
                    'opacity': '1'
                })
            }, {
                offset: '75%'
            })
        });
        const $searchWrap = $(".search-wrap");
        const $navSearch = $(".nav-search");
        const $searchClose = $("#search-close");
        $(".search-trigger").on("click", function(e) {
            e.preventDefault();
            $searchWrap.animate({
                opacity: "toggle"
            }, 500);
            $navSearch.add($searchClose).addClass("open")
        });
        $(".search-close").on("click", function(e) {
            e.preventDefault();
            $searchWrap.animate({
                opacity: "toggle"
            }, 500);
            $navSearch.add($searchClose).removeClass("open")
        });
        function closeSearch() {
            $searchWrap.fadeOut(200);
            $navSearch.add($searchClose).removeClass("open")
        }
        $(document.body).on("click", function(e) {
            closeSearch()
        });
        $(".search-trigger, .main-search-input").on("click", function(e) {
            e.stopPropagation()
        });
        var swiper = new Swiper(".gt-brand-top-active",{
            slidesPerView: 'auto',
            spaceBetween: 30,
            freemode: !0,
            centeredSlides: !0,
            loop: !0,
            speed: 4000,
            allowTouchMove: !1,
            autoplay: {
                delay: 1,
                disableOnInteraction: !0,
            },
        });
        $(".accordion-single .header-area").on("click", function() {
            if ($(this).closest(".accordion-single").hasClass("active")) {
                $(this).closest(".accordion-single").removeClass("active");
                $(this).next(".content-area").slideUp()
            } else {
                $(".accordion-single").removeClass("active");
                $(this).closest(".accordion-single").addClass("active");
                $(".content-area").not($(this).next(".content-area")).slideUp();
                $(this).next(".content-area").slideToggle()
            }
        });
        Splitting();
        $(".quantity-plus").each(function() {
            $(this).on("click", function(e) {
                e.preventDefault();
                const $qty = $(this).siblings(".qty-input");
                const currentVal = parseInt($qty.val(), 10);
                if (!isNaN(currentVal)) {
                    $qty.val(currentVal + 1)
                }
            })
        });
        $(".quantity-minus").each(function() {
            $(this).on("click", function(e) {
                e.preventDefault();
                const $qty = $(this).siblings(".qty-input");
                const currentVal = parseInt($qty.val(), 10);
                if (!isNaN(currentVal) && currentVal > 1) {
                    $qty.val(currentVal - 1)
                }
            })
        });
        $(".circle-bar").loading()
    });
    $.fn.loading = function() {
        const DEFAULTS = {
            backgroundColor: '#b3cef6',
            progressColor: '#4b86db',
            percent: 75,
            duration: 2000
        };
        $(this).each(function() {
            const $target = $(this);
            const opts = {
                backgroundColor: $target.data('color') ? $target.data('color').split(',')[0] : DEFAULTS.backgroundColor,
                progressColor: $target.data('color') ? $target.data('color').split(',')[1] : DEFAULTS.progressColor,
                percent: $target.data('percent') ? $target.data('percent') : DEFAULTS.percent,
                duration: $target.data('duration') ? $target.data('duration') : DEFAULTS.duration
            };
            $target.append('<div class="background"></div><div class="rotate"></div><div class="left"></div><div class="right"></div><div class=""><span>' + opts.percent + '%</span></div>');
            $target.find('.background').css('background-color', opts.backgroundColor);
            $target.find('.left').css('background-color', opts.backgroundColor);
            $target.find('.rotate').css('background-color', opts.progressColor);
            $target.find('.right').css('background-color', opts.progressColor);
            const $rotate = $target.find('.rotate');
            setTimeout(function() {
                $rotate.css({
                    'transition': 'transform ' + opts.duration + 'ms linear',
                    'transform': 'rotate(' + opts.percent * 3.6 + 'deg)'
                })
            }, 1);
            if (opts.percent > 50) {
                let animationRight = 'toggle ' + (opts.duration / opts.percent * 50) + 'ms step-end';
                let animationLeft = 'toggle ' + (opts.duration / opts.percent * 50) + 'ms step-start';
                $target.find('.right').css({
                    animation: animationRight,
                    opacity: 1
                });
                $target.find('.left').css({
                    animation: animationLeft,
                    opacity: 0
                })
            }
        })
    }
    function loader() {
        $(window).on('load', function() {
            $(".preloader").addClass('loaded');
            $(".preloader").delay(600).fadeOut()
        })
    }
    loader()
}
)(jQuery)
