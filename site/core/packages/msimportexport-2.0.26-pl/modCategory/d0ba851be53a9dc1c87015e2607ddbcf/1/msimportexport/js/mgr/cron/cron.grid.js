Msie.grid.Cron = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    if (!config.id) {
        config.id = 'msie-grid-cron-' + this.ident;
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/cron/getList',
            sort: 'id',
            dir: 'ASC'
        },
        multi_select: true,
        autoExpandColumn: 'preset_name',
        cls: 'msimportexport-grid-cron',
        save_action: 'mgr/cron/updateFromGrid',
    });

    Msie.grid.Cron.superclass.constructor.call(this, config);
    this.setup(config);
};
Ext.extend(Msie.grid.Cron, Msie.grid.Default, {
    getFields: function (config) {
        return ['id', 'preset_id', 'preset_name', 'preset_mode', 'description', 'schedule', 'date_last_run', 'active', 'actions'];
    },
    getColumns: function (config) {
        return [config.sm, {
            header: _('id'),
            dataIndex: 'id',
            width: 45,
            sortable: true,
        }, {
            header: _('msimportexport_cron_header_preset'),
            dataIndex: 'preset_name',
            sortable: true
        }, {
            header: _('msimportexport_cron_header_preset_mode'),
            dataIndex: 'preset_mode',
            width: 60,
            sortable: true,
            renderer: function (value) {
                return _('msimportexport_mode_' + value)
            }

        }, {
            header: _('msimportexport_cron_header_description'),
            dataIndex: 'description',
            sortable: true,
        }, {
            header: _('msimportexport_cron_header_schedule'),
            dataIndex: 'schedule',
            width: 70,
            sortable: true
        }, {
            header: _('msimportexport_cron_header_date_last_run'),
            dataIndex: 'date_last_run',
            width: 80,
            renderer: Msie.utils.formatDate,
            sortable: true,
        }, {
            header: _('msimportexport_cron_header_active'),
            dataIndex: 'active',
            width: 60,
            sortable: true,
            editor: {
                xtype: 'combo-boolean',
                renderer: 'boolean'
            }
        }, {
            header: _('msimportexport_cron_header_actions'),
            id: 'actions',
            dataIndex: 'actions',
            width: 60,
            renderer: Msie.utils.renderActions,
        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('msimportexport_cron_btn_create'),
            handler: this.cronCreate,
            cls: 'primary-button',
            scope: this
        });
        tbar.push('->', this.getSearchField());
        return tbar;
    },
    setup: function (config) {
    },
    cronAction: function (method, delay) {
        delay = typeof (delay) === 'undefined' ? 500 : delay;
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/cron/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
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
    cronCreate: function (btn, e, row) {
        var record = {
            active: 1
        };
        var w = Ext.getCmp('msie-window-cron');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-cron',
            id: 'msie-window-cron',
            record: record,
            listeners: {
                success: {
                    fn: function (r) {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(record);
        w.show(e.target);
    },
    cronUpdate: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/cron/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msie-window-cron');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msie-window-cron',
                            id: 'msie-window-cron',
                            record: r.object,
                            listeners: {
                                success: {
                                    fn: function (r) {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    cronRemove: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msimportexport_cron_title_win_remove'),
            ids.length > 1
                ? _('msimportexport_cron_confirm_multiple_remove')
                : _('msimportexport_cron_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.cronAction('remove', 0);
                }
            }, this
        );
    }

});
Ext.reg('msie-grid-cron', Msie.grid.Cron);