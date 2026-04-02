Msie.grid.Task = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    if (!config.id) {
        config.id = 'msie-grid-task-' + this.ident;
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/task/getList',
            sort: 'id',
            dir: 'DESC'
        },
        multi_select: true,
        autoExpandColumn: 'label',
        cls: 'msimportexport-grid-task',
        save_action: 'mgr/task/updateFromGrid',
    });

    Msie.grid.Task.superclass.constructor.call(this, config);
    this.setup(config);
    this.on('cellclick', function (grid, rowIndex, columnIndex, e) {
        if (e.getTarget('a')) {
            var record = grid.getStore().getAt(rowIndex);
            this.taskPid(record.get('id'), record.get('pid'));
        }
    }, this);
};
Ext.extend(Msie.grid.Task, Msie.grid.Default, {
    getFields: function (config) {
        return ['id', 'label', 'preset_id', 'cron_id', 'preset_name', 'preset_mode', 'pid', 'iteration', 'restarted', 'status', 'creator', 'settings', 'properties', 'priority', 'start_time', 'finish_time', 'total_time', 'actions'];
    },
    getColumns: function (config) {
        return [config.sm, {
            header: _('id'),
            dataIndex: 'id',
            sortable: true,
            width: 40,
        }, {
            header: _('msimportexport_task_header_label'),
            dataIndex: 'label',
            sortable: true,
            editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msimportexport_task_header_preset_mode'),
            dataIndex: 'preset_mode',
            width: 60,
            sortable: true,
            renderer: function (value) {
                return _('msimportexport_mode_' + value)
            }

        }, {
            header: _('msimportexport_task_header_preset_name'),
            dataIndex: 'preset_name',
            sortable: true
        }, {
            header: _('msimportexport_task_header_pid'),
            dataIndex: 'pid',
            width: 60,
            sortable: true,
            renderer: function (value, props, record) {
                if (
                    parseInt(value) > 0 &&
                    (record.data.status == 'running' || record.data.status == 'waiting_kill')
                ) {
                    return String.format('<a  href="#" class="importexport-link">{0}</a>', value);
                }
                return value;

            }
        }, {
            header: _('msimportexport_task_header_iteration'),
            dataIndex: 'iteration',
            width: 70,
            sortable: false
        }, {
            header: _('msimportexport_task_header_restarted'),
            dataIndex: 'restarted',
            width: 70,
            sortable: true
        }, {
            header: _('msimportexport_task_header_status'),
            dataIndex: 'status',
            sortable: true,
            renderer: function (value) {
                return _('msimportexport_task_status_' + value)
            }
        }, {
            header: _('msimportexport_task_header_creator'),
            dataIndex: 'cron_id',
            sortable: true,
            renderer: function (value) {
                if (value == 0) {
                    return _('msimportexport_task_creator_1');
                } else {
                    return _('msimportexport_task_creator_2');
                }
            }
        }, {
            header: _('msimportexport_task_header_start_time'),
            dataIndex: 'start_time',
            width: 90,
            sortable: true,
            renderer: Msie.utils.formatTimeStamp
        }, {
            header: _('msimportexport_task_header_finish_time'),
            hidden: true,
            dataIndex: 'finish_time',
            width: 90,
            sortable: true,
            renderer: Msie.utils.formatTimeStamp
        }, {
            header: _('msimportexport_task_header_total_time'),
            dataIndex: 'total_time',
            width: 90,
            sortable: false,
            renderer: Msie.utils.msToTime
        }, {
            header: _('msimportexport_task_header_actions'),
            id: 'actions',
            dataIndex: 'actions',
            width: 140,
            renderer: Msie.utils.renderActions,
        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        return tbar;
    },
    setup: function (config) {
    },
    taskAction: function (method, delay, params) {
        params = params || {};
        delay = typeof (delay) === 'undefined' ? 500 : delay;
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: Ext.applyIf({
                action: 'mgr/task/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            }, params),
            listeners: {
                success: {
                    fn: function (response) {
                        var self = this;
                        setTimeout(function () {
                            self.refresh();
                        }, delay);

                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        })
    },
    taskPid: function (task, pid) {
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
    taskReport: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        var w = Ext.getCmp('msie-window-task-report');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-task-report',
            id: 'msie-window-task-report',
            closeAction: 'close',
            task: id
        });
        w.show(e.target);

    },
    taskLog: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/task/log/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msie-window-task-log');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msie-window-task-log',
                            id: 'msie-window-task-log',
                            log: r.object.log,
                            task: id
                        });
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    taskRun: function (btn, e, row) {
        this.taskAction('run');
    },
    taskOpenPage: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        const mode = this.menu.record.preset_mode;
        const preset = this.menu.record.preset_id;
        window.open('?a=mgr/' + mode + '&namespace=msimportexport&preset=' + preset, '_blank');
    },
    taskStop: function (btn, e, row) {
        this._taskStop(false);
    },
    taskHardStop: function (btn, e, row) {
        this._taskStop(true);

    },
    taskRemove: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msimportexport_task_title_win_remove'),
            ids.length > 1
                ? _('msimportexport_task_confirm_multiple_remove')
                : _('msimportexport_task_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.taskAction('remove', 0);
                }
            }, this
        );
    },
    _taskStop: function (hard) {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msimportexport_task_title_win_stop'),
            ids.length > 1
                ? _('msimportexport_task_confirm_multiple_stop')
                : _('msimportexport_task_confirm_stop'),
            function (val) {
                if (val == 'yes') {
                    this.taskAction('stop', 500, {hard: hard});
                }
            }, this
        );
    }

});
Ext.reg('msie-grid-task', Msie.grid.Task);