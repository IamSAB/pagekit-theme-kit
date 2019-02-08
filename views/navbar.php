<div class="uk-navbar-container <?= $overlay ? 'uk-position-absolute uk-position-z-index uk-navbar-transparent' : '' ?>">
    <div class="uk-container <?= $container ?>">
        <div <?= $view->tm()->attr('uk-navbar', $ukNavbar) ?>>
            <?= $content ?>
        </div>
    </div>
</div>