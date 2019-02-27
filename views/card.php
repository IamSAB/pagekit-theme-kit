<div <?= $view->values()->class("$form.card:style,hover,size", 'uk-card') ?>>

    <?php if ($view->values()->has("$form.card.header")): ?>
        <div class="uk-card-header">
            <?= $view->values()->content("$form.card.header") ?>
        </div>
    <?php endif ?>

    <div class="uk-card-body">
        <?= $heading ?>
        <?= $content ?>
    </div>

    <?php if ($view->values()->has("$form.card.footer")): ?>
        <div class="uk-card-footer">
            <?= $view->values()->content("$form.card.footer") ?>
        </div>
    <?php endif ?>

</div>
