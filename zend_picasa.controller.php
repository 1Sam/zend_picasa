<?php
/**
 * @class  zend_picasaController
 * @author  (csh@korea.com)
 * @brief  zend_picasa 모듈의 Controller class
 **/

class zend_picasaController extends zend_picasa {


	/**
	 * @brief 초기화
	 **/

	function init() {
		$clientLibraryPath = realpath('./modules/zend_picasa');
		$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_Query'); // 보류
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_Photos');
		Zend_Loader::loadClass('Zend_Gdata_Photos_UserQuery');
		Zend_Loader::loadClass('Zend_Gdata_Photos_AlbumQuery');
		Zend_Loader::loadClass('Zend_Gdata_Photos_PhotoQuery');
		Zend_Loader::loadClass('Zend_Gdata_App_Extension_Category');

		$oModuleModel = &getModel('module');
		$this->config = $oModuleModel->getModuleConfig('zend_picasa');
	}

	function procZend_picasaUpload() {
		$filename = Context::get('image');
		$album=Context::get('album');
		$title = $_SESSION['upload_info'][$album]->title;
		$mid=Context::get('mid');
		$tag = Context::get('tag');
		$page=Context::get('page');

		if(!$album) return;
		if(!$mid) return;
		$config = $this->config;
		@set_time_limit(30);
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$summary = Context::get('summary');

		$result = $oZend_picasaModel->addPhoto($client, $config->user, $album, $title, $filename, $summary,$tag);
		@unlink($filename);
		$this->add('mid', Context::get('mid'));
		$this->add('page', $page);
		$this->add('album', $album);
		$this->add('act','dispZend_picasaListPhoto');



		/*$myFile = "testFile.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = print_r($result, true);
		fwrite($fh, $stringData);
		fclose($fh);*/
	}

	function procZend_picasaModify() {
		$album=Context::get('album');
		$mid=Context::get('mid');
		$tag = Context::get('tag');
		$page=Context::get('page');
		$title =Context::get('title');
		$photoid =Context::get('photoid');

		if(!$album) return;
		if(!$mid) return;
		if(!$photoid) return;

		$config = $this->config;
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$summary = Context::get('summary');

		$result = $oZend_picasaModel->modifyPhoto($client, $config->user, $album, $photoid,$title, $tag, $summary);
		$this->add('mid', Context::get('mid'));
		$this->add('photoid', $photoid);
		$this->add('album', $album);
		$this->add('act','dispZend_picasaViewPhoto');
	}



	function procZend_picasaAddTag() {
		$album=Context::get('album');
		$mid=Context::get('mid');
		$tag = Context::get('tag');
		$photoid =Context::get('photoid');
		$tag = implode(" ",explode(",",$tag));

		if(!$album) return;
		if(!$mid) return;
		if(!$photoid) return;
		$config = $this->config;
		$oZend_picasaModel = &getModel('zend_picasa');
		
		$client = $oZend_picasaModel->getClient();

		$result = $oZend_picasaModel->addTag($client, $config->user, $album, $photoid, $tag);
		$this->add('mid', Context::get('mid'));
		$this->add('photoid', $photoid);
		$this->add('album', $album);
		$this->add('act','dispZend_picasaViewPhoto');
	}

	function procZend_picasaAddAlbum(){
		$name = Context::get('albumname');
		$public = Context::get('public');
		if($public) $public='public';
		else $public='private';

		$config = $this->config;
		@set_time_limit(10);
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$oZend_picasaModel->addAlbum($client, $config->user, $name,$public);
		$this->add('mid', Context::get('mid'));
		$this->add('act','dispZend_picasaList');
		$this->setMessage('success_registed');

	}

	function procZend_picasaModifyAlbum(){
		$name = Context::get('albumname');
		$album = Context::get('album');
		$public = Context::get('public');
		if($publici=='Y') $public='public';
		else $public='private';

		$config = $this->config;
		@set_time_limit(10);
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$oZend_picasaModel->modifyAlbum($client, $config->user, $album,$name,$public);
		$this->add('mid', Context::get('mid'));
		$this->add('act','dispZend_picasaList');
		$this->setMessage('success_registed');

	}

	function procZend_picasaMakePublic(){
		$mid = Context::get('mid');
		$album = Context::get('album');
		if(!$album) return false;

		$config = $this->config;
		$oZend_picasaModel = &getModel('zend_picasa');

		$client = $oZend_picasaModel->getClient();

		$result = $oZend_picasaModel->makePublicAlbum($client, $config->user, $album);
		unset($album);
		$loca ="/?mid=".$mid."&act=dispZend_picasaList";
		@header('Location: '.$loca);
	}

	function procZend_picasaAddComment(){
		$album = Context::get('album');
		$photoid = Context::get('photoid');
		$comment = Context::get('nickname') ." - ".Context::get('comment');
		$config = $this->config;
		@set_time_limit(10);
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$result = $oZend_picasaModel->addComment($client, $config->user, $album,$photoid,$comment);
		if($result) $msg_code= 'success_registed';
		else $msg_code='failed';
		$this->add('mid', Context::get('mid'));
		$this->add('photoid', $photoid);
		$this->add('album', $album);
		$this->add('act','dispZend_picasaViewPhoto');
		$this->setMessage($msg_code);

	}

