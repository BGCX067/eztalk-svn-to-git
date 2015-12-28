(function() {
    var Event = YAHOO.util.Event,
        Color  = YAHOO.util.Color,
        picker;
    var nowid="color-background";
    Event.onDOMReady(function() {
            picker = new YAHOO.widget.ColorPicker("colorpicker", {
                    showhsvcontrols: false,
                    showhexcontrols: false,
                    showrgbcontrols : false,
                    showwebsafe : false,
                    showhexsummary: false,
                    showhsvcontrols:false,
                    showswatch:false,
					images: {
						PICKER_THUMB: webaddr+"/include/javascript/yui/colorpicker/assets/picker_thumb.png",
						HUE_THUMB: webaddr+"/include/javascript/yui/colorpicker/assets/hue_thumb.png"
    				}
                });

			var onRgbChange = function(o) {
                if (nowid=="color-background") document.body.style.backgroundColor="#"+Color.rgb2hex(o.newValue);
                if (nowid=="color-links") {
                    var aList = $("sideview").getElementsByTagName("a");
                    for(var j = 0; j < aList.length; j++)
                    {
                        aList[j].style.color ="#"+Color.rgb2hex(o.newValue);
                    }
                    var aList2 = $("header").getElementsByTagName("a");
                    for(var j = 0; j < aList2.length; j++)
                    {
                        aList2[j].style.color ="#"+Color.rgb2hex(o.newValue);
                    }
                }
                if (nowid=="color-text") document.body.style.color="#"+Color.rgb2hex(o.newValue);
                if (nowid=="color-sidebar") $("sidebar").style.background="#"+Color.rgb2hex(o.newValue);
                if (nowid=="color-sidebox") $("sidebar").style.borderColor="#"+Color.rgb2hex(o.newValue);

                $(nowid).value="#"+Color.rgb2hex(o.newValue);
                if (Color.rgb2hsv(o.newValue)[2]>0.8 && Color.rgb2hsv(o.newValue)[1]<0.4) $(nowid).style.color="#000000";
                else $(nowid).style.color="#FFFFFF";
                $(nowid).style.backgroundColor="#"+Color.rgb2hex(o.newValue);
            }

			picker.on("rgbChange", onRgbChange);

			Event.on("color-background", "click", function(e) {
                nowid="color-background";
                picker.setValue([Color.hex2rgb(this.value.replace("#",""))], false);
            });
            Event.on("color-text", "click", function(e) {
                nowid="color-text";
                picker.setValue([Color.hex2rgb(this.value.replace("#",""))], false);
            });
            Event.on("color-links", "click", function(e) {
                nowid="color-links";
                picker.setValue([Color.hex2rgb(this.value.replace("#",""))], false);
            });
            Event.on("color-sidebar", "click", function(e) {
                nowid="color-sidebar";
                picker.setValue([Color.hex2rgb(this.value.replace("#",""))], false);
            });
            Event.on("color-sidebox", "click", function(e) {
                nowid="color-sidebox"
                picker.setValue([Color.hex2rgb(this.value.replace("#",""))], false);
            });

            function defcolor()
            {
                $("color-background").value="#"+Color.rgb2hex(Color.hex2rgb(my_theme_bgcolor.replace("#","")));
                $("color-background").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(my_theme_bgcolor.replace("#","")));;
                $("color-text").value="#"+Color.rgb2hex(Color.hex2rgb(my_theme_text.replace("#","")));
                $("color-text").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(my_theme_text.replace("#","")));
                $("color-links").value="#"+Color.rgb2hex(Color.hex2rgb(my_theme_link.replace("#","")));
                $("color-links").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(my_theme_link.replace("#","")));
                $("color-sidebar").value="#"+Color.rgb2hex(Color.hex2rgb(my_theme_sidebar.replace("#","")));
                $("color-sidebar").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(my_theme_sidebar.replace("#","")));
                $("color-sidebox").value="#"+Color.rgb2hex(Color.hex2rgb(my_theme_sidebox.replace("#","")));
                $("color-sidebox").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(my_theme_sidebox.replace("#","")));
                if (Color.rgb2hsv(Color.hex2rgb(my_theme_bgcolor.replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(my_theme_bgcolor.replace("#","")))[1]<0.4) $("color-background").style.color="#000000";
                else $("color-background").style.color="#FFFFFF";
                if (Color.rgb2hsv(Color.hex2rgb(my_theme_text.replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(my_theme_text.replace("#","")))[1]<0.4) $("color-text").style.color="#000000";
                else $("color-text").style.color="#FFFFFF";
                if (Color.rgb2hsv(Color.hex2rgb(my_theme_link.replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(my_theme_link.replace("#","")))[1]<0.4) $("color-links").style.color="#000000";
                else $("color-links").style.color="#FFFFFF";
                if (Color.rgb2hsv(Color.hex2rgb(my_theme_sidebar.replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(my_theme_sidebar.replace("#","")))[1]<0.4) $("color-sidebar").style.color="#000000";
                else $("color-sidebar").style.color="#FFFFFF";
                if (Color.rgb2hsv(Color.hex2rgb(my_theme_sidebox.replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(my_theme_sidebox.replace("#","")))[1]<0.4) $("color-sidebox").style.color="#000000";
                else $("color-sidebox").style.color="#FFFFFF";
            }

            Event.onContentReady("container",defcolor);

            Event.on("cencel", "click", function(e) {
                window.location.href=webaddr+"/op/theme";
            });

            Event.on("setbgyes", "click", function(e) {
                t=$('color-background').value;
                u=this.style.backgroundImage.replace("_thumb","");
                document.body.style.background= t+' '+ u;
                $('user-background-repeat').checked=true;
                $('newbgurl').value=$("setbgyes").style.backgroundImage.replace(webaddr,"").replace("url(","").replace(")","").replace("/themebg_thumb.jpg","");
            });

            Event.on("setbgno", "click", function(e) {
                t=$('color-background').value;
                document.body.style.background= t;
                $('newbgurl').value='';
            });

            Event.on("tab-bg", "click", function(e) {
                $('settings-color').style.display="none";
                $('settings-background').style.display="block";
                $('tab-bg').className="tab-bg show current";
                $('tab-color').className="tab-color show";
            });
            Event.on("tab-color", "click", function(e) {
                $('settings-color').style.display="block";
                $('settings-background').style.display="none";
                $('tab-bg').className="tab-bg show";
                $('tab-color').className="tab-color show current";
            });
        });
})();


