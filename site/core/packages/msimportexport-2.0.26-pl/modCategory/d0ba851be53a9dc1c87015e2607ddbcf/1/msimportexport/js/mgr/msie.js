var Msie = function (config) {
    config = config || {};
    Msie.superclass.constructor.call(this, config);
};
Ext.extend(Msie, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},
    view: {},
    group: {},
    extra: {},
    msg: {},
    service: {},
    connector_url: '',
    utils: {
        formatDate: function (string) {
            if (string && string != '0000-00-00 00:00:00' && string != '-1-11-30 00:00:00' && string != 0) {
                var date = /^[0-9]+$/.test(string)
                    ? new Date(string * 1000)
                    : new Date(string.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));
                return date.strftime(Msie.config['date_format'] || '%d.%m.%y <span class="gray">%H:%M:%S</span>');
            } else {
                return '&nbsp;';
            }
        },
        formatTimeStamp: function (string) {
            if (string && string != 0) {
                string = parseFloat(string);
                if (Math.floor(string) === 0) return '00:00:00.' + String(Math.floor(string * 100000) / 100000).split('.')[1];
                var format = Msie.config['timestamp_format'] || 'DD.MM.YY HH:mm:ss.SSSS';
                return moment.unix(string).format(format);
            } else {
                return '&nbsp;';
            }
        },
        msToTime: function (s, precision) {
            s = String(s);
            if (s === '0') return '';
            precision = (precision && Number.isInteger(precision)) ? precision : 4;
            var splits = s.split('.'),
                ss = parseInt(splits[0]),
                hh = Math.floor(ss / 3600),
                mm = Math.floor((ss - (hh * 3600)) / 60),
                ss = ss - (hh * 3600) - (mm * 60),
                ms = splits[1] ? splits[1].slice(0, precision) : 0;

            hh = (hh < 10) ? "0" + hh : hh;
            mm = (mm < 10) ? "0" + mm : mm;
            ss = (ss < 10) ? "0" + ss : ss;

            for (var i = String(ms).length; i < precision; i++) {
                ms += '0';
            }
            return hh + ":" + mm + ":" + ss + "." + ms;
        },
        renderActions: function (value, props, row) {
            var res = [];
            var cls, icon, title, action, item = '';
            for (var i in row.data.actions) {
                if (!row.data.actions.hasOwnProperty(i)) {
                    continue;
                }
                var a = row.data.actions[i];
                if (!a['button']) {
                    continue;
                }

                icon = a['icon'] ? a['icon'] : '';
                if (typeof (a['cls']) == 'object') {
                    if (typeof (a['cls']['button']) != 'undefined') {
                        icon += ' ' + a['cls']['button'];
                    }
                } else {
                    cls = a['cls'] ? a['cls'] : '';
                }
                action = a['action'] ? a['action'] : '';
                title = a['title'] ? a['title'] : '';

                item = String.format(
                    '<li class="{0}"><button class="btn btn-default {1}" action="{2}" title="{3}"></button></li>',
                    cls, icon, action, title
                );

                res.push(item);
            }

            return String.format(
                '<ul class="msimportexport-row-actions">{0}</ul>',
                res.join('')
            );
        },
        getMenu: function (actions, grid, selected) {
            var menu = [];
            var cls, icon, title, action = '';

            var has_delete = false;
            for (var i in actions) {
                if (!actions.hasOwnProperty(i)) {
                    continue;
                }

                var a = actions[i];
                if (!a['menu']) {
                    if (a == '-') {
                        menu.push('-');
                    }
                    continue;
                } else if (menu.length > 0 && !has_delete && (/^remove/i.test(a['action']) || /^delete/i.test(a['action']))) {
                    menu.push('-');
                    has_delete = true;
                }

                if (selected.length > 1) {
                    if (!a['multiple']) {
                        continue;
                    } else if (typeof (a['multiple']) == 'string') {
                        a['title'] = a['multiple'];
                    }
                }

                icon = a['icon'] ? a['icon'] : '';
                if (typeof (a['cls']) == 'object') {
                    if (typeof (a['cls']['menu']) != 'undefined') {
                        icon += ' ' + a['cls']['menu'];
                    }
                } else {
                    cls = a['cls'] ? a['cls'] : '';
                }
                title = a['title'] ? a['title'] : a['title'];
                action = a['action'] ? grid[a['action']] : '';
                menu.push({
                    handler: action,
                    text: String.format(
                        '<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
                        cls, icon, title
                    ),
                    scope: grid
                });
            }

            return menu;
        },
        userLink: function (value, id, blank) {
            if (!value) {
                return '';
            } else if (!id) {
                return value;
            }

            return String.format(
                '<a href="?a=security/user/update&id={0}" class="ms2-link" target="{1}">{2}</a>',
                id,
                (blank ? '_blank' : '_self'),
                value
            );
        },
        resourceLink: function (value, id, blank) {
            if (!value) {
                return '';
            } else if (!id) {
                return value;
            }

            return String.format(
                '<a href="index.php?a=resource/update&id={0}" class="Msie-link" target="{1}">{2}</a>',
                id,
                (blank ? '_blank' : '_self'),
                value
            );
        },
        renderBoolean: function (value) {
            var color, text;
            if (value == 0 || value == false || value == undefined) {
                color = 'red';
                text = _('no');
            } else {
                color = 'green';
                text = _('yes');
            }

            return String.format('<span class="{0}">{1}</span>', color, text);
        },
        strTruncate: function (str, maxLength) {
            return str.replace(new RegExp("^(.{" + maxLength + "}).+", 'g'), '$1…');
        },
        stripTags: function (str) {
            var s = Ext.util.Format;
            return s.stripTags(str);
        },
        uniqid: function (length) {
            length = length || 32;
            return (Date.now().toString(36) + Math.random().toString(36).substr(2, length));
        },
        isEmpty: function (obj) {
            if (!obj || Object.keys(obj).length === 0) {
                return true;
            }
            return false;
        },
        goTo: function (to) {
            window.open('?a=mgr/' + to + '&namespace=msimportexport', '_blank');
        },
        goToTaskManager: function () {
            Msie.utils.goTo('task');
        },
        goToImport: function () {
            Msie.utils.goTo('import');
        },
        goToExport: function () {
            Msie.utils.goTo('export');
        },
        goToSysSettings: function () {
            Msie.utils.goTo('settings');
        },
    }

});
Ext.reg('Msie', Msie);

