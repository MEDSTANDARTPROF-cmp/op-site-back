<div class="col">
    <div class="card border-0 rounded-4 text-bg-dark overflow-hidden">
        {if $thumb?}
            <img src="[[!phpthumbon? &input=`{$thumb}` &options=`&w=320&h=240&f=webp&zc=1&q=40`]]" width="320" height="240" class="card-img rounded-4 h-auto" alt="{$pagetitle}" title="{$pagetitle}">
        {else}
            
        {/if}
        <a href="{$id | url}" class="bg-gr card-img-overlay d-flex flex-column fs-13 justify-content-end link-light p-4 text-decoration-none">
            <p class="card-text fs-11 mb-2">{$publishedon | date_format:" %d %b %Y"}</p>
            <div class="card-title fw-7 fs-14 h5">{$pagetitle}</div>
        </a>
    </div>
</div>
