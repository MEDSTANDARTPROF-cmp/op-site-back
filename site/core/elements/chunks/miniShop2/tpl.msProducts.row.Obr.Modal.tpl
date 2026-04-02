<div class="ms2_product card-pr " itemtype="http://schema.org/Product" itemscope>
    <meta itemprop="description" content="{$description = $description ?: $pagetitle}">
    <meta itemprop="name" content="{$pagetitle}">

    <form method="post" class="ms2_form card-pr-row no-gutters">
        <input type="hidden" name="id" value="{$id}">
        <input type="hidden" name="count" value="1">
        <input type="hidden" name="options" value="[]">
        
        <a href="#" class="align-items-end b24-form-marker bg-primary0 card-pr-a-img d-flex bg-offer4 " data-bs-toggle="modal" data-marker="Список товаров - {$menutitle}" data-bs-target="#MdGl">
            
                <img src="[[!phpthumbon? &input=`/assets/images/offer/at-img3.png` &options=`&w=120&h=80&zc=1&f=webp&q=90&fltr[]=usm|150|0.7|2`]]"
                     width="120"
                     height="120"
                     alt="{$pagetitle}"
                     title="{$pagetitle}"
                     itemprop="image" />
        </a>

    
        <div class="card-pr-col-all no-gutters position-relative py-3" itemtype="http://schema.org/Offer" itemprop="offers" itemscope>
            <meta itemprop="price" content="{$price | replace:" ":""}">
            <meta itemprop="priceCurrency" content="RUB">
            <link itemprop="url" href="{$id | url}" />
            <meta itemprop="availability" content="http://schema.org/InStock" />
            <meta itemprop="itemCondition" content="http://schema.org/NewCondition" />
            <meta itemprop="category" content="{$parent | resource: "pagetitle"}">

            <div class="d-flex ps-3 ps-md-4 w-100">
                <div class="d-flex flex-column w-100">
                    <h5><a href="#" class="fw-7 link-dark text-decoration-none b24-form-marker" data-bs-toggle="modal" data-marker="Список товаров - {$menutitle}" data-bs-target="#MdGl">{$menutitle}</a></h5>
                    <div class="align-items-center d-flex justify-content-between">
                        <div>
                            <span class="price ml-md-3">{$price} ₽</span>
                            {if $old_price?}
                                <span class="old_price ml-md-3">{$old_price}</span>
                            {/if}
                        </div>
                        <a onclick="ym(75081295,'reachGoal','WA')" class="d-md-none d-flex btn btn-gr btn-sm me-0 me-md-2 border-2 align-items-center pe-3" target="_blank" href="https://wa.me/79292101126">
                            <span class="ic ic-16 me-2 ic-whatsapp in"></span><span>Написать*</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="align-items-end d-flex flex-column h-100 justify-content-center pe-3 py-3">
                <div class="fs-10 text-uppercase mb-2 d-none">
                    {if $new?}
                        <span class="d-flex px-2 py-1 rounded-2 text-bg-primary">{'ms2_frontend_new' | lexicon}</span>
                    {/if}
                    {if $popular?}
                        <span class="d-flex px-2 py-1 rounded-2 bg-warning">Популярный</span>
                    {/if}
                    {if $favorite?}
                        <span class="d-flex px-2 py-1 rounded-2 text-bg-info">{'ms2_frontend_favorite' | lexicon}</span>
                    {/if}
                </div>
                <div class="d-flex flex-column flex-md-row">
                    <a class="d-md-flex d-none btn btn-gr btn-sm me-0 me-md-2 border-2 align-items-center pe-3" target="_blank" href="https://wa.me/79292101126">
                        <span class="ic ic-16 me-2 ic-whatsapp in"></span><span>Написать*</span>
                    </a>
                    <a href="#" class="border-2 btn btn-outline-success btn-sm d-md-flex d-none fw-6 b24-form-marker" data-bs-toggle="modal" data-marker="Список товаров - {$menutitle}" data-bs-target="#MdGl"><span>Подробнее</span></a>
                </div>
            </div>
        </div>
    </form>
</div>
