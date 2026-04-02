[[+YdOn:is=`1`:then=`
    <offer id="[[+id]]" type="vendor.model" available="true">
        <typePrefix>[[+YdTypePrefix]]</typePrefix>
        <vendor>[[+YdVendor]]</vendor>
        <model>[[+YdModel]]</model>
        <name>[[+YdName]]</name>
        <description>[[+YdDescription]]</description>
        <picture>[[+YdPicture:isempty=`assets/images/offerimg/[[+offerImg]]`]]</picture>
        <url>{$id | url}</url>
        <price>{$price|replace:' ':''}</price>
        <oldprice>{$old_price|replace:' ':''}</oldprice>
        <currencyId>RUR</currencyId>
        <categoryId>{$parent}</categoryId>
        <sales_notes>[[+YdSales]]</sales_notes>
        <collectionId>{$parent}</collectionId>
        <custom_label_0>[[+YdLable0]]</custom_label_0>
        <custom_label_1>[[+YdLable1]]</custom_label_1>
        <custom_label_2>[[+YdLable2]]</custom_label_2>
    </offer>
`:else=``]] 



