<ul class="uk-subnav <?= $classes ?>" uk-margin>

    <?php foreach ($root->getChildren() as $node) : ?>

        <li class="<?= $node->get('active') ? ' uk-active' : '' ?>">

            <a href="<?= $node->getUrl() ?>">
                <?php if ($node->theme['menu']['icon']): ?>
                    <span class="uk-margin-small-right uk-icon" uk-icon="icon: <?= $node->theme['menu']['icon'] ?>"></span>
                <?php endif ?>
                <?= $node->title ?>
            </a>

            <?php if ($node->hasChildren()) : ?>
                <div <?= $view->tm()->attr('uk-dropdown', $ukDropdown) ?>>
                    <?= $view->render('theme-core/nav-nav.php', ['root' => $node, 'classes' => 'uk-dropdown-nav']) ?>
                </div>
            <?php endif ?>

        </li>

    <?php endforeach ?>

</ul>