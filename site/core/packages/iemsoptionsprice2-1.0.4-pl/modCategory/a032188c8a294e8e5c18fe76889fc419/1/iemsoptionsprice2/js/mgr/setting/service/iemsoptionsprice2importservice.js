Msie.service.ieMsOptionsPrice2ImportService = {
    getTabSettings: function (config) {
        return {
            title: _('iemsoptionsprice2_iemsoptionsprice2importservice_setting_tab'),
            id: 'iemsoptionsprice2-setting-tab-iemsoptionsprice2importservice',
            layout: 'form',
            items: [{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iemsoptionsprice2_iemsoptionsprice2importservice_setting_disable_modification'),
                description: '<b>msopm_disable_modification</b>',
                name: 'msopm_disable_modification',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iemsoptionsprice2_iemsoptionsprice2importservice_setting_disable_modification_help'),
                cls: 'desc-under'
            }, {
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iemsoptionsprice2_iemsoptionsprice2importservice_setting_remove_modification'),
                description: '<b>msopm_remove_modification</b>',
                name: 'msopm_remove_modification',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iemsoptionsprice2_iemsoptionsprice2importservice_setting_remove_modification_help'),
                cls: 'desc-under'
            }]
        };
    }
};