<div
    <?= $view->values()->class("$form.grid") ?>
    uk-grid
    >

    <?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->use($widget->theme) ?>

        <div
            <?= $view->values()->class(['tilePosition.tile:style,padding', 'alignment', 'tilePosition.background']) ?>
            <?= $view->values()->image("$form.tile.image") ?>
            >
            <?= $view->render('theme-kit/heading.php', ['title' => $widget->title]) ?>
            <?= $widget->get('result') ?>
        </div>

    <?php endforeach ?>

    <?php $view->values()->useParams() ?>

</div>
