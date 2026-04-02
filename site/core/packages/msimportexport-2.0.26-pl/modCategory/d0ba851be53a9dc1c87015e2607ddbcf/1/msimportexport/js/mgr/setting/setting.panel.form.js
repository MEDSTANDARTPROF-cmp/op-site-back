Msie.panel.SettingForm = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        layout: 'form',
        labelAlign: 'top',
        buttons: this.getButtons(config),
        tbar: this.getToolBar(config),
        keys: this.getKeys(config),
        listeners: this.getListeners(config),
        items: this.getFields(config),
    });
    Msie.panel.SettingForm.superclass.constructor.call(this, config);
    this.setup(config);
};
Ext.extend(Msie.panel.SettingForm, MODx.FormPanel, {
    getKeys: function (config) {
        var keys = [];
        return keys;
    },
    setup: function (config) {
    },
    getFields: function (config) {
    },
    getButtons: function (config) {
        return [];
    },
    getToolBar: function (config) {
        var tbar = [];
        return tbar;
    },
    getListeners: function (config) {
        return {
            setup: function () {
                this.getForm().setValues(config.settings || {});
            }
        }
    },
    getSettings: function () {
        return this.getFormValues() || {};
    },
    getFormValues: function () {
        var values = {},
            data = this.getForm().getFieldValues();
        Ext.iterate(data || {}, function (key, val) {
            if (Ext.isArray(val)) {
                if (Msie.utils.isEmpty(values[key])) values[key] = [];
                Ext.each(val, function (o) {
                    if (o instanceof Ext.Component) {
                        if (typeof o.getValue !== 'function') return true;
                        if (typeof o.inputValue !== 'undefined') {
                            if (o.getValue()) {
                                values[key].push(o.inputValue);
                            }
                        } else {
                            values[key].push(o.getValue());
                        }
                    }
                }, this);
            } else {
                values[key] = val;
            }
        }, this);
        return values;
    },
    getBtnSubmitText: function () {
        return '<i class="icon icon-floppy-o"></i> ' + _('save');
    },
    submit: function (e) {
    },

});
Ext.reg('msie-panel-setting-form', Msie.panel.SettingForm);