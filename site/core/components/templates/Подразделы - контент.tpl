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
                <article class="w-100">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col  mh-100 px-0 px-lg-0 bg-body-secondary">
                                
                                
                                
                                
                                
                                <div class="d-flex py-5 text text-bg-dark">
                                    <div class="container mb-5 pb-5">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="align-items-center d-flex">
                                                    <h1 class="fw-9 h4 text-uppercase">[[*pagetitle]]</h1>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                        [[$CrumbsLight]]
                                    </div>
                                </div>
                                <div class="container mb-5" style="margin-top: -8rem;">
                                    <div class="bg-body overflow-hidden rounded-4 shadow p-5 p-6">
                                        [[*content]]
                                    </div>    
                                </div>



                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                            </div>
                            <div class="col navbar-en mh-100 pb-4 pb-lg-5 px-4 px-lg-4 d-none d-lg-block" style="background: #198754;">
                                <h3 class="fw-9 mt-5 text-light text-uppercase mb-4">Сведения</h3>
                                <div class="sticky-top0">
                                    <ul class="navbar-nav navbar-dark">
                                        [[pdoMenu?
                                            &startId=`298`
                                            &level=`2`
                                            &tplOuter=`@INLINE [[+wrapper]]`
                                            &tpl=`@INLINE
                                                <li class="nav-item [[+classnames]]">
                                                    <a class="btn btn-0 btn-outline-light text-start mb-2 w-100 [[+classnames]]" href="[[+link]]" itemprop="url">[[+menutitle]]</a>
                                                </li>`
                                            &tplParentRow=`@INLINE [[+wrapper]]`
                                        ]]
                                    </ul>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </article>
                [[$Footer]]
            </div>
        </div>
	</body>
</html>
