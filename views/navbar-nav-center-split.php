<?php

    use Pagekit\Site\Model\Node;

    // split menu in two roots
    $nodes = $root->getChildren();
    $chunks = array_chunk($nodes, ceil(count($nodes)/2));
    $left = Node::create()->addAll($chunks[0]);
    $right = Node::create()->addAll($chunks[1]);
?>

<div class="uk-navbar-center">

    <div class="uk-navbar-center-left">
        <div>
            <?= $view->render('theme-kit/navbar-nav.php', ['root' => $left]) ?>
        </div>
    </div>

    <div class="uk-navbar-item">
        <?= $content ?>
    </div>

    <div class="uk-navbar-center-right">
        <div>
            <?= $view->render('theme-kit/navbar-nav.php', ['root' => $right]) ?>
        </div>
    </div>

</div>