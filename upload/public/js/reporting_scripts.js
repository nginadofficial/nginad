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

        updateStatistic('report_list', $(this).data('action'), datasend);

//        if($(this).data('refresh')){
//            
//            if($(this).data('tab') == 1){
//                getTotalSpend();
//                getImpressionsByUserTld();
//            }
//            else if($(this).data('tab') == 2){
//                getAverageBids();
//            }
//            else if($(this).data('tab') == 3){
//                getSpendByWebomain();
//            }
//            
//            
//        }

        if($(this).data('refresh')){
            
            if($(this).data('tab') == 1){
                updateStatistic('impressions_by_user_tld_area', 'getUserTLDStatistic');
            }
            else if($(this).data('tab') == 2){
                updateStatistic('average_bids_area', 'getAverageIncomingBids');
            }
            else if($(this).data('tab') == 3){
                updateStatistic('spend_by_webdomain_area', 'getOutgoingBidsPerZone');
            }
            else if($(this).data('tab') == 4){
                updateStatistic('spend_per_webdomain', 'getImpressionsPerContractZone');
            }
            else if($(this).data('tab') == 5){
                updateStatistic('total_spend_area', 'getUserImpressionsSpend');
            }
            
            
        }
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
