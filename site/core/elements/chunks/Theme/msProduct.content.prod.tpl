<div itemscope itemtype="https://schema.org/Product" class="pb-5">
    <div class="bg-white rounded-4 shadow overflow-hidden mb-5">
      <meta itemprop="name" content="[[*pagetitle]]">
      <meta itemprop="description" content="[[*description:default=`[[*pagetitle]]`]]">
      <meta itemprop="category" content="[[#[[*parent]].pagetitle]]">
      <meta itemprop="sku" content="[[*id]]">
      <meta itemprop="brand" content="[[++site_name]]">
      <meta itemprop="image" content="[[!phpthumbon? &input=`[[*offerImg]]` &options=`&w=426&h=243&f=webp`]]">
            <div class="position-relative row row-cols-1 row-cols-md-2 g-0 shadow">
                <div class="bg-body-secondary px-5 px-6 pt-5" style="background-image: url([[!phpthumbon? &input=`[[*offerImgBg:default=`[[#[[#[[*parent]].temPrId]].offerImgBg]]`]]` &options=`&w=512&h=394&f=webp`]]); background-size: cover; background-position: center;     min-height: 22rem;">
                    <div class="d-flex h-100 pt-5">
                        <img itemprop="image" src="[[!phpthumbon? &input=`[[*offerImg:default=`[[#[[#[[*parent]].temPrId]].offerImg]]`]]` &options=`&w=512&h=512&f=webp`]]" alt="[[*pagetitle]]" class="mt-auto h-auto w-100">
                    </div>
                </div>
                <div class=" position-relative px-4 p-5 p-sm-5 p-6" onclick="ym(75081295,'reachGoal','zayavka')">
                    <img src="[[!phpthumbon? &input=`[[*offerIcon:default=`[[#[[#[[*parent]].temPrId]].offerIcon]]`]]` &options=`&w=72&h=72&f=webp`]]" width="72" alt="[[*pagetitle]]" class="end-0 m-3 position-absolute top-0">
                    [[*offerMarker:default=`[[#[[#[[*parent]].temPrId]].offerMarker]]`]]
                    <h1 class="fw-bold h3 text-uppercase">[[*offerH1:default=`[[*pagetitle]]`]]</h1>
                    [[*offerDescription:default=`[[#[[#[[*parent]].temPrId]].offerDescription]]`]]
                </div>
            </div>
            <div class="bg-ws">
                
                <div class="row g-0">
                    <div class="col-12 col-md-6 order-2 order-md-1 pb-3 pb-md-5 pe-4 ps-4 ps-6 ps-sm-5 py-5">
                        [[*offerDescription2:default=`[[#[[#[[*parent]].temPrId]].offerDescription2]]`]]
                    </div>
                    <div class="col-12 col-md-6 order-1 order-md-2 px-4 px-6 px-sm-5 py-5" style="background: #ecf2f7;">
                        <!--div class="border border-dark-subtle d-flex justify-content-between price-box rounded-3">
                            <div class="d-flex flex-column p-3">
                                <small class="text-black-50">Стоимость  обучения</small>
                                <b>
                                    <span class="text-dark">от</span> <span class="text-danger">[[+price]] руб.</span>
                                </b>
                                
                            </div>
                            <div class="bg-white border-dark-subtle border-start d-flex flex-column p-3 rounded-end-3 rounded-start-3">
                                <small class="text-black-50">Рассрочка без %</small>
                                <b>на 12 месяцев</b>
                            </div>
                        </div-->
                        <div class="border border-dark-subtle d-flex justify-content-between price-box rounded-3">
                            <div class="d-flex flex-column p-3">
                                <small class="text-black-50">Стоимость  обучения</small>
                                <b class="[[#[[*parent]].priceOld:isnotempty=`d-none`:else=``]]">
                                    <span class="text-dark">от</span> <span class="text-danger">[[+price]] руб.</span>
                                </b>
                                
                                    
                                
                                [[#[[*parent]].priceOld:isnotempty=`
                                <span>
                                    <span class="fs-20 fw-9 me-2 mу-2 text-danger">[[#[[*parent]].priceAll]]</span>
                                    <span class="fs-20 fw-5 ml-md-3  price text-decoration-line-through"> [[#[[*parent]].priceOld]] </span> ₽
                                </span>
                                <span class="px-3 py-1 fs-14 rounded-2 text-bg-danger">[[#[[*parent]].priceData]]</span>
                                `:else=``]]
                                
                            </div>
                            <div class="bg-white border-dark-subtle border-start d-flex flex-column p-3 rounded-end-3 rounded-start-3">
                                <small class="text-black-50">Рассрочка без %</small>
                                <b>на 12 месяцев</b>
                                [[#[[*parent]].priceOld:isnotempty=`
                                    <div class="fs-18 px-2 rounded-1 text-bg-warning"><span class="fw-6">[[!Rasrochka? &value=`[[#[[*parent]].priceAll]]` &months=`12`]]</span> ₽/мес</div>
                                `:else=``]]
                            </div>
                        </div>
                        <!-- a onclick="ym(75081295,'reachGoal','WA')" href="https://wa.me/79292101126" target="_blank" rel="noopener noreferrer" class="btn btn-gr btn-ic mt-4 py-2 w-100 b24-form-marker"><img src="assets/images/temp/ic-WhatsApp-32.svg" width="32" height="32" alt="WhatsApp"><span>Написать на WhatsApp*</span></a -->
                        <a onclick="ym(75081295,'reachGoal','zayavka')" href="#" class="btn btn-gr-l btn-ic mt-4 py-2 w-100 b24-form-marker" data-bs-toggle="modal" data-marker="Консультация товар" data-bs-target="#MdGl"><img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Консультация с менеджером"><span>Консультация  с менеджером</span></a>
                    </div>
                </div>
                <div class="g-0 px-4 px-sm-5 px-6 row">
                    <div class="bg-white col-12 g-0 overflow-hidden rounded-4 row row-cols-1 row-cols-md-2 shadow">
                        <div style="background-image: url([[*cont2Img]]);background-size: cover;background-position: center;min-height: 15rem;"></div>
                        <div class="p-4 p-sm-5">
                            [[*cont2]]
                        </div>
                    </div>
                </div>
            </div>
    
        [[$boxComerch]]
    </div>
    
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
    
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        
        <div class="bg-white pt-5 pb-0 pt-6 px-4 px-6 px-sm-5 shadow pb-5">
            [[*cont2Prog:default=`[[#[[#[[*parent]].temPrId]].cont2Prog]]`]]
        </div>
        [[0
              
                <!--div class="d-flex justify-content-between px-4 px-md-5 px-6 text-bg-secondary">
                    <div class="row">
                        <div class="col-12 col-md-6 py-5">
                            <div class="fw-light h4 mb-4 text-uppercase">
                               <b> Хотите ? </b> <br>чтобы учебный план был <br> всегда под рукой
                            </div>
                            <a onclick="ym(75081295,'reachGoal','zayavka')" href="#" class="btn btn-lim btn-ic py-2 pe-4 b24-form-marker" data-bs-toggle="modal" data-bs-target="#MdGl"><img src="assets/icon/ic-List.svg" width="32" height="32" alt="Оставить заявку"><span>Скачать учебный план</span></a>
                        </div>
                        <div class="col-12 col-md-6 d-flex">
                            <img src="assets/images/temp/lic3.jpg" width="770" height="241" alt="Учебный план типовой" class="h-auto mt-md-auto shadow-b w-100">
                        </div>
                    </div>
                </div-->
        ]]
        
        
        
        
        
        
    </div>    
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        [[$boxStadii?]]
     </div>  
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        <div class="bg-body-secondary px-4 px-sm-5 p-5 p-6">
            <div class="row g-0">
                <div class="col-12 col-md-9">
                    [[*Cont3Lic:default=`[[#[[#[[*parent]].temPrId]].Cont3Lic]]`]]
                </div>
            </div>
            <a href="#" class="row-lic" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <div class="col-lic">
                        <img src="[[!phpthumbon? &input=`[[*cont3ImgA:default=`[[#[[#[[*parent]].temPrId]].cont3ImgA]]`]]` &options=`&w=960&h=960&f=webp`]] " width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                    </div>
                    <div class="col-lic">
                        <img src="[[!phpthumbon? &input=`[[*cont3ImgB:default=`[[#[[#[[*parent]].temPrId]].cont3ImgB]]`]]` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                    </div>
                    
                    [[#[[#[[*parent]].temPrId]].cont3ImgC:isempty=``:notempty=`
                        <div class="col-lic">
                            <img src="[[!phpthumbon? &input=`[[*cont3ImgC:default=`[[#[[#[[*parent]].temPrId]].cont3ImgC]]`]]` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                        </div>
                    `]]
            </a>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="bg-primary modal-header px-4 py-4">
                    <div class="fs-5 fw-bold modal-title text-light" id="exampleModalLabel">Документы</div>
                    <button type="button" class="bg-white btn-close opacity-100 p-3 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                  <div class="modal-body">
                    [[#[[#[[*parent]].temPrId]].cont3ImgC:isempty=``:notempty=`
                        
                        <img src="[[!phpthumbon? &input=`[[*cont3ImgC:default=`[[#[[#[[*parent]].temPrId]].cont3ImgC]]`]]` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                        
                    `]]
                    
                    
                        <img src="[[!phpthumbon? &input=`[[*cont3ImgB:default=`[[#[[#[[*parent]].temPrId]].cont3ImgB]]`]]` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                    
                        <img src="[[!phpthumbon? &input=`[[*cont3ImgA:default=`[[#[[#[[*parent]].temPrId]].cont3ImgA]]`]]` &options=`&w=960&h=960&f=webp`]] " width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                    
                    
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    
                  </div>
                </div>
              </div>
            </div>
        </div>
        
        <div class="position-relative px-4 px-6 px-sm-5" style="background: linear-gradient(180deg, rgb(233 236 239) 0%, rgb(233 236 239) 51%, rgb(255 255 255) 51.001%);">
            <div class="bg-white col-12 g-0 overflow-hidden rounded-4 row row-cols-1 row-cols-md-2 shadow flex-column-reverse flex-md-row">
                <div class="p-4 p-sm-5">
                    [[*cont4:default=`[[#[[#[[*parent]].temPrId]].cont4]]`]]
                </div>
                <div style="background-image: url([[*cont4Img:default=`[[#[[#[[*parent]].temPrId]].cont4Img]]`]]);background-size: cover;background-position: center;min-height: 20rem;"></div>
            </div>
        </div>
        [[$boxPrepod?]]
        <div class="bg-white px-4 px-sm-5 p-5 p-6">
        [[$boxLicComp?]]
        </div>
    
    [[*cont2FaqNew:!empty=`[[*cont2FaqNew]]`]]
    </div>
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        [[$boxManager?]]
    </div>
    <div class="bg-white rounded-4 shadow overflow-hidden mt-5">
        [[$contAdres?]]
        [[$contRek?]]
    </div>

</div>


<!-- Скачать учебный план -->
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