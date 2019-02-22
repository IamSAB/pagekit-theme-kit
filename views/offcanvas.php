<div id="<?= $id ?>" <?= $view->values()->attr('uk-offcanvas', "$form.offcanvas") ?>>
    <div class="uk-offcanvas-bar">
        <button class="uk-offcanvas-close" type="button" uk-close></button>
        <?= $content ?>
    </div>
</div>
