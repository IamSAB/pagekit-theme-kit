<article <?= $view->values()->class('general.inverse.style', 'uk-article') ?>>

    <?= $view->render('theme-kit/heading.php', [
        'form' => 'general',
        'title' => $page->title,
        'default' => 'uk-article-title'
    ]) ?>

    <?= $page->content ?>

</article>
