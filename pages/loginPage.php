<form method="POST">
Логин <input name="login" type="text" required><br>
Пароль <input name="password" type="password" required><br>
<input type="hidden" name="token" value="<?=$token?>"> <br/>
<input name="submit" type="submit" value="Войти">
</form>