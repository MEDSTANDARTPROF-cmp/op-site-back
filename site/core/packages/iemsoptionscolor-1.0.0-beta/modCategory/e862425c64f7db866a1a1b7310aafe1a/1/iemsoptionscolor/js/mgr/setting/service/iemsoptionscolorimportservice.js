Msie.service.ieMsOptionsColorImportService = {
    getTabSettings: function (config) {
        return {
            title: _('iemsoptionscolor_iemsoptionscolorimportservice_setting_tab'),
            id: 'iemsoptionscolor-setting-tab-iemsoptionscolorimportservice',
            layout: 'form',
            items: [{
                xtype: 'msie-field',
                fieldLabel: _('iemsoptionscolor_iemsoptionscolorimportservice_setting_record_find_fields'),
                description: '<b>msoc_record_find_fields</b>',
                name: 'msoc_record_find_fields',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iemsoptionscolor_iemsoptionscolorimportservice_setting_record_find_fields_help'),
                cls: 'desc-under'
            },{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iemsoptionscolor_iemsoptionscolorimportservice_setting_disable_color'),
                description: '<b>msoc_disable_color</b>',
                name: 'msoc_disable_color',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iemsoptionscolor_iemsoptionscolorimportservice_setting_disable_color_help'),
                cls: 'desc-under'
            }, {
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iemsoptionscolor_iemsoptionscolorimportservice_setting_remove_color'),
                description: '<b>msoc_remove_color</b>',
                name: 'msoc_remove_color',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iemsoptionscolor_iemsoptionscolorimportservice_setting_remove_color_help'),
                cls: 'desc-under'
            }]
        };
    }
};