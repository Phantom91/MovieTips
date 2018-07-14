// Equal Height
var _EqualHeight = function() {
  "use strict";

  // Handle Equal Height
  var handleEqualHeight = function() {
    $(function($) {
      $('.js__form-eqaul-height-v1').responsiveEqualHeightGrid();
      $('.js__tab-eqaul-height-v1').responsiveEqualHeightGrid();
    });
  };

  return {
    init: function() {
      handleEqualHeight(); // initial setup for equal height
    }
  };
}();

export const EqualHeight = _EqualHeight;