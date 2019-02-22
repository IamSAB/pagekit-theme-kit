<div uk-slideshow>
    <ul class="uk-slideshow-items">
        <li>
            <img <?= $view->values()->image() ?> uk-cover>

            <?php foreach ($widgets as $widget) : ?>

                <?php $view->values()->use($widget->theme) ?>

                <div <?= $view->values()->class(['position', 'alignment']) ?>>
                    <?= $view->render('theme-kit/heading.php', ['title' => $widget->title]) ?>
                    <?= $widget->get('result') ?>
                </div>

            <?php endforeach ?>

            <?php $view->values()->useParams() ?>
        </li>
    </ul>
</div>
