<div <?= $view->values("$form.grid") ?> uk-grid>

    <?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->use($widget->theme) ?>

        <div <?= $view->values(['grid', 'alignment']) ?>>
            <?= $view->render('theme-kit/card.php', ['form' => 'content', 'title' => $widget->title, 'content' => $widget->get('result')]) ?>
        </div>

    <?php endforeach ?>

    <?php $view->values()->reset() ?>

</div>