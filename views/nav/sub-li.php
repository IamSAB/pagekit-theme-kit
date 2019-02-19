<li <?= $node->get('active') ? 'class="uk-active"' : '' ?>>

    <a href="<?= $node->getUrl() ?>">
            <?php if ($view->values()->has('general.node.icon')): ?>
                <span class="uk-margin-small-right" <?= $view->values()->icon('general.node.icon', 0.75) ?>></span>
            <?php endif ?>
        <?= $node->title ?>
    </a>

    <?php if ($node->hasChildren()) : ?>
    <ul>
        <?php foreach ($node->getChildren() as $child) : ?>
            <?php $view->values()->use($child->theme) ?>
            <?= $view->render('theme-kit/nav/sub-li.php', ['node' => $child]) ?>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

</li>
