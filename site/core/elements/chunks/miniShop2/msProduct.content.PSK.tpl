<div itemscope itemtype="https://schema.org/Course" class="pb-5">
    <div class="bg-white rounded-4 shadow overflow-hidden mb-5">
      <meta itemprop="name" content="[[*pagetitle]]">
      <meta itemprop="description" content="[[*description:default=`[[*pagetitle]]`]]">
      <meta itemprop="provider" content="УЦ ОбрПрофи">
      <meta itemprop="educationalCredentialAwarded" content="Удостоверение о присвоении рабочей профессии">
      <meta itemprop="image" content="[[!phpthumbon? &input=`[[*offerImg]]` &options=`&w=426&h=243&f=webp`]]">

            <!-- ===== 1. HERO ===== -->
            <div class="position-relative row row-cols-1 row-cols-md-2 g-0 shadow">
                <div class="bg-body-secondary" style="background-image: url([[!phpthumbon? &input=`[[*offerImgBg:default=`[[#[[#[[*parent]].temPrId]].offerImgBg]]`]]` &options=`&w=800&h=600&f=webp`]]); background-size: cover; background-position: center; min-height: 22rem;">
                </div>
                <div class="position-relative px-4 p-5 p-sm-5 p-6" onclick="ym(75081295,'reachGoal','zayavka')">
                    <img src="[[!phpthumbon? &input=`[[*offerIcon:default=`[[#[[#[[*parent]].temPrId]].offerIcon]]`]]` &options=`&w=72&h=72&f=webp`]]" width="72" alt="[[*pagetitle]]" class="end-0 m-3 position-absolute top-0">
                    <div class="bg-body-secondary d-inline-block h6 lh-sm px-3 py-2 rounded-2 text-uppercase mb-3">
                        <b>Дистанционный</b><br>и очный формат
                    </div>
                    <h1 class="fw-bold h3 text-uppercase">[[*offerH1:default=`[[*pagetitle]]`]]</h1>
                    [[*offerDescription:default=`[[#[[#[[*parent]].temPrId]].offerDescription]]`]]
                </div>
            </div>

            <!-- ===== 2. SOCIAL PROOF STRIP ===== -->
            <div class="d-flex flex-wrap justify-content-center justify-content-md-around gap-3 gap-md-0 px-4 py-3" style="background: linear-gradient(90deg, #f0f7f0 0%, #e8f5e9 100%);">
                <div class="text-center px-3">
                    <div class="fw-8 fs-5 text-success">10 000+</div>
                    <small class="text-black-50">выпускников</small>
                </div>
                <div class="vr opacity-25 d-none d-md-block"></div>
                <div class="text-center px-3">
                    <div class="fw-8 fs-5 text-success">200+</div>
                    <small class="text-black-50">компаний</small>
                </div>
                <div class="vr opacity-25 d-none d-md-block"></div>
                <div class="text-center px-3">
                    <div class="fw-8 fs-5 text-success">15 лет</div>
                    <small class="text-black-50">на рынке</small>
                </div>
                <div class="vr opacity-25 d-none d-md-block"></div>
                <div class="text-center px-3">
                    <div class="fw-8 fs-5 text-success">по всей</div>
                    <small class="text-black-50">России</small>
                </div>
            </div>

            <!-- ===== 3. PRICE + DESCRIPTION ===== -->
            <div class="bg-ws">
                <div class="row g-0">
                    <div class="col-12 col-md-6 order-2 order-md-1 pb-3 pb-md-5 pe-4 ps-4 ps-6 ps-sm-5 py-5">
                        [[*offerDescription2:default=`[[#[[#[[*parent]].temPrId]].offerDescription2]]`]]
                    </div>
                    <div class="col-12 col-md-6 order-1 order-md-2 px-4 px-6 px-sm-5 py-5" style="background: #ecf2f7;">
                        <div class="border border-dark-subtle d-flex justify-content-between price-box rounded-3">
                            <div class="d-flex flex-column p-3">
                                <small class="text-black-50">Стоимость обучения</small>
                                <b class="[[*old_price:isnotempty=`d-none`:else=``]]">
                                    <span class="text-dark">от</span> <span class="text-danger">[[+price]] руб.</span>
                                </b>
                                [[*old_price:isnotempty=`
                                <span>
                                    <span class="fs-20 fw-9 me-2 mу-2 text-danger">[[*price]]</span>
                                    <span class="fs-20 fw-5 ml-md-3 price text-decoration-line-through"> [[*old_price]] </span> ₽
                                </span>
                                <span class="px-3 py-1 fs-14 rounded-2 text-bg-danger">[[*priceData]]</span>
                                `:else=``]]
                            </div>
                            <div class="bg-white border-dark-subtle border-start d-flex flex-column p-3 rounded-end-3 rounded-start-3">
                                <small class="text-black-50">Рассрочка без %</small>
                                <b>на 12 месяцев</b>
                                [[*old_price:isnotempty=`
                                    <div class="fs-18 px-2 rounded-1 text-bg-warning"><span class="fw-6">[[!Rasrochka? &value=`[[*price]]` &months=`12`]]</span> ₽/мес</div>
                                `:else=``]]
                            </div>
                        </div>
                        <a onclick="ym(75081295,'reachGoal','zayavka')" href="#" class="btn btn-warning btn-ic mt-4 py-3 w-100 fs-5 fw-bold text-dark b24-form-marker" data-bs-toggle="modal" data-marker="Консультация товар" data-bs-target="#MdGl"><span class="bi bi-chat-dots-fill me-2 fs-4"></span><span>Консультация с менеджером</span></a>
                        <a href="tel:+78005502462" class="btn btn-outline-secondary btn-ic mt-2 py-2 w-100"><span class="bi bi-telephone-fill me-2"></span><span>8 800 550-24-62</span></a>
                    </div>
                </div>

                <!-- ===== 4. MERGED: О КУРСЕ (Кому подходит + Профиль) ===== -->
                <div class="g-0 px-4 px-sm-5 px-6 row pb-5">
                    <div class="bg-white col-12 g-0 overflow-hidden rounded-4 row shadow">
                        <div class="col-12 col-md-5 p-0" style="background-image: url([[!phpthumbon? &input=`[[*cont2Img]]` &options=`&w=640&h=800&f=webp`]]);background-size: cover;background-position: center;min-height: 18rem;"></div>
                        <div class="col-12 col-md-7 p-4 p-sm-5">
                            [[*cont2]]
                            <a onclick="ym(75081295,'reachGoal','zayavka')" href="#" class="btn btn-gr-l btn-ic mt-4 py-3 w-100 fs-5 fw-bold text-dark b24-form-marker" data-bs-toggle="modal" data-marker="Записаться о курсе" data-bs-target="#MdGl"><img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Записаться на курс"><span>Записаться на курс</span></a>
                        </div>
                    </div>
                </div>
            </div>

        [[$boxComerch]]
    </div>

    <!-- ===== 5. ДОКУМЕНТЫ ПО ОКОНЧАНИИ ===== -->
    [[$boxDocsPSK?]]

    <!-- ===== 6. PROGRAM ===== -->
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        <div class="bg-white pt-5 pb-5 pt-6 px-4 px-6 px-sm-5 shadow">
            [[*cont2Prog:default=`[[#[[#[[*parent]].temPrId]].cont2Prog]]`]]
        </div>
    </div>

    <!-- ===== 7. ADVANTAGES ===== -->
    [[$boxAdvPSK?]]

    <!-- ===== 8. FORM ===== -->
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        <div class="bg-white p-5 p-6 position-relative px-4 px-sm-5 rounded-4 shadow-lg">
            <script data-b24-form="inline/330/rmpexx" data-skip-moving="true">
                (function(w,d,u){
                var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
                var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
                })(window,document,'https://cdn-ru.bitrix24.ru/b16716304/crm/form/loader_330.js');
            </script>
        </div>
    </div>

    <!-- ===== 9. STAGES ===== -->
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        [[$boxStadii?]]
    </div>

    <!-- ===== 10. GUARANTEES ===== -->
    [[$boxGuaranteePSK?]]

    <!-- ===== 11. CTA BANNER ===== -->
    [[$boxCTABanner?]]

    <!-- ===== 12. PREPOD + LICCOMP + FAQ ===== -->
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        
        <div class="bg-white px-4 px-sm-5 p-5 p-6">
        [[$boxLicComp?]]
        </div>

    [[*cont2FaqNew:!empty=`[[*cont2FaqNew]]`]]
    </div>

    <!-- ===== 13. MANAGER ===== -->
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        [[$boxManager?]]
    </div>

    <!-- ===== 14. CONTACTS ===== -->
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        [[$contAdres?]]
        [[$contRek?]]
    </div>

</div>


<!-- Modal: Получить программу обучения -->
<div class="modal fade" id="exampleModaProgramm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="bg-primary modal-header px-4 py-4">
            <div class="fs-5 fw-bold modal-title text-light" id="exampleModalLabel">Получить программу обучения</div>
            <button type="button" class="bg-white btn-close opacity-100 p-3 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <div class="modal-body px-0 py-5">
            Скачать учебный план
      </div>
    </div>
  </div>
</div>