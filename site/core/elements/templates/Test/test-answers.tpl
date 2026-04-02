
<!DOCTYPE html>
<html lang="ru">
	<head>
		[[$Head]]
		<script src="assets/components/jquery-3.7.1.min.js"></script>
        <script src="assets/components/ui-main/dist/carousel/carousel.umd.js"></script>
        <script src="assets/components/ui-main/dist/carousel/carousel.thumbs.umd.js"></script>    
        <script src="assets/components/ui-main/dist/fancybox/fancybox.umd.js"></script>
	</head>
	<body>
		[[$Navbar]]
		<div  class="d-flex">
            [[$NavbarLeft]]
            <div  class="bg-body-secondary container-en ">
                [[$saleTop?]]
                <div class="">
                    <div class="d-flex py-5 text text-bg-dark">
                        <div class="container mb-5">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-5">
                                <div class="align-items-center d-flex">
                                    <h6 class="fw-2 h3 m-0 text-uppercase">[[#[[*parent]].offerH1]]</h6>
                                </div>
                            </div>
                        </div>
                        [[$CrumbsLight]]
                    </div>
                    </div>
                    <div class="position-relative mb-5" style="margin-top: -6rem;">
                        <div class="container  position-relative">
                            <div class="row g-4">
                                <div class="col-12 col-md-8">
                                    
                                    <div class="bg-body bg-body-secondary  rounded-4 shadow">
                                        
                                        <div class="px-4 px-md-5 pt-4 pt-md-5 ">
                                            <p>
                                                <span class="px-3 py-1 rounded-3 text-bg-primary">Готовые ответы на тест</span>  
                                            </p>  
                                            <h1 class="h4 fw-8 mb-0">[[*pagetitle]]</h1>
                                            <div class="py-3 pe-md-0">
                                                <p class="fs-12 mb-1 opacity-50">Быстрый поиск по тестам </p>
                                                [[!mSearchForm?
                                                	&tplForm=`tpl.mSearch2.form.Mini`
                                                	&pageId=`3441`
                                                	&tpl=`tpl.mSearch2.ac.pr`
                                                	&parents=`3430`
                                                ]]
                                            </div>
                                            <p>
                                                <a href="[[~[[*testParent]]]]" class="btn btn-gr w-100">Пройти тест на тренажере </a>
                                            </p>
<p>
<a href="#" class="btn btn-danger w-100 b24-form-marker" data-bs-toggle="modal" data-marker="Обучение тесты" data-bs-target="#MdGl">
  Обучение по направлению «[[!pdoField? &id=`[[*parent]]` &field=`menutitle`]]»
</a>
</p>
                                        </div>
                                     
                                        
                                            <div class="card border-0 px-4 px-md-5  py-5 rounded-4 shadow-lg " style="min-height: 200px;">
        [[*content]]
                                            
                                            [[*testParent:isnot=``:then=``:else=`[[*content]]`]]
                                            [[*testParent:notempty=`
                                                [[*content:notempty=`<div class="alert alert-light" role="alert">
                                                    [[*content]]
                                                </div>
                                                <hr>`]]
                                                
                                                [[getImageList?
                                                    &tvname=`testBoxTv`
                                                    &tpl=`testAnswersTpl`
                                                    &docid=`[[*testParent]]`
                                                ]]
                                                `
                                            ]]
    </div>
                                        <div class="px-4 px-md-5 py-4 py-md-5 ">
                                            
                                            
                                        </div>
                                    </div>
                                    [[$boxFormNews]]
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="bg-body overflow-hidden rounded-4 shadow p-4 p-md-4">
                                        
                                        <h6 class="fw-2 mb-3  fs-22 h3 m-0 text-uppercase"><b class="fw-9">Рекомендуем</b><br>почитать</h6>
                                        
                                        <div class="row g-4 row-cols-1">
                                            [[!msProducts? 
                                                &link=`1` 
                                                &master=`[[*id]]` 
                                                &parents=`27,26` 
                                                &tpl=`tpl.news.row`
                                                &limit=`0`
                                            ]]
                                            [[!msProducts?
                                                &parents=`27,26`
                                                &limit=`3`
                                                &tpl=`tpl.news.row`
                                                &resources=`-[[*id]]`
                                                &sortby=`RAND()`
                                            ]]
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                [[$Footer?]]
            </div>
        </div>
        
	</body>
</html>