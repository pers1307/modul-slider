<? if(!empty($sliders)): ?>
    <div class="mainSliderWrap">
        <div class="mainSlider">
            <? foreach($sliders as $slider): ?>
                <?
                $imageUrl = MSFiles::getImageUrl($slider['image'], 'min');

                if (empty($imageUrl)) {
                    continue;
                }
                ?>

                <div style="background-image: url('<?= $imageUrl  ?>');">
                    <div class="slideText">
                        <div>
                            <? if(!empty($slider['title'])): ?>
                                <div class="slideTextTitle"><?= $slider['title'] ?></div>
                            <? endif; ?>

                            <? if(!empty($slider['text'])): ?>
                                <div class="slideTextContent">
                                    <?= $slider['text'] ?>
                                </div>
                            <? endif; ?>

                            <? if(!empty($slider['url'])): ?>
                                <a href="<?= $slider['url'] ?>" class="button">
                                    <span>подробнее</span>
                                </a>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>