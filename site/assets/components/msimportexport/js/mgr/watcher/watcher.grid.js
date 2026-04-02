Msie.grid.Watcher = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    if (!config.id) {
        config.id = 'msie-grid-watcher-' + this.ident;
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/watcher/getList',
        },
        multi_select: true,
        autoExpandColumn: 'pid',
        cls: 'msimportexport-grid-watcher',
    });

    Msie.grid.Watcher.superclass.constructor.call(this, config);
    this.setup(config);
    this.on('cellclick', function (grid, rowIndex, columnIndex, e) {
        if (e.getTarget('a')) {
            var record = grid.getStore().getAt(rowIndex);
            this.watcherPidInfo(record.get('pid'));
        }
    }, this);
};
Ext.extend(Msie.grid.Watcher, Msie.grid.Default, {
    getFields: function (config) {
        return ['pid', 'status','time', 'max_execution_time', 'actions'];
    },
    getColumns: function (config) {
        return [config.sm, {
            header: _('msimportexport_watcher_header_pid'),
            dataIndex: 'pid',
            sortable: false,
            renderer: function (value, props, record) {
                if (parseInt(value) < 0) return value;
                return String.format('<a  href="#" class="msimportexport-link">{0}</a>', value);
            }
        }, {
            header: _('msimportexport_watcher_header_status'),
            dataIndex: 'status',
            renderer: function (val) {
                return _('msimportexport_watcher_status_'+val);
            },
            sortable: false,

        }, {
            header: _('msimportexport_watcher_header_time'),
            dataIndex: 'time',
            renderer: Msie.utils.formatDate,
            sortable: false,

        }, {
            header: _('msimportexport_watcher_header_max_execution_time'),
            dataIndex: 'max_execution_time',
            renderer: function (val) {
                if (parseInt(val) == 0) {
                    val = _('msimportexport_watcher_execution_time_unlimited');
                }
                return val;
            },
            sortable: false,

        }, {
            header: _('msimportexport_watcher_header_actions'),
            id: 'actions',
            dataIndex: 'actions',
            width: 35,
            renderer: Msie.utils.renderActions,
        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        return tbar;
    },
    setup: function (config) {
    },
    watcherAction: function (method, delay) {
        delay = typeof (delay) === 'undefined' ? 500 : delay;
        var pids = this.getSelectedPids();
        if (!pids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/watcher/multiple',
                method: method,
                pids: Ext.util.JSON.encode(pids),
            },
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
    watcherPidInfo: function (pid) {
        var wid = 'msie-window-watcher-pid-' + this.ident,
            w = Ext.getCmp(wid);
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-watcher-pid',
            id: wid,
            closeAction: 'close',
            pid: pid,
        });
        w.show();
    },
    watcherPid: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        this.watcherPidInfo(this.menu.record.pid);
    },
    watcherRemove: function () {
        var pids = this.getSelectedPids();
        Ext.MessageBox.confirm(
            _('msimportexport_watcher_title_win_remove'),
            pids.length > 1
                ? _('msimportexport_watcher_confirm_multiple_remove')
                : _('msimportexport_watcher_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.watcherAction('remove', 0);
                }
            }, this
        );
    },
    getSelectedPids: function () {
        var pids = [];
        var selected = this.getSelectionModel().getSelections();
        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }

            pids.push(selected[i]['data']['pid']);
        }
        return pids;
    },

});
Ext.reg('msie-grid-watcher', Msie.grid.Watcher);