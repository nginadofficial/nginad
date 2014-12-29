var step = 1;
$(document).ready(function() {
    sendFilterData();

    $('.datetimepicker').datetimepicker({
        pickSeconds: false,
    }).on('show', function(ev){
    
        $('#interval_type').val('1');
        $('.datetimepicker input').removeAttr('disabled');

    });

    $('#interval_type').on('change', function() {
        if ($(this).val() == 1) {
            $('.datetimepicker input').removeAttr('disabled');
            $('.step_navigation').hide();
        }
        else {
            $('.datetimepicker input').attr('disabled', 'disabled');
            $('.step_navigation').show();
            step = 1;
        }
    });

    $(document).on('click', '.step_btn', function() {
        step = $(this).data('step');
        $("#interval_setter_btn").click();
        return false;
    });

});


function sendFilterData() {

    $(".report_btn").click(function() {
        $('.step_navigation').html('');
        var datasend = {};
        datasend['interval'] = $('#interval_type').val();
        datasend['step'] = step;
        datasend['refresh'] = $(this).data('refresh');
        datasend['excel'] = $(this).data('excel');
        if (datasend['interval'] == 1) {
            datasend['time_from'] = $('#time_from').val();
            datasend['time_to'] = $('#time_to').val();
            if (datasend['time_from'] == '' || datasend['time_to'] == '') {
                alert('Select interval please');
                return false;
            }
        }
        else {
            $('.step_navigation').append('<a href="#" data-step="' + (step + 1) + '" class="step_btn">Prev 12 Hours</a>');
            if (step > 1) {
                $('.step_navigation').append('<a href="#" data-step="' + (step - 1) + '" class="step_btn">Next 12 Hours</a>');
            }
        }

        if($(this).data('excel')){
            var method = "";
            var url = '/report/' + $(this).data('action') + 'Excel/?'+ $.param( datasend, true );
            window.location = url;
            return false;
        }
        
        updateStatistic('report_list', $(this).data('action'), datasend);

        return false;
    });
}

function updateStatistic(target, method, datasend){
        url = '/report/' + method;
        $.ajax({
            type: "GET",
            dataType: "JSON",
            data: datasend,
            url: url,
            success: function(data) {
                $('#' + target + ' tbody').html('');
                $.each(data['data'], function() {
                    var html = '<tr>';
                    $.map($(this)[0], function(value, index) {
                        html += ('<td>' + value + '</td>');
                    });
                    html += '</tr>';
                    $('#' + target + ' tbody').append(html);
                });
                var html = '<tr>';
                for (i in data['totals']) {
                	 html += ('<td>' + data['totals'][i] + '</td>');
                }
                html += '</tr>';
                $('#' + target + ' tbody').append(html);
            }
        });
    
    
}
