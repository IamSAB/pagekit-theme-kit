<div <?= $view->values(["$form.card" => ['style', 'size']], 'uk-card') ?>>

    <?php if ($view->values()->has("$form.card.header")): ?>
        <div class="uk-card-header">
            <?= $view->values()->get("$form.card.header") ?>
        </div>
    <?php endif ?>

    <div class="uk-card-body">
        <?= $view->render('theme-kit/heading.php',['form' => $form, 'title' => $title, 'default' => 'uk-card-title']) ?>
        <p>
            <?= $content ?>
        </p>
    </div>

    <?php if ($view->values()->has("$form.card.footer")): ?>
        <div class="uk-card-footer">
            <?= $view->values()->get("$form.card.footer") ?>
        </div>
    <?php endif ?>

</div>