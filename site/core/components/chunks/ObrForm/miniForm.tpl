<div class="obr-mini-form" id="ObrMiniForm">
  <div class="obr-mini-form__label">Ответим на все вопросы по обучению</div>

  <form class="obr-mini-form__form" id="ObrMiniFormForm" novalidate>
    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="obr-mini-form__honeypot">
    <input type="hidden" name="form_open_at" id="ObrMiniFormOpenAt" value="">
    <input type="hidden" name="form_source" value="Микроформа hero: [[*pagetitle]]">
    <input type="hidden" name="page_title" value="[[*offerH1:default=`[[*pagetitle]]`]]">
    <input type="hidden" name="page_url" value="[[++site_url]][[~[[*id]]]]">
    <input type="hidden" name="channel" value="phone">

    <div class="obr-mini-form__row">
      <div class="obr-mini-form__phone-wrap">
        <span class="obr-mini-form__phone-ic" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="#7a8696" width="18" height="18"><path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z"/></svg>
        </span>
        <input
          type="tel"
          name="phone"
          id="ObrMiniFormPhone"
          placeholder="+7 (___) ___-__-__"
          autocomplete="tel"
          required
          class="obr-mini-form__phone"
          maxlength="20">
      </div>
      <button type="submit" class="obr-mini-form__submit" id="ObrMiniFormSubmit">
        <span class="obr-mini-form__submit-text">Узнать подробнее</span>
        <span class="obr-mini-form__submit-loader" aria-hidden="true"></span>
      </button>
    </div>

    <label class="obr-mini-form__consent">
      <input type="checkbox" name="consent" id="ObrMiniFormConsent" checked required>
      <span>Отправляя форму, вы даёте согласие на обработку ваших персональных данных ООО «УЦ ОбрПрофи» в соответствии с Федеральным законом № 152-ФЗ «О персональных данных» и <a href="#" data-bs-toggle="modal" data-bs-target="#ObrFormConsentModal" role="button" data-mini-consent="1">Политикой в отношении обработки персональных данных</a>.</span>
    </label>

    <div class="obr-mini-form__error" id="ObrMiniFormError" role="alert" aria-live="polite"></div>
  </form>

  <div class="obr-mini-form__success" id="ObrMiniFormSuccess" hidden>
    <div class="obr-mini-form__success-ic" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" width="32" height="32"><path d="M5 13l4 4L19 7"/></svg>
    </div>
    <h3 class="obr-mini-form__success-title">Спасибо!</h3>
    <p class="obr-mini-form__success-text">Ваше сообщение успешно отправлено. Менеджер перезвонит в течение 15 минут.</p>
  </div>
</div>
