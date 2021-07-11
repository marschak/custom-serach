jQuery( document ).ready(function( $ ) {

  $.fn.donetyping=function(n,t){t||(t=1e3);var i,u=function(t){i&&(i=null,n(t))};return this.each(function(){var n=$(this);n.on("keyup",function(){i&&clearTimeout(i),i=setTimeout(function(){u(n)},t)}).on("blur",function(){u(n)})}),this};
  
  jQuery('#search input[type="text"]').donetyping(function(){
        var t = $('#search input[type="text"]'),
          val = t.val(),
          form = t.parent();
          var filter = $('#search');
    if(val.length >= 3) {
      $.ajax({
        url:filter.attr('action'),
               type: 'post',
              data: {
                  'action':'search_ajax_search',
                  term: jQuery('#keyword').val() 
              },
      
        beforeSend:function(xhr){
          //$('#datasearch').text('Processing...'); 
        },
        success:function(data){
        
          $('#datasearch').html(data); // insert data
        
        }
      });
      return false;
    } 
    
    });
  });