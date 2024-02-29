<?php
/**
 * Video
 */
$platform = get_sub_field('platform');
$video_id = get_sub_field('video_id');
$still_image = get_sub_field('still_image');

switch ($platform) {
    case 'youtube':
        $url_embed = 'https://www.youtube.com/embed/' . $video_id . '?rel=0&amp;showinfo=0';
        $url_play = 'https://www.youtube.com/watch?v=' . $video_id;
        break;
    
    default:
        $url_embed = 'https://player.vimeo.com/video/' . $video_id;
        $url_play = 'https://vimeo.com/' . $video_id;
        break;
}
?>
<div class="video-wrapper">
    <?php
        if($still_image):
            $still_image_url = vt_resize($still_image, '', 800, 450, true);
    ?>
        <figure class="video-pop">
            <a href="<?php echo $url_play; ?>">
                <span>
                    <i class="fa-regular fa-play"></i>
                </span>
                <img src="<?php echo $still_image_url['url']; ?>" />
            </a>
        </figure>

    <?php else: ?>
        <div class="video-responsive">
            <iframe width="100%" height="100%" src="<?php echo $url_embed; ?>" frameborder="0" allowfullscreen></iframe>
        </div><!-- video-responsive -->
    <?php endif; ?>
</div><!-- video-wrapper -->
