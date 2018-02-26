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
  $('.js-provinces').empty().append('<option value="-1">Please select your province</option>');
  $('.js-cities').empty().append('<option value="-1">Please select your city</option>');
  loadItems(element, '../api/provinces/', '.js-provinces');
}

function loadCities(element) {
  alert(element);
  $('.js-cities').empty().append('<option value="-1">Please select your city</option>');;
  loadItems(element, '../api/cities/', '.js-cities');
}

function loadEmployees(element) {
  $('.js-employees').empty().append('<option value="-1">Please select your employee</option>');;
  loadItems(element, '../api/employees/', '.js-employees');
}

function registerEvents() {
  $('.js-country').on('change', function() {
    loadProvinces($(this).val());
  });

  $('.js-provinces').on('change', function() {
    loadCities($(this).val());
  });
}

function init() {
  registerEvents();
}

init();