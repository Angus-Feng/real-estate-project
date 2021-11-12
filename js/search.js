$(document).ready(function() {

    $('button[name=propSearch]').click(function(e) {

      e.preventDefault();
            minPrice = $("#min-price option:selected").val();
            maxPrice = $('#max-price option:selected').val();
            beds = $('#beds option:selected').val();
            baths = $('#baths option:selected').val();
            keyword = $('#keyword').val();
      window.location.href = '/properties?minPrice=' + minPrice + '&maxPrice=' + maxPrice + '&beds=' + beds + 
                  '&baths=' + baths + '&keyword=' + keyword;
      return false;
        })
  })