function inputcolor(obj,temp) {
    var Color=YAHOO.util.Color;
    if (obj=="color-background") document.body.style.backgroundColor=temp;
    if (obj=="color-links") {
        var aList = $("sidebar").getElementsByTagName("a");
        for(var j = 0; j < aList.length; j++)
        {
            aList[j].style.color =temp;
        }
        var aList2 = $("header").getElementsByTagName("a");
        for(var j = 0; j < aList2.length; j++)
        {
            aList2[j].style.color =temp;
        }
    }
    if (obj=="color-text") document.body.style.color=temp;
    if (obj=="color-sidebar") $("sidebar").style.background=temp;
    if (obj=="color-sidebox") $("sidebar").style.borderColor=temp;
    $(obj).style.backgroundColor=temp;
    if (Color.rgb2hsv(Color.hex2rgb(temp.replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(temp.replace("#","")))[1]<0.4) $(obj).style.color="#000000";
    else $(obj).style.color="#FFFFFF";
}

function usertheme(obj) {
    var ele= obj.split(",");
    var Color=YAHOO.util.Color;
    $("color-background").value="#"+Color.rgb2hex(Color.hex2rgb(ele[0].replace("#","")));
    $("color-background").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(ele[0].replace("#","")));;
    $("color-text").value="#"+Color.rgb2hex(Color.hex2rgb(ele[1].replace("#","")));
    $("color-text").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(ele[1].replace("#","")));
    $("color-links").value="#"+Color.rgb2hex(Color.hex2rgb(ele[2].replace("#","")));
    $("color-links").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(ele[2].replace("#","")));
    $("color-sidebar").value="#"+Color.rgb2hex(Color.hex2rgb(ele[3].replace("#","")));
    $("color-sidebar").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(ele[3].replace("#","")));
    $("color-sidebox").value="#"+Color.rgb2hex(Color.hex2rgb(ele[4].replace("#","")));
    $("color-sidebox").style.backgroundColor="#"+Color.rgb2hex(Color.hex2rgb(ele[4].replace("#","")));

    if (Color.rgb2hsv(Color.hex2rgb(ele[0].replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(ele[0].replace("#","")))[1]<0.4) $("color-background").style.color="#000000";
    else $("color-background").style.color="#FFFFFF";
    if (Color.rgb2hsv(Color.hex2rgb(ele[1].replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(ele[1].replace("#","")))[1]<0.4) $("color-text").style.color="#000000";
    else $("color-text").style.color="#FFFFFF";
    if (Color.rgb2hsv(Color.hex2rgb(ele[2].replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(ele[2].replace("#","")))[1]<0.4) $("color-links").style.color="#000000";
    else $("color-links").style.color="#FFFFFF";
    if (Color.rgb2hsv(Color.hex2rgb(ele[3].replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(ele[3].replace("#","")))[1]<0.4) $("color-sidebar").style.color="#000000";
    else $("color-sidebar").style.color="#FFFFFF";
    if (Color.rgb2hsv(Color.hex2rgb(ele[4].replace("#","")))[2]>0.8 && Color.rgb2hsv(Color.hex2rgb(ele[4].replace("#","")))[1]<0.4) $("color-sidebox").style.color="#000000";
    else $("color-sidebox").style.color="#FFFFFF";

    document.body.style.backgroundColor=ele[0];
    var aList = $("sideview").getElementsByTagName("a");
    for(var j = 0; j < aList.length; j++)
    {
        aList[j].style.color =ele[2];
    }
    var aList2 = $("header").getElementsByTagName("a");
    for(var j = 0; j < aList2.length; j++)
    {
        aList2[j].style.color =ele[2];
    }
    document.body.style.color=ele[1];
    $("sidebar").style.background=ele[3];
    $("sidebar").style.borderColor=ele[4];

    if (ele[5]==1) {
        if ($('setbgyes')) {
            $('setbgyes').style.backgroundImage="url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg_thumb.jpg)";
            $('user-background-'+ele[6]).checked=true;
            if (ele[6]=="repeat") {
                document.body.style.background= ele[0]+" url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg.jpg) repeat scroll left top";
            } else if (ele[6]=="center"){
                document.body.style.background= ele[0]+" url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg.jpg) no-repeat scroll center top";
            } else if (ele[6]=="left"){
                document.body.style.background= ele[0]+" url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg.jpg) no-repeat scroll left top";
            }
            $('newbgurl').value="attachments/usertemplates/"+ele[7];
        } else {
            $('themeimages').innerHTML='<a href="javascript:void(0);" id="setbgyes" style=""></a><a href="javascript:void(0);" class="nobg" id="setbgno"><img src="'+webaddr+'/images/'+temp_dir+'/theme-nobg.gif" alt="">&nbsp;<span>²»Òª±³¾°Í¼Æ¬</span></a><p><label for="user-background-repeat"><input id="user-background-repeat" onclick="repeatclick()" name="pictype" type="radio" value="repeat"> Æ½ÆÌ±³¾°Í¼Æ¬</label>&nbsp;&nbsp;<label for="user-background-center"><input id="user-background-center" onclick="centerclick()" name="pictype" type="radio" value="center"> ±³¾°¾ÓÖÐ</label>&nbsp;&nbsp;<label for="user-background-left"><input id="user-background-left" onclick="leftclick()" name="pictype" type="radio" value="left"> ×ó¶ÔÆë</label></p>';
            $('setbgyes').style.backgroundImage="url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg_thumb.jpg)";
            $('user-background-'+ele[6]).checked=true;
            if (ele[6]=="repeat") {
                document.body.style.background= ele[0]+" url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg.jpg) repeat scroll left top";
            } else if (ele[6]=="center"){
                document.body.style.background= ele[0]+" url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg.jpg) no-repeat scroll center top";
            } else if (ele[6]=="left"){
                document.body.style.background= ele[0]+" url("+webaddr+"/attachments/usertemplates/"+ele[7]+"/themebg.jpg) no-repeat scroll left top";
            }
            $('newbgurl').value="attachments/usertemplates/"+ele[7];
        }
    } else {
        document.body.style.background= ele[0];
        $('themeimages').innerHTML="";
        $('newbgurl').value="";
    }
}

function repeatclick() {
    t=$('color-background').value;
    u=$("setbgyes").style.backgroundImage.replace("_thumb","");
    document.body.style.background= t+' '+ u +' repeat scroll left top';
    $('newbgurl').value=$("setbgyes").style.backgroundImage.replace(webaddr,"").replace("url(","").replace(")","").replace("/themebg_thumb.jpg","");
}

function centerclick() {
    t=$('color-background').value;
    u=$("setbgyes").style.backgroundImage.replace("_thumb","");
    document.body.style.background= t+' '+ u +' no-repeat scroll center top';
    $('newbgurl').value=$("setbgyes").style.backgroundImage.replace(webaddr,"").replace("url(","").replace(")","").replace("/themebg_thumb.jpg","");
}

function leftclick() {
    t=$('color-background').value;
    u=$("setbgyes").style.backgroundImage.replace("_thumb","");
    document.body.style.background= t+' '+ u +' no-repeat scroll left top';
    $('newbgurl').value=$("setbgyes").style.backgroundImage.replace(webaddr,"").replace("url(","").replace(")","").replace("/themebg_thumb.jpg","");
}