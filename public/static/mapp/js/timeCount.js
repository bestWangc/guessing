//倒计时
window.setInterval(function () {
    //倒计时总秒数量
    var now_time = new Date();
    var time_value = [];
    var specified_time = [10,20,30,40,50,60];
    specified_time.forEach(function (value,index) {
        if(value-now_time.getMinutes() > 0){
            time_value.push(value-now_time.getMinutes());
        }
    });
    var intDiffs = Math.min.apply(null, time_value);
    intDiffs = intDiffs*60-now_time.getSeconds();
    intDiff = parseInt(intDiffs);

    if (intDiff > 0) {
        minute = Math.floor(intDiff / 60);
        second = intDiff-(minute*60);
    }
    if (minute <= 9) minute = '0' + minute;
    if (second <= 9) second = '0' + second;
    $('#minute_show').html('<s></s>' + minute + '分');
    $('#second_show').html('<s></s>' + second + '秒');
    $('#minute_show1').html('<s></s>'+minute+'分');
    $('#second_show1').html('<s></s>'+second+'秒');
    $('#minute_show5').html('<s></s>' + minute + '分');
    $('#second_show5').html('<s></s>' + second + '秒');
    intDiff--;
}, 1000);