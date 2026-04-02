Msie.service.IeYandexMarketExportService = {
    getTabSettings: function (config) {
        return {
            title: _('ieyandexmarket_ieyandexmarketexportservice_setting_tab'),
            id: 'ieyandexmarket-setting-tab-ieyandexmarketexportservice',
            layout: 'form',
            listeners: {
                activate: function () {
                    var grids = [
                        'ieyandexmarket-grid-param',
                        'ieyandexmarket-grid-delivery-options',
                        'ieyandexmarket-grid-pickup-options'
                    ];
                    Ext.each(grids, function (key) {
                        var grid = Ext.getCmp(key).grid;
                        if (grid) {
                            try {
                                grid.getView().refresh();
                            } catch (e) {
                                console.log(e);
                            }

                        }
                    });
                }
            },
            items: [{
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_shop'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-shop',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'msie-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_name'),
                    description: '<b>ym_store_name</b>',
                    name: 'ym_store_name',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_name_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_company'),
                    description: '<b>ym_store_company</b>',
                    name: 'ym_store_company',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_company_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_email'),
                    description: '<b>ym_store_email</b>',
                    name: 'ym_store_email',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_email_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-offer-type',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_offer_type'),
                    name: 'ym_offer_type',
                    description: '<b>ym_offer_type</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_offer_type_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_currency'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-currency',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-combo-default-currency',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_default_currency'),
                    description: '<b>ym_default_currency</b>',
                    name: 'ym_default_currency',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_default_currency_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-rate-currency',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_rate_currency'),
                    description: '<b>ym_rate_currency</b>',
                    name: 'ym_rate_currency',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_rate_currency_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-currencies',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_currencies'),
                    description: '<b>ym_currencies</b>',
                    name: 'ym_currencies',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_default_currency_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_param'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-param',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-grid-param',
                    id: 'ieyandexmarket-grid-param',
                    name: 'ym_param',
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_param_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_delivery'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-delivery',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-grid-delivery-options',
                    id: 'ieyandexmarket-grid-delivery-options',
                    name: 'ym_delivery_options',
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_delivery_options_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_delivery_field_options'),
                    name: 'ym_delivery_field_options',
                    description: '<b>ym_delivery_field_options</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_delivery_field_options_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_delivery_field'),
                    name: 'ym_delivery_field',
                    description: '<b>ym_delivery_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_delivery_field_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_delivery_default'),
                    name: 'ym_delivery_default',
                    description: '<b>ym_delivery_default</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_delivery_default_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_pickup'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-pickup',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-grid-pickup-options',
                    id: 'ieyandexmarket-grid-pickup-options',
                    name: 'ym_pickup_options',
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_pickup_options_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_pickup_field_options'),
                    name: 'ym_pickup_field_options',
                    description: '<b>ym_pickup_field_options</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_pickup_field_options_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_pickup_field'),
                    name: 'ym_pickup_field',
                    description: '<b>ym_pickup_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_pickup_field_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_pickup_default'),
                    name: 'ym_pickup_default',
                    description: '<b>ym_pickup_default</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_pickup_default_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_store'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-store',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_field'),
                    name: 'ym_store_field',
                    description: '<b>ym_store_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_field_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_default'),
                    name: 'ym_store_default',
                    description: '<b>ym_store_default</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_store_default_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_available'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-available',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_available_field'),
                    name: 'ym_available_field',
                    description: '<b>ym_available_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_available_field_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_available_default'),
                    name: 'ym_available_default',
                    description: '<b>ym_available_default</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_available_default_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_vendor'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-vendor',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_vendor_code_field'),
                    name: 'ym_vendor_code_field',
                    description: '<b>ym_vendor_code_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_vendor_code_field_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_model_field'),
                    name: 'ym_model_field',
                    description: '<b>ym_model_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_model_field_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_type_prefix_field'),
                    name: 'ym_type_prefix_field',
                    description: '<b>ym_type_prefix_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_type_prefix_field_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_sales_notes'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-sales-notes',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'msie-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_sales_notes_default'),
                    name: 'ym_sales_notes_default',
                    description: '<b>ym_sales_notes_default</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_sales_notes_default_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_sales_notes_field'),
                    name: 'ym_sales_notes_field',
                    description: '<b>ym_sales_notes_field</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_sales_notes_field_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_description'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-description',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'ieyandexmarket-combo-multi-select-product-field',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_description_fields'),
                    name: 'ym_description_fields',
                    description: '<b>ym_description_fields</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_description_fields_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'ieyandexmarket-combo-allowed-tags',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_description_allowed_tags'),
                    name: 'ym_desc_allowed_tags',
                    description: '<b>ym_desc_allowed_tags</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_description_allowed_tags_help'),
                    cls: 'desc-under'
                }]
            }, {
                xtype: 'ieyandexmarket-combo-product-field',
                fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_group_id_field'),
                name: 'ym_group_id_field',
                description: '<b>ym_group_id_field</b>',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('ieyandexmarket_ieyandexmarketexportservice_setting_group_id_field_help'),
                cls: 'desc-under'
            }, {
                xtype: 'ieyandexmarket-combo-product-field',
                fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_country_of_origin_field'),
                name: 'ym_country_of_origin_field',
                description: '<b>ym_country_of_origin_field</b>',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('ieyandexmarket_ieyandexmarketexportservice_setting_country_of_origin_field_help'),
                cls: 'desc-under'
            }, {
                xtype: 'fieldset',
                title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_other'),
                hideLabel: true,
                collapsible: true,
                autoHeight: true,
                stateId: 'ieyandexmarket-export-setting-fieldset-other',
                stateful: true,
                stateEvents: ['collapse', 'expand'],
                items: [{
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_multicategories'),
                    name: 'ym_multicategories',
                    description: '<b>ym_multicategories</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_multicategories_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'msie-combo-boolean',
                    fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_include_optionsprice2'),
                    name: 'ym_include_optionsprice2',
                    description: '<b>ym_include_optionsprice2</b>',
                    anchor: '100%'
                }, {
                    xtype: 'label',
                    html: _('ieyandexmarket_ieyandexmarketexportservice_setting_include_optionsprice2_help'),
                    cls: 'desc-under'
                }, {
                    xtype: 'fieldset',
                    title: _('ieyandexmarket_ieyandexmarketexportservice_setting_fieldset_saleprice'),
                    hideLabel: true,
                    collapsible: true,
                    autoHeight: true,
                    stateId: 'ieyandexmarket-export-setting-fieldset-saleprice',
                    stateful: true,
                    stateEvents: ['collapse', 'expand'],
                    items: [{
                        xtype: 'msie-combo-boolean',
                        fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_include_saleprice'),
                        name: 'ym_include_saleprice',
                        description: '<b>ym_include_saleprice</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('ieyandexmarket_ieyandexmarketexportservice_setting_include_saleprice_help'),
                        cls: 'desc-under'
                    }, {
                        xtype: 'msie-combo-boolean',
                        fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_only_saleprice_price'),
                        name: 'ym_only_saleprice_price',
                        description: '<b>ym_only_saleprice_price</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('ieyandexmarket_ieyandexmarketexportservice_setting_only_saleprice_price_help'),
                        cls: 'desc-under'
                    }, {
                        xtype: 'ieyandexmarket-combo-saleprice-prices',
                        fieldLabel: _('ieyandexmarket_ieyandexmarketexportservice_setting_exclude_saleprice_price'),
                        name: 'ym_exclude_saleprice_price',
                        description: '<b>ym_exclude_saleprice_price</b>',
                        anchor: '100%'
                    }, {
                        xtype: 'label',
                        html: _('ieyandexmarket_ieyandexmarketexportservice_setting_exclude_saleprice_price_help'),
                        cls: 'desc-under'
                    }
                    ]
                }]
            }]
        };
    }
};