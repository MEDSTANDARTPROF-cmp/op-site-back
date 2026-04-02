IeYandexMarket.combo.defaultCurrency = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                ['RUB', 'RUB'],
                ['EUR', 'EUR'],
                ['USD', 'USD'],
                ['UAH', 'UAH'],
                ['BYR', 'BYR'],
                ['KZT', 'KZT'],
            ]
        }),
        hiddenName: config.name || '',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: true,
        typeAhead: true,
        minChars: 2,
        preventRender: true,
        forceSelection: true,
    });
    IeYandexMarket.combo.defaultCurrency.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.combo.defaultCurrency, Msie.combo.ComboBox);
Ext.reg('ieyandexmarket-combo-default-currency', IeYandexMarket.combo.defaultCurrency);

IeYandexMarket.combo.RateCurrency = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                ['CBRF — курс по Центральному банку РФ', 'CBRF'],
                ['NBU — курс по Национальному банку Украины', 'NBU'],
                ['NBK — курс по Национальному банку Казахстана', 'NBK'],
                ['CB — курс по банку той страны, к которой относится магазин', 'CB'],
            ]
        }),
        hiddenName: config.name || '',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: true,
        typeAhead: true,
        minChars: 2,
        preventRender: true,
        forceSelection: true,
    });
    IeYandexMarket.combo.RateCurrency.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.combo.RateCurrency, Msie.combo.ComboBox);
Ext.reg('ieyandexmarket-combo-rate-currency', IeYandexMarket.combo.RateCurrency);

IeYandexMarket.combo.Currencies = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'superboxselect',
        allowBlank: true,
        msgTarget: 'under',
        allowAddNewData: false,
        addNewDataOnBlur: false,
        pinList: false,
        resizable: true,
        editable: false,
        preventRender: true,
        name: config.name || 'currencies',
        ctCls: 'msimportexport-field',
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v', 'rank'],
            data: [
                ['RUB', 'RUB'],
                ['EUR', 'EUR'],
                ['USD', 'USD'],
                ['UAH', 'UAH'],
                ['BYR', 'BYR'],
                ['KZT', 'KZT'],
            ],
            remoteSort: true,
        }),
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls: 'x-form-trigger',
        clearBtnCls: 'x-form-trigger',
        displayFieldTpl: config.displayFieldTpl || '{d}',
    });

    IeYandexMarket.combo.Currencies.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.combo.Currencies, Ext.ux.form.SuperBoxSelect);
Ext.reg('ieyandexmarket-combo-currencies', IeYandexMarket.combo.Currencies);

IeYandexMarket.combo.SalePricePrices = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        minChars: 1,
        editable: true,
        allowAddNewData: true,
        addNewDataOnBlur: true,
        url: IeYandexMarket.config.connector_url,
        action: 'mgr/saleprice/price/getlist',
    });

    IeYandexMarket.combo.SalePricePrices.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.combo.SalePricePrices, Msie.combo.MultiSelect);
Ext.reg('ieyandexmarket-combo-saleprice-prices', IeYandexMarket.combo.SalePricePrices);

IeYandexMarket.combo.ProductField = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        url: IeYandexMarket.config.connector_url,
        baseParams: {
            action: 'mgr/product/field/getlist',
            combo: true,
        },
        triggerConfig: {
            tag: 'span',
            cls: 'x-field-combo-btns',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger',
                    trigger: ''
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-clear',
                    trigger: 'clear',
                    title: _('msimportexport_fields_btn_clear')
                }
            ]
        },

    });
    IeYandexMarket.combo.ProductField.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.combo.ProductField, Msie.combo.Field);
Ext.reg('ieyandexmarket-combo-product-field', IeYandexMarket.combo.ProductField);

IeYandexMarket.combo.MultiSelectProductField = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        url: IeYandexMarket.config.connector_url,
        action: 'mgr/product/field/getlist',
        displayField: 'display',
        valueField: 'key',
        displayFieldTpl: config.displayFieldTpl || '{display}',
        fields: ['key', 'name', 'alias', 'label', {
            name: 'display',
            convert: function (v, rec) {
                var display = rec.name;
                if (rec.alias) {
                    display += ' - ' + rec.alias;
                }
                return display;
            }
        }]
    });
    IeYandexMarket.combo.MultiSelectProductField.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.combo.MultiSelectProductField, Msie.combo.MultiSelect);
Ext.reg('ieyandexmarket-combo-multi-select-product-field', IeYandexMarket.combo.MultiSelectProductField);

IeYandexMarket.combo.AllowedTags = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'superboxselect',
        allowBlank: true,
        msgTarget: 'under',
        allowAddNewData: true,
        addNewDataOnBlur: true,
        pinList: false,
        resizable: true,
        editable: true,
        preventRender: true,
        name: config.name || 'allowed_tags',
        ctCls: 'msimportexport-field',
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                ['a', '<a>'],
                ['b', '<b>'],
                ['br', '<br>'],
                ['h1', '<h1>'],
                ['h2', '<h2>'],
                ['h3', '<h3>'],
                ['h4', '<h4>'],
                ['h5', '<h5>'],
                ['h6', '<h6>'],
                ['i', '<i>'],
                ['li', '<li>'],
                ['ol', '<ol>'],
                ['p', '<p>'],
                ['small', '<small>'],
                ['span', '<span>'],
                ['strong', '<strong>'],
                ['u', '<u>'],
                ['ul', '<ul>'],
            ],
            remoteSort: true,
        }),
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls: 'x-form-trigger',
        clearBtnCls: 'x-form-trigger',
        displayFieldTpl: config.displayFieldTpl || '{d}',
    });
    IeYandexMarket.combo.AllowedTags.superclass.constructor.call(this, config);
    this.on('newitem', function (comp, v) {
        comp.addNewItem({d: v, v: v});
    });
};
Ext.extend(IeYandexMarket.combo.AllowedTags, Ext.ux.form.SuperBoxSelect);
Ext.reg('ieyandexmarket-combo-allowed-tags', IeYandexMarket.combo.AllowedTags);

IeYandexMarket.combo.OfferType = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v', 'desc'],
            data: [
                [_('ieyandexmarket_offer_type_tour'), 'tour', 'tour'],
                [_('ieyandexmarket_offer_type_book'), 'book', 'book'],
                [_('ieyandexmarket_offer_type_audiobook'), 'audiobook', 'audiobook'],
                [_('ieyandexmarket_offer_type_simple'), 'simple', ''],
                [_('ieyandexmarket_offer_type_custom'), 'vendor.model', 'vendor.model'],
                [_('ieyandexmarket_offer_type_artist_title'), 'artist.title', 'artist.title'],
                [_('ieyandexmarket_offer_type_event_ticket'), 'event-ticket', 'event-ticket'],

            ]
        }),
        hiddenName: config.name || '',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true,
        forceSelection: true,
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{d}</span>',
            '<tpl if="desc"><br><span style="font-style:italic">{desc}</span></tpl></div></tpl>'
        ),
    });
    IeYandexMarket.combo.OfferType.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.combo.OfferType, Msie.combo.ComboBox);
Ext.reg('ieyandexmarket-combo-offer-type', IeYandexMarket.combo.OfferType);