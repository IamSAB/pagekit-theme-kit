<div class="uk-cover-container uk-height-large">

    <?php if ($type == 'img'): ?>
        <img <?= $src ? sprintf('data-src="%s" uk-img', $view->url($src)): '' ?> uk-cover>
    <?php elseif ($type == 'video'): ?>
        <video src="<?= $src ?>" uk-cover></video>
    <?php  elseif ($type == 'iframe'): ?>
        <iframe src="<?= $src ?>" uk-cover></iframe>
    <?php endif; ?>

    <?php foreach ($widgets as $widget) : ?>

        <div class="uk-overlay <?= $widget->theme['hero']['classes'] ?> <?= $widget->theme['hero']['custom'] ?>">

            <?= $view->tm()->heading($widget->title, $widget->theme['heading'], 'uk-h3') ?>

            <p>
                <?= $widget->get('result') ?>
            </p>

        </div>

    <?php endforeach ?>

</div>