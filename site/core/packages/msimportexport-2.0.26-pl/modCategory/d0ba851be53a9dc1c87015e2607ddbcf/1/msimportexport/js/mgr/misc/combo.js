Msie.combo.ComboBox = function (config) {
    Ext.applyIf(config, {
        hiddenName: config.name || '',
        ctCls: 'msimportexport-combo',
        triggerConfig: {
            tag: 'span',
            cls: 'x-field-combo-btns',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger',
                    trigger: ''
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-clear',
                    trigger: 'clear',
                }
            ]
        }
    });
    Msie.combo.ComboBox.superclass.constructor.call(this, config);
    this.addEvents('clear');
};
Ext.extend(Msie.combo.ComboBox, MODx.combo.ComboBox, {
    onTriggerClick: function (event, btn) {
        if (this.disabled) return;
        switch (btn.getAttribute('trigger')) {
            case 'clear':
                this.clearValue();
                this.fireEvent('clear', this);
                break;
            default:
                MODx.combo.ComboBox.superclass.onTriggerClick.call(this);
        }
    }
});
Ext.reg('msie-combo', Msie.combo.ComboBox);

Msie.field.Field = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'msimportexport-field',
        msgTarget: 'under',
        triggerAction: 'all',
        onTrigger1Click: this._triggerClear
    });
    Msie.field.Field.superclass.constructor.call(this, config);
    this.addEvents('clear');
};
Ext.extend(Msie.field.Field, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns one',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-clear'
                }
            ]
        };
    },
    _triggerClear: function () {
        Ext.form.TwinTriggerField.superclass.setValue.call(this, '');
        this.fireEvent('clear', this);
    }
});
Ext.reg('msie-field', Msie.field.Field);

Msie.combo.Boolean = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('yes'), 1],
                [_('no'), 0],
            ]
        }),
        displayField: 'd',
        valueField: 'v',
        hiddenName: config.name || '',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        forceSelection: true,
        listeners: {
            afterrender: function (combo) {
                var val = this.getValue();
                if (val == 'false' || val == false) {
                    val = 0;
                } else if (val == 'true' || val == true) {
                    val = 1;
                }
                this.setValue(val);
            }
        }
    });
    Msie.combo.Boolean.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Boolean, Msie.combo.ComboBox);
Ext.reg('msie-combo-boolean', Msie.combo.Boolean);

Msie.combo.MultiSelect = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'superboxselect',
        allowBlank: true,
        msgTarget: 'under',
        allowAddNewData: false,
        addNewDataOnBlur: false,
        pinList: false,
        resizable: true,
        anchor: '100%',
        store: new Ext.data.JsonStore({
            root: 'results',
            autoLoad: true,
            autoSave: false,
            totalProperty: 'total',
            fields: config.fields || ['id', 'name'],
            url: config.url || Msie.config.connector_url,
            baseParams: {
                combo: true,
                action: config.action,
            }
        }),
        mode: 'remote',
        displayField: 'name',
        valueField: 'id',
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls: 'x-form-trigger',
        clearBtnCls: 'x-form-trigger',
        displayFieldTpl: config.displayFieldTpl || '{name}',
    });
    // config.name += '[]';

    Ext.apply(config, {
        listeners: {
            newitem: {
                fn: this.newitem,
                scope: this
            },
        }
    });

    Msie.combo.MultiSelect.superclass.constructor.call(this, config);
    this.store.on('load', function () {
        if (this.remoteLookup.length) {
            this.addValue(this.remoteLookup);
            this.remoteLookup = [];
        }
    }, this);
};
Ext.extend(Msie.combo.MultiSelect, Ext.ux.form.SuperBoxSelect, {
    addValue: function (value) {
        if (Ext.isEmpty(value)) {
            return;
        }
        var values = value;
        if (!Ext.isArray(value)) {
            value = '' + value;
            values = value.split(this.valueDelimiter);
        }
        Ext.each(values, function (val) {
            var record = this.findRecord(this.valueField, val);
            if (record) {
                this.addRecord(record);
                return true;
            }
            this.remoteLookup.push(val);
        }, this);
        if (this.mode === 'remote' && this.store.autoLoad == false) {
            var q = this.remoteLookup.join(this.queryValuesDelimiter);
            this.doQuery(q, false, true);
        }
    },
    newitem: function (comp, v) {
        var obj = {};
        obj[this.valueField] = v;
        obj[this.displayField] = v;
        comp.addItem(obj);
    },
    shouldQuery: function (q) {
        if (this.lastQuery) {
            return (q !== this.lastQuery);
        }
        return true;
    },
});
Ext.reg('msie-multi-select', Msie.combo.MultiSelect);

