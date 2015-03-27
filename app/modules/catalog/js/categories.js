
/**
 * Функционал для кнопки удаления записи DicVal (в меню) при ее редактировании
 */

$(function(){

    $(".remove-category-record").click(function() {
        var $this = this;
        $.SmartMessageBox({
            title : "Удалить данную категорию?",
            content : "Восстановить ее будет невозможно",
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
                            showMessage.constructor('Удаление', 'Возникла ошибка. Обновите страницу и повторите снова.');
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
	
	$(".remove-category-list").click(function(e) {

        e.preventDefault();

		var $this = this;

        if ($(this).parents('form').attr('data-can-delete') != 1 || $(this).parents('form').attr('data-products-count') != 0) {
            $.SmartMessageBox({
                title : "Невозможно удалить непустую категорию",
                content : "Перенесите или удалите все вложенные категории и товары - после этого данную категорию можно будет удалить",
                buttons : '[Хорошо]'
            });
            return false;
        }

        $.SmartMessageBox({
			title : "Удалить категорию?",
			content : "Восстановить ее будет невозможно",
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
							showMessage.constructor('Удалить запись', response.responseText);
							showMessage.smallSuccess();

							//$($this).parents('tr').fadeOut(500,function(){$(this).remove();});
                            //$('.dd-item[data-id=' + $($this).parents('.dd-item').attr('data-id') + ']').slideUp();
                            $('.dd-item[data-id=' + $($this).parents('.dd-item').attr('data-id') + ']').remove();
                            show_hide_delete_buttons()

						} else {
							$($this).elementDisabled(false);
							showMessage.constructor('Удалить запись', 'Возникла ошибка. Обновите страницу и повторите снова.');
							showMessage.smallError();
						}
					},
					error: function(xhr, textStatus, errorThrown){
						$($this).elementDisabled(false);
						showMessage.constructor('Удалить запись', 'Возникла ошибка. Повторите снова.');
						showMessage.smallError();
					}
				});

			}
		});
		return false;
	});
});
