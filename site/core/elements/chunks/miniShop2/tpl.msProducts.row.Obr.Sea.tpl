
<div class="ms2_product card-pr">
    <form method="post" class="ms2_form card-pr-row no-gutters">
        <input type="hidden" name="id" value="{$id}">
        <input type="hidden" name="count" value="1">
        <input type="hidden" name="options" value="[]">
        <a href="{$id | url}" class="card-pr-a-img">
            {if $offerImgBg?}
                
                    <img src="[[!phpthumbon? &input=`{$offerImgBg}` &options=`&w=120&h=120&zc=1&f=webp&q=90&fltr[]=usm|150|0.7|2`]]"
                        width="120"
                        height="120"
                        alt="{$pagetitle}"
                        title="{$pagetitle}"
                        itemprop="image" />
                
            {else}
                <img src="[[!phpthumbon? &input=`[[#[[+parent]].offerImgBg]]` &options=`&w=120&h=120&zc=1&f=webp&q=90&fltr[]=usm|150|0.7|2`]]"
                        width="120"
                        height="120"
                        alt="{$pagetitle}"
                        title="{$pagetitle}"
                        itemprop="image" />
            {/if}
        </a>
        
        <div class="card-pr-col-all no-gutters position-relative">
            

            <div class="d-flex ps-3 ps-md-4 w-100">
                <div class="d-flex flex-column w-100">
                    <h5><a href="{$id | url}" class="fw-7 link-dark text-decoration-none">{$menutitle}</a></h5>
                    
                    
                </div>
            </div>
            <div class="align-items-end d-flex flex-column h-100 justify-content-center pe-3 py-3">
                <div class="d-flex flex-column flex-md-row">
                    <a class="d-md-flex d-none btn btn-gr btn-sm me-0 me-md-2  border-2 align-items-center pe-3" href="123123123"> <span class="ic ic-16 me-2 ic-whatsapp in"></span><span>Написать</span></a>
                    <a class="border-2 btn btn-outline-success btn-sm d-md-flex d-none fw-6" href="{$id | url}"><span>Подробнее</span></a>
                    
                </div>
            </div>
                
        </div>
        
    </form>
</div>