Msie.combo.Clipboard = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        allowBlank: false,
        msgTarget: 'under',
        triggerAction: 'all',
        ctCls: 'msimportexport-field',
        onTrigger1Click: this._triggerCopy
    });
    Msie.combo.Clipboard.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Clipboard, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns one',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-copy',
                    title: _('msimportexport_copy_to_clipboard')
                }
            ]
        };
    },

    _triggerCopy: function () {
        Clipboard.copy(this.getValue());
        MODx.msg.status({
            title: _('success'),
            message: _('msimportexport_copied_to_clipboard'),
            dontHide: false
        });
    }
});
Ext.reg('msie-field-clipboard', Msie.combo.Clipboard);

Msie.combo.Preset = function (config) {
    config = config || {};
    if (config.full) {
        config.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns four',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger',
                    trigger: '',
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-settings trigger-disable',
                    trigger: 'setting',
                    title: _('msimportexport_import_btn_preset_settings')
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-plus trigger-disable',
                    trigger: 'add',
                    title: _('msimportexport_import_btn_preset_add')
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-list trigger-disable',
                    trigger: 'list',
                    title: _('msimportexport_import_btn_preset_list')
                }
            ]
        }
    }
    Ext.applyIf(config, {
        displayField: 'name',
        valueField: 'id',
        fields: ['name', 'id', 'service', 'mode', 'settings'],
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Msie.config.connector_url,
        hiddenName: config.name || 'preset',
        baseParams: {
            action: 'mgr/preset/getlist',
            mode: config.mode || '',
            service: config.service || '',
            combo: true
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <b>{name}</b> ({id})<br>\
                        <tpl if="mode"><small>{mode:this.renderMode}</small></tpl>\
                    </span>\
                </div>\
            </tpl>',
            {
                compiled: true,
                renderMode: function (value, record) {
                    var mode = value || record['key'];
                    mode = _('msimportexport_preset_mode') + ': ' + _('msimportexport_preset_mode_' + mode) || mode;
                    return mode;
                }
            }
        ),
    });
    Msie.combo.Preset.superclass.constructor.call(this, config);
    this.addEvents('setting', 'add', 'list');
    this.store.on('load', function (e, r) {
        if (this.val !== null) {
            if (this.val != '' && this.hasValue(this.val)) {
                this.setValue(this.val);
            } else {
                this.setValue('');
            }
            this.val = null;
        }
    }, this);
    this.on('select', this.onSelectPreset, this);
};
Ext.extend(Msie.combo.Preset, Msie.combo.ComboBox, {
    val: null,
    reload: function (params, value, callback) {
        var oldValue = this.getValue();
        this.val = value == undefined ? oldValue : value;
        params = Ext.apply(this.baseParams, params || {});
        this.getStore().reload({params: params, callback: callback});
    },
    hasValue: function (value) {
        var match = false;
        this.getStore().each(function (item) {
            if (item.data[this.valueField] == value) {
                match = true;
                return false;
            }
        }, this);
        return match;
    },
    setValue: function (value) {
        if (!this.loaded && value) {
            this.reload();
        } else {
            MODx.combo.ComboBox.superclass.setValue.call(this, value);
        }
        if (!value) {
            this.setTriggerDisable(true, 'settings');
        }
    },
    getRecord: function (value) {
        if (this.loaded && value) {
            return this.findRecord(this.valueField, value);
        }
        return null;
    },
    enable: function () {
        MODx.combo.ComboBox.superclass.enable.call(this);
        this.setTriggerDisable(false, 'list');
        this.setTriggerDisable(false, 'plus');
    },
    onTriggerClick: function (event, btn) {
        if (this.disabled) return;
        var trigger = btn.getAttribute('trigger');
        switch (trigger) {
            case 'setting':
                if (!this.getValue()) return;
                this.fireEvent(trigger, this);
                break;
            case 'add':
            case 'list':
                this.fireEvent(trigger, this);
                break;
            default:
                MODx.combo.ComboBox.superclass.onTriggerClick.call(this);
        }
    },
    onSelectPreset: function (combo, record) {
        this.setTriggerDisable(false, 'settings');
    }
});
Ext.reg('msie-combo-preset', Msie.combo.Preset);

