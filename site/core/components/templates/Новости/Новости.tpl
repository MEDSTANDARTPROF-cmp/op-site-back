<!DOCTYPE html>
<html lang="en">
	<head>
		[[$Head]]
	</head>
	<body>
		[[$Navbar]]
        <div  class="d-flex">
            [[$NavbarLeft]]
            <div  class="container-en bg-body-secondary">
                [[$saleTop?]]
                <article class="w-100">
                    <div class="d-flex py-5 text text-bg-dark">
                        <div class="container mb-5">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-5">
                                <div class="align-items-center d-flex">
                                    <div class="fw-2 h3 m-0 text-uppercase">[[*offerDescription2]]</div>
                                </div>
                            </div>
                        </div>    
                    </div>
                    </div>
                    <div class="position-relative mb-5" style="margin-top: -8rem;">
                        <div class="container my-3 pb-2" >
            			    [[$CrumbsLight]]
            		    </div>
                        <div class="container position-relative">
                            <div class="row-cols-1 row row-cols-md-3 g-4">
                                [[!pdoPage?
                                  &element=`msProducts`
                                  &tpl=`tpl.news2.row`
                                  &sortby=`{"id":"DESC"}`
                                ]]
                            </div>
                            <div class="container mt-3">
                    			[[!+page.nav]]
                    		</div>
                        </div>
                    </div>
                [[*content]]
                </article>
                [[$Footer]]
            </div>
        </div>
	</body>
</html>
