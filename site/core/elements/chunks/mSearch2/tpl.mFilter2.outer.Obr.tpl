<div class="row msearch2" id="mse2_mfilter">
    <div class="col-md-12">
        <p class="h6 mb-3"><span class="opacity-75">Всего программ обучения</span> <b id="mse2_total">{$total ?: 0}</b> <span class="opacity-75">“[[*pagetitle]]”</span> </p>
        <div class="row">
            <div id="mse2_sort" class="col-md-6 d-none">
                {'mse2_sort' | lexicon}
                <a href="#" data-sort="resource|publishedon"
                   data-dir="{if $sort == 'resource|publishedon:desc'}desc{/if}" data-default="desc" class="sort">
                    {'mse2_sort_publishedon' | lexicon} <span></span>
                </a>
            </div>

            {if $tpls}
                <div id="mse2_tpl" class="col-md-6">
                    <a href="#" data-tpl="0" class="{$tpl0}">{'mse2_chunk_default' | lexicon}</a> /
                    <a href="#" data-tpl="1" class="{$tpl1}">{'mse2_chunk_alternate' | lexicon}</a>
                </div>
            {/if}
        </div>

        <div id="mse2_selected_wrapper"  class=" d-none">
            <div id="mse2_selected">
                {'mse2_selected' | lexicon}:<span></span>
            </div>
        </div>

        <div id="mse2_results">
            {$results}
        </div>

        <div class="mse2_pagination">
            {'page.nav' | placeholder}
        </div>
    </div>
    [[0!
    
    <div class="col-md-3 d-none">
        <form action="{$_modx->resource.id | url}" method="post" id="mse2_filters">
            <div>
                {$filters}
            </div>
            {if $filters}
                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-light hidden">{'mse2_reset' | lexicon}</button>
                    <button type="submit" class="btn btn-primary hidden">{'mse2_submit' | lexicon}</button>
                </div>
            {/if}
        </form>

        <br/><br/>
        <div class="form-group">
            <label for="mse2_limit">{'mse2_limit' | lexicon}</label>
            <select name="mse_limit" id="mse2_limit" class="form-control">
                {foreach [6, 10, 25, 50] as $v}
                    <option value="{$v}"{if $limit == $v} selected{/if}>{$v}</option>
                {/foreach}
            </select>
        </div>
    </div>
    ]]
</div>