$(document).ready(function() {

    // check for cookie
//    var count;
//    var cookiValur = getCookie('santa_show_ni')
//    if(cookiValur){
//        // alert("ima cooki");
//        count = parseInt(cookiValur, 10);
//        count++;
//    }else{
//        // alert('nqma cookie');
//        count = 0;
//    }

    // set cookie
//    function setCookie(cname, cvalue, exdays) {
//        var d = new Date();
//        d.setTime(d.getTime() + (exdays*24*60*60*1000));
//        var expires = "expires="+d.toUTCString();
//        document.cookie = cname + "=" + cvalue + "; " + expires;
//    }
//
//    // read cookie
//    function getCookie(cname) {
//        var name = cname + "=";
//        var ca = document.cookie.split(';');
//        for(var i=0; i<ca.length; i++) {
//            var c = ca[i];
//            while (c.charAt(0)==' ') c = c.substring(1);
//            if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
//        }
//        return "";
//    }


    // show Santa only 3 times per 24h
//    if (cookiValur <= 1) {

//        setCookie('santa_show_ni', count , 1);

        var christmasEl = '\
                <div id="object1" style="z-index:1; height: 52px; width: 70px; position: fixed; top: 180px;">\
                    <a href="#">\
                         <img style="position: absolute; top: 0px;" src="https://sparta.corp.magento.com/dev/support/tools/patchchk/design/image/santa_anim_4_3.gif?v=' + new Date().getTime() + '">\
                    </a>\
                </div>';

        $('body').append(christmasEl);

        var windowWidthChristmas = $(window).width() - 70;
        var animateMe = function(targetElement, speed) {

            $(targetElement).css({left: '0'});
          //  $("body").css({"overflow-x": "hidden"});
            $(targetElement).animate({
                left: windowWidthChristmas
            }, {
                duration: 10000,
                step: function(now, fx) {
                    $(targetElement).css("left", now);


                },
                complete: function() {
                    setTimeout(function() {
                        $(targetElement).remove();
                        //  $("body").css({"overflow-x": "visible"});
                    }, 5000);
                }

            });

        };
        animateMe($('#object1'), 5000);
//    }


});
