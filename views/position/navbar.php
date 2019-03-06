<?php foreach ($widgets as $widget) : ?>

    <?php $view->values()->useWidget($widget) ?>

    <div <?= $view->values()->class('widget.text', 'uk-navbar-item') ?>>

        <?= $widget->get('result') ?>

    </div>

<?php endforeach ?>

<?php $view->values()->useNode() ?>
