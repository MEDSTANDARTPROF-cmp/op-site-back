Msie.window.TaskReport = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_task_title_window_report') + ' #' + config.task,
        modal: true,
        //height: 700,
        width: 1200,
        stateful: false,
        resizable: true,
        collapsible: true,
        maximizable: true,
        autoHeight: true,
        autoScroll: true,
        items: this.getFields(config),
        buttons: this.getButtons(config)
    });
    Msie.window.TaskReport.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.TaskReport, Ext.Window, {
    getFields: function (config) {
        return [{
            xtype: "msie-panel-task-report",
            task: config.task
        }
        ];
    },
    getButtons: function (config) {
        return [{
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
Ext.reg('msie-window-task-report', Msie.window.TaskReport);

Msie.window.TaskPid = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_task_title_window_pid') + ' #' + config.pid,
        modal: true,
        //height: 700,
        width: 1000,
        stateful: false,
        resizable: true,
        collapsible: true,
        maximizable: true,
        autoHeight: true,
        autoScroll: true,
        items: this.getFields(config),
        buttons: this.getButtons(config)
    });
    Msie.window.TaskPid.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.TaskPid, Ext.Window, {
    getFields: function (config) {
        return [{
            xtype: "msie-panel-task-pid",
            task: config.task,
            pid: config.pid
        }
        ];
    },
    getButtons: function (config) {
        return [{
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
Ext.reg('msie-window-task-pid', Msie.window.TaskPid);

Msie.window.TaskLog = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: _('msimportexport_task_title_window_log') + ' #' + config.task,
        modal: true,
        height: 500,
        width: 1000,
        stateful: false,
        resizable: true,
        collapsible: true,
        maximizable: true,
        autoHeight: false,
        autoScroll: true,
        items: this.getFields(config),
        buttons: this.getButtons(config)
    });
    Msie.window.TaskLog.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.TaskLog, Ext.Window, {
    getFields: function (config) {
        return [{
            xtype: "textarea",
            id: 'msie-task-log-content',
            name: "log",
            hideLabel: true,
            style: 'margin-top: 10px;',
            readOnly: true,
            height: "94%",
            width: "99%",
            value: config.log
        }
        ];
    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-eraser"></i> ' + _('msimportexport_task_btn_clear_log'),
            disabled: config.log ? false : true,
            scope: this,
            handler: function () {
                MODx.Ajax.request({
                    url: Msie.config.connector_url,
                    params: {
                        action: 'mgr/task/log/clear',
                        id: config.task
                    },
                    listeners: {
                        success: {
                            fn: function (r) {
                                var log = Ext.getCmp('msie-task-log-content');
                                log.setValue('');
                            }, scope: this
                        }
                    }
                });
            }
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
Ext.reg('msie-window-task-log', Msie.window.TaskLog);