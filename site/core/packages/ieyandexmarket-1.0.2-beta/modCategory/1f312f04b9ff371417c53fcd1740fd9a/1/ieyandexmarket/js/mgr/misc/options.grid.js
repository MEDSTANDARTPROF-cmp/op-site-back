IeYandexMarket.grid.Options = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        stateId: 'ieyandexmarket-options' + config.id || Ext.id(),
        btnAddRowText: _('ieyandexmarket_options_btn_add_option'),
    });
    IeYandexMarket.grid.Options.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.grid.Options, Msie.grid.Local, {
    getFields: function (config) {
        return ['cost', 'days', 'order_before'];
    },
    getColumns: function (config) {
        return [{
            header: 'cost',
            dataIndex: 'cost',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: 'days',
            dataIndex: 'days',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }, {
            header: 'order_before',
            dataIndex: 'order_before',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }];
    }
});
Ext.reg('ieyandexmarket-grid-options', IeYandexMarket.grid.Options);