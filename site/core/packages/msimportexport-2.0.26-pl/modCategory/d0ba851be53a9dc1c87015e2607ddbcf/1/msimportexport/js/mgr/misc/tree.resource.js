Msie.tree.Resource = function (config) {
    config = config || {};
    Msie.tree.Resource.superclass.constructor.call(this, config);
};
Ext.extend(Msie.tree.Resource, Ext.form.Field, {
    defaultAutoCreate: {tag: 'input', type: 'hidden'},
    initComponent: function () {
        Ext.form.Field.superclass.initComponent.call(this);
        this.tree = new MODx.tree.Tree({
            url: Msie.config.connector_url,
            title: '',
            anchor: '100%',
            autoHeight: true,
            rootVisible: false,
            expandFirst: true,
            enableDD: false,
            ddGroup: 'modx-treedrop-dd',
            //tbar: {hidden: true},
            action: 'mgr/resource/getnodes',
            tbarCfg: {id: this.id ? this.id + '-tbar' : 'modx-tree-resource-tbar'},
            baseParams: {
                action: 'mgr/resource/getnodes',
                currentResource: MODx.request.id || 0,
                currentAction: MODx.request.a || 0,
                preset: this.preset || 0,
                class_key: this.class_key || 'modResource',
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
Ext.reg('msie-tree-resource', Msie.tree.Resource);