Msie.service.MsIeMSearch2ImportService = {
    getTabSettings: function (config) {
        return {
            title: _('msimportexport_msearch2_import_service_setting_tab'),
            id: 'msie-import-tab-setting-service-msearch2',
            layout: 'form',
            items: [{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('msimportexport_msearch2_import_service_setting_disable_indexing'),
                name: 'msearch2_disable_indexing',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_msearch2_import_service_setting_disable_indexing_help'),
                cls: 'desc-under'
            }]
        }
    }
};