var Camry = {};
var Help = {};
var Dictionary = {};
Dictionary.KSP = [
    {
        id: 500,
        name: "Динамика",
        image: "images/tmp/ksp1.jpg",
        ksp: [
            {
                id: 1,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp2.jpg"
            },
            {
                id: 2,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp3.jpg"
            },
            {
                id: 3,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp4.jpg"
            },
            {
                id: 4,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp1.jpg"
            },
        ]
    },
    {
        id: 2,
        name: "Дизайн",
        image: "images/tmp/ksp2.jpg",
        ksp: [
            {
                id: 1,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp3.jpg"
            },
            {
                id: 2,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp4.jpg"
            }
        ]
    },
    {
        id: 3,
        name: "Иновации",
        image: "images/tmp/ksp3.jpg",
        ksp: [
            {
                id: 1,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp1.jpg"
            },
            {
                id: 2,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp2.jpg"
            },
            {
                id: 3,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp3.jpg"
            }
        ]
    },
    {
        id: 4,
        name: "Комфорт",
        image: "images/tmp/ksp4.jpg",
        ksp: [
            {
                id: 1,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp4.jpg"
            },
            {
                id: 2,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp1.jpg"
            }
        ]
    },
    {
        id: 5,
        name: "Экономичность",
        image: "images/tmp/ksp1.jpg",
        ksp: [
            {
                id: 1,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp2.jpg"
            },
            {
                id: 2,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp3.jpg"
            },
            {
                id: 3,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp4.jpg"
            }
        ]
    },
    {
        id: 6,
        name: "Безопасность",
        image: "images/tmp/ksp2.jpg",
        ksp: [
            {
                id: 1,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp1.jpg"
            },
            {
                id: 2,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp2.jpg"
            }
        ]
    },
    {
        id: 7,
        name: "Функциональность",
        image: "images/tmp/ksp3.jpg",
        ksp: [
            {
                id: 1,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp3.jpg"
            },
            {
                id: 2,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp4.jpg"
            },
            {
                id: 3,
                preview: "Новый двигатель 2.0 L с 6-ступенчатой АКПП",
                image: "images/tmp/ksp1.jpg"
            }
        ]
    }
];

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
        $('.js-yes-cont').html(html);
    }
    this.start = function() {
        var new_bottom = -262/16;
        yesCont.attr('style', Help.Transform('translateY(' + new_bottom + 'rem)'));
    }
    this.kspNav = function() {
        var addKsp = function(id) {
            var this_block = $('.js-yes-option[data-id="' + id + '"]'),
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
                            console.log(value);
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
                }, 500);
            }, 500);
            setNav(this_block);
        }
        var deleteKsp = function(id) {
            var this_block = $('.js-yes-option[data-id="' + id + '"]');
            var this_ksp = this_block.find('.js-ksp');
            this_ksp.filter('.opened').first().removeClass('opened');
            setNav(this_block);
        }
        var setNav = function(this_block) {
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
        self.setHtml();
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