Msie.panel.SystemRequirement = function (config) {
    config = config || {};
    this.ident = config.ident || Ext.id();
    Ext.apply(config, {
        border: false,
        buttonAlign: 'left',
        items: this.getFields(config),
        listeners: this.getListeners(config),
        buttons: this.getButtons(config),
    });
    Msie.panel.SystemRequirement.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.SystemRequirement, MODx.Panel, {
    getFields: function (config) {
        return [{
            xtype: 'panel',
            cls: 'msimportexport-system-requirement-dashboard',
            id: 'msie-panel-system-requirement-dashboard-' + this.ident,
            anchor: '100%',
        }];
    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-check-circle"></i> ' + _('msimportexport_system_requirement_btn_check'),
            handler: this.load,
            scope: this,
            cls: 'btn-sm',
            iconCls: 'x-btn-small'
        }];
    },
    getListeners: function (config) {
        return {
            afterrender: {
                fn: function () {
                    this.setup(config);
                    this.load();
                }, scope: this
            },
        };
    },
    updateDashboard: function (data) {
        if (Msie.utils.isEmpty(data)) return;
        var html = '';
        Ext.each(data, function (item) {
            var status = item.status == true ? 'ok' : 'error',
                icon = item.status == true ? 'check-circle' : 'times-circle';

            html += '<div class="msimportexport-system-requirement-dashboard-item status-' + status + ' level-' + item.level + '" title="' + item.title + '">' +
                '<label>' + item.label + '</label><span><i class="icon icon-' + icon + '"></i></span>' +
                '</div>';
        }, this);
        this.dashboard.body.update(html);
    },
    setup: function (config) {
        this.dashboard = Ext.getCmp('msie-panel-system-requirement-dashboard-' + this.ident);
        this.mask = new Ext.LoadMask(this.getEl(), {msg: _('loading')});
    },
    load: function () {
        this.mask.show();
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/system/requirement/get',
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.updateDashboard(r.object);
                        this.mask.hide();
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        this.mask.hide();
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                }
            }
        })
    }
});
Ext.reg('msie-panel-system-requirement', Msie.panel.SystemRequirement);