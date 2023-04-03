jQuery(document).ready(function($) {
    var mangaReader = $('.manga-reader');
    var mangaImages = mangaReader.find('.manga-images img');
    var mangaPagination = mangaReader.find('.manga-pagination');
    var currentIndex = 0;
    var totalPages = mangaImages.length;

    // Paged View
    var pagedView = function() {
        var currentImage = $(mangaImages[currentIndex]);
        currentImage.show().siblings().hide();
        mangaPagination.text((currentIndex + 1) + '/' + totalPages);
        // NEW: Disable clicking until arrow keys are used
        currentImage.one('click', function() {
            $(document).off('keydown');
            pagedView();
        });
    }

    // List View
    var listView = function() {
        mangaImages.show();
        mangaPagination.empty();
    }

    // Default View
    pagedView();

    // Switch View
    $('.manga-reader-view button').click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        if ($(this).hasClass('paged-view')) {
            $(document).off('keydown');
            pagedView();
        } else {
            listView();
        }
    });

    // Arrow Key Navigation
    $(document).keydown(function(e) {
        if (mangaReader.is(':visible') && e.keyCode === 39) { // Right Arrow
            currentIndex = currentIndex === totalPages - 1 ? 0 : currentIndex + 1;
            pagedView();
            if (currentIndex === totalPages - 1) {
                alert('Last page reached');
            }
        } else if (mangaReader.is(':visible') && e.keyCode === 37) { // Left Arrow
            if (currentIndex === 0) {
                alert('First page reached');
            } else {
                currentIndex = currentIndex === 0 ? 0 : currentIndex - 1;
                pagedView();
            }
        }
    });
});
