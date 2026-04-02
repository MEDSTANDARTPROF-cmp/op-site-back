<!DOCTYPE html>
<html lang="ru">
	<head>
		[[$Head]]

		<!-- Bootstrap Icons для PSK -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
		<!-- Fix: icon.css задаёт .bi{background-image:2-circle.svg} — сбрасываем -->
		<style>.bi{background-image:none!important;width:auto!important;height:auto!important;min-width:0!important;background-size:auto!important;display:inline!important}</style>

		<!-- JSON-LD: Course structured data для Google -->
		<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "Course",
			"name": "[[*pagetitle]]",
			"description": "[[*description]]",
			"url": "[[~[[*id]]? &scheme=`full`]]",
			"image": "[[!phpthumbon? &input=`[[*offerImg]]` &options=`&w=640&h=640&f=webp`]]",
			"provider": {
				"@type": "EducationalOrganization",
				"name": "УЦ ОбрПрофи",
				"url": "https://obrprofi.ru",
				"telephone": "+78005502462",
				"address": {
					"@type": "PostalAddress",
					"streetAddress": "ул. Кирова, зд. 172, офис 205",
					"addressLocality": "Ижевск",
					"addressRegion": "Удмуртская Республика",
					"postalCode": "426008",
					"addressCountry": "RU"
				}
			},
			"offers": {
				"@type": "Offer",
				"category": "[[#[[*parent]].pagetitle]]",
				"priceCurrency": "RUB",
				"availability": "https://schema.org/InStock",
				"url": "[[~[[*id]]? &scheme=`full`]]"
			},
			"hasCourseInstance": {
				"@type": "CourseInstance",
				"courseMode": ["online", "blended"],
				"courseWorkload": "PT[[*offerHours:default=`210`]]H",
				"inLanguage": "ru"
			},
			"educationalCredentialAwarded": "Удостоверение о присвоении рабочей профессии",
			"inLanguage": "ru"
		}
		</script>
	</head>
	<body>
		[[$Navbar]]
		<div  class="d-flex">
            [[$NavbarLeft]]
            <div  class="bg-body-secondary container-en ">

                <article  class="container px-0 px-sm-0 px-md-3 px-lg-4 px-xl-4 px-xxl-5">
                    <div class="pt-3 px-3 px-md-0">
                        [[$Crumbs]]
                    </div>
                    [[$msProduct.content.PSK?]]
                    [[$boxFaqTemp?]]
                </article>
                [[$Footer?]]
            </div>
        </div>

	</body>
</html>