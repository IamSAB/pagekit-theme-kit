<div class="uk-navbar-container uk-navbar-transparent">
    <div <?= $view->values()->class("$form.container", 'uk-container') ?>>
        <div <?= $view->values()->attr('uk-navbar', "$form.ukNavbar", false) ?>>
            <?= $content ?>
        </div>
    </div>
</div>
