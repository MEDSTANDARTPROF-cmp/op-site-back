(function ($, QuickViewConfig) {
    "use strict";

    var indexOf = [].indexOf || function (item) {
        for (var i = 0, l = this.length; i < l; i++) {
            if (i in this && this[i] === item) return i;
        }
        return -1;
    };
    var hasProp = {}.hasOwnProperty;
    var slice = [].slice;

    var bind = function (fn, me) {
        return function () {
            return fn.apply(me, arguments);
        };
    };
    var extend = function (child, parent) {
        for (var key in parent) {
            if (hasProp.call(parent, key)) child[key] = parent[key];
        }

        function ctor() {
            this.constructor = child;
        }

        ctor.prototype = parent.prototype;
        child.prototype = new ctor();
        child.__super__ = parent.prototype;
        return child;
    };

    var runAction = function (action, bind) {
        if (typeof action == 'function') {
            return action.apply(bind, Array.prototype.slice.call(arguments, 2));
        }
        else if (typeof action == 'object') {
            for (var i in action) {
                if (action.hasOwnProperty(i)) {
                    var response = action[i].apply(bind, Array.prototype.slice.call(arguments, 2));
                    if (response === false) {
                        return false;
                    }
                }
            }
        }
        return true;
    };

    var camelize = function (str) {
        return str.replace(/(-|\.)(\w)/g, function (match, symbol) {
            return symbol.toUpperCase();
        });
    };
    var uncamelize = function (str) {
        return str.replace(/[A-Z]/g, function (symbol, index) {
            return (index == 0 ? '' : '-') + symbol.toLowerCase();
        });
    };

    var addClass = function (element, classNames) {
        element.className += ' ' + classNames;
    };

    var removeClass = function (element, classNames) {
        var original = element.className.split(' ');
        var toBeRemoved = classNames.split(' ');
        for (var x = 0; x < toBeRemoved.length; x += 1) {
            var index = original.indexOf(toBeRemoved[x]);
            if (index > -1) {
                original.splice(index, 1);
            }
        }
        element.className = original.join(' ');
    };

    var setOptions = function ($node, ns, options) {
        var prefix, quickview;

        if (typeof ns == 'undefined') {
            ns = $.fn.quickview.defaults.ns;
        }

        prefix = camelize(ns);
        quickview = $node.data('QuickView');

        $.each(options, function (index, value) {
            $node.data(prefix + '-' + index, value);
        });

        if (quickview) {
            switch (prefix) {
                case 'data':
                    quickview.data = $.extend(true, quickview.data, options);
                    break;
                case 'options':
                    quickview.options = $.extend(true, quickview.options, options);
                    break;
                case 'hash':
                    quickview.hash = $.extend(true, quickview.hash, options);
                    break;
            }
        }

        return options;
    };

    var getOptions = function ($node, ns) {
        var prefix, options;

        if (typeof ns == 'undefined') {
            ns = $.fn.quickview.defaults.ns;
        }

        prefix = camelize(ns);
        options = $node.data(prefix) || {};

        $.each($node.data(), function (index, value) {
            if (index.indexOf(prefix) === 0) {
                var key = uncamelize(index.replace(prefix, ''));
                if (key.length > 0) {
                    options[key] = value;
                }
            }
        });

        return options;
    };

    var newGuid = function () {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    };

    /*
     * from https://github.com/ruidfigueiredo/findHandlersJS/blob/master/findEventHandlers.js
     */
    var findEventHandlers = function (eventType, jqSelector) {
        var results = [];
        var $ = jQuery;// to avoid conflict between others frameworks like Mootools

        var arrayIntersection = function (array1, array2) {
            return $(array1).filter(function (index, element) {
                return $.inArray(element, $(array2)) !== -1;
            });
        };

        var haveCommonElements = function (array1, array2) {
            return arrayIntersection(array1, array2).length !== 0;
        };


        var addEventHandlerInfo = function (element, event, $elementsCovered) {
            var extendedEvent = event;
            if ($elementsCovered !== void 0 && $elementsCovered !== null) {
                $.extend(extendedEvent, {targets: $elementsCovered.toArray()});
            }
            var eventInfo;
            var eventsInfo = $.grep(results, function (evInfo, index) {
                return element === evInfo.element;
            });

            if (eventsInfo.length === 0) {
                eventInfo = {
                    element: element,
                    events: [extendedEvent]
                };
                results.push(eventInfo);
            } else {
                eventInfo = eventsInfo[0];
                eventInfo.events.push(extendedEvent);
            }
        };


        var $elementsToWatch = $(jqSelector);
        if (jqSelector === "*")//* does not include document and we might be interested in handlers registered there
            $elementsToWatch = $elementsToWatch.add(document);
        var $allElements = $("*").add(document);

        $.each($allElements, function (elementIndex, element) {
            var allElementEvents = $._data(element, "events");
            if (allElementEvents !== void 0 && allElementEvents[eventType] !== void 0) {
                var eventContainer = allElementEvents[eventType];
                $.each(eventContainer, function (eventIndex, event) {
                    var isDelegateEvent = event.selector !== void 0 && event.selector !== null;
                    var $elementsCovered;
                    if (isDelegateEvent) {
                        $elementsCovered = $(event.selector, element); //only look at children of the element, since those are the only ones the handler covers
                    } else {
                        $elementsCovered = $(element); //just itself
                    }
                    if (haveCommonElements($elementsCovered, $elementsToWatch)) {
                        addEventHandlerInfo(element, event, $elementsCovered);
                    }
                });
            }
        });

        return results;
    };

    var findEvent = function (eventType, jqSelector) {
        var results = findEventHandlers(eventType, jqSelector);
        return results.length > 0 ? true : false;
    };

    var Hash = {
        get: function () {
            var vars = {}, hash, splitter, hashes;
            if (!this.oldbrowser()) {
                var pos = window.location.href.indexOf('?');
                hashes = (pos != -1) ? decodeURIComponent(window.location.href.substr(pos + 1)) : '';
                splitter = '&';
            }
            else {
                hashes = decodeURIComponent(window.location.hash.substr(1));
                splitter = '/';
            }

            if (hashes.length == 0) {
                return vars;
            }
            else {
                hashes = hashes.split(splitter);
            }

            for (var i in hashes) {
                if (hashes.hasOwnProperty(i)) {
                    hash = hashes[i].split('=');
                    if (typeof hash[1] == 'undefined') {
                        vars['anchor'] = hash[0];
                    }
                    else {
                        vars[hash[0]] = hash[1];
                    }
                }
            }
            return vars;
        },
        set: function (vars) {
            var hash = '';
            for (var i in vars) {
                if (vars.hasOwnProperty(i)) {
                    hash += '&' + i + '=' + vars[i];
                }
            }
            if (!this.oldbrowser()) {
                if (hash.length != 0) {
                    hash = '?' + hash.substr(1);
                }
                window.history.pushState({QuickView: document.location.pathname + hash}, '', document.location.pathname + hash);
            }
            else {
                window.location.hash = hash.substr(1);
            }
        },
        add: function (key, val) {
            var hash = this.get();
            if (!hash[key] || hash[key] != val) {
                hash[key] = val;
                this.set(hash);
            }
        },
        remove: function (key) {
            var hash = this.get();
            delete hash[key];
            this.set(hash);
        },
        clear: function () {
            this.set({});
        },
        oldbrowser: function () {
            return !(window.history && history.pushState);
        }
    };

    var findViewByHash = function (hash) {
        if (typeof hash == 'undefined') {
            hash = Hash.get();
        }
        if (hash['QuickView']) {
            try {
                var selector = $.fn.quickview.selectors.main;
                hash = hash['QuickView'].split('+');
                hash.map(function (key, i) {
                    hash[i] = key.split('|');
                    selector += "[data-data-{key}='{value}']"
                        .replace('{key}', hash[i][0])
                        .replace('{value}', hash[i][1]);
                }, this);
                return $(selector);
            } catch (e) {
                console.log(e);
            }
        }

        return null;
    };


    var Module, moduleKeywords, QuickView, ViewSelector, ViewModal, ViewAlertify;
    moduleKeywords = ['extended', 'included'];

    Module = (function () {
        function Module() {
        }

        Module.extend = function (obj) {
            var key, ref, value;
            for (key in obj) {
                value = obj[key];
                if (indexOf.call(moduleKeywords, key) < 0) {
                    this[key] = value;
                }
            }
            if ((ref = obj.extended) != null) {
                ref.apply(this);
            }
            return this;
        };

        Module.include = function (obj) {
            var key, ref, value;
            for (key in obj) {
                value = obj[key];
                if (indexOf.call(moduleKeywords, key) < 0) {
                    this.prototype[key] = value;
                }
            }
            if ((ref = obj.included) != null) {
                ref.apply(this);
            }
            return this;
        };

        return Module;

    })();


    QuickView = function (element, options) {

        this.defaults = $.fn.quickview.defaults;
        this.selectors = $.fn.quickview.selectors;

        this.$body = $('body');

        if (element) {
            this.$element = $(element);
            this.$parent = this.$element.closest(this.selectors.parent);
            if (this.$parent.length < 1) {
                this.$parent = $('body');
            }

            this.options = $.extend(true, {},
                getOptions(this.$parent),
                options
            );

            this.data = $.extend(true, {id: 0, ctx: QuickViewConfig.ctx, action: 'chunk'},
                getOptions(this.$parent, 'data'),
                getOptions(this.$element, 'data')
            );

            this.hash = $.extend(true, {},
                getOptions(this.$parent, 'hash'),
                getOptions(this.$element, 'hash')
            );

            if (!this.data.action.match('/')) {
                this.data.action = 'content/' + this.data.action
            }
        }

    };

    QuickView.prototype = {
        constructor: QuickView,

        init: function () {
            this.processSuccess = bind(this.processSuccess, this);
            this.processFailure = bind(this.processFailure, this);
            this.showOutput = bind(this.showOutput, this);
            this.hideOutput = bind(this.hideOutput, this);
            this.addMethodAction = bind(this.addMethodAction, this);
            this.removeMethodAction = bind(this.removeMethodAction, this);
        },

        getNamespace: function () {
            var args, option;
            option = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [];
            return this.defaults.ns + ' ' + this.defaults.ns + (option || '') + ' ' + args.join(' ');
        },

        getHtml: function () {

        },

        getContent: function () {
            if (this.options && this.options.hasOwnProperty('html')) {
                this.processSuccess({'success': true, 'data': {'output': this.options.html}});
            }
            else {
                this.showSpinner();
                $.ajax({
                    url: QuickViewConfig.actionUrl,
                    type: 'post',
                    dataType: 'json',
                    cache: false,
                    data: this.data,
                }).done((function (_this) {
                    return function (r) {
                        _this.hideSpinner();
                        _this.processSuccess(r);
                    };
                })(this)).fail((function (_this) {
                    return function (r) {
                        _this.hideSpinner();
                        _this.processFailure(r);
                    };
                })(this));
            }
        },

        getNeighbor: function (direction) {
            var items, index, loop, work, neighbors, group;

            switch (true) {
                case this.$element.is(this.selectors.actionClick):
                    items = $(this.$parent).find(this.selectors.main + this.selectors.actionClick);
                    break;
                case this.$element.is(this.selectors.actionMouseOver):
                    items = $(this.$parent).find(this.selectors.main + this.selectors.actionMouseOver);
                    break;
            }

            group = this.options.group || '';
            if (group && items) {
                items = items.filter('[data-quickview-group="' + group + '"]');
            }

            index = items.index(this.$element);
            loop = this.options.loop;

            switch (true) {
                case items.length <= 1:
                    return this.$element;
                case !loop && index == 0 && direction == 'prev':
                    return false;
                case !loop && index == (items.length - 1) && direction == 'next':
                    return false;
            }

            neighbors = {
                item: {},
                index: {}
            };
            work = {};

            if (index !== (items.length - 1)) {
                neighbors.item.next = items[index + 1];
                neighbors.index.next = index + 1;
            } else {
                neighbors.item.next = items[0];
                neighbors.index.next = 0;
            }

            if (index == 0) {
                neighbors.item.prev = items[items.length - 1];
                neighbors.index.prev = items.length - 1;
            } else {
                neighbors.item.prev = items[index - 1];
                neighbors.index.prev = index - 1;
            }

            switch (direction) {
                case 'prev':
                    work.item = neighbors.item.prev;
                    work.index = neighbors.index.prev;
                    break;
                case 'next':
                    work.item = neighbors.item.next;
                    work.index = neighbors.index.next;
                    break;
            }

            if (!work.item) {
                return false;
            }

            return work.item;
        },

        createSpinner: function () {
            return $('<div class="' + this.getNamespace('-spinner') + '"></div>');
        },

        showSpinner: function () {
            if (!this.defaults.spinner || !this.options.spinner) {
                return;
            }
            this.$spinner = this.$spinner || this.createSpinner();
            this.$body.append(this.$spinner);

            return this.$spinner.show();
        },

        hideSpinner: function () {
            if (this.$spinner) {
                return this.$spinner.remove();
            }
        },

        processSuccess: function (r) {
            var e, message;

            e = $.Event($.fn.quickview.CONTENT_SUCCESS);
            this.$element.trigger(e, [r, this]);
            if (e.isDefaultPrevented()) {
                return;
            }

            if (!r.success) {
                message = r.message;
                if (!message) {
                    message = 'error unknown';
                }

                if (!r.data.output) {
                    r.data.output = message;
                }
            }
            // TODO add history

            return this.showOutput(r);
        },

        processFailure: function (r) {
            var e, message;

            e = $.Event($.fn.quickview.CONTENT_FAILURE);
            this.$element.trigger(e, [r, this]);
            if (e.isDefaultPrevented()) {
                return;
            }

            message = r.responseJSON;
            if (!message) {
                message = 'error unknown';
            }

            return this.hideOutput(r);
        },

        showOutput: function (r) {
            return setTimeout((function () {
                var e = $.Event($.fn.quickview.CONTENT_SHOW);

                if (this.$output && this.$output.$modal) {
                    this.$output.$modal.trigger(e, [r, this]);
                }
                else if (this.$output) {
                    this.$output.trigger(e, [r, this]);
                }

                this.setViewHash();
                this.setParamHash();
                runAction($.fn.quickview.methodActions['show'], this);

                return this.$output;
            }.bind(this)), $.fn.quickview.defaults.timeout);
        },

        hideOutput: function (r) {
            return setTimeout((function () {
                var e = $.Event($.fn.quickview.CONTENT_HIDE);

                if (this.$output && this.$output.$modal) {
                    this.$output.$modal.trigger(e, [r, this]);
                }
                else if (this.$output) {
                    this.$output.trigger(e, [r, this]);
                }

                this.removeViewHash();
                this.removeParamHash();
                runAction($.fn.quickview.methodActions['hide'], this);

                return this.$output;
            }.bind(this)), $.fn.quickview.defaults.timeout);
        },

        setViewHash: function () {
            if (this.defaults.viewhash && this.options.viewhash) {
                var hash = [];
                this.defaults.viewhash.map(function (key) {
                    if (typeof this.data[key] != 'undefined') {
                        hash.push(key + '|' + this.data[key]);
                    }
                }, this);

                if (hash.length > 0) {
                    Hash.add('QuickView', hash.join('+'));
                }
            }
        },

        removeViewHash: function () {
            if (this.defaults.viewhash && this.options.viewhash) {
                Hash.remove('QuickView');
            }
        },

        setParamHash: function () {
            if (this.options.sethash) {
                var new_url = null, old_url = window.location.href;
                var tag = this.$element.get(0).tagName.toLowerCase();

                if (tag === 'form') {
                    var action = this.$element.get(0).getAttribute('action');
                    var method = this.$element.get(0).getAttribute('method');
                }
                else if (tag === 'a') {
                    var action = this.$element.get(0).getAttribute('href');
                }
                else {
                    var action = this.$element.get(0).getAttribute('action');
                }

                switch (true) {
                    case !action:
                        new_url = window.location.href;
                        break;
                    case (/^http/i.test(action)):
                        new_url = action;
                        break;
                    default:
                        new_url = window.location.origin + action;
                        break;
                }

                if (new_url && old_url != new_url && new RegExp(window.location.origin).test(new_url)) {
                    old_url = old_url.replace(window.location.origin, '');
                    new_url = new_url.replace(window.location.origin, '');

                    this.$element.data('quickview-old-url', old_url);
                    this.$element.data('quickview-new-url', new_url);

                    try {
                        history.pushState({}, '', new_url);
                    } catch (e) {
                    }
                }
            }

            //QuickView
            if (this.options.sethash) {
                $.each(this.hash || {}, function (key, value) {
                    Hash.add(key, value);
                });
            }
        },

        removeParamHash: function () {
            if (this.options.sethash) {
                var old_url = this.$element.data('quickview-old-url');
                var new_url = this.$element.data('quickview-new-url');
                var tag = this.$element.get(0).tagName.toLowerCase();

                //if (tag == 'form') {

                if (old_url && old_url != new_url) {
                    try {
                        history.pushState({}, '', old_url);
                    } catch (e) {
                    }
                }
                //}

            }

            if (this.options.sethash) {
                $.each(this.hash || {}, function (key, value) {
                    Hash.remove(key);
                });
            }
        },

        addMethodAction: function (path, name, func) {
            if (typeof func != 'function') {
                return false;
            }
            if (!$.fn.quickview.methodActions[path]) {
                $.fn.quickview.methodActions[path] = {};
            }
            $.fn.quickview.methodActions[path][name] = func;

            return true;
        },

        removeMethodAction: function (path, name) {
            if (!$.fn.quickview.methodActions[path]) {
                $.fn.quickview.methodActions[path] = {};
            }

            delete $.fn.quickview.methodActions[path][name];

            return true;
        },

    };


    ViewSelector = (function (superClass) {
        extend(ViewSelector, QuickView, superClass);

        function ViewSelector(element, options) {
            QuickView.prototype.constructor.call(this, element, options);
        };

        ViewSelector.prototype.init = function () {
            QuickView.prototype.init.call(this);

            this.OutputOptions = $.extend(true, {},
                getOptions(this.$parent, 'output'),
                getOptions(this.$element, 'output')
            );

            this.OutputButtons = $.extend(true, {},
                $.fn.quickview.defaultButtons,
                getOptions(this.$parent, 'button'),
                getOptions(this.$element, 'button')
            );

            this.$output = this.$output || $(this.options.output || this.selectors.output);
            this.$output.setButtons = bind(this.setButtons, this.$output);
        };

        ViewSelector.prototype.createButtonIcon = function (icon) {
            var $icon = $('<span></span>');
            $icon.addClass(this.getNamespace('button-icon')).addClass(icon);

            return $icon;
        };

        ViewSelector.prototype.setButtons = function (buttons) {
            this.append(buttons);

            return this;
        };

        ViewSelector.prototype.getButtons = function () {
            var buttons, button;
            buttons = [];

            if (!this.defaults.buttons) {
                return buttons;
            }

            if (this.OutputOptions.buttons) {
                jQuery.each(this.OutputOptions.buttons, (function (_this) {
                    return function (index, action) {
                        if (typeof action === 'string') {
                            button = _this.OutputButtons[action] || {};
                        }
                        else {
                            button = action || {};
                            action = button['action'] || '';
                        }

                        button = $.extend({}, {
                            label: _this.defaults.msg[action] || action,
                            cssClass: _this.getNamespace('-' + _this.defaults.cls[action], _this.defaults.ns + '-action', (button['cls'] || '')),
                            action: action,
                            QuickView: _this
                        }, button);

                        buttons.push(button);
                    };
                })(this));
            }

            return buttons;
        };

        ViewSelector.prototype.createButtons = function () {
            var buttons, $buttons;

            buttons = this.getButtons();
            if (buttons.length > 0) {
                $buttons = $('<div></div>');
                $buttons.addClass(this.getNamespace('-buttons'));

                jQuery.each(buttons, (function (_this) {
                    return function (index, button) {
                        if (!button.id) {
                            button.id = newGuid();
                        }
                        var $button = _this.createButton(button);
                        $buttons.append($button);
                    };
                })(this));
            }
            return $buttons;
        };

        ViewSelector.prototype.createButton = function (button) {
            var $button = $('<button class="btn"></button>');
            $button.prop('id', button.id);
            $button.data('button', button);

            // Icon
            if (typeof button.icon !== 'undefined' && $.trim(button.icon) !== '') {
                $button.append(this.createButtonIcon(button.icon));
            }

            // Label
            if (typeof button.label !== 'undefined') {
                $button.append(button.label);
            }

            // title
            if (typeof button.title !== 'undefined') {
                $button.attr('title', button.title);
            }

            // Css class
            if (typeof button.cssClass !== 'undefined' && $.trim(button.cssClass) !== '') {
                $button.addClass(button.cssClass);
            } else {
                $button.addClass('btn-default');
            }

            // Data attributes
            if (typeof button.data === 'object' && button.data.constructor === {}.constructor) {
                $.each(button.data, function (key, value) {
                    $button.attr('data-' + key, value);
                });
            }

            // Button on click
            $button.on('click', {QuickView: this, $button: $button, button: button}, function (event) {
                var QuickView = event.data.QuickView;
                var $button = event.data.$button;
                var button = $button.data('button');
                if (button.autospin) {
                    $button.toggleSpin(true);
                }
                if (typeof button.action === 'function') {
                    return button.action.call($button, QuickView, event);
                }
            });

            //Initialize enabled or not
            if (typeof button.enabled !== 'undefined') {
                $button.toggleEnable(button.enabled);
            }

            return $button;
        };

        ViewSelector.prototype.showOutput = function (r) {
            if (this.$output) {
                this.$output.html(r.data.output);
                this.$output.setButtons(this.createButtons());
                this.$output.show();
                this.$output.focus();

                // add cls "quickview-main", add "QuickView"
                this.$output.addClass($.fn.quickview.defaults.ns + '-main');
                this.$output.data('QuickView', this);
            }

            return QuickView.prototype.showOutput.call(this, r);
        };

        ViewSelector.prototype.hideOutput = function (r) {
            if (this.$output) {
                this.$output.html('');
                this.$output.hide();
            }

            return QuickView.prototype.hideOutput.call(this, r);
        };

        return ViewSelector;

    })(Module);


    ViewModal = (function (superClass) {
        extend(ViewModal, QuickView, superClass);

        function ViewModal(element, options) {
            QuickView.prototype.constructor.call(this, element, options);
        };

        ViewModal.prototype.init = function () {
            QuickView.prototype.init.call(this);

            this.BDialogOptions = $.extend(true, {},
                $.fn.quickview.defaultBDialogOptions,
                getOptions(this.$parent, 'dialog'),
                getOptions(this.$element, 'dialog')
            );

            this.BDialogButtons = $.extend(true, {},
                $.fn.quickview.defaultButtons,
                getOptions(this.$parent, 'button'),
                getOptions(this.$element, 'button')
            );

            if (typeof BootstrapDialog != 'function') {
                throw new Error("BootstrapDialog required");
            }

            this.$output = this.$parent.data('BDialog');
            if (!this.$output) {
                var _this = this;

                BootstrapDialog.prototype = new BootstrapDialog();
                BootstrapDialog.prototype.close = function () {
                    if (this.isQuickView) {
                        _this.hideOutput();
                    }
                    else {
                        !this.isRealized() && this.realize();
                        this.getModal().modal('hide');
                    }
                    return this;
                };

                this.$parent.data('BDialog', (this.$output = new BootstrapDialog(this.BDialogOptions)));
            }

            this.$output.isQuickView = true;
        };


        ViewModal.prototype.showOutput = function (r) {
            this.$output.initOptions(this.BDialogOptions);

            // update footer
            if (this.$output.getModalFooter()) {
                this.$output.getModalFooter().html(this.$output.createFooterContent());
            }

            this.$output.setButtons(this.getButtons());
            this.$output.updateType();
            this.$output.updateTitle();
            this.$output.updateClosable();
            this.$output.updateAnimate();
            this.$output.updateSize();
            this.$output.setMessage(r.data.output);

            !this.$output.isRealized() && this.$output.realize();
            this.$output.getModal().modal('show');
            this.$output.getModal().focus();

            // add cls "quickview-main", add "QuickView"
            this.$output.getModalBody().addClass($.fn.quickview.defaults.ns + '-main');
            this.$output.getModalBody().data('QuickView', this);

            return QuickView.prototype.showOutput.call(this, r);
        };

        ViewModal.prototype.hideOutput = function (r) {
            !this.$output.isRealized() && this.$output.realize();
            this.$output.getModal().modal('hide');

            return QuickView.prototype.hideOutput.call(this, r);
        };

        ViewModal.prototype.getButtons = function () {
            var buttons, button;
            buttons = [];

            if (!this.defaults.buttons) {
                return buttons;
            }

            if (this.BDialogOptions.buttons) {
                jQuery.each(this.BDialogOptions.buttons, (function (_this) {
                    return function (index, action) {
                        if (typeof action === 'string') {
                            button = _this.BDialogButtons[action] || {};
                        }
                        else {
                            button = action || {};
                            action = button['action'] || '';
                        }

                        button = $.extend({}, {
                            label: _this.defaults.msg[action] || action,
                            cssClass: _this.getNamespace('-' + _this.defaults.cls[action], _this.defaults.ns + '-action', (button['cls'] || '')),
                            action: action,
                            QuickView: _this
                        }, button);

                        buttons.push(button);
                    };
                })(this));
            }

            return buttons;
        };

        return ViewModal;

    })(Module);


    ViewAlertify = (function (superClass) {
        extend(ViewAlertify, QuickView, superClass);

        function ViewAlertify(element, options) {
            QuickView.prototype.constructor.call(this, element, options);
        };

        ViewAlertify.prototype.init = function () {
            QuickView.prototype.init.call(this);

            if (typeof alertify == 'undefined') {
                throw new Error("alertify required");
            }

            this.AlertifyOptions = $.extend(true, {},
                $.fn.quickview.defaultAlertifyOptions,
                getOptions(this.$parent, 'alertify'),
                getOptions(this.$element, 'alertify')
            );

            this.AlertifyButtons = $.extend(true, {},
                $.fn.quickview.defaultButtons,
                getOptions(this.$parent, 'button'),
                getOptions(this.$element, 'button')
            );

            var self = this;

            alertify.quickview || alertify.dialog('quickview', function factory() {
                return {
                    main: function (content) {
                        this.setContent(content);
                        // this.setContent(content.replace(/\n/g, '<br/>'));
                    },
                    setup: function () {
                        return {
                            // https://alertifyjs.com/factory.html
                            buttons: [],
                            focus: {element: 0},
                            options: {
                                modal: true,
                                maximizable: false,
                                resizable: false,
                                overflow: false,
                                pinnable: false,
                                closable: true,
                                closableByDimmer: true,// закрытие - клик вне окна
                                title: '&nbsp;',
                                autoReset: false,
                                maintainFocus: false,

                                onclose: function (instance) {
                                    self.hideOutput(true);
                                },

                            },
                        };
                    },
                    hooks: {
                        'onshow': function () {

                            // custom "size"
                            if (self.options.size === 'big') {
                                this.elements.dialog.style.maxWidth = 'none';
                                this.elements.dialog.style.width = '85%';
                            }
                            else if (self.options.size === 'mid') {
                                this.elements.dialog.style.maxWidth = 'none';
                                this.elements.dialog.style.width = '55%';
                            }
                            else if (self.options.size === 'small') {
                                this.elements.dialog.style.maxWidth = 'none';
                                this.elements.dialog.style.width = '35%';
                            }

                        },
                        'onclose': function () {
                            return setTimeout((function () {
                                return this.destroy();
                            }.bind(this)), $.fn.quickview.defaults.timeout);
                        },
                    },

                    settings: {
                        message: undefined,
                        label: undefined,
                        selector: undefined,
                    },

                }
            }, true, 'alert');

            this.instance = alertify.quickview('');
            this.$output = $(this.instance.elements.root);
        };

        ViewAlertify.prototype.showOutput = function (r) {

            if (!this.instance) {
                this.instance = alertify.quickview('');
                this.$output = $(this.instance.elements.root);
            }

            this.instance.set(this.AlertifyOptions || {});
            this.setButtons(this.createButtons());
            $(this.instance.elements.content).html(r.data.output);

            // add cls "quickview-main", add "QuickView"
            this.$output.find('.ajs-modal').addClass($.fn.quickview.defaults.ns + '-main');
            this.$output.find('.ajs-modal').data('QuickView', this);

            if (!this.instance.__internal.isOpen) this.instance.show();

            return QuickView.prototype.showOutput.call(this, r);
        };

        ViewAlertify.prototype.hideOutput = function (r) {

            if (this.instance && !r) {
               this.instance.close();
            }

            this.instance = null;
            return QuickView.prototype.hideOutput.call(this, r);
        };

        ViewAlertify.prototype.getButtons = function () {
            var buttons, button;
            buttons = [];

            if (!this.defaults.buttons) {
                return buttons;
            }

            if (this.AlertifyOptions.buttons) {
                jQuery.each(this.AlertifyOptions.buttons, (function (_this) {
                    return function (index, action) {
                        if (typeof action === 'string') {
                            button = _this.AlertifyButtons[action] || {};
                        }
                        else {
                            button = action || {};
                            action = button['action'] || '';
                        }

                        button = $.extend({}, {
                            label: _this.defaults.msg[action] || action,
                            cssClass: _this.getNamespace('-' + _this.defaults.cls[action], _this.defaults.ns + '-action', (button['cls'] || '')),
                            action: action,
                            QuickView: _this
                        }, button);

                        buttons.push(button);
                    };
                })(this));
            }

            return buttons;
        };

        ViewAlertify.prototype.createButtons = function () {
            var buttons, $buttons;

            buttons = this.getButtons();
            if (buttons.length > 0) {
                $buttons = $('<div></div>');
                $buttons.addClass(this.getNamespace('-buttons'));

                jQuery.each(buttons, (function (_this) {
                    return function (index, button) {
                        if (!button.id) {
                            button.id = newGuid();
                        }
                        var $button = _this.createButton(button);
                        $buttons.append($button);
                    };
                })(this));
            }
            return $buttons;
        };

        ViewAlertify.prototype.createButton = function (button) {
            var $button = $('<button class="btn ajs-button"></button>');
            $button.prop('id', button.id);
            $button.data('button', button);

            // Icon
            if (typeof button.icon !== 'undefined' && $.trim(button.icon) !== '') {
                $button.append(this.createButtonIcon(button.icon));
            }

            // Label
            if (typeof button.label !== 'undefined') {
                $button.append(button.label);
            }

            // title
            if (typeof button.title !== 'undefined') {
                $button.attr('title', button.title);
            }

            // Css class
            if (typeof button.cssClass !== 'undefined' && $.trim(button.cssClass) !== '') {
                $button.addClass(button.cssClass);
            } else {
                $button.addClass('btn-default');
            }

            // Data attributes
            if (typeof button.data === 'object' && button.data.constructor === {}.constructor) {
                $.each(button.data, function (key, value) {
                    $button.attr('data-' + key, value);
                });
            }

            // Button on click
            $button.on('click', {QuickView: this, $button: $button, button: button}, function (event) {
                var QuickView = event.data.QuickView;
                var $button = event.data.$button;
                var button = $button.data('button');
                if (button.autospin) {
                    $button.toggleSpin(true);
                }
                if (typeof button.action === 'function') {
                    return button.action.call($button, QuickView, event);
                }
            });

            //Initialize enabled or not
            if (typeof button.enabled !== 'undefined') {
                $button.toggleEnable(button.enabled);
            }

            return $button;
        };


        ViewAlertify.prototype.setButtons = function (buttons) {
            if (this.instance.elements.buttons.primary) {
                $(this.instance.elements.buttons.primary).html(buttons);
            }

            return this;
        };


        return ViewAlertify;

    })(Module);


    $.fn.quickview = function () {
        var args, option;
        option = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [];
        return this.each(function () {
            var $this, $parent, quickview, options;

            $this = $(this);
            quickview = $this.data('QuickView');
            if (!quickview) {

                $parent = $this.closest($.fn.quickview.selectors.parent);
                if ($parent.length < 1) {
                    $parent = $('body');
                }

                options = $.extend(true, {},
                    getOptions($parent),
                    getOptions($this)
                );

                switch (options['mode']) {
                    case 'selector':
                        quickview = new ViewSelector(this, options);
                        break;
                    case 'modal':
                        quickview = new ViewModal(this, options);
                        break;
                    case 'alertify':
                        quickview = new ViewAlertify(this, options);
                        break;
                    default:
                        quickview = new ViewModal(this, options);
                        break;
                }

                if (quickview) {
                    $this.data('QuickView', quickview);
                    quickview.init();
                }
            }

            if (typeof option == 'undefined') {
                option = 'getContent';
            }

            if (typeof option === 'string') {
                return quickview[option].apply(quickview, args);
            }
        })
    };


    /* event constants */
    $.fn.quickview.CONTENT_SUCCESS = 'quickview:content-success.quickview';
    $.fn.quickview.CONTENT_FAILURE = 'quickview:content-failure.quickview';
    $.fn.quickview.CONTENT_SHOW = 'quickview:content-show.quickview';
    $.fn.quickview.CONTENT_HIDE = 'quickview:content-hide.quickview';


    $.fn.quickview.defaults = {
        ns: 'quickview',
        spinner: true,
        buttons: true,
        timeout: 300,
        msg: {
            prev: '&lt;',
            next: '&gt;',
            close: '&times;',
        },
        cls: {
            prev: 'prev btn-default',
            next: 'next btn-default',
            close: 'remove btn-default',
        },
        viewhash: ['id', 'selector']
    };


    $.fn.quickview.selectors = {
        main: '.' + $.fn.quickview.defaults.ns,
        parent: '.' + $.fn.quickview.defaults.ns + '-parent',
        output: '.' + $.fn.quickview.defaults.ns + '-output',
        actionBtn: '.' + $.fn.quickview.defaults.ns + '-action',
        actionClick: '[data-click]',
        actionMouseOver: '[data-mouseover]',
        actionWindowLoad: '[data-windowload]',
        actionFormSubmit: '[data-formsubmit]',
    };

    $.fn.quickview.defaultBDialogOptions = {
        title: 'title',
        message: 'message',
        size: 'size-normal',
        type: 'type-default',
        buttons: [],
        data: {},
        nl2br: false,
        closable: true,
        draggable: true,
        quickview: true,
        onshow: null,
        onshown: null,
        onhide: null,
        onhidden: null,
        closeIcon: '&times;',
        cssClass: $.fn.quickview.defaults.ns,
    };


    $.fn.quickview.defaultAlertifyOptions = {
        buttons: [],
        closeIcon: '&times;',
        cssClass: $.fn.quickview.defaults.ns,
    };

    $.fn.quickview.defaultButtons = {
        prev: {
            hotkey: 37,
            action: 'prev'
        },
        next: {
            hotkey: 39,
            action: 'next'
        },
        close: {
            action: 'close'
        },
    };


    $.fn.quickview.methodActions = {
        show: {
            'miniShop2.Message.initialize': function () {
                if (typeof miniShop2 == 'object') {
                    miniShop2.Message.initialize();
                }
            },
            'miniShop2.Gallery.initialize': function () {
                if (typeof miniShop2 == 'object') {
                    if (typeof ($.fn.fotorama) == 'undefined') {
                        miniShop2.Gallery.initialize();
                    }
                    else {
                        $('.fotorama').fotorama();
                    }
                }
            },
            'miniShop2.Cart.initialize': function () {
                if (typeof miniShop2 == 'object') {
                    if (!findEvent('change', miniShop2.Cart.cart + ' ' + miniShop2.Cart.countInput)) {
                        miniShop2.Cart.initialize();
                    }
                }
            },
            'miniShop2.Order.initialize': function () {
                if (typeof miniShop2 == 'object') {
                    if (!findEvent('change', miniShop2.Order.order + ' input,' + miniShop2.Order.order + ' textarea')) {
                        miniShop2.Order.initialize();
                    }
                }
            },

            'AjaxForm.submit.success': function () {
                if (typeof AjaxForm == 'object') {
                    var quickview = this;
                    $(document).off('af_complete').on('af_complete', function (e, response) {
                        // grecaptcha
                        if (typeof(grecaptcha) != 'undefined') {
                            //noinspection JSUnresolvedVariable
                            grecaptcha.reset();
                        }
                        // bootstrap modal
                        if (response.success && $('body').hasClass('modal-open')) {
                            quickview.hideOutput();
                            $('.modal-backdrop').remove();
                        }
                        // fancybox modal
                        if (response.success && $('body').hasClass('fancybox-active')) {
                            quickview.hideOutput();
                            $.fancybox.getInstance().close()
                        }
                    });
                }
            },

            'payandsee.Message.initialize': function () {
                if (typeof payandsee == 'object') {
                    payandsee.Message.initialize();
                }
            },
            'payandsee.Order.initialize': function () {
                if (typeof payandsee == 'object') {
                    payandsee.Order.initialize();
                }
            },

            'msfavorites.initialize': function () {
                if (typeof msFavorites == 'object') {
                    $($.fn.msfavorites.selectors.main).msfavorites();
                }
            }

        },
        hide: {
            'bootstrap-modal.close': function () {
                // bootstrap modal
                if ($('body').hasClass('modal-open')) {
                    var modal = $('.modal.fade.in').data('bs.modal');
                    if (modal) {
                        modal.hide();
                    }
                }
            },
            'fancybox.close': function () {
                // fancybox modal
                if ($('body').hasClass('fancybox-active')) {
                    $.fancybox.getInstance().close()
                }
            }
        },
    };


    /* click */
    $(document).on('click', $.fn.quickview.selectors.main + $.fn.quickview.selectors.actionClick, function (e) {
        var $this = $(this);
        if ($this.is('a')) {
            e.preventDefault();
        }
        $this.quickview();
    });

    /* mouseover */
    $(document).on('mouseover', $.fn.quickview.selectors.main + $.fn.quickview.selectors.actionMouseOver, function (e) {
        var $this = $(this);
        if ($this.is('a')) {
            e.preventDefault();
        }
        $this.quickview();
    });

    /* hide modal/ fix for clear output */
    $(document).on('hide.bs.modal', function (e) {
        if (e && e.target) {
            var $this = $(e.target),
                $output,
                tmp = $this.data('bs.modal');
            if (tmp && tmp.options && tmp.options.quickviewOutput) {
                $output = $this.find(tmp.options.quickviewOutput);
                if ($output.length > 0) {
                    $output.html('');
                }
            }
        }
    });


    /* load */
    $(window).on('load', function (e) {
        $($.fn.quickview.selectors.main + $.fn.quickview.selectors.actionWindowLoad).quickview();
    });

    /* form submit */
    $(document).on('submit', $.fn.quickview.selectors.main + $.fn.quickview.selectors.actionFormSubmit, function (e) {
        var $this = $(this), options = {};
        e.preventDefault();

        $.map($this.serializeArray() || [], function (n, i) {
            options[n['name']] = n['value'];
        });
        setOptions($this, 'data', options);
        $this.quickview();
    });

    /* action */
    $(document).on('click', $.fn.quickview.selectors.main + ' ' + $.fn.quickview.selectors.actionBtn, function (e, m) {
        var $this, button, quickview, neighbor;

        $this = $(this);
        if ($this.is('a')) {
            e.preventDefault();
        }

        if (!(button = $this.data('button'))) {
            return;
        }
        if (!(quickview = button.QuickView) && !(quickview = $this.closest('.' + $.fn.quickview.defaults.ns + '-main').data('QuickView'))) {
            return;
        }

        switch (button.action) {
            case 'prev':
            case 'next':
                neighbor = quickview.getNeighbor(button.action);
                if (neighbor) {
                    $(neighbor).quickview();
                }
                break;
            case 'close':
                quickview.hideOutput();
                break;
            default:
                break;
        }
    });

    /* window load  */
    $(window).on('load', function (e) {
        var view = findViewByHash();
        if (view && view.length) {
            view.get(0).click();
        }
    });

    /* window popstate  */
    $(window).on('popstate', function (e) {
        // TODO add history
        if (e.originalEvent.state && e.originalEvent.state['QuickView']) {
            var view = findViewByHash();
            if (view && view.length) {
                view.get(0).click();
            }
        }
    });

    window.QuickView = new QuickView();


}(window.jQuery, QuickViewConfig));