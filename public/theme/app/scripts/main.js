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
Camry.yesScreen = function() {
    if(!$('.js-yes-screen').length) return;
    var self = this,
        parent = $('.js-yes-screen'),
        yesCont = parent.find('.js-yes-cont'),
        yesBlock = parent.find('.js-yes-list'),
        wrapper = $('.wrapper'),
        opened = false,
        active_tr,
        active_td;
    var csizes = {
            height: 0,
            width: 181
        };
    var osizes = {
            height: 360,
            width: 610
        };
    this.setHtml = function() {
        var obj = Dictionary.KSP;
        var dataid = 0;
        var html = '';
        for(var i = 0; i < obj.length; i++) {
            html += '<div class="yes-screen__option empty-option-before"></div>';
        }
        $.each(obj, function(index, value){
            var options_str = '';
            var ksp_step = value.ksp.length - 1;
            $.each(value.ksp, function(ksp_index, ksp_value){
                options_str += '\
                    <div data-td="' + dataid + '" data-tr="' + ksp_step + '" class="list__other-item js-ksp" data-option-id="' + value.id + '" data-ksp-id="' + ksp_value.id + '">\
                        <div class="other-item__opened">\
                            <div class="opened__top-title">' + value.name + '</div>\
                            <div class="opened__bottom">\
                                <div class="opened__title">' + ksp_value.title + '</div>\
                                <div class="opened__desc">' + ksp_value.desc + '</div>\
                            </div>\
                        </div>\
                      <div class="other-item__content">\
                        <div class="other-item__background" style="background-image: url(' + ksp_value.image + ');">\
                          <div class="background__text">' + ksp_value.preview + '</div>\
                        </div>\
                      </div>\
                      <div class="other-item__line"></div>\
                    </div>';
                ksp_step--;
            });
            html += '<div data-id="' + dataid + '" class="yes-screen__option js-yes-option">\
              <div class="yes-screen__cont">\
                <div class="list__other-items">\
                  ' + options_str + '\
                </div>\
                <div class="list__main-item" style="background-image: url(' + value.image + ');"></div>\
                <div class="list__option-name">' + value.name + '</div>\
                <div class="list__nav"><a href="#" class="nav__minus js-ksp-remove"><span>-</span></a><a href="#" class="nav__plus js-ksp-add opened"><span>+</span></a></div>\
              </div>\
            </div>';
            dataid++;
        });
        for(var i = 0; i < obj.length; i++) {
            html += '<div class="yes-screen__option empty-option-after"></div>';
        }
        $('.js-yes-screen .js-yes-cont').html(html);
        if($.cookie('ksps')) {
            var kspsObj = JSON.parse($.cookie('ksps'));
            $.each(kspsObj.items, function(index, value){
                $('.js-ksp[data-ksp-id="' + value + '"]').addClass('opened');
            });
        }
    }
    this.start = function() {
        var new_bottom = -262/16;
        yesCont.attr('style', Help.Transform('translateY(' + new_bottom + 'rem)'));
    }
    this.kspNav = function() {
        var addKsp = function(id) {
            var this_block = $('.js-yes-screen .js-yes-option[data-id="' + id + '"]'),
                this_ksp = this_block.find('.js-ksp');
            if(!this_ksp.filter('.opened').length) {
                var this_item = this_ksp.last();
                this_item.addClass('opened');
            } else {
                var this_item = this_ksp.filter('.opened').first().prev();
                this_item.addClass('opened');
            }
            this_item.addClass('active');
            var option_id = this_ksp.attr('data-option-id');
            var ksp_id = this_ksp.filter('.opened').first().attr('data-ksp-id');
            var background_new;
            $.each(Dictionary.KSP, function(index, value){
                if(value.id == option_id) {
                    $.each(value.ksp, function(index, value){
                        if(value.id == ksp_id) {
                            //console.log(value);
                            background_new = value.image;
                        }
                    });
                }
            });
            var background_block = $('<div class="main-item__background" style="background-image: url(' + background_new + ');"></div>');
            this_block.find('.list__main-item').append(background_block);
            setTimeout(function(){
                this_item.removeClass('active');
                setTimeout(function(){
                    background_block.addClass('active');
                }, 1000);
            }, 1000);
            setNav(this_block);
        }
        var deleteKsp = function(id) {
            var this_block = $('.js-yes-screen .js-yes-option[data-id="' + id + '"]');
            var this_ksp = this_block.find('.js-ksp');
            var this_background = this_block.find('.main-item__background').last().removeClass('active');
            this_ksp.filter('.opened').first().removeClass('opened');
            setTimeout(function(){
                this_background.remove();
            }, 500);
            setNav(this_block);
        }
        var emptyOption = function(option) {
            option.find('.js-ksp-remove').removeClass('opened');
            option.addClass('empty-option');
        }
        var setNav = function(this_block) {
            var kspObj = {};
            var kspArray = [];
            $('.js-ksp.opened').each(function(){
                var this_id = $(this).attr('data-ksp-id');
                kspArray.push(this_id);
            });
            kspObj.items = kspArray;
            var kspJSON = JSON.stringify(kspObj);
            $.cookie('ksps', kspJSON);
            $('#yessubmit').find('input').val(kspJSON);
            $('.js-yes-screen .js-yes-option').each(function(){
                var this_option = $(this);
                this_option.removeClass('empty-option');
                if($(this).find('.js-ksp.opened').length) {
                    $(this).find('.js-ksp-remove').addClass('opened');
                    if($(this).find('.js-ksp.opened').length == $(this).find('.js-ksp').length) {
                        $(this).find('.js-ksp-add').removeClass('opened');
                    } else {
                        $(this).find('.js-ksp-add').addClass('opened');
                    }
                } else {
                    $(this).find('.js-ksp-add').addClass('opened');
                    emptyOption(this_option);
                }
            });
        }
        $(document).on('click', '.js-ksp-add', function(){
            if(!$(this).hasClass('opened')) return false;
            var id = $(this).parents('.js-yes-option').attr('data-id');
            addKsp(id);
            return false;
        });
        $(document).on('click', '.js-ksp-remove', function(){
            if(!$(this).hasClass('opened')) return false;
            var id = $(this).parents('.js-yes-option').attr('data-id');
            deleteKsp(id);
            return false;
        });
        setNav();
    }
    this.navigation = {
        left: function() {
            var this_td = parseInt(active_td) - 1;
            self.open(0, this_td);
        },
        right: function() {
            var this_td = parseInt(active_td) + 1;
            self.open(0, this_td);
        },
        top: function() {
            var this_tr = parseInt(active_tr) + 1;
            self.open(this_tr, active_td);
        },
        bottom: function() {
            var this_tr = parseInt(active_tr) - 1;
            self.open(this_tr, active_td);
        }
    };
    this.close = function() {
        self.start();
        $('.yes-overlay').removeClass('active');
        $('.js-yes-screen .empty-option-before, .empty-option-after').removeClass('show');
        parent.removeClass('opened');
        $('.js-yes-option').siblings().attr('style', Help.Transform('translateY(0)'));
        $('.yes-main-btn').removeClass('to-back');
        $('.yes-navigation').fadeOut().removeClass('opened');
        $('.footer').addClass('to-front');
        opened = false;
    }
    this.open = function(tr_id, td_id) {
        var time1 = 0;
        var time2 = 0;
        var ksp_block = $('.js-ksp[data-tr="' + tr_id + '"][data-td="' + td_id + '"]');
        if(!opened) {
            opened = true;
            time1 = 500;
            time2 = 350;
        } else {
            if(!ksp_block.length || !ksp_block.hasClass('nav_active')) {
                return false;
            }
        }
        var old_tr = active_tr;
        var old_td = active_td;
        active_tr = tr_id;
        active_td = td_id;
        $('.footer').removeClass('to-front');
        $('.yes-navigation').fadeIn().addClass('opened');
        $('.yes-main-btn').addClass('to-back');
        var options_visible = $('.js-yes-option').not('.empty-option');
        var this_option = ksp_block.parents('.js-yes-option');
        var options_after = this_option.nextAll('.js-yes-option').not('.empty-option').length;
        var options_before = this_option.prevAll('.js-yes-option').not('.empty-option').length;
        $('.js-yes-screen .empty-option-before, .empty-option-after').removeClass('show');
        var show_empty_first = (3 - td_id) * 2;
        if(show_empty_first > 0) {
            for(var i = 0; i < show_empty_first; i++)
                $('.js-yes-screen .empty-option-before').eq(i).addClass('show');
        } else {
            show_empty_first = show_empty_first * (-1);
            for(var i = 0; i < show_empty_first; i++)
                $('.js-yes-screen .empty-option-after').eq(i).addClass('show');
        }
        $('.js-ksp').removeClass('nav_active');
        ksp_block.addClass('nav_active');
        var next_td = parseInt(td_id) + 1;
        var prev_td = parseInt(td_id) - 1;
        var next_tr = parseInt(tr_id) + 1;
        var prev_tr = parseInt(tr_id) - 1;
        this_option.nextAll('.js-yes-option').not('.empty-option').eq(0).find('.js-ksp.opened[data-tr="0"]').addClass('nav_active');
        this_option.prevAll('.js-yes-option').not('.empty-option').eq(0).find('.js-ksp.opened[data-tr="0"]').addClass('nav_active');
        $('.js-ksp.opened[data-td="' + td_id + '"][data-tr="' + next_tr + '"]').addClass('nav_active');
        $('.js-ksp.opened[data-td="' + td_id + '"][data-tr="' + prev_tr + '"]').addClass('nav_active');
        $('.js-ksp').removeClass('active-ksp');
        ksp_block.addClass('active-ksp');
        setTimeout(function(){
            $('.js-yes-screen .empty-option-before, .empty-option-after').removeClass('show');
            var show_empty = options_before - options_after;
            if(show_empty > 0) {
                for(var i = 0; i < show_empty; i++)
                    $('.js-yes-screen .empty-option-after').eq(i).addClass('show');
            } else {
                show_empty = show_empty * (-1);
                for(var i = 0; i < show_empty; i++)
                    $('.js-yes-screen .empty-option-before').eq(i).addClass('show');
            } 
            var new_bottom = - $(document).height() + $(document).height()/2 + osizes.height/2;
            var option_bottom = $('.ksp-opened-sample').height()*tr_id;
            parent.addClass('opened');
            $('.yes-overlay').addClass('active');
            setTimeout(function(){
                yesCont.attr('style', Help.Transform('translateY(' + new_bottom + 'px)'));
                this_option.attr('style', Help.Transform('translateY(' + option_bottom + 'px)'));
                setTimeout(function(){
                    this_option.siblings().attr('style', Help.Transform('translateY(0)'));
                }, 500);
            }, time2);
        }, time1);
        self.setNav();
    }
    this.setNav = function() {
        var this_ksp = $('.js-ksp[data-tr="' + active_tr + '"][data-td="' + active_td + '"]');
        var this_option = this_ksp.parents('.js-yes-option');
        if(this_option.nextAll('.js-yes-option').not('.empty-option').eq(0).find('.js-ksp.opened[data-tr="0"]').length) {
            $('.nav__right').removeClass('disabled');
        } else {
            $('.nav__right').addClass('disabled');
        }
        if(this_option.prevAll('.js-yes-option').not('.empty-option').eq(0).find('.js-ksp.opened[data-tr="0"]').length) {
            $('.nav__left').removeClass('disabled');
        } else {
            $('.nav__left').addClass('disabled');
        }
        var next_tr = parseInt(active_tr) + 1;
        var prev_tr = parseInt(active_tr) - 1;
        if(this_option.find('.js-ksp.opened[data-tr="' + next_tr + '"]').length) {
            $('.nav__top').removeClass('disabled');
        } else {
            $('.nav__top').addClass('disabled');
        }
        if(this_option.find('.js-ksp.opened[data-tr="' + prev_tr + '"]').length) {
            $('.nav__bottom').removeClass('disabled');
        } else {
            $('.nav__bottom').addClass('disabled');
        }
    }
    this.setEvents = function() {
        $(document).on('click', '.js-ksp', function(e) {
            if(opened && !$(this).hasClass('nav_active')) {
                self.close();
                return false;
            }
            self.open($(this).attr('data-tr'), $(this).attr('data-td'));
            return false;
        });
        $(document).on('click', '.yes-overlay', function() {
            self.close();
            return false;
        });
        $(document).on('click', '.js-yes-list', function(e){
            if($(e.target).hasClass('js-yes-list') || $(e.target).hasClass('js-yes-cont')) {
                self.close();
            }
            return false;
        });
        $(document).on('click', '.nav__right', function() {
            self.navigation.right();
            return false;
        });
        $(document).on('click', '.nav__bottom', function() {
            self.navigation.bottom();
            return false;
        });
        $(document).on('click', '.nav__top', function() {
            self.navigation.top();
            return false;
        });
        $(document).on('click', '.nav__left', function() {
            self.navigation.left();
            return false;
        });
        $(document).on('keydown', function(e){ 
            var code = e.which;
            if(code == 40) {
                e.preventDefault();
                self.navigation.bottom();
            }
        });
        $(document).on('keydown', function(e){ 
            var code = e.which;
            if(code == 38) {
                e.preventDefault();
                self.navigation.top();
            }
        });
        $(document).on('keydown', function(e){ 
            var code = e.which;
            if(code == 37) {
                e.preventDefault();
                self.navigation.left();
            }
        });
        $(document).on('keydown', function(e){ 
            var code = e.which;
            if(code == 39) {
                e.preventDefault();
                self.navigation.right();
            }
        });
        $(document).on('click', '#yessubmit [type="submit"]', function(e){
            e.preventDefault();
            var form = $(this).parents('form');
            console.log(form.find('input').val());
            form.submit();
            return false;
        });
    }
    this.init = function() {
        self.setHtml();
        self.start();
        self.setEvents();
        self.kspNav();
        $('.footer').addClass('to-front');
    }
    self.init();
}

