Msie.panel.Export = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        btnRunText: _('msimportexport_export_btn_export'),
        tbar: this.getTopBar(config),
    });
    Msie.panel.Export.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.Export, Msie.panel.ImportExport, {
    setup: function (config) {
        this.cmpFormat = Ext.getCmp('msie-combo-export-format');
        Msie.panel.Export.superclass.setup.call(this, config);
        if (config.setupData.preset) {
            this.cmpService.reload({}, config.setupData.service);
            this.cmpPreset.reload({service: config.setupData.service}, config.setupData.preset, (records) => {
                this.cmpPreset.enable();
                const record = this.cmpPreset.getRecord(config.setupData.preset);
                this.cmpPreset.fireEvent('select', this.cmpPreset, record, this.cmpPreset.store.indexOf(record));
            });
        }
    },
    getTopBar: function (config) {
        return ['->', {
            text: '<i class="icon icon-bars"></i> ' + _('msimportexport_page_menu_task'),
            handler: Msie.utils.goToTaskManager,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm primary-button',
        }, {
            text: '<i class="icon icon-download"></i> ' + _('msimportexport_page_menu_import'),
            handler: Msie.utils.goToImport,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        }, {
            text: '<i class="icon icon-cog"></i> ' + _('msimportexport_page_menu_settings'),
            handler: Msie.utils.goToSysSettings,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        }];
    },
    getFields: function (config) {
        var fields = [{
            xtype: 'msie-combo-service',
            id: 'msie-combo-import-export-service',
            mode: Msie.config.mode,
            fieldLabel: _('msimportexport_export_service'),
            anchor: '100%',
            listeners: {
                select: {
                    fn: this.onServiceChange,
                    scope: this
                }
            }

        }, {
            xtype: 'msie-combo-preset',
            id: 'msie-combo-import-export-preset',
            fieldLabel: _('msimportexport_export_preset'),
            mode: Msie.config.mode,
            full: true,
            disabled: true,
            allowBlank: false,
            anchor: '100%',
            listeners: {
                select: {
                    fn: this.onPresetChange,
                    scope: this
                },
                setting: {
                    fn: this.onPresetSetting,
                    scope: this
                },
                add: {
                    fn: this.onPresetAdd,
                    scope: this
                },
                list: {
                    fn: this.onPresetList,
                    scope: this
                }
            }
        }, {
            xtype: 'msie-combo-export-format',
            id: 'msie-combo-export-format',
            fieldLabel: _('msimportexport_export_file_format'),
            anchor: '100%',
            listeners: {
                select: {
                    fn: this.onFormatChange,
                    scope: this
                },
            }
        }];
        fields.push(this.getControlPanel(config));
        fields.push(this.getReportPanel(config));
        return fields;
    },
    onServiceChange: function (combo, records) {
        this.cmpPreset.reload({service: combo.getValue()}, '');
        this.cmpPreset.enable();
        this.cmpFormat.reload({preset: 0}, '');
        this.reset();
    },
    onFormatChange: function (combo, records) {
        this.showControl();
    },
    onPresetChange: function (combo, records) {
        this.reset();
        this.cmpFormat.reload({preset: combo.getValue()}, '');
        if (records.data.settings.export_format) {
            this.cmpFormat.setValue(records.data.settings.export_format);
            this.showControl();
        }
    },
    onPresetAdd: function (combo) {
        var record = {
            service: this.cmpService.getValue(),
            mode: Msie.config.mode
        };
        var w = Ext.getCmp('msie-window-preset-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-preset-create'
            , id: 'msie-window-preset-create'
            , record: record
            , listeners: {
                success: {
                    fn: function (r) {
                        this.cmpPreset.reload({}, r.a.result.object.id);
                        this.cmpPreset.setTriggerDisable(false, 'settings');
                        this.cmpFormat.reload({preset: r.a.result.object.id}, '');
                        this.reset();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(record);
        w.show(combo.target);
    },
    onPresetSetting: function (combo) {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/preset/get',
                id: combo.getValue()
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msie-window-preset-setting');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msie-window-preset-setting',
                            id: 'msie-window-preset-setting',
                            record: r.object
                        });
                        w.show(combo.target);
                    }, scope: this
                }
            }
        });
    },
    onPresetList: function (combo) {
        var w = Ext.getCmp('msie-window-preset');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-preset',
            id: 'msie-window-preset',
            mode: Msie.config.mode,
            service: this.cmpService.getValue(),
            listeners: {
                addPreset: {
                    fn: function () {
                        this.cmpPreset.reload();
                        this.reset();
                    }, scope: this
                },
                removePreset: {
                    fn: function () {
                        this.cmpPreset.reload();
                        this.reset();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.show();
    },
    run: function () {
        this.cmpBtnRun.setDisabled(true);
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/export/run',
                preset: this.cmpPreset.getValue(),
                format: this.cmpFormat.getValue(),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.showReport(r.object.id);
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        this.cmpBtnRun.setDisabled(false);
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        });
    },
    completed: function (data) {
        this.cmpBtnRun.setDisabled(false);
        if (!this.getPresetSetting('save_to_file')) {
            var url = Msie.config.doUrl + '?act=download&token=' + data.token;
            window.open(url, '_blank');
        }
    },
});
Ext.reg('msie-panel-export', Msie.panel.Export);