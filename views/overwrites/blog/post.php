<?php $view->script('post', 'blog:app/bundle/post.js', 'vue') ?>

<article id="<?= $app->filter($post->title, 'slugify') ?>" class="uk-article">

    <?php if ($image = $post->get('image.src')): ?>
    <a href="<?= $view->url('@blog/id', ['id' => $post->id]) ?>">
        <img  data-src="<?= $view->url($post->get('image.src')) ?>" data-alt="<?= $post->get('image.alt') ?>" uk-img>
    </a>
    <?php endif ?>

    <h1 class="uk-article-title">
        <span class="uk-link-heading" href="<?= $view->url('@blog/id', ['id' => $post->id]) ?>">
            <?= $post->title ?>
        </span>
    </h1>

    <p class="uk-article-meta">
        <?= __('Written by %name% on %date%', ['%name%' => $this->escape($post->user->name), '%date%' => '<time datetime="'.$post->date->format(\DateTime::ATOM).'" v-cloak>{{ "'.$post->date->format(\DateTime::ATOM).'" | date "longDate" }}</time>' ]) ?>
    </p>

    <div><?= $post->excerpt ?: $post->content ?></div>

    <?= $view->render('blog/comments.php') ?>

</article>
