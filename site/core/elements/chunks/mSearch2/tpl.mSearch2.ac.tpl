<div class="mse2-ac-item">
    {$idx}. {$pagetitle}
    {if $weight}
        <span class="mse2-ac-weight d-none">
			<small>{'mse2_weight' | lexicon}: {$weight}</small>
		</span>
    {/if}
    {if $intro}
        <br>
        <small>{$intro}</small>
    {/if}
</div>