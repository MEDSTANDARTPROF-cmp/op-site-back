<!DOCTYPE html>
<html lang="ru">
	<head>
		[[$Head]]
	</head>
	<body>
		[[$Navbar]]
		<div  class="d-flex">
            [[$NavbarLeft?]]
            <div class="container-en bg-body-secondary">
                [[$saleTop?]]
                [[$catalogHeaderDPO?]]
                
                <div class="container d-none" style="margin-top: -39px;">
                    [[!mSearchForm?
                    	&tplForm=`tpl.mSearch2.form.home`
                    	&pageId=`545`
                    	&tpl=`tpl.mSearch2.ac.pr`
                    ]]
                </div>
                <div class="container pt-3">
                    [[!mFilter2?
                          &class=`msProduct`
                          &element=`msProducts`
                          &tpl=`tpl.msProducts.row.Obr.DPO`
                          &tplOuter=`tpl.mFilter2.outer.Obr`
                          &parents=`[[*id]]`
                          &includeTVs=`offerImgBg,priceOld,priceData,priceAll`
                          &limit=`8`
                          &showEmpty=`0`
                    ]]
                </div>
                <br><br>
                [[$catalogFooterDPO?]]
                [[$boxFaqTemp?]]
                <div class="container pt-3">[[*cont2FaqNew:!empty=`[[*cont2FaqNew]]`]]</div>
                [[$Footer]]
            </div>
        </div>
	</body>
</html>