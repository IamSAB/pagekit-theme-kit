<?php foreach ($widgets as $widget) : ?>

    <?php $view->values()->useWidget($widget) ?>

    <div <?= $view->values()->class(['widget.visibility', 'widget.alignment', 'widget.inverse', 'widget.custom.class']) ?> <?= $view->values('widget.custom.tag') ?>>
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
