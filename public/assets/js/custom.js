// document.addEventListener('DOMContentLoaded', function () {
//     const swiper = new Swiper('.gt-slider', {
//         loop: true,
//         navigation: {
//             nextEl: '.swiper-button-next',
//             prevEl: '.swiper-button-prev',
//         },
//         breakpoints: {
//             1200: { slidesPerView: 8 },
//             1024: { slidesPerView: 4 },
//             768: { slidesPerView: 3 },
//             480: { slidesPerView: 2 },
//         },
//     });
// });



document.addEventListener('DOMContentLoaded', function () {
    const swiper = new Swiper('.hero-service-slider', {
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        speed: 800,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        slidesPerView: 3,
        spaceBetween: 10,
        breakpoints: {
            1200: { slidesPerView: 8 },
            992: { slidesPerView: 6 },
            768: { slidesPerView: 4 },
            576: { slidesPerView: 3 },
            0: { slidesPerView: 2 },
        },
    });
});



document.addEventListener('DOMContentLoaded', () => {
    const prevButton = document.querySelector('.slider-button.prev');
    const nextButton = document.querySelector('.slider-button.next');

    const servicesWrapper = document.querySelector('.services-wrapper');

    // Scroll left on clicking the previous button
    prevButton.addEventListener('click', () => {
        servicesWrapper.scrollBy({ left: -200, behavior: 'smooth' });
    });

    // Scroll right on clicking the next button
    nextButton.addEventListener('click', () => {
        servicesWrapper.scrollBy({ left: 200, behavior: 'smooth' });
    });
});







var swiperInstances = [];

$(document).ready(function () {
    $(".product-slider").each(function () {
        var swiperInstance = new Swiper(this, {
            slidesPerView: "auto",
            spaceBetween: 20,
            loop: true,
            navigation: {
                
                prevEl: $(this).closest("#product-section").find(".product-custom-prev")[0],  // ✅ Fixed selector
                nextEl: $(this).closest("#product-section").find(".product-custom-next")[0],  // ✅ Fixed selector
            },
            breakpoints: {
                0: { slidesPerView: 1 },
                576: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                992: { slidesPerView: 3 },
                1200: { slidesPerView: 4 },
            }
        });
        swiperInstances.push(swiperInstance);
    });
});













$(document).ready(function () {
    // Set the default active tab as "Cybersecurity"
    let defaultTab = $(".tab[data-src='assets/videos/cybersecurity.mp4']");
    defaultTab.addClass("active");

    $(".tab").on("click", function () {
        let mediaType = $(this).data("type");
        let mediaSrc = $(this).data("src");

        // Remove active class from all tabs and add to clicked tab
        $(".tab").removeClass("active");
        $(this).addClass("active");

        if (mediaType === "video") {
            $("#features-video").attr("src", mediaSrc).removeClass("hidden");
            $("#features-image").addClass("hidden");
        } else {
            $("#features-image").attr("src", mediaSrc).removeClass("hidden");
            $("#features-video").addClass("hidden");
        }
    });
});







$(document).ready(function () {
    $(".industry-tab").on("click", function () {
        // Remove active class from all tabs and add to clicked tab
        $(".industry-tab").removeClass("active");
        $(this).addClass("active");

        // Get new image and text content
        let newImage = $(this).data("image");
        let newTitle = $(this).data("title");
        let newDescription = $(this).data("description");

        // Fade out current content
        $("#industry-bg-image, .industries-info").fadeOut(300, function () {
            // Change content after fade out
            $("#industry-bg-image").attr("src", newImage);
            $("#industry-title").text(newTitle);
            $("#industry-description").text(newDescription);

            // Fade in new content
            $("#industry-bg-image, .industries-info").fadeIn(300);
        });
    });
});







// Open Video Modal
function openVideoPopup() {
    document.getElementById("video-popup").style.display = "flex";
    document.getElementById("video-frame").src = "https://www.youtube.com/embed/YOUR_VIDEO_ID"; // Replace with actual video URL
}

// Close Video Modal
function closeVideoPopup() {
    document.getElementById("video-popup").style.display = "none";
    document.getElementById("video-frame").src = "";
}


var clientSwiper = new Swiper('.client-slider', {
    slidesPerView: 6, // Show 6 logos at a time
    loop: true, // Infinite loop
    autoplay: {
        delay: 2000, // 2 seconds per slide
        disableOnInteraction: false, // Keeps autoplay running
    },
    speed: 1000, // Smooth transition speed
    breakpoints: {
        0: { slidesPerView: 2 }, // Mobile
        576: { slidesPerView: 3 }, // Small screens
        768: { slidesPerView: 4 }, // Tablets
        1024: { slidesPerView: 5 }, // Medium screens
        1200: { slidesPerView: 6 }, // Large screens
    }
});




document.addEventListener("DOMContentLoaded", function () {
    let serviceCard = document.getElementById("services-card");

    serviceCard.addEventListener("mouseenter", function () {
        serviceCard.classList.add("hover-effect");
    });

    serviceCard.addEventListener("mouseleave", function () {
        serviceCard.classList.remove("hover-effect");
    });
});


