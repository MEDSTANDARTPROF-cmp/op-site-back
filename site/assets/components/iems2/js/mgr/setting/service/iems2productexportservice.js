Msie.service.IeMs2ProductExportService = {
    getTabSettings: function (config) {
        return {
            title: _('iems2_iems2productexportservice_setting_tab'),
            id: 'iems2-setting-tab-iems2productexportservice',
            layout: 'form',
            items: [{
                layout: 'column',
                defaults: {
                    layout: 'form',
                    labelAlign: 'top'
                },
                items: [{
                    columnWidth: .5,
                    items: [{
                        xtype: 'msie-field',
                        fieldLabel: _('iems2_iems2productexportservice_setting_price_format'),
                        name: 'price_format',
                        description: '<b>price_format</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('iems2_iems2productexportservice_setting_price_format_help'),
                        cls: 'desc-under'
                    }, {
                        xtype: 'msie-combo-boolean',
                        fieldLabel: _('iems2_iems2productexportservice_setting_price_format_no_zeros'),
                        name: 'price_format_no_zeros',
                        description: '<b>price_format_no_zeros</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('iems2_iems2productexportservice_setting_price_format_no_zeros_help'),
                        cls: 'desc-under'
                    }, {
                        xtype: 'msie-combo-boolean',
                        fieldLabel: _('iems2_iems2productexportservice_setting_allow_price_modification'),
                        name: 'allow_price_modification',
                        description: '<b>allow_price_modification</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('iems2_iems2productexportservice_setting_allow_price_modification_help'),
                        cls: 'desc-under'
                    }, {
                        xtype: 'msie-combo-boolean',
                        fieldLabel: _('iems2_iems2productexportservice_setting_multicategory_format'),
                        name: 'multicategory_format',
                        description: '<b>multicategory_format</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('iems2_iems2productexportservice_setting_multicategory_format_help'),
                        cls: 'desc-under'
                    }]
                }, {
                    columnWidth: .5,
                    items: [{
                        xtype: 'iems2-tree-vendor',
                        name: 'vendors',
                        preset: config.preset,
                    }]
                }]
            }]
        };
    }
};