IeYandexMarket.grid.PickupOptions = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        stateId: 'ieyandexmarket-pickup-options',
        btnAddRowText: _('ieyandexmarket_options_btn_add_option'),
    });
    IeYandexMarket.grid.PickupOptions.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.grid.PickupOptions, Msie.grid.Local, {
    getFields: function (config) {
        return ['cost', 'days', 'order_before'];
    },
    getColumns: function (config) {
        return [{
            header: _('ieyandexmarket_pickup_options_header_cost'),
            dataIndex: 'cost',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('ieyandexmarket_pickup_options_header_days'),
            dataIndex: 'days',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: _('ieyandexmarket_pickup_options_header_order_before'),
            dataIndex: 'order_before',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }];
    }
});
Ext.reg('ieyandexmarket-grid-pickup-options', IeYandexMarket.grid.PickupOptions);