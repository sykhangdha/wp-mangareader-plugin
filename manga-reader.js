jQuery(document).ready(function($) {
  var mangaReader = $('.manga-reader');
  var mangaImages = mangaReader.find('.manga-images a.img-popup');
  var currentIndex = 0;
  var totalPages = mangaImages.length;

  var scrollToTop = function() {
    var currentImage = $(mangaImages[currentIndex]);
    var top = currentImage.offset().top;
    $('html, body').animate({
      scrollTop: top
    }, 500);
  };

  var handleEscapeKey = function(e) {
    if (e.keyCode === 27) { // Escape key
      $.magnificPopup.close();
    }
  };

  var openMagnificPopup = function(index) {
    if (index < totalPages) {
      $.magnificPopup.open({
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
          },
          close: function() {
            scrollToTop();
          },
          beforeClose: function() {
            $(document).off('keydown.magnificPopup', handleEscapeKey);
          }
        },
        closeOnBgClick: false,
        // Enable swipe functionality for mobile
        swipe: {
          onTouchMove: function(e) {
            handleSwipeMove(e);
          },
          onTouchEnd: function() {
            handleSwipeEnd();
          }
        }
      }, index);

      // Add close button in Magnific Popup
      $.magnificPopup.instance.close = function() {
        if ($.magnificPopup.proto.close) {
          $.magnificPopup.proto.close.call(this);
          scrollToTop();
        }
      };

      // Handle escape key separately
      $(document).on('keydown.magnificPopup', handleEscapeKey);
    } else {
      // Close the gallery if trying to open beyond the last image
      $.magnificPopup.close();
    }
  };

  var handleSwipeMove = function(e) {
    // Handle swipe move event if needed
  };

  var handleSwipeEnd = function() {
    var delta = touchEndX - touchStartX;

    if (delta > 50) {
      // Swipe right, go to the previous image
      if (currentIndex > 0) {
        currentIndex--;
        openMagnificPopup(currentIndex);
      }
    } else if (delta < -50) {
      // Swipe left, go to the next image
      if (currentIndex < totalPages - 1) {
        currentIndex++;
        openMagnificPopup(currentIndex);
      } else {
        // Reached the last image, close the gallery
        $.magnificPopup.close();
      }
    }

    // Reset touch coordinates
    touchStartX = null;
    touchEndX = null;
  };

  // Touch event variables
  var touchStartX = null;
  var touchEndX = null;

  mangaImages.click(function(e) {
    e.preventDefault();
    openMagnificPopup(currentIndex);
  });

  $(document).on('touchstart', '.mfp-img', function(e) {
    touchStartX = e.originalEvent.touches[0].pageX;
  });

  $(document).on('touchmove', '.mfp-img', function(e) {
    // Handle touch move event if needed
    handleSwipeMove(e);
  });

  $(document).on('touchend', '.mfp-img', function(e) {
    touchEndX = e.originalEvent.changedTouches[0].pageX;
    handleSwipeEnd();
  });

  // Close button click event
  $(document).on('click', '.mfp-close, .mfp-arrow-right, .mfp-arrow-left', function() {
    $.magnificPopup.close();
  });
});
