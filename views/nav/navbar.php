<ul class="uk-navbar-nav">

    <?php foreach($root->getChildren() as $node) : ?>

        <?php $view->values()->useNode($node) ?>

        <li <?= $node->get('active') ? 'class="uk-active"' : '' ?>>

            <a href="<?= $node->getUrl() ?>">
                <?php if ($view->values()->has('general.node.icon')): ?>
                    <span class="uk-margin-small-right" <?= $view->values()->icon('general.node.icon') ?>></span>
                <?php endif ?>
                <div>
                    <?= $node->title ?>
                    <?php if ($view->values()->has('general.node.subtitle')): ?>
                        <div class="uk-navbar-subtitle"><?= $view->values()->get('general.node.subtitle') ?></div>
                    <?php endif ?>
                </div>
            </a>

            <?php if ($node->hasChildren()) : ?>
                <div class="uk-navbar-dropdown">
                    <ul class="uk-nav uk-navbar-dropdown-nav">
                        <?php foreach($root->getChildren() as $node) : ?>
                            <?php $view->values()->useNode($node) ?>
                            <?= $view->render('theme-kit/nav/li.php', ['node' => $node]) ?>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

        </li>

    <?php endforeach ?>

    <?php $view->values()->useNode() ?>

</ul>
