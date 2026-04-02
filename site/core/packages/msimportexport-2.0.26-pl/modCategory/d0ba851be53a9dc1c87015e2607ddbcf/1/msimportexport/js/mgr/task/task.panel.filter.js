Msie.panel.TaskFilter = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'msie-panel-task-filter';
    }
    Ext.applyIf(config, {
        border: false,
        layout: 'form',
        cls: 'main-wrapper',
        items: this.getFields(config),
        listeners: this.getListeners(config),
        buttons: this.getButtons(config),
        keys: this.getKeys(config),
    });
    Msie.panel.TaskFilter.superclass.constructor.call(this, config);
    this.setupEvents(config);
};
Ext.extend(Msie.panel.TaskFilter, MODx.FormPanel, {
    grid: null,
    getListeners: function (config) {
        return {};
    },
    setupEvents: function (config) {
        this.addEvents('reset');
        this.on('beforerender', function () {
            this.grid = Ext.getCmp('msie-grid-task');
        }, this);
        this.on('change', function () {
            this.submit();
        }, this);
    },
    getFields: function (config) {
        return [{
            layout: 'column',
            items: [{
                columnWidth: .4,
                layout: 'form',
                defaults: {
                    anchor: '100%',
                    hideLabel: true,
                },
                items: this.getLeftFields(config),
            }, {
                columnWidth: .3,
                layout: 'form',
                labelWidth: 30,
                defaults: {
                    anchor: '100%',
                    hideLabel: true,
                },
                items: this.getCenterFields(config),
            }, {
                columnWidth: .3,
                layout: 'form',
                defaults: {
                    anchor: '100%',
                    hideLabel: true,
                },
                items: this.getRightFields(config),
            }],
        }];
    },
    getLeftFields: function (config) {
        return [{
            xtype: 'msie-combo-preset',
            name: 'preset',
            emptyText: _('msimportexport_task_filter_preset'),
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            }
        }, {
            xtype: 'msie-combo-task-status',
            name: 'status',
            emptyText: _('msimportexport_task_filter_status'),
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            }
        }, {
            xtype: 'msie-combo-mode',
            name: 'mode',
            emptyText: _('msimportexport_task_filter_mode'),
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            }
        }];
    },
    getCenterFields: function (config) {
        return [{
            xtype: 'msie-combo-task-time',
            emptyText: _('msimportexport_task_filter_time'),
            name: 'time',
            value: 'start',
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            },
        }, {
            xtype: 'xdatetime',
            labelAlign: 'left',
            hideLabel: false,
            emptyText: _('msimportexport_task_filter_time_begin'),
            fieldLabel: _('msimportexport_task_filter_time_begin'),
            name: 'time_begin',
            hiddenFormat: 'Y-m-d H:i:s',
            timePosition: 'right',
            dateFormat: MODx.config.manager_date_format,
            timeFormat: 'H:i:s',
            startDay: parseInt(MODx.config.manager_week_start),
            offset_time: MODx.config.server_offset_time,
            allowBlank: true,
            listeners: {
                change: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            },
        }, {
            xtype: 'xdatetime',
            labelAlign: 'left',
            hideLabel: false,
            emptyText: _('msimportexport_task_filter_time_end'),
            fieldLabel: _('msimportexport_task_filter_time_end'),
            name: 'time_end',
            hiddenFormat: 'Y-m-d H:i:s',
            timePosition: 'right',
            dateFormat: MODx.config.manager_date_format,
            timeFormat: 'H:i:s',
            startDay: parseInt(MODx.config.manager_week_start),
            offset_time: MODx.config.server_offset_time,
            allowBlank: true,
            listeners: {
                change: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            },
        }];
    },
    getRightFields: function (config) {
        return [{
            xtype: 'msie-combo-task-creator',
            emptyText: _('msimportexport_task_filter_creator'),
            name: 'creator',
            listeners: {
                select: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
            },
        }, {
            xtype: 'msie-combo-search',
            emptyText: _('msimportexport_task_filter_search'),
            listeners: {
                search: {
                    fn: function () {
                        this.fireEvent('change');
                    }, scope: this
                },
                clear: {
                    fn: function (field) {
                        field.setValue('');
                        this.fireEvent('change');
                    }, scope: this
                },
            }
        }];
    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-times"></i> ' + _('msimportexport_task_filter_reset'),
            handler: this.reset,
            scope: this,
            iconCls: 'x-btn-small',
        }, {
            text: '<i class="icon icon-check"></i> ' + _('msimportexport_task_filter_submit'),
            handler: this.submit,
            scope: this,
            cls: 'primary-button',
            iconCls: 'x-btn-small',
        }];
    },
    getKeys: function (config) {
        return [{
            key: Ext.EventObject.ENTER,
            fn: function () {
                this.submit();
            },
            scope: this
        }];
    },
    submit: function () {
        var store = this.grid.getStore();
        var form = this.getForm();
        var values = form.getFieldValues();
        for (var i in values) {
            if (i != undefined && values.hasOwnProperty(i)) {
                store.baseParams[i] = values[i];
            }
        }
        this.refresh();
    },
    reset: function () {
        var store = this.grid.getStore();
        var form = this.getForm();

        form.items.each(function (f) {
            if (f.name === 'time') return true;
            if (typeof (f['clearValue']) === 'function') {
                f.clearValue();
            } else {
                f.reset();
            }
        });

        var values = form.getValues();
        for (var i in values) {
            if (values.hasOwnProperty(i)) {
                store.baseParams[i] = '';
            }
        }
        this.refresh();
        this.fireEvent('reset', this);
    },
    refresh: function () {
        this.grid.getBottomToolbar().changePage(1);
    },
});
Ext.reg('msie-panel-task-filter', Msie.panel.TaskFilter);