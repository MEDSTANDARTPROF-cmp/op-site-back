Msie.panel.TaskReport = function (config) {
    config = config || {};
    this.task = config.task;
    Ext.apply(config, {
        border: false,
        items: this.getFields(config),
    });
    Msie.panel.TaskReport.superclass.constructor.call(this, config);
    this.on('afterrender', function () {
        this.setup(config);
        this.load();
    }, this);
    this.on('beforedestroy', function () {
        this.destroy();
    }, this);

};
Ext.extend(Msie.panel.TaskReport, MODx.Panel, {
    getFields: function (config) {
        return [{
            xtype: 'msie-panel-timer',
            id: 'msie-task-timer',
            time: config.refresh_freq || 10,
            listeners: {
                complete: {
                    fn: this.load, scope: this
                }
            }
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_task_fieldset_stats'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-task-fieldset-stats',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'panel',
                cls: 'container msimportexport-task-report-dashboard',
                id: 'msie-panel-task-dashboard',
                anchor: '99%',
                listeners: {
                    render: {
                        fn: function (panel) {
                            panel.getEl().on('click', this.downloadDuplication, this, {
                                delegate: '.download-duplication'
                            });
                        },
                        scope: this
                    }
                }
            }]
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_task_fieldset_chart'),
            autoHeight: true,
            hideLabel: true,
            collapsible: true,
            stateId: 'msie-task-fieldset-chart',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-panel-task-chart',
                id: 'msie-panel-task-chart',
                style: 'margin-top: 25px',
                cls: 'container',
                anchor: '99%',
            }]
        }];
    },
    updateDashboard: function (data) {
        if (Msie.utils.isEmpty(data)) return;
        var keys = ['pid', 'status', 'restarted'];
        Ext.each(keys, function (key) {
            if (typeof (data[key]) !== undefined) {
                this.setDashboardValue(key, data[key]);
            }
        }, this);

        if (!Msie.utils.isEmpty(data['report'])) {
            var report = data['report'];
            var lastReport = report[report.length - 1];
            if (lastReport.total === undefined || data.mode == Msie.MODE_IMPORT || data.status == 'initiated') {
                this.setDashboardValue('iteration', lastReport.iteration);
            } else {
                this.setDashboardValue('iteration', lastReport.iteration + '/' + lastReport.total);
            }
            Ext.iterate(lastReport.stats_total || {}, function (key, val) {
                this.setDashboardValue(key, val);
            }, this);

            if (lastReport.cached) {
                this.setDashboardValue('cached', lastReport.cached);
            }

            if (lastReport.download && data.mode == Msie.MODE_EXPORT) {
                this.setDashboardValue('download', lastReport.download);
            }
        }

        if (!data['start_time']) return;
        var startTime = Msie.utils.formatTimeStamp(data['start_time']);
        this.setDashboardValue('start_time', startTime);

        if (!data['finish_time']) return;
        var finishTime = Msie.utils.formatTimeStamp(data['finish_time']);
        this.setDashboardValue('finish_time', finishTime);

        var totalTime = data['finish_time'] - data['start_time'];
        totalTime = Msie.utils.msToTime(totalTime);
        this.setDashboardValue('total_time', totalTime);

    },
    setDashboardValue: function (key, val) {
        switch (key) {
            case 'pid':
                if (this.dataset.status === 'running') {
                    val = '<a  href="#" onclick="Ext.getCmp(\'' + this.id + '\').pidInfo(' + this.task + ',' + val + '); return false;" class="msimportexport-link">' + val + '</a>';
                }
                break;
            case 'status':
                val = _('msimportexport_task_status_' + val);
                break;
            case 'errors':
                val = val ? '<a href="' + MODx.config.manager_url + '?a=system/event" target="_blank">' + val + '</a>' : val;
            case 'duplication':
                val = val ? '<a href="#" class="download-duplication">' + val + '</a>' : val;
                break;
            case 'cached':
                val = _('yes');
                break;
            case 'download':
                val = val ? '<a href="' + val + '" target="_blank" onclick="window.open(\'' + val + '\', \'_blank\')"><i class="icon icon-download"></i></a>' : '';
                break;
        }

        var html = '<label>' + _('msimportexport_task_dashboard_' + key) + '</label><span>' + val + '</span>';
        if (!this.dashboardItems[key]) {
            this.dashboardItems[key] = this.dashboard.add({
                html: html,
                cls: 'msimportexport-task-report-dashboard-item'
            });
            this.dashboard.doLayout();
        } else {
            this.dashboardItems[key].body.update(html);
        }
    },
    clearDashboard: function () {
        this.dashboardItems = [];
        this.dashboard.removeAll();
    },
    updateChart: function (data) {
        if (Msie.utils.isEmpty(data)) return;
        this.chart.setData(data);
    },
    pidInfo: function (task, pid) {
        var wid = 'msie-window-task-pid-' + this.ident,
            w = Ext.getCmp(wid);
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-task-pid',
            id: wid,
            closeAction: 'close',
            task: task,
            pid: pid,
        });
        w.show();

    },
    setup: function (config) {
        this.offset = 0;
        this.dataset = {};
        this.dashboardItems = [];
        this.delayCheckTaskStatus = 2000;
        this.timer = Ext.getCmp('msie-task-timer');
        this.chart = Ext.getCmp('msie-panel-task-chart');
        this.dashboard = Ext.getCmp('msie-panel-task-dashboard');
    },
    load: function () {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/task/report',
                id: this.task,
                offset: this.offset,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var resize = this.dashboardItems.length;
                        this.dataset = r.object;
                        this.updateDashboard(this.dataset);
                        this.updateChart(this.dataset.report);
                        if (!resize) {
                            setTimeout(function () {
                                window.dispatchEvent(new Event('resize'));
                            }, 300);
                        }
                        this.offset += r.object.report.length;
                        if (this.isReload(this.dataset)) {
                            this.timer.reset();
                        } else if (!Msie.utils.isEmpty(this.dataset)) {
                            this.fireEvent(this.dataset.status, this.dataset);
                        }
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
    },
    isReload: function (dataset) {
        if (Msie.utils.isEmpty(dataset)) return true;
        return ['completed', 'killed', 'failed'].indexOf(dataset.status) === -1 ? true : false;
    },
    destroy: function () {
        this.removeAll();
    },
    downloadDuplication: function (e) {
        e.preventDefault();
        Ext.Msg.show({
            title: _('please_wait'),
            progressText: _('upf_progress_wait'),
            width: 240,
            progress: true,
            closable: false
        });
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/import/duplication/download',
                task: this.task,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.checkTaskStatus(r.object.id);
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        Ext.Msg.hide();
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
    },
    checkTaskStatus: function (task) {
        var self = this;
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/task/report',
                id: task,
                last: true,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var total = parseInt(r.object.report.total || 0),
                            iteration = parseInt(r.object.report.iteration || 0);
                        if (total) {
                            Ext.Msg.updateProgress(iteration / total, iteration + '/' + total);
                        }
                        if (this.isReload(r.object)) {
                            setTimeout(function () {
                                self.checkTaskStatus(task);
                            }, this.delayCheckTaskStatus);
                        } else {
                            if (r.object.status == 'completed') {
                                var url = r.object.report.download || '';
                                if (url) {
                                    window.open(url, '_blank');
                                }
                            }
                            Ext.Msg.hide();
                        }
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        Ext.Msg.hide();
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
    }
});
Ext.reg('msie-panel-task-report', Msie.panel.TaskReport);