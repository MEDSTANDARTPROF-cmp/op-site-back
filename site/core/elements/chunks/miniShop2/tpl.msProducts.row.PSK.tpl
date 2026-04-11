<div class="ms2_product card-pr" itemtype="http://schema.org/Product" itemscope>
    <meta itemprop="description" content="{$description = $description ?: $pagetitle}">
    <meta itemprop="name" content="{$pagetitle}">

    <div  class="card-pr-row no-gutters">
        
        
        
        {if $offerImgBg?}
            <a href="{$id | url}" class="card-pr-a-img">
                <img src="[[!phpthumbon? &input=`{$offerImgBg}` &options=`&w=120&h=120&zc=1&f=webp&q=90&fltr[]=usm|150|0.7|2`]]"
                     width="120"
                     height="120"
                     alt="{$pagetitle}"
                     title="{$pagetitle}"
                     itemprop="image" />
            </a>
        {else} 
        {/if}
        

    
        <div class="card-pr-col-all no-gutters position-relative my-2" itemtype="http://schema.org/Offer" itemprop="offers" itemscope>
            <meta itemprop="price" content="{$price | replace:" ":""}">
            <meta itemprop="priceCurrency" content="RUB">
            <link itemprop="url" href="{$id | url}" />
            <meta itemprop="availability" content="http://schema.org/InStock" />
            <meta itemprop="itemCondition" content="http://schema.org/NewCondition" />
            <meta itemprop="category" content="{$parent | resource: "pagetitle"}">

            <div class="d-flex ps-3 ps-md-4 w-100">
                <div class="d-flex flex-column w-100">
                    <p class="fs-16 mb-1"><a href="{$id | url}" class="fw-7 link-dark text-decoration-none">{$menutitle}</a></p>
                    <div class="align-items-center d-flex justify-content-between">
                        <div class="">
                            [[+old_price:isnotempty=`
                                <span class="fs-18 fw-7 mу-2 text-danger">[[+price]]</span>
                                <span class="fs-18 fw-5 ml-md-3  price text-decoration-line-through"> [[+old_price]] ₽ </span>
                            `:else=``]]
                          
                        
                            <span class="fs-18 fw-5 ml-md-3  price [[+old_price:isnotempty=`d-none`:else=``]]">[[+price]] ₽</span>

                        </div>
                        <!-- a onclick="ym(75081295,'reachGoal','WA')" class="d-md-none d-flex btn btn-gr btn-sm me-0 me-md-2 border-2 align-items-center pe-3" target="_blank" href="https://wa.me/79292101126">
                            <span class="ic ic-16 me-2 ic-whatsapp in"></span><span>Написать*</span>
                        </a -->
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
                    <!-- a class="d-md-flex d-none btn btn-gr btn-sm me-0 me-md-2 border-2 align-items-center pe-3" target="_blank" href="https://wa.me/79292101126">
                        <span class="ic ic-16 me-2 ic-whatsapp in"></span><span>Написать*</span>
                    </a -->
                    <a class="border-2 btn btn-outline-success btn-sm d-md-flex d-none fw-6" href="{$id | url}"><span>Подробнее</span></a>
                </div>
            </div>
        </div>
    </div>
</div>
