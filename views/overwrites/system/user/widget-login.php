<?php if ($user->isAuthenticated()): ?>

<p>
    <?= __('Welcome. Your are logged in as %username%', ['%username%' => $user->username]) ?>
</p>
<a class="uk-button" href="<?= $view->url('@user/logout', ['redirect' => $redirect]) ?>">
    <?= __('Logout') ?>
</a>

<?php else: ?>

<form class="uk-form" action="<?= $view->url('@user/authenticate') ?>" method="post" uk-margin>

    <div class="uk-inline uk-width-1-1">
        <span class="uk-form-icon" uk-icon="icon: user"></span>
        <input class="uk-input" type="text" name="credentials[username]" value="<?= $this->escape($last_username) ?>" placeholder="<?= __('username') ?>">
    </div>

    <div class="uk-inline uk-width-1-1">
        <span class="uk-form-icon" uk-icon="icon: unlock"></span>
        <input class="uk-input" type="password" name="credentials[password]" value="" placeholder="<?= __('password') ?>">
    </div>

    <button class="uk-button uk-button-primary uk-width-1-1" type="submit"><?= __('Login') ?></button>

    <div>
        <label>
            <input class="uk-checkbox" type="checkbox" name="remember_me"> <?= __('Remember Me') ?>
        </label>
    </div>

    <div>
        <a class="uk-button uk-button-text" href="<?= $view->url('@user/resetpassword') ?>"><?= __('Forgot Password?') ?></a>
        <?php if ($app->module('system/user')->config('registration') != 'admin'): ?>
        <a class="uk-button uk-button-text" href="<?= $view->url('@user/registration') ?>"><?= __('Sign up') ?></a>
        <?php endif ?>
    </div>

    <input type="hidden" name="redirect" value="<?= $this->escape($redirect) ?>">

    <?php $view->token()->get() ?>

</form>

<?php endif; ?>
