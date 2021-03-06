<?php if ($view->values()->has('general.node.header')): ?>
<li class="uk-nav-header">
    <?= $view->values()->get('general.node.header') ?>
</li>
<?php endif ?>

<li class="<?= $node->get('active') ? 'uk-active' : '' ?><?= $node->hasChildren() ? ' uk-parent' : '' ?>">

    <a href="<?= $node->getUrl() ?>">
        <?php if ($view->values()->has('general.node.icon')): ?>
            <span class="uk-margin-small-right" <?= $view->values()->icon('general.node.icon') ?>></span>
        <?php endif ?>
        <?= $node->title ?>
    </a>

    <?php if ($node->hasChildren()) : ?>
        <ul class="uk-nav-sub">
            <?php foreach ($node->getChildren() as $child): ?>
                <?php $view->values()->useNode($child) ?>
                <?= $view->render('theme-kit/nav/sub-li.php', ['node' => $child, 'level' => 0]) ?>
            <?php endforeach ?>
            <?php $view->values()->useNode() ?>
        </ul>
    <?php endif ?>

</li>

<?php if ($view->values()->get('general.node.divider', false)): ?>
<li class="uk-nav-divider"></li>
<?php endif ?>
