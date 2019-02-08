<ul class="uk-navbar-nav">

    <?php foreach($root->getChildren() as $node) : ?>

        <li class="<?= $node->get('active') ? ' uk-active' : '' ?>">

            <a href="<?= $node->getUrl() ?>">
                <?php if (isset($node->theme['menu']['icon'])): ?>
                    <span class="uk-margin-small-right uk-icon" uk-icon="icon: <?= $node->theme['menu']['icon'] ?>"></span>
                <?php endif ?>
                <div>
                    <?= $node->title ?>
                    <?php if (isset($node->theme['menu']['subtitle'])): ?>
                        <div class="uk-navbar-subtitle"><?= $node->theme['menu']['subtitle'] ?></div>
                    <?php endif ?>
                </div>
            </a>

            <?php if ($node->hasChildren()) : ?>
                <div class="uk-navbar-dropdown">
                    <?= $view->render('theme-kit/nav-nav.php', ['root' => $node, 'classes' => 'uk-navbar-dropdown-nav', 'ukNav' => '']) ?>
                </div>
            <?php endif ?>

        </li>

    <?php endforeach ?>

</ul>