jQuery(document).ready(function($) {
  var mangaReader = $('.manga-reader');
  var mangaImages = mangaReader.find('.manga-images a.img-popup');
  var currentIndex = 0;
  var totalPages = mangaImages.length;
  var magnificPopupInstance = null;

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
    } else {
      // Close the gallery if trying to open beyond the last image
      $.magnificPopup.close();
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

  var handleSwipeMove = function(e) {
    // Handle swipe move event if needed
  };

  var handleSwipeEnd = function() {
    // Handle swipe end event if needed
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
