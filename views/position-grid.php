<div
    <?= $view->values()->class("$form.grid") ?>
    <?= $view->values()->attr('uk-height-viewport', "$form.ukHeightViewport") ?>
    uk-grid
    >

    <?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->use($widget->theme) ?>

        <div <?= $view->values()->class(['grid', 'alignment']) ?>>
            <?= $view->render('theme-kit/card.php', ['form' => 'content', 'title' => $widget->title, 'content' => $widget->get('result')]) ?>
        </div>

    <?php endforeach ?>

    <?php $view->values()->useParams() ?>

</div>