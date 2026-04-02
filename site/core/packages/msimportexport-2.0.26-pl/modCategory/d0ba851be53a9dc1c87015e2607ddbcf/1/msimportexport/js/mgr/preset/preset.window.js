Msie.window.Preset = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_preset_title_window_view'),
        onlyCancelBtn: true,
    });
    Msie.window.Preset.superclass.constructor.call(this, config);
    this.addEvents('addPreset', 'removePreset');

};
Ext.extend(Msie.window.Preset, Msie.window.Default, {

    getFields: function (config) {
        return [{
            xtype: 'msie-grid-preset',
            service: config.service || '',
            mode: config.mode || '',
            listeners: {
                addPreset: {
                    fn: function (preset) {
                        this.fireEvent('addPreset', preset);
                    }, scope: this
                },
                removePreset: {
                    fn: function (ids) {
                        this.fireEvent('removePreset', ids);
                    }, scope: this
                }
            }
        }];
    }
});
Ext.reg('msie-window-preset', Msie.window.Preset);

Msie.window.CreatePreset = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: r.id ? _('msimportexport_preset_title_window_update') : _('msimportexport_preset_title_window_create'),
        baseParams: {
            action: r.id ? 'mgr/preset/update' : 'mgr/preset/create'
        }
    });
    Msie.window.CreatePreset.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.CreatePreset, Msie.window.Default, {
    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'hidden',
            name: 'mode'
        }, {
            xtype: 'hidden',
            name: 'service'
        }, {
            xtype: 'textfield',
            fieldLabel: _('msimportexport_preset_label_name'),
            description: _('msimportexport_preset_label_name_help'),
            name: 'name',
            allowBlank: false,
            anchor: '100%'
        }, {
            xtype: 'textarea',
            fieldLabel: _('msimportexport_preset_label_description'),
            description: _('msimportexport_preset_label_description_help'),
            name: 'description',
            allowBlank: true,
            anchor: '100%'
        }];
    },

});
Ext.reg('msie-window-preset-create', Msie.window.CreatePreset);

Msie.window.UpdatePreset = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Msie.window.UpdatePreset.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.UpdatePreset, Msie.window.CreatePreset);
Ext.reg('msie-window-preset-update', Msie.window.UpdatePreset);

Msie.window.SettingPreset = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: _('msimportexport_preset_title_window_settings') + ' "' + r.name + '"',
        cls: 'msimportexport-window-preset-settings',
        modal: true,
        width: 1024,
        height: 750,
        stateful: false,
        resizable: true,
        collapsible: true,
        maximizable: true,
        autoHeight: false,
        autoScroll: true,
        items: this.getFields(config),
        buttons: this.getButtons(config)
    });
    Msie.window.SettingPreset.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.SettingPreset, Ext.Window, {
    activeForm: null,
    getFields: function (config) {
        return [{
            xtype: 'msie-panel-setting',
            id: 'msie-panel-setting',
            mode: config.record.mode,
            preset: config.record.id,
            fields: config.record.fields,
            settings: config.record.settings,
            parent_class_key: config.record.parent_class_key,
            do_link: config.record.do_link,
            listeners: {
                formchange: {
                    fn: this.onFormChange,
                    scope: this
                }
            },
        }];
    },
    submit: function () {
        if (this.activeForm) {
            this.activeForm.submit();
        }
    },
    getButtons: function (config) {
        return [{
            text: _('save'),
            scope: this,
            cls: 'primary-button',
            iconCls: 'x-btn-small',
            id: 'msie-window-preset-setting-btn-submit',
            handler: this.submit
        }, {
            text: config.cancelBtnText || _('cancel'),
            scope: this,
            handler: function () {
                config.closeAction !== 'close'
                    ? this.hide()
                    : this.close();
            }
        }];
    },
    onFormChange: function (form) {
        if (form) {
            this.activeForm = form;
            Ext.getCmp('msie-window-preset-setting-btn-submit').setText(form.getBtnSubmitText());
        }
    }

});
Ext.reg('msie-window-preset-setting', Msie.window.SettingPreset);

Msie.window.CreateMenuPreset = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_preset_title_window_create_menu'),
        baseParams: {
            action: 'mgr/system/menu/preset/create'
        }
    });
    Msie.window.CreateMenuPreset.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.CreateMenuPreset, Msie.window.Default, {
    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'msie-combo-menu',
            fieldLabel: _('msimportexport_preset_label_menu_parent'),
            description: _('msimportexport_preset_label_menu_parent_help'),
            name: 'parent',
            allowBlank: false,
            anchor: '100%'
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_preset_label_menu_name'),
            description: _('msimportexport_preset_label_menu_name_help'),
            name: 'name',
            allowBlank: false,
            anchor: '100%'
        }, {
            xtype: 'textarea',
            fieldLabel: _('msimportexport_preset_label_menu_description'),
            description: _('msimportexport_preset_label_menu_description_help'),
            name: 'description',
            allowBlank: true,
            anchor: '100%'
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_preset_label_menu_btn_text'),
            description: _('msimportexport_preset_label_menu_btn_text_help'),
            name: 'btn_text',
            allowBlank: false,
            anchor: '100%'
        }, {
            xtype: 'textarea',
            fieldLabel: _('msimportexport_preset_label_menu_page_text'),
            description: _('msimportexport_preset_label_menu_page_text_help'),
            name: 'page_text',
            allowBlank: true,
            anchor: '100%'
        }];
    },

});
Ext.reg('msie-window-preset-menu-create', Msie.window.CreateMenuPreset);

Msie.window.UpdateMenuPreset = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_preset_title_window_update_menu'),
        baseParams: {
            action: 'mgr/system/menu/preset/update'
        }
    });
    Msie.window.UpdateMenuPreset.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.UpdateMenuPreset, Msie.window.CreateMenuPreset);
Ext.reg('msie-window-preset-menu-update', Msie.window.UpdateMenuPreset);