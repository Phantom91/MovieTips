// Progress Bar
var _ProgressBar = function() {
  'use strict';

  // Handle Progress Bar
  var handleProgressBar = function() {
    $(document).ready(function() {
      $('.progress').each(function() {
        $(this).appear(function() {
          $(this).animate({
            opacity: 1,
            left: '0'
          }, 800);
          var w = $(this).find('.progress-bar').attr('data-width');
          var h = $(this).find('.progress-bar').attr('data-height');
          $(this).find('.progress-bar').animate({
            width: w + '%',
            height: h + '%'
          }, 100, 'linear');
        });
      });
    });
  };

  return {
    init: function() {
      handleProgressBar(); // initial setup for Progress Bar
    }
  };
}();

export const ProgressBar = _ProgressBar;