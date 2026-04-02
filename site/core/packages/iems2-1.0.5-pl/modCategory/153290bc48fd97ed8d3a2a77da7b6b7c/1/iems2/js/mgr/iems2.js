var IeMs2 = function (config) {
    config = config || {};
    IeMs2.superclass.constructor.call(this, config);
};
Ext.extend(IeMs2, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},
    view: {},
    extra: {},
    utils: {},
    connector_url: ''
});
Ext.reg('IeMs2', IeMs2);
IeMs2 = new IeMs2();

