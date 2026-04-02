
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
                                    <div class="bg-body overflow-hidden rounded-4 shadow">
                                        
                                        <div class="px-4 px-md-5 pt-4 pt-md-5 ">
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
                                     
                                        </div>
                                        <hr class="mb-4 mt-2">
                                        <div class="px-4 px-md-5 pb-5">
                                            <ul class="nav nav-pills flex-column">
                                                [[pdoMenu?
                                                  &parents=`[[*id]]`
                                                  &level=`2`
                                                  &tplOuter=`@INLINE [[+wrapper]]`
                                                  &tpl=`@INLINE
                                                    <li class="nav-item">
                                                      <a class="btn btn-outline-secondary fw-6 text-start w-100 mb-3 [[+classes]]" href="[[+link]]" [[+attributes]]>[[+menutitle]]</a>
                                                    </li>`
                                                  &showHidden=`0`
                                                  &showUnpublished=`0`
                                                  &countChildren=`1`
                                                ]]
                                            </ul>


                                         </div>
                                                [[*content:ne=``:then=`
                                                <hr />
                                                <div class="px-4 px-md-5 pb-5">
                                                  [[*content]]
                                                </div>
                                                `]]
                                    </div>
                                    [[$boxFormNews]]
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="bg-body overflow-hidden rounded-4 shadow p-4 p-md-4">
                                        
                                        <div class="fw-2 mb-3 h6 fs-22 h3 m-0 text-uppercase"><b class="fw-9">Рекомендуем</b><br>почитать</div>
                                        
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