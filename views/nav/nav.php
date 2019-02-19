<ul <?= $view->values()->class("$form.nav", 'uk-nav') ?> <?= $view->values()->attr('uk-nav', "$form.ukNav") ?>>

    <?php foreach($root->getChildren() as $node) : ?>
        <?php $view->values()->use($node->theme) ?>
        <?= $view->render('theme-kit/nav/li.php', ['node' => $node]) ?>
    <?php endforeach ?>

    <?php $view->values()->useParams() ?>

</ul>
