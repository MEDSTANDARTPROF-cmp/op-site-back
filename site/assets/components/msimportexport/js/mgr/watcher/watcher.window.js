Msie.window.WatcherPid = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('msimportexport_watcher_title_window_pid') + ' #' + config.pid,
        modal: true,
        //height: 700,
        width: 1000,
        stateful: false,
        resizable: true,
        collapsible: true,
        maximizable: true,
        autoHeight: true,
        autoScroll: true,
        items: this.getFields(config),
        buttons: this.getButtons(config)
    });
    Msie.window.WatcherPid.superclass.constructor.call(this, config);
};
Ext.extend(Msie.window.WatcherPid, Ext.Window, {
    getFields: function (config) {
        return [{
            xtype: 'msie-panel-watcher-pid',
            pid: config.pid
        }
        ];
    },
    getButtons: function (config) {
        return [{
            text: '<i class="icon icon-close"></i> ' + _('cancel'),
            scope: this,
            handler: function () {
                config.closeAction !== 'close'
                    ? this.hide()
                    : this.close();
            }
        }];
    },

});
Ext.reg('msie-window-watcher-pid', Msie.window.WatcherPid);