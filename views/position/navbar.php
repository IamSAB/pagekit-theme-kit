<?php foreach ($widgets as $widget) : ?>

    <?php $view->values()->use($widget->theme) ?>

    <div <?= $view->values()->class('widget.text', 'uk-navbar-item') ?>>

        <?= $widget->get('result') ?>

    </div>

<?php endforeach ?>

<?php $view->values()->useParams() ?>
