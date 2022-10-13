<?php
include_once APP . '/Views/partials/header.php';
$response = $_SESSION['response'] ?? null;
unset($_SESSION['response']);

if(isset($response['message']) || $message) {
    require_once APP .'/Views/partials/alert.php';
}
?>

<div class="container-fluid">
    <form action="/" method="POST">
        <input id="getInformation"  name="link" class="form-control" style="margin-top:70px;" type="text" placeholder="Вставьте ссылку">
        <div class="form-check mt-3">
            <input name="savePhoto" class="form-check-input" type="checkbox" id="gridCheck">
            <label class="form-check-label" for="gridCheck">Скачать фото</label>
        </div>
        
        <button id="parsingStart" class="mt-3 btn btn-primary btn-md btn-block" type="submit">Получить</button>
    </form>

    <?php 
        if(isset($response['link'])) require_once APP .'/Views/partials/link_alert.php';
        if(isset($response['textTelegram'])) require_once APP .'/Views/partials/telegram_preview.php';
     ?>

    <form action="/logout" method="post" class="mt-5" style="text-align: center;">
        <button class="btn btn-warning" type="submit">Выйти</button>
    </form>
</div>

<?php
include_once APP . '/Views/partials/footer.php';
?>