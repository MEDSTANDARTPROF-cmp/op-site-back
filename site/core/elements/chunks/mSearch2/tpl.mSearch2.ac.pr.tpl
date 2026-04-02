<div class="d-flex mse2-ac-item">
    <div class="bg-offer border me-3 overflow-hidden position-relative rounded-2 d-none" style="width: 70px;height: 70px;min-width: 70px;">
        <img class="h-100 w-100" src="[[#[[+id]].offerImg:phpthumbon=`&w=120&h=120`]]">
    </div>
    <div class="d-none">{$idx}.</div> 
    <div> 
        <div class="card-title fw-7 text-dark mb-1">{$pagetitle}</div>
       {if $intro}
            <div>{$intro}</div>
        {/if}
        {if $weight}
            <div class="mse2-ac-weight d-none">
                <small>{'mse2_weight' | lexicon}: {$weight}0</small>
            </div>
        {/if}
    </div>
    
</div>