Msie.window.Default = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: '',
        url: Msie.config.connector_url,
        cls: 'modx-window msimportexport-window ' || config['cls'],
        width: 800,
        modal: true,
        autoHeight: true,
        allowDrop: false,
        onlyCancelBtn: false,
        record: {},
        baseParams: {},
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config),
        listeners: this.getListeners(config),
    });
    Msie.window.Default.superclass.constructor.call(this, config);

    this.on('hide', function () {
        var w = this;
        window.setTimeout(function () {
            w.close();
        }, 200);
    });
};
Ext.extend(Msie.window.Default, MODx.Window, {

    getFields: function (config) {
        return [];
    },

    getButtons: function (config) {
        var buttons = [{
            text: config.cancelBtnText || _('cancel'),
            scope: this,
            handler: function () {
                config.closeAction !== 'close'
                    ? this.hide()
                    : this.close();
            }
        }];
        if (!config.onlyCancelBtn) {
            buttons.push({
                text: config.saveBtnText || _('save'),
                cls: 'primary-button',
                scope: this,
                handler: this.submit,
            });
        }
        return buttons;
    },

    getKeys: function () {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function () {
                this.submit();
            }, scope: this
        }];
    },

    getListeners: function () {
        return {};
    },

});
Ext.reg('msie-window-default', Msie.window.Default);

Msie.window.Alert = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        buttonAlign: 'center'
    });
    Msie.window.Alert.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.Alert, Msie.window.Default, {
    getFields: function (config) {
        return [{
            html: config.text || '',
            style: 'padding:15px 0 10px 0;'
        }];
    },
    getButtons: function (config) {
        return [{
            text: _('ok'),
            scope: this,
            cls: 'x-btn-over',
            handler: function () {
                config.closeAction !== 'close'
                    ? this.hide()
                    : this.close();
            }
        }];
    },

});
Ext.reg('msie-window-alert', Msie.window.Alert);