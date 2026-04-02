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
                   [[*offerMarker]]
                </div>
                [[!+modx.user.id:is=`1`:then=`<br><mark>01 Оффер - H1 оффер или pagetitle</mark>`]]
                <h1 class="fw-bold h3 text-uppercase">[[*offerH1:default=`[[*pagetitle]]`]]</h1>
                
                <div style="margin-bottom: 1rem;">
                [[!+modx.user.id:is=`1`:then=`<mark>01 Оффер - Дескриптор под Оффером</mark>`]]
                [[*offerDescription:default=`
                
<p>Готовый комплект лекционного материала для дистанционного обучения. Что входит в состав поставки:</p>
<ul class="list-check mb-0">
<li>Лекционный материал в формате PDF</li>
<li>Тесты</li>
<li>Учебный или тематический план</li>
</ul>
<br><p>Также вы можете заказать комплект другого лекционного материала под ключ, если на данный момент у нас его нет.</p>
                
                `]]

                    <a href="#" class="btn text-white btn-ic mt-4 py-2 w-100 b24-form-marker" style="background-color:#d25304; font-weight:600; text-shadow:0 1px 2px rgba(0,0,0,0.4);" data-bs-toggle="modal" data-marker="Получить подробности ДПО" data-bs-target="#MdGl"><img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Обучение" style="filter: brightness(0) invert(1);"><span>Получить подробности</span></a>
                    <!-- a href="https://wa.me/79292101126" target="_blank" rel="noopener noreferrer" class="btn btn-gr btn-ic mt-4 py-2 w-100 b24-form-marker"><img src="assets/images/temp/ic-WhatsApp-32.svg" width="32" height="32" alt="WhatsApp"><span>Написать на WhatsApp*</span></a -->
                </div>
            </div>
        </div>
        
            
            <div class="row g-0">
                <div class="col-12 order-1 order-md-2 px-4 px-6 px-sm-5 py-5" style="background: #ecf2f7;">
                      <div class="g-4  row">
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/s1.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>5 лет</b><br> успешной работы</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/003.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Круглосуточная</b><br> поддержка</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/005.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>1000+ покупателей</b><br> по всей России</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/001.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Работа</b><br> по официальному договору</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/s2.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>30 человек</b><br> штат методистов</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="align-items-center d-flex">
                <img src="assets/images/icon/s3.png" width="72" height="72" style="width:3.125rem;height:3.125rem;" alt="Формат">
                <div class="h6 small fw-medium m-0 ps-3 text-uppercase"><b>Разработка программ</b><br> с учетом последних изменений Законодательства</div>
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
[[$boxLicComp?]]</div>


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
    
    




    
  [[*cont2faqNew:default=`[[#[[#[[*parent]].temPrId]].cont2faqNew]]`]]

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