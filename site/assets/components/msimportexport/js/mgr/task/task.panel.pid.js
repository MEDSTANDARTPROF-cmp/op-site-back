Msie.panel.TaskPid = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    Ext.apply(config, {
        border: false,
        cls: 'container',
        items: this.getFields(config),
        listeners: this.getListeners(config),
    });
    Msie.panel.TaskPid.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.TaskPid, MODx.Panel, {
    getFields: function (config) {
        return [{
            xtype: 'msie-panel-timer',
            id: 'msie-task-pid-timer-' + this.ident,
            style: 'margin-bottom: 15px',
            time: Msie.config.sys_settings.pid_refresh_freq,
            listeners: {
                complete: {
                    fn: this.load, scope: this
                }
            }
        }, {
            xtype: 'panel',
            cls: 'msie-task-pid-dashboard',
            id: 'msie-panel-task-pid-dashboard-' + this.ident,
            items: [{
                html: '<div class="msimportexport-task-pid-dashboard-item">' + _('msimportexport_task_pid_status_loading') + '</div>'
            }],
            anchor: '100%',
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_task_fieldset_pid_help'),
            style: 'padding: 10px 0',
            autoHeight: true,
            hideLabel: true,
            collapsible: true,
            stateId: 'msie-task-fieldset-pid-help',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'label',
                cls: 'desc-under',
                html: _('msimportexport_task_pid_help'),
                anchor: '100%'
            }]
        }];
    },
    getListeners: function (config) {
        return {
            afterrender: {
                fn: function () {
                    this.setup(config);
                    this.load();
                }, scope: this
            },
            beforedestroy: {
                fn: this.destroy, scope: this
            },
        };
    },
    updateDashboard: function (data) {
        if (Msie.utils.isEmpty(data)) return;
        var html = '';
        Ext.each(data.top, function (val) {
            html += '<div class="msimportexport-task-pid-dashboard-item">' + val + '</div>';
        }, this);
        if (data.status !== 'running') {
            html += '<div class="msimportexport-task-pid-dashboard-item"><strong>' + _('msimportexport_task_pid_status_completed') + '</strong></div>';
        }
        this.dashboard.body.update(html);
    },
    setup: function (config) {
        this.dashboard = Ext.getCmp('msie-panel-task-pid-dashboard-' + this.ident);
        this.timer = Ext.getCmp('msie-task-pid-timer-' + this.ident);
    },
    load: function () {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/task/pid/top',
                id: this.task
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.updateDashboard(r.object);
                        if (r.object.status === 'running') {
                            this.timer.reset();
                        }
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        });
    },
    destroy: function () {
        this.removeAll();
    }
});
Ext.reg('msie-panel-task-pid', Msie.panel.TaskPid);