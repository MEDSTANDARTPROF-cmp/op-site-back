Msie.field.CronTime = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        msgTarget: 'under',
        triggerAction: 'all',
        readOnly: true,
        onTrigger1Click: this._triggerHelper,
    });
    Msie.field.CronTime.superclass.constructor.call(this, config);
    this.addEvents('clear');
    this.on('afterrender', function () {
        this.getEl().on('click', function () {
            this._triggerHelper();
        }, this);
    });
};
Ext.extend(Msie.field.CronTime, Ext.form.TwinTriggerField, {
    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-combo-btns one',
            cn: [
                {
                    tag: 'div',
                    cls: 'x-form-trigger x-field-combo-btn-clock'
                }
            ]
        };
    },
    _triggerHelper: function () {
        var w = Ext.getCmp('msie-window-cron-time-helper');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-cron-time-helper',
            id: 'msie-window-cron-time-helper',
            closeAction: 'close',
            time: this.getValue(),
            listeners: {
                success: {
                    fn: function (value) {
                        Ext.form.TwinTriggerField.superclass.setValue.call(this, value);
                        w.close();
                    }, scope: this
                }
            }
        });
        w.show();

    }
});
Ext.reg('msie-field-cron-time', Msie.field.CronTime);

Msie.window.CronTimeHelper = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_cron_helper_title'),
        modal: true,
        width: 350,
        stateful: false,
        resizable: true,
        collapsible: true,
        maximizable: false,
        autoHeight: true,
        autoScroll: true,
        items: [{
            xtype: 'panel',
            layout: 'form',
            labelAlign: 'top',
            items: this.getFields(config)
        }],
        buttons: this.getButtons(config)
    });
    Msie.window.CronTimeHelper.superclass.constructor.call(this, config);
    this.addEvents('success');
    this.setup();
};
Ext.extend(Msie.window.CronTimeHelper, Ext.Window, {
    fields: ['min', 'hour', 'day', 'month', 'wday'],
    getFields: function (config) {
        return [{
            layout: 'column',
            anchor: '100%',
            border: false,
            fieldLabel: _('msimportexport_cron_helper_min'),
            items: [{
                xtype: 'textfield',
                id: 'msie-cron-helper-min',
                name: 'min',
                allowBlank: false,
                validator: this.validator,
                columnWidth: .2,
            }, {
                xtype: 'msie-combo',
                id: 'msie-cron-helper-combo-min',
                target: 'min',
                columnWidth: .8,
                anchor: '100%',
                displayField: 'd',
                valueField: 'v',
                mode: 'local',
                preventRender: true,
                forceSelection: true,
                store: new Ext.data.SimpleStore({
                    fields: ['d', 'v'],
                    data: [
                        [_('msimportexport_cron_every_minute'), '*'],
                        [_('msimportexport_cron_every_2_minute'), '*/2'],
                        [_('msimportexport_cron_every_5_minute'), '*/5'],
                        [_('msimportexport_cron_every_10_minute'), '*/10'],
                        [_('msimportexport_cron_every_15_minute'), '*/15'],
                        [_('msimportexport_cron_every_30_minute'), '*/30']
                    ]
                }),
                listeners: {
                    select: {
                        fn: this.onChange,
                        scope: this
                    }, clear: {
                        fn: this.onChange,
                        scope: this
                    }
                }
            }]
        }, {
            layout: 'column',
            anchor: '100%',
            border: false,
            fieldLabel: _('msimportexport_cron_helper_hour'),
            items: [{
                xtype: 'textfield',
                id: 'msie-cron-helper-hour',
                name: 'hour',
                allowBlank: false,
                columnWidth: .2,
            }, {
                xtype: 'msie-combo',
                id: 'msie-cron-helper-combo-hour',
                target: 'hour',
                columnWidth: .8,
                anchor: '100%',
                displayField: 'd',
                valueField: 'v',
                mode: 'local',
                preventRender: true,
                forceSelection: true,
                store: new Ext.data.SimpleStore({
                    fields: ['d', 'v'],
                    data: [
                        [_('msimportexport_cron_every_hour'), '*'],
                        [_('msimportexport_cron_every_2_hour'), '*/2'],
                        [_('msimportexport_cron_every_6_hour'), '*/6'],
                        [_('msimportexport_cron_every_12_hour'), '*/12']
                    ]
                }),
                listeners: {
                    select: {
                        fn: this.onChange,
                        scope: this
                    }, clear: {
                        fn: this.onChange,
                        scope: this
                    }
                }
            }]
        }, {
            layout: 'column',
            anchor: '100%',
            border: false,
            fieldLabel: _('msimportexport_cron_helper_day'),
            items: [{
                xtype: 'textfield',
                id: 'msie-cron-helper-day',
                name: 'day',
                allowBlank: false,
                columnWidth: .2,
            }, {
                xtype: 'msie-combo',
                id: 'msie-cron-helper-combo-day',
                target: 'day',
                columnWidth: .8,
                anchor: '100%',
                displayField: 'd',
                valueField: 'v',
                mode: 'local',
                preventRender: true,
                forceSelection: true,
                store: new Ext.data.SimpleStore({
                    fields: ['d', 'v'],
                    data: [
                        [_('msimportexport_cron_every_day'), '*'],
                        [_('msimportexport_cron_every_odd_day'), '1-31/2'],
                        [_('msimportexport_cron_every_even_day'), '*/2'],
                        [_('msimportexport_cron_every_3_day'), '*/3'],
                        [_('msimportexport_cron_every_5_day'), '*/5'],
                        [_('msimportexport_cron_every_10_day'), '*/10'],
                        [_('msimportexport_cron_every_15_day'), '*/15'],
                    ]
                }),
                listeners: {
                    select: {
                        fn: this.onChange,
                        scope: this
                    }, clear: {
                        fn: this.onChange,
                        scope: this
                    }
                }
            }]
        }, {
            layout: 'column',
            anchor: '100%',
            border: false,
            fieldLabel: _('msimportexport_cron_helper_month'),
            items: [{
                xtype: 'textfield',
                id: 'msie-cron-helper-month',
                name: 'month',
                allowBlank: false,
                columnWidth: .2,
            }, {
                xtype: 'msie-combo',
                id: 'msie-cron-helper-combo-month',
                target: 'month',
                columnWidth: .8,
                anchor: '100%',
                displayField: 'd',
                valueField: 'v',
                mode: 'local',
                preventRender: true,
                forceSelection: true,
                store: new Ext.data.SimpleStore({
                    fields: ['d', 'v'],
                    data: [
                        [_('msimportexport_cron_every_month'), '*'],
                        [_('msimportexport_cron_every_odd_month'), '1-31/2'],
                        [_('msimportexport_cron_every_even_month'), '*/2'],
                        [_('msimportexport_cron_every_3_month'), '*/3'],
                        [_('msimportexport_cron_every_6_month'), '*/6'],
                        [_('msimportexport_cron_jan_month'), '1'],
                        [_('msimportexport_cron_feb_month'), '2'],
                        [_('msimportexport_cron_mar_month'), '3'],
                        [_('msimportexport_cron_apr_month'), '4'],
                        [_('msimportexport_cron_may_month'), '5'],
                        [_('msimportexport_cron_jun_month'), '6'],
                        [_('msimportexport_cron_jul_month'), '7'],
                        [_('msimportexport_cron_aug_month'), '8'],
                        [_('msimportexport_cron_sep_month'), '9'],
                        [_('msimportexport_cron_oct_month'), '10'],
                        [_('msimportexport_cron_nov_month'), '11'],
                        [_('msimportexport_cron_dec_month'), '12'],
                    ]
                }),
                listeners: {
                    select: {
                        fn: this.onChange,
                        scope: this
                    }, clear: {
                        fn: this.onChange,
                        scope: this
                    }
                }
            }]
        }, {
            layout: 'column',
            anchor: '100%',
            border: false,
            fieldLabel: _('msimportexport_cron_helper_wday'),
            items: [{
                xtype: 'textfield',
                id: 'msie-cron-helper-wday',
                name: 'wday',
                allowBlank: false,
                columnWidth: .2,
            }, {
                xtype: 'msie-combo',
                id: 'msie-cron-helper-combo-wday',
                target: 'wday',
                columnWidth: .8,
                anchor: '100%',
                displayField: 'd',
                valueField: 'v',
                mode: 'local',
                preventRender: true,
                forceSelection: true,
                store: new Ext.data.SimpleStore({
                    fields: ['d', 'v'],
                    data: [
                        [_('msimportexport_cron_every_wday'), '*'],
                        [_('msimportexport_cron_weekdays_wday'), '1,2,3,4,5'],
                        [_('msimportexport_cron_weekend_wday'), '0,6'],
                        [_('msimportexport_cron_monday_wday'), '1'],
                        [_('msimportexport_cron_tuesday_wday'), '2'],
                        [_('msimportexport_cron_wednesday_wday'), '3'],
                        [_('msimportexport_cron_thursday_wday'), '4'],
                        [_('msimportexport_cron_friday_wday'), '5'],
                        [_('msimportexport_cron_saturday_wday'), '6'],
                        [_('msimportexport_cron_sunday_wday'), '0'],

                    ]
                }),
                listeners: {
                    select: {
                        fn: this.onChange,
                        scope: this
                    }, clear: {
                        fn: this.onChange,
                        scope: this
                    }
                }
            }]
        }];
    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-clock-o"></i> ' + _('msimportexport_cron_helper_btn_generate'),
            scope: this,
            cls: 'primary-button',
            handler: this.generate
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
    onChange: function (combo, records) {
        var target = Ext.getCmp('msie-cron-helper-' + combo.target);
        target.setValue(combo.getValue());
    },
    validator: function (value) {
        var success = false,
            regs = [
                /^\*$/,
                /^\d{1,2}$/,
                /^\*\/\d{1,2}$/,
                /^\d{1,2}-\d{1,2}$/
            ];
        Ext.each(regs || [], function (reg, index) {
            if (reg.test(value)) {
                switch (index) {
                    case 0:
                        success = true;
                        break;
                    case 1:
                    case 2:
                        success = parseInt(value.replace('*/', '')) <= 59;
                        break;
                    case 3:
                        var arr = value.split('-');
                        success = parseInt(arr[0]) <= 59 && parseInt(arr[1]) <= 59 && parseInt(arr[0]) < parseInt(arr[1]);
                        break;
                }
                return false;
            }
        });
        return success ? true : _('msimportexport_cron_valid_format_data');
    },
    checkValidity: function () {
        var success = true;
        Ext.each(this.fields || [], function (name) {
            var field = Ext.getCmp('msie-cron-helper-' + name);
            field.validate();
            if (!field.isValid()) {
                success = false;
                return false;
            }
        }, this);
        return success;
    },
    generate: function () {
        var result = [];
        if (!this.checkValidity()) return;

        Ext.each(this.fields || [], function (name) {
            var field = Ext.getCmp('msie-cron-helper-' + name);
            result.push(field.getValue().trim());
        }, this);
        this.fireEvent('success', result.join(' '), this);

    },
    setup: function () {
        if (!this.time) return;
        var arr = this.time.split(' ');
        Ext.each(arr || [], function (val, index) {
            var name = this.fields[index];
            if (name) {
                var field = Ext.getCmp('msie-cron-helper-' + name),
                    combo = Ext.getCmp('msie-cron-helper-combo-' + name);
                field.setValue(val);
                combo.setValue(val);
            }
        }, this);

    }

});
Ext.reg('msie-window-cron-time-helper', Msie.window.CronTimeHelper);


