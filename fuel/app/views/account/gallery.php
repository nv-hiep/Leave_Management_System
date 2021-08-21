<div id="outer_container">
    <div id="customScrollBox">
        <div class="icontainer">
            <div class="icontent">
                <h1>SIDE<span class="lightgrey">WAYS</span> <br /><span class="light"><span class="grey"><span class="s36">JQUERY FULLSCREEN IMAGE GALLERY</span></span></span></h1>
                <p>A simple, yet elegant fullscreen image gallery created with the jQuery framework and some simple CSS. <a href="http://manos.malihu.gr/sideways-jquery-fullscreen-image-gallery" target="_blank">Full post and download files.</a></p>
                <div id="toolbar"></div>
                <div class="clear"></div>

                <?php foreach ($photos as $img): ?>
                <?php $img_info = explode('.', $img); ?>
                <?php if (strpos($img_info[0],'_thumb') !== false): ?>
                <a href="<?php echo Uri::base().'gallery/'.str_replace('_thumb', '', $img); ?>" class="thumb_link">
                    <span class="selected"></span>
                    <img src="<?php echo Uri::base().'gallery/'.$img; ?>" title="<?php $img; ?>" alt="<?php $img; ?>" class="thumb" />
                </a>
                <?php endif; ?>
                <?php endforeach;?>

                <?php foreach ($photos as $img): ?>
                <?php $img_info = explode('.', $img); ?>
                <?php if (strpos($img_info[0],'_thumb') !== false): ?>
                <a href="<?php echo Uri::base().'gallery/'.str_replace('_thumb', '', $img); ?>" class="thumb_link">
                    <span class="selected"></span>
                    <img src="<?php echo Uri::base().'gallery/'.$img; ?>" title="<?php $img; ?>" alt="<?php $img; ?>" class="thumb" />
                </a>
                <?php endif; ?>
                <?php endforeach;?>

                <?php foreach ($photos as $img): ?>
                <?php $img_info = explode('.', $img); ?>
                <?php if (strpos($img_info[0],'_thumb') !== false): ?>
                <a href="<?php echo Uri::base().'gallery/'.str_replace('_thumb', '', $img); ?>" class="thumb_link">
                    <span class="selected"></span>
                    <img src="<?php echo Uri::base().'gallery/'.$img; ?>" title="<?php $img; ?>" alt="<?php $img; ?>" class="thumb" />
                </a>
                <?php endif; ?>
                <?php endforeach;?>

                <?php foreach ($photos as $img): ?>
                <?php $img_info = explode('.', $img); ?>
                <?php if (strpos($img_info[0],'_thumb') !== false): ?>
                <a href="<?php echo Uri::base().'gallery/'.str_replace('_thumb', '', $img); ?>" class="thumb_link">
                    <span class="selected"></span>
                    <img src="<?php echo Uri::base().'gallery/'.$img; ?>" title="<?php $img; ?>" alt="<?php $img; ?>" class="thumb" />
                </a>
                <?php endif; ?>
                <?php endforeach;?>

                <p class="clear"></p>
                <p>Created by <a href="http://astro.com" target="_blank">astro</a>.</p>
            </div>
        </div>
        <div id="dragger_container"><div id="dragger"></div></div>
    </div>
</div>
<div id="bg">
    <img src="<?php echo Uri::base().'gallery/'; ?>Universe_and_planets_digital_art_wallpaper_lucernarium.jpg" title="Supremus Lucernarium" id="bgimg" />
        <div id="preloader"><img src="<?php echo Uri::base().'assets\\img\\'; ?>ajax-loader_dark.gif" width="32" height="32" align="absmiddle" />LOADING...</div>
    <div id="arrow_indicator"><img src="<?php echo Uri::base().'assets\\img\\'; ?>sw_arrow_indicator.png" width="50" height="50"  /></div>
    <div id="nextimage_tip">Click for next image</div>
</div>