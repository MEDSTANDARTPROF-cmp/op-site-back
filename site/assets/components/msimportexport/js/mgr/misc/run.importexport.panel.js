Msie.panel.RunImportExport = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'msie-panel-run',
        baseCls: 'modx-formpanel',
        cls: 'container',
        items: [{
            xtype: 'modx-tabs',
            id: 'msie-run-tabs',
            anchor: '100% 100%',
            forceLayout: true,
            deferredRender: false,
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            items: [{
                title: _('msimportexport_' + config.settings.mode + '_tab_' + config.settings.mode),
                layout: 'form',
                labelAlign: 'top',
                labelSeparator: '',
                baseCls: 'modx-formpanel',
                cls: 'container',
                autoHeight: true,
                collapsible: false,
                animCollapse: false,
                hideMode: 'offsets',
                items: this.getFields(config)
            }]
        }],
        listeners: this.getListeners(config)
    });
    Msie.panel.RunImportExport.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.RunImportExport, MODx.Panel, {
    getListeners: function (config) {
        return {
            afterrender: {
                fn: function () {
                    this.setup(config);
                }, scope: this
            }
        }
    },
    setup: function (config) {
        this.cmpBtnRun = Ext.getCmp('msie-panel-import-export-btn-run');
        this.cmpReport = Ext.getCmp('msie-panel-import-export-report');
    },
    getFields: function (config) {
        var fields = [];
        fields.push(this.getControlPanel(config));
        fields.push({
                xtype: 'label',
                html: config.settings.descRun,
                style: 'margin-top:10px;',
                cls: 'desc-under'
            }
        );
        fields.push({
            xtype: 'panel',
            id: 'msie-panel-import-export-report',
        });
        return fields;
    },
    getControlPanel: function (config) {
        return {
            xtype: 'panel',
            id: 'msie-panel-import-export-control',
            style: 'padding-top:15px',
            items: [{
                xtype: 'button',
                id: 'msie-panel-import-export-btn-run',
                handler: this.run,
                scope: this,
                cls: 'btn-sm primary-button',
                iconCls: 'x-btn-small',
                text: '<i class="icon icon-play"></i> ' + config.settings.btnRunText
            }, {
                xtype: config.settings.showBtnTask ? 'button' : 'hidden',
                handler: Msie.utils.goToTaskManager,
                scope: this,
                style: 'margin-left:10px',
                cls: 'btn-sm',
                iconCls: 'x-btn-small',
                text: '<i class="icon icon-tasks"></i> ' + _('msimportexport_export_btn_task')
            }]
        };
    },
    run: function () {
        this.cmpBtnRun.setDisabled(true);
    },
    completed: function (data) {
        this.cmpBtnRun.setDisabled(false);
    },
    reset: function () {
        this.cmpBtnRun.setDisabled(false);
        this.hideReport();
    },
    showReport: function (taskId) {
        this.hideReport();
        this.cmpReport.add({
            xtype: 'msie-panel-task-report',
            style: 'margin-top:25px;',
            task: taskId,
            refresh_freq: this.getPresetSetting('task_refresh_freq', 10),
            listeners: {
                completed: {
                    fn: this.completed,
                    scope: this
                }
            }
        });
        this.doLayout();
    },
    hideReport: function () {
        this.cmpReport.removeAll();
        this.doLayout();
    },
    getPresetSetting: function (key, def) {
        var settings = this.settings.presetSettings;
        if (settings && settings[key] !== undefined) {
            return settings[key];
        } else {
            return def || null;
        }
    },


});
Ext.reg('msie-panel-run-import-export', Msie.panel.RunImportExport);