<div <?= $view->values()->class("$form.card:style,hover,size", 'uk-card') ?>>

    <?php if ($view->values()->has("$form.card.header")): ?>
        <div class="uk-card-header">
            <?= $view->values("$form.card.header") ?>
        </div>
    <?php endif ?>

    <div class="uk-card-body">
        <?= $view->render('theme-kit/heading.php',['form' => $form, 'title' => $title, 'default' => 'uk-card-title']) ?>
        <?= $content ?>
    </div>

    <?php if ($view->values()->has("$form.card.footer")): ?>
        <div class="uk-card-footer">
            <?= $view->values()->get("$form.card.footer") ?>
        </div>
    <?php endif ?>

</div>
