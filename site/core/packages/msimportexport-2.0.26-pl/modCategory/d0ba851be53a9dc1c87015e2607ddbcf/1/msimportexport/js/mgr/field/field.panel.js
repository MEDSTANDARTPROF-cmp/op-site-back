Msie.panel.Field = function (config) {
    config = config || {};
    if (!config.fid) {
        config.fid = Ext.id();
    }

    Ext.applyIf(config, {
        id: 'msie-panel-field-' + config.fid,
        cls: 'msimportexport-panel-field',
        layout: 'form',
        labelAlign: 'top',
        items: this.getFields(config),
    });
    Msie.panel.Field.superclass.constructor.call(this, config);
    this.on('render', function (panel) {
        panel.getEl().on('click', function (e) {
            if (e.getTarget('.msimportexport-combo')) {
                var target = Ext.getCmp('msie-panel-field-combo-' + this.fid);
                target.focus();
            }
        }, this);
    }, this);
    this.addEvents('remove');
};
Ext.extend(Msie.panel.Field, MODx.Panel, {
    getFields: function (config) {
        return [{
            xtype: 'msie-combo-field',
            id: 'msie-panel-field-combo-' + config.fid,
            fieldLabel: config.label || '',
            preset: config.preset || 0,
            value: config.value || '',
            //  mode: config.mode || '',
            msgTarget: 'under',
            anchor: '100%',
            listeners: {
                remove: {
                    fn: this.remove,
                    scope: this
                }
            }
        }];
    },
    setLabel: function () {
        return this.label;
    },
    setLabel: function (label) {
        if (label) {
            this.label = label;
        } else {
            label = this.label || '';
        }
        var target = Ext.getCmp('msie-panel-field-combo-' + this.fid);
        label = '#' + this.getIndex() + ': ' + Msie.utils.strTruncate(Msie.utils.stripTags(label), 200);
        target.label.update(label);
    },
    getValue: function () {
        var target = Ext.getCmp('msie-panel-field-combo-' + this.fid);
        return target.getValue();
    },
    setValue: function (value, fromLabel) {
        var target = Ext.getCmp('msie-panel-field-combo-' + this.fid);
        value = fromLabel ? (this.label || '') : value;
        target.setValue(value);
    },
    selectByValue: function (value, fromLabel) {
        var target = Ext.getCmp('msie-panel-field-combo-' + this.fid);
        value = fromLabel ? (this.label || '') : value;
        target.selectByValue(value);
    },
    refreshLabel: function () {
        this.setLabel('');
    },
    getIndex: function () {
        return this.index;
    },
    setIndex: function (index) {
        this.index = index;
    },
    remove: function () {
        this.fireEvent('remove', this);
    },
});
Ext.reg('msie-panel-field', Msie.panel.Field);