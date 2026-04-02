<div class="col">
    <div class="border-0 bg-white d-flex flex-md-column flex-row h-100 overflow-hidden rounded-4 shadow ">
        <div class="row g-0 w-100 d-grid d-md-flex  align-items-center">
            <div class="overflow-hidden col-12 col-md-6"
            style="
                background-image: url([[!phpthumbon? &input=`[[+tv.img]]` &options=`&w=512&h=512&f=webp&zc=1`]]);
                background-size: cover;
                background-position: center;
                
                min-height: 450px;
                height: 100%;
            ">
            </div>
            <div  class="col-12 col-md-6 d-flex flex-column px-3 px-md-4 py-3">
                <div class="p-3">
                    <p  class="mb-0 h4 fw-4">Автор курса</p>
                    <div class="card-title h3 fw-8 mb-2"><a href="[[~300]]" class="link-body-emphasis text-decoration-none">[[+pagetitle]]</a></div>
                    
                    <hr class="border-3 my-2 opacity-100 w-25">
                    {if $prep23?}
                        <p class="card-text d-inline-block fs-12 fs-18 fw-6">{$prep23}</p>
                    {else}
                        <p class="card-text d-inline-block fs-12 fs-18 fw-6">
                            Специалист по направлению «[[#[[*parent]].menutitle]]»
                        </p>
                    {/if}
                    {if $introtext?}
                        <p class="card-text small text-secondary">{$introtext}</p>
                    {/if}
                    <a href="[[~300]]">Подробнее</a>
                </div>
                
            </div>
        </div>
    </div>
</div>

