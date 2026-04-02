Msie.panel.WatcherPid = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    Ext.apply(config, {
        border: false,
        cls: 'container',
        items: this.getFields(config),
        listeners: this.getListeners(config),
    });
    Msie.panel.WatcherPid.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.WatcherPid, MODx.Panel, {
    getFields: function (config) {
        return [{
            xtype: 'msie-panel-timer',
            id: 'msie-watcher-pid-timer-' + this.ident,
            style: 'margin-bottom: 15px',
            time: Msie.config.sys_settings.pid_refresh_freq,
            listeners: {
                complete: {
                    fn: this.load, scope: this
                }
            }
        }, {
            xtype: 'panel',
            cls: 'msie-watcher-pid-dashboard',
            id: 'msie-panel-watcher-pid-dashboard-' + this.ident,
            items: [{
                html: '<div class="msimportexport-watcher-pid-dashboard-item">' + _('msimportexport_watcher_pid_status_loading') + '</div>'
            }],
            anchor: '100%',
        }, {
            xtype: 'label',
            cls: 'desc-under',
            html: _('msimportexport_watcher_pid_help'),
            anchor: '100%'
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
            html += '<div class="msimportexport-watcher-pid-dashboard-item">' + val + '</div>';
        }, this);
        this.dashboard.body.update(html);
    },
    setup: function (config) {
        this.dashboard = Ext.getCmp('msie-panel-watcher-pid-dashboard-' + this.ident);
        this.timer = Ext.getCmp('msie-watcher-pid-timer-' + this.ident);
    },
    load: function () {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/watcher/pid/top',
                pid: this.pid
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.updateDashboard(r.object);
                        this.timer.reset();
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
Ext.reg('msie-panel-watcher-pid', Msie.panel.WatcherPid);