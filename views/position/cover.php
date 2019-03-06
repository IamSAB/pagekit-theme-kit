<?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->useWidget($widget) ?>

        <div <?= $view->values()->class(["$form.position", "$form.overlay", 'widget.text', 'widget.visibility', 'widget.inverse'], 'uk-overlay uk-position-absolute') ?>>
            <?= $view->render('theme-kit/heading.php', [
                'form' => 'widget',
                'title' => $widget->title,
                'default' => 'uk-card-title'
            ]) ?>
            <?= $widget->get('result') ?>
        </div>

<?php endforeach ?>

<?php $view->values()->useNode() ?>
