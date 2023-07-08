/**
 * Common.js
 * @author  TxChrixo
 */
$(function () {

    /**
     * Handles the click event of navigation links with "collapse" behavior.
     * Updates the submenu icon based on the state of the submenu.
     */
    $('.nav-link[data-bs-toggle="collapse"]').on('click', function () {
        // Select the submenu icon within the clicked element
        var submenuIcon = $(this).find('.icon-submenu');

        // Check if the submenu is expanded
        if ($(this).attr('aria-expanded') === 'false') {
            // Submenu is not expanded, change the icon to indicate expansion
            submenuIcon.removeClass('lnr-chevron-down');
            submenuIcon.addClass('lnr-chevron-right');
        } else {
            // Submenu is expanded, change the icon to indicate collapse
            submenuIcon.removeClass('lnr-chevron-right');
            submenuIcon.addClass('lnr-chevron-down');
        }
    });

    $(function() {
        $('.btn-toggle-fullwidth').on('click', function() {
            $('.toggle-icon').toggleClass('rotated');
        });
    });

    $('.btn-toggle-fullwidth').on('click', function () {
        if (!$('body').hasClass('layout-fullwidth')) {
            $('body').addClass('layout-fullwidth');

        } else {
            $('body').removeClass('layout-fullwidth');
            $('body').removeClass('layout-default');
        }

        $(this).find('.lnr').toggleClass('lnr-arrow-left-circle lnr-arrow-right-circle');

        if ($(window).innerWidth() < 1025) {
            if (!$('body').hasClass('offcanvas-active')) {
                $('body').addClass('offcanvas-active');
            } else {
                $('body').removeClass('offcanvas-active');
            }
        }
    });

    $(window).on('load', function () {
        if ($(window).innerWidth() < 1025) {
            $('.btn-toggle-fullwidth').find('.icon-arrows')
                .removeClass('icon-arrows-move-left')
                .addClass('icon-arrows-move-right');
        }
        $('.right-sidebar').css('top', $('.navbar').innerHeight());
        if ($('.has-content-menu').length > 0) {
            $('.navbar + .main-content').css('padding-top', $('.navbar').innerHeight());
        }
        if ($('.main').height() < $('#sidebar-nav').height()) {
            $('.main').css('min-height', $('#sidebar-nav').height());
        }
    });

    /*
    $('.sidebar a[data-bs-toggle="collapse"]').on('click', function () {
        if ($(this).hasClass('collapsed')) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });
    */

    if ($('.sidebar-scroll').length > 0) {
        $('.sidebar-scroll').slimScroll({
            height: '100%',
            wheelStep: 2,
        });
    }

    $('.panel .btn-remove').click(function (e) {

        e.preventDefault();
        $(this).parents('.panel').fadeOut(300, function () {
            $(this).remove();
        });
    });

    var affectedElement = $('.panel-body');

    $('.panel .btn-toggle-collapse').clickToggle(
        function (e) {
            e.preventDefault();

            // if has scroll
            if ($(this).parents('.panel').find('.slimScrollDiv').length > 0) {
                affectedElement = $('.slimScrollDiv');
            }

            $(this).parents('.panel').find(affectedElement).slideUp(300);
            $(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
        },
        function (e) {
            e.preventDefault();

            // if has scroll
            if ($(this).parents('.panel').find('.slimScrollDiv').length > 0) {
                affectedElement = $('.slimScrollDiv');
            }

            $(this).parents('.panel').find(affectedElement).slideDown(300);
            $(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
        }
    );



    if ($('.panel-scrolling').length > 0) {
        $('.panel-scrolling .panel-body').slimScroll({
            height: '430px',
            wheelStep: 2,
        });
    }

    if ($('#panel-scrolling-demo').length > 0) {
        $('#panel-scrolling-demo .panel-body').slimScroll({
            height: '175px',
            wheelStep: 2,
        });
    }

    $('.todo-list input').change(function () {
        if ($(this).prop('checked')) {
            $(this).parents('li').addClass('completed');
        } else {
            $(this).parents('li').removeClass('completed');
        }
    });
});

$.fn.clickToggle = function (f1, f2) {
    return this.each(function () {
        var clicked = false;
        $(this).bind('click', function () {
            if (clicked) {
                clicked = false;
                return f2.apply(this, arguments);
            }

            clicked = true;
            return f1.apply(this, arguments);
        });
    });

}


