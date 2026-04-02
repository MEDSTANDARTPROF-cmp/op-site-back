Msie.window.CreateCron = function (config) {
    config = config || {};
    var r = config.record;
    Ext.applyIf(config, {
        title: r.id ? _('msimportexport_cron_title_window_update') : _('msimportexport_cron_title_window_create'),
        baseParams: {
            action: r.id ? 'mgr/cron/update' : 'mgr/cron/create'
        }
    });
    Msie.window.CreateCron.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.CreateCron, Msie.window.Default, {
    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'msie-combo-preset',
            fieldLabel: _('msimportexport_cron_label_preset'),
            description: _('msimportexport_cron_label_preset_help'),
            name: 'preset_id',
            allowBlank: false,
            anchor: '100%'
        }, {
            xtype: 'msie-field-cron-time',
            fieldLabel: _('msimportexport_cron_label_schedule'),
            description: _('msimportexport_cron_label_schedule_help'),
            name: 'schedule',
            allowBlank: false,
            anchor: '100%'
        }, {
            xtype: 'textarea',
            fieldLabel: _('msimportexport_cron_label_description'),
            description: _('msimportexport_cron_label_description_help'),
            name: 'description',
            allowBlank: true,
            anchor: '100%'
        }, {
            xtype: 'msie-combo-boolean',
            fieldLabel: _('msimportexport_cron_label_active'),
            description: _('msimportexport_cron_label_active_help'),
            name: 'active',
            anchor: '100%'
        }];
    },

});
Ext.reg('msie-window-cron', Msie.window.CreateCron);