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
                
                [[*content]]
                
                [[pdoMenu?
                    &parents=`[[*id]]`
                    &level=`3`
                    &tplOuter=`@INLINE 
                        <div class="container pt-5 pb-0" style="">
                            <div class="row [[*row]] g-4 ">
                                [[+wrapper]]
                            </div>
                        </div>
                    `
                    &tpl=`catalogCard.tpl`
                    &includeTVs=`img,offerImgBg,offerImg,offerImgBgSm,icon`
                    &templates=`3,5,14`
                ]]
                
                [[$ObrFormFindCourse]]

                [[$catalogFooter?]]
                [[$boxFaqTemp?]]
                [[$Footer]]
            </div>
        </div>
	</body>
</html>