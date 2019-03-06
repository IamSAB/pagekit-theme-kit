<div
    <?= $view->values()->class("$form.grid:!parallax,viewportHeight,offsetTop,offsetBottom") ?>
    <?= $view->values()->attr('uk-grid', "$form.grid.parallax") ?>
    <?= $view->values()->attr('uk-height-viewport', "$form.grid viewportHeight ? true:offsetTop,offsetBottom", false) ?>
    >

    <?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->useWidget($widget) ?>

        <div <?= $view->values()->class(['grid.gridItem', 'widget.text', 'widget.visibility', 'widget.inverse', 'widget.custom.class']) ?> <?= $view->values('widget.custom.tag') ?>>
            <?= $view->render('theme-kit/card.php', [
                'form' => 'grid',
                'heading' => $view->render('theme-kit/heading.php', [
                    'form' => 'widget',
                    'title' => $widget->title,
                    'default' => 'uk-card-title'
                ]),
                'content' => $widget->get('result')
            ]) ?>
        </div>

    <?php endforeach ?>

    <?php $view->values()->useNode() ?>

</div>
