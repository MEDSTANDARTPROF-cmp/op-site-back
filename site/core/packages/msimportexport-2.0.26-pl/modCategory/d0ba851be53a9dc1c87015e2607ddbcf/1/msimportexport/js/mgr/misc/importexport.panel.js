Msie.panel.ImportExport = function (config) {
    config = config || {};
    config.mode = Msie.config.mode;
    Ext.applyIf(config, {
        id: 'msie-panel-' + config.mode,
        ctCls: 'msimportexport-tbar-fixed',
        baseCls: 'modx-formpanel tbar',
        cls: 'container',
        textBtnRun: _('msimportexport_' + config.mode + '_btn_' + config.mode),
        items: [{
            xtype: 'modx-tabs',
            id: 'msie-' + config.mode + '-tabs',
            anchor: '100% 100%',
            forceLayout: true,
            deferredRender: false,
            stateEvents: ['tabchange'],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            items: [{
                title: _('msimportexport_' + config.mode + '_tab_' + config.mode),
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
    Msie.panel.ImportExport.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.ImportExport, MODx.Panel, {
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
        this.cmpService = Ext.getCmp('msie-combo-import-export-service');
        this.cmpPreset = Ext.getCmp('msie-combo-import-export-preset');
        this.cmpControl = Ext.getCmp('msie-panel-import-export-control');
        this.cmpBtnRun = Ext.getCmp('msie-panel-import-export-btn-run');
        this.cmpReport = Ext.getCmp('msie-panel-import-export-report');
    },
    getFields: function (config) {
        return [
            this.getControlPanel(config),
            this.getReportPanel(config),
        ];
    },
    getControlPanel: function (config) {
        return {
            xtype: 'panel',
            id: 'msie-panel-import-export-control',
            style: 'padding-top:15px',
            listeners: {
                afterrender: {
                    fn: function (panel) {
                        panel.getEl().dom.style.display = 'none';
                    }, scope: this
                }
            },
            items: [{
                xtype: 'button',
                id: 'msie-panel-import-export-btn-run',
                handler: this.run,
                scope: this,
                cls: 'btn-sm primary-button',
                iconCls: 'x-btn-small',
                text: '<i class="icon icon-play"></i> ' + config.btnRunText
            }]
        }
    },
    getReportPanel: function (config) {
        return {
            xtype: 'panel',
            id: 'msie-panel-import-export-report',
        }
    },
    run: function () {
        this.cmpBtnRun.setDisabled(true);
    },
    completed: function (data) {
        this.cmpBtnRun.setDisabled(false);
    },
    getPresetSettings: function () {
        return this.cmpPreset.getSelectedRecord();
    },
    getPresetSetting: function (key, def) {
        var record = this.getPresetSettings();
        if (record && record.data.settings && record.data.settings[key] !== undefined) {
            return record.data.settings[key];
        } else {
            return def || null;
        }
    },
    reset: function () {
        this.cmpBtnRun.setDisabled(false);
        this.hideControl();
        this.hideReport();
    },
    showControl: function () {
        if (!this.cmpControl.isDisplay) {
            this.cmpControl.isDisplay = true;
            this.cmpControl.getEl().slideIn('t', {useDisplay: true});
        }
    },
    hideControl: function () {
        this.cmpControl.getEl().slideOut('t', {useDisplay: true});
        this.cmpControl.isDisplay = false;
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
    }
});
Ext.reg('msie-panel-import-export', Msie.panel.ImportExport);