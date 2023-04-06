jQuery(document).ready(function($) {
    var mangaReader = $('.manga-reader');
    var mangaImages = mangaReader.find('.manga-images img');
    var mangaPagination = mangaReader.find('.manga-pagination');
    var currentIndex = 0;
    var totalPages = mangaImages.length;
    var listViewFlag = false;

    var pagedView = function() {
        var currentImage = $(mangaImages[currentIndex]);
        currentImage.show().siblings().hide();
        mangaPagination.text((currentIndex + 1) + '/' + totalPages);
        $(document).off('keydown').on('keydown', function(e) {
            if (e.keyCode === 39) { // Right Arrow
                if (currentIndex === totalPages - 1) {
                    // Check if there is a next post in the same category using WordPress post navigation
                    var nextPostUrl = $('.nav-next a').attr('href');
                    if (nextPostUrl) {
                        window.location.href = nextPostUrl;
                    } else {
                        alert('Last page reached');
                    }
                } else {
                    currentIndex++;
                    pagedView();
                }
            } else if (e.keyCode === 37) { // Left Arrow
                if (currentIndex === 0) {
                    // Check if there is a previous post in the same category using WordPress post navigation
                    var prevPostUrl = $('.nav-previous a').attr('href');
                    if (prevPostUrl) {
                        window.location.href = prevPostUrl;
                    } else {
                        alert('First page reached');
                    }
                } else {
                    currentIndex--;
                    pagedView();
                }
            }
        });
    }

    var listView = function() {
        mangaImages.show();
        mangaPagination.empty();
        $(document).off('keydown');
    }

    // Retrieve the user's last view selection from localStorage
    var lastView = localStorage.getItem('mangaReaderView');
    if (lastView === 'list') {
        listViewFlag = true;
        listView();
        $('.manga-reader-view .list-view').addClass('active').siblings().removeClass('active');
    } else {
        pagedView();
        $('.manga-reader-view .paged-view').addClass('active').siblings().removeClass('active');
    }

    $('.manga-reader-view button').click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        if ($(this).hasClass('paged-view')) {
            listViewFlag = false;
            pagedView();
            // Store the user's view selection in localStorage
            localStorage.setItem('mangaReaderView', 'paged');
        } else {
            listViewFlag = true;
            listView();
            // Store the user's view selection in localStorage
            localStorage.setItem('mangaReaderView', 'list');
        }
    });

    mangaImages.click(function() {
        if (listViewFlag) {
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

$(window).on('load', function() {
    var mangaImages = $('.manga-reader .manga-images img');
    var totalPages = mangaImages.length;

    mangaImages.each(function(index) {
        var image = $(this);
        image.on('load', function() {
            var aspectRatio = image.width() / image.height();
            if (aspectRatio > 1) {
                image.addClass('landscape');
            } else {
                image.addClass('portrait');
            }
        });
    });

    // Add event listener for left arrow key
    $(document).on('keydown', function(e) {
        if (e.keyCode === 37) { // Left Arrow
            // Check if there is a previous post in the same category using WordPress post navigation
            var prevPostUrl = $('.nav-previous a').attr('href');
            if (prevPostUrl) {
                window.location.href = prevPostUrl;
            } else {
                alert('First page reached');
            }
        }
    });
});
