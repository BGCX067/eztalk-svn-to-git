// Onload
YAHOO.util.Event.onContentReady("maincontainer",function(){
    // toolbar
    if ($("toolbar") && $("toolbarmin")) {
        var showbottom = YAHOO.util.Cookie.get("showbottom");
        if (showbottom=="min") {
            $("toolbarmin").style.display="block";
            var anim1 = new YAHOO.util.Anim($("toolbarmin"), {opacity: {to:"1"}});
            anim1.animate();
        } else {
            $("toolbar").style.display="block";
            var anim1 = new YAHOO.util.Anim($("toolbar"), {opacity: {to:"1"}});
            anim1.animate();
        }
    }

    // home
    if (backto=="privatemsg") {
        if ($("homecontainer")) showhome("privatemsg",user_id,"1",'send');
    } else {
        if ($("homecontainer")) showhome("home",user_id,"1");
    }

    // li
    var el=YAHOO.util.Dom.get("stream");
    if(!el){return;}
    var _li=el.getElementsByTagName("li");
    YAHOO.util.Dom.addClass(_li,"unlight");
    YAHOO.util.Event.on(_li,"mouseover",function(e){
        YAHOO.util.Dom.addClass(this,"light");
        YAHOO.util.Dom.removeClass(this,"unlight");
        });
    YAHOO.util.Event.on(_li,"mouseout",function(e){
        YAHOO.util.Dom.addClass(this,"unlight");
        YAHOO.util.Dom.removeClass(this,"light");
    });
});


function GetRandomNum(Min,Max) {
    var Range = Max - Min;
    var Rand = Math.random();
    return(Min + Math.round(Rand * Range));
}
// 输入限制
YAHOO.util.Event.on("contentbox", "keyup", function(e) {
    var len=$('contentbox').value.length;
    $('nums').innerHTML=len;
    if (len>140) {
        $('contentbox').value=$('contentbox').value.slice(0,140);
    }
});


//删除 home.php&&browse.php
function delmsg(url,mes,obj) {
    var mymes;
    mymes=confirm(mes);
    if(mymes==true){
	    var request = YAHOO.util.Connect.asyncRequest('GET', url, {
            success:function(o){
                if (o.responseText=="success") {
                    var attributes = {opacity: {to:"0"}};
                    var anim = new YAHOO.util.Anim(obj, attributes);
                    anim.animate();
                    anim.onComplete.subscribe(function() {
                        obj.parentNode.removeChild(obj);
                    });
                }else {
                    alert(o.responseText);
                }
            },
            failure:function(o){}
        });
    }
}

//收藏
function send_f(id){
   var request = YAHOO.util.Connect.asyncRequest('GET', webaddr+'/home/favorite&act=addfav&fid='+id, {
        success:function(o){alert("提示：收藏成功！");},
        failure:function(o){alert("未知异常，收藏失败！");}
   });
}

function isfun() {
    if (document.message.content.value=="") {
        alert("错误提示：您没有填写发表的内容，请填写后发表！");
    } else if (document.message.content.value.length>140)  {
        alert("错误提示：发送的信息长度不能大于140字符！");
    } else {
        return true;
    }
    return false;
}

function jsop(url,mes){
    var mymes;
    mymes=confirm(mes);
    if(mymes==true){
        window.location=url;
    }
}

// Showhome
function showhome(tp,u,p,primsgtype){
    $("homecontainer").innerHTML='<img src="'+webaddr+'/images/spinner.gif"> 数据载入中...';
    var linum=$("homestabs").getElementsByTagName("li").length;
    for (var i=0; i<linum; i++) {
        if ($("homestabs").getElementsByTagName("li")[i].className=="current"){
            $("homestabs").getElementsByTagName("li")[i].className="";
        }
    }
    $("stab_"+tp).className="loading";
    var request = YAHOO.util.Connect.asyncRequest('GET', webaddr+"/home/"+tp+"/u."+u+"/p."+p+"&pm="+primsgtype+"&rank="+GetRandomNum(1,999999), {
        success:function(o){
            $("homecontainer").innerHTML=o.responseText;
            $("stab_"+tp).className="current";
            YAHOO.util.Event.onContentReady("stream",function(){
                var el=YAHOO.util.Dom.get("stream");
                if(!el){return;}
                var _li=el.getElementsByTagName("li");
                YAHOO.util.Event.on(_li,"mouseover",function(e){
                    YAHOO.util.Dom.addClass(this,"light");
                    YAHOO.util.Dom.removeClass(this,"unlight");
                    });
                YAHOO.util.Event.on(_li,"mouseout",function(e){
                    YAHOO.util.Dom.addClass(this,"unlight");
                    YAHOO.util.Dom.removeClass(this,"light");
                });
            });
            if ($("homenum") && $("hmhome")) {
                $("homenum").innerHTML=$("hmhome").value;
            }
            if ($("favoritenum") && $("hmfavorite")) {
                $("favoritenum").innerHTML=$("hmfavorite").value;
            }
            if ($("privatemsgnum") && $("hmprivatemsg")) {
                $("privatemsgnum").innerHTML=$("hmprivatemsg").value;
            }
            if ($("primsghead")) {
                $("info").style.display="none";
            } else {
                $("info").style.display="block";
            }
            if (tp=="privatemsg") {
                YAHOO.util.Event.on("pmcontentbox", "keyup", function(e) {
                    var pmlen=$('pmcontentbox').value.length;
                    $('pmnums').innerHTML=pmlen;
                    if (len>140) {
                        $('pmcontentbox').value=$('pmcontentbox').value.slice(0,140);
                    }
                });
            }
        },
        failure:function(o){}
    });
}


