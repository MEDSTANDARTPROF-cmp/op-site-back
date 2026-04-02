<div class="col">
    <div class="border-0 bg-white d-flex flex-md-column flex-row h-100 overflow-hidden rounded-4 shadow ">
        <div class="row g-0 w-100 d-grid d-md-flex align-items-center">
            <div href="[[+link]]" class="overflow-hidden rounded-4 col-12 col-md-4"
            style="
            background-image: url([[!phpthumbon? &input=`[[+img]]` &options=`&w=512&h=512&f=webp&q=0.8&zc=1`]]);
            background-size: cover;
            background-position: center;
            max-height: 80vh;
            min-height: 300px;
            height: 100%;
            ">
            </div>
            <div  class="col-12 col-md-8 d-flex flex-column px-3 px-md-4 py-3">
                <div class="p-3">
                    <div class="card-title h3 fw-8 mb-2">[[+pagetitle]]</div>
                    
                    <hr class="border-3 my-2 opacity-100 w-25">
                     <p class="card-text d-inline-block fs-12 fs-18 fw-6">{$longtitle}</p>
                    {if $description?}
                        <p class="card-text small text-secondary">{$description}</p>
                    {/if}
                </div>
                
            </div>
        </div>
    </div>
</div>
