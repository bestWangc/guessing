$(function () {
    $.ajax({
        url : '/manage/income/getChartsInfo',
        type : 'POST',
        dataType : 'json',
        success : function (res) {
            res = JSON.parse(res);
            console.log(res);
            var xAxisData =[],incomeData = [];
            if(!!res.status){
                xAxisData = res.data.xAxisData;
                incomeData = res.data.countAll;
                $('.yesBonus').html(res.data.yesBonus);
                $('.monthBonus').html(res.data.monthBonus);
            }
            var myChart = echarts.init(document.getElementById("incomeChart"));
            var option = getOption(xAxisData,incomeData);
            myChart.setOption(option);
            window.onresize = myChart.resize;

        },
        error : function (res) {
            console.log(res);
        }

    });
    var getOption = function (xAxisData,incomeData) {
        return {
            title : {
                text: '收入统计图'
            },
            tooltip : {
                trigger: 'axis'
            },
            legend: {
                data:['收入金额']
            },
            grid:{
                x:30,
                x2:40,
                y2:24
            },
            // calculable : true,
            xAxis : [
                {
                    type : 'category',
                    data : xAxisData
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'收入金额',
                    type:'bar',
                    itemStyle:{
                        normal:{
                            color:'#0ae',
                        }
                    },
                    markLine : {
                        data : [
                            {type : 'average', name : '平均值'}
                        ]
                    },
                    data : incomeData
                }
            ]
        };
    };

});
