<div id="previewImages" class="container">
    <div id="countImage" class="mb-2"></div>
    <div class='row mb-3'>
        <?= createCardImages($response['imgs']) ?>
    </div>
</div>


<?php 

function createCardImages($images) {
    $html = '';
    $colEl = '';

    foreach($images as $i => $img) {
     $imgEl = createImgEl($img);
     $colEl = createColEl($imgEl, $i);
     $html .= $colEl;
    }

    return $html;
}  

function createColEl($img,$id) {
    return "<div data-id='{$id}' class='col-3 card-preview mb-3'>{$img}</div>";
}

function createImgEl($img) {
    return "<img src={$img} class='img-thumbnail'/>";
}

?>