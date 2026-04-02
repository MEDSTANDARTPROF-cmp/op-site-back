<!-- Базовая мета -->
<meta charset="[[++modx_charset]]">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="[[*description]]">
<meta name="robots" content="index, follow">
[[getResponseCode:isnot=`404`:then=`
    <base href="[[++site_url]]"/>
`]]

<title>[[!pdoTitle]] | УЦ ОбрПрофи</title>

[[getResponseCode:isnot=`404`:then=`
    <link rel="canonical" href="[[~[[*id]]? &scheme=`full`]]" />
`]]

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="[[*pagetitle]] | УЦ ОбрПрофи">
<meta property="og:description" content="[[*description]]">
<meta property="og:url" content="[[~[[*id]]? &scheme=`full`]]">
<meta property="og:image" content="[[*offerImgBg:isempty=`[[!phpthumbon? &input=`assets/images/offer/546.jpg` &options=`&w=640&h=320&f=webp&q=0.5&zc=1`]]`:notempty=`[[!phpthumbon? &input=`[[*offerImgBg]]` &options=`&w=640&h=320&f=webp&q=0.5&zc=1`]]`]]">
<!-- Schema.org microdata -->
<meta itemprop="name" content="[[*pagetitle]]">
<meta itemprop="description" content="[[*description]]">
<meta itemprop="url" content="[[~[[*id]]? &scheme=`full`]]">
<meta itemprop="image" content="[[*offerImgBg:isempty=`[[!phpthumbon? &input=`assets/images/offer/546.jpg` &options=`&w=640&h=320&f=webp&q=0.5&zc=1`]]`:notempty=`[[!phpthumbon? &input=`[[*offerImgBg]]` &options=`&w=640&h=320&f=webp&q=0.5&zc=1`]]`]]">

<!-- Favicon -->
<link rel="shortcut icon" href="/assets/images/favicons/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicons/180x180.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicons/favicon-16x16.png">


<!-- CSS -->
<link href="[[++assets_url]]components/themebootstrap/css/bootstrap.min.css?v=332" rel="stylesheet">
<link href="[[++assets_url]]css/style.css?v=332" rel="stylesheet">
<link href="[[++assets_url]]css/icon.css?v=3221" rel="stylesheet">
<link href="[[++assets_url]]css/ic.css?v=3221" rel="stylesheet">

<!-- Jivo -->
<script src="//code.jivo.ru/widget/Jwl0POxn4R" async></script>



<!-- b24 form value: отложенная загрузка -->
<script>
window.addEventListener('load', function () {

    function setCookie(name, value, days){
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000)); 
        let expires = "; expires=" + date.toGMTString();
        document.cookie = name + "=" + value + expires + ";path=/";
    }

    function getParam(p){
        let match = RegExp('[?&]' + p + '=([^&]*)').exec(window.location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    }
    
    function readCookie(name) { 
        let n = name + "="; 
        let cookie = document.cookie.split(';'); 
        for(let i = 0; i < cookie.length; i++) {      
            let c = cookie[i];      
            while (c.charAt(0)==' ') {c = c.substring(1,c.length);}      
            if (c.indexOf(n) == 0) {return c.substring(n.length,c.length);} 
        } 
        return null; 
    }

    let yclid = getParam('yclid');
    if (yclid) {
        setCookie('yclid', yclid, 90);
    } else {
        yclid = readCookie('yclid') || 0;
    }

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && node.classList.contains('b24-form')) {
                    fillFormField(node);
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    function fillFormField(form, markerValue = '') {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.value === 'defaultValues' || input.classList.contains('marker-none')) {
                let baseValue = '[[+pl_gorod:ifempty=`Не определено`]]/[[*parent:is=`3`:then=`Не определено`:else=`[[#[[*parent]].pagetitle:ifempty=`Не определено`]]`]]/[[*id:is=`3`:then=`Не определено`:else=`[[*pagetitle]]`]]/' + getDeviceTypeAdvanced() + "/" + yclid;
                if (markerValue) {
                    baseValue += `/${markerValue}`;
                }
                input.value = baseValue;
                input.classList.add('marker-none');
                input.dispatchEvent(new Event('input', { bubbles: true }));
                const parentDiv = input.closest('.b24-form-control-string.b24-form-field.b24-form-field-string');
                if (parentDiv) {
                    parentDiv.classList.add('d-none');
                }
            }
        });
    }

    function getDeviceTypeAdvanced() {
        const userAgent = navigator.userAgent.toLowerCase();
        const platform = navigator.platform.toLowerCase();
        const width = window.innerWidth;

        if (/mobile|android|iphone|ipad|ipod|blackberry|iemobile|opera mini/.test(userAgent) || width < 600) {
            return "mobile";
        }
        if (/tablet|mac|ipad/.test(userAgent) || (width >= 600 && width <= 1024)) {
            return "tablet";
        }
        if (/win|linux|cros/.test(platform)) {
            return "desktop";
        }
        return "Unknown";
    }

    jQuery(document).on('click', '.b24-form-marker', function () {
        const markerValue = this.getAttribute('data-marker');
        const forms = document.querySelectorAll('.b24-form');
        forms.forEach(form => fillFormField(form, markerValue));
    });

    const startForms = document.querySelectorAll('.b24-form');
    startForms.forEach(form => fillFormField(form));
});
</script>




<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window, document,'script','https://mc.yandex.ru/metrika/tag.js', 'ym');

    ym(75081295, 'init', {webvisor:true, clickmap:true, ecommerce:"dataLayer", accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/75081295" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->




