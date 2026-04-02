IeYandexMarket.grid.DeliveryOptions = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        stateId: 'ieyandexmarket-delivery-options',
        btnAddRowText: _('ieyandexmarket_delivery_options_btn_add_option'),
    });
    IeYandexMarket.grid.DeliveryOptions.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.grid.DeliveryOptions, Msie.grid.Local, {
    getFields: function (config) {
        return ['cost', 'days', 'order_before'];
    },
    getColumns: function (config) {
        return [{
            header: _('ieyandexmarket_delivery_options_header_cost'),
            dataIndex: 'cost',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('ieyandexmarket_delivery_options_header_days'),
            dataIndex: 'days',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('ieyandexmarket_delivery_options_header_order_before'),
            dataIndex: 'order_before',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }];
    }
});
Ext.reg('ieyandexmarket-grid-delivery-options', IeYandexMarket.grid.DeliveryOptions);