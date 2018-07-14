// Tab
var _Tab = function() {
  'use strict';

  // Handle Portfolio
  var handlePortfolio = function() {
    // init cubeportfolio
    $('.js__grid-tabs').cubeportfolio({
      filters: '.js__filters-tabs',
      defaultFilter: '.-is-active',
      animationType: 'fadeOut',
      gridAdjustment: 'default',
      displayType: 'default',
      caption: '',
    });
  };

  return {
    init: function() {
      handlePortfolio(); // initial setup for Portfolio
    }
  };
}();

export const Tab = _Tab;