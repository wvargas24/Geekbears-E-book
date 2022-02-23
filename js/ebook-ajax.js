jQuery(function($){
    var maincontent = $('#container-grid-ebook');
    var link = $('ul.pgafu-filter li a');
    var loadmore = $('#load_more_button');

    link.click(function (e) {
        e.preventDefault();
        var id = $(this).attr("id");
        link.removeClass('active');
        link.parent().removeClass('pgafu-active-filtr');
        $(this).addClass('active');
        $(this).parent().addClass('pgafu-active-filtr');
        //link.css("border-bottom", "1px solid #f5693d");
        //$(this).css( "border-bottom", "2px solid #f5693d" );
        var name = $(this).text();
        //console.log("Category: "+name+" Id: "+id+" ajax_object.ajaxurl: "+ajax_object.ajaxurl);

        if( id != 'all-items'){            
            $.post(
                ajax_object.ajaxurl, {
                action: 'load_ebook',
                term_id: id,
                name: name
            }, function(data) {
                maincontent.animate({opacity:0.5});
                maincontent.html(data); 
                maincontent.animate({opacity:1});
            });
        }else{
            $.post(
                ajax_object.ajaxurl, {
                action: 'load_all_ebook',
            }, function(data) {
                maincontent.animate({opacity:0.5});
                maincontent.html(data); 
                maincontent.animate({opacity:1});
            });
        }        
        

    });

    loadmore.click(function (e) {
        e.preventDefault();    
        //console.log('click in the button'); 
        var paged =  Number($('#paged').attr('value'))+Number(1);
        //console.log('paged: '+paged); 
        var link = $('ul.pgafu-filter li a.active');
        var id = link.attr("id"); 
        var name = link.text();
        //console.log("Category: "+name+" Id: "+id+" ajax_object.ajaxurl: "+ajax_object.ajaxurl);


        $.post(
            ajax_object.ajaxurl, {
            action: 'load_more_ebook',
            term_id: id,
            paged: paged
        }, function(data) {
            maincontent.animate({opacity:0.5});
            maincontent.append(data); 
            maincontent.animate({opacity:1});
            $('#paged').attr('value',paged);
        });
        
        

    });
});