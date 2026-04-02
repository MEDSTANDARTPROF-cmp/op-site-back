IeGallery.combo.Gallery = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        displayField: 'name',
        valueField: 'key',
        fields: ['key', 'name'],
        typeAhead: true,
        forceSelection: true,
        url: IeGallery.config.connector_url,
        hiddenName: config.name || 'format',
        baseParams: {
            action: 'mgr/gallery/getlist',
            combo: true,
        },
        tpl: new Ext.XTemplate('\
          <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span style="font-weight: bold">{name}</span>\
                </div>\
          </tpl>')
    });
    IeGallery.combo.Gallery.superclass.constructor.call(this, config);
};
Ext.extend(IeGallery.combo.Gallery, Msie.combo.ComboBox);
Ext.reg('iegallery-combo-gallery', IeGallery.combo.Gallery);

IeGallery.combo.SortDir = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        store: new Ext.data.SimpleStore({
            fields: ['d', 'v'],
            data: [
                [_('iegallery_sortdir_asc'), 'ASC'],
                [_('iegallery_sortdir_desc'), 'DESC'],
            ]
        }),
        hiddenName: config.name || 'sortdir',
        custm: true,
        clear: true,
        addall: true,
        displayField: 'd',
        valueField: 'v',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        preventRender: true,
        forceSelection: true,
    });
    IeGallery.combo.SortDir.superclass.constructor.call(this, config);
};
Ext.extend(IeGallery.combo.SortDir, Msie.combo.ComboBox);
Ext.reg('iegallery-combo-sortdir', IeGallery.combo.SortDir);
