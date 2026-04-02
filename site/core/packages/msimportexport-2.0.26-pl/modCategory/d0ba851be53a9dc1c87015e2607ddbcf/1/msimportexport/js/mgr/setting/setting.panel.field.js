Msie.panel.SettingField = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    if (!config.id) {
        config.id = 'msie-panel-setting-field-' + this.ident;
    }
    Ext.applyIf(config, {
        cls: 'main-wrapper msimportexport-panel-setting-field',
        itemSelector: 'div.msimportexport-panel-field',
    });
    Msie.panel.SettingField.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.dragZone = new Msie.FieldDragZone(this);
        this.dropZone = new Msie.FieldDropZone(this);
    }, this);
};
Ext.extend(Msie.panel.SettingField, Msie.panel.SettingForm, {
    getToolBar: function (config) {
        var tbar = [];
        tbar.push({
            xtype: 'tbtext',
            text: _('msimportexport_fields_count')
        });
        tbar.push({
            xtype: 'numberfield',
            id: 'msie-fields-count',
            style: 'text-align: left',
            width: 60,
            value: 1,
        });
        tbar.push({
            xtype: 'button',
            text: '<i class="icon icon-plus"></i> ' + _('msimportexport_fields_btn_add'),
            cls: '',
            listeners: {
                click: {fn: this.onAddNewField, scope: this}
            }
        });

        tbar.push({
            xtype: 'button',
            text: '<i class="icon icon-upload"></i> ' + _('msimportexport_fields_btn_load'),
            cls: 'btn-success',
            listeners: {
                click: {fn: this.onLoadFields, scope: this}
            }
        });

        tbar.push({
            xtype: 'button',
            text: '<i class="icon icon-magic"></i> ' + _('msimportexport_fields_btn_auto_set'),
            tooltip: _('msimportexport_fields_btn_auto_set_desc'),
            cls: 'btn-info',
            listeners: {
                click: {fn: this.onAutoSetValueFields, scope: this}
            }
        });
        tbar.push({
            xtype: 'button',
            text: '<i class="icon icon-trash"></i> ' + _('msimportexport_fields_btn_remove_all'),
            cls: 'btn-danger',
            listeners: {
                click: {fn: this.onRemoveAllFields, scope: this}
            }
        });

        return tbar;
    },
    getListeners: function (config) {
        return {
            afterrender: function () {
                Ext.each(config.fields || [], function (field) {
                    this.addField(field);
                }, this);
            }
        }
    },
    onAddNewField: function () {
        var coutn = Ext.getCmp('msie-fields-count').getValue();
        for (var i = 0; i < coutn; i++) {
            this.addField();
        }

    },
    onLoadFields: function (el, e) {
        var w = Ext.getCmp('msie-window-file-upload');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-file-upload',
            id: 'msie-window-file-upload',
            preset: this.preset || 0,
            parse: true,
            listeners: {
                success: {
                    fn: function (r) {
                        this.removeAll();
                        this.buildFields(r.a.result.object.fields);
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
        w.fp.getForm().reset();
        w.show(e.target);
    },
    onAutoSetValueFields: function () {
        var self = this;
        Ext.MessageBox.show({
            title: _('warning'),
            msg: _('msimportexport_fields_warning_auto_set'),
            width: 450,
            buttons: Ext.MessageBox.OKCANCEL,
            icon: Ext.MessageBox.WARNING,
            fn: function (btn, text) {
                if (btn == 'ok') {
                    Ext.each(self.items.getRange() || [], function (field) {
                        field.setValue(null, true);
                    }, this);
                }
            }
        });
    },
    onRemoveAllFields: function () {
        if (!this.items.getRange().length) return;
        Ext.MessageBox.confirm(
            _('msimportexport_fields_title.window_remove'),
            _('msimportexport_fields_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.removeAll();
                }
            }, this
        );
    },
    buildFields: function (fields, values) {
        var value;
        Ext.each(fields || [], function (field, index) {
            value = '';
            if (values && values[index]) value = values[index];
            this.addField(value, field);
        }, this);
    },
    addField: function (value, label) {
        var item = {
            xtype: 'msie-panel-field',
            parent: this,
            mode: this.mode,
            preset: this.preset || 0,
            value: value || '',
            index: this.items.length + 1,
            listeners: {
                remove: {
                    fn: this.removeField,
                    scope: this
                }
            }
        };
        var field = this.add(item);
        this.doLayout();
        if (label) field.setLabel(label);
        this.refreshFields();
    },
    refreshFields: function () {
        this.sortFields();
        Ext.each(this.items.getRange() || [], function (field, index) {
            field.setIndex(index + 1);
            field.refreshLabel();
        }, this);
    },
    sortFields: function () {
        this.items.sort('ASC', function (field1, field2) {
            if (field1.getIndex() == field2.getIndex()) return 0;
            return (field1.getIndex() < field2.getIndex()) ? -1 : 1;
        });
    },
    removeField: function (field) {
        this.remove(field, true);
        this.refreshFields();
    },
    getFieldValues: function () {
        var values = [];
        Ext.each(this.items.getRange() || [], function (field, index) {
            values.push(field.getValue());
        }, this);
        return values;
    },
    submit: function (e) {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/settings/preset/update',
                fields: Ext.encode(this.getFieldValues()),
                id: this.preset
            },
            listeners: {
                success: {
                    fn: function (r) {
                        MODx.msg.status({
                            title: _('success'),
                            message: r.message ? r.message : _('msimportexport_preset_success_save_fields'),
                            dontHide: false
                        });

                        if(r.object.warning) {
                            MODx.msg.alert(_('warning'), r.object.warning);
                        }
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
    },
    getBtnSubmitText: function () {
        return '<i class="icon icon-floppy-o"></i> ' + _('msimportexport_fields_btn_save');
    },
});
Ext.reg('msie-panel-setting-field', Msie.panel.SettingField);