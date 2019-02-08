<li <?= $node->get('active') ? 'class="uk-active"' : '' ?>>

    <a href="<?= $node->getUrl() ?>">
        <?php if (isset($node->theme['nav']['nav']['icon'])): ?>
            <span class="uk-margin-small-right uk-icon" uk-icon="icon: <?= $node->theme['nav']['nav']['icon'] ?>"></span>
        <?php endif ?>
        <?= $node->title ?>
    </a>

    <?php if ($node->hasChildren()) : ?>
    <ul <?= $level == 0 ? 'class="uk-nav-sub"' : '' ?>>
        <?php foreach ($node->getChildren() as $child) : ?>
            <?= $view->render('theme-kit/recursive-nav.php', ['node' => $child]) ?>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

</li>