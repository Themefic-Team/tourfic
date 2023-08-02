(()=>{var t,e;function a(t,e){var a,i,n;for(i=document.getElementsByClassName("tf-tabcontent"),a=0;a<i.length;a++)i[a].style.display="none";for(n=document.getElementsByClassName("tf-tablinks"),a=0;a<n.length;a++)n[a].className=n[a].className.replace(" active","");document.getElementById(e).style.display="block",document.getElementById(e).style.transition="all 0.2s",t.target.className+=" active"}t=jQuery,e=window,t(document).ready((function(){const i=new Notyf({ripple:!0,duration:3e3,dismissable:!0,position:{x:"right",y:"bottom"}}),n=()=>{var e=t("#adults").attr("type"),a=t("#children").attr("type");if(""!=t.trim(t("input[name=check-in-out-date]").val())){1===t("#tf-required").length&&t(".tf_booking-dates .tf_label-row .required").html("");var i=[];t(".tf-room-checkbox :checkbox:checked").each((function(e){i[e]=t(this).val()}));var n=t("input[name=tf_room_avail_nonce]").val(),s=t("input[name=post_id]").val();if("number"==e)var o=t("#adults").val();else o=t("select[name=adults] option").filter(":selected").val();if("number"==a)var r=t("#children").val();else r=t("select[name=children] option").filter(":selected").val();var l=t("input[name=children_ages]").val(),c=t("input[name=check-in-out-date]").val(),f={action:"tf_room_availability",tf_room_avail_nonce:n,post_id:s,adult:o,child:r,features:i,children_ages:l,check_in_out:c};jQuery.ajax({url:tf_params.ajax_url,type:"post",data:f,beforeSend:function(){t("#tf-single-hotel-avail .btn-primary.tf-submit").addClass("tf-btn-booking-loading")},success:function(e){t("html, body").animate({scrollTop:t("#rooms").offset().top},500),t("#rooms").html(e),t(".tf-room-filter").show(),t("#tf-single-hotel-avail .btn-primary.tf-submit").removeClass("tf-btn-booking-loading")},error:function(t){console.log(t)}})}else 0===t("#tf-required").length&&t(".tf_booking-dates .tf_label-row").append('<span id="tf-required" class="required"><b>'+tf_params.field_required+"</b></span>")};t(document).on("click","#tf-single-hotel-avail .tf-submit",(function(t){t.preventDefault(),n()})),t(document).on("change",".tf-room-checkbox :checkbox",(function(){n()})),t(document).on("click",".hotel-room-availability",(function(e){e.preventDefault(),t("html, body").animate({scrollTop:t("#tf-single-hotel-avail").offset().top},500)})),t(document).on("click",".hotel-room-book",(function(e){e.preventDefault();var a=t(this),n=t("input[name=tf_room_booking_nonce]").val(),s=t("input[name=post_id]").val();if(t(this).closest(".room-submit-wrap").find("input[name=room_id]").val())var o=t(this).closest(".room-submit-wrap").find("input[name=room_id]").val();else o=t("#hotel_roomid").val();if(t(this).closest(".room-submit-wrap").find("input[name=unique_id]").val())var r=t(this).closest(".room-submit-wrap").find("input[name=unique_id]").val();else r=t("#hotel_room_uniqueid").val();var l=t("input[name=place]").val(),c=t("input[name=adult]").val(),f=t("input[name=child]").val(),d=t("input[name=children_ages]").val(),u=t("input[name=check_in_date]").val(),p=t("input[name=check_out_date]").val();if(t(this).closest(".reserve").find("select[name=hotel_room_selected] option").filter(":selected").val())var m=t(this).closest(".reserve").find("select[name=hotel_room_selected] option").filter(":selected").val(),h=t(this).closest(".room-submit-wrap").find("input[name=make_deposit]").is(":checked");else m=t("#hotel_room_number").val(),h=t("#hotel_room_depo").val();var _={action:"tf_hotel_booking",tf_room_booking_nonce:n,post_id:s,room_id:o,unique_id:r,location:l,adult:c,child:f,children_ages:d,check_in_date:u,check_out_date:p,room:m,deposit:h,airport_service:t(".fancybox-slide #airport-service").val()};t.ajax({type:"post",url:tf_params.ajax_url,data:_,beforeSend:function(e){a.block({message:null,overlayCSS:{background:"#fff",opacity:.5}}),t(".tf_notice_wrapper").html("").hide()},complete:function(t){a.unblock()},success:function(t){a.unblock();var e=JSON.parse(t);if("error"==e.status)return e.errors&&e.errors.forEach((function(t){i.error(t)})),!1;e.redirect_to?window.location.replace(e.redirect_to):jQuery(document.body).trigger("added_to_cart")},error:function(t){console.log(t)}})})),t('[data-fancybox="hotel-gallery"]').fancybox({loop:!0,buttons:["zoom","slideShow","fullScreen","close"],hash:!1});var s,o=t(".swiper-button-prev"),r=t(".swiper-button-next");t(".single-slider-wrapper .tf_slider-for").slick({slide:".slick-slide-item",slidesToShow:1,slidesToScroll:1,arrows:!1,fade:!1,dots:!1,centerMode:!1,variableWidth:!1,adaptiveHeight:!0}),o.on("click",(function(){t(this).closest(".single-slider-wrapper").find(".tf_slider-for").slick("slickPrev")})),r.on("click",(function(){t(this).closest(".single-slider-wrapper").find(".tf_slider-for").slick("slickNext")})),t(".reserve-button a").click((function(){t("html, body").animate({scrollTop:t("#rooms").offset().top-32},1e3)})),t(".tf-top-review a").click((function(){t("html, body").animate({scrollTop:t("#tf-review").offset().top-32},1e3)})),t(".tf-map-link a").click((function(){t("html, body").animate({scrollTop:t("#tour-map").offset().top-32},1e3)})),t(document).on("submit","form.tf_tours_booking",(function(e){e.preventDefault();var a=t(this),n=new FormData(this);n.append("action","tf_tours_booking");var s=[];jQuery(".tour-extra-single input:checkbox:checked").each((function(){s.push(jQuery(this).val())})),n.append("tour_extra",s),t.ajax({type:"post",url:tf_params.ajax_url,data:n,processData:!1,contentType:!1,beforeSend:function(e){a.block({message:null,overlayCSS:{background:"#fff",opacity:.5}}),t(".tf-notice-wrapper").html("").hide()},complete:function(t){a.unblock()},success:function(e){a.unblock();var n=JSON.parse(e);if("error"==n.status)return t.fancybox.close(),n.errors&&n.errors.forEach((function(t){i.error(t)})),!1;n.redirect_to?window.location.replace(n.redirect_to):jQuery(document.body).trigger("added_to_cart"),console.log(n)},error:function(t){console.log(t)}})})),t('[data-fancybox="tour-gallery"]').fancybox({loop:!0,buttons:["zoom","slideShow","fullScreen","close"],hash:!1}),t(".tf-itinerary-gallery").fancybox({buttons:["zoom","slideShow","fullScreen","close"]}),t(document).on("click",".tf-single-tour-pricing .tf-price-tab li",(function(){var e=t(this).attr("id");t(this).addClass("active").siblings().removeClass("active"),t(".tf-price").addClass("tf-d-n"),t("."+e+"-price").removeClass("tf-d-n")})),t(".tf-single-tour-pricing .tf-price-tab li:first-child").trigger("click"),t(document).on("click",".tf-trip-person-info ul li",(function(){var e=t(this).attr("data");t(this).addClass("active").siblings().removeClass("active"),t(".tf-trip-pricing").removeClass("active"),t(".tf-"+e).addClass("active")}));const l=()=>{var e=t("#tf-place").val(),a=t("#adults").val(),n=t("#room").val(),o=t("#children").val(),r=t("#check-in-out-date").val(),l=t('.widget_tf_price_filters input[name="from"]').val(),c=t('.widget_tf_price_filters input[name="to"]').val(),f=t("#tf_author").val(),d=r.split(" - "),u=d[0],p=d[1],m=t(".tf-post-type").val();if(""===t.trim(u)&&tf_params.date_hotel_search&&"tf_hotel"===m)0===t("#tf-required").length&&t(".tf_booking-dates .tf_label-row").append('<span id="tf-required" class="required" style="color:white;"><b>'+tf_params.field_required+"</b></span>");else if(""===t.trim(u)&&tf_params.date_tour_search&&"tf_tours"===m)0===t("#tf-required").length&&t(".tf_booking-dates .tf_label-row").append('<span id="tf-required" class="required" style="color:white;"><b>'+tf_params.field_required+"</b></span>");else{var h=[];t("[name*=tf_filters]").each((function(){t(this).is(":checked")&&h.push(t(this).val())})),h=h.join();var _=[];t("[name*=tf_features]").each((function(){t(this).is(":checked")&&_.push(t(this).val())})),_=_.join();var v=[];t("[name*=tour_features]").each((function(){t(this).is(":checked")&&v.push(t(this).val())})),v=v.join();var g=[];t("[name*=tf_attractions]").each((function(){t(this).is(":checked")&&g.push(t(this).val())})),g=g.join();var k=[];t("[name*=tf_activities]").each((function(){t(this).is(":checked")&&k.push(t(this).val())})),k=k.join();var b=new FormData;b.append("action","tf_trigger_filter"),b.append("type",m),b.append("dest",e),b.append("adults",a),b.append("room",n),b.append("children",o),b.append("checkin",u),b.append("checkout",p),b.append("filters",h),b.append("features",_),b.append("tour_features",v),b.append("attractions",g),b.append("activities",k),b.append("checked",r),l&&b.append("startprice",l),c&&b.append("endprice",c),f&&b.append("tf_author",f),s&&4!=s.readyState&&s.abort(),s=t.ajax({type:"post",url:tf_params.ajax_url,data:b,processData:!1,contentType:!1,beforeSend:function(e){t(".archive_ajax_result").block({message:null,overlayCSS:{background:"#fff",opacity:.5}}),t("#tf_ajax_searchresult_loader").show(),""!==t.trim(u)&&t(".tf_booking-dates .tf_label-row").find("#tf-required").remove()},complete:function(e){if(t(".archive_ajax_result").unblock(),t("#tf_ajax_searchresult_loader").hide(),t(".tf-nothing-found")[0]){t(".tf_posts_navigation").hide();var a=t(".tf-nothing-found").data("post-count");t(".tf-total-results").find("span").html(a)}else{t(".tf_posts_navigation").show();var i=t(".tf-posts-count").html();t(".tf-total-results").find("span").html(i)}},success:function(e,a){t(".archive_ajax_result").unblock(),t("#tf_ajax_searchresult_loader").hide(),t(".archive_ajax_result").html(e),i.success(tf_params.ajax_result_success)},error:function(t){console.log(t)}})}};t(document).on("click",".tf_posts_ajax_navigation a.page-numbers",(function(e){var a;e.preventDefault(),page=((a=t(this).clone()).find("span").remove(),parseInt(a.html())),c(page)}));const c=e=>{var a=t("#tf-place").val(),n=t("#adults").val(),o=t("#room").val(),r=t("#children").val(),l=t("#check-in-out-date").val(),c=t('.widget_tf_price_filters input[name="from"]').val(),f=t('.widget_tf_price_filters input[name="to"]').val(),d=t("#tf_author").val(),u=l.split(" - "),p=u[0],m=u[1],h=t(".tf-post-type").val(),_=[];t("[name*=tf_filters]").each((function(){t(this).is(":checked")&&_.push(t(this).val())})),_=_.join();var v=[];t("[name*=tf_features]").each((function(){t(this).is(":checked")&&v.push(t(this).val())})),v=v.join();var g=[];t("[name*=tour_features]").each((function(){t(this).is(":checked")&&g.push(t(this).val())})),g=g.join();var k=[];t("[name*=tf_attractions]").each((function(){t(this).is(":checked")&&k.push(t(this).val())})),k=k.join();var b=[];t("[name*=tf_activities]").each((function(){t(this).is(":checked")&&b.push(t(this).val())})),b=b.join();var w=new FormData;w.append("action","tf_trigger_filter"),w.append("type",h),w.append("page",e),w.append("dest",a),w.append("adults",n),w.append("room",o),w.append("children",r),w.append("checkin",p),w.append("checkout",m),w.append("filters",_),w.append("features",v),w.append("tour_features",g),w.append("attractions",k),w.append("activities",b),w.append("checked",l),c&&w.append("startprice",c),f&&w.append("endprice",f),d&&w.append("tf_author",d),s&&4!=s.readyState&&s.abort(),s=t.ajax({type:"post",url:tf_params.ajax_url,data:w,processData:!1,contentType:!1,beforeSend:function(e){t(".archive_ajax_result").block({message:null,overlayCSS:{background:"#fff",opacity:.5}}),""!==t.trim(p)&&t(".tf_booking-dates .tf_label-row").find("#tf-required").remove()},complete:function(e){if(t(".archive_ajax_result").unblock(),t(".tf-nothing-found")[0]){t(".tf_posts_navigation").hide();var a=t(".tf-nothing-found").data("post-count");t(".tf-total-results").find("span").html(a)}else{t(".tf_posts_navigation").show();var i=t(".tf-posts-count").html();t(".tf-total-results").find("span").html(i)}},success:function(e,a){t(".archive_ajax_result").unblock(),t(".archive_ajax_result").html(e),i.success(tf_params.ajax_result_success)},error:function(t){console.log(t)}})};t(document).on("submit","#tf-widget-booking-search",(function(t){t.preventDefault(),l()})),t(document).on("change","[name*=tf_filters],[name*=tf_features],[name*=tour_features],[name*=tf_attractions],[name*=tf_activities]",(function(){l()})),t(document).on("submit",".tf_archive_search_result",(function(t){t.preventDefault(),l()})),t.fn.inViewport=function(a){return this.each((function(i,n){function s(){var e=t(this).height(),i=n.getBoundingClientRect(),s=i.top,o=i.bottom;return a.call(n,Math.max(0,s>0?e-s:o<e?o:e))}s(),t(e).on("resize scroll",s)}))},t(window).load((function(){jQuery("[data-width]").each((function(){var t=jQuery(this),e=t.attr("data-width");t.inViewport((function(a){a>0?t.css("width",+e+"%"):t.css("width","0%")}))}))})),t('.share-toggle[data-toggle="true"]').click((function(e){e.preventDefault();var a=t(this).attr("href");t(a).slideToggle("fast")})),t("button#share_link_button").click((function(){t(this).addClass("copied"),setTimeout((function(){t("button#share_link_button").removeClass("copied")}),3e3),t(this).parent().find("#share_link_input").select(),document.execCommand("copy")})),t(".tf-slider-items-wrapper,.tf-slider-activated").slick({dots:!0,arrows:!1,infinite:!0,speed:300,autoplaySpeed:2e3,slidesToShow:3,slidesToScroll:1,responsive:[{breakpoint:1024,settings:{slidesToShow:3,slidesToScroll:1,infinite:!0,dots:!0}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:1}},{breakpoint:480,settings:{slidesToShow:1,slidesToScroll:1}}]}),t(".tf-review-items-wrapper").slick({dots:!0,arrows:!1,infinite:!0,speed:300,autoplay:!0,autoplaySpeed:2e3,slidesToShow:4,slidesToScroll:1,responsive:[{breakpoint:1024,settings:{slidesToShow:4,slidesToScroll:1,infinite:!0,dots:!0}},{breakpoint:600,settings:{slidesToShow:3,slidesToScroll:1}},{breakpoint:480,settings:{slidesToShow:2,slidesToScroll:1}}]});const f="wishlist_item",d=()=>{let t=localStorage.getItem(f);return null===t?[]:JSON.parse(t)},u=()=>{let e=t(".tf-wishlist-holder");t.each(e,(function(e,a){let i=t(a).data("type");i=i?i.split(","):void 0;let n=d();void 0!==i&&(n=n.filter((t=>i.includes(t.type))));let s=n.map((t=>t.post)),o={nonce:t(a).data("nonce"),action:"tf_generate_table",ids:s};t.post(tf_params.ajax_url,o,(function(e){e.success&&t(a).html(e.data)}))}))},p=t=>{t.addClass("remove-wishlist"),t.addClass("fa-heart"),t.addClass("tf-text-red"),t.removeClass("fa-heart-o"),t.removeClass("add-wishlist")},m=t=>{t.addClass("add-wishlist"),t.addClass("fa-heart-o"),t.removeClass("fa-heart"),t.removeClass("tf-text-red"),t.removeClass("remove-wishlist")};function h(t,e){var a;function i(t){if(!t)return!1;!function(t){for(var e=0;e<t.length;e++)t[e].classList.remove("autocomplete-active")}(t),a>=t.length&&(a=0),a<0&&(a=t.length-1),t[a].classList.add("autocomplete-active")}function n(e){for(var a=document.getElementsByClassName("autocomplete-items"),i=0;i<a.length;i++)e!=a[i]&&e!=t&&a[i].parentNode.removeChild(a[i])}t.addEventListener("input",(function(i){var s,o,r=this.value;if(n(),!r)return!1;a=-1,(s=document.createElement("DIV")).setAttribute("id",this.id+"autocomplete-list"),s.setAttribute("class","autocomplete-items"),this.parentNode.appendChild(s);var l=[];for(const[a,i]of Object.entries(e))i.substr(0,r.length).toUpperCase()==r.toUpperCase()?(l.push("found"),(o=document.createElement("DIV")).innerHTML="<strong>"+i.substr(0,r.length)+"</strong>",o.innerHTML+=i.substr(r.length),o.innerHTML+=`<input type='hidden' value="${i}" data-slug='${a}'> `,o.addEventListener("click",(function(e){let a=this.getElementsByTagName("input")[0];console.log(a.dataset.slug),t.value=a.value,t.closest("input").nextElementSibling.value=a.dataset.slug,n()})),s.appendChild(o)):l.push("notfound");-1==l.indexOf("found")&&((o=document.createElement("DIV")).innerHTML+=tf_params.no_found,o.innerHTML+="<input type='hidden' value=''>",o.addEventListener("click",(function(e){t.value=this.getElementsByTagName("input")[0].value,n()})),s.appendChild(o))})),t.addEventListener("keydown",(function(t){var e=document.getElementById(this.id+"autocomplete-list");e&&(e=e.getElementsByTagName("div")),40==t.keyCode?(a++,i(e)):38==t.keyCode?(a--,i(e)):13==t.keyCode&&(t.preventDefault(),a>-1&&e&&e[a].click())})),document.addEventListener("click",(function(t){n(t.target)}))}t(document).on("click",".add-wishlist",(function(){let e=t(".add-wishlist"),a={type:e.data("type"),post:e.data("id")};return e.data("page-title"),e.data("page-url"),t("body").hasClass("logged-in")?(a.action="tf_add_to_wishlists",a.nonce=e.data("nonce"),t.ajax({type:"post",url:tf_params.ajax_url,data:a,beforeSend:function(t){i.success(tf_params.wishlist_add)},success:function(t){t.success&&(p(e),i.success({message:t.data,duration:4e3}))}})):!0===(t=>{let e=d();return 0===e.filter((e=>e.post==t.post)).length&&(e.push(t),localStorage.setItem(f,JSON.stringify(e)),!0)})(a)?(i.success(tf_params.wishlist_add),p(e),i.success({message:tf_params.wishlist_added,duration:4e3})):i.error(tf_params.wishlist_add_error),!1})),t("body").find(".tf-wishlist-holder").length&&u(),t(document).on("click",".remove-wishlist",(function(){let e=t(".remove-wishlist"),a=e.data("id");if(t("body").hasClass("logged-in")){let n=e.closest("table"),s={id:a,action:"tf_remove_wishlist",type:n.data("type"),nonce:e.data("nonce")};t.get(tf_params.ajax_url,s,(function(t){t.success&&("1"!=tf_params.single&&n.closest(".tf-wishlists").html(t.data),m(e),i.success(tf_params.wishlist_removed))}))}else 1==(t=>{let e=d(),a=e.findIndex((e=>e.post==t));return console.log(a,t,e),a>=0&&(e.splice(a,1),console.log(e),localStorage.setItem(f,JSON.stringify(e)),"1"!=tf_params.single&&u(),!0)})(a)?(m(e),i.success(tf_params.wishlist_removed)):i.error(tf_params.wishlist_remove_error)})),(()=>{if(!t(document).hasClass("logged-in")&&t(document).find(".add-wishlist")){let e=t(".add-wishlist"),a=e.data("id");d().findIndex((t=>t.post==a))>=0?p(e):m(e)}})();var _=document.getElementById("tf-location"),v=tf_params.locations;_&&h(_,v);var g,k=document.getElementById("tf-destination"),b=tf_params.tour_destinations;k&&h(k,b),t(window).scroll((function(){var e=t(".tf-tour-booking-wrap");t(window).scrollTop()>=800?e.addClass("tf-tours-fixed"):e.removeClass("tf-tours-fixed")})),t(document).on("click",".tf_selectperson-wrap .tf_input-inner,.tf_person-selection-wrap .tf_person-selection-inner",(function(){t(".tf_acrselection-wrap").slideToggle("fast")})),jQuery(document).on("click",(function(t){jQuery(t.target).closest(".tf_selectperson-wrap").length||jQuery(".tf_acrselection-wrap").slideUp("fast")})),t(".acr-inc").on("click",(function(e){var a=t(this).parent().find("input");a.val(parseInt(a.val())+1).change()})),t(".acr-dec").on("click",(function(e){var a=t(this).parent().find("input"),i=a.attr("min");a.val()>i&&a.val(a.val()-1).change()})),t(document).on("change","#adults",(function(){let e=t(this),a=e.val();e.closest(".tf_selectperson-wrap").find(".adults-text").text(a+" "+tf_params.adult)})),t(document).on("change","#children",(function(){let e=t(this),a=e.val();e.closest(".tf_selectperson-wrap").find(".child-text").text(a+" "+tf_params.children)})),t(document).on("change","#infant",(function(){let e=t(this),a=e.val();e.closest(".tf_selectperson-wrap").find(".infant-text").text(a+" "+tf_params.infant)})),t(document).on("change","#room",(function(){let e=t(this),a=e.val();e.closest(".tf_selectperson-wrap").find(".room-text").text(a+" "+tf_params.room)})),t(document).on("click","#reply-title",(function(){var e=t(this);t("#commentform").slideToggle("fast","swing",(function(){e.parent().toggleClass("active")}))})),t(document).on("click","#tf-ask-question-trigger",(function(e){e.preventDefault(),t("#tf-ask-question").fadeIn().find(".response").html("")})),t(document).on("click","span.close-aq",(function(){t("#tf-ask-question").fadeOut()})),t(document).on("submit","form#ask-question",(function(e){e.preventDefault();var a=t(this),i=new FormData(this);i.append("action","tf_ask_question"),t.ajax({type:"post",url:tf_params.ajax_url,data:i,processData:!1,contentType:!1,beforeSend:function(t){a.block({message:null,overlayCSS:{background:"#fff",opacity:.5}}),a.find(".response").html(tf_params.sending_ques)},complete:function(t){a.unblock()},success:function(t){a.unblock();var e=JSON.parse(t);"sent"==e.status?(a.find(".response").html(e.msg),a.find('[type="reset"]').trigger("click")):a.find(".response").html(e.msg)},error:function(t){console.log(t)}})})),t(document).on("click",".change-view",(function(e){e.preventDefault(),t(".change-view").removeClass("active"),t(this).addClass("active"),"grid-view"==t(this).data("id")?t(".archive_ajax_result").addClass("tours-grid"):t(".archive_ajax_result").removeClass("tours-grid")})),t(document).on("click",".tf-grid-list-layout",(function(e){e.preventDefault(),t(".tf-grid-list-layout").removeClass("active"),t(this).addClass("active"),"grid-view"==t(this).data("id")?(t(".tf-item-cards").addClass("tf-layout-grid"),t(".tf-item-cards").removeClass("tf-layout-list")):(t(".tf-item-cards").addClass("tf-layout-list"),t(".tf-item-cards").removeClass("tf-layout-grid"))})),t(document).on("click",".tf_posts_page_navigation a.page-numbers",(function(e){e.preventDefault();var a,i=e.target.href?e.target.href:t(this).context.href;a=i,g&&4!=g.readyState&&g.abort(),g=t.ajax({url:a,contentType:!1,processData:!1,asynch:!0,beforeSend:function(){t(document).find(".tf_posts_navigation").addClass("loading"),t(document).find(".archive_ajax_result").addClass("loading")},success:function(e){t(".archive_ajax_result").html(t(".archive_ajax_result",e).html()),t(".tf_posts_navigation").html(t(".tf_posts_navigation",e).html()),t(document).find(".tf_posts_navigation").removeClass("loading"),t(document).find(".archive_ajax_result").removeClass("loading")}}),window.history.pushState({url:""+i},"",i)})),t(".tf_selectdate-wrap.tf_more_info_selections .tf_input-inner").click((function(){t(".tf-more-info").toggleClass("show")}));let w={range:{min:parseInt(tf_params.tf_hotel_min_price),max:parseInt(tf_params.tf_hotel_max_price),step:1},initialSelectedValues:{from:parseInt(tf_params.tf_hotel_min_price),to:parseInt(tf_params.tf_hotel_max_price)/2},grid:!1,theme:"dark"};0!=tf_params.tf_hotel_min_price&&0!=tf_params.tf_hotel_max_price&&t(".tf-hotel-filter-range").alRangeSlider(w);var y=new window.URLSearchParams(window.location.search);let C={range:{min:parseInt(tf_params.tf_hotel_min_price),max:parseInt(tf_params.tf_hotel_max_price),step:1},initialSelectedValues:{from:y.get("from")?y.get("from"):parseInt(tf_params.tf_hotel_min_price),to:y.get("to")?y.get("to"):parseInt(tf_params.tf_hotel_max_price)/2},grid:!1,theme:"dark",onFinish:function(){l()}};0!=tf_params.tf_hotel_min_price&&0!=tf_params.tf_hotel_max_price&&t(".tf-hotel-result-price-range").alRangeSlider(C);let x={range:{min:parseInt(tf_params.tf_tour_min_price),max:parseInt(tf_params.tf_tour_max_price),step:1},initialSelectedValues:{from:parseInt(tf_params.tf_tour_min_price),to:parseInt(tf_params.tf_tour_max_price)/2},grid:!1,theme:"dark"};0!=tf_params.tf_tour_min_price&&0!=tf_params.tf_tour_max_price&&t(".tf-tour-filter-range").alRangeSlider(x);let S={range:{min:parseInt(tf_params.tf_tour_min_price),max:parseInt(tf_params.tf_tour_max_price),step:1},initialSelectedValues:{from:y.get("from")?y.get("from"):parseInt(tf_params.tf_tour_min_price),to:y.get("to")?y.get("to"):parseInt(tf_params.tf_tour_max_price)/2},grid:!1,theme:"dark",onFinish:function(){l()}};0!=tf_params.tf_tour_min_price&&0!=tf_params.tf_tour_max_price&&t(".tf-tour-result-price-range").alRangeSlider(S),t("#tf-destination-adv").click((function(e){t(this).val()?t(".tf-hotel-locations").removeClass("tf-locations-show"):t(".tf-hotel-locations").addClass("tf-locations-show")})),t("#tf-destination-adv").keyup((function(e){var a=t(this).val();t("#tf-place-destination").val(a)})),t("#tf-location").keyup((function(e){var a=t(this).val();t("#tf-search-hotel").val(a)})),t(document).on("click",(function(e){t(e.target).closest("#tf-destination-adv").length||t(".tf-hotel-locations").removeClass("tf-locations-show")})),t("#ui-id-1 li").click((function(e){var a=t(this).attr("data-name"),i=t(this).attr("data-slug");t(".tf-preview-destination").val(a),t("#tf-place-destination").val(i),t(".tf-hotel-locations").removeClass("tf-locations-show")})),t("#tf-tour-location-adv").click((function(e){t(this).val()?t(".tf-tour-results").removeClass("tf-destination-show"):t(".tf-tour-results").addClass("tf-destination-show")})),t("#tf-tour-location-adv").keyup((function(e){var a=t(this).val();t("#tf-tour-place").val(a)})),t("#tf-destination").keyup((function(e){var a=t(this).val();t("#tf-search-tour").val(a)})),t(document).on("click",(function(e){t(e.target).closest("#tf-tour-location-adv").length||t(".tf-tour-results").removeClass("tf-destination-show")})),t("#ui-id-2 li").click((function(e){var a=t(this).attr("data-name"),i=t(this).attr("data-slug");t(".tf-tour-preview-place").val(a),t("#tf-tour-place").val(i),t(".tf-tour-results").removeClass("tf-destination-show")})),t(".tf-accordion-head").click((function(){t(this).toggleClass("active"),t(this).parent().find(".arrow").toggleClass("arrow-animate"),t(this).parent().find(".tf-accordion-content").slideToggle(),t(this).siblings().find(".ininerary-other-gallery").slick({slidesToShow:6,slidesToScroll:1,arrows:!0,fade:!1,adaptiveHeight:!0,infinite:!0,useTransform:!0,speed:400,cssEase:"cubic-bezier(0.77, 0, 0.18, 1)",responsive:[{breakpoint:1024,settings:{slidesToShow:4,slidesToScroll:1}},{breakpoint:640,settings:{slidesToShow:2,slidesToScroll:1}},{breakpoint:420,settings:{slidesToShow:2,slidesToScroll:1}}]})})),t(".tf-faq-title").click((function(){var e=t(this);e.hasClass("active")||(t(".tf-faq-desc").slideUp(400),t(".tf-faq-title").removeClass("active"),t(".arrow").removeClass("arrow-animate")),e.toggleClass("active"),e.next().slideToggle(),t(".arrow",this).toggleClass("arrow-animate")})),t(".tf-faq-collaps").click((function(){var e=t(this);e.hasClass("active")||(t(".tf-faq-content").slideUp(400),t(".tf-faq-collaps").removeClass("active"),t(".tf-faq-single").removeClass("active")),e.toggleClass("active"),e.next().slideToggle(),t(this).closest(".tf-faq-single").toggleClass("active")})),t(".tf-itinerary-title").click((function(){var e=t(this);e.hasClass("active")||(t(".tf-itinerary-content-box").slideUp(400),t(".tf-itinerary-title").removeClass("active"),t(".tf-single-itinerary-item").removeClass("active")),e.toggleClass("active"),t(this).closest(".tf-single-itinerary-item").toggleClass("active"),e.next().slideToggle()})),t(".tf-form-title.tf-tour-extra").click((function(){var e=t(this);e.hasClass("active")||(t(".tf-tour-extra-box").slideUp(400),t(".tf-form-title.tf-tour-extra").removeClass("active")),e.toggleClass("active"),e.next().slideToggle()})),t(window).on("load",(function(){t(".tf-tablinks").length>0&&t(".tf-tablinks").first().trigger("click").addClass("active")})),t(document).on("click",".tf-tablinks",(function(e){let i=t(this).data("form-id");a(event,i)})),t(document).on("change",'select[name="tf-booking-form-tab-select"]',(function(){var e=t(this).val();a(event,e)})),t(document).on("keyup",".tf-hotel-side-booking #tf-location, .tf-hotel-side-booking #tf-destination",(function(){let e=t(this).val();t(this).next("input[name=place]").val(e)})),t(".child-age-limited")[0]&&(t(".acr-select .child-inc").on("click",(function(){var e=t('div[id^="tf-age-field-0"]'),a=t('div[id^="tf-age-field-"]:last');if(0!=a.length)var i=parseInt(a.prop("id").match(/\d+/g),10)+1;var n=a.clone().prop("id","tf-age-field-"+i);n.find("label").html("Child age "+i),n.find("select").attr("name","children_ages[]"),a.after(n),n.show(),e.hide()})),t(".acr-select .child-dec").on("click",(function(){var e=t(".tf-children-age").length,a=t('div[id^="tf-age-field-"]:last');1!=e&&a.remove()})));var j=t(".tf-posts-count").html();t(".tf-total-results").find("span").html(j),t(".tf-widget-title").on("click",(function(){t(this).find("i").toggleClass("collapsed"),t(this).siblings(".tf-filter").slideToggle("medium")})),t("a.see-more").on("click",(function(e){var a=t(this);e.preventDefault(),a.parent(".tf-filter").find(".filter-item").filter((function(t){return t>3})).removeClass("hidden"),a.hide(),a.parent(".tf-filter").find(".see-less").show()})),t("a.see-less").on("click",(function(e){var a=t(this);e.preventDefault(),a.parent(".tf-filter").find(".filter-item").filter((function(t){return t>3})).addClass("hidden"),a.hide(),a.parent(".tf-filter").find(".see-more").show()})),t(".tf-filter").each((function(){var e=t(this).find("ul").children().length;t(this).find(".see-more").hide(),e>4&&t(this).find(".see-more").show(),t(this).find(".filter-item").filter((function(t){return t>3})).addClass("hidden")})),t(".tf_widget input").on("click",(function(){t(this).parent().parent().toggleClass("active")})),t("form.checkout").on("click",".cart_item a.remove",(function(e){e.preventDefault();var a=t(this).attr("data-cart_item_key");t.ajax({type:"POST",url:tf_params.ajax_url,data:{action:"tf_checkout_cart_item_remove",cart_item_key:a},beforeSend:function(){t("body").trigger("update_checkout")},success:function(e){t("body").trigger("update_checkout")},error:function(t){}})})),t(document).on("submit","#tf_hotel_aval_check",(function(e){e.preventDefault();let a=t(this),n=a.find(".tf-submit"),s=new FormData(a[0]);s.append("action","tf_hotel_search"),t.ajax({url:tf_params.ajax_url,type:"POST",data:s,contentType:!1,processData:!1,beforeSend:function(){a.css({opacity:"0.5","pointer-events":"none"}),n.addClass("tf-btn-loading")},success:function(t){let e=JSON.parse(t);a.css({opacity:"1","pointer-events":"all"}),n.removeClass("tf-btn-loading"),"error"===e.status&&i.error(e.message),"success"===e.status&&(location.href=a.attr("action")+"?"+e.query_string)}})})),t(document).on("submit","#tf_tour_aval_check",(function(e){e.preventDefault();let a=t(this),n=a.find(".tf-submit"),s=new FormData(a[0]);s.append("action","tf_tour_search"),t.ajax({url:tf_params.ajax_url,type:"POST",data:s,contentType:!1,processData:!1,beforeSend:function(){a.css({opacity:"0.5","pointer-events":"none"}),n.addClass("tf-btn-loading")},success:function(t){let e=JSON.parse(t);a.css({opacity:"1","pointer-events":"all"}),n.removeClass("tf-btn-loading"),"error"===e.status&&i.error(e.message),"success"===e.status&&(location.href=a.attr("action")+"?"+e.query_string)}})})),t(document).on("click","#featured-gallery",(function(e){e.preventDefault(),t("#tour-gallery").trigger("click")})),t(document).on("click",".tf-tabs-control",(function(e){e.preventDefault();let a=t(this).attr("data-step");if(a>1){for(let e=1;e<=a;e++)t(".tf-booking-step-"+e).removeClass("active"),t(".tf-booking-step-"+e).addClass("done");t(".tf-booking-step-"+a).addClass("active"),t(".tf-booking-content").hide(),t(".tf-booking-content-"+a).show()}})),t(document).on("click",".tf-step-back",(function(e){e.preventDefault();let a=t(this).attr("data-step");1==a&&(t(".tf-booking-step").removeClass("active"),t(".tf-booking-step").removeClass("done"),t(".tf-booking-step-"+a).addClass("active"),t(".tf-booking-content").hide(),t(".tf-booking-content-"+a).show())}))}))})();