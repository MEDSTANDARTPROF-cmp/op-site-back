Msie.service.MsIeResourceExportService = {
    getTabSettings: function (config) {
        return {
            title: _('msimportexport_resource_export_service_setting_tab'),
            id: 'msie-export-tab-setting-service-resource',
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
                        xtype: 'msie-combo-ctx',
                        fieldLabel: _('msimportexport_settings_ctx'),
                        name: 'ctx',
                        description: '<b>ctx</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('msimportexport_settings_ctx_help'),
                        cls: 'desc-under'
                    }, {
                        xtype: 'msie-combo-boolean',
                        fieldLabel: _('msimportexport_resource_export_service_setting_published_only'),
                        name: 'published_only',
                        description: '<b>published_only</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('msimportexport_resource_export_service_setting_published_only_help'),
                        cls: 'desc-under'
                    }, {
                        xtype: 'msie-combo-boolean',
                        fieldLabel: _('msimportexport_resource_export_service_setting_exclude_deleted'),
                        name: 'exclude_deleted',
                        description: '<b>exclude_deleted</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('msimportexport_resource_export_service_setting_exclude_deleted_help'),
                        cls: 'desc-under'
                    },{
                        xtype: 'fieldset',
                        title: _('msimportexport_resource_export_service_setting_fieldset_utm'),
                        hideLabel: true,
                        collapsible: true,
                        autoHeight: true,
                        stateId: 'ieyandexmarket-export-setting-fieldset-utm',
                        stateful: true,
                        stateEvents: ['collapse', 'expand'],
                        items: [{
                            xtype: 'msie-field',
                            fieldLabel: _('msimportexport_resource_export_service_setting_utm_source'),
                            name: 'utm_source',
                            description: '<b>utm_source</b>',
                            anchor: '100%'
                        },{
                            xtype: 'msie-field',
                            fieldLabel: _('msimportexport_resource_export_service_setting_utm_medium'),
                            name: 'utm_medium',
                            description: '<b>utm_medium</b>',
                            anchor: '100%'
                        },{
                            xtype: 'msie-field',
                            fieldLabel: _('msimportexport_resource_export_service_setting_utm_campaign'),
                            name: 'campaign',
                            description: '<b>utm_campaign</b>',
                            anchor: '100%'
                        }, {
                            xtype: 'label',
                            html: _('msimportexport_resource_export_service_setting_utm_help'),
                            cls: 'desc-under'
                        },{
                            xtype: 'msie-field',
                            fieldLabel: _('msimportexport_resource_export_service_setting_utm_extra_param'),
                            name: 'utm_extra_param',
                            description: '<b>utm_extra_param</b>',
                            anchor: '100%'
                        }, {
                            xtype: 'label',
                            html: _('msimportexport_resource_export_service_setting_utm_extra_param_help'),
                            cls: 'desc-under'
                        }]
                    }]
                }, {
                    columnWidth: .5,
                    items: [{
                        xtype: 'msie-tree-resource',
                        name: 'resources',
                        preset: config.preset,
                        class_key: config.parent_class_key || 'modResource',
                    }]
                }]
            }]
        }
    }
};