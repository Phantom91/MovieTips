// Wow
var _Wow = function() {
  'use strict';

  // Handle Wow
  var handleWow = function() {
    var wow = new WOW({
      mobile: false
  	});
  	wow.init();
  };

  return {
    init: function() {
      handleWow(); // initial setup for Wow
    }
  };
}();

export const Wow = _Wow;