Msie.combo.Service = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        valueField: 'key',
        fields: ['key', 'name', 'description', 'extensions', 'home_link'],
        pageSize: 20,
        // typeAhead: true,
        //  editable: true,
        minChars: 2,
        forceSelection: true,
        url: Msie.config.connector_url,
        hiddenName: config.name || 'service',
        baseParams: {
            action: 'mgr/service/getlist',
            mode: config.mode,
            combo: true
        },
        triggerConfig: {
            tag: 'span',
            cls: 'x-field-combo-btns three',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger',
                    trigger: ''
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-info trigger-disable',
                    trigger: 'info',
                    title: _('msimportexport_service_btn_info')
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-home trigger-disable',
                    trigger: 'home',
                    title: _('msimportexport_service_btn_home')
                }
            ]
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <b>{name}</b><br>\
                        <tpl if="extensions"><small>{extensions:this.rendereEtensions}</small></tpl>\
                    </span>\
                </div>\
            </tpl>',
            {
                compiled: true,
                rendereEtensions: function (value) {
                    return _('msimportexport_service_allowed_extensions') + ': ' + value.join(";");
                }
            }
        ),
    });
    Msie.combo.Service.superclass.constructor.call(this, config);
    this.store.on('load', function (e, r) {
        if (this.val !== null) {
            if (this.val != '' && this.hasValue(this.val)) {
                this.setValue(this.val);
            } else {
                this.setValue('');
            }
            this.val = null;
        }
    }, this);
    this.on('select', this.onSelectService, this);
};
Ext.extend(Msie.combo.Service, Msie.combo.ComboBox, {
    val: null,
    onTriggerClick: function (event, btn) {
        if (this.disabled) return;
        var trigger = btn.getAttribute('trigger');
        var record = this.getSelectedRecord();
        switch (trigger) {
            case 'info':
                if (!this.getValue() || this.isTriggerDisable(trigger)) return;
                Msie.msg.alert(_('info'), record.data.description);
                break;
            case 'home':
                if (!this.getValue() || this.isTriggerDisable(trigger)) return;
                window.open(record.data.home_link);
                break;
            default:
                MODx.combo.ComboBox.superclass.onTriggerClick.call(this);
        }
    },
    reload: function (params, value, callback) {
        var oldValue = this.getValue();
        this.val = value === undefined ? oldValue : value;
        params = Ext.apply(this.baseParams, params || {});
        this.getStore().reload({params: params, callback: callback});
    },
    hasValue: function (value) {
        var match = false;
        this.getStore().each(function (item) {
            if (item.data[this.valueField] === value) {
                match = true;
                return false;
            }
        }, this);
        return match;
    },
    setValue: function (value) {
        if (!this.loaded && value) {
            this.reload();
        } else {
            MODx.combo.ComboBox.superclass.setValue.call(this, value);
        }
        if (!value) {
            //  this.setTriggerDisable(true, 'info');
        }
    },
    onSelectService: function (combo, record) {
        this.setTriggerDisable(!record.data.description, 'info');
        this.setTriggerDisable(!record.data.home_link, 'home');
    },
});
Ext.reg('msie-combo-service', Msie.combo.Service);

