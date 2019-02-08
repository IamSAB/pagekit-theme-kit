<div <?= $view->values([$form => ['section', 'background']], 'uk-section') ?> <?= $view->values()->img("$form.image.src") ?>>
    <div <?= $view->values("$form.container",'uk-container') ?>>
        <?= $content ?>
    </div>
</div>
