Msie.service.ieMsProductRemainsImportService = {
    getTabSettings: function (config) {
        return {
            title: _('iemsproductremains_iemsproductremainsimportservice_setting_tab'),
            id: 'iemsproductremains-setting-tab-iemsproductremainsimportservice',
            layout: 'form',
            items: [ {
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iemsoptionsprice2_iemsoptionsprice2importservice_setting_remove_remains'),
                description: '<b>mspr_remove_remains</b>',
                name: 'mspr_remove_remains',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iemsoptionsprice2_iemsoptionsprice2importservice_setting_remove_remains_help'),
                cls: 'desc-under'
            }]
        };
    }
};