Msie.combo.Upload = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        allowBlank: false,
        msgTarget: 'under',
        name: 'filename',
        triggerAction: 'all',
        ctCls: 'msimportexport-field',
        onTrigger1Click: this._triggerUpload,
        onTrigger2Click: this._triggerView,
    });
    Msie.combo.Upload.superclass.constructor.call(this, config);
    this.addEvents('upload', 'view');
};
Ext.extend(Msie.combo.Upload, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.file = '';
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-upload',
                    title: _('msimportexport_import_btn_upload_file')
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-view',
                    title: _('msimportexport_import_btn_preview_fields')
                }
            ]
        };
    },
    setValue: function (value) {
        if (Ext.isObject(value)) {
            this.file = value.path + value.filename;
            Ext.form.TwinTriggerField.superclass.setValue.call(this, value.filename);
        } else {
            this.file = '';
            Ext.form.TwinTriggerField.superclass.setValue.call(this, value);
        }
    },
    getFile: function () {
        return this.file;
    },
    _triggerUpload: function () {
        this.fireEvent('upload', this);
    },
    _triggerView: function () {
        this.fireEvent('view', this);
    },
});
Ext.reg('msie-field-upload', Msie.combo.Upload);

Msie.combo.AccessToken = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        allowBlank: false,
        msgTarget: 'under',
        local: false,
        ctCls: 'msimportexport-field',
        triggerGenerateText: _('msimportexport_generate_token'),
        triggerAction: 'all',
        onTrigger1Click: this._triggerCopy,
        onTrigger2Click: this._triggerGenerate,
    });
    Msie.combo.AccessToken.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.AccessToken, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-copy',
                    title: _('msimportexport_copy_to_clipboard')
                }, {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-refresh',
                    title: this.triggerGenerateText
                }
            ]
        };
    },

    _triggerCopy: function () {
        Clipboard.copy(this.getValue());
        MODx.msg.status({
            title: _('success'),
            message: _('msimportexport_copied_to_clipboard'),
            dontHide: false
        });
    },
    _triggerGenerate: function () {
        if (this.local) {
            var token = CryptoJS.MD5(Msie.utils.uniqid() + this.getValue());
            this.setValue(token);
        }
        this.fireEvent('generate', this);
    },

});
Ext.reg('msie-field-access-token', Msie.combo.AccessToken);

Msie.combo.Field = function (config) {
    config = config || {};
    var disableRemove = '';// config.mode == Msie.MODE_IMPORT ? 'trigger-disable' : '';
    Ext.applyIf(config, {
        displayField: 'display',
        valueField: 'key',
        fields: ['key', 'name', 'alias', 'label', {
            name: 'display',
            convert: function (v, rec) {
                var display = rec.name;
                if (rec.alias) {
                    display += ' - ' + rec.alias;
                }
                return display;
            }
        }],
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        selectOnFocus: true,
        /* autoLoad: true,
         lazyInit: false,
         lazyRender: false,
         triggerAction: 'all',
          autoLoad: true,
          lazyInit: false,*/
        url: Msie.config.connector_url,
        hiddenName: config.name || 'fields[]',
        baseParams: {
            action: 'mgr/field/getlist',
            id: config.preset || '',
            checking: config.checking || 0,
            combo: true,
            limit: 0,
        },
        triggerConfig: {
            tag: 'span',
            cls: 'x-field-combo-btns three',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger',
                    trigger: ''
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-clear',
                    trigger: 'clear',
                    title: _('msimportexport_fields_btn_clear')
                },
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-remove ' + disableRemove,
                    trigger: 'remove',
                    title: _('msimportexport_fields_btn_remove')
                }
            ]
        },
        tpl: new Ext.XTemplate('\
          <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span style="font-weight: bold">{name}</span>\
                     <tpl if="alias"> - <span style="font-style:italic">{alias}</span></tpl>\
                    <tpl if="label"><br />{label}</tpl>\
                </div>\
          </tpl>'),
    });
    Msie.combo.Field.superclass.constructor.call(this, config);
    this.addEvents('clear', 'remove');
};
Ext.extend(Msie.combo.Field, Msie.combo.ComboBox, {
    onTriggerClick: function (event, btn) {
        if (this.disabled) return;
        var trigger = btn.getAttribute('trigger');
        switch (trigger) {
            case 'clear':
                this.clearValue();
                this.fireEvent('clear', this);
                break;
            case 'remove':
                if (this.isTriggerDisable(trigger)) return;
                this.fireEvent('remove', this);
                break;
            default:
                MODx.combo.ComboBox.superclass.onTriggerClick.call(this);
        }
    },
    /* setValue: function (value) {
         var text = value,
             record = this.findRecord(this.valueField, value);
         console.log("loaded", this.loaded);
         if (record) {
             text = record.data[this.displayField];
             this.fireEvent('select', this, record, this.store.indexOf(record));
         }
         this.lastSelectionText = text;
         if (this.hiddenField) {
             this.hiddenField.value = value;
         }
         MODx.combo.ComboBox.superclass.setValue.call(this, text);
         this.value = value;
         return this;
     },*/
});
Ext.reg('msie-combo-field', Msie.combo.Field);

