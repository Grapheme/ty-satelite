var Camry = {};
var Help = {};
Help.Transform = function(transform_value) {
    var pref = ['', '-webkit-', '-ms-'];
    var str = '';
    $.each(pref, function(index, value){
        str += value + 'transform:' + transform_value + ';';
    });
    return str;
}

Camry.answer = function() {
    var link_yes = $('.js-answer-yes'),
        link_no = $('.js-answer-no'),
        block_yes = $('.js-yes-block'),
        block_no = $('.js-no-block');
    var showYes = function() {
        block_yes.addClass('active');
        block_no.addClass('hide');
        setTimeout(function(){
            $('.js-background').fadeOut();
            $('.js-main-screen').fadeOut();
            $('.yes-screen').addClass('active');
        }, 1000);
    }
    var init = function() {
        link_yes.on('click', function(){
            showYes();
        });
    }
    init();
}
Camry.yesScreen = function() {
    var self = this,
        parent = $('.yes-screen'),
        yesCont = $('.js-yes-cont'),
        yesBlock = $('.js-yes-list'),
        wrapper = $('.wrapper'),
        opened = false,
        items_length = $('.js-yes-option').length;
    var csizes = {
        height: 360,
        width: 160
    };
    var osizes = {
        height: 360,
        width: 610
    };
    this.start = function() {
        var new_bottom = wrapper.height()/100 * 10;
        yesCont.attr('style', Help.Transform('translateY(' + new_bottom * (-1) + 'px)'));
    }
    this.kspNav = function() {
        var addKsp = function(id) {
            var this_block = $('.js-yes-option[data-id="' + id + '"]');
            var this_ksp = this_block.find('.js-ksp');
            if(!this_ksp.filter('.opened').length) {
                var this_item = this_ksp.last();
                this_item.addClass('opened');
            } else {
                var this_item = this_ksp.filter('.opened').first().prev();
                this_item.addClass('opened');
            }
            this_item.addClass('active');
            setTimeout(function(){
                this_item.removeClass('active');
            }, 500);
            setNav();
        }
        var deleteKsp = function(id) {
            var this_block = $('.js-yes-option[data-id="' + id + '"]');
            var this_ksp = this_block.find('.js-ksp');
            this_ksp.filter('.opened').first().removeClass('opened');
            setNav();
        }
        var setNav = function() {
            $('.js-yes-option').each(function(){
                if($(this).find('.js-ksp.opened').length) {
                    $(this).find('.js-ksp-remove').addClass('opened');
                    if($(this).find('.js-ksp.opened').length == $(this).find('.js-ksp').length) {
                        $(this).find('.js-ksp-add').removeClass('opened');
                    } else {
                        $(this).find('.js-ksp-add').addClass('opened');
                    }
                } else {
                    $(this).find('.js-ksp-add').addClass('opened');
                    $(this).find('.js-ksp-remove').removeClass('opened');
                }
            });
        }
        $(document).on('click', '.js-ksp-add', function(){
            if(!$(this).hasClass('opened')) return;
            var id = $(this).parents('.js-yes-option').attr('data-id');
            addKsp(id);
            return false;
        });
        $(document).on('click', '.js-ksp-remove', function(){
            if(!$(this).hasClass('opened')) return;
            var id = $(this).parents('.js-yes-option').attr('data-id');
            deleteKsp(id);
            return false;
        });
        setNav();
    }
    this.open = function(tr_id, td_id) {
        var time1 = 0;
        var time2 = 0;
        if(!opened) {
            opened = true;
            time1 = 500;
            time2 = 350;
        }
        $('.empty-option-before, .empty-option-after').removeClass('show');
        var show_empty = (3 - td_id) * 2;
        if(show_empty > 0) {
            for(var i = 0; i < show_empty; i++)
                $('.empty-option-before').eq(i).addClass('show');
        } else {
            show_empty = show_empty * (-1);
            for(var i = 0; i < show_empty; i++)
                $('.empty-option-after').eq(i).addClass('show');
        }
        setTimeout(function(){
            var new_bottom = -(wrapper.height() - (osizes.height)) / 2 + tr_id * osizes.height;
            parent.addClass('opened');
            setTimeout(function(){
                yesCont.attr('style', Help.Transform('translateY(' + new_bottom + 'px)'));
            }, time2);
        }, time1);
        /*var ksp_block = $('.js-ksp[data-tr="' + tr_id + '"][data-td="' + td_id + '"]');
        var option_index = ksp_block.parents('.js-yes-option').index();*/
    }
    this.setEvents = function() {
        $(document).on('click', '.js-ksp', function() {
            self.open($(this).attr('data-tr'), $(this).attr('data-td'));
            return false;
        })
    }
    this.init = function() {
        self.start();
        self.setEvents();
        self.kspNav();
    }
    self.init();
}

$(function(){
    Camry.answer();
    Camry.yesScreen();
});