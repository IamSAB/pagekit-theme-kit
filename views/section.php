<div
    <?= $view->values()->class(["$form.section:style,size,preserve", "$form.background"], 'uk-section') ?>
    <?= $view->values()->image("$form.section.image") ?>
    >
    <div <?= $view->values()->class("$form.container",'uk-container') ?>>
        <?= $content ?>
    </div>
</div>
