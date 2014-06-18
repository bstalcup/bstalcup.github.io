$(document).ready(function(){
    
    $('.tab-pane.active .button').on( "click", function() {
      $('#tab1').hide("slide", { direction: "right" }, 800);
      $('#tab1').removeClass("active");
      setTimeout(function(){
      	$('.btn-striped li.active').next().addClass("active");
      	$('.btn-striped li.active').prev().addClass("prev_active");
        $('#tab2').show("slide", { direction: "right" }, 800);
        $('#tab2').addClass("active");
        $('ul.button-pager').fadeIn();
      }, 800);
      $('ul.button-pager').addClass('show'); //show the next/prev buttons
    });    
    //end first slide options slider

    $('ul.button-pager li.previous a').on("click", function(e) {
    	var current = $('.tab-pane.active');
    	var previous = $('.tab-pane.active').prev();
    	current.hide("slide", { direction: "right" }, 800).removeClass('active');
    	setTimeout(function(){
    		previous.show("slide", { direction: "right" }, 800).addClass('active');
    		if ( $('#tab1').hasClass('active') ) { $('ul.button-pager').fadeOut(); };
    		$('.btn-striped li.prev_active').last().next().removeClass("active");
      	$('.btn-striped li.prev_active').last().removeClass("prev_active");
        if ($('#tab3').is(":hidden")) { $('ul.button-pager li.next').removeClass('get-import'); }
        $('ul.button-pager li.next').fadeIn(); //show the next button
        if ( $('#tab3').is(":visible") && $('.sort-container li').length > 0 ) {
          $('ul.button-pager li.next').removeClass('disabled');
        } //disable the next button
       }, 800);
    });
    //end PREV button JS

    $('ul.button-pager li.next a').on("click", function(e) {
    	var current = $('.tab-pane.active');
    	var next = $('.tab-pane.active').next();
      if ( $(this).parent().hasClass('disabled') ){
          return false;
          console.log('clicked false');
        } else {
    	    current.hide("slide", { direction: "right" }, 800).removeClass('active');
    	    setTimeout(function(){
    		    next.show("slide", { direction: "right" }, 800).addClass('active');
    		    if ( $('#tab1').hasClass('active') ) { $('ul.button-pager').fadeOut(); };
    		    $('.btn-striped li.active').next().addClass("active");
      	    $('.btn-striped li.active').prev().addClass("prev_active");
            if ($('#tab3').is(":visible")) { $('ul.button-pager li.next').addClass('get-import'); }
            if ($('#tab4').is(":visible")) { $('ul.button-pager li.next').fadeOut(); }
    	    }, 800); //keep them in a timeout function to wait for the slide animation to finish
          $(this).parent().addClass('disabled'); //disable the NEXT button by adding this class   
          if ( $(this).parent().hasClass('get-import') ){
            GetInfo();
          }
          //execute the GetInfo(); function if the parent li has class
        }        
    });
    //end NEXT button JS

    function GetInfo() {
      //click works!
      console.log('get the info now!');
      
      //store the values of all inputs in .select-action ul
      arr = $('.sort-container li.select-action input').map(function(){
        return $(this).val();
      });
      
      for(i=0; i < arr.length; i++)
        console.log(arr[i]);
    }
    //function to get the info from #tab3

    /*
    function NextCheck() {
      if ($('.sort-container li').length == 0) {
        $('ul.button-pager li.next').addClass('disabled');
      } else {
        $('ul.button-pager li.next').removeClass('disabled');
      }
    }
    */
	  Dropzone.autoDiscover = false;
	  var myDropzone = new Dropzone(".dropzone", {
  	  url: "http://www.torrentplease.com/dropzone.php",
  	  previewsContainer: "#previews",
  	  clickable: ".clickable",
  	  addRemoveLinks: true,
  	  dictDefaultMessage: "",
  	  acceptedFiles: '.CSV'
	  });
    //initialize the drag'n'drop file upload JS
    //this should help: http://www.dropzonejs.com/

    //teoretic cand e uploadat fisierul de aici facem enable la butonul de next
    Dropzone.confirm = function(question, accepted, rejected) {
      console.log('success');
    };
    
    myDropzone.on("addedfile", function(file) {
      $('ul.button-pager li.next').removeClass('disabled');
    });
    //temporary to enable the next button when adding a file

    $( "ul.droptrue" ).sortable({
      connectWith: "ul"
    });
    $( "ul.droptrue" ).droppable({});
    $( "ul.dropfalse" ).droppable({
      drop: function(event,ui) {},
      out: function( event, ui ) {
        var draggableId = ui.draggable.data('id');
        $('.select-action[data-id="' + draggableId +'"]').remove();
        if ($('.sort-container li').length == 0) {
          $('ul.button-pager li.next').addClass('disabled');
        } //disable the next button
      }
    });
    //initialize jQueryUI draggable / sortable JS
    
    var originalIndex;
    var newIndex;
    var originalImage;
    var newImage;
    var gallery = $(".sort-container");
    
    $("#sortable2").sortable({ 
    
      start:function(event,ui){
        originalIndex = ui.item.index();
        originalImage = gallery[0].children[originalIndex];
      },
      
      stop:function(event,ui){
        newIndex = ui.item.index();
        newImage = gallery[0].children[newIndex];        
        if(originalIndex < newIndex){
            $(newImage).after(originalImage);
        }else{
            $(newImage).before(originalImage);
        }
      },
      
      receive: function( event, ui ) {

        var draggableId = ui.item.data('id');

        var HTMLvar = '<li class="select-action" data-id="' + draggableId + '"><select class="turnintodropdown"><option>Choose action ( ' + draggableId + '):</option><option>Always Replaces</option><option>Replaces if empty</option><option>Replaces for unclaimed families</option></select></li>';

        if ($('.sort-container li').length == 0) {
          if ( ui.item.index() == 0 ) {
            $('.sort-container').html(HTMLvar);
          } 
        }
        else {
          if ( ui.item.index() == 0 ) {
            $('.sort-container li').eq( ui.item.index() ).before(HTMLvar);
          } 
          else {
            if ( $('.sort-container > li').length == ui.item.index() )
            {
              $( HTMLvar ).appendTo( $('.sort-container') );
            }
            else {
              $('.sort-container > li').eq( ui.item.index() -1 ).after(HTMLvar);
            }
          }
        }
        //generate select and put them next to their li
        
        $('ul.button-pager li.next').removeClass('disabled'); //enable the button

        tamingselect(); //reinitialize the select dropdown JS
    }
  });
  //end initialize sortable on #sortable2
});