Msie.panel.SettingImport = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Msie.panel.SettingImport.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.SettingImport, Msie.panel.SettingForm, {
    getFields: function (config) {
        return [{
            xtype: 'modx-tabs',
            stateful: true,
            stateId: config.id + '-tabs',
            stateEvents: ['tabchange'],
            deferredRender: true,
            border: true,
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
            id: 'msie-tab-settings-options-import-common',
            layout: 'form',
            items: this.getCommonFields(config)
        };
    },
    getCommonFields: function (config) {
        return [{
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
            xtype: 'numberfield',
            fieldLabel: _('msimportexport_settings_start_from_line'),
            description: '<b>start_from_line</b>',
            name: 'start_from_line',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_start_from_line_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-field',
            fieldLabel: _('msimportexport_settings_file'),
            description: '<b>file</b>',
            name: 'file',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_file_help'),
            cls: 'desc-under'
        }, {
            xtype: 'msie-combo-boolean',
            fieldLabel: _('msimportexport_settings_remove_source_file'),
            name: 'remove_source_file',
            description: '<b>remove_source_file</b>',
            anchor: '100%'
        }, {
            xtype: 'label',
            html: _('msimportexport_settings_remove_source_file_help'),
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
            xtype: 'numberfield',
            fieldLabel: _('msimportexport_settings_auto_restart_limit'),
            description: '<b>auto_restart_limit</b><br/>' + _('msimportexport_settings_auto_restart_limit_help'),
            name: 'auto_restart_limit',
            anchor: '100%'
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_xlsx'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-import-settings-fieldset-xlsx',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('msimportexport_settings_xlsx_only_first_sheet'),
                description: '<b>xlsx_only_first_sheet</b>',
                name: 'xlsx_only_first_sheet',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_xlsx_only_first_sheet_help'),
                cls: 'desc-under'
            }, {
                xtype: 'textfield',
                fieldLabel: _('msimportexport_settings_xlsx_skip_sheet'),
                description: '<b>xlsx_skip_sheet</b>',
                name: 'xlsx_skip_sheet',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_xlsx_skip_sheet_help'),
                cls: 'desc-under'
            }]
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_cvs'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-import-settings-fieldset-cvs',
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
            }, {
                xtype: 'msie-combo-boolean',
                fieldLabel: _('msimportexport_settings_csv_convert_encoding'),
                description: '<b>convert_encoding</b>',
                name: 'convert_encoding',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_csv_convert_encoding_help'),
                cls: 'desc-under'
            }, {
                xtype: 'textfield',
                fieldLabel: _('msimportexport_settings_csv_source_encode'),
                description: '<b>source_encode</b>',
                name: 'source_encode',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('msimportexport_settings_csv_source_encode_help'),
                cls: 'desc-under'
            }]
        }, {
            xtype: 'fieldset',
            title: _('msimportexport_settings_fieldset_notice'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateId: 'msie-import-settings-fieldset-notice',
            stateful: true,
            stateEvents: ['collapse', 'expand'],
            items: [{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('msimportexport_settings_notice'),
                description: '<b>notice</b><br/>' + _('msimportexport_settings_notice_help'),
                name: 'notice',
                anchor: '100%'
            }, {
                xtype:  Msie.config.show_hidden_settings ? 'msie-combo-notice-method' : 'hidden',
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
            title: _('msimportexport_settings_fieldset_other'),
            hideLabel: true,
            collapsible: true,
            autoHeight: true,
            stateful: true,
            stateId: 'msie-import-settings-fieldset-other',
            stateEvents: ['collapse', 'expand'],
            items: [/*{
                xtype: 'numberfield',
                fieldLabel: _('msimportexport_settings_script_time_limit'),
                description: '<b>script_time_limit</b><br/>' + _('msimportexport_settings_script_time_limit_help'),
                name: 'script_time_limit',
                anchor: '100%'
            },*/ {
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

});
Ext.reg('msie-panel-setting-import', Msie.panel.SettingImport);