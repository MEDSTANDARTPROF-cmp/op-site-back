<!DOCTYPE html>
<html lang="ru">
	<head>
		[[$Head]]
	</head>
	<body>
		[[$Navbar]]
		<div  class="d-flex">
            [[$NavbarLeft]]
            <div  class="bg-body-secondary container-en ">
                
                <article  class="container px-0 px-sm-0 px-md-3 px-lg-4 px-xl-4 px-xxl-5">
                    <div class="pt-3 px-3 px-md-0">
                        [[$Crumbs]]
                    </div>
                    <div class="bg-white rounded-4 shadow overflow-hidden mb-5" itemscope itemtype="https://schema.org/Product">
                      <meta itemprop="name" content="[[*pagetitle]]">
                      <meta itemprop="description" content="[[*description:default=`[[*pagetitle]]`]]">
                      <meta itemprop="category" content="[[#[[*parent]].pagetitle]]">
                      <meta itemprop="sku" content="[[*id]]">
                      <meta itemprop="brand" content="[[++site_name]]">
                      <meta itemprop="image" content="[[!phpthumbon? &input=`[[*offerImg]]` &options=`&w=426&h=243&f=webp`]]">
        <div class="position-relative row row-cols-1  g-0 shadow">
            
            <div class=" position-relative px-4 p-5 p-sm-5 p-6">
                <div class="bg-body-secondary d-inline-block h6 lh-sm px-3 py-2 rounded-2 text-uppercase">
                   Доставка документов от 3-х дней
                </div>
                [[!+modx.user.id:is=`1`:then=`<br><mark>01 Оффер - H1 оффер или pagetitle</mark>`]]
                <h1 class="fw-bold h3 text-uppercase">[[*offerH1:default=`[[*pagetitle]]`]]</h1>
                
                <div style="margin-bottom: 1rem;">
                [[!+modx.user.id:is=`1`:then=`<mark>01 Оффер - Дескриптор под Оффером</mark>`]]
                [[*offerDescription:default=`
                
                <ul class="list-check mb-0">
                    <li><b>Продолжительность:</b> 36, 72, 144, 256, 512 часов.</li>
                    <li><b>Форма обучения:</b> дистанционно / очно.</li>
                </ul>
                <br><p>Курс рассчитан на руководителей организаций и специалистов по данному направлению.</p>
                <p>Все выдаваемые документы регистрируются в Федеральном реестре сведений о документах об образовании и (или) о квалификации, документах об обучении ФИС ФРДО.</p>
                
                `]]
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
                    <a href="#" class="btn text-white btn-ic mt-4 py-2 w-100 b24-form-marker" style="background-color:#d25304; font-weight:600; text-shadow:0 1px 2px rgba(0,0,0,0.4);" data-bs-toggle="modal" data-marker="Начать обучение" data-bs-target="#MdGl"><img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Обучение" style="filter: brightness(0) invert(1);"><span>Начать обучение</span></a>
                    <!-- a href="https://wa.me/79292101126" target="_blank" rel="noopener noreferrer" class="btn btn-gr btn-ic mt-4 py-2 w-100 b24-form-marker"><img src="assets/images/temp/ic-WhatsApp-32.svg" width="32" height="32" alt="WhatsApp"><span>Написать на WhatsApp*</span></a -->
                </div>
            </div>
        </div>
        
            
            <div class="row g-0">
                <div class="col-12 order-1 order-md-2 px-4 px-6 px-sm-5 py-5" style="background: #ecf2f7;">
                      <div class="g-4  row">
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/009.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Бесплатная доставка</b><br> оригиналов документов</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/010.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Обучение во всех</b><br> регионах России</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/005.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Актуальные</b><br> программы 2025 года</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/003.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>сопровождение</b><br> Во время обучения</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/001.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Официальные</b><br> документы</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/004.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Чат ОНЛАЙН</b><br> поддержки 24/7</div>
            </div>
        </div>

                </div>
            </div>
            
        </div>
