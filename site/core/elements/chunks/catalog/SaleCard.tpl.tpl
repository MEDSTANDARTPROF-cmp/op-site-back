<div class="col">
    <div class="border-0 bg-white flex-md-column flex-row h-100 overflow-hidden rounded-4 shadow ">
        <div class="row g-0 w-100 d-flex align-items-center">
            <div href="[[+link]]" class="overflow-hidden rounded-4 col-md-4"            >
                <img class="w-100" src="[[!phpthumbon? &input=`[[+img]]` &options=`&w=960&h=960&f=webp&q=0.5`]]" alt="">
            </div>
            <div  class="col-md-8 d-flex flex-column px-3 px-md-4 py-3">
                <div class="p-3">
                    <div class="card-title h4 fw-8 mb-2"><a href="[[+link]]" class="link-body-emphasis text-decoration-none">[[+pagetitle]]</a></div>
                    
                    <p class="alert alert-warning card-text fs-11 p-2">{$longtitle}</p>
                    {if $introtext?}
                        <p class="card-text small text-secondary">{$introtext}</p>
                    {/if}
                </div>
                <div class="bottom-0  mb-4 ps-3 w-100">
                    <a href="#" class="btn btn-gr btn-sm b24-form-marker" data-bs-toggle="modal" data-bs-target="#MdGl">Подробнее</a>
                </div>
            </div>
        </div>
    </div>
</div>
