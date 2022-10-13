import { addMessageTelegram } from './addTelegram.js';
import htmlLoading from './htmlLoading.js';

document.addEventListener('DOMContentLoaded', (e) => {
  const containerImagesEl = document.getElementById('previewImages');
  const parsingStartBtn = document.getElementById('parsingStart');
  const allImageEl = document.getElementById('countImage');
  const addTelegramBtn = document.getElementById('addTelegram');

  if (addTelegramBtn) {
    addMessageTelegram();
  }

  if (parsingStartBtn)
    parsingStartBtn.addEventListener('click', onClickParsingStartBtn);

  if (allImageEl) {
    addValueInHiddenInputImages();
  }

  if (!containerImagesEl) return null;
  showCountImages();
  containerImagesEl.addEventListener('click', onClickRemoveImages);
});

function showCountImages() {
  const allImageEl = document.getElementById('countImage');
  const countImages = document.querySelectorAll('div[data-id]').length;
  allImageEl.innerHTML = `<b class='mb-2'>Всего ${countImages} шт.</b>`;
}

function addValueInHiddenInputImages() {
  const imagesInputEl = document.getElementById('imagesInput');
  const countImages = document.querySelectorAll('div[data-id]');
  let html = '';
  countImages.forEach((node) => {
    let link = node.firstChild.attributes.src.textContent;
    let inputHTML = `<input name="images[]" value="${link}"/>`;
    html += inputHTML;
  });

  imagesInputEl.innerHTML = html;
}

function onClickParsingStartBtn(e) {
  const inputEl = document.getElementById('getInformation');
  if (!inputEl.value.trim()) return;

  document.body.insertAdjacentHTML('afterbegin', htmlLoading);
}

function onClickRemoveImages(e) {
  const cardImage = e.target.attributes['data-id'] ? e.target : null;
  if (cardImage) {
    const rowEl = cardImage.parentElement;
    cardImage.remove();
    showCountImages();
    addValueInHiddenInputImages();
  }
}
