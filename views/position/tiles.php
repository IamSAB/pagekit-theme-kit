<div <?= $view->values()->class("$form.tiles", 'uk-grid-collapse') ?> uk-grid uk-height-match="target: > div > .uk-tile" >

    <?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->useWidget($widget) ?>

        <div
            <?= $view->values()->class('widget.text', 'widget.visibility', 'widget.inverse', 'widget.custom.class') ?>
            <?= $view->values('widget.custom.tag') ?>
        >
            <div
                <?= $view->values()->class(["$form.tile:style,padding", "$form.background"], 'uk-tile') ?>
                <?= $view->values()->image("$form.tile.image") ?>
            >
                <?= $view->render('theme-kit/heading.php', ['title' => $widget->title, 'form' => 'tile', 'default' => 'uk-card-title']) ?>
                <?= $widget->get('result') ?>
            </div>
        </div>


    <?php endforeach ?>

    <?php $view->values()->useNode() ?>

</div>
