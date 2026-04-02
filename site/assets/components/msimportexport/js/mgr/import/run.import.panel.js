Msie.panel.RunImport = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    Msie.panel.RunImport.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.RunImport, Msie.panel.RunImportExport, {
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
    run: function () {
        if (!this.file) {
            this.onUploadFile();
            return;
        }
        this.cmpBtnRun.setDisabled(true);
        MODx.Ajax.request({
            url: Msie.config.connector_url,
            params: {
                action: 'mgr/import/run',
                preset: this.settings.preset || 0,
                file: this.file,
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
        this.file = '';
    },
    onUploadFile: function () {
        var w = Ext.getCmp('msie-window-file-upload');
        if (w) {
            w.close();
        }
        w = MODx.load({
            xtype: 'msie-window-file-upload',
            id: 'msie-window-file-upload',
            preset: this.settings.preset,
            file: this.isPostFile() ? '' : this.settings.file,
            listeners: {
                success: {
                    fn: function (r) {
                        this.file = r.a.result.object.path + r.a.result.object.filename;
                        this.run();
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                    }, scope: this
                },
            }
        });
        w.show();

    },
    isPostFile: function () {
        var file = this.settings.file;
        if (!file) return true;
        if (file[0] === '/' || file.indexOf('http:') > -1 || file.indexOf('https:') > -1) return false;
        return true;
    },
});
Ext.reg('msie-panel-run-import', Msie.panel.RunImport);