var popup =  function(){
    var d = $(this);
    d.datepicker()
    .on('changeDate', function(ev){
        // do what you want here
        d.datepicker('hide');
    });
};