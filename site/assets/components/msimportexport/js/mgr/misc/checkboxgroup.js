Msie.group.CheckboxGroup = function (config) {
    config = config || {};
    if (!config.hiddenName) {
        config.hiddenName = config.name || '';
    }
    Ext.applyIf(config, {
        msgTarget: 'under',
        listeners: {
            afterrender: {fn: this.onAfterRender, scope: this}

        }
    });
    Msie.group.CheckboxGroup.superclass.constructor.call(this, config);
};
Ext.extend(Msie.group.CheckboxGroup, Ext.form.CheckboxGroup, {
    onAfterRender: function () {
        if (this.url) {
            this.loadData();
        } else {
            this.setup();
        }
    },
    loadData: function () {
        MODx.Ajax.request({
            url: this.url,
            params: this.baseParams,
            listeners: {
                success: {
                    fn: function (r) {
                        this.setup(r.results);
                    },
                    scope: this
                }
            }
        });
    },
    setup: function (data) {
        if (data) {
            Ext.each(data || [], function (item) {
                this.addItem({
                    boxLabel: item[this.displayField],
                    inputValue: item[this.valueField],
                    checked: this.checkValue(item[this.valueField])
                });
            }, this);
        } else {
            Ext.each(this.items.items || [], function (item) {
                item.setValue(this.checkValue(item.inputValue));
            }, this);
        }
    },
    setValue: function (values) {
        this.values = values;
    },
    addItem: function (data) {
        var checkbox = new Ext.form.Checkbox(data),
            col = this.panel.items.get(this.items.getCount() % this.panel.items.getCount());
        this.items.add(checkbox);
        col.add(checkbox);
    },
    checkValue: function (val) {
        var check = false;
        if (!Msie.utils.isEmpty(this.values)) {
            check = this.values.indexOf(val) > -1 ? true : false;
        }
        return check;
    },
    validateValue: function (b) {
        var valid = false;
        if (this.allowBlank) return true;
        Ext.each(this.items.items || [], function (item) {
            if (item.getValue()) {
                valid = true;
                return false;
            }
        }, this);
        if (!valid) this.markInvalid(this.blankText);
        return valid;
    }

});
Ext.reg('msie-checkboxgroup', Msie.group.CheckboxGroup);