Msie.combo.ExportFormat = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        valueField: 'key',
        fields: ['key', 'name'],
        typeAhead: true,
        forceSelection: true,
        url: Msie.config.connector_url,
        hiddenName: config.name || 'format',
        baseParams: {
            action: 'mgr/export/format/getlist',
            preset: config.preset || 0,
            combo: true,
            limit: 0,
        },
        tpl: new Ext.XTemplate('\
          <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span style="font-weight: bold">{name}</span>\
                </div>\
          </tpl>')
    });
    Msie.combo.Field.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.ExportFormat, Msie.combo.ComboBox, {
    reload: function (params, value) {
        params = Ext.apply(this.baseParams, params || {});
        this.getStore().reload({params: params});
    }
});
Ext.reg('msie-combo-export-format', Msie.combo.ExportFormat);

Msie.combo.Ctx = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        hiddenName: config.name || 'key',
        valueField: 'key',
        fields: ['name', 'key'],
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Msie.config.connector_url,
        baseParams: {
            combo: true,
            action: 'mgr/system/context/getlist'
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <b>{name}</b>\
                        <tpl if="key"> ({key})</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    Msie.combo.Ctx.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Ctx, Msie.combo.ComboBox);
Ext.reg('msie-combo-ctx', Msie.combo.Ctx);

Msie.combo.Menu = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'parent',
        hiddenName: config.name || 'parent',
        fields: ['text', 'text_lex'],
        displayField: 'text_lex',
        valueField: 'text',
        editable: false,
        forceSelection: true,
        url: MODx.config.connector_url,
        baseParams: {
            action: 'system/menu/getlist',
            combo: true,
            limit: 0,
            showNone: true
        },
    });
    Msie.combo.Menu.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Menu, Msie.combo.ComboBox);
Ext.reg('msie-combo-menu', Msie.combo.Menu);

Msie.combo.TaskStatus = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('msimportexport_task_status_not_completed'), 'not_completed'],
                [_('msimportexport_task_status_initiated'), 'initiated'],
                [_('msimportexport_task_status_running'), 'running'],
                [_('msimportexport_task_status_waiting'), 'waiting'],
                [_('msimportexport_task_status_completed'), 'completed'],
                [_('msimportexport_task_status_stopped'), 'stopped'],
                [_('msimportexport_task_status_killed'), 'killed'],
                [_('msimportexport_task_status_failed'), 'failed'],
            ]
        }),
        hiddenName: config.name || '',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true,
        forceSelection: true,
    });
    Msie.combo.TaskStatus.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.TaskStatus, Msie.combo.ComboBox);
