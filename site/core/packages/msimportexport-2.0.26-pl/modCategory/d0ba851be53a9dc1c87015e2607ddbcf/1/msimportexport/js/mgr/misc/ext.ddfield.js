Msie.FieldDragZone = function (panel, config) {
    config = config || {};
    this.panel = panel;
    Msie.FieldDragZone.superclass.constructor.call(this, panel.getEl());
};
Ext.extend(Msie.FieldDragZone, Ext.dd.DragZone, {
    getDragData: function (e) {
        var target = e.getTarget(this.panel.itemSelector);

        if (!target) {
            return false;
        }
        return {
            ddel: target,
            single: true
        };
    }
});


Msie.FieldDropZone = function (panel, config) {
    config = config || {};
    this.panel = panel;
    Msie.FieldDropZone.superclass.constructor.call(this, panel.getEl(), {containerScroll: true});
};
Ext.extend(Msie.FieldDropZone, Ext.dd.DropZone, {
    getTargetFromEvent: function (e) {
        return e.getTarget(this.panel.itemSelector);
    }
    , onNodeEnter: function (target, dd, e, data) {
        Ext.fly(target).addClass('x-panel-selected');
    }
    , onNodeOut: function (target, dd, e, data) {
        Ext.fly(target).removeClass('x-panel-selected');
    }

    , onNodeOver: function (target, dd, e, data) {
        if (target != data.ddel) {
            return Ext.dd.DropZone.prototype.dropAllowed;
        } else {
            Ext.dd.DropZone.prototype.dropNotAllowed;
        }
    }

    , onNodeDrop: function (target, dd, e, data) {
        var targetElement = Ext.get(target),
            sourceElement = Ext.get(data.ddel),
            targetCmp = Ext.getCmp(targetElement.getAttribute('id')),
            sourceCmp = Ext.getCmp(sourceElement.getAttribute('id')),
            targetIndex = targetCmp.getIndex(),
            sourceIndex = sourceCmp.getIndex();

        if (sourceElement.getTop() > targetElement.getTop()) {
            sourceElement.insertBefore(targetElement);
        } else {
            sourceElement.insertAfter(targetElement);
        }
        targetCmp.setIndex(sourceIndex);
        sourceCmp.setIndex(targetIndex);
        this.panel.refreshFields();
        return true;
    }
});