<style>
    .bg-offer {
        background-image: url([[!phpthumbon? &input=`[[*offerImgBg]]` &options=`&w=1200&f=webp&q=10`]]);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #aaaaaa;
    }
    .bg-offer.lazy-bg-mini  {
        background-image: url([[!phpthumbon? &input=`[[*offerImgBg]]` &options=`&w=100&f=webp&q=10`]]);
    }
    [[*offerImgBgSm:notempty=`
        @media (max-width: 768px) { 
            .bg-offer {
                background-image: none;
            }
            .bg-offer2 {
                background-image: url([[!phpthumbon? &input=`[[*offerImgBgSm:default=`assets/img/expand__23336.jpeg`]]`&options=`&w=320&q=40&f=webp&far=1`]]);
                background-size: cover;
                background-position: bottom;
                min-height: 380px;
            }
            .bg-offer2.lazy-bg-mini {
                background-image: url([[!phpthumbon? &input=`[[*offerImgBgSm:default=`assets/img/expand__23336.jpeg`]]`&options=`&w=20&q=40&f=webp&far=1`]]);
            }
        }
    `]]
.lazy-img {

  opacity: 1;
  
}

.lazy-img.loaded {
    opacity: 1;
}
</style>
<script>
window.addEventListener('load', function () {
  document.querySelectorAll('.lazy-bg-mini').forEach(el => {
    el.classList.remove('lazy-bg-mini');
  });
});
</script>

<div style="background-color: #33528D !important;">
    <div class="w-100 position-relative bg-box-blur bg-blur-md-50st bg-offer" >
        <div class="bg-blur bg-white bg-opacity-75 d-block position-absolute col-12 col-md-7 col-xxl-6 z-0">
        </div>
        <div class="container position-relative ">
           <div class="row g-0">
                <div class="col-12 col-md-6 my-4 py-5 pe-5 bg-blur-offer">
                    [[*offerMarker0]]
                    <p class="lh-1 text-primary text-uppercase"><b>ОНЛАЙН</b><br>Образование</p>
                    <h1 class="fw-bold h2 text-uppercase">[[*offerH1:default=`[[*pagetitle]]`]]</h1>
                    <div style="max-width: 512px;">[[*offerDescription]]</div>
                    [[*offerMarker2]]
                    [[*priceOld:notempty=`[[!$priceHeader?]]`:else=``]]
                    <div class="align-items-baseline d-flex flex-column pb-5" >
                        <a onclick="ym(75081295,'reachGoal','zayavka')" href="#" class="b24-form-marker bg-white border border-2 border-primary btn btn-ic fw-6 mt-4 pe-3 py-2" data-marker="Консультация  с менеджером - шапка сайта" data-bs-toggle="modal" data-bs-target="#MdGl" style="
    color: #06ae1f;
"> <img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Консультация с менеджером" class="" style="filter: brightness(1.5);"><span>Консультация  с менеджером</span></a>
                        <!-- a onclick="ym(75081295,'reachGoal','WA')" href="https://wa.me/79292101126" class="b24-form-marker btn btn-gr btn-ic mt-4 pe-3 py-2 btn-blic" target="_blank" rel="noopener noreferrer"><img src="assets/images/temp/ic-WhatsApp-32.svg" width="32" height="32" alt="Написать на WhatsApp"><span> <span class="flare"></span> Написать на WhatsApp*</span></a -->
                    </div>
                    <br class="pb-5 d-none d-md-blokc">
                </div>
                    <div class="align-items-end col-12 col-md-6 d-flex position-relative pb-5 px-4 px-md-0 bg-offer2">
                    <img 
  class="w-100 lazy-img "
  src="[[!phpthumbon? &input=`[[*offerImg]]` &options=`&w=660&f=webp&q=50&far=1`]]"
  width="660"
  height="auto"
  alt="[[*pagetitle]]"
  decoding="auto"
  >
                    </div>
            </div>
        </div>
    </div>
    <div class="position-relative" style="background: linear-gradient(180deg, rgb(255 255 255  / 0%) 0%, rgb(233 236 239  / 0%) 51%, rgb(51 82 140) 51.001%);margin-top: -4rem;">
        <div class="container  position-relative">
            [[*offerDescription2:default=`[[#[[#[[*parent]].temPrId]].offerDescription2]]`]]
        </div>
    </div>
    <div class="  text-bg-secondary" style="padding-top: 180px;margin-top: -180px;background-color: #33528D !important;">
        <div class="align-items-md-end container d-flex flex-column flex-md-row justify-content-between" style="margin-top: -39px;">
            <div class="align-items-center align-items-md-baseline d-flex flex-column mb-0 mb-md-5 pb-0 pb-md-4 pt-5 py-sm-5 text-center text-md-start">
                <div class="fw-light h4 mb-4 text-uppercase pt-5">
                    Запросить коммерческое<br><b>предложение для организации</b> 
                </div>
                <a onclick="ym(75081295,'reachGoal','zayavka')" href="#" class="btn btn-lim btn-ic py-2 pe-4 b24-form-marker" data-bs-toggle="modal" data-marker="Коммерческое предложение" data-bs-target="#MdGl"><img src="assets/icon/ic-List.svg" width="32" height="32" alt="Оставить заявку"><span>Оставить заявку</span></a>
            </div>
            <img class="mw-100 h-auto" src="[[!phpthumbon? &input=`assets/images/temp/com.png` &options=`&w=600&h=300&f=webp`]]" width="540" height="270" alt="Запросить коммерческое">
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function () {
  document.querySelectorAll('img.lazy-img').forEach(img => {
    const fullSrc = img.getAttribute('data-src');
    if (!fullSrc) return;

    const highRes = new Image();
    highRes.src = fullSrc;
    highRes.onload = () => {
      img.src = fullSrc;
      img.classList.add('loaded');
    };
  });
});
</script>