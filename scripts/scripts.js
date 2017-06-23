// rows number treatment
$('.rows-number-selector form .submit a').click(function(e){
  // we prevent the default browser behavior
  e.preventDefault();
  // we get the form
  var form = $(this).closest('form');
  // we submit the form
  form.submit();
});

// search bar treatment
$('.search-bar form .submit a').click(function(e){
  // we prevent the default browser behavior
  e.preventDefault();
  // we get the form
  var form = $(this).closest('form');
  // we submit the form
  form.submit();
});

// activation treatment
$('.table-list .switch-btn').click(function () {
  // we get the switch group
  var switch_group = $(this).closest('.switch-group');
  // we add a loading spinner
  $.when(switch_group.find('.switch-action-icon').remove()).then(
    switch_group.append('<span class="switch-action-icon"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></span>')
  );
  // we get the form
  var form = $(this).closest('form');
  // we execute the ajax activation on the form submit
  form.one('submit', function (e) {
    // we prevent the default browser behavior
    e.preventDefault();
    // we get the form object
    var $this = $(this);
    // we do the post request
    $.ajax({
      method: $this.attr('method'),
      url: $this.attr('action'),
      data: {
        _token: $this.find('input[name=_token]').val(),
        active: !$this.find('input[name=active]').is(':checked')
      }
    }).done(function () {
      // we replace the loading spinner by a success icon
      $.when(switch_group.find('.switch-action-icon').remove()).then(
        switch_group.append('<span class="switch-action-icon text-success"><i class="fa fa-check-circle-o" aria-hidden="true"></i></span>')
      );
    }).fail(function (data) {
      // we replace the loading spinner by an error icon
      $.when(switch_group.find('.switch-action-icon').remove()).then(
        switch_group.append('<span class="switch-action-icon text-danger"><i class="fa fa-ban" aria-hidden="true"></i></span>')
      );
      // we set the checkbox at its original value
      window.setTimeout(function () {
        switch_group.find('input.switch').prop('checked', data.responseJSON.active);
      }, 500);
    }).always(function () {
      // we fade out the icon
      switch_group.find('.switch-action-icon').css({
        '-webkit-animation': 'fadeOut 2000ms ease-in',
        '-moz-animation': 'fadeOut 2000ms ease-in',
        '-ms-animation': 'fadeOut 2000ms ease-in',
        '-o-animation': 'fadeOut 2000ms ease-in',
        'animation': 'fadeOut 2000ms ease-in'
      }).promise().done(function () {
        // keep invisible
        $(this).css('opacity', 0);
      });
    });
  });
  // we submit the form
  form.submit();
});