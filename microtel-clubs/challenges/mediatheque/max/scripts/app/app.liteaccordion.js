(function(a) {
  a(window).resize(function() {
    delay(function() {
      a(".liteAccordion").each(function(d, c) {
        var g = c.id;
        var b = jQuery("#" + g + " ol li.slide h2.selected").parent().prevAll().length;
        jQuery("#" + g).liteAccordion().liteAccordion("destroy");
        jQuery("#" + g + " div").css("overflow", "auto");
        Global.setAccordion(g, b)
      });
      a('div[id^="saisie"]').each(function(c, b) {
        a(this).width(a(this).parent().width());
        a(this).height(a(this).parent().height() - a(this).prev().height() - 1)
      })
    }, 500)
  })
})(jQuery);