[[!+modx.user.id:is=`1`:then=`<mark>02 контент - Описание программы</mark>`]]
[[*cont2Prog:notempty=`
<div class="g-0 px-4 px-sm-5 px-6 row" style="background: linear-gradient(180deg, rgb(236 242 247) 0%, rgb(231 240 250) 51%, rgb(108 117 125) 51.001%);">
  <div class="bg-white col-12 g-0 overflow-hidden rounded-4 shadow p-4 p-md-5">
    [[*cont2Prog]]
  </div>
</div>
`]]

            
            
            

    
    
<div class="px-4 px-6 px-sm-5 py-4 text-white text-bg-secondary" >
    <div class="fw-2 h3 m-0  text-center text-uppercase mb-4 mt-4">Об Учебном Центре<b class="fw-9"><br> «ОбрПрофи»</b></div>
         <div class="align-items-center col-md-12 d-flex pt-3 ">
             
                        <div style="position: relative; padding-top: 56.25%; width: 100%">
                            <iframe class="shadow" src="https://kinescope.io/embed/v9kyzA9yQ7XtCZkNmDfqU3" allow="autoplay; fullscreen; picture-in-picture; encrypted-media; gyroscope; accelerometer; clipboard-write; screen-wake-lock;" frameborder="0" allowfullscreen style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;"></iframe></div>
                    </div>
                                
                                <div class="g-4 mt-4 row row-cols-2  row-cols-md-5 text-dark fs-12 text-center justify-content-center">
                                    <div class="col ">
                                        <div class="bg-white p-3 rounded-3 shadow-sm h-100 d-flex flex-column position-relative align-items-center">
                                            <img src="assets/images/temp/a1.png" class="mb-3" height="72" width="72" alt="дистанционного обучения">
                                            <span>ЗАПОЛНЕНИЕ  <br> ЗАЯВКИ</span>
                                            <span class="badge bg-primary fs-18 position-absolute rounded-pill start-0 top-0 translate-middle">
                                                1
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="bg-white p-3 rounded-3 shadow-sm h-100 d-flex flex-column  position-relative align-items-center">
                                            <img src="assets/images/temp/a2.png" class="mb-3" height="72" width="72" alt="дистанционного обучения">
                                            <span>ОТПРАВКА НА ВАШ  <br> E-MAIL : ДОГОВОРА, СЧЕТА И ДАННЫЕ К СДО</span>
                                            <span class="badge bg-primary fs-18 position-absolute rounded-pill start-0 top-0 translate-middle">
                                                2
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="bg-white p-3 rounded-3 shadow-sm h-100 d-flex flex-column  position-relative align-items-center">
                                            <img src="assets/images/icon/04aaa.png" class="mb-3" height="72" width="72" alt="дистанционного обучения">
                                            <span>ОБУЧЕНИЕ И ТЕСТИРОВАНИЕ</span>
                                            <span class="badge bg-primary fs-18 position-absolute rounded-pill start-0 top-0 translate-middle">3</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="bg-white p-3 rounded-3 shadow-sm h-100 d-flex flex-column  position-relative align-items-center">
                                            <img src="assets/images/temp/a4.png" class="mb-3" height="72" width="72" alt="дистанционного обучения">
                                            <span>ОПЛАТА <br>ОБУЧЕНИЯ</span>
                                            <span class="badge bg-primary fs-18 position-absolute rounded-pill start-0 top-0 translate-middle">
                                                4
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col col-12 col-md">
                                        <div class="bg-white p-3 rounded-3 shadow-sm h-100 d-flex flex-column  position-relative align-items-center">
                                            <img src="assets/images/temp/a5.png" class="mb-3" height="72" width="72" alt="дистанционного обучения">
                                            <span>ПОЛУЧЕНИЕ УДОСТОВЕРЕНИЙ</span>
                                            <span class="badge bg-primary fs-18 position-absolute rounded-pill start-0 top-0 translate-middle">
                                                5
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <div class="d-flex justify-content-between"> 
                                    <div class="align-items-center d-flex justify-content-between mb-4 pt-4 w-100">
                                        <div class="fw-light h5 text-uppercase">
                                           <b>Формат обучения:</b><br>дистанционный (без отрыва от производства) или очный
                                        </div>
                                    <img src="assets/images/temp/BoxImportant.png" width="48" height="48" alt="Внимание" class=""></div>
                                    
                                </div></div>
