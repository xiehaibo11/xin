$(function () {
    $(document).on('click','.sort_btn', function(){
        var sort_field = $(this).data('field');
        var has_ascending = $(this).hasClass('ascending');
        var has_descending = $(this).hasClass('descending');
        if (!has_ascending && !has_descending) {
            $(this).addClass('ascending');
            $("#sort_field").val(sort_field);
            $("#sort_asc").val(1);
        }
        if (has_ascending) {
            $(this).removeClass('ascending');
            $(this).addClass('descending');
            $("#sort_field").val(sort_field);
            $("#sort_asc").val(0);
        }
        if (has_descending) {
            $(this).removeClass('descending');
            $(this).removeClass('ascending');
            $("#sort_field").val('');
            $("#sort_asc").val('');
        }
        $("#sort_field").closest('form').submit();

    })
    $('th.sort_btn').each(function(){
        var param_field = $("#sort_field").val();
        var param_asc = $("#sort_asc").val();
        var sort_field = $(this).data('field');
        if (sort_field == param_field) {

            if (param_asc && param_asc != 0) {
                $(this).addClass('ascending');
            } else {
                $(this).addClass('descending');
            }
        }
        var html = ' <span class="caret-wrapper"><i class="sort-caret ascending"></i><i class="sort-caret descending"></i></span>';
        $(this).append(html);
    })
})