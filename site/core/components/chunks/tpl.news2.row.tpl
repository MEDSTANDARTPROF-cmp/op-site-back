<div class="col" itemscope itemtype="https://schema.org/NewsArticle">
    <div class="card border-0 rounded-4 bg-white shadow-sm h-100">
        {if $thumb?}
            <a href="{$id | url}" itemprop="url">
                <img src="[[!phpthumbon? &input=`{$thumb}` &options=`&w=320&h=240&f=webp&zc=1&q=40`]]"
                     width="320" height="240" class="card-img-top h-auto" alt="{$pagetitle}" itemprop="image">
            </a>
        {else}
        {/if}
        <div class="align-items-baseline card-body d-flex flex-column">
            <p class="card-text fs-11 mb-1">{$publishedon | date_format:" %d %b %Y"}</p>
            <h2 class="card-title fs-16 fw-6 mb-2" itemprop="headline">
                <a href="{$id | url}" class="fs-14 text-body text-decoration-none">{$pagetitle}</a>
            </h2>
            
            {if $longtitle?} 
                <p class="card-text fs-13">{$longtitle}</p>
            {/if}
            {if $description?}
                <p class="card-text fs-12" itemprop="description">{strip_tags($description) | truncate:60:"..."}</p>
            {/if}
            <a href="{$id | url}" class="btn mt-auto btn-primary btn-sm px-3">Подробнее</a>
        </div>
    </div>
</div>
