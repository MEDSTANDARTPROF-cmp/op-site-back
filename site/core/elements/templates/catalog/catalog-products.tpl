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
                [[$catalogHeader?]]
                
                <div class="container pt-5 pb-0" style="">
                    <div class="bg-white p-5 p-6 position-relative px-4 px-sm-5 rounded-4 shadow-lg">
                        <script data-b24-form="inline/330/rmpexx" data-skip-moving="true">
                            (function(w,d,u){
                            var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
                            var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
                            })(window,document,'https://cdn-ru.bitrix24.ru/b16716304/crm/form/loader_330.js');
                        </script>
                    </div> 
                </div>
                
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
                          &tpl=`tpl.msProducts.row.Obr`
                          &tplOuter=`tpl.mFilter2.outer.Obr`
                          &parents=`[[*id]]`
                          &includeTVs=`offerImgBg,priceOld,priceData,priceAll`
                          &limit=`8`
                          &showEmpty=`0`
                    ]]
                </div>
                
                [[$catalogFooter?]]
                [[$boxFaqTemp?]]
                <div class="container pt-3">[[*cont2FaqNew:!empty=`[[*cont2FaqNew]]`]]</div>
                [[$Footer]]
            </div>
        </div>
	</body>
</html>