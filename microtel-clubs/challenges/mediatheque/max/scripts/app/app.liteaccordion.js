(function ($) {
    $(window).resize (function () {
        delay (function () {
            $('.liteAccordion').each (function (e, f) {
                var id=f.id;
                var actif = jQuery ('#' + id + ' ol li.slide h2.selected').parent().prevAll().length;
                
                jQuery ("#" + id).liteAccordion ().liteAccordion ('destroy');
                jQuery ("#" + id + " div").css ('overflow', 'auto');
                Global.setAccordion (id, actif);
            });
            $('div[id^="saisie"]').each (function (e, f) {
                $(this).width ($(this).parent ().width ());
                $(this).height ($(this).parent ().height () - $(this).prev ().height () - 1);
            });
        }, 500);
    });
}) (jQuery);