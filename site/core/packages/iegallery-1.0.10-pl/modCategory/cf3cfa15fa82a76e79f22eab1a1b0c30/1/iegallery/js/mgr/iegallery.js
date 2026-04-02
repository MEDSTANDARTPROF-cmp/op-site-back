var IeGallery = function (config) {
    config = config || {};
    IeGallery.superclass.constructor.call(this, config);
};
Ext.extend(IeGallery, Ext.Component, {
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
Ext.reg('IeGallery', IeGallery);
IeGallery = new IeGallery();