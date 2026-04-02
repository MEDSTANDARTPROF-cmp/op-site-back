Msie.panel.SettingSystem = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    if (!config.id) {
        config.id = 'msie-panel-setting-system-' + this.ident;
    }
    Ext.applyIf(config, {
        layout: 'form',
        labelAlign: 'top',
        buttonAlign: 'left'
    });
    Msie.panel.SettingSystem.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.SettingSystem, Msie.panel.SettingForm, {
    getKeys: function (config) {
        var keys = [];
        keys.push({
            key: 's',
            ctrl: true,
            scope: this,
            stopEvent: true,
            fn: this.submit
        });
        return keys;
    },
    getButtons: function (config) {
        return [];
        /*return [{
            text: '<i class="icon icon-floppy-o"></i> ' + _('msimportexport_system_btn_save'),
            handler: this.submit,
            scope: this,
            cls: 'primary-button',
            iconCls: 'x-btn-small'
        }];*/
    },
    getListeners: function (config) {
        return {
            setup: function () {
                this.getForm().setValues(config.settings || {});
            }
        }
    },
    getFields: function (config) {
        return [{
            xtype: 'fieldset',
            title: _('msimportexport_system_requirements'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-system-fieldset-requirements',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-panel-system-requirement'
            }]
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_settings_sys_php_interpreter'),
            description: '<b>php_interpreter</b>',
            name: 'php_interpreter',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_sys_php_interpreter_help'),
            cls: 'desc-under'
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_watcher'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-system-fieldset-watcher',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-field-clipboard',
                fieldLabel: _('msimportexport_settings_sys_watcher_script_path'),
                readOnly: true,
                value: config.settings.watcher_script_path || '',
                name: 'watcher_script_path',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_watcher_script_path_help'),
                cls: 'desc-under'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_sys_watcher_max_count'),
                description: '<b>watcher_max_count</b>',
                name: 'watcher_max_count',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_watcher_max_count_help'),
                cls: 'desc-under'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_sys_watcher_wait'),
                description: '<b>watcher_wait</b>',
                name: 'watcher_wait',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_watcher_wait_help'),
                cls: 'desc-under'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_sys_watcher_lifetime'),
                description: '<b>watcher_lifetime</b>',
                name: 'watcher_lifetime',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_watcher_lifetime_help'),
                cls: 'desc-under'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'numberfield' : 'hidden',
                fieldLabel: _('msimportexport_settings_sys_mysql_wait_timeout'),
                description: '<b>mysql_wait_timeout</b>',
                name: 'mysql_wait_timeout',
                anchor: '100%'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'label' : 'hidden',
                html: _('msimportexport_settings_sys_mysql_wait_timeout_help'),
                cls: 'desc-under'
            }, {
                xtype: 'msie-combo-boolean',
                fieldLabel: _('msimportexport_settings_sys_watcher_debug'),
                description: '<b>watcher_debug</b>',
                name: 'watcher_debug',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_watcher_debug_help'),
                cls: 'desc-under'
            }]
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_other'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-system-fieldset-other',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_sys_task_refresh_freq'),
                description: '<b>task_list_refresh_freq</b>',
                name: 'task_list_refresh_freq',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_task_refresh_freq_help'),
                cls: 'desc-under'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'numberfield' : 'hidden',
                fieldLabel: _('msimportexport_settings_sys_pid_refresh_freq'),
                description: '<b>pid_refresh_freq</b>',
                name: 'pid_refresh_freq',
                anchor: '100%'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'label' : 'hidden',
                html: _('msimportexport_settings_sys_pid_refresh_freq_help'),
                cls: 'desc-under'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_sys_gc_file_maxlifetime'),
                description: '<b>gc_file_maxlifetime</b>',
                name: 'gc_file_maxlifetime',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_gc_file_maxlifetime_help'),
                cls: 'desc-under'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_sys_gc_task_maxlifetime'),
                description: '<b>gc_task_maxlifetime</b>',
                name: 'gc_task_maxlifetime',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_gc_task_maxlifetime_help'),
                cls: 'desc-under'
            }, {
                xtype: 'msie-field-cron-time',
                fieldLabel: _('msimportexport_settings_sys_gc_schedule'),
                description: '<b>gc_schedule</b>',
                name: 'gc_schedule',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_sys_gc_schedule_help'),
                cls: 'desc-under'
            }, {
                xtype: 'msie-field',
                fieldLabel: _('msimportexport_settings_sys_tmp_path'),
                description: '<b>tmp_path</b><br/>' + _('msimportexport_settings_sys_tmp_path_help'),
                name: 'tmp_path',
                anchor: '100%'
            }, {
                xtype: 'msie-field',
                fieldLabel: _('msimportexport_settings_sys_upload_path'),
                description: '<b>upload_path</b><br/>' + _('msimportexport_settings_sys_upload_path_help'),
                name: 'upload_path',
                anchor: '100%'
            }, {
                xtype: 'msie-field',
                fieldLabel: _('msimportexport_settings_sys_export_path'),
                description: '<b>export_path</b><br/>' + _('msimportexport_settings_sys_export_path_help'),
                name: 'export_path',
                anchor: '100%'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_sys_search_depth'),
                description: '<b>search_depth</b><br/>' + _('msimportexport_settings_sys_search_depth_help'),
                name: 'search_depth',
                anchor: '100%'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'msie-combo-boolean' : 'hidden',
                fieldLabel: _('msimportexport_settings_sys_daemon_mode'),
                description: '<b>daemon_mode</b>',
                name: 'daemon_mode',
                anchor: '100%'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'label' : 'hidden',
                html: _('msimportexport_settings_sys_daemon_mode_help'),
                cls: 'desc-under'
            }]
        }];
    },
    submit: function (e) {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/settings/system/update',
                settings: Ext.encode(this.getFormValues()),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        MODx.msg.status({
                            title: _('success'),
                            message: r.message ? r.message : _('msimportexport_settings_success_save'),
                            dontHide: false
                        });
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

});
Ext.reg('msie-panel-setting-system', Msie.panel.SettingSystem);