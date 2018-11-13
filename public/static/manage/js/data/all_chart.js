$(function () {
    getChartInfo();
    $('.choseDate li').click(function () {
        var choseDate = $(this).data('val');
        getChartInfo(choseDate);
    });
});
var getChartInfo = function(choseDate){
    $.ajax({
        url : '/manage/income/getDetailChartsInfo',
        type : 'POST',
        data:{
            choseDate : choseDate
        },
        dataType : 'json',
        success : function (res) {
            console.log(res);
            var xAxisData =[],incomeData = [],outData =[],netIncomeData = [];
            if(!!res.code){
                xAxisData = res.data.xAxisData;
                incomeData = res.data.income;
                outData = res.data.out;
                netIncomeData = res.data.netIncome;
            }
            var myChart = echarts.init(document.getElementById("incomeChartAll"));
            var option = getOption(xAxisData,incomeData,outData,netIncomeData);
            myChart.setOption(option);
            window.onresize = myChart.resize;

        },
        error : function (res) {
            console.log(res);
        }
    });
};

var getOption = function (xAxisData,incomeData,outData,netIncomeData) {
    var colors = ['#5793f3', '#d14a61', '#675bba'];
    return {
        color: colors,
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross'
            }
        },
        grid: {
            right: '20%',
            bottom: '10%'
        },
        legend: {
            data:['收入','支出','净收益']
        },
        xAxis: [
            {
                type: 'category',
                axisTick: {
                    alignWithLabel: true
                },
                data: xAxisData
            }
        ],
        yAxis: [
            {
                type: 'value',
                name: '支出/元',
                min: 0,
                position: 'right',
                axisLine: {
                    lineStyle: {
                        color: colors[0]
                    }
                },
                axisLabel: {
                    formatter: '{value}'
                }
            },
            {
                type: 'value',
                name: '净收益/元',
                min: 0,
                position: 'right',
                offset: 80,
                axisLine: {
                    lineStyle: {
                        color: colors[1]
                    }
                },
                axisLabel: {
                    formatter: '{value}'
                }
            },
            {
                type: 'value',
                name: '收入/元',
                min: 0,
                position: 'left',
                axisLine: {
                    lineStyle: {
                        color: colors[2]
                    }
                },
                axisLabel: {
                    formatter: '{value}'
                }
            }
        ],
        series: [
            {
                name:'收入',
                type:'bar',
                data:incomeData
            },
            {
                name:'支出',
                type:'bar',
                yAxisIndex: 1,
                data:outData
            },
            {
                name:'净收益',
                type:'line',
                yAxisIndex: 2,
                data:netIncomeData
            }
        ]
    };
};

