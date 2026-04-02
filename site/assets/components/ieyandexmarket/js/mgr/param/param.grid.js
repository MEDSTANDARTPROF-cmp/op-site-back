IeYandexMarket.grid.Param = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        stateId: 'ieyandexmarket-param',
        btnAddRowText: _('ieyandexmarket_param_btn_add'),
    });
    IeYandexMarket.grid.Param.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket.grid.Param, Msie.grid.Local, {
    getFields: function (config) {
        return ['field', 'unit'];
    },
    getColumns: function (config) {
        return [{
            header: _('ieyandexmarket_param_header_field'),
            dataIndex: 'field',
            sortable: true,
            editor: {
                xtype: 'ieyandexmarket-combo-product-field',
                renderer: true
            },
        },{
            header: _('ieyandexmarket_param_header_name'),
            dataIndex: 'name',
            sortable: true,
            editor: {
                xtype: 'textfield',
                renderer: true
            },
        }, {
            header: _('ieyandexmarket_param_header_unit'),
            dataIndex: 'unit',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }];
    }
});
Ext.reg('ieyandexmarket-grid-param', IeYandexMarket.grid.Param);