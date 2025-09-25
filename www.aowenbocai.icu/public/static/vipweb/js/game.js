$(function () {
    var height = $(window).height() - 200;
    var width = height / 16 * 9;
    $('.game-iframe-sp').css({ 'height': height + 'px', 'width': width + 'px' });
    $('.game-lucky .help').css({ 'height': height + 'px' });
});