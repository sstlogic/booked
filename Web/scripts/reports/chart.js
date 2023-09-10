var TimeTickFormatter = function (format, val) {
    var numdays = Math.floor(val / 86400);
    var numhours = Math.floor((val % 86400) / 3600);
    var numminutes = Math.floor(((val % 86400) % 3600) / 60);

    var hoursAndMinutes = numhours + "h " + numminutes + "m ";

    if (numdays > 0) {
        return numdays + "d " + hoursAndMinutes;
    }
    return hoursAndMinutes;
};

const colors = ["#ffeb30","#1a9e00","#ff1f80","#00db80","#8aa500","#dba200","#91a300","#f36a00","#ef6e00","#16fee0","#4c48b0","#b60030","#007b00","#007900","#c89000","#c59100","#7d9100","#007800","#bf2050","#8f0600","#ba3b00","#9d0020","#b63f00","#b14400","#7f0000","#7b0000","#892000","#12dbc0","#e2e380","#b08000","#212160","#678100","#ad4700","#a74c00","#770900","#731200","#923d00","#630000","#9b7000","#557000","#004a00","#004900","#004700","#5d0000","#751f00","#712300","#570000","#520000","#6c2700","#8d5e00","#8b5f00","#0092e0","#8a5f00","#5a6f00","#999f40","#5d6e00","#004700","#004600","#004500","#360000","#4f0000","#4c0000","#580c00","#682a00","#886000","#856100","#004400","#014300","#7a4080","#490000","#370000","#340000","#300000","#480000","#470000","#450000","#420000","#3f0000","#3c0000","#340000","#2a0000","#f4f6f0","#2c0100","#350000","#031000","#001200","#001400","#001600","#002800","#002600","#001700","#0e0d00","#380000","#420500","#456000","#003b00","#003600","#002b00","#002500","#001900","#001900","#001b00","#002300","#004d10","#fc9cb0","#664000","#3b0000","#3a0000","#3b0000","#001c00","#012300","#002100","#002100","#394f00","#036f40","#573000","#310100","#2f0200","#cde7f0","#270200","#3d0900","#210400","#001f00","#001d00","#1e0600","#83c5f0","#414c00","#fbb2a0","#c6c790","#1b0700","#230700","#290b10","#1b0900","#160a00","#100e00","#120f00","#c9d2d0","#472200","#042000","#151100","#161200","#171200","#181300","#191400","#1c1600","#0e2000","#2d4000","#553100","#533200","#513200","#4f3300","#4d3400","#2e3f00","#2f3f00","#004920","#333c00","#303e00","#585080","#323d00","#7488b0","#ad9770","#003450","#002530","#9e7da0","#282f00","#b29380","#776440","#004360","#516f40","#839ea0","#007680","#004550","#5f5260","#435560","#1940e1","#00df71","#1b8b01","#003091","#fcc141","#003ca1","#970001","#fc4e81","#111e71","#ffa841","#ff77b1","#730001","#0074d1","#002c71","#97a031","#6a0001","#717e01","#005fb1","#dc8441","#836101","#2a0001","#fdf2f1","#fdf3e1","#040d11","#745001","#505c01","#043101","#250201","#b5ddf1","#390e01","#00a7c1","#5e4201","#265891","#003311","#582931","#021d21","#222001","#83a271","#1b2a41","#008891","#282c01","#8b5a51","#8b84a1","#006081","#686941","#063b51","#292f21","#825c61","#377251","#3e3f21","#495771","#1d4e51","#6a6661","#676761","#43b512","#0034b2","#90eb62","#e40812","#be0002","#d5a422","#e2e472","#0096f2","#026902","#002c72","#610002","#eeeaa2","#771712","#f5ce82","#ffb472","#f6d292","#004702","#001102","#ff8b72","#e17462","#8e6e22","#f9b9c2","#00aad2","#6b7122","#c1b8e2","#b6d8c2","#bacec2","#031912","#905a32","#c98c62","#493502","#bebcc2","#c38a92","#271812","#b8bec2","#9c9c72","#c28d82","#312532","#007192","#8b9e92","#928492","#4b3a32","#264332","#5b6b52","#646762","#5a5552","#6ceb33","#c1d413","#001b83","#4145c3","#0032a3","#8378f3","#59ac33","#0ffff3","#ab0003","#db0653","#00b853","#00a543","#ff60a3","#ce2a23","#a6e783","#fa5983","#31ca83","#870203","#a50033","#00def3","#002863","#015803","#5c0003","#8ae8b3","#554d93","#3aad73","#642d03","#5489d3","#ffb4d3","#154103","#3f1b43","#0f8953","#002a53","#001213","#dde4e3","#f9c2b3","#0089c3","#00a1b3","#301903","#d8bdb3","#0b1d03","#632c23","#704973","#363b03","#005c83","#6aa683","#007393","#502e33","#8e9e83","#372523","#023743","#6d8ca3","#473b23","#004c63","#025d73","#786163","#738693","#3b3733","#7b7673","#6c6563","#383833","#4b4643","#00cb64","#005cd4","#e87714","#0089f4","#008724","#fc5644","#00e8f4","#f9ca64","#f9cd74","#004fa4","#897cd4","#00d6e4","#00a4f4","#b8fbd4","#56dce4","#00a2e4","#71a854","#a2bef4","#00a6d4","#d2e5f4","#0d0c04","#110b04","#431304","#662834","#3b1504","#9bbed4","#9c5144","#0e9aa4","#47aeb4","#003154","#b19664","#1b79a4","#008264","#3c2804","#7a6334","#b88f94","#007054","#004124","#9d9994","#9a9a94","#8c8784","#585464","#363834","#626864","#36bb05","#ffc605","#efb405","#4fae25","#002d85","#71f9b5","#c02e25","#ff6385","#e18135","#a2d785","#003e85","#0fa665","#1f5705","#e2abe5","#af72b5","#7cd7f5","#500805","#007fc5","#2abdc5","#ffd9e5","#fdd1b5","#031725","#ffac95","#009375","#001e05","#a1d2d5","#c9d1d5","#d68475","#008d75","#5a3315","#8b81b5","#007f65","#021f25","#004825","#291d05","#007d65","#003045","#007b65","#006c55","#7b88a5","#2a2735","#72a2a5","#005c45","#164f35","#979a95","#006655","#2d3c25","#024045","#3d3635","#6c6565","#343935","#515855","#87ec56","#001896","#a5b806","#0cffa6","#ff9716","#fc8206","#008bf6","#9ca726","#504aa6","#003986","#ff6c56","#730026","#001e46","#00afe6","#004c16","#00a686","#ff9f86","#090d06","#5e3106","#292546","#dad5b6","#94c3e6","#2193c6","#4babc6","#002b46","#9b9d66","#0085a6","#365786","#ba9176","#008876","#ac9396","#003136","#835f46","#676a36","#192c36","#2d2826","#a09896","#004c36","#004a36","#654f66","#005746","#005346","#6f6556","#447056","#154f46","#275f56","#536b66","#4f5666","#2d42d7","#005ff7","#0033a7","#002687","#007cf7","#d11a57","#fbcc57","#002f87","#870027","#ffb167","#347417","#00bb97","#b94637","#0059a7","#252257","#b13d57","#00b997","#00b597","#bcf1f7","#440517","#fce3d7","#ffb8c7","#fdcdd7","#002747","#fcd2c7","#cce8e7","#00adb7","#cf8957","#6189c7","#d5b2d7","#2c0d07","#002407","#a47aa7","#069d87","#0b91a7","#004877","#60a3b7","#362337","#b0aaa7","#1e1b17","#4b7037","#585177","#222937","#8a8697","#004737","#959b97","#004237","#004437","#4f3727","#825e57","#838987","#3d3637","#686757","#293e47","#586e77","#2f4747","#424847","#606867","#0015a8","#7dee48","#ffd818","#ffcd38","#9fe878","#005bc8","#d61318","#e00048","#53f4a8","#00bd68","#8579e8","#1b1f68","#e77e28","#287b08","#0065b8","#003678","#431648","#ededb8","#c1b4f8","#ab4638","#00b5c8","#00ae98","#003e78","#003468","#001e38","#001118","#551a08","#130a08","#ffbba8","#d87968","#955728","#006098","#002c08","#7ea468","#aabee8","#c0b9d8","#d27a88","#0097b8","#c8b9c8","#b49558","#7d6328","#51a898","#c1bea8","#76a488","#1a1c18","#003528","#033c28","#a39888","#9b9a88","#252a28","#a09798","#117278","#746358","#2d7488","#005868","#727878","#323938","#fc4639","#006ad9","#002069","#00eaf9","#00cda9","#004499","#0061b9","#0094e9","#f66a89","#7e0529","#00c4a9","#0093d9","#fee8b9","#8a7ec9","#006929","#6d0d29","#9c5419","#470009","#eff7f9","#11bbd9","#4dab79","#060e09","#427929","#e5c799","#001719","#ffb599","#d0dac9","#91d3e9","#a84559","#002a09","#574e89","#754579","#002709","#0d1909","#c2bcb9","#002229","#a8bcc9","#0d7399","#004569","#003319","#201a19","#66a599","#5c3129","#018199","#b79089","#0f7869","#988199","#76a299","#004159","#859f99","#818899","#566d49","#e2f51a","#31f19a","#001c7a","#e8eb6a","#4747ba","#9a093a","#00903a","#228aea","#85001a","#91211a","#6aa94a","#006c1a","#f8705a","#007a3a","#3cbbfa","#ffbf8a","#66700a","#00beea","#ff836a","#00579a","#fe917a","#fde4ea","#ffc4da","#59b4ea","#8dd6da","#00a8ca","#d4cfca","#d0d0ca","#00230a","#9b9e5a","#666a2a","#89a07a","#002e1a","#94564a","#50301a","#57441a","#002b3a","#a9968a","#4f302a","#939b9a","#00708a","#4c373a","#43858a","#084a5a","#416d7a","#60695a","#130a0b","#e7f44b","#005eeb","#005ddb","#e7eb5b","#00e2bb","#0058bb","#c1920b","#00dcbb","#5ffffb","#fa5f4b","#79fffb","#cafbab","#00337b","#ffae5b","#ba382b","#001a4b","#c03a5b","#8af1fb","#5fd9fb","#0060ab","#00285b","#b3db9b","#e96f5b","#ffe1eb","#e37a8b","#b8944b","#e6ddcb","#001b0b","#b9cfab","#cdd1cb","#004b1b","#00220b","#9f4c5b","#00231b","#cc887b","#8b83ab","#02283b","#032a1b","#007b8b","#41577b","#73654b","#37766b","#4a746b","#00535b","#4f6d5b","#61676b","#57565b","#51575b","#00198c","#01f68c","#00319c","#f7005c","#002d7c","#0083dc","#534b9c","#007e2c","#00cfec","#f0758c","#58000c","#00c4fc","#ffa4cc","#99eefc","#e4fbec","#c3f0fc","#c0e1ac","#d6874c","#030e0c","#006dac","#ffa68c","#00120c","#d7cecc","#b0bddc","#0079ac","#b0cccc","#c28f6c","#6c89bc","#b8bfbc","#201b0c","#a6adac","#8a5c3c","#8aa1ac","#a8977c","#31281c","#939c8c","#05342c","#68684c","#02646c","#33393c","#39686c","#0036bd","#3944cd","#8076fd","#98ea6d","#ff156d","#ff332d","#00f8cd","#00f6cd","#00f3cd","#005abd","#01ab5d","#e3e38d","#00d1fd","#91fffd","#e8a7ed","#00843d","#a9fffd","#00b8fd","#ff8bad","#bdfffd","#bb943d","#fcb77d","#001d3d","#002c5d","#cefefd","#00c9dd","#6ec6fd","#90d8ad","#00121d","#fdadbd","#c1b6ed","#001a2d","#33060d","#abdbbd","#f5be9d","#3a1f3d","#cbd1cd","#bfbbcd","#00568d","#96525d","#05272d","#9b9b7d","#003e5d","#26744d","#6a4c6d","#2c4b1d","#283d1d","#81a18d","#2a292d","#252a2d","#58536d","#006b7d","#89878d","#83898d","#77615d","#516b6d","#004cbe","#00edce","#ffa52e","#877bde","#00e3fe","#00a7fe","#70f3fe","#58e5ce","#7aeaae","#470f4e","#bd932e","#ff7eae","#ff97ce","#00326e","#006fbe","#73e2ce","#004c8e","#51000e","#defafe","#dcf0ce","#dbafde","#10b69e","#681a2e","#77a65e","#9a9e4e","#23510e","#aa76ae","#e7e1de","#f4d2be","#001a1e","#a44c3e","#d7cece","#44722e","#5da87e","#3db19e","#d9babe","#2a263e","#161d1e","#5d8dae","#034e6e","#939b9e","#3c4a3e","#00179f","#7271ff","#005ddf","#7773ff","#7c74ff","#b1bc2f","#008cff","#008cff","#008dff","#16ffff","#00fbff","#fe3f7f","#00f7ff","#00f4ff","#31faff","#c4003f","#008eff","#008fff","#008fff","#00f2ff","#00f0ff","#00edff","#00e9ff","#009cff","#00e6ff","#50f6ff","#00e0ff","#ffab4f","#b3a5ff","#b6a7ff","#62ab3f","#00b0ff","#ba5e0f","#00b6ff","#00beff","#2ae5ef","#00dcff","#31e2ff","#1fd9ff","#00cdff","#b9a9ff","#bbaaff","#bdacff","#bfaeff","#00cbff","#05c9ff","#00c3ff","#00c3ff","#00c2ff","#00c1ff","#00c1ff","#428adf","#c0afff","#c1b1ff","#c1b3ff","#28234f","#aaecff","#51c8ff","#00c2cf","#89dfcf","#76d9df","#e7f7ff","#eef6ff","#f8f3ff","#f9f1ff","#ffeaff","#faf0ff","#faeeff","#ffe3ff","#f9e4ff","#fae5ff","#fbecff","#030e0f","#fbedcf","#00182f","#73bfff","#83bfff","#99beff","#f2dcff","#f4deff","#f8e2ff","#fae7ff","#fbebff","#fbe9ff","#f6e0ff","#8fbeff","#9bdccf","#77291f","#fabc8f","#8b80bf","#0091cf","#005f9f","#fd977f","#ffc0af","#00345f","#acd9cf","#bcd3df","#bbd5cf","#200c0f","#ceb6cf","#5095bf","#00466f","#005f8f","#b6bdcf","#061a0f","#031f0f","#ce858f","#454a0f","#63abaf","#74a0af","#8c585f","#ac948f","#003d4f","#7c614f","#2d617f","#22362f","#413e2f","#3a5a6f","#596b5f"];

