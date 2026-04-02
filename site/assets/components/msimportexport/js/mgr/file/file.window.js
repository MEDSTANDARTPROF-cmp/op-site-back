Msie.window.UploadFile = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('msimportexport_import_window_title_upload_file'),
        saveBtnText: _('msimportexport_import_btn_upload'),
        fileUpload: true,
        baseParams: {
            action: 'mgr/system/file/upload',
            preset: config.preset || 0,
            parse: config.parse || 0
        }
    });
    Msie.window.UploadFile.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.UploadFile, Msie.window.Default, {
    getFields: function (config) {
        return [{
            layout: 'column',
            border: false,
            defaults: {
                layout: 'form',
                labelAlign: 'top',
                border: false,
                cls: 'main-wrapper',
                labelSeparator: ''
            }
            , items: [{
                columnWidth: 1,
                items: [{
                    xtype: config.file ? 'textfield' : 'fileuploadfield',
                    inputType: 'text',
                    fieldLabel: _('msimportexport_import_file'),
                    value: config.file,
                    readOnly: true,
                    name: 'file',
                    anchor: '100%',
                }]
            }]
        }];
    },
});
Ext.reg('msie-window-file-upload', Msie.window.UploadFile);

Msie.window.PreviewFileFields = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_import_window_title_preview_file_fields'),
        width: 950,
        height: 800,
        stateful: false,
        resizable: true,
        collapsible: true,
        maximizable: true,
        autoHeight: false,
        autoScroll: true,
        items: this.getFields(config),
        buttons: this.getButtons(config)
    });
    Msie.window.PreviewFileFields.superclass.constructor.call(this, config);
    this.on('afterrender', this.setup, this);
};
Ext.extend(Msie.window.PreviewFileFields, Ext.Window, {
    setup: function () {
        var mask = new Ext.LoadMask(this.getEl(), {msg: _('msimportexport_import_file_load_fields')});
        mask.show();
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/system/file/getFields',
                file: this.file,
                preset: this.preset
            },
            listeners: {
                success: {
                    fn: function (r) {
                        mask.hide();
                        var panel = Ext.getCmp('msie-preview-panel-setting-field');
                        panel.buildFields(r.object.fields, r.object.values);
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
    getFields: function (config) {
        return [{
            xtype: 'msie-panel-setting-field',
            id: 'msie-preview-panel-setting-field',
            mode: config.mode,
            preset: config.preset,
            service: config.service,
            showBtnLoadFields: false,
        }];
    },
    submit: function () {
        var form = Ext.getCmp('msie-preview-panel-setting-field');
        if (form) {
            form.submit();
        }
    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-floppy-o"></i> ' + _('msimportexport_fields_btn_save'),
            scope: this,
            cls: 'primary-button',
            iconCls: 'x-btn-small',
            handler: this.submit
        }, {
            text: '<i class="icon icon-close"></i> ' + _('cancel'),
            scope: this,
            handler: function () {
                config.closeAction !== 'close'
                    ? this.hide()
                    : this.close();
            }
        }];
    },
});
Ext.reg('msie-window-file-preview-fields', Msie.window.PreviewFileFields);