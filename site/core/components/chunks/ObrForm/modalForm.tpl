<link rel="stylesheet" href="assets/components/obr-form/form.css">

<div class="modal fade obr-form-modal" id="MdGlNew" tabindex="-1" aria-labelledby="MdGlNewTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content obr-form">

      <button type="button" class="obr-form__close" data-bs-dismiss="modal" aria-label="Закрыть">
        <span aria-hidden="true">&times;</span>
      </button>

      <h2 class="obr-form__title" id="MdGlNewTitle">Задать вопрос</h2>
      <p class="obr-form__subtitle" id="MdGlNewSubtitle">Менеджер свяжется с вами в течение 15 минут (рабочие часы)</p>

      <form class="obr-form__form" id="ObrLeadForm" novalidate>
        <input type="text" name="website" class="obr-form__honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

        <input type="hidden" name="form_source" id="ObrFormSource" value="Общий CTA">
        <input type="hidden" name="page_title" id="ObrFormPageTitle" value="">
        <input type="hidden" name="page_url" id="ObrFormPageUrl" value="">

        <div class="obr-form__field">
          <input type="text" name="name" id="ObrFormName" placeholder="Ваше имя" autocomplete="name">
        </div>

        <div class="obr-form__field" id="ObrFormPhoneField">
          <input type="tel" name="phone" id="ObrFormPhone" placeholder="Телефон" autocomplete="tel" required>
        </div>

        <div class="obr-form__field obr-form__field--hidden" id="ObrFormEmailField">
          <input type="email" name="email" id="ObrFormEmail" placeholder="Email" autocomplete="email">
        </div>

        <div class="obr-form__field">
          <textarea name="message" id="ObrFormMessage" placeholder="Сообщение (необязательно)" rows="2"></textarea>
        </div>

        <div class="obr-form__channels">
          <div class="obr-form__channels-label">Как удобнее связаться?</div>
          <div class="obr-form__channels-grid">
            <label class="obr-form__channel">
              <input type="radio" name="channel" value="phone" checked>
              <span class="obr-form__channel-btn">
                <span class="obr-form__channel-ic" style="background:#3b82f6">
                  <img src="assets/components/obr-form/icons/phone.svg" width="13" height="13" alt="">
                </span>
                <span>Позвоните мне</span>
              </span>
            </label>
            <label class="obr-form__channel">
              <input type="radio" name="channel" value="email">
              <span class="obr-form__channel-btn">
                <span class="obr-form__channel-ic" style="background:#ec4899">
                  <img src="assets/components/obr-form/icons/email.svg" width="13" height="13" alt="">
                </span>
                <span>По почте</span>
              </span>
            </label>
            <label class="obr-form__channel">
              <input type="radio" name="channel" value="wa">
              <span class="obr-form__channel-btn">
                <span class="obr-form__channel-ic" style="background:#25d366">
                  <img src="assets/components/obr-form/icons/whatsapp.svg" width="14" height="14" alt="">
                </span>
                <span>WhatsApp<sup class="obr-form__meta-mark" title="См. дисклеймер в футере">*</sup></span>
              </span>
            </label>
            <label class="obr-form__channel">
              <input type="radio" name="channel" value="tg">
              <span class="obr-form__channel-btn">
                <span class="obr-form__channel-ic" style="background:#229ED9">
                  <img src="assets/components/obr-form/icons/telegram.svg" width="14" height="14" alt="">
                </span>
                <span>Telegram</span>
              </span>
            </label>
            <label class="obr-form__channel">
              <input type="radio" name="channel" value="viber">
              <span class="obr-form__channel-btn">
                <span class="obr-form__channel-ic" style="background:#7360F2">
                  <img src="assets/components/obr-form/icons/viber.svg" width="14" height="14" alt="">
                </span>
                <span>Viber</span>
              </span>
            </label>
            <label class="obr-form__channel">
              <input type="radio" name="channel" value="max">
              <span class="obr-form__channel-btn">
                <span class="obr-form__channel-ic obr-form__channel-ic--max">
                  <img src="assets/components/obr-form/icons/max-original.svg" alt="">
                </span>
                <span>MAX</span>
              </span>
            </label>
          </div>
        </div>

        <div class="obr-form__consent">
          <label class="obr-form__consent-label">
            <input type="checkbox" name="consent" id="ObrFormConsent" checked required>
            <span>Отправляя форму, вы даёте согласие на обработку ваших персональных данных ООО «УЦ ОбрПрофи» в соответствии с Федеральным законом № 152-ФЗ «О персональных данных» и <a href="#" data-bs-toggle="modal" data-bs-target="#ObrFormConsentModal" role="button">Политикой в отношении обработки персональных данных</a>.</span>
          </label>
        </div>

        <button type="submit" class="obr-form__submit" id="ObrFormSubmit">
          <span class="obr-form__submit-text">Отправить</span>
          <span class="obr-form__submit-loader" aria-hidden="true"></span>
        </button>

        <div class="obr-form__error" id="ObrFormError" role="alert" aria-live="polite"></div>
      </form>

      <div class="obr-form__success" id="ObrFormSuccess" hidden>
        <div class="obr-form__success-ic">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3>Заявка отправлена</h3>
        <p id="ObrFormSuccessMsg">Менеджер свяжется с вами в течение 15 минут (в рабочие часы).</p>
      </div>

    </div>
  </div>
