function autoScroll(obj) {
    var randNum = Math.floor(Math.random() * 10);
    var randNum2 = Math.floor(Math.random() * 4);
    var randNum3 = Math.floor(Math.random() * 2);
    var account = '';
    if(randNum%2){
        account = getMoblieNum();
    }else{
        account = getRandomString(11);
    }

    var priceArr = ['88','128','188','268'];
    var choseArr = ['瑞雪','丰年'];
    var listr = '<tr>\n' +
        '<td>'+account+'</td>\n' +
        '<td>'+priceArr[randNum2]+'</td>\n' +
        '<td>'+choseArr[randNum3]+'</td>\n' +
        '</tr>'
    $(obj).children('tbody').prepend(listr);
    $(obj).find('tbody tr').each(function(index){
        if(index >10){
            $(this).remove();
        }
    });
};

function getMoblieNum(){
    var numArray = ["139","138","137","136","135","134","159","158","157","150","151","152","188","187","182","183","184","178","130","131","132","156","155","186","185","176","133","153","189","180","181","177"];
    var arraryLength = numArray.length;
    var i = parseInt( Math.random() * arraryLength);
    var phoneNum = numArray[i];

    for ( var j = 0; j < 8; j++){
        if(j >= 0 && j < 4){
            phoneNum += '*';
        }else{
            phoneNum = phoneNum + Math.floor(Math.random() * 10);
        }
    }
    return phoneNum;
};

function getRandomString(len) {
    len = len || 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    var maxPos = $chars.length;
    var randomStr = '';
    for (i = 0; i < len; i++) {
        if(i >4){
            randomStr += '*';
        }else{
            randomStr += $chars.charAt(Math.floor(Math.random() * maxPos));
        }
    }
    return randomStr.toLowerCase();
};