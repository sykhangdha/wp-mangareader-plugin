jQuery(document).ready(function($) {
    var mangaReader = $('.manga-reader');
    var mangaImages = mangaReader.find('.manga-images img');
    var mangaPagination = mangaReader.find('.manga-pagination');
    var currentIndex = 0;
    var totalPages = mangaImages.length;
    var pagedViewEnabled = true;

    var pagedView = function() {
        var currentImage = $(mangaImages[currentIndex]);
        currentImage.show().siblings().hide();
        mangaPagination.text((currentIndex + 1) + '/' + totalPages);
    }

    var listView = function() {
        mangaImages.show();
        mangaPagination.empty();
    }

    pagedView();

    $('.manga-reader-view button').click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        if ($(this).hasClass('paged-view')) {
            $(document).off('keydown');
            pagedViewEnabled = true;
            pagedView();
        } else {
            listView();
            pagedViewEnabled = false;
        }
    });

    $(document).keydown(function(e) {
        if (pagedViewEnabled && mangaReader.is(':visible') && e.keyCode === 39) { // Right Arrow
            if (currentIndex === totalPages - 1) {
                alert('Last page reached');
            } else {
                currentIndex++;
                pagedView();
            }
        } else if (pagedViewEnabled && mangaReader.is(':visible') && e.keyCode === 37) { // Left Arrow
            if (currentIndex === 0) {
                alert('First page reached');
            } else {
                currentIndex--;
                pagedView();
            }
        }
    });

    mangaImages.click(function() {
        if (!pagedViewEnabled) {
            var currentImage = $(this);
            var nextImage = currentImage.next('img');
            if (nextImage.length) {
                var top = nextImage.offset().top;
                $('html, body').animate({
                    scrollTop: top
                }, 500);
            } else {
                alert('Last image reached');
            }
        } else {
            if (currentIndex === totalPages - 1) {
                alert('Last page reached');
            } else {
                currentIndex++;
                pagedView();
                if (currentIndex === totalPages - 1) {
                    $(document).off('keydown');
                }
            }
        }
    });
});
// new
$(window).on('load', function() {
    var mangaImages = $('.manga-images img');
    mangaImages.on('load', function() {
        $(this).removeClass('img-loading');
    });
});
// end new
