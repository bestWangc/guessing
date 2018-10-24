function refreshIframe() {
    var activeItemIndex = $('li.active a.J_menuItem').data('index');
    var iframeName = 'iframe[name="'+activeItemIndex+'"]';
    $(iframeName).attr('src', $(iframeName).attr('src'));
}