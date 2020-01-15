$(function(){
  $('.emailError').fadeOut(300);

  $( ".contactUsBtn" ).each(function(index) {
      $(this).on("click", function(){
        var dot = $(this).find(".dot");
        dot.addClass('active');
        dot.css('top', event.clientY- this.offsetTop);
        dot.css('left', event.clientX- this.offsetLeft);
        setTimeout(function(){
          dot.removeClass('active');
        }, 400);
      });
  });

});

function showPopup()
{
  $('.popup').addClass('show');
}

function hidePopup()
{
  $('.popup').removeClass('show');
}

function sendRequest()
{
  grecaptcha.reset();
  grecaptcha.execute();
}

function dataDone()
{
  $('.submitBtn').removeClass('default').addClass('loading');
  var captcha = grecaptcha.getResponse();
  var email = $('.emailInput').val();

  $.post('./backend/request.php', {captcha: captcha, email: email, req: true}, function(data){
    if(data != "success")
    {
      $('.emailError').fadeIn(300);
      $('.emailError li b').html(data);
      $('.submitBtn').removeClass('loading').addClass('default');
    }
    else
    {
      $('.emailError').fadeIn(300);
      $('.emailError li b').addClass('success');
      $('.emailError li b').html("Vielen Dank für deine Anfrage!");
      $('.submitBtn').removeClass('loading').addClass('error');
    }

  });
}
