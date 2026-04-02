Msie.panel.Timer = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'importexport-timer-' + Ext.id();
    }
    Ext.applyIf(config, {
        border: false,
        enable: true,
        time: 60,
        autostart: false,
        items: this.getFields(config)
    });
    Msie.panel.Timer.superclass.constructor.call(this, config);
    this.addEvents('complete');
    this.on('afterlayout', this.setup, this);

};
Ext.extend(Msie.panel.Timer, MODx.Panel, {
    getFields: function (config) {
        return [{
            html: '<div class="msimportexport-timer"><div class="msimportexport-timer-progress"></div><div class="msimportexport-timer-btn"></div></div>',
        }];
    },
    setup: function () {
        if (!Msie.utils.isEmpty(this.bar)) return;
        this.paused = false;
        this.bar = null;
        this.timer = 0;
        this.repeat = 0;
        this.progress = 0;
        this.iteration = 0;
        this.body.on('click', this.pause, this);
        this.bar = Ext.select('#' + this.id + ' .msimportexport-timer-progress');
        if (Msie.utils.isEmpty(this.bar)) return;
        if (!this.time) {
            this.setEnable(false);
        } else {
            this.setEnable(this.enable);
        }
        if (this.theme) this.bar.addClass(this.theme);
        if (this.autostart) this.start();
    },
    start: function () {
        if (this.paused || !this.enable || Msie.utils.isEmpty(this.bar)) return;
        this.repeat++;
        var self = this;
        //this.bar.setStyle('width', '0px');
        this.bar.addClass('smooth active');
        this.timer = setInterval(function () {
            self.iteration++;
            self.progress = (self.iteration * 100) / self.time;
            self.bar.setStyle('width', self.progress + '%');
            if (self.iteration == self.time) {
                clearInterval(self.timer);
                self.bar.removeClass('active');
                setTimeout(function () {
                    self.fireEvent('complete', self);
                }, 1000);
            }
        }, 1000);
    },
    reset: function () {
        if (this.paused || !this.enable) return;
        var self = this;
        this.stop();
        if (this.repeat) {
            setTimeout(function () {
                self.start();
            }, 1000);
        } else {
            self.start();
        }
    },
    pause: function () {
        if (!this.enable) return;
        var self = this,
            cls = this.paused ? 'timer-play' : 'timer-pause';
        if (this.paused) {
            this.paused = false;
            this.addClass(cls);
            this.start();
        } else {
            if (!this.timer) return;
            this.paused = true;
            clearInterval(this.timer);
            this.addClass(cls);
        }
        setTimeout(function () {
            self.removeClass(cls);
        }, 600);
    },
    stop: function () {
        clearInterval(this.timer);
        if (this.bar) {
            this.bar.removeClass('active');
            this.bar.removeClass('smooth');
            this.bar.setStyle('width', '0px');
        }
        this.progress = 0;
        this.iteration = 0;
    },
    setTime: function (time) {
        this.time = time;
    },
    setEnable: function (enable) {
        this.enable = enable;
        if (this.enable) {
            this.removeClass('timer-disable');
        } else {
            this.addClass('timer-disable');
        }
    },
    isPause: function () {
        return this.paused;
    }
});
Ext.reg('msie-panel-timer', Msie.panel.Timer);