	function procZend_picasaDeletePhoto(){
		$photoid=Context::get('photoid');
		$album=Context::get('album');
		$config = $this->config;
		@set_time_limit(10);
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$oZend_picasaModel->deletePhoto($client, $config->user, $album, $photoid);
		$oZend_picasaModel->deleteThumbnail($album,$photoid);
		$this->add('mid', Context::get('mid'));
		$this->add('album', $album);
		$this->add('page', Context::get('page'));
		$this->add('act','dispZend_picasaListPhoto');
	}

	function procZend_picasaDeleteCache() {
		$album=Context::get('album');
		$mid =Context::get('mid');
		if(!$album) {
			$cache_file = "./files/cache/zend_picasa/zend_picasa.*.cache.php";
			$target = "dispZend_picasaList";
		} else {
			$cache_file = sprintf("./files/cache/zend_picasa/%s.*.cache.php", $album);
			$target = "dispZend_picasaListPhoto";
		}
		foreach (glob($cache_file) as $filename) {
		   unlink($filename);
		}

		$loca ="/?mid=".$mid."&act=".$target."&album=".$album;
		@header('Location: '.$loca);
	}

	function procZend_picasaDeleteAlbum() {
		$album=Context::get('album');
		$config = $this->config;
		@set_time_limit(10);
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$oZend_picasaModel->deleteAlbum($client, $config->user, $album);
		$oZend_picasaModel->deleteThumbnail($this->module_info->module_srl,$albumid);
		$this->add('mid', Context::get('mid'));
		$this->add('act','dispZend_picasaList');
	}
	

	function procZend_picasaDeleteComment() {
		$photoid=Context::get('photoid');
		$commentid=Context::get('comment');
		$album=Context::get('album');
		$mid = COntext::get('mid');

		$config = $this->config;
		@set_time_limit(10);
		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$oZend_picasaModel->deleteComment($client, $config->user, $album, $photoid, $commentid);
		$loca ="/?mid=".$mid."&act=dispZend_picasaViewPhoto&album=".$album."&photoid=".$photoid;
		@header('Location: '.$loca);
	}
	
	function procZend_picasaDeleteTag() {
		$photoid=Context::get('photoid');
		$tag=Context::get('tag');
		$album=Context::get('album');
		$mid =Context::get('mid');
		$config = $this->config;

		$oZend_picasaModel = &getModel('zend_picasa');
		$client = $oZend_picasaModel->getClient();

		$oZend_picasaModel->deleteTag($client,$config->user,$album, $photoid, $tag);
		$loca ="/?mid=".$mid."&act=dispZend_picasaViewPhoto&album=".$album."&photoid=".$photoid;
		@header('Location: '.$loca);
	}




	/**
	 * @brief File 업로드시에 이미지 파일만 피카사에 업로드하고 DB 저장
	 * 트리거 이후의 내용은 건너 뛰게 됨
	 **/
	function triggerInsertFile(&$trigger_obj) {
		if($config->use == 'N') return;

		// 파일정보는 insertFile 함수에서 보내주지 않음
		// 직접 웹페이지의 변수를 받아옴
		$vars = Context::getRequestVars();
		$file_info = $vars->Filedata;

		$oZend_picasaModel = getModel('zend_picasa');
		$config = $oZend_picasaModel->init();

		$output = $oZend_picasaModel->zend_picasa_upload($file_info, $trigger_obj->module_srl,$trigger_obj->upload_target_srl);
		
		// 트리거 before에서 중단하기 위해 error값 -1을 넣어줌
		// 피카사에만 업로드되며 DB는 피카사 파일값이 저장됨
		$output->error =  $output->toBool() ? -1 : 0;

		$myFile = "testFilefinal.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = print_r($vars,true). print_r($output,true);
		fwrite($fh, $stringData);
		fclose($fh);

		/*//$oModuleModel = &getModel('Module');
		//$triggers = $oModuleModel->getTriggers('file.insertFile', 'after');


		$myFile = "testFile_triggers.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = print_r($triggers,true). '<br>'.print_r($output,true);
		fwrite($fh, $stringData);
		fclose($fh);*/		
		//$output->add('1sam', 'xxxxxxxxxxxxxxx');
		return $output;	
	}

	function triggerInsertFileafter(&$trigger_obj) {

/*            $vars = Context::getRequestVars();

			$myFile = "testFile_after.txt";				
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData =  print_r($trigger_obj,true).'//'.print_r($vars,true);
			fwrite($fh, $stringData);
			fclose($fh);*/

	}


