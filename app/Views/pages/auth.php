<?php 
use  app\bootstrap\views\views;
$response = $_SESSION['response'] ?? null;
require_once ROOT . '/app/bootstrap/views/Views.php';
require_once ROOT . '/app/views/partials/header.php';
unset($_SESSION['response']);
?>


<?php if(isset($response['message'])) Views::getAlert(message:$response['message']);?>

<div class="container" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-80%);">
<form action="/auth" method="POST">
  <div class="form-group">
    <label for="exampleInputEmail1">Логин</label>
    <input type="email" class="form-control" id="exampleInputEmail1" name="login" placeholder="Введите логин" value="<?= isset($response['login']) ? $response['login'] : "" ?>">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Пароль</label>
    <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Введите пароль">
  </div>

  <button type="submit" style="display:block;margin: 0 auto;padding-left:30px;padding-right:30px;" class="btn btn-primary">Войти</button>
</form>
</div>
<?php require_once ROOT . '/app/views/partials/footer.php';?>