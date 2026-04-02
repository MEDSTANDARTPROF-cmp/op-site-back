<!DOCTYPE html>
<html lang="en">
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
                [[$catalogFooter?]]
                [[$boxFaqTemp?]]
                <div class="container pt-3">[[*cont2FaqNew:!empty=`[[*cont2FaqNew]]`]]</div>
                [[$Footer]]
            </div>
        </div>
	</body>
</html>