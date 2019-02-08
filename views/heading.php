<<?= $view->values()->get("$form.heading.tag", 'h3') ?> <?= $view->values("$form.heading.style", $default) ?>>
    <?php if ($view->values("$form.heading.link")): ?>
        <a class='uk-link-reset' href="#<?= $app->filter($title, 'slugify') ?>"><?= $title ?></a>
    <?php else: ?>
        <span><?= $title ?></span>
    <?php endif ?>
</<?= $view->values()->get("$form.heading.tag", 'h3') ?>>