	function triggerFiledeleteFile(&$trigger_obj) {

		if(strpos($trigger_obj->sid, '#') !== false) {
							
			$oZend_picasaModel = getModel('zend_picasa');
			$config = $oZend_picasaModel->init();
			if($config->use == 'N' || $config->delete == 'N') return;

			$client = $oZend_picasaModel->getClient();

			//sid 를 분리
			$sids = explode('#',$trigger_obj->sid);
			$albumId = $sids[0];
			$photoId = $sids[1];

			$result = $oZend_picasaModel->deletePhoto($client, $config->user, $albumId, $photoId);


			/*$myFile = "testFile_delete2.txt";				
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData =  'hi'.print_r($result,true);
			fwrite($fh, $stringData);
			fclose($fh);*/;

			//$oZend_picasaModel->deletePhoto($client, $config->user, $albumId, $photoid);
			//$oZend_picasaModel->deleteThumbnail($album,$photoid);
/*				$this->add('mid', Context::get('mid'));
			$this->add('album', $album);
			$this->add('page', Context::get('page'));
			$this->add('act','dispZend_picasaListPhoto');*/
			
			//$output->error =  $output->toBool() ? -1 : 0;
		}

		return $output;	
	}



	/**
	 * @brief Document 작성 및 수정시에 피카사 앨범명 수정
	 **/
	function triggerInsertDocument(&$obj) {

		//앨범 숫자코드 가져오기
		//$album = Context::get('album');
		$oZend_picasaModel = &getModel('zend_picasa');
		$config = $oZend_picasaModel->init();
		if($config->use == 'N') return;

		$client = $oZend_picasaModel->getClient();
		$albumId = $oZend_picasaModel->getAlbumidFromSid($obj->document_srl);
		
		// DB에 피카사 이미지가 저장되어 있는지를 검사하고 없으면 종료
		if(!$albumId) return;


		$args->module_srl = $obj->module_srl;
		$args->document_srl = $obj->document_srl;
		$args->type = 'insert';

		$albumname = $obj->title;//Context::get('albumname');


		/*$myFile = "testFilefinal2.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = print_r($vars,true). print_r($albumId,true);
		fwrite($fh, $stringData);
		fclose($fh);*/

		/*$public = Context::get('public');
		if($publici=='Y') $public='public';
		else $public='private';*/
		$public = 'private';

		$oModuleModel = &getModel('module');
		$config = $oModuleModel->getModuleConfig('zend_picasa');
		//@set_time_limit(10);

		$oZend_picasaModel->modifyAlbum($client, $config->user, $albumId, $albumname, $public);

/*			$this->add('mid', Context::get('mid'));
		$this->add('act','dispZend_picasaList');
		$this->setMessage('success_registed');*/
	}


	/**
	 * @brief Document 작제시에 피카사 앨범 삭제
	 * 또는 임시로 문서 작성하다 취소했을 때 첨부했던 파일과 앨범 삭제
	 **/
	function triggerDeleteDocument(&$trigger_obj) {
	
		$oZend_picasaModel = getModel('zend_picasa');
		$config = $oZend_picasaModel->init();

		//글 삭제할 때 시간이 많이 걸리는 것인지 파일 목록을 미리 살펴야 제대로 작동함
		$albumId = $oZend_picasaModel->getAlbumidFromSid($trigger_obj->document_srl);
		if(!$albumId) {
			//$trigger_obj->document_srl로 저장된 앨범명으로 앨범 아이디 가져오기
			$albumId = $oZend_picasaModel->get_albumidByTitle($config->user, $config->pass, $trigger_obj->document_srl);
		}
		
		if(!$albumId) return;
		
		if($config->use == 'N' || $config->delete == 'N') return;


		
		/*$myFile = "testFile_deletattached.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = print_r($trigger_obj,true).'앨범아이디'. print_r($albumId,true). '//'.print_r($albumId_test,true);
		fwrite($fh, $stringData);
		fclose($fh);*/
		
		// DB에 피카사 이미지가 저장되어 있는지를 검사하고 없으면 종료
		if(!$albumId) return;

		$client = $oZend_picasaModel->getClient();
		$output = $oZend_picasaModel->deleteAlbum($client, $config->user, $albumId);
		return;
	}


	/**
	 * @brief 글 이동 시 파일의 upload_target_srl 과 module_srl 수정
	 * 안타깝게도 document.admin.controller.php 의 moveDocumentModule()함수의 내용을 일부 수정하여 사용합니다.
	 * 파일에 관련된 코드만 살짝 수정했습니다.
	 * 완료시에 리턴값이 -1 이라서 '이동 실패했습니다.'라는 알림 메시지가 뜨지만 정상 처리된 것입니다.
	 **/
	function triggerMoveDocument(&$trigger_obj) {
		$output = $this->moveDocumentModule($trigger_obj);

		/*$myFile = "testFile_triggerMoveDocument_.txt";				
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData =  print_r($files,true).'//'.print_r($document_srl_list,true).'//'.print_r($obj,true).'//'.print_r($output,true);
		fwrite($fh, $stringData);
		fclose($fh);*/
		//document.admin.controller.php의 moveDocumentModule() 함수를 중단하게 함
		return new Object(-1, '트리거 중단');
	}

	/**
	 * @brief 글 복사 시 파일의 upload_target_srl 과 module_srl 수정
	 * 안타깝게도 document.admin.controller.php 의 copyDocumentModule()함수의 내용을 일부 수정하여 사용합니다.
	 * 파일에 관련된 코드만 살짝 수정했습니다.
	 **/
	function triggerCopyDocument(&$trigger_obj) {
		$output = $this->copyDocumentModule($trigger_obj);

		//document.admin.controller.php의 moveDocumentModule() 함수를 중단하게 함
		return new Object(-1, '트리거 중단');
	}