Camry.noScreen = function() {
    if(!$('.js-no-screen').length) return;
    var self = this,
        parent = $('.js-no-screen'),
        yesCont = parent.find('.js-yes-cont'),
        yesBlock = parent.find('.js-yes-list'),
        wrapper = $('.wrapper'),
        opened = false;
    this.setHtml = function() {
        var obj = Dictionary.NoScreen;
        var dataid = 0;
        var html = '';
        for(var i = 0; i < obj.length; i++) {
            html += '<div class="yes-screen__option empty-option-before"></div>';
        }
        $.each(obj, function(index, value){
            var options_str = '';
            var ksp_step = value.ksp.length - 1;
            $.each(value.ksp, function(ksp_index, ksp_value){
                options_str += '\
                    <div data-td="' + dataid + '" data-tr="' + ksp_step + '" class="list__other-item js-ksp" data-option-id="' + value.id + '" data-ksp-id="' + ksp_value.id + '">\
                      <div class="other-item__line"></div>\
                    </div>';
                ksp_step--;
            });
            html += '<div data-option-id="' + value.id + '" data-id="' + dataid + '" class="yes-screen__option js-yes-option">\
              <div class="yes-screen__cont">\
                <div class="list__other-items">\
                  ' + options_str + '\
                </div>\
                <div class="list__option-name">' + value.name + '</div>\
                <div class="list__nav"><a href="#" class="nav__minus js-ksp-remove"><span>-</span></a><a href="#" class="nav__plus js-ksp-add opened"><span>+</span></a></div>\
              </div>\
            </div>';
            dataid++;
        });
        for(var i = 0; i < obj.length; i++) {
            html += '<div class="yes-screen__option empty-option-after"></div>';
        }
        $('.js-no-screen .js-yes-cont').html(html);
    }
    this.start = function() {
        var new_bottom = -262/16;
        yesCont.attr('style', Help.Transform('translateY(' + new_bottom + 'rem)'));
    }
    this.kspNav = function() {
        var addKsp = function(id) {
            var this_block = $('.js-no-screen .js-yes-option[data-id="' + id + '"]'),
                this_ksp = this_block.find('.js-ksp');
            if(!this_ksp.filter('.opened').length) {
                var this_item = this_ksp.last();
                this_item.addClass('opened');
            } else {
                var this_item = this_ksp.filter('.opened').first().prev();
                this_item.addClass('opened');
            }
            setNav(this_block);
        }
        var deleteKsp = function(id) {
            var this_block = $('.js-no-screen .js-yes-option[data-id="' + id + '"]');
            var this_ksp = this_block.find('.js-ksp');
            this_ksp.filter('.opened').first().removeClass('opened');
            setNav(this_block);
        }
        var setNav = function(this_block) {
            var grades = {};
            $('.js-no-screen .js-yes-option').each(function(){
                var this_id = $(this).attr('data-option-id');
                var this_grade = $(this).find('.js-ksp.opened').length;
                grades[this_id] = this_grade;
            });
            var JSONgrades = JSON.stringify(grades);
            $('#nosubmit input').val(JSONgrades);
            if($('.js-no-screen .js-ksp').hasClass('opened')){
                $('.js-no-submit').removeAttr('disabled');
                $('.js-no-submit').removeAttr('onclick');
            } else {
                $('.js-no-submit').attr('onclick', 'return false;');
                $('.js-no-submit').attr('disabled', 'disabled');
            }
            $('.js-no-screen .js-yes-option').each(function(){
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
            if(!$(this).hasClass('opened')) return false;
            var id = $(this).parents('.js-yes-option').attr('data-id');
            addKsp(id);
            return false;
        });
        $(document).on('click', '.js-ksp-remove', function(){
            if(!$(this).hasClass('opened')) return false;
            var id = $(this).parents('.js-yes-option').attr('data-id');
            deleteKsp(id);
            return false;
        });
        $(document).on('click', '#nosubmit [type="submit"]', function(e){
            e.preventDefault();
            var form = $(this).parents('form');
            console.log(form.find('input').val());
            form.submit();
            return false;
        });
        setNav();
    }
    this.init = function() {
        self.setHtml();
        self.start();
        self.kspNav();
    }
    self.init();
}
Camry.Carousel = function() {
    $('.js-carousel').jcarousel();
    $('.js-carousel-prev').on('jcarouselcontrol:active', function() {
        $(this).removeClass('inactive');
    }).on('jcarouselcontrol:inactive', function() {
        $(this).addClass('inactive');
    }).jcarouselControl({
        target: '-=1'
    });

    $('.js-carousel-next').on('jcarouselcontrol:active', function() {
        $(this).removeClass('inactive');
    }).on('jcarouselcontrol:inactive', function() {
        $(this).addClass('inactive');
    }).jcarouselControl({
        target: '+=1'
    });
}
Camry.KSPs = function() {
    var item_block = $('.item__block');
    item_block.on('mouseover', function(e){
        if($(e.target).hasClass('item__hover') || $(e.target).parents('.item__hover').length) return false;
        $(this).find('.item__hover').fadeIn(250);
    }).on('mouseout', function(){
        $(this).find('.item__hover').hide();
    });
    $('.item__hover').on('mouseover', function(){
        $(this).hide();
    });
    $('.list__item-small').each(function(){
        var this_offset = $(this).find('.item__block').eq(1).offset().left - $(document).width()/2;
        if(this_offset < 0) {
            $(this).addClass('hover-right');
        }
    });
}
Camry.ShowMobile = function() {
    var md = new MobileDetect(window.navigator.userAgent);
    if((md.mobile() || md.phone() || md.tablet()) && $.cookie('ShowSite') != 1) {
        $('.js-mobile-screen').show();
        $('.js-content').hide();
        $('.js-show-site').on('click', function(){
            $.cookie('ShowSite', 1);
            window.location.href = window.location.href;
            return false;
        });
        throw new Error("This site isn't for mobile devices");
    }
}
Camry.Validate = function(form, callback) {
    function validateEmail(email) {
        var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        return re.test(email);
    }
    form.on('submit', function(e){
        e.preventDefault();
        var validate = true;
        var message = 'Сообщение не отправленно.';
        if(form.find('[name="name"]').val().length == 0) {
            form.find('[name="name"]').addClass('input-error');
            message += ' Введите ФИО.';
            validate = false;
        } else {
            form.find('[name="name"]').removeClass('input-error');
        }
        if(!validateEmail(form.find('[name="email"]').val())) {
            form.find('[name="email"]').addClass('input-error');
            message += ' Поле e-mail введено некорректно.';
            validate = false;
        } else {
            form.find('[name="email"]').removeClass('input-error');
        }
        if(form.find('[name="message"]').val().length == 0) {
            form.find('[name="message"]').addClass('input-error');
            message += ' Введите сообщение.';
            validate = false;
        } else {
            form.find('[name="message"]').removeClass('input-error');
        }
        if(validate) {
            form.find('.js-form-errors').text('');
            callback(form);
        } else {
            form.find('.js-form-errors').text(message);
        }
        return false;
    });
    $('.js-show-form').on('click', function(){
        $('#choose').slideUp();
        $('#form').slideDown();
        return false;
    });
}

$(function(){
    Camry.ShowMobile();
    Camry.Validate($('#why-form'), function(form){
        var response_cont = $('.js-form-final');
        var options = { 
            beforeSubmit: function(){
                $(form).find('[type="submit"]').addClass('loading')
                    .attr('disabled', 'disabled');
            }, 
            success: function(data){
                form.slideUp();
                response_cont.slideDown();
                $(form).find('[type="submit"]').removeClass('loading')
                    .removeAttr('disabled');
            },
            error: function(data) {
                $(form).find('[type="submit"]').removeClass('loading')
                    .removeAttr('disabled');
            }
        };
        $(form).ajaxSubmit(options);
    });
    Camry.KSPs();
    Camry.Carousel();
    Camry.yesScreen();
    Camry.noScreen();
    autosize(document.querySelectorAll('textarea'));
    $('.yes-overlay').blurjs({
        source: '.yes-screen-back .main-layer',
        radius: 175
    });
    $('#parallax-scene').parallax({
      limitY: 0.1
    });
});