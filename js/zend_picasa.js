(function($){
	$('<div class="wfsr2"></div>').appendTo('body');
})(jQuery);


// 링크 이미지가 제대로 보이도록
// xe/modules/editor/tpl/js/upload.js 의 함수를 수정하였음

function previewFiles(a,b){var c,d,e,f,g,h,i=jQuery;if(!b){if(c=i(a.target).parent().addBack().filter("select").find(">option:selected"),!c.length)return;b=c.attr("value")}b&&is_def(e=uploadedFiles[b])&&(d=i("#"+e.previewAreaID).html("&nbsp;"),d.length&&(f=e.download_url||"",g=f.match(/\.(?:(flv)|(swf)|(wmv|avi|mpe?g|as[fx]|mp3)|(jpe?g|png|gif))$/i),"Y"==e.direct_download&&g?g[1]?h='<embed src="'+request_uri+"common/img/flvplayer.swf?autoStart=false&file="+uploaded_filename+'" width="100%" height="100%" type="application/x-shockwave-flash" />':g[2]?h='<embed src="'+request_uri+f+'" width="100%" height="100%" type="application/x-shockwave-flash"  />':g[3]?h='<embed src="'+request_uri+f+'" width="100%" height="100%" autostart="true" showcontrols="0" />':g[4]&&(h=get_code_img(f)):h='<img src="'+request_uri+'modules/editor/tpl/images/files.gif" border="0" width="100%" height="100%" />',h&&d.html(h)))}

