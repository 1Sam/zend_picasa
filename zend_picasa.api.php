<?php
/**
 * @class  zend_picasaController
 * @author  (csh@korea.com)
 * @brief  zend_picasa 모듈의 Controller class
 **/

class zend_picasaApi extends zend_picasa {

	// 첨부된 파일에서 comment에 x 또는 s 가 포함된 것만 썸네일 생성을 시도합니다.
	// 본문에 삽입된 구글 피카사 이미지는 '썸네일 마법사'에서 관리합니다.

	function triggerGetThumbnail(&$triggerObj) {

		$oModuleModel = getModel('module');
		$config = $oModuleModel->getModuleConfig('zend_picasa');

		if($config->use == 'N') return;
		
		if(!$triggerObj->variables['uploaded_count']) return;

		// 'N' 가 썸네일을 저장한다는 것임
		//if($config->is_save == 'Y') {
		//	$triggerObj->variables['thumbnail_url'] = '';
		//} 

		//if($triggerObj->variables['thumbnail_url']  && ($config->is_save == 'N')) return new Object(-1,'zend : 이미 있네');

		/*$myFile = "testFile_trigger_zend_".$triggerObj->document_srl.$triggerObj->variables['comment_status'].".txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = print_r($triggerObj,true);
		fwrite($fh, $stringData);
		fclose($fh);*/

		//$triggerObj->add("module_zend_picasa", "zend_picasa");	
		

		//return new Object(0,'success');
		
		// 저장
		if($config->is_save == 'N') {
			$document_srl = $triggerObj->variables['document_srl'];
			$width = $triggerObj->variables['width'];
			$height = $triggerObj->variables['height'];
			$thumbnail_type = $triggerObj->variables['thumbnail_type'];
			
	
			// Define thumbnail information
			$thumbnail_path = sprintf('files/thumbnails/%s',getNumberingPath($document_srl, 3));
			$thumbnail_file = sprintf('%s%dx%d.%s.jpg', $thumbnail_path, $width, $height, $thumbnail_type);
			$thumbnail_url  = Context::getRequestUri().$thumbnail_file;
	
			/*$myFile = "testFile_trigger6_".$triggerObj->document_srl.$triggerObj->variables['comment_status'].".txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = $tmp_file.'///'.$video_info->target_src.'//'.print_r($video_info,true).'//'.print_r($oObj,true).'//'.print_r($triggerObj,true);
			fwrite($fh, $stringData);
			fclose($fh);
			return $oObj;*/
	
			/*// 저장된 썸네일이 있으면 바로 주소 리턴
			if(file_exists($thumbnail_file)) {
				//if(filesize($thumbnail_file)<1) return false;
				//else return $thumbnail_url;
				if(filesize($thumbnail_file)>0) {//return $thumbnail_url;
					$oObj = new Object(-1,'피카사 : 이미 있는 썸네일 주소 리턴');
					$oObj->variables['thumbnail_url'] = $thumbnail_url;//$source_file;
					$triggerObj->variables['thumbnail_url'] = $thumbnail_url;
	
					
	
					return $oObj;
				}
			} else {*/


				$oFileModel = getModel('file');
				$file_list = $oFileModel->getFiles($triggerObj->document_srl, array(), 'file_srl', true);
				if(count($file_list))
				{
					foreach($file_list as $file)
					{
						if(strpos($file->comment,"x") !== false || strpos($file->comment,"s") !== false) {


							if(file_exists($thumbnail_file)) {
								//if(filesize($thumbnail_file)<1) return false;
								//else return $thumbnail_url;
								if(filesize($thumbnail_file)>0) {//return $thumbnail_url;
									$oObj = new Object(-1,'피카사 : 이미 있는 썸네일 주소 리턴');
									$oObj->variables['thumbnail_url'] = $thumbnail_url;//$source_file;
									$triggerObj->variables['thumbnail_url'] = $thumbnail_url;
					
									
					
									return $oObj;
								}
							}


							// 썸네일 저장
							$target_src = str_replace('/s0/', '/',$file->uploaded_filename);
							//$target_src = "http://img.youtube.com/vi/".$youtubeid."/0.jpg"; 
							$tmp_file = sprintf('./files/cache/tmp/%s', md5(rand(111111,999999).$document_srl));
							if(!is_dir('./files/cache/tmp')) FileHandler::makeDir('./files/cache/tmp');
							FileHandler::getRemoteFile($target_src, $tmp_file);
							// 원격서버에서 소스파일을 tmp 폴더에 복사해 왔다면
							// thumbnails 폴더로 복사후 최종 주소 리턴
							if(file_exists($tmp_file)) {

								//list($_w, $_h, $_t, $_a) = @getimagesize($tmp_file);
								//if($_w>=$width && $_h>=$height)
								//{
								//$source_file = $tmp_file;
								$is_tmp_file = true;
								//}
								$outputz = FileHandler::createImageFile($tmp_file, $thumbnail_file, $width, $height, 'jpg', $thumbnail_type);
								FileHandler::removeFile($tmp_file);
		
								/*$myFile = "testFile_trigger01_".$triggerObj->document_srl.$triggerObj->variables['comment_status'].".txt";
								$fh = fopen($myFile, 'w') or die("can't open file");
								$stringData = $tmp_file.'///'.$video_info->target_src.'//'.print_r($video_info,true).'//'.print_r($oObj,true).'//'.print_r($triggerObj,true);
								fwrite($fh, $stringData);
								fclose($fh);*/
								$oObj = new Object(-1,'동영상 썸네일생성');
								$oObj->message = "썸네일 생성성공";
								$oObj->variables['thumbnail_url'] = $thumbnail_file;
								$triggerObj->variables['thumbnail_url'] = $thumbnail_file;
							} else {
								return new Object(0,'피카사 뭔가 이상함');
								//$oObj = new Object(-1,'동영상 썸네일생성 실패');
								//$oObj->message = "썸네일 생성 실패";
		
								// 유투브 영상이 짤렸으므로 에러 이미지를 표시함
								//$oObj->variables['thumbnail_url'] = './modules/thumbnail_wizard/tpl/images/youtube_blocked.png';//$thumbnail_file;
								//$triggerObj->variables['thumbnail_url'] = './modules/thumbnail_wizard/tpl/images/youtube_blocked.png';//$thumbnail_file;
							}
							
							return $oObj;

						}
					}
				}
			//}
		// 소스 주소 리턴	
		} else {
		
		//$output = new Object();
		//$output->add('thumbnail_url', "추헌성 바보ㅎㅇ염");
		//$args = new stdClass;
		//$args->thumbnail_url = "추추트레인";

		//$oObj = new Object(-1,'피카사 썸네일생성');

		//if(!empty($triggerObj->uploadedFiles)) $variables->isuploadedfile = "있음";

		//$oObj->key1 = "value1";
		//$oObj->key2 = "value1";
		//$variables->isuploadedfiles = $triggerObj->uploadedFiles;
		//$variables->key1 = "value1";
		//$variables->key2 = "value1";
		//$oObj->xxx = $triggerObj->variables->comment_status;
		//$oObj->yyy = $triggerObj->variables['module_srl'];
		//$oObj->adds($variables);

		
			//if($triggerObj->variables['uploaded_count']) {
				//return $triggerObj->variables['thumbnail_url'] = "good";
	
				$width = $triggerObj->variables['width'];
				$height = $triggerObj->variables['height'];
				$thumbnail_type = $triggerObj->variables['thumbnail_type'];
	
				$oFileModel = getModel('file');
				$file_list = $oFileModel->getFiles($triggerObj->document_srl, array(), 'file_srl', true);
				if(count($file_list))
				{
					foreach($file_list as $file)
					{
						if(strpos($file->comment,"x") !== false) {
							//피카사 이미지 crop 일때는 가로형 레이아웃, riaot은 세로형 레이아웃으로 구분
							$width_height = split("x", $file->comment);
							
							//if($thumbnail_type == 'crop') $picasa_option="-c/"; //폭과 높이 중에 짧은 쪽을 기준으로 크롭한 이미지
							//if($width < $height)  $picasa_size = $height;
	
							if($thumbnail_type == 'crop') {
								/*//세로형
								if($width < $height) {
									if ($width_height[0] >= $width_height[1]){
										$source_file = str_replace('/s0/', '/h'.$height.'/', $file->uploaded_filename);
									} else {
										$source_file = str_replace('/s0/', '/w'.$width.'/', $file->uploaded_filename);
									}
								} else { //가로형
									if ($width_height[0] >=$width_height[1]){
										$source_file = str_replace('/s0/', '/h'.$height.'/', $file->uploaded_filename);
									} else {
										$source_file = str_replace('/s0/', '/w'.$width.'/', $file->uploaded_filename);									
									}
								}*/
								$source_file = str_replace('/s0/', '/w'.$width.'-h'.$height.'-c/'.$thumbnail_type.'-', $file->uploaded_filename);
								
							}
							else { // ratio
								//세로형
								if($width < $height) {
									if ($width_height[0] >= $width_height[1]){
										$source_file = str_replace('/s0/', '/w'.$width.'/', $file->uploaded_filename);
									} else {
										$source_file = str_replace('/s0/', '/h'.$height.'/', $file->uploaded_filename);
									}
								} else { //가로형
									if ($width_height[0] >=$width_height[1]){
										$source_file = str_replace('/s0/', '/w'.$hwidth.'/', $file->uploaded_filename);
									} else {
										$source_file = str_replace('/s0/', '/h'.$height.'/', $file->uploaded_filename);									
									}
								}
							}
						}
						//피카사 이미지일 경우 코멘트에 s가 포함되어 있음.
						elseif(strpos($file->comment,"s") !== false) {
							//피카사 이미지 crop 일때는 가로형 레이아웃, riaot은 세로형 레이아웃으로 구분
							
							$picasa_option = "-d/"; //폭과 높이 중에 긴 쪽을 기준으로 비율을 맞춘 이미지
							$picasa_size = $width;
							
							//if($thumbnail_type == 'crop') $picasa_option="-c/"; //폭과 높이 중에 짧은 쪽을 기준으로 크롭한 이미지
							//if($width < $height)  $picasa_size = $height;
							
							if($thumbnail_type == 'crop') {
								/*if($width < $height) {
									$picasa_size = $height;
									$picasa_option="-d/";
									$source_file = str_replace("/".$file->comment."/", "/w".$picasa_size.$picasa_option, $file->uploaded_filename);
								} else {
									$picasa_size = $width;
									$picasa_option="-c/";
									$source_file = str_replace("/".$file->comment."/", "/s".$picasa_size.$picasa_option, $file->uploaded_filename);
	
								}*/
								$source_file = str_replace('/'.$file->comment.'/', '/w'.$width.'-h'.$height.'-c/'.$thumbnail_type.'-', $file->uploaded_filename);
							} else { //ratio
								if($width < $height) {
									$picasa_size = $height;
									$picasa_option="-d/";
									$source_file = str_replace("/".$file->comment."/", "/w".$picasa_size.$picasa_option, $file->uploaded_filename);
								} else {
									$picasa_size = $width;
									$picasa_option="-c/";
									$source_file = str_replace("/".$file->comment."/", "/s".$picasa_size.$picasa_option, $file->uploaded_filename);
	
								}
							}
	
	
							
						} 
						// comment 내용이 link 또는 NULL 인경우는 일단 그냥 통과하고
						// document.item.php에서 실행하도록 한다.
						/*else {
							// 일반 이미지일 경우 
							// 3. Link 첨부 이미지 : 업로드 파일이름에 (link)로 표시되는 파일
							// $file->uploaded_filename 원본 이미지 주소값으로 DB에 저장되어 있음
							$source_file = $file->uploaded_filename;
	
							$make_thumbnail = TRUE;
					
							if ($make_thumbnail == TRUE) {
								//썸네일을 생성하고 생성된 썸네일 주소 리턴
								$target_src = $source_file; 
				
								$tmp_file = sprintf('./files/cache/tmp/%d', md5(rand(111111,999999).$this->document_srl));
								if(!is_dir('./files/cache/tmp')) FileHandler::makeDir('./files/cache/tmp');
								FileHandler::getRemoteFile($target_src, $tmp_file);
			
								if(file_exists($tmp_file)) {
									list($_w, $_h, $_t, $_a) = @getimagesize($tmp_file);
									if($_w>=$width && $_h>=$height)	{
										$source_file = $tmp_file;
										$is_tmp_file = true;
									}
								}
						
								if($source_file) $output = FileHandler::createImageFile($tmp_file, $thumbnail_file, $width, $height, 'jpg', $thumbnail_type);
					
								if($is_tmp_file) FileHandler::removeFile($source_file);
			
								// Return its path if a thumbnail is successfully genetated
								// 성공하면 썸네일 주소 리턴
								if($output) return $thumbnail_url;
								// Create an empty file not to re-generate the thumbnail
								else FileHandler::writeFile($thumbnail_file, '','w');
			
								return;	
							}
							
						}*/
						
	
						//원본 이미지 링크를 썸네일로 속여서 리턴합니다.
						//$thumbnail_url  = $source_file;
						//$oObj->variables['thumbnail_url'] = $thumbnail_url;
	
						//$source_file = $file->uploaded_filename;
						//if(!file_exists($source_file)) $source_file = null;
						//else break;
						
						
						// 피카사 이미지가 포함되어 있을 경우 바로 썸네일 주소를 리턴
						if($source_file) {
							$oObj = new Object(-1,'피카사 썸네일생성');
							$oObj->variables['thumbnail_url'] = $source_file;
							$triggerObj->variables['thumbnail_url'] = $source_file;
	
							/*$myFile = "testFile_trigger_".$triggerObj->document_srl.$triggerObj->variables['comment_status'].".txt";
							$fh = fopen($myFile, 'w') or die("can't open file");
							$stringData = print_r($oObj,true).'\r\n\r\n'.print_r($triggerObj,true);
							fwrite($fh, $stringData);
							fclose($fh);*/

							return $oObj;
							//break;
						}
					}
				}
			//}

		}

		
		// 피카사 이미지가 없는 게시글일 경우 성공으로 리턴
		return new Object(0,'피카사 뭔가 이상함');

	}

}
