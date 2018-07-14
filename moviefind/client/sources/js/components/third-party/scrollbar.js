// Scrollbar
var _Scrollbar = function() {
  'use strict';

  // Handle Scrollbar
  var handleScrollbar = function() {
    $('.js__scrollbar').mCustomScrollbar({
      theme: 'minimal'
    });
  };

  return {
    init: function() {
      handleScrollbar(); // initial setup for Scrollbar
    }
  };
}();

export const Scrollbar = _Scrollbar;