import htmlLoading from './htmlLoading.js';

function addMessageTelegram() {
  const addTelegramBtn = document.getElementById('addTelegram');
  addTelegramBtn.addEventListener('click', onClickAddMessageTelegram);
}

function onClickAddMessageTelegram(e) {
  const inputTelegramContent = document.getElementById('telegramContent');
  const telegramContent = inputTelegramContent.value.trim();

  if (telegramContent) {
    document.body.insertAdjacentHTML('afterbegin', htmlLoading);
  }
}

export { addMessageTelegram };
