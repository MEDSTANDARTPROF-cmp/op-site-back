IeMs2.tree.Vendor = function (config) {
    config = config || {};
    IeMs2.tree.Vendor.superclass.constructor.call(this, config);
};
Ext.extend(IeMs2.tree.Vendor, MODx.tree.Tree);
Ext.extend(IeMs2.tree.Vendor, Ext.form.Field, {
    defaultAutoCreate: {tag: 'input', type: 'hidden'},
    initComponent: function () {
        Ext.form.Field.superclass.initComponent.call(this);
        this.tree = new MODx.tree.Tree({
            id: 'iems2-tree-vendors',
            url: IeMs2.config.connector_url,
            action: 'mgr/vendor/getnodes',
            root_id: 'vendor_root',
            root_name: _('iems2_iems2productexportservice_setting_vendors'),
            enableDD: false,
            rootVisible: true,
            autoHeight: true,
          //  remoteToolbar: false,
           // tbar: {hidden: true},
            baseParams: {
                action: 'mgr/vendor/getnodes',
                preset: this.preset || 0,
            },
            listeners: {
                checkchange: {
                    fn: function (node, checked) {
                        var ids = this.tree.getChecked('pk');
                        this.setValue(ids.join(','));
                    }, scope: this
                }
            }
        });
    },
    onRender: function (ct, position) {
        if (this.isRendered) {
            return;
        }
        Ext.form.Field.superclass.onRender.call(this, ct, position);
        this.tree.render(this.el);
    },
});
Ext.reg('iems2-tree-vendor', IeMs2.tree.Vendor);