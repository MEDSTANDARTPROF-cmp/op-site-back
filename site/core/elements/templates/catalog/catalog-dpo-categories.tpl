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
                

                [[*content]]
[[pdoMenu?
  &parents=`[[*id]]`
  &level=`3`
  &tplOuter=`@INLINE 
    <div class="container pt-5 pb-0">
      <div class="row">
        <div class="col-12">
          <h2 class="h4 fw-bold text-uppercase mb-4">Готовые курсы по направлениям</h2>
        </div>
      </div>
      <div class="row [[*row]] g-4">
        [[+wrapper]]
      </div>
    </div>
  `
  &tpl=`catalogCard.tpl.DPO`
  &includeTVs=`img,offerImgBg,offerImg,offerImgBgSm,icon`
  &templates=`23,24,14`
]]

                <br><br>
                [[$catalogFooterDPO?]]
                [[$boxFaqTemp?]]
                [[$Footer]]
            </div>
        </div>
	</body>
</html>