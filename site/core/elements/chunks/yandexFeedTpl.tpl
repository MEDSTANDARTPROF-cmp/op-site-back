<offer id="[[+id]]">
    <name>{$menutitle}</name>
    <url>[[++site_url]]{$id | url}?utm_source=yfeed</url>
    <set-ids>set_{$parent}</set-ids>
    <categoryId>1006</categoryId>
    <description>{$description}</description>
    {if $thumb?}
        <picture>[[++site_url]]{$thumb}</picture>
    {else}
        <picture>[[++site_url]]assets/images/temp/002.jpg</picture>
    {/if}
    <price>{$old_price|replace:' ':''}</price>
    <currencyId>RUB</currencyId>
    <param name="Цена по скидке">{$price|replace:' ':''}</param>
    <param name="Оплата в рассрочку" unit="месяц">12</param>
    <param name="Продолжительность" unit="час">72</param>
    <param name="Формат обучения">Самостоятельно</param>
    <param name="Тип обучения">Курс</param>
    <param name="Есть текстовые уроки">true</param>
    <param name="Есть домашние работы">true</param>
    <param name="Результат обучения">Удостоверение</param>
    <param name="План" order="1" unit="Блок 1">Этот курс нацелен на развитие профессиональных умений и навыков, обогащение знаниями и повышение квалификации в рамках профессионального сообщества. {$pagetitle} предоставляет возможность освоить новую профессию, расширить круг профессиональных компетенций, улучшить финансовое положение или претендовать на более высокие должности.</param>
    <param name="План" order="2" unit="Блок 2">Формирование у слушателей начальных знаний и навыков, направленных на понимание профессиональной области. Изучение терминологии, особенностей специальности.</param>
    <param name="План" order="3" unit="Блок 3">Основная часть: понимание специфики профессии, изучение основ, теоретические знания в профессиональной деятельности, должностные инструкции и характеристики специальности.</param>
    <param name="План" order="3" unit="Блок 3">Получение документа, внесение в госреестр</param>
</offer>