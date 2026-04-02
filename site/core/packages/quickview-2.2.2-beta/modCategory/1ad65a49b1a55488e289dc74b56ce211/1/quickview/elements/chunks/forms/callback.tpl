
<form action="" method="post" class="{$formSelector} form-horizontal">

    <div class="control-group">
        <label class="control-label" for="af_name">Ваше имя</label>
        <div class="controls">
            <input type="text" id="af_name" name="name" value="[[+fi.name]]" placeholder="" class="form-control" />
            <span class="error_name">[[+fi.error.name]]</span>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="af_phone">Ваш телефон</label>
        <div class="controls">
            <input type="text" id="af_phone" name="phone" value="[[+fi.phone]]" placeholder="" class="form-control" />
            <span class="error_phone">[[+fi.error.phone]]</span>
        </div>
    </div>

    <br>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn btn-primary">Отправить</button>
        </div>
    </div>

</form>