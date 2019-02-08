<ul class="uk-nav <?= $classes ?>">

    <?php foreach($root->getChildren() as $node) : ?>

        <?php if (isset($node->theme['menu']['header'])): ?>
            <li class="uk-nav-header"><?= $node->theme['menu']['header'] ?></li>
        <?php endif ?>

        <li class="<?= $node->get('active') ? ' uk-active' : '' ?> <?= $node->hasChildren() ? ' uk-parent' : '' ?>">

            <a href="<?= $node->getUrl() ?>">
                <?php if (isset($node->theme['menu']['icon'])): ?>
                    <span class="uk-margin-small-right uk-icon" uk-icon="icon: <?= $node->theme['menu']['icon'] ?>"></span>
                <?php endif ?>
                <?= $node->title ?>
            </a>

            <?php if ($node->hasChildren()) : ?>
                <ul class="uk-nav-sub">
                    <?php foreach ($node->getChildren() as $child): ?>
                        <?= $view->tm()->recursiveNav($child) ?>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>

        </li>

        <?php if (isset($node->theme['menu']['divider']) && $node['theme']['divider']): ?>
            <li class="uk-nav-divider"></li>
        <?php endif ?>

    <?php endforeach ?>

</ul>