// 注册检测
function check_register() {
    var t1=$('username').value;
    var t2=$('mailadres').value;
    var t3=$('password1').value;
    var t4=$('password2').value;

    var request = YAHOO.util.Connect.asyncRequest('GET', webaddr+"/op/register&act=check&uname="+t1+"&mail="+t2+"&pass1="+t3+"&pass2="+t4, {
        success:function(o){
            if (o.responseText=="check_ok") {
                var request = YAHOO.util.Connect.asyncRequest('GET', webaddr+"/op/register&act=reg&uname="+t1+"&mail="+t2+"&pass1="+t3+"&pass2="+t4, {
                    success:function(oo){
                        if (oo.responseText=="reg_ok") {
                            alert("恭喜您，您已经成功注册了冷知识，点击确定进入主页！");
                            location.href=webaddr+'/home';
                        }else {
                            alert(oo.responseText);
                        }
                    },failure:function(oo){}
                });
            } else {
                alert(o.responseText);
            }
        },
        failure:function(o){}
    });
}


//flash插件
function flashstyle(uid,sty) {
    $('sty').value=sty;
    $('flashdiv').innerHTML='<embed id="id_flash" height="400" width="180" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="'+webaddr+'/badge/flashlist.swf?uid='+uid+'&num=20&style='+sty+'&site='+webaddr+'" allowscriptaccess="sameDomain"/>';
}

function flashover(uid) {
    var sv=$('flashsv').value;
    var num=$('num').value;
    var sty=$('sty').value;
    if (sv==0) {
        $('code').value=webaddr+'/badge/flashlist.swf?uid='+uid+'&num='+num+'&style='+sty+'&site='+webaddr;
    }
    if (sv==1 || sv==2 || sv==4) {
        $('code').value='<embed type="application/x-shockwave-flash" src="'+webaddr+'/badge/flashlist.swf?uid='+uid+'&num='+num+'&style='+sty+'&site='+webaddr+'" quality="autohigh" wmode="transparent" width="180" height="400"></embed>';
    }
    if (sv==3 || sv==5 || sv==6) {
        $('code').value='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,124,0" width="180" height="400"><param name="movie" value="'+webaddr+'/badge/flashlist.swf" /><param name="flashvars" value="uid='+uid+'&num='+num+'&style='+sty+'&site='+webaddr+'" /><param name="quality" value="autohigh" /><param name="wmode" value="transparent" /><embed type="application/x-shockwave-flash" src="'+webaddr+'/badge/flashlist.swf" flashvars="uid='+uid+'&num='+num+'&style='+sty+'&site='+webaddr+'" quality="autohigh" wmode="transparent" width="180" height="400"></embed></object>';
    }
}

//bottomShow
function bottomtomin() {
    YAHOO.util.Cookie.set("showbottom","min", { path: "/" });
    var anim = new YAHOO.util.Anim($("toolbar"), {opacity: {to:"0"}});
    anim.animate();
    anim.onComplete.subscribe(function() {
        $("toolbar").style.display="none";
        $("toolbarmin").style.display="block";
        var anim2 = new YAHOO.util.Anim($("toolbarmin"), {opacity: {to:"1"}});
        anim2.animate();
    });
}

function bottomtomax() {
    YAHOO.util.Cookie.set("showbottom","max",{ path: "/" });
    var anim = new YAHOO.util.Anim($("toolbarmin"), {opacity: {to:"0"}});
    anim.animate();
    anim.onComplete.subscribe(function() {
        $("toolbarmin").style.display="none";
        $("toolbar").style.display="block";
        var anim2 = new YAHOO.util.Anim($("toolbar"), {opacity: {to:"1"}});
        anim2.animate();
    });
}