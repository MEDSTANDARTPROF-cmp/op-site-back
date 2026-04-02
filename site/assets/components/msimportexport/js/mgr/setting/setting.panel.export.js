Msie.panel.SettingExport = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Msie.panel.SettingExport.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.SettingExport, Msie.panel.SettingForm, {
    getFields: function (config) {
        return [{
            xtype: 'modx-tabs',
            stateful: true,
            stateId: config.id + '-tabs',
            stateEvents: ['tabchange'],
            forceLayout: true,
            deferredRender: false,
            border: true,
            defaults: {
                layoutOnTabChange: true
            },
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            },
            items: this.getTabs(config)
        }];
    },
    getTabs: function (config) {
        var tabs = [];
        tabs.push(this.getTabCommon(config));
        Ext.iterate(Msie.service || {}, function (key, service) {
            if (typeof (service.getTabSettings) == 'function') {
                var tab = service.getTabSettings(config);
                tabs.push(tab);
            }
        });
        return tabs;
    },
    getTabCommon: function (config) {
        return {
            title: _('msimportexport_settings_tab_options_common'),
            id: 'msie-tab-settings-options-export-common',
            layout: 'form',
            items: this.getCommonFields(config)
        };
    },
    getCommonFields: function (config) {
        return [{
            xtype: 'msie-field-access-token',
            fieldLabel: _('msimportexport_settings_export_link'),
            triggerGenerateText: _('msimportexport_settings_export_generate_new_link'),
            value: config.do_link || '',
            name: 'export_link',
            anchor: '100%',
            allowBlank: true,
            listeners: {
                generate: {
                    fn: this.handleGenerateExportLink, scope: this
                }
            },
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_export_link_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-combo-boolean',
            fieldLabel: _('msimportexport_settings_debug'),
            description: '<b>debug</b>',
            name: 'debug',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_debug_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-combo-export-format',
            fieldLabel: _('msimportexport_settings_export_format'),
            description: '<b>export_format</b><br>' + _('msimportexport_settings_export_format_help'),
            preset: config.preset,
            name: 'export_format',
            anchor: '100%',
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_settings_export_filename'),
            description: '<b>filename</b>',
            name: 'filename',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_export_filename_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_settings_export_path'),
            description: '<b>export_path</b>',
            name: 'export_path',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_export_path_help'),
            cls: 'desc-under'
        }, {
            xtype: 'numberfield',
            fieldLabel: _('msimportexport_settings_export_file_ttl'),
            description: '<b>file_ttl</b>',
            name: 'file_ttl',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_export_file_ttl_help'),
            cls: 'desc-under'
        }, {
            xtype: 'numberfield',
            fieldLabel: _('msimportexport_settings_download_lock_ttl'),
            description: '<b>download_lock_ttl</b>',
            name: 'download_lock_ttl',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_download_lock_ttl_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-combo-boolean',
            fieldLabel: _('msimportexport_settings_archive'),
            description: '<b>archive</b>',
            name: 'archive',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_archive_help'),
            cls: 'desc-under'
        }, {
            xtype: 'numberfield',
            fieldLabel: _('msimportexport_settings_skip_top_lines'),
            description: '<b>skip_top_lines</b>',
            name: 'skip_top_lines',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_skip_top_lines_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-combo-boolean',
            fieldLabel: _('msimportexport_settings_add_keys'),
            description: '<b>add_keys</b>',
            name: 'add_keys',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_add_keys_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-combo-boolean',
            fieldLabel: _('msimportexport_settings_add_fields'),
            description: '<b>add_fields</b>',
            name: 'add_fields',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_add_fields_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_settings_skip_conver_field_to_json'),
            description: '<b>skip_conver_field_to_json</b>',
            name: 'skip_conver_field_to_json',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_skip_conver_field_to_json_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_settings_first_delimiter'),
            description: '<b>first_delimiter</b><br/>' + _('msimportexport_settings_first_delimiter_help'),
            name: 'first_delimiter',
            anchor: '100%'
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_settings_second_delimiter'),
            description: '<b>second_delimiter</b><br/>' + _('msimportexport_settings_second_delimiter_help'),
            name: 'second_delimiter',
            anchor: '100%'
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_query_params'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateful: true,
            stateId: 'msie-export-settings-fieldset-query-params',
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_limit'),
                description: '<b>limit</b>',
                name: 'limit',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_limit_help'),
                cls: 'desc-under'
            }, {
                xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
                mimeType: 'application/json',
                height: 150,
                fieldLabel: _('msimportexport_settings_where'),
                description: '<b>where</b>',
                name: 'where',
                allowBlank: true,
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_where_help'),
                cls: 'desc-under',
            }, {
                xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
                mimeType: 'application/json',
                height: 150,
                fieldLabel: _('msimportexport_settings_leftjoin'),
                description: '<b>leftjoin</b>',
                name: 'leftjoin',
                allowBlank: true,
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_leftjoin_help'),
                cls: 'desc-under',
            }, {
                xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
                mimeType: 'application/json',
                height: 150,
                fieldLabel: _('msimportexport_settings_innerjoin'),
                description: '<b>innerjoin</b>',
                name: 'innerjoin',
                allowBlank: true,
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_innerjoin_help'),
                cls: 'desc-under',
            }, {
                xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
                mimeType: 'application/json',
                height: 150,
                fieldLabel: _('msimportexport_settings_select'),
                description: '<b>select</b>',
                name: 'select',
                allowBlank: true,
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_select_help'),
                cls: 'desc-under',
            }]
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_notice'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-export-settings-fieldset-notice',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('msimportexport_settings_notice'),
                description: '<b>notice</b><br/>' + _('msimportexport_settings_notice_help'),
                name: 'notice',
                anchor: '100%'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'msie-combo-notice-method' : 'hidden',
                fieldLabel: _('msimportexport_settings_notice_method'),
                description: '<b>notice_method</b><br/>' + _('msimportexport_settings_notice_method_help'),
                name: 'notice_method',
                anchor: '100%'
            }, {
                xtype: 'msie-field',
                fieldLabel: _('msimportexport_settings_notice_email'),
                description: '<b>notice_email</b>',
                name: 'notice_email',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_notice_email_help'),
                cls: 'desc-under'
            }, {
                xtype: 'msie-checkboxgroup',
                fieldLabel: _('msimportexport_settings_notice_status'),
                description: '<b>notice_status</b>',
                anchor: '100%',
                name: 'notice_status',
                columns: 4,
                items: [
                    {
                        boxLabel: _('msimportexport_task_status_initiated'),
                        inputValue: 'initiated',
                        checked: false
                    }, {
                        boxLabel: _('msimportexport_task_status_running'),
                        inputValue: 'running',
                        checked: false
                    }, {
                        boxLabel: _('msimportexport_task_status_waiting'),
                        inputValue: 'waiting',
                        checked: false
                    }, {
                        boxLabel: _('msimportexport_task_status_completed'),
                        inputValue: 'completed',
                        checked: false
                    }, {
                        boxLabel: _('msimportexport_task_status_stopped'),
                        inputValue: 'stopped',
                        checked: false
                    }, {
                        boxLabel: _('msimportexport_task_status_killed'),
                        inputValue: 'killed',
                        checked: false
                    }, {
                        boxLabel: _('msimportexport_task_status_failed'),
                        inputValue: 'failed',
                        checked: false
                    }
                ]
            }, {
                xtype: 'label',
                style: 'margin-top:10px',
                html: _('msimportexport_settings_notice_status_help'),
                cls: 'desc-under'
            }, {
                xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
                mimeType: 'text/plain',
                fieldLabel: _('msimportexport_settings_notice_template_subject'),
                description: '<b>notice_template_subject</b><br>' + _('msimportexport_settings_notice_template_subject_help'),
                name: 'notice_template_subject',
                height: 100,
                anchor: '100%'
            }, {
                xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
                mimeType: 'text/html',
                fieldLabel: _('msimportexport_settings_notice_template_message'),
                description: '<b>notice_template_message</b>',
                name: 'notice_template_message',
                height: 180,
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_notice_template_message_help'),
                cls: 'desc-under'
            }]
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_cvs'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-export-settings-fieldset-cvs',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-field',
                fieldLabel: _('msimportexport_settings_csv_delimiter'),
                description: '<b>csv_delimiter</b><br/>' + _('msimportexport_settings_csv_delimiter_help'),
                name: 'csv_delimiter',
                anchor: '100%'
            }, {
                xtype: 'msie-field',
                fieldLabel: _('msimportexport_settings_csv_enclosure'),
                description: '<b>csv_enclosure</b><br/>' + _('msimportexport_settings_csv_enclosure_help'),
                name: 'csv_enclosure',
                anchor: '100%'
            }, {
                xtype: 'msie-field',
                fieldLabel: _('msimportexport_settings_csv_escape'),
                description: '<b>csv_escape</b><br/>' + _('msimportexport_settings_csv_escape_help'),
                name: 'csv_escape',
                anchor: '100%'
            }]
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_other'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateful: true,
            stateId: 'msie-export-settings-fieldset-other',
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_iteration_report'),
                description: '<b>iteration_report</b><br/>' + _('msimportexport_settings_iteration_report_help'),
                name: 'iteration_report',
                anchor: '100%'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_task_refresh_freq'),
                description: '<b>task_refresh_freq</b><br/>' + _('msimportexport_settings_task_refresh_freq_help'),
                name: 'task_refresh_freq',
                anchor: '100%'
            }, {
                xtype: Msie.config.show_hidden_settings ? 'numberfield' : 'hidden',
                fieldLabel: _('msimportexport_settings_auto_restart_limit'),
                description: '<b>auto_restart_limit</b><br/>' + _('msimportexport_settings_auto_restart_limit_help'),
                name: 'auto_restart_limit',
                anchor: '100%'
            }, {
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_script_memory_limit'),
                description: '<b>script_memory_limit</b><br/>' + _('msimportexport_settings_script_memory_limit_help'),
                name: 'script_memory_limit',
                anchor: '100%'
            }]
        }];
    },
    getBtnSubmitText: function () {
        return '<i class="icon icon-floppy-o"></i> ' + _('msimportexport_settings_btn_save');
    },
    submit: function (e) {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/settings/preset/update',
                settings: Ext.encode(this.getFormValues()),
                id: this.preset,
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
    handleGenerateExportLink: function (field) {
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/preset/link/generate',
                id: this.preset
            },
            listeners: {
                success: {
                    fn: function (r) {
                        field.setValue(r.object.do_link);
                    }, scope: this
                }
            }
        });
    },

});
Ext.reg('msie-panel-setting-export', Msie.panel.SettingExport);