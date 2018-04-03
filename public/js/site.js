init();

function init() {
  registerEvents();
}

function registerEvents() {
  $('.js-country').on('change', function() {
    loadProvinces(this);
  });

  $('.js-provinces').on('change', function() {
    loadCities(this);
  });

  $('.btn-in').on('click', function(){
    $('.ctype').val('time_in');
    // $(this).attr('disabled', 'disabled');
  });

  $('.btn-out').on('click', function(){
    $('.ctype').val('time_out');
    // $('.btn-in').removeAttr('disabled');
  });

  $('.btn-log').on('click', function(){
    var dt = new Date(),
        date = getToday(),
        time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
    $('.checkdate').val(date);
    $('.checktime').val(time);
  });

  $('#formsTab a').on('shown.bs.tab', function(event) {
    var form = $(event.target).attr('id');
    $('#file_form').text('File new '+form+' form').attr('href','/forms/create?form='+form);
  });

  /* On file form submittion*/
  $('form.file-form :submit').on('click', function(event){
    event.stopPropagation();
    var param = $(this).attr('id'),
        form = $('form.file-form');
    var id = (param == 'draft' ? 0 : 1);
    form.attr('action', form.attr('action') + '/' + id);
  });

  $('form.edit-form :submit').on('click', function(event){
    event.stopPropagation();
    var param = $(this).attr('id'),
        form = $('form.edit-form');
    var id = (param == 'draft' ? 0 : 1);
    form.attr('action', form.attr('action') + '=' + id);
  });

  /*** Leave Forms ***/
  // Leave form edit on load
  if ($('#is-halfday').is(':checked')) {
    $('.halfday').show().removeAttr('disabled');
  } else {
    $('.halfday').hide().attr('disabled','disabled');
  }
  // If halfday is selected
  $('#is-halfday').on('change', function() {
    if($(this).is(':checked')) {
      $('.halfday').show().removeAttr('disabled');
    } else {
      $('.halfday').hide().attr('disabled','disabled');
    }
  });
  /*** End Leave Forms ***/

  /*** PROCESSING ***/
  $('.process-btn').on('click', function(){
    $(this).hide();
    var form = $('#tk-processing');
    form.submit(function (e) {
      e.preventDefault();
      $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize(),
        success: function(data) {
          $('.progress').show();
          var current_progress = 0;
          var interval = setInterval(function() {
              current_progress += 1;
              $("#dynamic")
              .css("width", current_progress + "%")
              .attr("aria-valuenow", current_progress)
              .text(current_progress + "% Complete");
              if (current_progress >= 100) {
                  clearInterval(interval);

                  $('.process-btn').show();
                  $('.progress').hide();
                  $("#dynamic").attr('aria-valuemax', 0).css('width', '0%').text("");
              }    
          }, 500);
        }
      });
    });
  });

  /* DTR LOGS */
  $('.datepicker').on('changeDate', function(e) {
    $_token = $('meta[name=_token]').attr('content');
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
      type: 'POST',
      url: $('input[name="dtrlog"]').val(),
      dataType: "json",
      data : {
        'date_from': $("#dtrFrom").val(),
        'date_to': $("#dtrTo").val(),
        '_token': $_token
      },
      success: function(response) {
        //success
        if(response.success) {
          $('#employee-dtr tbody').html(response.html);
        } else {
          $('#employee-dtr tbody').html('<tr><td colspan="12"><span>No logs yet</span></td></tr>');
        }
      },
      error: function() {
          alert("No Logs for the selected date");
      }
    });
  });

}

function loadItems(element, path, selectInputClass) {
  var selectedVal = $(element).val();

  // select all
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
    type: 'GET',
    url: path + selectedVal,
    success: function (datas) {console.log(datas);
      if (!datas || datas.length === 0) {
         return;
      }
      for (var  i = 0; i < datas.length; i++) {
        $(selectInputClass).append($('<option>', {
          value: datas[i].id,
          text: datas[i].name
      }));
      }
    },
    error: function (ex) {
    }
  });
}

function loadProvinces(element) {
  $('.js-provinces').empty().append('<option value="">Please select your province</option>');
  $('.js-cities').empty().append('<option value="">Please select your city</option>');
  loadItems(element, '/get-province-list/', '.js-provinces');
}

function loadCities(element) {
  $('.js-cities').empty().append('<option value="">Please select your city</option>');;
  loadItems(element, '/get-city-list/', '.js-cities');
}

/*function loadEmployees(element) {
  $('.js-employees').empty().append('<option value="">Please select your employee</option>');;
  loadItems(element, '../api/employees/', '.js-employees');
}*/

function getToday() {
  var d = new Date(),
      month = '' + (d.getMonth() + 1),
      day = '' + d.getDate(),
      year = d.getFullYear();

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;
  return [year, month, day].join('-');
}

/* Daily Time Records */
$('.dtr-period').on('change', function(){
  var period = $(this).val();

  $.ajax({
    type: 'POST',
    url: '/dtr-list/',
    data: {id: period},
    success: function(response){
      alert("test");
    }
  });
});