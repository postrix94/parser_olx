<form class="mt-3" action="/add_telegram" method="POST">
  <!-- row -->
<div class="row">
  <!-- col-6 -->
<div class="col-6">
  <div class="mb-3 text-center">
    <label for="name-zhk"><b>Название ЖК</b></label>
      <select id="name-zhk" name="name-zhk" class="form-control">
      <?php 
          if(isset($response['all_zhk'])) {
            foreach($response['all_zhk'] as $zhk) {
                echo "<option value='#{$zhk['value']}'>{$zhk['name_zhk']}</option>";
            }
          }
      ?>
      </select>
  </div>
</div>
  <!-- //col-6 -->
  <!-- col-6 -->
<div class="col-6">
  <div class="mb-3 text-center">
    <label for="district"><b>Район</b></label>
        <select multiple id="district" name="district[]" class="form-control">
         <?php 
          if(isset($response['all_district'])) {
            foreach($response['all_district'] as $district) {
                echo "<option value='#{$district['value']}'>{$district['name_district']}</option>";
            }
          }
         ?>
        </select>
  </div>
</div>
  <!-- \\col-6 -->
</div>
<!-- //row -->

  <div class="mb-3">
    <label for="telegramContent"><b>Телеграм</b></label>
    <textarea class="form-control" id="telegramContent" name="telegram_content" style="height: 200px;"><?= htmlspecialchars($response['textTelegram']) ?></textarea>
    <div id="imagesInput" class="d-none">
    </div>
    <div class="text-right">
        <button id="addTelegram" type="submit" class="mt-3 btn">
            <img src="/public/img/icons/telegram_icon.png"/>
        </button>
    </div>
  </div>
</form>
<?php if(isset($response['imgs'])) require_once APP . '/Views/partials/preview_images_telegram.php';?>
