<!DOCTYPE html>
<html lang="ru">
	<head>
		[[$Head]]
	</head>
	<body>
		[[$Navbar]]
        <div  class="d-flex">
            [[$NavbarLeft]]
            <div  class="container-en">
                [[$saleTop?]]
                <article class="w-100  bg-body-secondary pb-5">
                    <div class="d-flex py-5 text text-bg-dark">
                        <div class="container mb-5 px-4 px-md-0">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-5">
                                    <div class="align-items-center d-flex">
                                        <h1 class="fw-2 h3 m-0 text-uppercase"><b class="fw-9">Отзывы</b><br>УЦ «ОбрПрофи»</h1>
                                    </div>
                                    <a href="#" class="b24-form-marker border border-2 border-primary btn btn-ic fw-medium mt-4 pe-3 py-2 text-primary" data-bs-toggle="modal" data-bs-target="#Otz"><img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Консультация с менеджером" class="" style="filter: brightness(1.5);"><span>Оставить отзыв</span></a>
                                </div>
                                
                            </div>    
                        </div>
                    </div>
                    <div class="position-relative" style="margin-top: -8rem;">
                        <div class="container my-3 pb-2 px-4 px-md-0" >
            			[[$CrumbsLight]]
            		</div>
                        <div class="container  px-0 px-sm-0 px-md-3 px-4 px-md-0 position-relative">
                            <div class="row-cols-1 row row-cols-md-4 g-4">
                                [[getImageList?
                                    &tvname=`boxOtzivTv`
                                    &tpl=`boxOtzivTplCol`
                                    &docid=`73`
                                    &limit=`50`
                                ]]
                            </div>
                        </div>
                    </div>
            		<!-- Глобальная форма -->
                    <div class="modal fade" id="Otz" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="bg-primary modal-header px-4 py-4">
                                <h5 class="fs-5 fw-bold modal-title text-light" id="exampleModalLabel">Ваш отзыв</h5>
                                <button type="button" class="bg-white btn-close opacity-100 p-3 rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                          <div class="modal-body px-0 py-5">
                                <script data-b24-form="inline/312/y09o0r" data-skip-moving="true">
                                    (function(w,d,u){
                                    var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
                                    var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
                                    })(window,document,'https://cdn-ru.bitrix24.ru/b16716304/crm/form/loader_312.js');
                                </script>
                          </div>
                        </div>
                      </div>
                    </div>
                [[*content0]]
                </article>
                [[$Footer]]
            </div>
        </div>
	</body>
</html>
