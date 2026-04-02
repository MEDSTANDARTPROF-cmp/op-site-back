<div class="container mb-5 mt-5">
    <div class="overflow-hidden rounded-4 shadow">
         [[*Stadii2:default=`[[$boxStadii?]]`]]
    </div>
</div>
<div class="container">
    <div class="bg-white g-0 mt-5  rounded-top-4 row shadow-lg overflow-hidden">
        <div class="col-12 col-md-6 p-4 p-md-5">
            [[*cont2Prog:default=`
                <div class="fw-2 h2 text-uppercase my-4">
                    Учебный Центр<br><b class="fw-9"> «ОбрПрофи»</b>
                </div>
                <p>Проводит обучение на основании образовательной лицензии Министерства образования и аккредитации Минтруда РФ. Проверка в Реестре МИНТРУДА, проверка лицензии в РОСОБРНАДЗОРЕ</p>
                <div class="alert alert-success align-items-center d-flex rounded-0 rounded-end-4 rounded-top-4">
                    <img src="assets/images/icon/04aaa.png" height="72" width="72" alt="дистанционного обучения">
                    <p class="m-0 ms-4 small">Обучение проводится с использованием сертифицированной системы дистанционного обучения.</p>
                </div>
                <p>
                    Учебный центр «ОбрПрофи» проводит повышение квалификации по промышленной безопасности (промбезопасности),энергетической безопасности, безопасности гидротехнических сооружений для аттестации в Ростехнадзоре по следующим программам:
                </p>
            `]]
            
        </div>
        <div class="col-12 col-md-6 bg-secondary-subtle" style="background-image: url([[!phpthumbon? &input=`[[*cont2img:default=`/assets/images/temp/img01.jpg`]]`&options=`&w=960&h=640&f=webp`]]);background-size: cover;background-position: center;min-height: 34vh;">
        </div>
    </div>
</div>
<div class="bg-white">
    <div class="container pb-5">
        <div class="align-items-center d-flex flex-column flex-md-row g-0 p-4 p-md-5 rounded-bottom-4 text-bg-primary">
                <div class="align-items-md-center d-flex flex-column flex-md-row justify-content-between w-100 text-uppercase">
                    <div class="fs-3 h6 fw-8 mb-4 mb-md-0 text-light text-uppercase">Заказать  <br> <span class="fw-light">обратный звонок</span></div>
                        <a href="#" class="btn btn-lim btn-ic py-2 px-3 pe-4 24-form-marker" data-bs-toggle="modal" data-marker="Обратный звонок" data-bs-target="#MdGl"><img src="assets/images/icon/icons/telephone-inbound-fill.svg" width="24" height="24" alt="Оставить заявку"><span>Оставить заявку</span></a>
                </div>
        </div>
    </div>
    <div class="container pb-5">
    [[$boxLicComp?]]
    </div>
</div>
<div class="position-relative" style="background: linear-gradient(180deg, rgb(255 255 255) 0%, rgb(255 255 255) 51%, rgb(226 227 229) 51%);">
    <div class="container  position-relative">
        <div class="bg-white col-12 g-0 overflow-hidden rounded-4 row row-cols-1 row-cols-md-2 shadow">
            <div style="background-image: url([[!phpthumbon? &input=`[[*cont5Img2:default=`/assets/images/temp/asd.jpg`]]`&options=`&w=960&h=640&f=webp`]]);background-size: cover;background-position: center;min-height: 15rem;"></div>
            <div class="p-4 p-sm-5">
                 [[*cont5?]]
            </div>
        </div>
    </div>
</div>

<div class="bg-secondary-subtle">
    [[$boxOtziv?]]
</div> 
<div class="" style="background: linear-gradient(180deg, rgb(226 227 229) 0%, rgb(226 227 229) 51%, rgb(51 82 140) 51%);">
    <div class="container pb-5">
<div class="position-relative bg-body-secondary rounded-top-5">
        <div class="bg-white col-12 g-0 overflow-hidden rounded-4 row row-cols-1 row-cols-md-2 shadow">
            <div style="background-image: url([[!phpthumbon? &input=`[[*cont4Img:default=`[[#[[*temPrId]].cont4Img]]`]]` &options=`&w=960&h=960&f=webp`]]);background-size: cover;background-position: center;min-height: 15rem;"></div>
            <div class="p-4 p-sm-5">
                [[*cont4:default=`[[#[[*temPrId]].cont4]]`]]
            </div>
        </div>
    </div>
    <div class="bg-body-secondary px-4 px-sm-5 p-5 p-6 rounded-bottom-4">
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
                <div  class="fs-5 h5 fw-bold modal-title text-light" id="exampleModalLabel">Документы</div>
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
</div>
</div>
<div class="position-relative text-light" style="background: linear-gradient(180deg, rgb(51 82 140) 0%, rgb(51 82 140) 51%, rgb(255 255 255) 51.001%);">
    [[$boxBlago?]]
</div>
<div class="" style="background: linear-gradient(180deg, rgb(255 255 255) 0%, rgb(233 236 239) 51%, rgb(25 135 84) 51.001%);">
    <div class="container pb-5">
        
        <div class="align-items-center d-flex flex-column flex-md-row g-0 p-4 p-md-5 rounded-4 shadow text-bg-primary">
            <div class="pe-3 pe-md-4">
                    <img src="[[!phpthumbon? &input=`assets/images/temp/timur.png` &options=`&w=276&h=276&f=webp`]]" width="276" height="276">
                </div>
                <div class="align-items-center align-items-lg-start d-flex flex-column my-4 my-md-0">
                    <div class="fs-3 h6 fw-8 mb-0 text-light text-uppercase">Остались <br> <span class="fw-light">вопросы?</span></div>
                    <hr class="w-25 my-2  opacity-50">
                    <p class="fs-14 fw-4">Меня зовут Тимур, я менеджер учебного центра «ОбрПрофи».<br>Для получения консультации вы можете написать мне в WhatsApp:</p>
                    <p class="mb-0">
                        <a onclick="ym(75081295,'reachGoal','zayavka')" href="#" class="b24-form-marker bg-white border border-2 border-primary btn btn-ic fw-6 mt-4 pe-3 py-2" data-marker="Консультация" data-bs-toggle="modal" data-bs-target="#MdGl" style="
    color: #06ae1f;
"> <img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Консультация с менеджером" class="" style="filter: brightness(1.5);"><span>Консультация</span></a>
                    </p>
                </div>
        </div>
        
    </div>
</div>
[[*boxFaqTv:notempty=`[[$boxFaq]]`]]
<div class="position-relative mb-5" style="background: linear-gradient(180deg, rgb(25 135 84) 0%, rgb(25 135 84) 51%, rgb(233 236 239) 51%);">
    <div class="container  position-relative">
        <div class="bg-white overflow-hidden rounded-4 shadow">
            [[$contAdres?]]
[[$contRek?]]
        </div>
    </div>
</div>