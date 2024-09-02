export default function customSplide() {
  jQuery(document).ready(() => {
    if (jQuery('.splide').length) {
      setTimeout(() => {
        const fme_pgisfw_settings_data = window.fme_pgisfw_settings_data || {};
        const navigation_icon = fme_pgisfw_settings_data.fme_pgisfw_navigation_icon_status;
        const thumbnails_to_show = fme_pgisfw_settings_data.fme_thumbnails_to_show;
        const width = jQuery('.woocommerce-product-gallery').width();
        const height = 'auto';

        const secondary_slider = new Splide('.splide', {
          direction: 'ttb',
          rewind: true,
          gap: 1,
          pagination: false,
          cover: true,
          focus: 'center',
          fixedWidth: 80,
          fixedHeight: 80,
          isNavigation: true,
          perPage: thumbnails_to_show,
          heightRatio: 0.2,
          pauseOnHover: true,
          arrows: navigation_icon,
          classes: {
            arrows: 'splide__arrows secondary-class-arrows',
          },
          breakpoints: {
            1024: {
              direction: 'ltr',
              arrows: true,
              pagination: true
            },
          }
        }).mount();

        jQuery(document).ready(function() {
          jQuery('#secondary-slider-list li.splide__slide[role="button"]').removeAttr('role');
        });

        const primary_slider = new Splide('.primary-slider', {
          type: 'fade',
          fixedWidth: width,
          fixedHeight: height,
          pagination: false,
          pauseOnHover: true,
          focus: 'center',
          arrows: false,
          cover: true,
          classes: {
            arrows: 'splide__arrows primary-class-arrows',
          }
        });
        primary_slider.sync(secondary_slider).mount();

        // Custom pagination for secondary slider within 1024 breakpoint
        const customPaginationContainer = document.createElement('div');
        customPaginationContainer.className = 'custom-pagination';
        const splidePagination = document.querySelector('.splide__pagination');

        if (splidePagination) {
          splidePagination.parentNode.insertBefore(customPaginationContainer, splidePagination);
        }

        const updateCustomPagination = function () {
          const totalSlides = secondary_slider.length;
          const currentSlideNumber = secondary_slider.index + 1;
          customPaginationContainer.textContent = `${currentSlideNumber}/${totalSlides}`;
        };

        secondary_slider.on('move', updateCustomPagination);
        updateCustomPagination(); // Initialize the custom pagination on page load

      }, 500);
    }
  });
}