Ext.reg('msie-combo-task-status', Msie.combo.TaskStatus);

Msie.combo.Mode = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('msimportexport_mode_' + Msie.MODE_IMPORT), Msie.MODE_IMPORT],
                [_('msimportexport_mode_' + Msie.MODE_EXPORT), Msie.MODE_EXPORT],
            ]
        }),
        hiddenName: config.name || 'mode',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true,
        forceSelection: true,
    });
    Msie.combo.Mode.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Mode, Msie.combo.ComboBox);
Ext.reg('msie-combo-mode', Msie.combo.Mode);

Msie.combo.TaskCreator = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('msimportexport_task_creator_1'), 1],
                [_('msimportexport_task_creator_2'), 2],
            ]
        }),
        hiddenName: config.name || '',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true,
        forceSelection: true,
    });
    Msie.combo.TaskCreator.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.TaskCreator, Msie.combo.ComboBox);
Ext.reg('msie-combo-task-creator', Msie.combo.TaskCreator);

Msie.combo.NoticeMethod = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('msimportexport_task_notice_method_email'), 'email'],
            ]
        }),
        hiddenName: config.name || '',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true,
        forceSelection: true,
    });
    Msie.combo.NoticeMethod.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.NoticeMethod, Msie.combo.ComboBox);
Ext.reg('msie-combo-notice-method', Msie.combo.NoticeMethod);

Msie.combo.TaskTime = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('msimportexport_task_time_start'), 'start'],
                [_('msimportexport_task_time_finish'), 'finish'],
            ]
        }),
        displayField: 'd',
        valueField: 'v',
        hiddenName: config.name || '',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true,
        forceSelection: true,
        enableKeyEvents: true,
    });
    Msie.combo.TaskTime.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.TaskTime, Msie.combo.ComboBox);
Ext.reg('msie-combo-task-time', Msie.combo.TaskTime);

Msie.combo.Resource = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'pagetitle',
        hiddenName: config.name || 'resource',
        valueField: 'id',
        fields: ['id', 'pagetitle', 'parents', 'context_key'],
        pageSize: 20,
        typeAhead: true,
        editable: true,
        minChars: 2,
        forceSelection: true,
        url: Msie.config.connector_url,
        baseParams: {
            combo: true,
            class_key: config.class_key || 'modDocument',
            action: 'mgr/resource/getlist'
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{pagetitle:htmlEncode}</span>',
            '<tpl if="context_key"> - <span style="font-style:italic">{context_key:htmlEncode}</span></tpl>',
            '<tpl if="parents"><br>{parents:htmlEncode()}</tpl></div></tpl>'
        ),

    });
    Msie.combo.Resource.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Resource, Msie.combo.ComboBox);
Ext.reg('msie-combo-resource', Msie.combo.Resource);

Msie.combo.Template = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'templatename',
        hiddenName: config.name || 'template',
        valueField: 'id',
        editable: true,
        minChars: 2,
        forceSelection: true,
        pageSize: 20,
        fields: ['id', 'templatename', 'description', 'category_name'],
        tpl: new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item"><span style="font-weight: bold">{templatename:htmlEncode}</span>'
            , '<tpl if="category_name"> - <span style="font-style:italic">{category_name:htmlEncode}</span></tpl>'
            , '<br />{description:htmlEncode()}</div></tpl>'),
        url: MODx.config.connector_url,
        baseParams: {
            action: 'element/template/getlist',
            combo: 1
        }
    });
    Msie.combo.Template.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Template, Msie.combo.ComboBox);
Ext.reg('msie-combo-template', Msie.combo.Template);

Msie.combo.TextFormatMethod = function (config) {
    config = config || {};
    var data = [
        [_('msimportexport_import_combo_format_text_method_nl2br'), 'nl2br']
        , [_('msimportexport_import_combo_format_text_method_paragraph'), 'paragraph']
    ];
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v']
            , data: data
        })
        , displayField: 'd'
        , valueField: 'v'
        , hiddenName: config.name || 'text_format_method'
        , mode: 'local'
        , triggerAction: 'all'
        , editable: false
        , preventRender: true
    });
    Msie.combo.TextFormatMethod.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.TextFormatMethod, Msie.combo.ComboBox);
