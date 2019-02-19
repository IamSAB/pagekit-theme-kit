<ul <?= $view->values()->class("$form.subnav.style", 'uk-subnav') ?>>

    <?php foreach ($root->getChildren() as $node) : ?>

        <?php $view->values()->use($node->theme) ?>

        <li <?= $node->get('active') ? 'class="uk-active"' : '' ?>>

            <a href="<?= $node->getUrl() ?>">
                <?php if ($view->values()->has('general.node.icon')): ?>
                    <span class="uk-margin-small-right" <?= $view->values()->icon('general.node.icon') ?>></span>
                <?php endif ?>
                <?= $node->title ?>
            </a>

            <?php if ($node->hasChildren()) : ?>
                <div uk-dropdown>
                    <ul class="uk-nav uk-dropdown-nav">
                        <?php foreach($root->getChildren() as $node) : ?>
                            <?php $view->values()->use($node->theme) ?>
                            <?= $view->render('theme-kit/nav/li.php', ['node' => $node]) ?>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

        </li>

    <?php endforeach ?>

    <?php $view->values()->useParams() ?>

</ul>
