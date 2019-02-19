<div class="uk-cover-container" <?= $view->values()->attr('uk-height-viewport', "$form.ukHeightViewport") ?>>

    <?php
        $parts = explode(':', $view->values("$form.cover.ration", '16:9'));
        $canvas = "width=\"$parts[0]\" height=\"$parts[1]\"";
    ?>
    <canvas <?= $canvas ?>></canvas>

    <?php switch ($view->values("$form.cover.type")):

        case 'image': ?>
        <img <?= $view->values()->image("$form.cover.image") ?> uk-cover>
        <?php break;

        case 'video': ?>
        <video <?= $view->values()->video("$form.cover.video") ?> uk-cover></video>
        <?php break;

        case 'iframe': ?>
        <iframe <?= $view->values()->src("$form.cover.iframe") ?> uk-cover></iframe>

    <?php endswitch ?>

    <?= $content ?>

</div>
