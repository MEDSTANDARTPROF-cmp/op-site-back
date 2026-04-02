
<!DOCTYPE html>
<html lang="ru">
	<head>
		[[$Head]]
		<script src="assets/components/jquery-3.7.1.min.js"></script>
        <script src="assets/components/ui-main/dist/carousel/carousel.umd.js"></script>
        <script src="assets/components/ui-main/dist/carousel/carousel.thumbs.umd.js"></script>    
        <script src="assets/components/ui-main/dist/fancybox/fancybox.umd.js"></script>
	</head>
	<body>
		[[$Navbar]]
		<div  class="d-flex">
            [[$NavbarLeft]]
            <div  class="bg-body-secondary container-en ">
                [[$saleTop?]]
                
                    [[*content]]
                
                [[$Footer?]]
            </div>
        </div>
	</body>
</html>