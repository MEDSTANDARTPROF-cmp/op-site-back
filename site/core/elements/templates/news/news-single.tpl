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
                [[$saleTop?]]
                
                <div class="">
                    <div class="d-flex py-5 text text-bg-dark">
                        <div class="container mb-5">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-5">
                                <div class="align-items-center d-flex">
                                    <div class="fw-2 h3 m-0 text-uppercase">[[#[[*parent]].offerDescription2]]</div>
                                </div>
                            </div>
                        </div>    
                    </div>
                    </div>
                    <div class="position-relative mb-5" style="margin-top: -8rem;">
                        <div class="container my-3 pb-2" >
            			    [[$CrumbsLight]]
            		    </div>
                        <div class="container  position-relative">
                            <div class="row g-4">
                                <div class="col-12 col-md-8">
                                    <div class="bg-body overflow-hidden rounded-4 shadow">
                                        <div>
                                            [[msGallery?
                                            	&tpl=`tpl.msGalleryNew`
                                            ]]
                                        </div>
                                        <div class="px-4 px-md-5 pt-4 pt-md-5 ">
                                            <h1 class="h4 fw-8">[[*pagetitle]]</h1>
                                            <p class="card-text fs-12">[[!rusDate? &date=`[[*publishedon]]`]]</p>
                                            <hr class="my-3">
                                        </div>
                                        <div class="px-4 px-md-5 pb-5">
                                            [[*content]]
                                        </div>
                                    </div>
                                    [[$boxFormNews]]
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="bg-body overflow-hidden rounded-4 shadow p-4 p-md-4">
                                        
                                        <div class="fw-2 mb-3  fs-22 h3 m-0 text-uppercase"><b class="fw-9">Рекомендуем</b><br>почитать</div>
                                        
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
                            
                            [[$boxFaqTemp?]]
                        </div>
                    </div>
                </div>
                [[$Footer?]]
            </div>
        </div>
        
	</body>
</html>