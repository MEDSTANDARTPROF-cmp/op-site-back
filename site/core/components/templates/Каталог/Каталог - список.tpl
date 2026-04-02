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
                <div class="container pt-3">
                    [[!mFilter2?
                          &class=`msProduct`
                          &element=`msProducts`
                          &tpl=`tpl.msProducts.row.Obr.Modal`
                          &tplOuter=`tpl.mFilter2.outer.Obr`
                          &parents=`[[*id]]`
                          &includeTVs=`offerImgBg`
                          &limit=`6`
                          &showEmpty=`0`
                          &showUnpublished=`true`
                          &resources=`-346,-356,-62,-336-396,-31,-62,-356`
                    ]]
                </div>
                <style>
                    .bg-offer4 {
                        background-image: url([[!phpthumbon? &input=`[[*offerImgBg:default=`assets/img/expand__23336.jpeg`]]`&options=`&w=1920&f=webp`]]);
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-color: #6b6b6b;
                        background-blend-mode: soft-light;
                    }
                    
                </style>
                <div class="container mt-5 ">
                    <div class="overflow-hidden rounded-4 shadow">
                        <div class="rounded-4  rounded-top-5 shadow" style="background: #17224E;">
                            <div class="position-relative shadow">
                                <div class="px-4 px-6 px-sm-5 py-5  shadow-lg" style="background: #33d27b;color: #17224E;">
                                    <div class="fw-7 h4">Пройдите короткий опрос</div>
                                    <p class="m-0" style="max-width: 524px;">Мы подберем программу обучения и составим для <br>вас индивидуальное предложение</p>
                                </div>
                                <div class="px-4 px-6 px-sm-5 py-4 text-white " style="background: #17224E;">
                                    <div>
                                        <div class="rounded-4 overflow-hidden mt-4" data-marquiz-id="68071b017247320019abc27c"></div>
                                        <script>(function (t, p) { window.Marquiz ? Marquiz.add([t, p]) : document.addEventListener('marquizLoaded', function () { Marquiz.add([t, p]) }) })('Inline', { id: '68071b017247320019abc27c', buttonText: '«Старт»', bgColor: '#d34085', textColor: '#ffffff', rounded: true, shadow: 'rgba(211, 64, 133, 0.5)', blicked: true })</script>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="align-items-center d-flex justify-content-between mb-4 pt-4 w-100">
                                            <div class="fw-light h5 text-uppercase">
                                                <b>После заполнения формы</b><br>мы произведем расчет именно под ваши условия
                                            </div>
                                            <img src="assets/images/temp/BoxImportant.png" width="48" height="48" alt="Внимание"class="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                [[$catalogFooter?]]
                [[$boxFaqTemp?]]
                [[$Footer]]
            </div>
        </div>
	</body>
</html>