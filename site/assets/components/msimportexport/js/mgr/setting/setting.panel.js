Msie.panel.Setting = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    if (!config.id) {
        config.id = 'msie-panel-setting-' + this.ident;
    }

    Ext.applyIf(config, {
        labelAlign: 'top',
        buttonAlign: 'left',
        cls: 'panel-wrapper msimportexport-settings-tabs',
        tbar: this.getTopBar(config),
        items: [{
            xtype: 'modx-tabs',
            stateful: true,
            stateId: config.id + '-tabs',
            stateEvents: ['tabchange'],
            deferredRender: false,
            border: true,
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            listeners: {
                tabchange: {
                    fn: this.onTabChange,
                    scope: this
                }
            },
            items: this.getTabs(config)
        }]
    });
    Msie.panel.Setting.superclass.constructor.call(this, config);
    this.addEvents('formchange');
    this.setup(config);
};
Ext.extend(Msie.panel.Setting, MODx.Panel, {
    setup: function (config) {
        if (!config.preset) {
            this.baseCls = 'modx-formpanel';
            this.ctCls = 'msimportexport-tbar-fixed';
            this.cls = 'container';
        }
    },
    getTopBar: function (config) {
        if (config.preset) return [];
        return ['->', {
            text: '<i class="icon icon-floppy-o"></i> ' + _('msimportexport_page_menu_save_system_settings'),
            handler: this.saveSystemSettings,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm primary-button',
        }, {
            text: '<i class="icon icon-download"></i> ' + _('msimportexport_page_menu_import'),
            handler: Msie.utils.goToImport,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        }, {
            text: '<i class="icon icon-upload"></i> ' + _('msimportexport_page_menu_export'),
            handler: Msie.utils.goToExport,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        }, {
            text: '<i class="icon icon-bars"></i> ' + _('msimportexport_page_menu_task'),
            handler: Msie.utils.goToTaskManager,
            scope: this,
            iconCls: 'x-btn-small',
            cls: 'btn-sm',
        }];
    },
    getTabs: function (config) {
        var tabs = [];
        if (!config.preset) {
            tabs.push(this.getTabSystemSetting(config));
            tabs.push(this.getTabSystemWatchers(config));
        } else {
            tabs.push(this.getTabSetting(config));
            tabs.push(this.getTabFields(config));
        }
        return tabs;
    },
    getTabSystemSetting: function (config) {
        return {
            title: _('msimportexport_settings_tab_system'),
            id: 'msie-tab-settings-system',
            layout: 'form',
            cls: 'main-wrapper',
            items: [{
                xtype: 'msie-panel-setting-system',
                id: 'msie-panel-setting-system',
                settings: config.settings

            }]
        };
    },
    getTabSystemWatchers: function (config) {
        return {
            title: _('msimportexport_settings_tab_watchers'),
            id: 'msie-tab-settings-watchers',
            stateId: 'msie-tab-settings-watchers',
            layout: 'form',
            cls: 'main-wrapper',
            items: [{
                xtype: 'msie-grid-watcher',
            }]
        };
    },
    getTabSetting: function (config) {
        return {
            title: _('msimportexport_settings_tab_options'),
            id: 'msie-tab-settings-options',
            stateId: 'msie-tab-settings-options-' + config.mode,
            layout: 'form',
            cls: 'main-wrapper msimportexport-settings-tab-options',
            items: [{
                xtype: 'msie-panel-setting-' + config.mode,
                id: 'msie-panel-setting-options',
                mode: config.mode,
                preset: config.preset,
                do_link: config.do_link,
                settings: config.settings,
                parent_class_key: config.parent_class_key,
            }]
        };
    },
    getTabFields: function (config) {
        return {
            title: _('msimportexport_settings_tab_fields'),
            id: 'msie-tab-settings-fields',
            layout: 'form',
            cls: 'main-wrapper',
            items: [{
                xtype: 'msie-panel-setting-field',
                id: 'msie-panel-setting-field',
                mode: config.mode,
                preset: config.preset,
                fields: config.fields
            }]
        };
    },
    onTabChange: function (tabPanel, tab) {
        var form = null;
        switch (tab.id) {
            case 'msie-tab-settings-options':
                form = Ext.getCmp('msie-panel-setting-options');
                break
            case 'msie-tab-settings-fields':
                form = Ext.getCmp('msie-panel-setting-field');
                break
        }
        this.fireEvent('formchange', form);
    },
    saveSystemSettings: function (e) {
        var panel = Ext.getCmp('msie-panel-setting-system');
        panel.submit();
    },
    isSystem: function () {
        return (this.preset || this.mode) ? false : true;
    }

});
Ext.reg('msie-panel-setting', Msie.panel.Setting);