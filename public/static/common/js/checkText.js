function textChecked(pwd) {
    let num = 0;
    //为0
    if(pwd == 0){
        num++;
    }
    //小写字母
    if(pwd.match(/([a-z])+/)){
        num++;
    }
    //数字
    /*if(pwd.match(/([0-9])+/)){
        num++;
    }*/
    //大写字母
    if(pwd.match(/([A-Z])+/)){
        num++;
    }
    //特殊符号
    if(pwd.match(/[^a-zA-Z0-9]+/)){
        num++;
    }
    return num;
};
