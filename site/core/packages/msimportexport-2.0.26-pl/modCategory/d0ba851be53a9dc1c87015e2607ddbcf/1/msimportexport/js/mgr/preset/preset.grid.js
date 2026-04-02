Msie.grid.preset = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'msie-grid-preset';
    }
    Ext.applyIf(config, {
        multi_select: true,
        baseParams: {
            action: 'mgr/preset/getlist',
            sort: 'id',
            dir: 'DESC',
            service: config.service || '',
            mode: config.mode || '',
        },
        save_action: 'mgr/preset/updateFromGrid',
    });

    Msie.grid.preset.superclass.constructor.call(this, config);
    this.addEvents('addPreset', 'removePreset');

};
Ext.extend(Msie.grid.preset, Msie.grid.Default, {
    getFields: function (config) {
        return ['id', 'name', 'description', 'mode', 'service', 'fields', 'has_menu', 'run_page_url', 'settings', 'actions'];
    },
    getColumns: function (config) {
        return [config.sm, {
            header: _('id')
            , dataIndex: 'id'
            , sortable: true
            , width: 10
        }, {
            header: _('msimportexport_preset_header_name')
            , dataIndex: 'name'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msimportexport_preset_header_description')
            , dataIndex: 'description'
            , sortable: true
            , editor: {
                xtype: 'textfield'
            }
        }, {
            header: _('msimportexport_preset_header_actions')
            , dataIndex: 'actions'
            , renderer: Msie.utils.renderActions
            , width: 80

        }];
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="icon icon-plus"></i> ' + _('msimportexport_preset_btn_create'),
            handler: this.addItem,
            cls: 'primary-button',
            scope: this
        });
        tbar.push({
            text: '<i class="fa fa-cogs"></i> ',
            menu: [{
                text: '<i class="fa fa-download"></i> ' + _('msimportexport_preset_menu_import'),
                cls: 'msimportexport-cogs',
                handler: this.importItem,
                scope: this
            }, '-', {
                text: '<i class="fa fa-upload"></i> ' + _('msimportexport_preset_menu_export'),
                cls: 'msimportexport-cogs',
                handler: this.exportItem,
                scope: this
            }, '-', {
                text: '<i class="fa fa-trash-o"></i> ' + _('msimportexport_preset_menu_remove'),
                cls: 'msimportexport-cogs',
                handler: this.removeItem,
                scope: this
            }]
        });
        tbar.push('->', this.getSearchField());
        return tbar;
    },
    actionItem: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/preset/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.refresh();
                        if (method == 'remove') {
                            this.fireEvent('removePreset', ids);
                        }

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
    addItem: function (btn, e, row) {
        var record = {
            service: this.service || '',
            mode: this.mode || '',
        };
        var w = Ext.getCmp('msie-window-preset-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-preset-create',
            id: 'msie-window-preset-create',
            record: record,
            listeners: {
                success: {
                    fn: function (r) {
                        this.refresh();
                        this.fireEvent('addPreset', r.a.result.object);
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(record);
        w.show(e.target);
    },
    duplicateItem: function (btn, e, row) {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/preset/duplicate',
                id: this.menu.record.id
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
    },
    createMenu(btn, e, row) {
        var record = {
            id: this.menu.record.id,
            parent: 'msimportexport_menu_importexport',
            btn_text: _('msimportexport_' + this.mode + '_btn_' + this.mode)
        };
        var w = Ext.getCmp('msie-window-preset-menu-create');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-preset-menu-create',
            id: 'msie-window-preset-menu-create',
            record: record,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.fp.getForm().reset();
        w.fp.getForm().setValues(record);
        w.show(e.target);
    },
    updateMenu(btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/system/menu/preset/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msie-window-preset-menu-update');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msie-window-preset-menu-update',
                            id: 'msie-window-preset-menu-update',
                            record: r.object,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                },
                            }
                        });
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    removeMenu(btn, e, row) {
        Ext.MessageBox.confirm(
            _('msimportexport_preset_title_window_remove_menu'),
            _('msimportexport_preset_confirm_remove_menu'),
            function (val) {
                if (val == 'yes') {
                    MODx.Ajax.request({
                        url: Msie.config.connector_url,
                        params: {
                            action: 'mgr/system/menu/preset/remove',
                            id: this.menu.record.id
                        },
                        listeners: {
                            success: {
                                fn: function () {
                                    this.refresh();
                                }, scope: this
                            }
                        }
                    });
                }
            }, this
        );


    },
    openRunPage(btn, e, row) {
        window.open(this.menu.record.run_page_url, '_blank');
    },
    importItem: function (btn, e, row) {
        var w = Ext.getCmp('msie-window-file-upload');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-file-upload',
            id: 'msie-window-file-upload',
            title: _('msimportexport_preset_title_window_import'),
            baseParams: {
                action: 'mgr/preset/import',
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.refresh();
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
        w.show();
    },
    exportItem: function (btn, e, row) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        location.href = Msie.config.connector_url + "?action=mgr/preset/export&HTTP_MODAUTH=" + MODx.siteId + "&ids=" + ids.join(',');
    },
    updateItem: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/preset/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = Ext.getCmp('msie-window-preset-setting');
                        if (w) {
                            w.close();
                        }
                        w = MODx.load({
                            xtype: 'msie-window-preset-setting',
                            id: 'msie-window-preset-setting',
                            record: r.object,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                },
                            }
                        });
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
    removeItem: function () {
        var ids = this._getSelectedIds();
        Ext.MessageBox.confirm(
            _('msimportexport_preset_title_window_remove'),
            ids.length > 1
                ? _('msimportexport_preset_confirm_multiple_remove')
                : _('msimportexport_preset_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.actionItem('remove');
                }
            }, this
        );
    },
});
Ext.reg('msie-grid-preset', Msie.grid.preset);