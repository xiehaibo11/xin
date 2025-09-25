function GetUrl() {
    var url = document.location.toString();
    var arrUrl = url.split("//");

    var start = arrUrl[1].indexOf("/");
    var relUrl = arrUrl[1].substring(start);

    if(relUrl.indexOf("?") != -1){
        relUrl = relUrl.split("?")[0];
    }
    return relUrl;
}

$(function() {
    $(document).on('click', '.tab .tab-header .tab-link', function() {
        var $this = $(this);
        var $parent = $this.closest('.tab');
        var $activeTab = $parent.find('.tab-header .tab-link.tab-active');
        if ($this[0] == $activeTab[0]) return;
        $this.siblings().removeClass('tab-active');
        $this.addClass('tab-active');

        $($this.data('to-tab')).siblings().removeClass('tab-active');
        $parent.find('.tab-content').find($this.data('to-tab')).addClass('tab-active');
    });
});

(function($) {
    var Modal = function(options) {
        options = options || {};
        var $modal = options.modal ? $('<div class="' + options.modal + '"></div>') : $('<div class="modal"></div>'); //判断容器
        options.skin && $modal.addClass(options.skin);
        options.title && $('<div class="modal-header">' + options.title + '</div>').appendTo($modal); //判断标题

        options.content && $('<div class="modal-content">' + options.content + '</div>').appendTo($modal); //判断内容

        var $footer = '';
        var btn = [];
        if (options.btn && typeof options.btn == 'object') {
            $footer = $('<div class="modal-footer"></div>');
            for (var p in options.btn) {
                var $button = $('<span class="modal-btn"></span>').appendTo($footer)
                btn.push($button);
                options.btn[p]($button);
            }
            $footer.appendTo($modal);
        }
        var $overlay = '';
        if (!options.overlay) {
            $overlay = $('<div class="modal-overlay"></div>').appendTo('body');
            $('body').css('overflow', 'hidden');
        }
        $modal.appendTo('body');
        $modal.css({
            'marginTop': - Math.round($modal.height() / 2) + 'px',
            'marginLeft': - Math.round($modal.width() / 2 / 1.185) + 'px'
        });
        setTimeout(function() {
            $modal.addClass('modal-in');
            if ($overlay) {
                $overlay.addClass('modal-overlay-visible');
            }
        },0);
        for (var p in btn) {
            btn[p].click(function() {
                Modal.close($modal, $overlay);
            });
        }
        return $modal;
    }
    Modal.close = function($modal, $overlay) {
        $modal.removeClass('modal-in');
        if ($overlay) {
            $overlay.removeClass('modal-overlay-visible');
        }
        setTimeout(function() {
            if ($overlay) {
                $overlay.remove();
                $('body').css('overflow', '');
            }
            $modal.remove();
        }, 400);
    }

    $.modal = Modal;

    $.alert = function(context, title, callbackOk) {
        Modal({
            content: context,
            btn: [function($btn) {
                $btn.text('确认');
            }]
        });
    }
    $.confirm = function(context, options) {
        Modal({
            content: context,
            btn: [function($btn) {
                $btn.text('取消');
                if (options.no) {
                    $btn.click(function() {
                        options.no();
                    });
                }
            }, function($btn) {
                $btn.text('确认');
                if (options.yes) {
                    $btn.click(function() {
                        options.yes();
                    });
                }
            }]
        });
    }
    $.msg = function(context, time) {
        var $modal = Modal({
            content: context,
            overlay: 1,
            skin: 'modal-msg'
        });
        time = time || 2000;
        setTimeout(function() {
            Modal.close($modal);
        }, time)
    }

    $(document).on('click', '*[i-type=back]', function() {        
        history.go(-1); 
        location.reload(); 
    })
})($);