	function triggerMoveDocumentxx(&$trigger_obj) {
		//$document_srl_list, $module_srl, $category_srl
		
		if(!count($document_srl_list)) return;

		$oDocumentModel = getModel('document');
		$oDocumentController = getController('document');

		$oDB = &DB::getInstance();
		$oDB->begin();

		//$triggerObj = new stdClass();
		$triggerObj->document_srls = implode(',',$document_srl_list);
		$triggerObj->module_srl = $module_srl;
		$triggerObj->category_srl = $category_srl;
		// Call a trigger (before)
		$output = ModuleHandler::triggerCall('document.moveDocumentModule', 'before', $triggerObj);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}

		for($i=count($document_srl_list)-1;$i>=0;$i--)
		{
			$document_srl = $document_srl_list[$i];
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) continue;

			$source_category_srl = $oDocument->get('category_srl');

			unset($obj);
			$obj = $oDocument->getObjectVars();
			// Move the attached file if the target module is different
			if($module_srl != $obj->module_srl && $oDocument->hasUploadedFiles())
			{
				$oFileController = getController('file');

				$files = $oDocument->getUploadedFiles();
				if(is_array($files))
				{
					foreach($files as $val)
					{
						$file_info = array();
						$file_info['tmp_name'] = $val->uploaded_filename;
						$file_info['name'] = $val->source_filename;
						$inserted_file = $oFileController->insertFile($file_info, $module_srl, $obj->document_srl, $val->download_count, true);
						if($inserted_file && $inserted_file->toBool())
						{
							// for image/video files
							if($val->direct_download == 'Y')
							{
								$source_filename = substr($val->uploaded_filename,2);
								$target_filename = substr($inserted_file->get('uploaded_filename'),2);
								$obj->content = str_replace($source_filename, $target_filename, $obj->content);
								// For binary files
							}
							else
							{
								$obj->content = str_replace('file_srl='.$val->file_srl, 'file_srl='.$inserted_file->get('file_srl'), $obj->content);
								$obj->content = str_replace('sid='.$val->sid, 'sid='.$inserted_file->get('sid'), $obj->content);
							}
						}
						// Delete an existing file
						$oFileController->deleteFile($val->file_srl);
					}
				}
				// Set the all files to be valid
				$oFileController->setFilesValid($obj->document_srl);
			}

			if($module_srl != $obj->module_srl)
			{
				$oDocumentController->deleteDocumentAliasByDocument($obj->document_srl);
			}
			// Move a module of the article
			$obj->module_srl = $module_srl;
			$obj->category_srl = $category_srl;
			$output = executeQuery('document.updateDocumentModule', $obj);
			if(!$output->toBool()) {
				$oDB->rollback();
				return $output;
			}

			//Move a module of the extra vars
			$output = executeQuery('document.moveDocumentExtraVars', $obj);
			if(!$output->toBool()) {
				$oDB->rollback();
				return $output;
			}
			// Set 0 if a new category doesn't exist after catergory change
			if($source_category_srl != $category_srl)
			{
				if($source_category_srl) $oDocumentController->updateCategoryCount($oDocument->get('module_srl'), $source_category_srl);
				if($category_srl) $oDocumentController->updateCategoryCount($module_srl, $category_srl);
			}
		}


	}

	function triggerMoveDocumentx(&$trigger_obj) {
	
/*			$oZend_picasaModel = getModel('zend_picasa');
		$config = $oZend_picasaModel->init();

		//글 삭제할 때 시간이 많이 걸리는 것인지 파일 목록을 미리 살펴야 제대로 작동함
		$albumId = $oZend_picasaModel->getAlbumidFromSid($trigger_obj->document_srl);
		if(!$albumId) {
			//$trigger_obj->document_srl로 저장된 앨범명으로 앨범 아이디 가져오기
			$albumId = $oZend_picasaModel->get_albumidByTitle($config->user, $config->pass, $trigger_obj->document_srl);
		}
		
		if(!$albumId) return;
		
		if($config->use == 'N' || $config->delete == 'N') return;*/



		if(!$trigger_obj->document_srls) return;

		//$GLOBALS['IMAGEPROCESSING']= 'true';

		//$oImageprocessModel = &getModel('imageprocess');
		//$oModuleModel = &getModel('module');
		//$ipConfig = $oModuleModel->getModuleConfig('imageprocess');
		$oDocumentModel = &getModel('document');
		$document_srl_list = explode(',',$trigger_obj->document_srls);

		$args = new stdClass();	

		for($i=count($document_srl_list)-1;$i>=0;$i--) {
			$document_srl = $document_srl_list[$i];
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) continue;

			//unset($obj);
			//$obj = $oDocument->getObjectVars();

			$args->module_srl = $trigger_obj->module_srl;
			//$obj->module_srl = $trigger_obj->module_srl;

			if($module_srl != $args->module_srl && $oDocument->hasUploadedFiles()) {
				$files = $oDocument->getUploadedFiles();
				if(is_array($files)) {
					
					$file_comment ='';



					foreach($files as $key => $val)	{
						//$_file = array();
						//$_file = $val->uploaded_filename;
						//$ofile = $oImageprocessModel->checkOfile($val->uploaded_filename,$ipConfig->store_path);
						//if(!file_exists($ofile)) continue;
						//FileHandler::moveFile($ofile,$_file);
						$file_comment = $val->comment;
						if(strpos($file_comment, 'x') !== false) {

							/*$oDB = &DB::getInstance();
							$oDB->begin();*/
				
							// Move a module of the article

							$args->sid = $val->sid;
							$output = executeQueryArray('zend_picasa.moveDocumentModule', $args);
							
							/*if(!$output->toBool()) {
								$oDB->rollback();
								return $output;
							}*/
					$myFile = "testFile_MoveDocumentxx.txt";
					$fh = fopen($myFile, 'w') or die("can't open file");
					$stringData = print_r($trigger_obj,true).'//'.print_r($files,true).'//'.print_r($args,true).'//'.print_r($output,true).'//'.print_r($file_comment,true);
					fwrite($fh, $stringData);
					fclose($fh);

						}
					}
					$myFile = "testFile_MoveDocument".$i.".txt";
					$fh = fopen($myFile, 'w') or die("can't open file");
					$stringData = print_r($trigger_obj,true).'//'.print_r($files,true).'//'.print_r($args,true).'//'.print_r($output,true).'//'.print_r($file_comment,true);
					fwrite($fh, $stringData);
					fclose($fh);


				}
			}
		}
		return $args;

		

		
