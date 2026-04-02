Msie.service.ieMsSalePriceImportService = {
    getTabSettings: function (config) {
        return {
            title: _('iemssaleprice_iemssalepriceimportservice_setting_tab'),
            id: 'iemssaleprice-setting-tab-iemssalepriceimportservice',
            layout: 'form',
            items: [{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iemssaleprice_iemssalepriceimportservice_setting_remove_prices'),
                description: '<b>mssp_remove_prices</b>',
                name: 'mssp_remove_prices',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iemssaleprice_iemssalepriceimportservice_setting_remove_prices_help'),
                cls: 'desc-under'
            }]
        };
    }
};