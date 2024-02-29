(function ($, root, undefined) {

    $(window).on('load', function() { 

        var slideWrapper = $('.avb .avb-banners');

        if(slideWrapper.length > 0) {

            var iframes = slideWrapper.find('.embed-player'),
			firstSlide = slideWrapper.find('.avb-banner').first() ?? 6000,
            lazyImages = slideWrapper.find('.avb-banner__medium.image'),
            lazyCounter = 0;
            
            // Initialize
            slideWrapper.on('init', function(slick){
                slick = $(slick.currentTarget);
                setTimeout(function(){
                    playPauseVideo(slick,'play');
                }, 1000);
                resizePlayer(iframes, 16/9);
            });

            slideWrapper.on('beforeChange', function(event, slick) {
                slider = $(slick.$slider);
                playPauseVideo(slider, 'pause');
            });
        
            slideWrapper.on('afterChange', function(event, slick) {
                slider = $(slick.$slider);
                var wWidth = $('.avb .avb-banners').width();
                var action = wWidth >= 800 ? 'play' : 'pause';
                playPauseVideo(slider, action);

				var duration = slider.find('.slick-current').data('duration');
				console.log(duration);
				slider.slick('slickSetOption', 'autoplaySpeed', duration);
            });

            slideWrapper.on('lazyLoaded', function(event, slick, image, imageSource) {
                lazyCounter++;
                if (lazyCounter === lazyImages.length){
                    lazyImages.addClass('show');
                    slideWrapper.slick('slickPlay');
                }
            });

			console.log(firstSlide.data('duration'));

            //start the slider
            slideWrapper.slick({
                autoplaySpeed: firstSlide.data('duration'),
                lazyLoad: 'progressive',
                speed: 600,
                arrows: false,
                dots: true,
                rows: 0,
                cssEase: 'cubic-bezier(0.87, 0.03, 0.41, 0.9)'
            });

            // Resize event
            $(window).on('resize.slickVideoPlayer', function() { 
                resizePlayer(iframes, 16/9);
            });

        }

        $('.avb__down-arrow figure').on('click', function(evt) {
            var destination = $('.flexible__content').offset().top;

            $("html:not(:animated),body:not(:animated)").animate({
                scrollTop: destination
            }, 800);
        });

        formDistance();

        $(window).on('resize', function(){
            formDistance();
        });

    });

    // POST commands to YouTube or Vimeo API
    function postMessageToPlayer(player, command) {
        if (player == null || command == null) return;
        player.contentWindow.postMessage(JSON.stringify(command), '*');
    }

    // When the slide is changing
    function playPauseVideo(slick, control) {
        var currentSlide, slideType, startTime, player, video;

        currentSlide = slick.find('.slick-current');
        slideType = currentSlide.data('type');
        player = currentSlide.find('iframe').get(0);
        startTime = currentSlide.data('video-start');

        if (slideType === 'avb_vimeo') {
            switch (control) {
                case 'play':
                    if ((startTime != null && startTime > 0 ) && !currentSlide.hasClass('started')) {
                        console.log(startTime);
                    currentSlide.addClass('started');
                    postMessageToPlayer(player, {
                        'method': 'setCurrentTime',
                        'value' : startTime
                    });
                    }
                    postMessageToPlayer(player, {
                        'method': 'play',
                        'value' : 1
                    });
                    break;
                case 'pause':
                    postMessageToPlayer(player, {
                        'method': 'pause',
                        'value': 1
                    });
                    break;
            }
        } else if (slideType === 'avb_youtube') {
            switch (control) {
                case 'play':
                    postMessageToPlayer(player, {
                        'event': 'command',
                        'func': 'mute'
                    });
                    postMessageToPlayer(player, {
                        'event': 'command',
                        'func': 'playVideo'
                    });
                    break;
                case 'pause':
                    postMessageToPlayer(player, {
                        'event': 'command',
                        'func': 'pauseVideo'
                    });
                    break;
            }
        } else if (slideType === 'avb_html_video') {
            video = currentSlide.find('video').get(0);
            if (video != null) {
                if (control === 'play'){
                    video.play();
                } else {
                    video.pause();
                }
            }
        }

    }

    // Resize player
    function resizePlayer(iframes, ratio) {
        if (!iframes[0]) return;
        var win = $('.avb .avb-banners'),
            width = win.width(),
            playerWidth,
            height = win.height(),
            playerHeight,
            ratio = ratio || 16/9;

        iframes.each(function(){
            var current = $(this);
            if (width / ratio < height) {
            playerWidth = Math.ceil(height * ratio);
            current.width(playerWidth).height(height).css({
                left: (width - playerWidth) / 2,
                top: 0
                });
            } else {
            playerHeight = Math.ceil(width / ratio);
            current.width(width).height(playerHeight).css({
                left: 0,
                top: (height - playerHeight) / 2
            });
            }
        });
    }

    function formDistance() {

        var windowWidth = $(window).width();

        if(windowWidth <= 1000) {
            var avbBannerHeight = $('.avb-banner').height();
            var captionHeight = $('.avb-banner__caption').height();
            var marginTop = (avbBannerHeight - captionHeight) - 50;
            $('#atm_form').css({marginTop: -(marginTop)+'px'});
        } else { 
            $('#atm_form').css({marginTop: '0'});
        }

    }


})(jQuery, this);