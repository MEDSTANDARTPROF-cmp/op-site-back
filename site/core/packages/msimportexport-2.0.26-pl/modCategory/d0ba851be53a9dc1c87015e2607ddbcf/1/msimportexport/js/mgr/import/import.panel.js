Msie.panel.Import = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        btnRunText: _('msimportexport_import_btn_import'),
        tbar: this.getTopBar(config)
    });
    Msie.panel.Import.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.Import, Msie.panel.ImportExport, {
    setup: function (config) {
        this.cmpFile = Ext.getCmp('msie-filename');
        Msie.panel.Import.superclass.setup.call(this, config);
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
            text: '<i class="icon icon-upload"></i> ' + _('msimportexport_page_menu_export'),
            handler: Msie.utils.goToExport,
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
            fieldLabel: _('msimportexport_import_service'),
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
            fieldLabel: _('msimportexport_import_preset'),
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
            xtype: 'msie-field-upload',
            id: 'msie-filename',
            fieldLabel: _('msimportexport_import_upload'),
            anchor: '100%',
            disabled: true,
            editable: false,
            listeners: {
                upload: {
                    fn: this.onUploadFile,
                    scope: this
                },
                view: {
                    fn: this.onPreviewFileFields,
                    scope: this
                },
            }
        }, {
            xtype: 'label',
            html: _('msimportexport_import_upload_help'),
            cls: 'desc-under'
        }];
        fields.push(this.getControlPanel(config));
        fields.push(this.getReportPanel(config));
        return fields;

    },
    onServiceChange: function (combo, records) {
        this.cmpPreset.reload({service: combo.getValue()}, '');
        this.cmpPreset.enable();
        this.cmpFile.disable();
        this.reset();
    },
    onPresetChange: function (combo, records) {
        this.cmpFile.enable();
        this.cmpFile.setValue('');
        this.reset();
        if (Msie.utils.isEmpty(records.data.settings) || !records.data.settings.file) return;
        var mask = new Ext.LoadMask(this.getEl(), {msg: _('msimportexport_import_file_existence_check')});
        mask.show();
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/system/file/exists',
                file: records.data.settings.file
            },
            listeners: {
                success: {
                    fn: function () {
                        mask.hide();
                        this.cmpFile.setValue(records.data.settings.file);
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        mask.hide();
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
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
                        this.cmpFile.setValue('');
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
    onUploadFile: function (el) {
        var w = Ext.getCmp('msie-window-file-upload');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-file-upload',
            id: 'msie-window-file-upload',
            preset: this.cmpPreset.getValue(),
            file: this.isPostFile() ? '' : this.cmpFile.getValue(),
            listeners: {
                success: {
                    fn: function (r) {
                        this.cmpFile.setValue(r.a.result.object);
                        this.reset();
                        this.showControl();
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
        w.show(el.target);

    },
    onPreviewFileFields: function (el) {
        var file = this.cmpFile.getFile();
        if (!file) {
            MODx.msg.alert(_('warning'), _('msimportexport_import_warning_not_uploaded_file'));
            return;
        }
        var w = Ext.getCmp('msie-window-file-preview-fields');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-file-preview-fields',
            id: 'msie-window-file-preview-fields',
            file: file,
            mode: Msie.config.mode,
            preset: this.cmpPreset.getValue(),
            service: this.cmpService.getValue(),
            listeners: {}
        });
        w.show();
    },
    run: function () {
        this.cmpBtnRun.setDisabled(true);
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/import/run',
                preset: this.cmpPreset.getValue(),
                file: this.cmpFile.getFile(),
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

    },
    isPostFile: function () {
        var file = this.cmpFile.getValue();
        if (!file) return true;
        if (file[0] === '/' || file.indexOf('http:') > -1 || file.indexOf('https:') > -1) return false;
        return true;
    },
});
Ext.reg('msie-panel-import', Msie.panel.Import);