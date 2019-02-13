<div class="uk-navbar-container <?= $view->values('menu.overlay') ? 'uk-position-absolute uk-position-z-index uk-navbar-transparent' : '' ?>">
    <div <?= $view->values()->class('menu.container', 'uk-container') ?>>
        <div <?= $view->values()->attr('menu.uk-navbar') ?>>
            <?= $content ?>
        </div>
    </div>
</div>