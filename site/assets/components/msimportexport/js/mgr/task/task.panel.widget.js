Msie.panel.TaskWidget = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    Ext.apply(config, {
        border: false,
        items: this.getFields(config),
        listeners: this.getListeners(config),
    });
    Msie.panel.TaskWidget.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.TaskWidget, MODx.Panel, {
    getFields: function (config) {
        return [{
            xtype: 'msie-panel-timer',
            id: 'msie-timer-task-refresh',
            style:'padding: 15px 0',
            anchor: '100%',
            time: Msie.config.sys_settings.task_list_refresh_freq,
            listeners: {
                complete: {
                    fn: this.refreshTasks, scope: this
                }
            }
        }, {
            xtype: 'msie-grid-task',
            id: 'msie-grid-task',
            preventRender: true
        }];
    },
    getListeners: function (config) {
        return {
            afterrender: {
                fn: this.setup, scope: this
            }
        };
    },
    setup: function () {
        this.grid = Ext.getCmp('msie-grid-task');
        this.timer = Ext.getCmp('msie-timer-task-refresh');
        this.grid.getStore().on('load', function () {
            if (this.timer.isPause()) return;
            this.timer.reset();
        }, this);

    },
    refreshTasks: function (timer) {
        this.grid.getBottomToolbar().changePage(1);
    }
});
Ext.reg('msie-panel-task-widget', Msie.panel.TaskWidget);