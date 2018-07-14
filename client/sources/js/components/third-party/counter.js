// Counter
var _Counter = function() {
  'use strict';

  // Handle Counter
  var handleCounter = function() {
    $('.js__counter').counterUp();
  };

  return {
    init: function() {
      handleCounter(); // initial setup for Counter
    }
  };
}();

export const Counter = _Counter;