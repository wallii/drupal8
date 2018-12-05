(function ($, Drupal) {
 /*------------------------------------ Getting Video Data From YouTube/Transcode API ------------------------------------*/
	Drupal.behaviors.zeesalaamAdmin = {
		attach: function (context, settings) {
			var pause = 0; 
			function MaxLength(field, eventRef, limit) {
				console.log("function call");
				var fid = (field.id);				
				if(typeof (eventRef)=='undefined'){
					console.log("get");
					eventRef = window.eventRef;
				}
				console.log(eventRef);
				
				var str = $(field).val();
				var s = str.length;	
				console.log("s: " + s);
			
				str = "(Maximum Char: "+ limit + ", Remaining: " + (limit - parseInt(s))+ ")";
				var keyID = eventRef.keyCode ? eventRef.keyCode : ((eventRef.charCode) ? eventRef.charCode : eventRef.which);
				console.log("keyID:" + keyID);
				if(keyID==8 || keyID==46 || keyID==38 || keyID==37 || keyID==39 || keyID==40 || keyID==16 || keyID==20){
					pause = 0;
					if(s>limit) str = "(LIMIT EXCEEDED)";
					
				}else{
					if(s==limit)pause = 1;
					else if(s>limit){pause = 1; str = "(LIMIT EXCEEDED)";}
					else pause = 0;
				}
				
				return(str);
				
			}
			$(document).ready(function(){
				var keyPressed;
				setTimeout(function(){
					$(".maxlen").each(function() {
						var sting  = $(this);
						if($(sting)[0].tagName=='TEXTAREA'){
							$(sting).parent().parent().children().find('.counter').html(MaxLength($(sting), $(sting).trigger( "click" ), $(sting).attr('maxlength')));
						}else if($(sting)[0].tagName=='INPUT'){
							$(sting).parent().children().find('.counter').html(MaxLength($(sting), $(sting).trigger( "click" ), $(sting).attr('maxlength')));
						}
					});
				}, 1000);
				$('.maxlen').keypress(function(event){	
					keyPressed = event;					
					if($(this)[0].tagName=='TEXTAREA'){
							$(this).parent().parent().children().find('.counter').html(MaxLength($(this), keyPressed, $(this).attr('maxlength')));
					}else if($(this)[0].tagName=='INPUT'){
						$(this).parent().children().find('.counter').html(MaxLength($(this), keyPressed, $(this).attr('maxlength')));
					}
					if(pause==1) return false;
				});
				$('.maxlen').keyup(function(event){
					keyPressed = event;
			
					if($(this)[0].tagName=='TEXTAREA'){
						$(this).parent().parent().children().find('.counter').html(MaxLength($(this), keyPressed, $(this).attr('maxlength')));
					}else if($(this)[0].tagName=='INPUT'){
						$(this).parent().children().find('.counter').html(MaxLength($(this), keyPressed, $(this).attr('maxlength')));
					}if(pause==1) return false;
								
				});
				$('.maxlen').keydown(function(event){
					keyPressed = event;
					if($(this)[0].tagName=='TEXTAREA'){
						$(this).parent().parent().children().find('.counter').html(MaxLength($(this), keyPressed, $(this).attr('maxlength')));
					}else if($(this)[0].tagName=='INPUT'){
						$(this).parent().children().find('.counter').html(MaxLength($(this), keyPressed, $(this).attr('maxlength')));
					}if(pause==1) return false;
									
				});
				
			});
			/*
			  Code For: Hide Published later if content already published
			*/
			 /* if($("input[name='status[value]']").once().attr('checked', true)) {
				  $("#edit-field-publish-later--wrapper").hide();
			  } */
			/*
			  End Code
			*/
			$('#edit-field-yt-code-wrapper').once().append('<input type="button" id="getvideo_data" name="get_video_data" value="Get Video" class="button"><div class="clearfix"></div><div id="video_preview"></div><div class="clearfix"></div><div id="videothumbnailspreview"></div><div><input type="hidden" id="thumbimageURL" name="thumbimageURL"></div><div class="clearfix"></div><div id="divSelectVideoImage"></div><div class="clearfix"></div>');
		    /* function for get video data on click   */
			// fire click event
			//$('#getvideo_data').trigger('click');
			$("select[name='field_isyoutube']").once().change(function(){
                if(this.value==1){
                    jwplayer('video_preview').remove();
                    $('#edit-field-yt-code-0-value').val('');
                    $('#edit-field-video-featured-image-url-0-value').val('');
                    $('#edit-field-video-duration-0-value').val('');
                    $('#videothumbnailspreview').html('');
                    $('#divSelectVideoImage').html('');
                }else{
                    $('#edit-field-yt-code-0-value').val('');
                    $('#edit-field-video-featured-image-url-0-value').val('');
                    $('#edit-field-video-duration-0-value').val('');
                    $('#video_preview').html('');
                    $('#videothumbnailspreview').html('');
                    $('#divSelectVideoImage').html('');
                }
                
            });
			$("input[name='field_publish_later']").once().change(function(){
               
               if(this.value == 0) {
				  $("input[name='status[value]']").attr('checked', false); 
			   } else {
				  $("input[name='status[value]']").attr('checked', true); 
			   }
            });
			//#################
			$("input[name='field_publish_later']").once().change(function(){
               
               if(this.value == 0) {
				  $("input[name='status[value]']").attr('checked', false); 
			   } else {
				  $("input[name='status[value]']").attr('checked', true); 
			   }
            });
			$("#getvideo_data").once().on('click', function () {
				var videourldata = $.trim($('#edit-field-yt-code-0-value').val());
				var isYoutubeData = $.trim($('#edit-field-isyoutube option:selected').val());
				if(isYoutubeData == '_none' || isYoutubeData =='') {
					$('#edit-field-isyoutube').css('border-color', '#C80000');
				    
				}else if(videourldata == '') {
					$('#video_preview').html('Please Enter YT Code');
					$('#edit-field-yt-code-0-value').css('border-color', 'red');
				} else {
					$('#edit-field-yt-code-0-value').css('border-color', '');
					var video_type = $('#edit-field-isyoutube option:selected').val();
					if(video_type == 1){
						//YouTube Video bImTB8omR0Y
						var v_iframe_id = $.trim($('#edit-field-yt-code-0-value').val());
						var youtubeApi = "AIzaSyDlOaSOXEHxUfBDtGJL_ljPSGkGPh51OVM";
						$.ajax({
							url: "https://www.googleapis.com/youtube/v3/videos?id=" + v_iframe_id + "&key=" + youtubeApi + "&fields=items(snippet(title,thumbnails,description),contentDetails(duration))&part=snippet,contentDetails",
							dataType: "json",
							async: true,
							success: function (data) {
								console.log(data);
								if(data.items.length != 0 ){
									var v_iframe = "<iframe width='560' height='315' src='https://www.youtube.com/embed/" + v_iframe_id +"' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen></iframe>";
									$( "#video_preview" ).html(v_iframe);
									//getThumbnail();
									$( "#videothumbnailspreview" ).html("<img src="+data.items[0].snippet.thumbnails.high.url+" width='480' height='360'>");
									$( "#thumbimageURL" ).val(data.items[0].snippet.thumbnails.high.url);
									getDuration(data);
								}else{
									$( "#videothumbnailspreview" ).html("");
									$( "#thumbimageURL" ).val("");
									$('#divSelectVideoImage').html(' <p>Please Enter Valid Youtube Video ID. </p>');
									return false;		

								}
							},
							error: function (err) {
								$( "#videothumbnailspreview" ).html("");
								$( "#edit-field-video-featured-image-url-0-value" ).val("");
								$('#divSelectVideoImage').html(' <p>Please Enter Valid Youtube Video ID. </p>');
								return false;
							}
						});
					
					} else if(video_type == 0) {
						// transcode video : hardik_fast_9day
						var tanscodeAPI = "http://transcoding.zeenews.com/get_info/";
						var transcodeId = $.trim($('#edit-field-yt-code-0-value').val());
						var txtTranscodeAPIURL = tanscodeAPI + transcodeId;
							$.ajax({
							url: txtTranscodeAPIURL,
							type: "GET",
							dataType: "json",
							async: false,
							success: function (result) {
							$('#video_preview').html('');
							$('#videothumbnailspreview').html('');
							$('#divSelectVideoImage').html('');
							if (result.title != null) {
							$("#edit-title-0-value").val(result.title);

							} else {
							$("#edit-title-0-value").val('');
							}
							if (result.description != null) {
							CKEDITOR.instances['edit-body-0-value'].setData(result.description);
							}
							else {
							//CKEDITOR.instances['edit-body-0-value'].setData('');
							}
							BindVideoPreview(result.url_video, result.url_trickplay);
							console.log(result.thumbnails);
					
							BindHtml(result.thumbnails);
							$("input[name='rbVideoImage']").change(function(){
							$( "#thumbimageURL" ).val(this.value);
							});
							},
							error: function (err) {
							$('#video_preview').html('');
							$('#videothumbnailspreview').html('');
							$('#divSelectVideoImage').html(' <p>Please Enter Valid Transcode Video ID. </p>');
							return false;
							}
							});
						
					}
				}
				 
				
			});
			function getDuration(data1) {
				var data = data1;
				var videoid = $.trim($('#edit-field-yt-code-0-value').val());
				var youtubeApi = "AIzaSyDlOaSOXEHxUfBDtGJL_ljPSGkGPh51OVM";
				
				
				$("#edit-title-0-value").val(data.items[0].snippet.title);
				//$('input[id$="hddescription"]').val(data.items[0].snippet.description);
				str = data.items[0].contentDetails.duration;
				str = data.items[0].contentDetails.duration.replace("PT", "");
				var hour = "";
				var minute = "";
				var sec = "";
				if (str.indexOf("H") != -1) {
				str = str.replace("H", ":");
				}
				else {
				str = '00:' + str;
				}
				if (str.indexOf("M") != -1) {
				str = str.replace("M", ":");
				}
				else {
				str = str.replace(":", ":00:");
				}

				if (str.indexOf("S") != -1) {
				str = str.replace("S", "");
				}
				else {
				str = str + "00";

				}

				if (str.split(':')[0].length == 1) {

				hour = "0" + str.split(':')[0];
				}

				else {
				hour = str.split(':')[0];

				}


				if (str.split(':')[1].length == 1) {

				minute = "0" + str.split(':')[1];
				}
				else {
				minute = str.split(':')[1];
				}
				if (str.split(':')[2].length == 1) {

				sec = "0" + str.split(':')[2];
				}
				else {

				sec = str.split(':')[2];
				}

				var duration = hour + ":" + minute + ":" + sec;
				var description =  data.items[0].snippet.description
				if (description != null) {
				CKEDITOR.instances['edit-body-0-value'].setData(description);
				}
				else {
				CKEDITOR.instances['edit-body-0-value'].setData('');
				}
				$( "#edit-field-video-duration-0-value" ).val(duration);
			
			
			}
			function BindVideoPreview(videourl, trickplayurl) {
                jwplayer("video_preview").setup({
                    primary: "flash",
                    playlist: [{
                        file: videourl,
                        tracks: [{
                            file: trickplayurl,
                            kind: "thumbnails"
                        }]
                    }],
                    width: 550,
                    height: 315
                });
            }
			function BindHtml(result) {
				//alert(result);
				var thumbnailsList = result;
				var html = "<br> <b>Video Thumbnail <span style='color:red'>*</span>:</b><br>";
				var i = 0;
				$(thumbnailsList).each(function () {
					if( i == 0){
						var selected = "checked";
						$( "#thumbimageURL" ).val(this);		
					
                    } else {
						var selected = "";
					} 
					html += "<div class='col-sm-3 popuphead'><input type='radio' name='rbVideoImage' id='thumbid'  value=" + this + "  class='pull-left' " + selected + "><div class='thumbnail'><div class='image view view-first'><div class='category-thumb-icon'><i class='fa fa-camera'></i> </div><img class='img-responsive'  width='480' height='360' alt='' src='" + this + "'></div></div></div></div>";
					i++;
					
				});
				$('#videothumbnailspreview').html(html);
            }
		}
		 
		
	}
})(jQuery, Drupal);	