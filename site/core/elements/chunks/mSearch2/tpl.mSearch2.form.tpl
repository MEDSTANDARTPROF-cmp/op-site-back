<form action="{$pageId | url}" method="get" class="msearch2 d-flex w-100 " id="mse2_form">
    <div class="border border-2 border-dark border-opacity-50 input-group input-group-lg med-serch rounded rounded-3 shadow-none">
        <input type="text" class="form-control z-0 rounded-3" name="{$queryVar}" value="{$mse2_query}"placeholder="Поиск" style="height: 49px;"/>
        
        <button type="submit" class="btn btn-primary end-0 position-absolute z-3 h-100">
           Найти
        </button>
        
    </div>
</form>

