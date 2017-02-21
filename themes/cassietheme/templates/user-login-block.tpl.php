<?php

	include_once drupal_get_path('module', 'cassie').'/cassie.module';
	$login = set_text("Kirjaudu sisään", "войти");
	$register = set_text("Rekisteröinti", "регистрация");
	$adminLogin = set_text("Järjestelmänvalvojan kirjautuminen", "Войти Администратор");
?>
<div id="user-login-block-container">

  <div class="links">
    <a href="/login/"><?php echo $login ?></a><br> <a href="/registration"><?php echo $register ?></a><br> <a href="/user/login"><?php echo $adminLogin ?></a>
  </div>
</div>
