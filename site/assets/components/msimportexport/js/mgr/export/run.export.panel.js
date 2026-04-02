Msie.panel.RunExport = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Msie.panel.RunExport.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.RunExport, Msie.panel.RunImportExport, {
    run: function () {
        this.cmpBtnRun.setDisabled(true);
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/export/run',
                preset: this.settings.preset || 0,
                format: this.settings.format || '',
            },
            listeners: {
                success: {
                    fn: function (r) {
                        this.showReport(r.object.id);
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        this.cmpBtnRun.setDisabled(false);
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        });
    },
    completed: function (data) {
        this.cmpBtnRun.setDisabled(false);
        if (!this.getPresetSetting('save_to_file')) {
            var url = Msie.config.doUrl + '?act=download&token=' + data.token;
            window.open(url, '_blank');
        }
    },
});
Ext.reg('msie-panel-run-export', Msie.panel.RunExport);