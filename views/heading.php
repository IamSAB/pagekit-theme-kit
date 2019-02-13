<?php if ($view->values("$form.heading.show", true)): ?>
    <<?= $view->values("$form.heading.tag", 'h3') ?> <?= $view->values()->class("$form.heading.style", $default) ?>>
        <?php if ($view->values("$form.heading.link", false)): ?>
            <a class='uk-link-reset' href="#<?= $app->filter($title, 'slugify') ?>"><?= $title ?></a>
        <?php else: ?>
            <span><?= $title ?></span>
        <?php endif ?>
    </<?= $view->values("$form.heading.tag", 'h3') ?>>
<?php endif ?>