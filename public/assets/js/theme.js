var durotan;
(function($) {
  "use strict";
  durotan = (function() {
    return {
      init: function() {
        // Header
        this.popupSearch();
        this.mobileMenu();

        // Home default
        this.homeDefaultSlider();
        this.homeOurProducts();

        // Home full width
        this.homeFullWidthCanvasMenu();
        this.homeFullWidthCart();
        this.homeFullWidthSlider();

        // Home modern

        // Home collection
        this.homeCollectionSlider();

        // Home dark
        this.homeDarkSlider();
        this.homeDarkTestimonials();

        // Home classic
        this.homeClassicOurProducts();

        // Home best selling
        this.homeBestSellingSlider();

        // Home sidebar
        this.homeSideBarTestimonials();

        // Home minimal
        this.homeMinimalSlider();
        this.homeMinimalInstagram();

        // Home shoppable
        this.homeShoppableMainBanner();

        // Product grid item
        this.productGridItemImages();

        // Product list item
        this.productListItemImages();

        // Collection
        this.collectionFilter();

        // Blog
        this.blog();

        // Account
        this.accountLogin();

        // Product
        this.productImages();
        this.productMobileImages();
        this.productRelatedProducts();
        this.productStickyAddToCart();

        // Utilities
        this.videoPopup();
        this.countDown();
        this.quantity();
        this.tabs();
        this.accordion();
        this.rating();
        this.background();
        this.backgroundParallax();
        this.zoom();
      },

      // Header
      popupSearch: function() {
        $(".js-open-popup-search").on("click", function() {
          $(".js-search-popup").addClass("active");
          return false;
        });
        $(".js-close-search-popup").on("click", function() {
          $(".js-search-popup").removeClass("active");
          return false;
        });
      },

      mobileMenu: function() {
        $('.js-mobile-menu-dropdown-btn').on("click", function() {
          $(this).parent().find('.js-mobile-menu-dropdown-menu').slideToggle(500);
        });
        $(".js-open-mobile-menu").on("click", function() {
          $(".js-mobile-menu").addClass("active");
          $("body").css("overflow", "hidden");
          return false;
        });
        $(".js-close-mobile-menu").on("click", function() {
          $(".js-mobile-menu").removeClass("active");
          $("body").css("overflow", "visible");
          return false;
        });
      },

      // Home default
      homeDefaultSlider: function() {
        $('.js-home-default-slider').slick({
          dots: false,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          adaptiveHeight: true,
          fade: true,
          arrows: false
        });
        setTimeout(function () {
          $('.js-slider-tag, .js-slider-heading, .js-slider-description, .js-slider-action, .js-slider-image, .js-slider-price, .js-slider-second-heading').addClass("is-active");
        }, 100);
        $('.js-slider-prev').on("click", function() {
          $(this).parent().parent().parent().find('.slick-slider').slick('slickPrev');
        });
        $('.js-slider-next').on("click", function() {
          $(this).parent().parent().parent().find('.slick-slider').slick('slickNext');
        });
      },

      homeOurProducts: function() {
        $('.js-home-our-products').slick({
          slidesToShow: 4,
          slidesToScroll: 1,
          dots: true,
          infinite: false,
          adaptiveHeight: true,
          responsive: [
            {
              breakpoint: 1499,
              settings: {
                slidesToShow: 3
              }
            },
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },

      // Home full width
      homeFullWidthCanvasMenu: function() {
        $(".js-open-canvas-menu").on("click", function() {
          $(".js-canvas-menu").addClass("active");
          $("body").css("overflow", "hidden");
          return false;
        });
        $(".js-close-canvas-menu").on("click", function() {
          $(".js-canvas-menu").removeClass("active");
          $("body").css("overflow", "visible");
          return false;
        });
      },

      homeFullWidthCart: function() {
        $(".js-open-canvas-cart").on("click", function() {
          $(".js-canvas-cart").addClass("active");
          $("body").css("overflow", "hidden");
          return false;
        });
        $(".js-close-canvas-cart").on("click", function() {
          $(".js-canvas-cart").removeClass("active");
          $("body").css("overflow", "visible");
          return false;
        });
      },


   

      homeFullWidthSlider: function() {
        $('.js-home-full-width-slider').slick({
          dots: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          adaptiveHeight: true,
          fade: true,
          arrows: true
        });
        $(".js-slider-item-thumbnail").on("click", function() {
          var id = $(this).attr("data-id");
          var sliderId = $(this).attr("data-slider-id");
          $(".js-slider-item[data-id='" + sliderId + "']").find(".js-slider-item-thumbnail").removeClass("active");
          $(this).addClass("active");
          $(".js-slider-item[data-id='" + sliderId + "']").find(".js-slider-item-image").removeClass("active");
          $(".js-slider-item[data-id='" + sliderId + "']").find(".js-slider-item-image[data-id='" + id + "']").addClass("active");
          return false;
        });
      },

      // Home collection
      homeCollectionSlider: function() {
        $('.js-home-collection-slider').slick({
          dots: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          adaptiveHeight: true,
          fade: true,
          arrows: true
        });
      },

      // Home dark
      homeDarkSlider: function() {
        $('.js-home-dark-slider').slick({
          dots: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          adaptiveHeight: true,
          fade: true,
          arrows: true
        });
      },

      homeDarkTestimonials: function() {
        $('.js-dark-testimonials').slick({
          slidesToShow: 2,
          slidesToScroll: 1,
          dots: false,
          arrows: true,
          infinite: false,
          responsive: [
            {
              breakpoint: 1200,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 1199,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },

      // Home classic
      homeClassicOurProducts: function() {
        $('.js-home-classic-our-products').slick({
          slidesToShow: 3,
          slidesToScroll: 1,
          dots: true,
          infinite: false,
          adaptiveHeight: true,
          responsive: [
            {
              breakpoint: 1499,
              settings: {
                slidesToShow: 3
              }
            },
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
        $('.js-home-classic-our-products-2').slick({
          slidesToShow: 4,
          slidesToScroll: 1,
          dots: true,
          infinite: false,
          adaptiveHeight: true,
          responsive: [
            {
              breakpoint: 1499,
              settings: {
                slidesToShow: 3
              }
            },
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },

      // Home best selling
      homeBestSellingSlider: function() {
        $('.js-home-best-selling-slider').slick({
          dots: true,
          vertical: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          adaptiveHeight: true,
          arrows: false
        });
      },

      // Home sidebar 
      homeSideBarTestimonials: function() {
        $('.js-sidebar-testimonials').slick({
          slidesToShow: 2,
          slidesToScroll: 1,
          dots: false,
          arrows: true,
          infinite: false,
          responsive: [
            {
              breakpoint: 1200,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 1199,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },

      // Home minimal
      homeMinimalSlider: function() {
        $('.js-home-minimal-slider').slick({
          dots: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          adaptiveHeight: true,
          fade: true,
          arrows: true
        });
        if($('.js-home-minimal-slider').length > 0) {
          $(".minimal-slider .slick-prev").css("left", $(".minimal-slider__feature").offset().left);
          $(".minimal-slider .slick-next, .minimal-slider .slick-dots").css("right", $(".minimal-slider__feature").offset().left);
          $(window).resize(function() {
            setTimeout(function () {
              $(".minimal-slider .slick-prev").css("left", $(".minimal-slider__feature").offset().left);
              $(".minimal-slider .slick-next, .minimal-slider .slick-dots").css("right", $(".minimal-slider__feature").offset().left);
            }, 200);
          });
        }
      },

      homeMinimalInstagram: function() {
        $('.js-home-minimal-instagram').slick({
          slidesToShow: 6,
          slidesToScroll: 1,
          dots: true,
          infinite: false,
          adaptiveHeight: true,
          responsive: [
            {
              breakpoint: 1499,
              settings: {
                slidesToShow: 6
              }
            },
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 5
              }
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 3
              }
            }
          ]
        });
      },

      // Home shoppable
      homeShoppableMainBanner: function() {
        $(".js-open-pin").on("click", function() {
          var id = $(this).attr("data-id");
          $(".js-pin-content[data-id='" + id + "']").addClass("active");
          return false;
        });
        $(".js-close-pin").on("click", function() {
          var id = $(this).attr("data-id");
          $(".js-pin-content[data-id='" + id + "']").removeClass("active");
          return false;
        });
      },

      // Product grid item
      productGridItemImages: function() {
        $(".js-product-grid-previous-image").on("click", function() {
          var currentImage = parseInt($(this).parent().parent().parent().find(".js-product-grid-images").attr("data-current-image"));
          var previousImage = currentImage - 1;
          var totalImages = parseInt($(this).parent().parent().parent().find(".js-product-grid-image").length) - 1;
          $(this).parent().parent().parent().find(".js-product-grid-image").removeClass("active");
          if(previousImage >= 0) {
            $(this).parent().parent().parent().find(".js-product-grid-image").eq(previousImage).addClass("active");
            $(this).parent().parent().parent().find(".js-product-grid-images").attr("data-current-image", previousImage);
          } else {
            $(this).parent().parent().parent().find(".js-product-grid-image").eq(totalImages).addClass("active");
            $(this).parent().parent().parent().find(".js-product-grid-images").attr("data-current-image", totalImages);
          }
        });
        $(".js-product-grid-next-image").on("click", function() {
          var currentImage = parseInt($(this).parent().parent().parent().find(".js-product-grid-images").attr("data-current-image"));
          var nextImage = currentImage + 1;
          $(this).parent().parent().parent().find(".js-product-grid-image").removeClass("active");
          if($(this).parent().parent().parent().find(".js-product-grid-image").eq(nextImage).length > 0) {
            $(this).parent().parent().parent().find(".js-product-grid-image").eq(nextImage).addClass("active");
            $(this).parent().parent().parent().find(".js-product-grid-images").attr("data-current-image", nextImage);
          } else {
            $(this).parent().parent().parent().find(".js-product-grid-image").eq(0).addClass("active");
            $(this).parent().parent().parent().find(".js-product-grid-images").attr("data-current-image", 0);
          }
        });
      },

      // Product list item
      productListItemImages: function() {
        $(".js-product-list-previous-image").on("click", function() {
          var currentImage = parseInt($(this).parent().parent().parent().find(".js-product-list-images").attr("data-current-image"));
          var previousImage = currentImage - 1;
          var totalImages = parseInt($(this).parent().parent().parent().find(".js-product-list-image").length) - 1;
          $(this).parent().parent().parent().find(".js-product-list-image").removeClass("active");
          if(previousImage >= 0) {
            $(this).parent().parent().parent().find(".js-product-list-image").eq(previousImage).addClass("active");
            $(this).parent().parent().parent().find(".js-product-list-images").attr("data-current-image", previousImage);
          } else {
            $(this).parent().parent().parent().find(".js-product-list-image").eq(totalImages).addClass("active");
            $(this).parent().parent().parent().find(".js-product-list-images").attr("data-current-image", totalImages);
          }
        });
        $(".js-product-list-next-image").on("click", function() {
          var currentImage = parseInt($(this).parent().parent().parent().find(".js-product-list-images").attr("data-current-image"));
          var nextImage = currentImage + 1;
          $(this).parent().parent().parent().find(".js-product-list-image").removeClass("active");
          if($(this).parent().parent().parent().find(".js-product-list-image").eq(nextImage).length > 0) {
            $(this).parent().parent().parent().find(".js-product-list-image").eq(nextImage).addClass("active");
            $(this).parent().parent().parent().find(".js-product-list-images").attr("data-current-image", nextImage);
          } else {
            $(this).parent().parent().parent().find(".js-product-list-image").eq(0).addClass("active");
            $(this).parent().parent().parent().find(".js-product-list-images").attr("data-current-image", 0);
          }
        });
      },

      // Collection
      collectionFilter: function() {
        $(".js-open-collection-filter").on("click", function(){
          $(this).toggleClass('active');
          $(".js-top-filter").slideToggle('');
        });
        $(".js-open-mobile-filter").on("click", function() {
          $(".js-top-filter").addClass("active");
          setTimeout(function () {
            $(".js-top-filter").addClass("show");
          }, 100);
          return false;
        }); 
        $(".js-close-filter").on("click", function() {
          $(".js-top-filter").removeClass("show");
          setTimeout(function () {
            $(".js-top-filter").removeClass("active");
          }, 300);
          return false;
        });
        if ($(".js-price-slider").length) {
          const minValue = localStorage.getItem("sliderMin") || 100;
          const maxValue = localStorage.getItem("sliderMax") || 150000;
          $(".js-price-slider").slider({
            range: true,
            min: 100,
            max: 300000,
            values: [minValue, maxValue],
            slide: function( event, ui ) {
                $(".js-price-slider-value").val("AED " + ui.values[ 0 ] + " - AED " + ui.values[ 1 ]);
                localStorage.setItem("sliderMin", ui.values[0]);
                localStorage.setItem("sliderMax", ui.values[1]);
            }
          });
          if($(".js-price-slider-value").length) {
            $(".js-price-slider-value").val("AED " + $(".js-price-slider").slider("values", 0) + " - AED " + $(".js-price-slider").slider("values", 1));
          
          };
        };
      },

      // Blog
      blog: function() {
        $('.js-featured-articles').slick({
          dots: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          fade: true,
          arrows: false
        });
        $('.js-latest-articles').slick({
          slidesToShow: 2,
          slidesToScroll: 1,
          dots: false,
          arrows: true,
          infinite: false,
          responsive: [
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2
              }
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },

      // Account
      accountLogin: function() {
        $(".js-login-forgot-password").on("click", function() {
          $(".js-login-in").removeClass("active");
          $(".js-forgot-password").addClass("active");
          return false;
        });
        $(".js-login-back").on("click", function() {
          $(".js-login-in").addClass("active");
          $(".js-forgot-password").removeClass("active");
          return false;
        });
      },


        // Account
   

      // Product
      productImages: function() {
        $(".js-product-thumbnail").on("click", function() {
          var id = $(this).attr("data-id");
          $(".js-product-thumbnail").removeClass("active");
          $(this).addClass("active");
          $(".js-product-main-image").removeClass("active");
          $(".js-product-main-image[data-id='" + id + "']").addClass("active");
          durotan.zoom();
          if($(".js-product-layout-2").length > 0) {
            $("html, body").stop().animate({scrollTop: $(".js-product-main-image[data-id='" + id + "']").offset().top}, 500);
          }
          return false;
        });
        $(".js-popup-gallery a").jqPhotoSwipe({
          forceSingleGallery: true
        });
        $(".js-product-mobile-gallery a").jqPhotoSwipe({
          forceSingleGallery: true
        });
        $(".js-product-thumbnails-prev").on("click", function() {
          var currentGroup = $(this).attr("data-current-group");
          var allGroups = $(this).attr("data-groups");
          if(currentGroup == 1) {
            currentGroup = allGroups;
          } else {
            currentGroup--;
          }
          $(this).attr("data-current-group", currentGroup);
          $(".js-product-thumbnails-next").attr("data-current-group", currentGroup);
          $(".js-product-thumnail-slider").addClass("d-none");
          $(".js-product-thumnail-slider[data-group='" + currentGroup + "']").removeClass("d-none");
          return false;
        });
        $(".js-product-thumbnails-next").on("click", function() {
          var currentGroup = $(this).attr("data-current-group");
          var allGroups = $(this).attr("data-groups");
          if(currentGroup == allGroups) {
            currentGroup = 1;
          } else {
            currentGroup++;
          }
          $(this).attr("data-current-group", currentGroup);
          $(".js-product-thumbnails-prev").attr("data-current-group", currentGroup);
          $(".js-product-thumnail-slider").addClass("d-none");
          $(".js-product-thumnail-slider[data-group='" + currentGroup + "']").removeClass("d-none");
          return false;
        });
      },

      productMobileImages: function() {
        $('.js-product-mobile-gallery').slick({
          dots: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          arrows: true
        });
      },

      productRelatedProducts: function() {
        $('.js-related-products').slick({
          slidesToShow: 5,
          slidesToScroll: 1,
          dots: false,
          arrows: true,
          infinite: false,
          responsive: [
            {
              breakpoint: 1399,
              settings: {
                slidesToShow: 4
              }
            },
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2
              }
            }
          ]
        });
      },

      productStickyAddToCart: function() {
        if($(".js-product-action").length > 0) {
          $(window).scroll(function() {
            var scrollTop = $(window).scrollTop();
            if(scrollTop > $(".js-product-action").offset().top + $(".js-product-action").outerHeight()) { 
              $(".js-sticky-add-to-cart").addClass("active");
            } else {
              $(".js-sticky-add-to-cart").removeClass("active");
            }
          });
        }
      },

      // Utilities
      videoPopup: function() {
        $('.js-video-popup').magnificPopup({
          type: 'iframe',
          mainClass: 'mfp-fade',
          removalDelay: 160,
          preloader: false,
          fixedContentPos: false
        });
      },

      countDown: function() {
        $(".js-countdown").each(function () {
          var that = $(this);
          setInterval(function () {
            var currentDate = new Date().getTime();
            var deadlineDate = new Date($(that).attr("data-deadline-date")).getTime();
            var distance = deadlineDate - currentDate;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            $(that).find(".js-countdown-days").html(days);
            $(that).find(".js-countdown-hours").html(hours);
            $(that).find(".js-countdown-minutes").html(minutes);
            $(that).find(".js-countdown-seconds").html(seconds);
          }, 1000);
        });
      },

      quantity: function() {
        $(".js-quantity-down").on("click", function() {
          var q_val_up = parseInt($(this).parent().find(".js-quantity-field").val());
          if(isNaN(q_val_up)) {
            q_val_up = 0;
          }
          if(q_val_up > 0) {
            $(this).parent().find(".js-quantity-field").val(q_val_up-1).keyup();
          } 
          return false; 
        });
        $(".js-quantity-up").on("click", function() {
          var q_val_up = parseInt($(this).parent().find(".js-quantity-field").val());
          if(isNaN(q_val_up)) {
            q_val_up = 0;
          }
          $(this).parent().find(".js-quantity-field").val(q_val_up+1).keyup(); 
          return false; 
        });
      },

      tabs: function() {
        $(".js-open-tab").on("click", function() {
          var id = $(this).attr("data-id");
          $(".js-tab[data-id='" + id + "']").addClass("active");
          return false;
        });
        $(".js-close-tab").on("click", function() {
          $(".js-tab").removeClass("active");
          return false;
        });
        $(".js-tab-link").on("click", function() {
          $(this).parent().parent().find(".js-tab-link").removeClass("active");
          $(this).addClass("active");
          $(this).parent().parent().parent().find(".js-tab-content").removeClass("tab-content__active").removeClass("tab-content__show");
          var that = $(this).parent().parent().parent().find(".js-tab-content[data-id='" + $(this).attr("data-id") + "']");
          $(that).addClass("tab-content__active");
          setTimeout(function () {
            $(that).addClass("tab-content__show");
          }, 100);
          return false;
        });
      },

      accordion: function() {
        $(".js-accordion-title").on("click", function() {
          $(this).parent().parent().find(".js-accordion").removeClass("current");
          $(this).parent().toggleClass("active").addClass("current");
          $(this).parent().parent().find(".js-accordion:not(.current).active .js-accordion-content").slideToggle(500);
          $(this).parent().parent().find(".js-accordion:not(.current).active").removeClass("active");
          $(this).parent().find(".js-accordion-content").slideToggle(500);
        });
        $(".js-accordion.active").each(function () {
          $(this).find(".js-accordion-content").slideToggle(500);
        });
      },

      rating: function() {
        $(".js-rating-content").each(function () {
          var that = $(this);
          $(that).find('.js-rating').on("mouseover", function(){
            var rate = $(this).data('value');
            var i = 0;
            $(that).find('.js-rating').each(function(){
              i++;
              if(i <= rate) {
                $(this).addClass('active');
              } else {
                $(this).removeClass('active');
              }
            });
          });
          $(that).find('.js-rating').on("mouseleave", function(){
            var rate = $(this).parent().find(".js-rating-input:checked").val();
            rate = parseInt(rate);
            var i = 0;
            $(that).find('.js-rating').each(function(){
              i++;
              if(i <= rate){
                $(this).addClass('active');
              } else {
                $(this).removeClass('active');
              }
            });
          });
          $(that).find('.js-rating').on("click", function(){
            $(this).parent().find('.js-rating-input:nth('+ ($(this).data('value')-1) +')').prop('checked', true);
          });
        });
      },

      background: function() {
        $('.js-background').each(function() {
          $(this).css('background-image', 'url('+ $(this).attr('data-background') + ')');
        });
      },

      backgroundParallax: function() {
        if($(document).outerWidth() > 991) {
          $('.js-background-parallax').jarallax({
            speed: 0.3
          });
        }
      },

      zoom: function() {
        $('.active .js-zoomit').zoomIt();
        $('.active-2 .js-zoomit').zoomIt();
        $('.active-3 .js-zoomit').zoomIt();
        $('.active-4 .js-zoomit').zoomIt();
      }
    }
  })();
})(jQuery);

$(document).ready(function() {
  durotan.init();
});