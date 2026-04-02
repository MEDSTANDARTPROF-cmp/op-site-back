Msie.service.IeMs2ProductImportService = {
    getTabSettings: function (config) {
        return {
            title: _('iems2_iems2productimportservice_setting_tab'),
            id: 'iems2-setting-tab-iems2productimportservice',
            layout: 'form',
            items: [{
                title: _('iems2_iems2productimportservice_setting_fieldset_article'),
                xtype: 'fieldset',
                hideLabel: true,
                collapsible: true,
                stateful: true,
                stateId: 'msie-import-tab-setting-service-product-fieldset-article',
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('iems2_iems2productimportservice_setting_create_article'),
                    description: '<b>create_article</b>',
                    name: 'create_article',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('iems2_iems2productimportservice_setting_create_article_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-field',
                    fieldLabel: _('iems2_iems2productimportservice_setting_template_article'),
                    name: 'template_article',
                    description: '<b>template_article</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('iems2_iems2productimportservice_setting_template_article_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('iems2_iems2productimportservice_setting_fieldset_default'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateful: true,
                stateId: 'msie-import-tab-setting-service-product-fieldset-default',
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'msie-combo-template',
                    fieldLabel: _('iems2_iems2productimportservice_setting_template_default'),
                    description: '<b>template_product_default</b><br/>' + _('iems2_iems2productimportservice_setting_template_default_help'),
                    name: 'template_product_default',
                    anchor: '100%'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('iems2_iems2productimportservice_setting_published_default'),
                    description: '<b>published_product_default</b><br/>' + _('iems2_iems2productimportservice_setting_published_default_help'),
                    name: 'published_product_default',
                    anchor: '100%'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('iems2_iems2productimportservice_setting_hidemenu_default'),
                    description: '<b>hidemenu_product_default</b><br/>' + _('iems2_iems2productimportservice_setting_hidemenu_default_help'),
                    name: 'hidemenu_product_default',
                    anchor: '100%'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('iems2_iems2productimportservice_setting_searchable_default'),
                    description: '<b>searchable_product_default</b><br/>' + _('iems2_iems2productimportservice_setting_searchable_default_help'),
                    name: 'searchable_product_default',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: _('iems2_iems2linksimportservice_setting_fieldset_links'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateful: true,
                stateId: 'msie-import-tab-setting-service-links-fieldset',
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('iems2_iems2linksimportservice_setting_remove_links'),
                    description: '<b>remove_links</b>' ,
                    name: 'remove_links',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('iems2_iems2linksimportservice_setting_remove_links_help'),
                    cls: 'desc-under'
                }]
            }]
        };
    }
};