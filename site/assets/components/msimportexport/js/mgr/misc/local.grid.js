Msie.grid.Local = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        hideLabel: true,
        btnAddRowText: _('msimportexport_btn_add_row'),
        btnAddRowIcon: '<i class="icon icon-plus"></i>',
    });
    Msie.grid.Local.superclass.constructor.call(this, config);
};
Ext.extend(Msie.grid.Local, Ext.form.Field, {
    defaultAutoCreate: {tag: 'input', type: 'hidden'},
    initComponent: function () {
        var gridConfig = Ext.apply({}, {
            cls: 'msie-grid',
            paging: true,
            pageSize: 20,
            autoHeight: true,
            stateful: true,
            stateId: this.id,
            protectRender: true,
            singleSelect: false,
            stateEvents: ['columnresize', 'columnmove', 'columnhide', 'columnshow'],
            fields: this.getFields(this),
            columns: this.getColumns(this),
            propRecord: Ext.data.Record.create(this.getFields(this)),
            tbar: this.getTopBar(this),
            listeners: this.getListeners(this),
            actions: this.getActions(this),
        }, this.gridConfig);

        gridConfig.sm = new Ext.grid.RowSelectionModel({singleSelect: gridConfig.singleSelect});

        if (typeof (this['desc_trans']) != 'undefined' && this['desc_trans'] == true) {
            this.exp = new Ext.grid.RowExpander({
                tpl: new Ext.Template(
                    '<p class="modx-property-description"><i>{desc_trans}</i></p>'
                )
            });
            gridConfig.plugins = [this.exp];
        }
        this.grid = new MODx.grid.LocalGrid(gridConfig);
        this.grid.on('rowcontextmenu', this._showMenu, this);
        Ext.form.Field.superclass.initComponent.call(this);
    },
    onRender: function (ct, position) {
        if (this.isRendered) {
            return;
        }
        Ext.form.Field.superclass.onRender.call(this, ct, position);
        this.grid.render(this.el);
        this.isRendered = true;
    },
    getFields: function (config) {
        return ['id'];
    },
    getColumns: function (config) {
        return [{
            header: 'id',
            dataIndex: 'id',
            sortable: true,
            editor: {
                xtype: 'textfield'
            },
        }];

        /*   return [this.exp,{
               header: 'id',
               dataIndex: 'id',
               sortable: true,
               editor: {
                   xtype: 'textfield'
               },
           }];*/
    },
    getActions: function (config) {
        if (Msie.utils.isEmpty(config.actions)) {
            return [{
                cls: {menu: 'red', button: 'red'},
                icon: 'icon icon-trash-o',
                title: _('msimportexport_menu_remove_row'),
                multiple: _('msimportexport_menu_multiple_remove_row'),
                action: 'removeRow',
                button: true,
                menu: true,
            }];
        } else {
            return config.actions;
        }
    },
    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: this.btnAddRowIcon + ' ' + this.btnAddRowText,
            handler: this.addRow,
            cls: 'primary-button',
            scope: this
        });
        return tbar;
    },
    getListeners: function (config) {
        return {};
    },
    setValue: function (value) {
        this.grid.getStore().removeAll();
        if (value) {
            var data = Ext.decode(value);
            Ext.each(data || [], function (record) {
                this.grid.getStore().add(new this.grid.propRecord(record));
            }, this);
        }
        if (this.isRendered) {
            this.el.dom.value = value;
        }
        return this;
    },
    getValue: function () {
        var s = this.grid.getStore();
        var ct = s.getCount();
        var rs = this.grid.config.encodeByPk ? {} : [];
        var r;
        for (var j = 0; j < ct; j++) {
            r = s.getAt(j).data;
            if (!Msie.utils.isEmpty(r)) {
                if (this.grid.config.encodeAssoc) {
                    rs[r[this.grid.config.encodeByPk || 'id']] = r;
                } else {
                    rs.push(r);
                }
            }
        }
        return Ext.encode(rs);
    },
    addRow: function (btn, e, row) {
        var row = new this.grid.propRecord();
        // row.markDirty();
        this.grid.getStore().add(row);


    },
    removeRow: function () {
        var items = this.grid.getSelectionModel().getSelections();
        Ext.MessageBox.confirm(
            _('warning'),
            items.length > 1
                ? _('msimportexport_confirm_multiple_remove_row')
                : _('msimportexport_confirm_remove_row'),
            function (val) {
                if (val == 'yes') {
                    Ext.each(items || [], function (item) {
                        if (this.grid.fireEvent('beforeRemoveRow', item)) {
                            this.grid.getStore().remove(item);
                            this.grid.fireEvent('afterRemoveRow', item);
                        }
                    }, this);
                }
            }, this
        );
    },
    _showMenu: function (grid, ri, e) {
        e.stopEvent();
        e.preventDefault();
        var menu = grid.menu,
            length = grid.getSelectionModel().getSelections().length;
        menu.recordIndex = ri;
        menu.record = grid.getStore().getAt(ri).data;
        if (!grid.getSelectionModel().isSelected(ri)) {
            grid.getSelectionModel().selectRow(ri);
        }
        menu.removeAll();
        Ext.each(this.grid.actions, function (item) {
            if (length > 1) {
                if (!item['multiple']) {
                    return true;
                } else if (typeof (item['multiple']) == 'string') {
                    item['title'] = item['multiple'];
                }
            }
            item['icon'] = item['icon'] ? item['icon'] : '';
            if (typeof (item['cls']) == 'object') {
                if (typeof (item['cls']['menu']) != 'undefined') {
                    item['icon'] += ' ' + item['cls']['menu'];
                    item['cls'] = '';
                }
            } else {
                item['cls'] = item['cls'] ? item['cls'] : '';
            }
            item['title'] = item['title'] ? item['title'] : '';
            item['action'] = item['action'] ? item['action'] : '';
            menu.add({
                id: Ext.id(),
                handler: this[item.action],
                text: String.format(
                    '<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
                    item.cls, item.icon, item.title
                ),
                scope: this
            });
        }, this);
        menu.showAt(e.xy);
    }
});
Ext.reg('msie-grid-local', Msie.grid.Local);