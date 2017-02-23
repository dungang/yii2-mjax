/**
 * Created by dungang on 2017/2/22.
 */
+function ($) {

    if (!$.fn.mjaxInstance) {
        var modal = $('<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mjax"></div>');
        var modalDoc = $('<div class="modal-dialog" role="document"></div>');
        var modalContent = $('<div class="modal-content"></div>');
        var modalHeader = $('<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        var modalHeaderTitle = $('<h4 class="modal-title" id="myModalLabel">Modal title</h4>');
        var modalBody = $('<div class="modal-body"></div>');
        var modalFooter = $('<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>');

        modal.append(modalDoc);
        modalDoc.append(modalContent);
        modalContent.append(modalHeader).append(modalBody);
        //modalContent.append(modalFooter);
        modalHeader.append(modalHeaderTitle);
        $('body').append(modal);

        $.fn.mjaxInstance = {
            modal:modal,
            modalDoc:modalDoc,
            modalContent:modalContent,
            modalHeader:modalHeader,
            modalHeaderTitle:modalHeaderTitle,
            modalBody:modalBody,
            modalFooter:modalFooter
        };
    }

    $.fn.mjax = function (options) {
        var opts = $.extend({},$.fn.mjax.DEFAULTS,options);
        var instance = $.fn.mjaxInstance;
        return this.each(function () {
            var _this = $(this);
            _this.click(function (e) {
                e.preventDefault();
                instance.modalHeaderTitle.html(_this.html());
                instance.modalBody.load(_this.attr('href'),function () {
                    instance.modal.modal({
                        backdrop:false  //静态模态框，即单鼠标点击模态框的外围时，模态框不关闭。
                    });
                    instance.modal.on('hidden.bs.modal',function () {
                        //如果关闭模态框，则刷新当前页面
                        if( opts.refresh ) window.location.reload();
                    });
                    //如果有表单，则绑定ajax提交表单
                    instance.modalBody.find('form').on('beforeSubmit',function (event) {
                        //通知yii.activeForm 不要提交表单，由本对象通过ajax的方式提交表单
                        event.result = false;
                        $(this).ajaxSubmit({
                            success:function (response) {
                                //将表单的结果页面覆盖模态框Body
                                instance.modalBody.html(response);
                            }
                        });
                    });
                });
            });
        });
    };
    $.fn.mjax.DEFAULTS = {
        refresh:false //关闭模态框的时候是否刷新当前页面
    };
}(jQuery);