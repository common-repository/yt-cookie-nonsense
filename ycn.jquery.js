function ycn_readCookie(name){
  // console.log("load cookie func");
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++){
      var c = ca[i];
      while(c.charAt(0)==' ') c = c.substring(1,c.length);
      if(c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}
function ycn_loadVideo(ycn_this){
  // console.log("func loaded");
  if(ycn_this!=null){
    var ycn_url = ycn_this.closest(".yt-cookie-nonsense").data("videoid");
    ycn_this.closest(".ycn-video-preview").hide();
    ycn_this.closest(".yt-cookie-nonsense").find("iframe").show().attr("src",ycn_url);
    // console.log("video loaded");
  } else {
    jQuery(".yt-cookie-nonsense").each(function(){
      jQuery(this).children().hide();
      jQuery(this).find("iframe").show().attr("src",jQuery(this).data("videoid"));
      // console.log("video loaded from loop");
    });
  }
}
jQuery(function(){
 jQuery(".ycn-btn").click(function(e){
  e.preventDefault();
  document.cookie = "ytprefs_gdpr_consent=1; Secure; Max-Age=2600000; path=/; samesite=strict;";
  // console.log("btn_pressed");
  ycn_loadVideo(jQuery(this));
 });
 if(ycn_readCookie("ytprefs_gdpr_consent")!=null){
   // console.log("cookie_found");
  ycn_loadVideo(null);
 }
});
