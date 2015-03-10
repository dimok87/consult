$(document).ready(function () {
   if ($('#distribution').length) {
      $('#distribution').sortable({
         update: function() {
            var params = $(this).sortable('serialize');
            var url = $(this).attr('data-href');
            $.get(url + '?' + params);
            var i = 0;
            $(this).find('.list-group-item').each(function() {
               $(this).attr('id', 'line_' + i);
               i++;
            });
         }
      });
      $("#distribution").disableSelection();
   }
});