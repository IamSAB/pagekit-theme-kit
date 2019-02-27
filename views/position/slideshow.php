<div class="uk-position-relative uk-visible-toggle" <?= $view->values()->attr('uk-slideshow', "$form.slideshow:animation,autoplay,ratio,minHeight,maxHeight") ?>>
    <ul class="uk-slideshow-items">
        <?php foreach ($widgets as $widget) : ?>
        <li>
            <?php $view->values()->use($widget->theme) ?>

            <?php switch ($view->values("$form.slideshowItem.type")):

                case 'image': ?>
                <img <?= $view->values()->image("$form.slideshowItem.image") ?> uk-cover>
                <?php break;

                case 'video': ?>
                <video <?= $view->values()->video("$form.slideshowItem.video") ?> uk-cover></video>
                <?php break;

                case 'iframe': ?>
                <iframe <?= $view->values()->src("$form.slideshowItem.iframe") ?> uk-cover></iframe>

            <?php endswitch ?>

            <div <?= $view->values()->class(["$form.position", "$form.overlay", 'widget.text', 'widget.visibility', 'widget.inverse'], 'uk-overlay uk-position-absolute') ?>>
                <?= $view->render('theme-kit/heading.php', [
                    'form' => 'widget',
                    'title' => $widget->title,
                    'default' => 'uk-card-title'
                ]) ?>
                <?= $widget->get('result') ?>
            </div>

        </li>
        <?php endforeach ?>
        <?php $view->values()->useParams() ?>
    </ul>
    <div class="uk-light">
        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
    </div>
</div>
