function loadItems(element, path, selectInputClass) {
  var selectedVal = $(element).val();

  // select all
  if (selectedVal == -1) {
    return;
  }

  $.ajax({
    type: 'GET',
    url: path + selectedVal,
    success: function (datas) {
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
  loadItems(element, '../api/province/', '.js-provinces');
}

function loadCities(element) {
  $('.js-cities').empty().append('<option value="">Please select your city</option>');;
  loadItems(element, '../api/city/', '.js-cities');
}

function loadEmployees(element) {
  $('.js-employees').empty().append('<option value="">Please select your employee</option>');;
  loadItems(element, '../api/employees/', '.js-employees');
}

function registerEvents() {
  $('.js-country').on('change', function() {
    loadProvinces(this);
  });

  $('.js-provinces').on('change', function() {
    loadCities(this);
  });
}

function init() {
  registerEvents();
}

init();

$('.btn-in').on('click', function(){
  $('.ctype').val('In');
  // $(this).attr('disabled', 'disabled');
});

$('.btn-out').on('click', function(){
  $('.ctype').val('Out');
  // $('.btn-in').removeAttr('disabled');
});

$('.btn-log').on('click', function(){
  var dt = new Date(),
      date = getToday(),
      time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
  $('.checkdate').val(date);
  $('.checktime').val(time);
});

function getToday() {
  var d = new Date(),
      month = '' + (d.getMonth() + 1),
      day = '' + d.getDate(),
      year = d.getFullYear();

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;
  return [year, month, day].join('-');
}