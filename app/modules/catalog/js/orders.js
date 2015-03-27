
/**
 * Функционал для кнопки удаления записи DicVal (в меню) при ее редактировании
 */

$(function(){

    $(".remove-order-record").click(function() {
        var $this = this;
        $.SmartMessageBox({
            title : "Перенести заказ в архив?",
            content : "",
            buttons : '[Нет][Да]'
        },function(ButtonPressed) {
            if(ButtonPressed == "Да") {
                $.ajax({
                    url: $($this).attr('href'),
                    type: 'DELETE',
                    dataType: 'json',
                    beforeSend: function(){$($this).elementDisabled(true);},
                    success: function(response, textStatus, xhr){
                        if(response.status == true){
                            //showMessage.constructor('Удаление', response.responseText);
                            //showMessage.smallSuccess();
                            //$($this).parents('tr').fadeOut(500,function(){$(this).remove();});
                            location.href = $($this).attr('data-goto');
                            return false;
                        } else {
                            $($this).elementDisabled(false);
                            showMessage.constructor('Архивирование', 'Возникла ошибка. Обновите страницу и повторите снова.');
                            showMessage.smallError();
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        $($this).elementDisabled(false);
                        showMessage.constructor('Удаление', 'Возникла ошибка. Повторите снова.');
                        showMessage.smallError();
                    }
                });
            }
        });
        return false;
    });
});



/** 
 * Функционал для кнопки удаления записи (в списке)
 */
$(function(){
	
	$(".remove-order-list").click(function(e) {

        e.preventDefault();

		var $this = this;

        $.SmartMessageBox({
			title : "Перенести заказ в архив?",
			content : "",
			buttons : '[Нет][Да]'
		}, function(ButtonPressed) {

			if(ButtonPressed == "Да") {

				$.ajax({
					url: $($this).parent('form').attr('action'),
					type: 'DELETE',
                    dataType: 'json',
					beforeSend: function(){$($this).elementDisabled(true);},
					success: function(response, textStatus, xhr){
						if(response.status == true){
							showMessage.constructor('Архивирование', response.responseText);
							showMessage.smallSuccess();

							//$($this).parents('tr').fadeOut(500,function(){$(this).remove();});
                            $('.dd-item[data-id=' + $($this).parents('.dd-item').attr('data-id') + ']').slideUp();

						} else {
							$($this).elementDisabled(false);
							showMessage.constructor('Архивирование', 'Возникла ошибка. Обновите страницу и повторите снова.');
							showMessage.smallError();
						}
					},
					error: function(xhr, textStatus, errorThrown){
						$($this).elementDisabled(false);
						showMessage.constructor('Архивирование', 'Возникла ошибка. Повторите снова.');
						showMessage.smallError();
					}
				});

			}
		});
		return false;
	});
});


$('.catalog-change-order-status').click(function() {
    $('.catalog-current-order-status').addClass('hidden');
    $('.catalog-order-status-select').removeClass('hidden');
});

$('.catalog-check-value').on('input', function(){
    //alert($(this).val());

    var need_to_correct_price = false;
    var total = 0;
    var regex = /[^\d\.]/gi;

    $('.catalog-check-value').each(function(){
        var value = $(this).val();
        value = value.replace(regex, '');
        value = parseInt(value, 10);
        if (value == 'NaN' || !value)
            value = 0;

        $(this).val(value);

        if (value != $(this).attr('data-original')) {
            need_to_correct_price = true;
        }
    });

    if (need_to_correct_price) {

        $('.catalog-product-block').each(function(){
            var count = $(this).find('.catalog-product-count').val();
            var price = $(this).find('.catalog-product-price').val();
            //alert(count + ' * ' + price);

            count = count.replace(regex, '');
            price = price.replace(regex, '');

            count = parseInt(count, 10);
            price = parseInt(price, 10);

            //alert(typeof count);

            if (count == 'NaN' || !count)
                count = 0;

            if (price == 'NaN' || !price)
                price = 0;

            //alert(count + ' * ' + price);

            total += count * price;
        });

        $('.catalog-new-order-price').text(total);
        $('.catalog-new-order-price').removeClass('hidden');
        $('.catalog-current-order-price').css('text-decoration', 'line-through');
    } else {
        $('.catalog-new-order-price').addClass('hidden');
        $('.catalog-current-order-price').css('text-decoration', 'none');
    }

});