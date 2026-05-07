(function () {
  'use strict';

  var COUNTER_ID = 75081295;
  var ENDPOINT_URL = '/ajax-lead';

  function ym(goal) {
    if (typeof window.ym === 'function') {
      window.ym(COUNTER_ID, 'reachGoal', goal);
    }
  }

  // === Маска телефона: +7 (___) ___-__-__ ===
  // Логика: всегда форматируем 11 цифр в формат +7 (XXX) XXX-XX-XX.
  // Первая цифра кода (после +7) не может быть 8 — это распространённая ошибка
  // (когда пользователь вводит 8 9XX забывая что +7 уже стоит).
  function formatPhoneInput(rawValue, prevValue) {
    var digits = (rawValue || '').replace(/\D/g, '');
    // Если человек начал с 8 → меняем на 7 (форма мысли «8 9XX...»)
    if (digits.length === 1 && digits === '8') digits = '7';
    if (digits.length > 0 && digits[0] === '8') digits = '7' + digits.slice(1);
    // Если первая не 7 — добавляем 7 в начало (всегда +7)
    if (digits.length > 0 && digits[0] !== '7') digits = '7' + digits;
    // Блокируем 8 как первую цифру кода (после +7)
    if (digits.length >= 2 && digits[1] === '8') {
      digits = '7' + digits.slice(2); // удаляем 8, остальное сдвигается
    }
    digits = digits.slice(0, 11);

    if (digits.length === 0) return '';

    var out = '+7';
    if (digits.length > 1) out += ' (' + digits.slice(1, 4);
    if (digits.length >= 4) out += ')';
    if (digits.length > 4) out += ' ' + digits.slice(4, 7);
    if (digits.length > 7) out += '-' + digits.slice(7, 9);
    if (digits.length > 9) out += '-' + digits.slice(9, 11);
    return out;
  }

  function attachPhoneMask(input) {
    if (!input) return;
    input.setAttribute('inputmode', 'tel');
    // При фокусе на пустое поле — сразу показываем "+7 ("
    input.addEventListener('focus', function () {
      if (!input.value) input.value = '+7 (';
    });
    input.addEventListener('blur', function () {
      if (input.value === '+7 (' || input.value === '+7') input.value = '';
    });
    input.addEventListener('input', function () {
      var formatted = formatPhoneInput(input.value);
      if (input.value !== formatted) input.value = formatted;
    });
    // Запретить вставку нечисленного мусора через paste (но разрешить копию номера в любом виде)
    input.addEventListener('paste', function (e) {
      e.preventDefault();
      var text = (e.clipboardData || window.clipboardData).getData('text');
      input.value = formatPhoneInput(text);
    });
  }

  // Маска для телефонов в обеих формах
  attachPhoneMask(document.getElementById('ObrFormPhone'));
  attachPhoneMask(document.getElementById('ObrMiniFormPhone'));

  var modal = document.getElementById('MdGlNew');
  if (!modal) return;

  var form        = document.getElementById('ObrLeadForm');
  var titleEl     = document.getElementById('MdGlNewTitle');
  var subtitleEl  = document.getElementById('MdGlNewSubtitle');
  var sourceEl    = document.getElementById('ObrFormSource');
  var pageTitleEl = document.getElementById('ObrFormPageTitle');
  var pageUrlEl   = document.getElementById('ObrFormPageUrl');
  var nameEl      = document.getElementById('ObrFormName');
  var phoneField  = document.getElementById('ObrFormPhoneField');
  var phoneEl     = document.getElementById('ObrFormPhone');
  var emailField  = document.getElementById('ObrFormEmailField');
  var emailEl     = document.getElementById('ObrFormEmail');
  var submitBtn   = document.getElementById('ObrFormSubmit');
  var errorEl     = document.getElementById('ObrFormError');
  var successEl   = document.getElementById('ObrFormSuccess');
  var successMsg  = document.getElementById('ObrFormSuccessMsg');
  var consentEl   = document.getElementById('ObrFormConsent');

  modal.addEventListener('show.bs.modal', function (event) {
    resetForm();
    var trigger = event.relatedTarget;
    if (!trigger) return;

    var titleText    = trigger.getAttribute('data-form-title')    || 'Задать вопрос';
    var subtitleText = trigger.getAttribute('data-form-subtitle') || 'Менеджер свяжется с вами в течение 15 минут (рабочие часы)';
    var sourceText   = trigger.getAttribute('data-form-source')   || trigger.getAttribute('data-marker') || 'Общий CTA';

    titleEl.textContent    = titleText;
    subtitleEl.textContent = subtitleText;
    sourceEl.value         = sourceText;
    pageTitleEl.value      = (document.title || '').replace(/ \| ОбрПрофи.*$/, '');
    pageUrlEl.value        = window.location.href;

    ym('form_open');
  });

  var firstFocusFired = false;
  form.addEventListener('focusin', function () {
    if (!firstFocusFired) {
      firstFocusFired = true;
      ym('form_focus');
    }
  });

  // Поддержка и радио, и select
  var channelInputs = form.querySelectorAll('[name="channel"]');
  channelInputs.forEach(function (input) {
    input.addEventListener('change', function () {
      updateChannelUI(form.elements.channel.value);
    });
  });

  function updateChannelUI(channel) {
    if (channel === 'email') {
      emailField.classList.add('obr-form__field--shown');
      emailEl.required = true;
      phoneEl.required = false;
      phoneField.querySelector('input').placeholder = 'Телефон (необязательно)';
    } else {
      emailField.classList.remove('obr-form__field--shown');
      emailEl.required = false;
      phoneEl.required = true;
      phoneField.querySelector('input').placeholder = 'Телефон';
    }
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (submitBtn.disabled) return;

    clearError();

    if (!consentEl.checked) {
      showError('Подтвердите согласие на обработку персональных данных');
      return;
    }

    var channel = form.elements.channel.value || 'phone';

    if (channel === 'email') {
      var email = (emailEl.value || '').trim();
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError('Укажите корректный email');
        emailField.classList.add('obr-form__field--error');
        return;
      }
    } else {
      var phoneDigits = (phoneEl.value || '').replace(/\D+/g, '');
      if (phoneDigits.length < 10) {
        showError('Укажите корректный телефон (минимум 10 цифр)');
        phoneField.classList.add('obr-form__field--error');
        return;
      }
    }

    submitBtn.disabled = true;
    submitBtn.classList.add('obr-form__submit--loading');

    var fd = new FormData(form);

    fetch(ENDPOINT_URL, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin'
    })
      .then(function (r) { return r.json().catch(function () { return null; }); })
      .then(function (data) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('obr-form__submit--loading');

        if (!data) {
          showError('Не удалось отправить. Попробуйте позже или позвоните.');
          return;
        }

        if (data.ok) {
          ym('form_submit');
          var ch = channel;
          if (ch === 'phone') ym('form_channel_phone');
          else if (ch === 'email') ym('form_channel_email');
          else ym('form_channel_messenger');

          if (data.test_mode) {
            successMsg.textContent = '[Локальный тест] Лид залоггирован, в B24 не отправлен. На проде уйдёт менеджеру.';
          }
          form.style.display = 'none';
          if (titleEl) titleEl.style.display = 'none';
          if (subtitleEl) subtitleEl.style.display = 'none';
          successEl.hidden = false;
          return;
        }

        var err = data.error || 'unknown';
        var msg = {
          invalid_phone: 'Укажите корректный телефон',
          invalid_email: 'Укажите корректный email',
          rate_limit:    'Заявка с этого номера уже отправлена. Подождите минуту.',
          b24_unavailable: 'Сервис временно недоступен. Попробуйте через минуту или позвоните.',
          b24_rejected:  'Не удалось обработать заявку. Позвоните, пожалуйста.',
          config_missing: 'Сайт не настроен. Сообщите администратору.',
        }[err] || 'Что-то пошло не так. Попробуйте ещё раз.';
        showError(msg);
      })
      .catch(function () {
        submitBtn.disabled = false;
        submitBtn.classList.remove('obr-form__submit--loading');
        showError('Нет связи с сервером. Проверьте интернет.');
      });
  });

  function showError(text) {
    errorEl.textContent = text;
    errorEl.classList.add('obr-form__error--shown');
  }
  function clearError() {
    errorEl.textContent = '';
    errorEl.classList.remove('obr-form__error--shown');
    form.querySelectorAll('.obr-form__field--error').forEach(function (el) {
      el.classList.remove('obr-form__field--error');
    });
  }
  function resetForm() {
    if (form.style.display === 'none') {
      form.style.display = '';
      if (titleEl) titleEl.style.display = '';
      if (subtitleEl) subtitleEl.style.display = '';
      successEl.hidden = true;
    }
    form.reset();
    clearError();
    firstFocusFired = false;
    updateChannelUI('phone');
  }

  // === Логика модалки согласия (вторая модалка) ===
  var consentModal = document.getElementById('ObrFormConsentModal');
  if (consentModal) {
    var consentBody   = document.getElementById('ObrFormConsentBody');
    var consentHint   = document.getElementById('ObrFormConsentHint');
    var consentAccept = document.getElementById('ObrFormConsentAccept');
    var consentDecline= document.getElementById('ObrFormConsentDecline');
    var consentCheckbox = document.getElementById('ObrFormConsent');

    // При открытии — сбросить скролл и показать подсказку, если контент скроллится
    consentModal.addEventListener('shown.bs.modal', function () {
      if (consentBody) {
        consentBody.scrollTop = 0;
        // Если контент длиннее видимой области — показать подсказку
        var canScroll = consentBody.scrollHeight > consentBody.clientHeight + 4;
        if (consentHint) {
          consentHint.classList.toggle('obr-consent__hint--hidden', !canScroll);
        }
      }
    });

    // Скрыть подсказку при докрутке до низа
    if (consentBody && consentHint) {
      consentBody.addEventListener('scroll', function () {
        var atBottom = consentBody.scrollHeight - consentBody.scrollTop - consentBody.clientHeight < 16;
        consentHint.classList.toggle('obr-consent__hint--hidden', atBottom);
      });
    }

    if (consentAccept) {
      consentAccept.addEventListener('click', function () {
        if (consentCheckbox) consentCheckbox.checked = true;
        var inst = bootstrap.Modal.getInstance(consentModal);
        if (inst) inst.hide();
      });
    }
    if (consentDecline) {
      consentDecline.addEventListener('click', function () {
        if (consentCheckbox) consentCheckbox.checked = false;
        var inst = bootstrap.Modal.getInstance(consentModal);
        if (inst) inst.hide();
      });
    }
  }

  // === Микроформа на hero ===
  var miniForm = document.getElementById('ObrMiniFormForm');
  if (miniForm) {
    var miniWrap     = document.getElementById('ObrMiniForm');
    var miniOpenAt   = document.getElementById('ObrMiniFormOpenAt');
    var miniPhone    = document.getElementById('ObrMiniFormPhone');
    var miniConsent  = document.getElementById('ObrMiniFormConsent');
    var miniSubmit   = document.getElementById('ObrMiniFormSubmit');
    var miniError    = document.getElementById('ObrMiniFormError');
    var miniSuccess  = document.getElementById('ObrMiniFormSuccess');

    // timestamp при загрузке — для проверки "слишком быстрая отправка = бот"
    miniOpenAt.value = Date.now();

    // Цель «открытие формы» (микроформа считается тоже)
    var miniFocusFired = false;
    miniForm.addEventListener('focusin', function () {
      if (!miniFocusFired) {
        miniFocusFired = true;
        ym('form_focus');
        ym('form_open'); // показ формы и фокус считаем за открытие
      }
    });

    miniForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (miniSubmit.disabled) return;
      miniError.classList.remove('obr-mini-form__error--shown');

      if (!miniConsent.checked) {
        miniError.textContent = 'Подтвердите согласие на обработку персональных данных';
        miniError.classList.add('obr-mini-form__error--shown');
        return;
      }
      var phoneDigits = (miniPhone.value || '').replace(/\D+/g, '');
      if (phoneDigits.length < 10) {
        miniError.textContent = 'Укажите корректный телефон';
        miniError.classList.add('obr-mini-form__error--shown');
        return;
      }

      miniSubmit.disabled = true;
      miniSubmit.classList.add('obr-mini-form__submit--loading');

      var fd = new FormData(miniForm);

      fetch(ENDPOINT_URL, {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
      })
        .then(function (r) { return r.json().catch(function () { return null; }); })
        .then(function (data) {
          miniSubmit.disabled = false;
          miniSubmit.classList.remove('obr-mini-form__submit--loading');

          if (data && data.ok) {
            ym('form_submit');
            ym('form_channel_phone');
            // Виртуальный hit /spasibo.html для совместимости со старой целью «Заявка (shems)»
            if (typeof window.ym === 'function') {
              window.ym(COUNTER_ID, 'hit', '/spasibo.html', { title: 'Спасибо за заявку' });
            }
            miniForm.style.display = 'none';
            miniSuccess.hidden = false;
            return;
          }
          var err = (data && data.error) || 'unknown';
          var msg = {
            invalid_phone: 'Укажите корректный телефон',
            rate_limit:    'Заявка с этого номера уже отправлена. Подождите минуту.',
            invalid_origin: 'Запрос отклонён. Перезагрузите страницу.',
            too_fast:      'Слишком быстро — заполняйте внимательнее.',
            b24_unavailable: 'Сервис временно недоступен. Попробуйте через минуту или позвоните.',
            b24_rejected:  'Не удалось обработать заявку. Позвоните, пожалуйста.',
          }[err] || 'Что-то пошло не так. Попробуйте ещё раз.';
          miniError.textContent = msg;
          miniError.classList.add('obr-mini-form__error--shown');
        })
        .catch(function () {
          miniSubmit.disabled = false;
          miniSubmit.classList.remove('obr-mini-form__submit--loading');
          miniError.textContent = 'Нет связи с сервером. Проверьте интернет.';
          miniError.classList.add('obr-mini-form__error--shown');
        });
    });
  }

  // === Виртуальный hit /spasibo.html и для основной формы ===
  // Перехват: после успешной отправки основной формы — тоже дёргаем hit
  var origForm = document.getElementById('ObrLeadForm');
  if (origForm) {
    // Также добавим form_open_at в основную форму для проверки "слишком быстро"
    var origModal = document.getElementById('MdGlNew');
    if (origModal) {
      origModal.addEventListener('shown.bs.modal', function () {
        // Добавим скрытое поле form_open_at в основную форму
        var existed = origForm.querySelector('input[name="form_open_at"]');
        if (!existed) {
          existed = document.createElement('input');
          existed.type = 'hidden';
          existed.name = 'form_open_at';
          origForm.appendChild(existed);
        }
        existed.value = Date.now();
      });
    }
    // Перехватим успех — дёрнем hit. Используем MutationObserver на success-блоке
    var successDiv = document.getElementById('ObrFormSuccess');
    if (successDiv) {
      new MutationObserver(function () {
        if (!successDiv.hidden) {
          if (typeof window.ym === 'function') {
            window.ym(COUNTER_ID, 'hit', '/spasibo.html', { title: 'Спасибо за заявку' });
          }
        }
      }).observe(successDiv, { attributes: true, attributeFilter: ['hidden'] });
    }
  }
})();
