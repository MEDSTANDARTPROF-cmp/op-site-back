<form action="{$pageId | url}" method="get" class="msearch2 mse2-home d-flex w-100 " id="mse2_form">
    <div class="input-group input-group-lg">
        <input type="text" class="form-control z-0 rounded-3" name="{$queryVar}" value="{$mse2_query}"placeholder="Поиск обучения"/>
        <button type="submit" class="btn btn-primary end-0 position-absolute z-3 h-100">
           Найти
        </button>
    </div>
</form>

