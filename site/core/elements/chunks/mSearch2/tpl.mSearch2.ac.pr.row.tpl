<div class="bg-white d-flex mb-3 mse2-ac-item p-2 rounded-3 shadow-sm">
    <div class="me-3 overflow-hidden position-relative rounded-2" style="width: 70px;height: 70px;min-width: 70px;">
        <img class="h-100" style="margin-left: -30px;"src="[[#[[+id]].offerImg:phpthumbon=`&w=120&h=120`]]">
    </div>
    <div class="d-none">{$idx}.</div> 
    <a href="[[~[[+id]]]]" class="text-decoration-none text-body-secondary">
        <div class="card-title fw-bold text-dark mb-1">{$pagetitle}</div>
        <div  class="pt-2 pb-2">
            [[+parent:in=`17,2,10,1243,1341`:then=`
                От
            `:else=``]]
            <span class="fw-bold ml-md-3 price text-danger px-2 bg-warning"> [[#[[+id]].price]] руб.<!--{'ms2_frontend_currency' | lexicon}--></span>
            [[+parent:in=`17,2,10,1243,1341`:then=``:else=`
                <span class=" fw-bold ml-md-3 old_price px-1  rounded-2 text-danger text-decoration-line-through">[[#[[+id]].old_price]] руб.</span>
            `]]
        {if $price?}
        {/if}
        </div>
        {if $intro}
            <div>{$intro}</div>
        {/if}
    </a>
    
    {if $weight}
        <div class="mse2-ac-weight d-none">
			<small>{'mse2_weight' | lexicon}: {$weight}0</small>
		</div>
    {/if}
</div>