/*			// DB에 피카사 이미지가 저장되어 있는지를 검사하고 없으면 종료
		if(!$albumId) return;

		$client = $oZend_picasaModel->getClient();
		$output = $oZend_picasaModel->deleteAlbum($client, $config->user, $albumId);
		return;*/
	}

	/**
	 * A trigger to delete the attachment in the upload_target_srl (document_srl)
	 * 임시저장 파일 삭제
	 *
	 * @param object $obj Trigger object
	 * @return Object
	 */
	function triggerDeleteAttached(&$obj)
	{

	}


	/**
	 * Change the module to move a specific article
	 * @param array $document_srl_list
	 * @param int $module_srl
	 * @param int $category_srl
	 * @return Object
	 */
	function moveDocumentModule($triggerObj) {//$document_srl_list, $module_srl, $category_srl)	{

		$document_srl_list = explode(',', $triggerObj->document_srls);
		$module_srl = $triggerObj->module_srl;
		$category_srl = $triggerObj->category_srl;



		if(!count($document_srl_list)) return;

		$oDocumentModel = getModel('document');
		$oDocumentController = getController('document');

		$oDB = &DB::getInstance();
		$oDB->begin();

		/*$triggerObj = new stdClass();
		$triggerObj->document_srls = implode(',',$document_srl_list);
		$triggerObj->module_srl = $module_srl;
		$triggerObj->category_srl = $category_srl;
		// Call a trigger (before)
		$output = ModuleHandler::triggerCall('document.moveDocumentModule', 'before', $triggerObj);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}*/
		


		for($i=count($document_srl_list)-1;$i>=0;$i--)
		{
			$document_srl = $document_srl_list[$i];
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) continue;

			$source_category_srl = $oDocument->get('category_srl');

			unset($obj);
			$obj = $oDocument->getObjectVars();
			// Move the attached file if the target module is different
			if($module_srl != $obj->module_srl && $oDocument->hasUploadedFiles())
			{
				$oFileController = getController('file');

				$files = $oDocument->getUploadedFiles();



				if(is_array($files))
				{
					foreach($files as $val)
					{
						$file_info = array();
						$file_info['tmp_name'] = $val->uploaded_filename;
						$file_info['name'] = $val->source_filename;
						$oFileController = getController('file');						

						$myFile = "testFile_move_inserted_filex1.txt";				
						$fh = fopen($myFile, 'w') or die("can't open file");
						$stringData =  print_r($files,true).'//'.print_r($inserted_file,true).'//'.print_r($obj,true).'//'.print_r($output,true);
						fwrite($fh, $stringData);
						fclose($fh);

						// 피카사 이미지 파일일 때
						if(strpos($val->comment, "x") !== false) {
							// List file information
							$args = new stdClass;
							$args->file_srl = getNextSequence(); // 시퀀스 증가코드
							$args->module_srl = $module_srl;
							$args->sid = $val->sid; //비교값
				
							// xe/modules/file/queries/updateFile_picasa.xml 을 이용하여 files DB 수정

							//$upload_target_srl
							$_SESSION['__XE_UPLOADING_FILES_INFO__'][$args->file_srl] = true;
							$output = executeQueryArray('zend_picasa.updateFile_picasaBySid', $args);


							/*$output->add('file_srl', $args->file_srl);
							$output->add('file_size', $val->file_size);
							$output->add('sid', $file_info->sid);
							$output->add('direct_download', $val->direct_download);
							$output->add('source_filename', $val->source_filename);
							$output->add('upload_target_srl', $val->upload_target_srl);
							$output->add('uploaded_filename', $val->uploaded_filename);*/
							// for image/video files
							//$source_filename = substr($val->uploaded_filename,2);
							//$target_filename = substr($inserted_file->get('uploaded_filename'),2);


							//$obj->content = str_replace($source_filename, $target_filename, $obj->content);

						} else {
							$inserted_file = $oFileController->insertFile($file_info, $module_srl, $obj->document_srl, $val->download_count, true);
	
							if($inserted_file && $inserted_file->toBool())
							{
								// For binary files
								$obj->content = str_replace('file_srl='.$val->file_srl, 'file_srl='.$inserted_file->get('file_srl'), $obj->content);
								$obj->content = str_replace('sid='.$val->sid, 'sid='.$inserted_file->get('sid'), $obj->content);

							}
							// Delete an existing file
							$oFileController->deleteFile($val->file_srl);
						}
						
						
						/*$myFile = "testFile_move_inserted_filexx.txt";				
						$fh = fopen($myFile, 'w') or die("can't open file");
						$stringData =  print_r($files,true).'//'.print_r($inserted_file,true).'//'.print_r($obj,true).'//'.print_r($output,true);
						fwrite($fh, $stringData);
						fclose($fh);*/


					}
				}
				// Set the all files to be valid
				$oFileController->setFilesValid($obj->document_srl);
			}

			if($module_srl != $obj->module_srl)
			{
				$oDocumentController->deleteDocumentAliasByDocument($obj->document_srl);
			}
			// Move a module of the article
			$obj->module_srl = $module_srl;
			$obj->category_srl = $category_srl;
			$output = executeQuery('document.updateDocumentModule', $obj);
			if(!$output->toBool()) {
				$oDB->rollback();
				return $output;
			}

			//Move a module of the extra vars
			$output = executeQuery('document.moveDocumentExtraVars', $obj);
			if(!$output->toBool()) {
				$oDB->rollback();
				return $output;
			}
			// Set 0 if a new category doesn't exist after catergory change
			if($source_category_srl != $category_srl)
			{
				if($source_category_srl) $oDocumentController->updateCategoryCount($oDocument->get('module_srl'), $source_category_srl);
				if($category_srl) $oDocumentController->updateCategoryCount($module_srl, $category_srl);
			}
		}

		$args = new stdClass();
		$args->document_srls = implode(',',$document_srl_list);
		$args->module_srl = $module_srl;
		// move the comment
		$output = executeQuery('comment.updateCommentModule', $args);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}

		$output = executeQuery('comment.updateCommentListModule', $args);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}
		
		// move the trackback
		if(getClass('trackback'))
		{
			$output = executeQuery('trackback.updateTrackbackModule', $args);
			if(!$output->toBool())
			{
				$oDB->rollback();
				return $output;
			}
		}

		// Tags
		$output = executeQuery('tag.updateTagModule', $args);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}
		// Call a trigger (before)
		/*$output = ModuleHandler::triggerCall('document.moveDocumentModule', 'after', $triggerObj);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}*/

		$oDB->commit();
		//remove from cache
		$oCacheHandler = CacheHandler::getInstance('object');
		if($oCacheHandler->isSupport())
		{
			foreach($document_srl_list as $document_srl)
			{
				$cache_key_item = 'document_item:'. getNumberingPath($document_srl) . $document_srl;
				$oCacheHandler->delete($cache_key_item);
			}
		}
		return new Object();
	}


	/**
	 * Copy the post
	 * @param array $document_srl_list
	 * @param int $module_srl
	 * @param int $category_srl
	 * @return object
	 */
	function copyDocumentModule($triggerObj) {//$document_srl_list, $module_srl, $category_srl)	{

		$document_srl_list = explode(',', $triggerObj->document_srls);
		$module_srl = $triggerObj->module_srl;
		$category_srl = $triggerObj->category_srl;

		if(count($document_srl_list) < 1) return;

		$oDocumentModel = getModel('document');
		$oDocumentController = getController('document');

		$oFileModel = getModel('file');

		$oDB = &DB::getInstance();
		$oDB->begin();

		/*$triggerObj = new stdClass();
		$triggerObj->document_srls = implode(',',$document_srl_list);
		$triggerObj->module_srl = $module_srl;
		$triggerObj->category_srl = $category_srl;
		// Call a trigger (before)
		$output = ModuleHandler::triggerCall('document.copyDocumentModule', 'before', $triggerObj);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}*/

		$extraVarsList = $oDocumentModel->getDocumentExtraVarsFromDB($document_srl_list);
		$extraVarsListByDocumentSrl = array();
		if(is_array($extraVarsList->data))
		{
			foreach($extraVarsList->data as $value)
			{
				if(!isset($extraVarsListByDocumentSrl[$value->document_srl]))
				{
					$extraVarsListByDocumentSrl[$value->document_srl] = array();
				}

				$extraVarsListByDocumentSrl[$value->document_srl][] = $value;
			}
		}

		for($i=count($document_srl_list)-1;$i>=0;$i--)
		{
			$document_srl = $document_srl_list[$i];
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) continue;

			$obj = $oDocument->getObjectVars();

			$extraVars = $extraVarsListByDocumentSrl[$document_srl];
			if($module_srl == $obj->module_srl)
			{
				if(is_array($extraVars))
				{
					foreach($extraVars as $extraItem)
					{
						if($extraItem->var_idx >= 0) $obj->{'extra_vars'.$extraItem->var_idx} = $extraItem->value;
					}
				}
			}
			$obj->module_srl = $module_srl;
			$obj->document_srl = getNextSequence();
			$obj->category_srl = $category_srl;
			$obj->password_is_hashed = true;
			$obj->comment_count = 0;
			$obj->trackback_count = 0;

			// Pre-register the attachment
			if($oDocument->hasUploadedFiles())
			{
				$files = $oDocument->getUploadedFiles();
					foreach($files as $val)
					{
						$file_info = array();
						$file_info['tmp_name'] = $val->uploaded_filename;
						$file_info['name'] = $val->source_filename;
						$oFileController = getController('file');						

						/*$myFile = "testFile_move_inserted_filex1.txt";				
						$fh = fopen($myFile, 'w') or die("can't open file");
						$stringData =  print_r($files,true).'//'.print_r($inserted_file,true).'//'.print_r($obj,true).'//'.print_r($output,true);
						fwrite($fh, $stringData);
						fclose($fh);*/

						// 피카사 이미지 파일일 때
						if(strpos($val->comment, "x") !== false) {
							// List file information
							$args = new stdClass;
							$args->file_srl = getNextSequence(); // 시퀀스 증가코드
							$args->module_srl = $module_srl;
							$args->sid = $val->sid; //비교값
				
							// xe/modules/file/queries/updateFile_picasa.xml 을 이용하여 files DB 수정

							//$upload_target_srl
							$_SESSION['__XE_UPLOADING_FILES_INFO__'][$args->file_srl] = true;
							$output = executeQueryArray('zend_picasa.updateFile_picasaBySid', $args);


							/*$output->add('file_srl', $args->file_srl);
							$output->add('file_size', $val->file_size);
							$output->add('sid', $file_info->sid);
							$output->add('direct_download', $val->direct_download);
							$output->add('source_filename', $val->source_filename);
							$output->add('upload_target_srl', $val->upload_target_srl);
							$output->add('uploaded_filename', $val->uploaded_filename);*/
							// for image/video files
							//$source_filename = substr($val->uploaded_filename,2);
							//$target_filename = substr($inserted_file->get('uploaded_filename'),2);


							//$obj->content = str_replace($source_filename, $target_filename, $obj->content);

						} else {
							$inserted_file = $oFileController->insertFile($file_info, $module_srl, $obj->document_srl, $val->download_count, true);
	
							if($inserted_file && $inserted_file->toBool())
							{
								// For binary files
								$obj->content = str_replace('file_srl='.$val->file_srl, 'file_srl='.$inserted_file->get('file_srl'), $obj->content);
								$obj->content = str_replace('sid='.$val->sid, 'sid='.$inserted_file->get('sid'), $obj->content);

							}
							// Delete an existing file
							$oFileController->deleteFile($val->file_srl);
						}
						
						
						/*$myFile = "testFile_move_inserted_filexx.txt";				
						$fh = fopen($myFile, 'w') or die("can't open file");
						$stringData =  print_r($files,true).'//'.print_r($inserted_file,true).'//'.print_r($obj,true).'//'.print_r($output,true);
						fwrite($fh, $stringData);
						fclose($fh);*/


					}
			}

			// Write a post
			$output = $oDocumentController->insertDocument($obj, true, true);
			if(!$output->toBool())
			{
				$oDB->rollback();
				return $output;
			}

			// copy multi language contents
			if(is_array($extraVars))
			{
				foreach($extraVars as $value)
				{
					if($value->idx >= 0 && $value->lang_code == Context::getLangType())
					{
						continue;
					}

					if( $value->var_idx < 0 || ($module_srl == $value->module_srl && $value->var_idx >= 0) )
					{
						$oDocumentController->insertDocumentExtraVar($value->module_srl, $obj->document_srl, $value->var_idx, $value->value, $value->eid, $value->lang_code);
					}
				}
			}

			// Move the comments
			if($oDocument->getCommentCount())
			{
				$oCommentModel = getModel('comment');
				$comment_output = $oCommentModel->getCommentList($document_srl, 0, true, 99999999);
				$comments = $comment_output->data;
				if(count($comments) > 0)
				{
					$oCommentController = getController('comment');
					$success_count = 0;
					$p_comment_srl = array();
					foreach($comments as $comment_obj)
					{
						$comment_srl = getNextSequence();
						$p_comment_srl[$comment_obj->comment_srl] = $comment_srl;

						// Pre-register the attachment
						if($comment_obj->uploaded_count)
						{
							$files = $oFileModel->getFiles($comment_obj->comment_srl, true);
							/*foreach($files as $val)
							{
								$file_info = array();
								$file_info['tmp_name'] = $val->uploaded_filename;
								$file_info['name'] = $val->source_filename;
								$oFileController = getController('file');
								$inserted_file = $oFileController->insertFile($file_info, $module_srl, $comment_srl, 0, true);
								// if image/video files
								if($val->direct_download == 'Y')
								{
									$source_filename = substr($val->uploaded_filename,2);
									$target_filename = substr($inserted_file->get('uploaded_filename'),2);
									$comment_obj->content = str_replace($source_filename, $target_filename, $comment_obj->content);
									// If binary file
								}
								else
								{
									$comment_obj->content = str_replace('file_srl='.$val->file_srl, 'file_srl='.$inserted_file->get('file_srl'), $comment_obj->content);
									$comment_obj->content = str_replace('sid='.$val->sid, 'sid='.$inserted_file->get('sid'), $comment_obj->content);
								}
							}*/

							foreach($files as $val)
							{
								$file_info = array();
								$file_info['tmp_name'] = $val->uploaded_filename;
								$file_info['name'] = $val->source_filename;
								$oFileController = getController('file');	
															
								/*$myFile = "testFile_move_inserted_filex1.txt";				
								$fh = fopen($myFile, 'w') or die("can't open file");
								$stringData =  print_r($files,true).'//'.print_r($inserted_file,true).'//'.print_r($obj,true).'//'.print_r($output,true);
								fwrite($fh, $stringData);
								fclose($fh)*/;
		
								// 피카사 이미지 파일일 때
								if(strpos($val->comment, "x") !== false) {
									// List file information
									$args = new stdClass;
									$args->file_srl = getNextSequence(); // 시퀀스 증가코드
									$args->module_srl = $module_srl;
									$args->sid = $val->sid; //비교값
						
									// xe/modules/file/queries/updateFile_picasa.xml 을 이용하여 files DB 수정
		
									//$upload_target_srl
									$_SESSION['__XE_UPLOADING_FILES_INFO__'][$args->file_srl] = true;
									$output = executeQueryArray('zend_picasa.updateFile_picasaBySid', $args);
		
		
									/*$output->add('file_srl', $args->file_srl);
									$output->add('file_size', $val->file_size);
									$output->add('sid', $file_info->sid);
									$output->add('direct_download', $val->direct_download);
									$output->add('source_filename', $val->source_filename);
									$output->add('upload_target_srl', $val->upload_target_srl);
									$output->add('uploaded_filename', $val->uploaded_filename);*/
									// for image/video files
									//$source_filename = substr($val->uploaded_filename,2);
									//$target_filename = substr($inserted_file->get('uploaded_filename'),2);
		
		
									//$obj->content = str_replace($source_filename, $target_filename, $obj->content);
		
								} else {
									$inserted_file = $oFileController->insertFile($file_info, $module_srl, $comment_srl, 0, true);
									//$inserted_file = $oFileController->insertFile($file_info, $module_srl, $obj->document_srl, $val->download_count, true);
			
									if($inserted_file && $inserted_file->toBool())
									{
										// For binary files
										$obj->content = str_replace('file_srl='.$val->file_srl, 'file_srl='.$inserted_file->get('file_srl'), $comment_obj->content);
										$obj->content = str_replace('sid='.$val->sid, 'sid='.$inserted_file->get('sid'), $comment_obj->content);
		
									}
								}
								
								
								/*$myFile = "testFile_move_inserted_filexx.txt";				
								$fh = fopen($myFile, 'w') or die("can't open file");
								$stringData =  print_r($files,true).'//'.print_r($inserted_file,true).'//'.print_r($obj,true).'//'.print_r($output,true);
								fwrite($fh, $stringData);
								fclose($fh);*/
		
		
							}

						}

						$comment_obj->module_srl = $obj->module_srl;
						$comment_obj->document_srl = $obj->document_srl;
						$comment_obj->comment_srl = $comment_srl;

						if($comment_obj->parent_srl) $comment_obj->parent_srl = $p_comment_srl[$comment_obj->parent_srl];

						$output = $oCommentController->insertComment($comment_obj, true);
						if($output->toBool()) $success_count ++;
					}
					$oDocumentController->updateCommentCount($obj->document_srl, $success_count, $comment_obj->nick_name, true);
				}
			}

			// Move the trackbacks
			$oTrackbackModel = getModel('trackback');
			if($oTrackbackModel && $oDocument->getTrackbackCount())
			{
				$trackbacks = $oTrackbackModel->getTrackbackList($oDocument->document_srl);
				if(count($trackbacks))
				{
					$success_count = 0;
					foreach($trackbacks as $trackback_obj)
					{
						$trackback_obj->trackback_srl = getNextSequence();
						$trackback_obj->module_srl = $obj->module_srl;
						$trackback_obj->document_srl = $obj->document_srl;
						$output = executeQuery('trackback.insertTrackback', $trackback_obj);
						if($output->toBool()) $success_count++;
					}
					// Update the number of trackbacks
					$oDocumentController->updateTrackbackCount($obj->document_srl, $success_count);
				}
			}

			$copied_srls[$document_srl] = $obj->document_srl;
		}

		// Call a trigger (before)
		$triggerObj->copied_srls = $copied_srls;
		$output = ModuleHandler::triggerCall('document.copyDocumentModule', 'after', $triggerObj);
		if(!$output->toBool())
		{
			$oDB->rollback();
			return $output;
		}

		$oDB->commit();

		$output = new Object();
		$output->add('copied_srls', $copied_srls);
		return $output;
	}


}
