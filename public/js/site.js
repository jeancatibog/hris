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
});

$('.btn-in').on('click', function(){
  $('.ctype').val('In');
});

$('.btn-log').on('click', function(){
  var dt = new Date();
  alert(dt.format('Y-m-d'));
  var date = dt.getFullYear() + "-" + (dt.getMonth()+1) + "-" + dt.getDate();
  var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
  $('.checkdate').val(date);
  $('.checktime').val(time);
});