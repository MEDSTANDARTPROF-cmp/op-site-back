Msie.panel.Task = function (config) {
    config = config || {};
    Ext.apply(config, {
        ctCls: 'msimportexport-tbar-fixed',
        baseCls: 'tbar',
        cls: 'container',
        tbar: this.getTopBar(config),
        items: [{
            xtype: 'modx-tabs',
            id: 'msie-task-tabs',
            stateful: true,
            stateId: 'msie-task-tabs',
            stateEvents: ['tabchange'],
            items: this.getTabs(config),
            listeners: this.getListeners(config),
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },

        }]
    });
    Msie.panel.Task.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.Task, MODx.Panel, {
    grid: null,
    timer: null,
    getTopBar: function (config) {
        return ['->', {
            text: '<i class="icon icon-download"></i> ' + _('msimportexport_page_menu_import'),
            handler: Msie.utils.goToImport,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        },{
            text: '<i class="icon icon-upload"></i> ' + _('msimportexport_page_menu_export'),
            handler:  Msie.utils.goToExport,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        },{
            text: '<i class="icon icon-cog"></i> ' + _('msimportexport_page_menu_settings'),
            handler:  Msie.utils.goToSysSettings,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        }];
    },
    getListeners: function (config) {
        return {
            afterrender: {
                fn: this.setup, scope: this
            }
        };
    },
    getTabs: function (config) {
        return [
            {
                title: _('msimportexport_task_tab_tasks'),
                id: 'msie-tab-task',
                layout: 'anchor',
                items: this.getTaskFields(config)
            }, {
                title: _('msimportexport_cron_tab'),
                id: 'msie-tab-cron',
                layout: 'form',
                cls: 'container',
                items: this.getCronFields(config)
            }
        ];
    },
    getTaskFields: function (config) {
        return [{
            xtype: 'fieldset',
            cls: 'container',
            title: _('msimportexport_task_fieldset_filter'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-task-fieldset-filter',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-panel-task-filter'
            }]
        }, {
            xtype: 'msie-panel-timer',
            id: 'msie-timer-task-refresh',
            cls: 'container',
            time: Msie.config.sys_settings.task_list_refresh_freq,
            listeners: {
                complete: {
                    fn: this.refreshTasks, scope: this
                }
            }
        }, {
            xtype: 'msie-grid-task',
            cls: 'container',
            id: 'msie-grid-task',
            preventRender: true
        }];
    },
    getCronFields: function (config) {
        return [{
            xtype: 'msie-grid-cron',
            id: 'msie-grid-cron',
            preventRender: true
        }];
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
Ext.reg('msie-panel-task', Msie.panel.Task);