function get_code_img(filename){
		// 첨부파일이 http로 시작하면 도메인주소값을 비움

		matchs = filename.match(/\/\/lh.*?google.*\.com\/(?:-\w).*\/?(\/.*?\/)/i);
		//alert(JSON.stringify(match)); 
		//피카사 이미지와 일반이미지를 미리보기 박스에 표시
		if(matchs) {
			filename = filename.replace(matchs[1],'/w70-h70-c/');
			html = '<img src="'+filename+'" border="0" width="100%" height="100%" />';
		} else if(filename.match(/http[s]?:\/\//gi)) {
			html = '<img src="'+filename+'" border="0" width="100%" height="100%" />';
		} else {
			html = '<img src="'+request_uri+filename+'" border="0" width="100%" height="100%" />';
		}
		return html;
}

/**
 * @author NAVER (developers@xpressengine.com)
 * @version 0.1.1
 * @brief 파일 업로드 관련
 **/
var uploadedFiles    = [];
var uploaderSettings = [];
var loaded_images    = [];
var swfUploadObjs    = [];
var uploadSettingObj = [];
var uploadAutosaveChecker = false;

var selected_files;
var processing_files = 1;


/**
 * 업로드를 하기 위한 준비 시작
 * 이 함수는 editor.html 에서 파일 업로드 가능할 경우 호출됨
 **/
// window.load 이벤트일 경우 && 문서 번호가 가상의 번호가 아니면 기존에 저장되어 있을지도 모르는 파일 목록을 가져옴
(function($){
	var defaultHandlers;

	function init(cfg, exe) {
		var seq = cfg.editorSequence;

		if(!is_def(seq)) return;

		cfg = $.extend({
			sessionName : 'PHPSESSID',
			allowedFileSize  : 2097152, // byte. 2*1024*1024
			allowedFileTypes : '*.*',
			allowedFileTypesDescription : 'All Files',
			replaceButtonID : 'swfUploadButton'+cfg.editorSequence,
			insertedFiles   : 0
		}, cfg);

		uploadSettingObj[seq] = cfg;
		$(function(){ start(cfg); });

	}

	function start(cfg) {
		var $button, width, height, $span, seq, id, settings, handlers, name, fn, swfu, $swf;

		seq     = cfg.editorSequence;
		id      = cfg.replaceButtonID;
		$button = $('#'+id).wrap('<span style="position:relative;display:inline-block" />');
		width   = $button.width();
		height  = $button.height();
		$span   = $('<span id="dummy'+id+'" />').insertAfter($button);

		settings = {
			flash_url   : request_uri + 'modules/editor/tpl/images/SWFUpload.swf',
			upload_url  : request_uri + 'index.php',
			post_params : {
				mid : current_mid,
				act : 'procFileUpload',
				editor_sequence : seq,
				uploadTargetSrl : editorRelKeys[seq].primary.value
			},
			http_success : [302],
			file_size_limit   : Math.floor( (parseInt(cfg.allowedFileSize,10)||1024) / 1024 ),
			file_queue_limit  : 0,
			file_upload_limit : 0,
			file_types : cfg.allowedFileTypes,
			file_types_description : cfg.allowedFileTypesDescription,
			custom_settings : {
				progressTarget : null,
				cancelButtonId : null
			},
			debug : false,

			// Button settings
			button_window_mode : 'transparent',
			button_placeholder_id : $span.attr('id'),
			button_text : null,
			button_image_url : request_uri+'common/img/blank.gif',
			button_width  : width,
			button_height : height,
			button_text_style : null,
			button_text_left_padding : 0,
			button_text_top_padding  : 0,
			button_cursor : -2,

			editorSequence   : seq,
			uploadTargetSrl  : editorRelKeys[seq].primary.value,
			fileListAreaID   : cfg.fileListAreaID,
			previewAreaID    : cfg.previewAreaID,
			uploaderStatusID : cfg.uploaderStatusID
		};

		if(typeof(enforce_ssl)!=="undefined" && enforce_ssl)
		{
			settings.upload_url = request_uri+'index.php';
		}

		// preview
		$('#'+cfg.fileListAreaID).click(previewFiles);

		// The event handler functions are defined in handlers.js
		handlers = {
			file_queued          : 'FileQueued',
			file_queue_error     : 'FileQueueError',
			file_dialog_complete : 'FileDialogComplete',
			upload_start    : 'UploadStart',
			upload_progress : 'UploadProgress',
			upload_error    : 'UploadError',
			upload_success  : 'UploadSuccess',
			upload_complete : 'UploadComplete',
			queue_complete  : 'QueueComplete'
		};

		for(name in handlers) {
			if(!handlers.hasOwnProperty(name)) continue;
			fn = 'on'+handlers[name];
			settings[name+'_handler'] = cfg['on'+fn] || defaultHandlers[fn];
		}

		if(is_def(window.xeVid)) settings.post_params.vid = xeVid;
		settings.sessionName = cfg.sessionName;
		if(getCookie(cfg.sessionName)) settings.post_params[cfg.sessionName] = getCookie(cfg.sessionName);

		uploaderSettings[seq] = settings;

		swfu = new SWFUpload(settings);
		$swf = $('#'+swfu.movieName);
		swfUploadObjs[seq] = swfu.movieName;
		if(!$swf.length) return;

		$swf.css({
			display  : 'block',
			cursor   : 'pointer',
			position : 'absolute',
			left     : 0,
			top      : 0,
			width    : width + 'px',
			height   : height + 'px'
		});

		if(cfg.insertedFiles || editorRelKeys[seq].primary.value) reloadFileList(cfg);
	}

	function _true(){ return true; }


	defaultHandlers = {
		onFileQueued : _true,
		onFileQueueError : function(file, errorCode, message) {
			try {
				switch(errorCode) {
					case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED :
						alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
						break;
					case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
						alert("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
						break;
					case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
						alert("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
						break;
					case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
						alert("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
						break;
					default:
						alert("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
						break;
				}
			} catch(e) {
				alert(JSON.stringify(ex));
				this.debug(e);
			}
		},
		onFileDialogComplete : function(numFilesSelected, numFilesQueued) {
			try {
				jQuery(".wfsr2").show();
				//alert(JSON.stringify(numFilesQueued));
				// 업로드할 파일 개수
				selected_files = numFilesSelected;

				if(getCookie(this.settings.sessionName)) this.addPostParam(this.settings.sessionName, getCookie(this.settings.sessionName));
				this.startUpload();
			} catch (e)  {
				this.debug(e);
			}
		},
		onUploadStart : _true,
		onUploadProgress : function(file, bytesLoaded, bytesTotal) {

			//alert(JSON.stringify(this.settings));
			if(getCookie(this.settings.sessionName)) this.addPostParam(this.settings.sessionName, getCookie(this.settings.sessionName));
			try {
				var $list, $lastopt, percent, filename,filefullname;

				$list    = jQuery('#'+this.settings.fileListAreaID);
				percent  = Math.ceil((bytesLoaded / bytesTotal) * 100);
				filename = file.name;
				$lastopt = $list.find('>option:last');
				
				filefullname = filename;

				if(filename.length>20) filename = filename.substr(0,20)+'...';
				if(!$lastopt.length || $lastopt.attr('value') != file.id) {
					$lastopt = jQuery('<option />').attr('value', file.id).appendTo($list);
				}

				
				
				if(percent >= 100) {
					// 피카사로 파일 업로드 진행 상황 표시
					jQuery(".wfsr2").html(filefullname + bytesLoaded + bytesTotal + ' (' + processing_files + ' / ' + selected_files + ')' +'<br> (피카사로 업로드 중입니다.용량이 큰 파일은 시간이 오래 걸립니다.)');
					$lastopt.text(filename + ' (' + percent + '%)');
				} else {
					jQuery(".wfsr2").html(filename + ' (' + percent + '%)');
					$lastopt.text(filename + ' (100%)');
				}


			} catch (e)  {
				alert(JSON.stringify(e));
				this.debug(e);
			}
		},
		onUploadSuccess : function(file, serveData) {
			// 진행중인 파일의 개수
			
			//jQuery(".wfsr").hide().trigger('cancel_confirm');
			try {
				if(this.getStats().files_queued !== 0) {
					processing_files = processing_files + 1;
					this.startUpload();

				} else {
					//alert(JSON.stringify(processing_files +'//'+selected_files));
					jQuery(".wfsr2").hide();
					//processing_files = 1;
				}

				
			} catch (e)  {
				alert(JSON.stringify(e));
				this.debug(e);
			}
		},
		onUploadError : function(file, errorCode, message) {
			try {
				switch (errorCode) {
				case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
					alert("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
					alert("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
					break;
				case SWFUpload.UPLOAD_ERROR.IO_ERROR:
					alert("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
					break;
				case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
					alert("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
					alert("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
					break;
				case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
					alert("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
					break;
				case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
					// If there aren't any files left (they were all cancelled) disable the cancel button
					if (this.getStats().files_queued === 0) {
						document.getElementById(this.customSettings.cancelButtonId).disabled = true;
					}
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
					break;
				default:
					alert("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
					break;
				}
			} catch (ex) {
				alert(JSON.stringify(ex));
				this.debug(ex);
			}
		},
		onUploadComplete : function(file) {
			try {
				var fileListAreaID = this.settings.fileListAreaID;
				var uploadTargetSrl = this.settings.uploadTargetSrl;
				reloadFileList(this.settings);
			} catch(e) {
				alert(JSON.stringify(ex));
				jQuery(".wfsr2").hide();
				this.debug(ex);
			}
		}
	};


	function reloadFileList(cfg) {
		var params = {
			mid : current_mid,
			file_list_area_id : cfg.fileListAreaID,
			editor_sequence   : cfg.editorSequence,
			upload_target_srl : cfg.uploadTargetSrl
		};

		function autosave() {
			if(typeof(_editorAutoSave) != 'function') return;
			uploadAutosaveChecker = true;
			_editorAutoSave(true);
		}

		function on_complete(ret, response_tags) {

			var $list, seq, files, target_srl, up_status, remain, items, i, c, itm, file_srl;

			seq   = ret.editor_sequence;
			files = ret.files;
			up_status  = ret.upload_status;
			target_srl = ret.upload_target_srl;
			remain     = Math.floor((parseInt(ret.left_size,10)||0)/1024);

			$list = $('#'+cfg.fileListAreaID).empty();

			if(target_srl) {
				if(editorRelKeys[seq].primary.value != target_srl) {
					editorRelKeys[seq].primary.value = target_srl;
					autosave();
				}

				editorRelKeys[seq].primary.value = target_srl;
				cfg.uploadTargetSrl = target_srl;
			}

			$('#'+cfg.uploaderStatusID).html(up_status);
			$('#'+cfg.previewAreaID).empty();

			if(files && files.item) {
				items = files.item;
				if(!$.isArray(items)) items = [items];
				for(i=0,c=items.length; i < c; i++) {
					itm = items[i];

					file_srl = itm.file_srl;
					uploadedFiles[file_srl] = itm;

					itm.previewAreaID = cfg.previewAreaID;

					if(/\.(jpe?g|png|gif)$/i.test(itm.download_url)) {
						loaded_images[file_srl] = $('<img />').attr('src', itm.download_url).get(0);
					}

					$('<option />')
						.text(itm.source_filename + ' ('+itm.disp_file_size+' )')
						.attr('value', file_srl)
						.appendTo($list);
				}

				if(i) $list.prop('selectedIndex', i-1).click();
			}

			// 문서 강제 자동저장 1번만 사용 ( 첨부파일 target_srl로 자동 저장문서를 저장하기 위한 용도일 뿐 )
			if(!uploadAutosaveChecker) autosave();
		}

		exec_xml(
			'file',         // module
			'getFileList',  // act
			params,         // parameters
			on_complete,    // callback
			'error,message,files,upload_status,upload_target_srl,editor_sequence,left_size'.split(',') // response_tags
		);
	}

	window.editorUploadInit = init;
	window.reloadFileList   = reloadFileList;

	$(function(){
		try { document.execCommand('BackgroundImageCache',false,true); } catch(e) { }
	});
})(jQuery);
