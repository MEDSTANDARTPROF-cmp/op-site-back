<div class="col">
    <div class="border-0 card flex-md-column flex-row h-100 overflow-hidden rounded-4 shadow-sm">
        <a href="[[+link]]" class="overflow-hidden rounded-top-4 col-4 col-md-12 position-relative bg-secondary b24-form-marker" data-marker="Карточка - [[+pagetitle]]" data-bs-toggle="modal" data-bs-target="#MdGl">
            <img src="[[!phpthumbon? &input=`[[+offerImg]]` &options=`&w=480&h=480&f=webp&q=0.5&zc=1`]]" width="240" height="240" class="px-2 px-md-5 position-absolute bottom-0 card-img mt-auto h-auto" alt="[[+pagetitle]]">
           <img src="[[!phpthumbon?
  &input=`[[+offerImgBgSm]]`
  &options=`w=480&h=360&zc=1&far=BC&f=webp&q=80`
]]" width="480" height="360" class="bottom-0 card-img mt-auto h-auto" alt="[[+pagetitle]]">

        </a>
        <div  class=" col-8 col-md-12 d-flex flex-md-column flex-row align-items-center align-items-lg-baseline">
            
            <div class="p-3">
                <img class="mb-2"src="[[+icon]]" width="48" height="48" alt="[[+pagetitle]]">
                <div class="h6 card-title"><a href="[[+link]]" class="link-body-emphasis text-decoration-none  fw-6 fs-18">[[+menutitle]]</a></div>
                {if $introtext?}
                    <p class="card-text small text-secondary d-md-block d-none">{$introtext}</p>
                {/if}
            </div>
            <div class="bottom-0 d-md-block d-none mb-3 ps-3 w-100">
                <a href="[[+link]]" class="btn btn-gr btn-sm b24-form-marker" data-marker="Карточка - [[+pagetitle]]" data-bs-toggle="modal" data-bs-target="#MdGl">Подробнее</a>
            </div>
        </div>
        
    </div>
</div>

