var IeYandexMarket = function (config) {
    config = config || {};
    IeYandexMarket.superclass.constructor.call(this, config);
};
Ext.extend(IeYandexMarket, Ext.Component, {
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
Ext.reg('IeYandexMarket', IeYandexMarket);
IeYandexMarket = new IeYandexMarket();