function BookedChart(options) {
    var chartDiv = $('#chartdiv');

    var chartIndicator = $('#chart-indicator');

    this.clear = function () {
        chartDiv.hide();
    };

    this.generate = function () {
        var resultsDiv = $('#report-results');
        chartDiv.show();
        chartIndicator.show();

        var chartType = resultsDiv.attr('chart-type');
        var series = null;
        if (chartType == 'totalTime' || chartType == 'total') {
            series = new TotalSeries();
        } else {
            series = new DateSeries(options);
        }
        $('#report-results>tbody>tr').each(function () {
            series.Add($(this));
        });

        var data = {
            labels: series.GetXLabels(),
            datasets: series.GetData()
        };

        var ctx = document.getElementById('chart-canvas').getContext('2d');
        var chart = new Chart(ctx, {
            type: "line",
            data: data,
            options: {
                scales: {
                    xAxes: series.GetXAxis(),
                    yAxes: {
                        min: 0
                    },
                }
            }
        });

        chartIndicator.hide();

        return chart;
    };

    function Series() {
        this.Add = function (row) {
        };

        this.GetData = function () {
            return [];
        };

        this.GetLabels = function () {
            return [];
        };

        this.GetXLabels = function () {
            return [];
        };

        this.GetXAxis = function () {
            return {};
        };
    }

    function TotalSeries() {
        this.series = [];
        this.labels = [];

        this.Add = function (row) {
            var itemLabel = row.find('td[chart-column-type="label"]').text();
            var val = parseInt(row.find('td[chart-column-type="total"]').attr("chart-value"));
            if (!this.labels.includes(itemLabel)) {
                this.labels.push(itemLabel);
            }
            this.series.push(val);
        };

        this.GetData = function () {
            return this.series;
        };

        this.GetXLabels = function () {
            return this.labels;
        };
    }

    TotalSeries.prototype = new Series();

    function TotalTimeSeries() {
        this.series = [];
        this.labels = [];
    }

    TotalTimeSeries.prototype = new TotalSeries();

    function DateSeries(options) {
        this.labels = [];
        this.groups = [];
        this.min = null;
        this.first = true;
        this.dates = [];

        this.Add = function (row) {
            var date = moment(row.find('td[chart-column-type="date"]').attr('chart-value'));
            var groupCell = row.find('td[chart-group="r"],td[chart-group="a"]');
            var groupId = groupCell.attr('chart-value');
            var groupName = groupCell.text();
            var totalValue = row.find('td[chart-column-type="total"]').attr('chart-value');
            var total = !totalValue ? 1 : parseInt(totalValue);

            if (!this.groups[groupId]) {
                this.groups[groupId] = new this.GroupSeries(groupName, groupId);
            }
            this.groups[groupId].AddDate(date, total);

            if (this.first) {
                this.min = date;
                this.first = false;
            }

            let formatted = date.format("YYYY-MM-DD");

            if (!this.dates.includes(formatted)) {
                this.dates.push(formatted);
            }
        };

        this.dataLoaded = false;
        this.GetData = function () {
            var data = [];
            if (!this.dataLoaded) {
                for (var group in this.groups) {
                    data.push({
                        label: this.groups[group].GetLabel(),
                        data: this.groups[group].GetData(),
                        backgroundColor: colors,
                    });

                    this.labels.push({label: this.groups[group].GetLabel()});
                }
                this.dataLoaded = true;
            }

            return data;
        };

        this.GetLabels = function () {
            if (this.labels.length <= 0) {
                for (var group in this.groups) {
                    this.labels.push({label: this.groups[group].GetLabel()});
                }
            }

            return this.labels;
        };

        this.GetLegendOptions = function () {
            return {
                show: true,
                placement: 'outsideGrid',
                fontSize: '10pt'
            };
        };

        this.GroupSeries = function (label, groupId) {
            var groupLabel = label;
            var series = [];
            var id = groupId;

            this.AddDate = function (date, count) {
                if (count === '' || count === undefined) {
                    count = 0;
                }
                if (series[date]) {
                    series[date] += count;
                } else {
                    series[date] = count;
                }
            };

            this.GetLabel = function () {
                return groupLabel;
            };

            this.GetData = function () {
                var data = [];
                for (var date in series) {
                    // data.push([date, series[date]]);
                    // data.push({t: date, y:series[date]});
                    data.push(series[date]);
                }
                return data;
            };

            this.GetId = function () {
                return id;
            };
        };

        this.GetXLabels = function () {
            return this.dates;
        };

        this.GetXAxis = function () {
            return {
                type: 'time',
                time: {
                    parser: "YYYY-MM-DD",
                    unit: 'day'
                }
            };
        };
    }

    DateSeries.prototype = new Series();
}