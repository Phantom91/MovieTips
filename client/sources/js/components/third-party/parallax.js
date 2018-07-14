// Parallax
var _Parallax = function() {
  'use strict';

  // Handle Parallax
  var handleParallax = function() {
    $('.js__parallax-window').parallax("50%", 0.1);
  };

  return {
    init: function() {
      handleParallax(); // initial setup for Parallax
    }
  };
}();

export const Parallax = _Parallax;