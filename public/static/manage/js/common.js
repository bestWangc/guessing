//根据左侧树刷新右侧tab
function refreshIframe() {
    var activeItemIndex = $('li.active a.J_menuItem').data('index')+1;
    $('iframe[name="iframe'+activeItemIndex+'"]').attr('src', $('iframe[name="iframe'+activeItemIndex+'"]').attr('src'));
}