Ext.reg('msie-combo-text-format-method', Msie.combo.TextFormatMethod);

Msie.combo.SkipAction = function (config) {
    config = config || {};
    var data = [
        [_('msimportexport_import_combo_skip_action_create'), 'create']
        , [_('msimportexport_import_combo_skip_action_update'), 'update']
    ];
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v']
            , data: data
        })
        , displayField: 'd'
        , valueField: 'v'
        , hiddenName: config.name || 'skip_action'
        , mode: 'local'
        , triggerAction: 'all'
        , editable: false
        , preventRender: true
    });
    Msie.combo.SkipAction.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.SkipAction, Msie.combo.ComboBox);
Ext.reg('msie-combo-skip-action', Msie.combo.SkipAction);

Msie.combo.CheckUniqueAlias = function (config) {
    config = config || {};
    var data = [
        [_('msimportexport_import_combo_check_unique_alias_no'), 0],
        [_('msimportexport_import_combo_check_unique_alias_all'), 1],
        [_('msimportexport_import_combo_check_unique_alias_only_create'), 'create'],
        [_('msimportexport_import_combo_check_unique_alias_only_update'), 'update'],
    ];
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: data
        }),
        displayField: 'd',
        valueField: 'v',
        hiddenName: config.name || 'skip_action',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true
    });
    Msie.combo.CheckUniqueAlias.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.CheckUniqueAlias, Msie.combo.ComboBox);
Ext.reg('msie-combo-check-unique-alias', Msie.combo.CheckUniqueAlias);

Msie.combo.ImportCompletionAction = function (config) {
    config = config || {};
    var data = [
        [_('msimportexport_import_combo_completion_action_clear_bin'), 'clear_bin'],
        [_('msimportexport_import_combo_completion_action_clear_cache'), 'clear_cache'],
        [_('msimportexport_import_combo_completion_action_remove'), 'remove'],
        [_('msimportexport_import_combo_completion_action_unpublish'), 'unpublish'],
    ];
    Ext.applyIf(config, {
        xtype: 'superboxselect',
        allowBlank: true,
        msgTarget: 'under',
        allowAddNewData: false,
        addNewDataOnBlur: false,
        pinList: false,
        resizable: true,
        editable: false,
        preventRender: true,
        name: config.name || 'completion_action',
        ctCls: 'msimportexport-field',
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v', 'rank'],
            data: data,
            remoteSort: true,
        }),
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls: 'x-form-trigger',
        clearBtnCls: 'x-form-trigger',
        displayFieldTpl: config.displayFieldTpl || '{d}',
    });

    Msie.combo.ImportCompletionAction.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.ImportCompletionAction, Ext.ux.form.SuperBoxSelect);
Ext.reg('msie-combo-import-completion-action', Msie.combo.ImportCompletionAction);

Msie.combo.Gallery = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        valueField: 'key',
        displayField: 'name',
        hiddenName: config.name || 'gallery_class',
        forceSelection: true,
        pageSize: 20,
        url: Msie.config.connector_url,
        baseParams: {
            combo: true,
            class_key: 'msCategory',
            action: 'mgr/gallery/photo/getlist'
        },
    });
    Msie.combo.Gallery.superclass.constructor.call(this, config);
};
Ext.extend(Msie.combo.Gallery, Msie.combo.ComboBox);
Ext.reg('msie-combo-gallery', Msie.combo.Gallery);

Msie.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        ctCls: 'msimportexport-field',
        clearBtnCls: 'x-field-combo-btn-clear',
        searchBtnCls: 'x-field-combo-btn-search',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    Msie.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(Msie.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    },

});
Ext.reg('msie-combo-search', Msie.combo.Search);
