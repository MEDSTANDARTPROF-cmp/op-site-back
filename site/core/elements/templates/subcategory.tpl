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
                                [[*content]]
                            </div>
                            <div class="col navbar-en mh-100 pb-4 pb-lg-5 px-4 px-lg-4 d-none d-lg-block" style="background: #198754;">
                                <h3 class="fw-9 mt-5 text-light text-uppercase mb-4">О компании</h3>
                                <div class="sticky-top">
                                    <ul class="navbar-nav navbar-dark">
                                        [[pdoMenu?
                                            &resources=`2,301,30,2,7,298,29,300`
                                            &startId=`0`
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
