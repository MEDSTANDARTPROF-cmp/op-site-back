Msie.panel.TaskChart = function (config) {
    config = config || {};
    Ext.apply(config, {
        border: false,
        items: this.getFields(config),
        listeners: this.getListeners(config)
    });
    Msie.panel.TaskChart.superclass.constructor.call(this, config);
};
Ext.extend(Msie.panel.TaskChart, MODx.Panel, {
    data: [],
    chart: null,
    getFields: function (config) {
        return [{
            html: '<div id="msie-task-chart-container"></div>',
        }];
    },
    getListeners: function (config) {
        return {
            afterlayout: {
                fn: this.setup, scope: this
            }
        };
    },
    getColors: function () {
        return {
            base: '#2A3338',
            memory: '#907b91',
            memory_peak: '#D5CAD6',
            time: '#A7C9B4',
            stats: {
                created: '#66E5CC',
                updated: '#2197CF',
                errors: '#FA4C28',
                duplication: '#F3B116',
            },
            other: [
                '#B88069',
                '#33363E',
                '#7E5664',
                '#593C8F',
                '#8EF9F3',
                '#171738',
                '#ACC196',
                '#E9EB9E',
            ]
        };
    },
    setup: function () {
        var dashboard = Ext.get('msie-panel-task-dashboard');
        this.setWidth(dashboard.getWidth() - 40);
        if (this.chart) {
            window.dispatchEvent(new Event('resize'));
            return;
        }
        var options = {
            chart: {
                zoomType: 'x'
            },
            title: {
                text: null
            },
            xAxis: {
                allowDecimals: false,
            },
            yAxis: [{
                gridLineWidth: 0,
                title: {
                    text: _('msimportexport_task_chart_title_time'),
                    style: {
                        color: this.getColors().base
                    }
                },
                labels: {
                    format: '{value} Ms',
                    formatter: function () {
                        if (!this.value) return;
                        return Msie.utils.msToTime(this.value);
                    },
                    style: {
                        color: this.getColors().base
                    }
                }
            }, {
                gridLineWidth: 0,
                title: {
                    text: _('msimportexport_task_chart_title_memory'),
                    style: {
                        color: this.getColors().base
                    }
                },
                labels: {
                    format: '{value} Mb',
                    style: {
                        color: this.getColors().base
                    }
                },
                opposite: true

            }, {
                title: {
                    text: _('msimportexport_task_chart_title_stats'),
                    style: {
                        color: this.getColors().base
                    }
                },
                labels: {
                    format: '{value}',
                    style: {
                        color: this.getColors().base
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true,
                formatter: function () {
                    var val,
                        s = _('msimportexport_task_chart_iteration') + ': <b>' + this.x + '</b><br>';
                    Ext.each(this.points, function (point) {
                        val = this.y;
                        switch (point.series.options.id) {
                            case 'timestamp':
                                val = Msie.utils.msToTime(this.y, 6);
                                break;
                            case 'memory':
                            case 'memory_peak':
                                if (parseInt(this.y) !== this.y) {
                                    val = parseFloat(this.y).toFixed(5);
                                }
                                break;
                        }
                        s += '<span style="color: ' + point.series.color + ';">●</span> ' + point.series.name + ': <b>' + val + '</b><br/>';
                    });
                    return s;
                }
            },
            plotOptions: {
                column: {
                    borderRadius: 2,
                    grouping: false,
                    shadow: false,
                    borderWidth: 0,
                    states: {
                        hover: {
                            enabled: false
                        }
                    }
                },
            },
            series: []
        };
        this.chart = Highcharts.chart('msie-task-chart-container', options);
        if (!Msie.utils.isEmpty(this.data)) {
            this.setData(this.data);
        }
    },
    setupSeries: function (data) {
        if (!this.chart || !Msie.utils.isEmpty(this.chart.series)) return;
        this.chart.addSeries({
            name: _('msimportexport_task_chart_serie_time'),
            type: 'column',
            id: 'time',
            yAxis: 0,
            pointPadding: 0.3,
            pointPlacement: 0.2,
            pointStart: 1,
            color: this.getColors().time,
            tooltip: {
                valueSuffix: ' ms'
            }
        }, false);
        this.chart.addSeries({
            name: _('msimportexport_task_chart_serie_memory_peak'),
            type: 'column',
            id: 'memory_peak',
            yAxis: 1,
            pointPadding: 0.3,
            pointPlacement: -0.2,
            pointStart: 1,
            color: this.getColors().memory_peak,
            //color: 'rgba(165,170,217,1)',
            marker: {
                enabled: false
            },
            tooltip: {
                valueSuffix: ' mb'
            }
        }, false);
        this.chart.addSeries({
            name: _('msimportexport_task_chart_serie_memory'),
            type: 'column',
            id: 'memory',
            yAxis: 1,
            pointPadding: 0.4,
            pointPlacement: -0.2,
            pointStart: 1,
            color: this.getColors().memory,
            //color: 'rgba(126,86,134,.9)',
            marker: {
                enabled: false
            },
            tooltip: {
                valueSuffix: ' mb'
            }
        }, false);

        var idx = 0;
        Ext.iterate(data.stats || {}, function (key, val) {
            var color = this.getColors().stats[key] ? this.getColors().stats[key] : this.getColors().other[idx];
            this.chart.addSeries({
                name: _('msimportexport_task_chart_serie_' + key),
                id: key,
                type: 'spline',
                yAxis: 2,
                pointStart: 1,
                color: color,
            }, false);
            idx++;
        }, this);
        this.chart.redraw();
    },
    getSerieById: function (id) {
        var serie = null;
        if (!this.chart || !id) return serie;
        Ext.each(this.chart.series, function (item) {
            if (item.options.id === id) {
                serie = item;
                return false;
            }
        });
        return serie;
    },
    setData: function (data) {
        this.data = data;
        if (!this.chart) return;
        Ext.each(this.data, function (set) {
            this.setupSeries(set);
            this.chart.series[0].addPoint([set.iteration, set.time], false, false, false);
            this.chart.series[1].addPoint([set.iteration, set.memory_peak], false, false, false);
            this.chart.series[2].addPoint([set.iteration, set.memory], false, false, false);
            Ext.iterate(set.stats || {}, function (key, val) {
                var serie = this.getSerieById(key);
                if (!serie) return true;
                serie.addPoint([set.iteration, val], false, false, false);
            }, this);
        }, this);
        this.chart.redraw(true);
    },
    reset: function () {
        if (this.chart) this.chart.destroy();
    }
});
Ext.reg('msie-panel-task-chart', Msie.panel.TaskChart);