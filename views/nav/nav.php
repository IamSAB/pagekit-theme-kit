<ul
    <?= $view->values()->class("$form.nav accordion ? true:style,center,parentIcon false:style,center", 'uk-nav') ?>
    <?= $view->values()->attr('uk-nav', "$form.nav accordion ? true:multiple", false) ?>
    >

    <?php foreach($root->getChildren() as $node) : ?>
        <?php $view->values()->useNode($node) ?>
        <?= $view->render('theme-kit/nav/li.php', ['node' => $node]) ?>
    <?php endforeach ?>

    <?php $view->values()->useNode() ?>

</ul>
