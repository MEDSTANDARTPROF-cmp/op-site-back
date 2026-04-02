Msie.service.ieGalleryImportService = {
    getTabSettings: function (config) {
        return {
            title: _('iegallery_iegalleryimportservice_setting_tab'),
            id: 'iegallery-setting-tab-iegalleryimportservice',
            layout: 'form',
            items: [{
                xtype: 'msie-field',
                fieldLabel: _('iegallery_iegalleryimportservice_setting_base_path_images'),
                description: '<b>gallery_base_path_images</b>',
                name: 'gallery_base_path_images',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iegallery_iegalleryimportservice_setting_base_path_images_help'),
                cls: 'desc-under'
            },{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iegallery_iegalleryimportservice_setting_resize_upload_image'),
                description: '<b>gallery_resize_upload_image</b>',
                name: 'gallery_resize_upload_image',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iegallery_iegalleryimportservice_setting_resize_upload_image_help'),
                cls: 'desc-under'
            },{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iegallery_iegalleryimportservice_setting_remove_images'),
                description: '<b>gallery_remove_images</b>',
                name: 'gallery_remove_images',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iegallery_iegalleryimportservice_setting_remove_images_help'),
                cls: 'desc-under'
            },{
                xtype: 'msie-combo-boolean',
                fieldLabel: _('iegallery_iegalleryimportservice_setting_force_update'),
                description: '<b>gallery_force_update</b>',
                name: 'gallery_force_update',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iegallery_iegalleryimportservice_setting_force_update_help'),
                cls: 'desc-under'
            },{
                xtype: 'iegallery-combo-gallery',
                fieldLabel: _('iegallery_iegalleryimportservice_setting_gallery_type'),
                description: '<b>gallery_type</b>',
                name: 'gallery_type',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iegallery_iegalleryimportservice_setting_gallery_type_help'),
                cls: 'desc-under'
            }, {
                xtype: 'msie-field',
                fieldLabel: _('iegallery_iegalleryimportservice_setting_image_delimiter'),
                description: '<b>gallery_image_delimiter</b>',
                name: 'gallery_image_delimiter',
                anchor: '100%'
            }, {
                xtype: 'label',
                html: _('iegallery_iegalleryimportservice_setting_image_delimiter_help'),
                cls: 'desc-under'
            }]
        };
    }
};