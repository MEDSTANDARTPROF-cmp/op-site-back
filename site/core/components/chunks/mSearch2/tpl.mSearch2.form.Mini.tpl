<form action="{$pageId | url}" method="get" class="msearch2 mse2-home mse2-mini d-flex w-100 " id="mse2_form">
    <div class="input-group border-0 input-group-lg">
        <input type="text" class="form-control z-0 rounded-3 border-black" name="{$queryVar}" value="{$mse2_query}"placeholder="Поиск"/>
        <button type="submit" class="btn btn-primary end-0 position-absolute z-3 h-100 border-black">
           Найти
        </button>
    </div>
</form>

