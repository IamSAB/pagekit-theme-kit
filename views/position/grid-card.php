<div
    <?= $view->values()->class("$form.grid masonry ? true:masonryChildWidth,gutter false:childWidth,horizontal,vertical,gutter,divider,match") ?>
    <?= $view->values()->attr('uk-grid', "$form.grid masonry ? true:masonry,parallax false:parallax") ?>
    <?= $view->values()->attr('uk-height-viewport', "$form.grid viewportHeight ? true:offsetTop,offsetBottom", false) ?>
    >

    <?php foreach ($widgets as $widget) : ?>

        <?php $view->values()->use($widget->theme) ?>

        <div <?= $view->values()->class(['grid', 'alignment']) ?>>
            <?= $view->render('theme-kit/card.php', ['form' => 'content', 'title' => $widget->title, 'content' => $widget->get('result')]) ?>
        </div>

    <?php endforeach ?>

    <?php $view->values()->useParams() ?>

</div>
