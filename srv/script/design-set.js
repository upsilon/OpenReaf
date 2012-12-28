/*!
 * jQuery v1.4.4
 * jquery.easing.1.3.js
 *  
 *  Copyright 2011 ZiWAVE Co., Ltd.
 * 
*/

/* アコーディオンメニュー*/
function AcrdIcon(str){
	   $(str + " .set").click(function(){
		 	var target = jQuery(this);
			var class_name = target.attr("class");
			var Acrds = {height:"toggle", opacity:"toggle"};
			$(this).next().animate(Acrds, "fast","easeInQuad");
			if(class_name == "set") {
				$(this).addClass("chk");
				$(this).removeClass("set");
			} else { 
				$(this).addClass("set");
				$(this).removeClass("chk");
			}
		});
	};

function AcrdIcon2(){ 
  $(".acrd-menu").each(function(){ 
    $(".acrd-menu > dt", this).each(function(index){ 
      var $this = $(this); 
 
      if(index > 0) $this.next().hide(); 
 
      $this.click(function(){ 
        $(this).next().toggle().parent().siblings() 
          .children("dd:visible").hide(); 
		  
        var params = {height:"toggle", opacity:"toggle"}; 
        j$(this).next().animate(params).parent().siblings() 
          .children("ul:visible").animate(params); 
		  
        return false; 
      }); 
    }); 
  }); 
}; 

/* クリックによりクラスチェンジ 汎用的に使用*/
function ChkBtn(str){
	   $("#" + str + " li").click(function(){
		 	var target = jQuery(this);
			var class_name = target.attr("class");
			if(class_name == "set") {
				$(this).addClass("chk");
				$(this).removeClass("set");
			} else { 
				$(this).addClass("set");
				$(this).removeClass("chk");
			}
		});
	};

	
/*メッセージポップアップボックス*/
function Message(){
		setTimeout("$('#message-box').animate({'opacity':'1'}, 'slow', 'easeOutQuart');", 1000);
		$("#message-top").click(function(){
			$(this).fadeOut("slow",function(){$(this).css("display", "none")});
			$("#message-box").fadeOut("slow",function(){$(this).css("display", "none")});
			$("#message-back").fadeOut("slow",function(){$(this).css("display", "none")});
		});	
	};

/*ラジオボタン・チェックボックス装飾*/

function CheckRadios(str){
	$(str + " .chkbox").change(function(){
		if($(this).is(":checked")){
			$(this).parent().addClass("Lset");
		}else{
			$(this).parent().removeClass("Lset");
		}
	});
	$(str + " .rdo").change(function(){
		if($(this).is(":checked")){
			$(".Lset:not(:checked)").removeClass("Lset");
			$(this).parent().addClass("Lset");
		}
	}); 
};