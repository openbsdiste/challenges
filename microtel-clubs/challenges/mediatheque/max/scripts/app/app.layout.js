$(document).ready(function() {
  $("body").layout({
    defaults: {
      size: "auto"
    },
    north: {
      spacing_open: 1,
      togglerLength_open: 0,
      togglerLength_closed: -1,
      resizable: false,
      slidable: false,
      fxName: "none"
    },
    south: {
      spacing_open: 1,
      togglerLength_open: 0,
      togglerLength_closed: -1,
      resizable: false,
      slidable: false,
      fxName: "none"
    },
    center: {
      spacing_open: 1,
      togglerLength_open: 0,
      togllerLength_closed: -1,
      resizable: false,
      slidable: false,
      fxName: "none"
    }
  })
});
