/*
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2010, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
/*
 * ExpressionEngine Template Manager Javascript
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		ExpressionEngine Dev Team
 * @link		http://expressionengine.com
 */
(function(g){var a,h;g(document).ready(function(){a=g("#prefRowTemplate").html();h=g("#accessRowTemplate").html();if(!a||!h){var o=g("#templateAccess, #templatePreferences"),n=g("input:hidden[name=template_id]").val(),p=g("input:hidden[name=group_id]").val();g("#templatePreferences").data("ajax_ids",{id:n,group_id:p});e(g("#templateAccess"));o.find("input:text").unbind("blur.manager_updated").bind("blur.manager_updated",d);o.find("input:radio").unbind("click.manager_updated").bind("click.manager_updated",d);o.find("select").unbind("change.manager_updated").bind("change.manager_updated",d);return}g("#prefRowTemplate, #accessRowTemplate").remove();EE.manager={showPrefsRow:function(s,q){var r=g(q).parent().parent();if(!j(r,"prefsRow")){l(r,s);c()}return false},showAccessRow:function(q,t,r){var s=g(r).parent().parent();if(!j(s,"accessRow")){f(q,s,t);c();s.trigger("applyWidgets")}return false}}});function j(o,p){if(o.hasClass("highlightRow")){o.removeClass("highlightRow")}if(o.data(p)){var n=o.data(p).is(":visible");m(o);if(!n){o.addClass("highlightRow");o.data(p).show()}return true}m(o);return false}function m(o,n){if(n){if(g(o).data(n)){g(o).data(n).hide()}return}m(o,"prefsRow");m(o,"accessRow")}function k(n,o){n.find("input:radio").each(function(){var r,p,q;r=g(this).attr("id").split("_");p=r.slice(0,-1).join("_");q=r.slice(-1)[0];g(this).attr({id:p+"_"+o+"_"+q,name:p+"_"+o})})}function l(n,p){var o=g('<tr class="accessRowHeader"><td colspan="6">'+a+"</td></tr>");o.find("select").each(function(){var q=g(this);switch(this.name){case"template_type":q.val(p.type);break;case"cache":q.val(p.cache);break;case"allow_php":q.val(p.allow_php);break;case"php_parse_location":q.val(p.php_parsing);break}q.attr("name",this.name+"_"+p.id)});o.find(".template_name").val(p.name);if(p.name=="index"){o.find(".template_name").attr({readonly:"readonly"})}o.find(".refresh").val(p.refresh);o.find(".hits").val(p.hits);o.data("ajax_ids",{id:p.id,group_id:p.group_id});n.data("prefsRow",o);g(n).addClass("highlightRow");g(n).after(o)}function c(){g(".templateTable .accessTable").find("input:text").unbind("blur.manager_updated").bind("blur.manager_updated",d);g(".templateTable .accessTable").find("input:radio").unbind("click.manager_updated").bind("click.manager_updated",d);g(".templateTable .accessTable").find("select").unbind("change.manager_updated").bind("change.manager_updated",d)}function e(o,n){var p="input:radio[id$=_";if(n){p="input:radio[id$=_"+n+"_"}o.find(".ignore_radio").click(function(){if(this.value=="y"){o.find(p+"y]").filter(":not(.ignore_radio)").trigger("click")}if(this.value=="n"){o.find(p+"n]").filter(":not(.ignore_radio)").trigger("click")}g(this).attr("checked","");return false})}function f(n,o,q){var p=g('<tr class="accessRowHeader"><td colspan="6">'+h+"</td></tr>");p.find(".no_auth_bounce").val(q.no_auth_bounce);p.find(".no_auth_bounce").attr({id:"no_auth_bounce_"+n,name:"no_auth_bounce_"+n});p.find(".enable_http_auth").val(q.enable_http_auth);p.find(".enable_http_auth").attr({id:"enable_http_auth_"+n,name:"enable_http_auth_"+n});k(p,n);g.each(q.access,function(u,t){var s=p.find("#access_"+u+"_"+n+"_y");var r=p.find("#access_"+u+"_"+n+"_n");if(t.access===true){s.attr("checked","checked");r.attr("checked","")}else{r.attr("checked","checked");s.attr("checked","")}});e(p,n);g(o).addClass("highlightRow");g(o).after(p);p.find(".accessTable").tablesorter({widgets:["zebra"]});o.data("accessRow",p)}function i(p){if(p.attr("name").substr(0,14)=="no_auth_bounce"){n=(p.attr("name").substr(15))?p.attr("name").substr(15):g("input:hidden[name=template_id]").val();b(p,n,"","no_auth_bounce")}else{if(p.attr("name").substr(0,16)=="enable_http_auth"){n=(p.attr("name").substr(17))?p.attr("name").substr(17):g("input:hidden[name=template_id]").val();b(p,n,"","enable_http_auth")}else{var o=p.attr("name").replace("access_","").split("_"),n=(o.length<2)?g("input:hidden[name=template_id]").val():o[1];b(p,n,o[0],"access")}}}function b(q,o,r,p){switch(p){case"no_auth_bounce":var s=jQuery.param({template_id:o,no_auth_bounce:q.val()});break;case"enable_http_auth":var s=jQuery.param({template_id:o,enable_http_auth:q.val()});break;case"access":var n=(!g(q).closest(".accessTable").length)?g(".no_auth_bounce").val():g(q).closest(".accessTable").find(".no_auth_bounce").val();var s=jQuery.param({template_id:o,member_group_id:r,new_status:q.val(),no_auth_bounce:n});break}g.ajax({type:"POST",url:EE.access_edit_url,data:"is_ajax=TRUE&XID="+EE.XID+"&"+s,success:function(t){if(t!=""){g.ee_notice(t,{duration:3000,type:"success"})}},error:function(u,t){if(u.responseText!=""){g.ee_notice(u.responseText,{duration:3000,type:"error"})}}})}function d(){var u=g(this).closest(".accessRowHeader"),p,v,x,q,w,n,t,s,o,r;if(u.length<1){u=g(this).closest(".templateEditorTable")}p=u.data("ajax_ids");if(!p){if(g(this).hasClass("ignore_radio")){return false}return i(g(this))}v=p.id;x=p.group_id;q=u.find(".template_name").val();w=u.find(".template_type").val();n=u.find("select[name^=cache]").val();t=u.find(".refresh").val();s=u.find("select[name^=allow_php]").val();o=u.find("select[name^=php_parse_location]").val();r=u.find(".hits").val();template_size=u.find(".template_size").val();str=jQuery.param({template_id:v,group_id:x,template_name:q,template_type:w,cache:n,refresh:t,hits:r,allow_php:s,php_parse_location:o,template_size:template_size});g.ajax({type:"POST",url:EE.template_edit_url,data:"is_ajax=TRUE&XID="+EE.XID+"&"+str,success:function(y){g("#templateId_"+v).text(q);g("#template_data").attr("rows",template_size);g("#hitsId_"+v).text(r);if(y!=""){g.ee_notice(y,{duration:3000,type:"success"})}},error:function(z,y){if(z.responseText!=""){g.ee_notice(z.responseText,{duration:3000,type:"error"})}}})}g(document).ready(function(){if(!EE.manager||!EE.manager.warnings){return}g(".warning_details").hide();g(".toggle_warning_details").click(function(){g(".warning_details").hide();g("#wd_"+this.id.substr(3)).show();return false});var o=g("#template_data"),n;find_and_replace=function(s,q,t){var r,p="";if(t&&t.length>1){p='<select name="fr_options" id="fr_options"></select>'}r='<div style="padding: 5px;"><label>Find:</label> <input name="fr_find" id="fr_find" type="text" value="" /> <label>Replace:</label> <input type="text" name="fr_replace" id="fr_replace" value=""/> '+p+"</div>";r+='<div style="padding: 5px;"><button class="submit" id="fr_find_btn">Find Next</button> <button class="submit" id="fr_replace_btn">Replace</button> <button class="submit" id="fr_replace_all_btn">Replace All</button> <label><input name="fr_replace_closing_tags" id="fr_replace_closing_tags" type="checkbox" /> Include Closing Tags</label></div>';g.ee_notice(r,{type:"custom",open:true,close_on_click:false});g("#fr_find").val(s);g("#fr_replace").val(q);g("#fr_replace_closing_tags").attr("checked","");if(p!=""){g("#fr_options").append(g(t));g("#fr_options").click(function(){g("#fr_find").val(g(this).val());g("#fr_find_btn").click()})}if(s){g("#fr_find_btn").click()}};g("#fr_find_btn").live("click",function(){var p=g("#fr_find").val();n=o.selectNext(p).scrollToCursor()});g("#fr_replace_btn").live("click",function(){var q=g("#fr_find").val(),p=g("#fr_replace").val();if(n.getSelectedText()==q){n.replaceWith(p)}});g("#fr_replace_all_btn").live("click",function(){var q=g("#fr_find").val(),p=g("#fr_replace").val();if(jQuery.trim(q)==""){return}o.val(o.val().split(q).join(p));if(g("#fr_replace_closing_tags").attr("checked")){if(q[0]=="{"&&q.substr(0,2)!="{/"){q="{/"+q.substr(1)}if(p[0]=="{"&&p.substr(0,2)!="{/"){p="{/"+p.substr(1)}if(jQuery.trim(q)==""){return}o.val(o.val().split(q).join(p))}});g(".find_and_replace").click(function(){var t=this.id.substr(8),s="{exp:"+t,r="{exp:"+EE.manager.warnings[t]["suggestion"],u=EE.manager.warnings[t]["full_tags"],p=new Array(new Option(s,s));if(u&&u.length>1){for(var q=0;q<u.length;q++){var t="{"+u[q]+"}";p.push(new Option(t,t))}}if(r=="{exp:"){r=""}find_and_replace(s,r,p);return false})})})(jQuery);