</div>

<div class="modal fade obr-consent-modal" id="ObrFormConsentModal" tabindex="-1" aria-labelledby="ObrFormConsentModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content obr-consent">

      <div class="obr-consent__header">
        <h2 class="obr-consent__title" id="ObrFormConsentModalTitle">Согласие на обработку персональных данных</h2>
        <button type="button" class="obr-form__close" data-bs-dismiss="modal" aria-label="Закрыть">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="obr-consent__body" id="ObrFormConsentBody">
        <p>Настоящим Я (далее – Субъект персональных данных), действуя сознательно, свободно, своей волей и в своем интересе даю согласие Обществом с ограниченной ответственностью Учебный центр «ОБРПРОФИ» (ИНН/КПП: 5032316800 / 184001001; ОГРН: 1205000021126), зарегистрированному по адресу: 426008, УДМУРТСКАЯ РЕСПУБЛИКА, Г.О. ГОРОД ИЖЕВСК, Г ИЖЕВСК, УЛ КИРОВА, ЗД. 172, ОФИС 205, (далее – оператор) на обработку моих персональных данных с целью: предоставления консультаций по вопросам, касающимся товаров, работ, услуг, акций и специальных предложений Оператора, с целью дальнейшего заключения договора гражданско-правового характера с субъектом персональных данных.</p>
        <p>Даю согласие на осуществление следующих действий в отношении моих персональных данных:</p>
        <p><strong>Категории обрабатываемых персональных данных:</strong></p>
        <ul>
          <li>фамилия, имя, отчество</li>
          <li>адрес электронной почты</li>
          <li>номер телефона</li>
          <li>сведения, собираемые посредством метрических программ.</li>
        </ul>
        <p><strong>Способы обработки персональных данных:</strong></p>
        <ul>
          <li>смешанная обработка персональных данных (автоматизированная и неавтоматизированная);</li>
          <li>без передачи информации по внутренней сети юридического лица;</li>
          <li>информация передается с использованием сети общего доступа Интернет.</li>
        </ul>
        <p><strong>Перечень действий, совершаемых Оператором с персональными данными субъектов:</strong></p>
        <ul>
          <li>сбор, запись, систематизация, накопление, хранение, уточнение (обновление, изменение), извлечение, использование, блокирование, удаление, уничтожение персональных данных.</li>
        </ul>
        <p><strong>Сроки обработки и хранения персональных данных:</strong></p>
        <ul>
          <li>обработка указанных персональных данных осуществляется в течение пяти лет от момента получения согласия от субъекта на обработку персональных данных.</li>
          <li>сроки хранения указанных персональных данных равны установленному сроку обработки персональных данных.</li>
        </ul>
        <p><strong>Порядок уничтожения персональных данных при достижении целей их обработки или при наступлении иных законных оснований:</strong></p>
        <ul>
          <li>уничтожение персональных данных на электронных носителях производится путем механического нарушения целостности носителя, не позволяющего произвести считывание или восстановление персональных данных, или удаления с электронных носителей методами и средствами гарантированного удаления остаточной информации.</li>
        </ul>
        <p>Передача (предоставление) оператором персональных данных третьим лицам не осуществляется.</p>
        <p><strong>Порядок обработки персональных данных (Способ получения согласия субъекта ПДн):</strong></p>
        <ul>
          <li>субъект предварительно самостоятельно знакомится с текстом согласия, а затем выражает своё согласие в электронном виде путём акцепта условий.</li>
          <li>субъект предварительно самостоятельно знакомится с текстом согласия, а затем даёт письменное согласие на обработку своих персональных данных в письменной форме.</li>
        </ul>
        <p>Настоящее согласие на обработку персональных данных действует с момента его представления оператору и может быть отозвано мной в любое время путем подачи оператору заявления в простой письменной форме.</p>
        <p><strong>Мои персональные данные уничтожаются:</strong></p>
        <ul>
          <li>по достижению целей обработки персональных данных и истечения срока хранения;</li>
          <li>при ликвидации или реорганизации оператора;</li>
          <li>на основании моего письменного обращения с требованием о прекращении обработки персональных данных (оператор прекратит обработку таких персональных данных в течение 3 (трех) рабочих дней, о чем будет направлено письменное уведомление субъекту персональных данных в течение 10 (десяти) рабочих дней.</li>
        </ul>
      </div>

      <div class="obr-consent__hint" id="ObrFormConsentHint">
        Прочитайте до конца
        <span class="obr-consent__hint-arrow">⌄</span>
      </div>

      <div class="obr-consent__footer">
        <button type="button" class="obr-consent__btn obr-consent__btn--accept" id="ObrFormConsentAccept">Принимаю</button>
        <button type="button" class="obr-consent__btn obr-consent__btn--decline" id="ObrFormConsentDecline">Не принимаю</button>
      </div>

    </div>
  </div>
</div>

<script src="assets/components/obr-form/form.js" defer></script>