Ext.override(Ext.form.BasicForm, {
    clearDirty: function (nodeToRecurse) {
        nodeToRecurse = nodeToRecurse || this;
        if (!nodeToRecurse.items.each) return;
        nodeToRecurse.items.each(function (f) {
            if (!f.getValue) return;

            if (f.items) {
                this.clearDirty(f);
            } else if (f.originalValue != f.getValue()) {
                f.originalValue = f.getValue();
            }
        }, this);
    }
});

Ext.override(Ext.form.ComboBox, {
    getSelectedRecord: function (value) {
        value = value || this.getValue();
        var record = this.findRecord(this.valueField || this.displayField, value);
        return record ? record : null;
    },
    getSelectedIndex: function () {
        var record = this.getSelectedRecord();
        return (this.store.indexOf(record));
    },
    setTriggerDisable: function (disabled, trigger) {
        if (!this.trigger) return;
        var btn = this.trigger.select('.x-form-trigger.x-field-combo-btn-' + trigger, trigger).item(0);
        if (!btn) return;
        if (disabled) {
            btn.addClass('trigger-disable');
        } else {
            btn.removeClass('trigger-disable');
        }
    },
    isTriggerDisable: function (trigger) {
        var btn = this.trigger.select('.x-form-trigger.x-field-combo-btn-' + trigger, trigger).item(0);
        if (!btn) return true;
        return btn.hasClass('trigger-disable');
    }
});

Ext.override(Ext.form.FieldSet, {
    getState: function () {
        return {collapsed: this.collapsed};
    }
});

Ext.override(MODx.Msg, {
    getStatusMarkup: function (opt) {
        var width = opt.width ? 'width="' + opt.width + '"' : '',
            mk = '<div class="modx-status-msg msimportexport-status-msg' + (opt.cls || '') + '" ' + width + ' >';
        if (opt.title) {
            mk += '<h3>' + opt.title + '</h3>';
        }
        if (opt.message) {
            mk += '<span class="modx-smsg-message">' + opt.message + '</span>';
        }
        return mk + '</div>';
    }
});

Msie = new Msie();

Msie.msg.alert = function (title, text) {
    var w = Ext.getCmp('msie-window-alert');
    if (w) {
        w.close();
    }
    w = MODx.load({
        xtype: 'msie-window-alert',
        id: 'msie-window-alert',
        title: title,
        text: text
    });
    w.show();
}