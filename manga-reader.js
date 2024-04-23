jQuery(document).ready(function($) {
  var mangaReader = $('.manga-reader');
  var mangaImages = mangaReader.find('.manga-images a.img-popup');
  var currentIndex = 0;
  var totalPages = mangaImages.length;
  var magnificPopupInstance = null;
  var touchStartX = null;

  var scrollToNextImage = function() {
    if (currentIndex < totalPages - 1) {
      currentIndex++;
      var currentImage = $(mangaImages[currentIndex]);
      var top = currentImage.offset().top;
      $('html, body').animate({
        scrollTop: top
      }, 500);
    }
  };

  var openMagnificPopup = function(index) {
    if (index < totalPages) {
      magnificPopupInstance = $.magnificPopup.open({
        items: mangaImages.toArray(),
        gallery: {
          enabled: true
        },
        type: 'image',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        callbacks: {
          change: function() {
            currentIndex = this.index;
            // Check if the last image is reached
            if (currentIndex === totalPages - 1) {
              $.magnificPopup.close();
            }
          },
          close: function() {
            scrollToTop();
            $('.exit-button').hide(); // Hide exit button when popup is closed
          },
          beforeClose: function() {
            $(document).off('keydown.magnificPopup');
          },
          open: function() {
            $('.exit-button').show(); // Show exit button when popup is opened
            // Handle escape key separately
            $(document).on('keydown.magnificPopup', handleEscapeKey);
            // Enable swipe functionality for mobile
            $('.mfp-container').on('touchstart', handleSwipeStart);
            $('.mfp-container').on('touchend', handleSwipeEnd);
          }
        },
        closeOnBgClick: false
      }, index);
    }
  };

  var scrollToTop = function() {
    var currentImage = $(mangaImages[currentIndex]);
    var top = currentImage.offset().top;
    $('html, body').animate({
      scrollTop: top
    }, 500);
  };

  var handleEscapeKey = function(e) {
    if (e.keyCode === 27 && magnificPopupInstance !== null) { // Escape key
      magnificPopupInstance.close();
    }
  };

  var handleSwipeStart = function(e) {
    touchStartX = e.originalEvent.touches[0].pageX;
  };

  var handleSwipeEnd = function(e) {
    if (touchStartX !== null) {
      var touchEndX = e.originalEvent.changedTouches[0].pageX;
      var deltaX = touchEndX - touchStartX;
      if (deltaX > 50) {
        // Swipe right, go to the previous image
        if (currentIndex > 0) {
          currentIndex--;
          openMagnificPopup(currentIndex);
        }
      } else if (deltaX < -50) {
        // Swipe left, go to the next image
        if (currentIndex < totalPages - 1) {
          currentIndex++;
          openMagnificPopup(currentIndex);
        }
      }
      touchStartX = null;
    }
  };

  // Function to open Magnific Popup image gallery
  var openPagedView = function() {
    openMagnificPopup(currentIndex);
  };

  // Add "Paged view" button
  var pagedViewButton = $('<button/>', {
    text: 'Switch to Paged View',
    class: 'paged-view-button',
    click: openPagedView
  });

  // Append button to the reader
  mangaReader.prepend(pagedViewButton);

  // Click event for mangaImages
  mangaImages.click(function(e) {
    e.preventDefault();
    scrollToNextImage();
  });

  // Close popup when clicking outside of the image or pressing Escape key
  $(document).on('click', function(e) {
    if (!$(e.target).closest('.mfp-container').length && magnificPopupInstance !== null) {
      magnificPopupInstance.close();
    }
  });

  // Close popup when pressing Escape key
  $(document).on('keydown', function(e) {
    if (e.keyCode === 27 && magnificPopupInstance !== null) { // Escape key
      magnificPopupInstance.close();
    }
  });
});