<div class="p-5 p-6 position-relative px-4 px-sm-5 shadow-lg">
    <script data-b24-form="inline/330/rmpexx" data-skip-moving="true">
        (function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
        })(window,document,'https://cdn-ru.bitrix24.ru/b16716304/crm/form/loader_330.js');
    </script>
</div>                             
                              
                                
    <div class="bg-body-secondary px-4 px-sm-5 p-5 p-6">
        <div class="row g-0">
            <div class="col-12 col-md-9">
                [[!+modx.user.id:is=`1`:then=`<mark>03 лицензия - Заголовок Удостоверение</mark>`]]
                [[*Cont3Lic:default=`[[#[[#[[*parent]].temPrId]].Cont3Lic]]`]]
            </div>
        </div>
        <a href="#" class="row" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <div class="col">
                    <img src="[[!phpthumbon? &input=`assets/images/temp/diplom.jpg` &options=`&w=960&h=960&f=webp`]] " width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                </div>
                <div class="col">
                    <img src="[[!phpthumbon? &input=`assets/images/temp/udostov.jpg` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                </div>
                <div class="col">
                        <img src="[[!phpthumbon? &input=`assets/images/temp/svidetl.jpg` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                </div>
                
        </a>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
              <div class="bg-primary modal-header px-4 py-4">
                <div class="fs-5 fw-bold modal-title text-light h5" id="exampleModalLabel">Документы</div>
                <button type="button" class="bg-white btn-close opacity-100 p-3 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
              <div class="modal-body">
                    <img src="[[!phpthumbon? &input=`assets/images/temp/diplom.jpg` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
              
                
                
                    <img src="[[!phpthumbon? &input=`assets/images/temp/udostov.jpg` &options=`&w=960&h=960&f=webp`]]" width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                
                    <img src="[[!phpthumbon? &input=`assets/images/temp/svidetl.jpg` &options=`&w=960&h=960&f=webp`]] " width="770" height="241" alt="Учебный план типовой" class="h-auto mb-4 w-100 shadow">
                
                
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                
              </div>
            </div>
          </div>
        </div>
    </div>
    [[!+modx.user.id:is=`1`:then=`<mark>04 контент - Контент</mark>`]]
    [[*cont4:isempty=``
        :then=`
               <div class="position-relative px-4 px-6 px-sm-5" style="background: linear-gradient(180deg, rgb(233 236 239) 0%, rgb(233 236 239) 51%, rgb(255 255 255) 51.001%);">
                <div class="bg-white col-12 g-0 overflow-hidden rounded-4 row row-cols-1 row-cols-md-2 shadow flex-column-reverse flex-md-row">
                    <div class="p-4 p-sm-5">
                        [[*cont4:default=`[[#[[#[[*parent]].temPrId]].cont4]]`]]
                    </div>
                    <div style="background-image: url([[*cont4Img:default=`[[#[[#[[*parent]].temPrId]].cont4Img]]`]]);background-size: cover;background-position: center;min-height: 20rem;"></div>
                </div>
            </div>
        `]]
    
    
    
    
    
    
    
    [[$boxPrepod?]]
    <div class="bg-white px-4 px-sm-5 p-5 p-6">
    [[$boxLicComp?]]
    </div>

  [[*cont2faqNew:default=`[[#[[#[[*parent]].temPrId]].cont2faqNew]]`]]

    [[$boxManager?]]
    [[$contAdres?]]
    [[$contRek?]]
    
    
    
    
</div>
[[$boxFaqTemp?]]

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
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                </article>
                [[$Footer?]]
            </div>
        </div>
        
	</body>
</html>