<div
    <?= $view->values()->class("$form.masonry:childWidth,gutter") ?>
    <?= $view->values()->attr('uk-grid', "$form.masonry.parallax", true, 'masonry:true;') ?>
    >

    <?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->useWidget($widget) ?>

        <div <?= $view->values()->class(['masonry.masonryItem', 'widget.text', 'widget.visibility'. 'widget.inverse', 'widget.custom']) ?> <?= $view->values('widget.custom.tag') ?>>
            <?= $view->render('theme-kit/card.php', [
                'form' => 'masonry',
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
