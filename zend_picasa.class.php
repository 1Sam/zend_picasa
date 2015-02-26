<?php
	/**
	 * @class  zend_picasa
	 * @author  (csh@korea.com)
	 * @brief  zend_picasa 모듈의 상위 class
	 **/

	class zend_picasa extends ModuleObject {

		public function __construct() {
	        set_include_path(get_include_path() . PATH_SEPARATOR . realpath('./modules/zend_picasa/'));
			require_once 'Zend/Loader/Autoloader.php';
			Zend_Loader_Autoloader::getInstance();

			$oModuleModel = &getModel('module');
			$this->config = $oModuleModel->getModuleConfig('zend_picasa');
			//if(!$this->config->user || !$this->config->pass) return new Object(-1, 'msg_module_not_configured');
		}


		/**
		 * @brief 설치시 추가 작업이 필요할시 구현
		 **/
		function moduleInstall() {
			$oModuleController = &getController('module');

			// 글쓰기 상태에서 uploader.js 의 defaultHandlers 변수값을 다시 정의하도록 변경된 js 파일을 html에 삽입합니다.
			$oModuleController->insertTrigger('display', 'zend_picasa', 'controller', 'triggerDisplay', 'before');
  			
			//이미지 파일 등록시 피카사로 파일 전송
			$oModuleController->insertTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFile', 'before');
			//$oModuleController->insertTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFileafter', 'after');


			//이미지 파일 삭제시 피카사도 파일 삭제
			$oModuleController->insertTrigger(' file.deleteFile', 'zend_picasa', 'controller', 'triggerFiledeleteFile', 'after');


			//글 등록 시 피카사 앨범명 수정
			$oModuleController->insertTrigger('document.insertDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after');
			//글 수정 시 비교 후 피카사 앨범명 수정
			$oModuleController->insertTrigger('document.updateDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after');
			//글 삭제 시 피카사 앨범 삭제하기
			$oModuleController->insertTrigger('document.deleteDocument', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'before');
			//글 이동 시 파일의 upload_target_srl 과 module_srl 수정
			$oModuleController->insertTrigger('document.moveDocumentModule','zend_picasa', 'controller', 'triggerMoveDocument', 'before');
			//글 복사 시 파일의 upload_target_srl 과 module_srl 수정
			$oModuleController->insertTrigger('document.copyDocumentModule','zend_picasa', 'controller', 'triggerCopyDocument', 'before');
			//임시 저장글 삭제 시 피카사 앨범 삭제하기
			$oModuleController->insertTrigger('editor.deleteSavedDoc', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'after');  

			//썸네일 주소 리턴
			$oModuleController->insertTrigger('document.getThumbnail', 'zend_picasa', 'api', 'triggerGetThumbnail', 'before');

			return new Object();
		}

		/**
		 * @brief 설치가 이상이 없는지 체크하는 method
		 **/
		function checkUpdate() {
			$oModuleModel = &getModel('module');

			// 글쓰기 상태에서 uploader.js 의 defaultHandlers 변수값을 다시 정의하도록 변경된 js 파일을 html에 삽입합니다.
			if(!$oModuleModel->getTrigger('display', 'zend_picasa', 'controller', 'triggerDisplay', 'before'))	return true;

			//이미지 파일 등록시 피카사로 파일 전송
			if(!$oModuleModel->getTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFile', 'before'))	return true;
			//if(!$oModuleModel->getTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFileafter', 'after'))	return true;
			
			//이미지 파일 삭제시 피카사도 파일 삭제
			if(!$oModuleModel->getTrigger('file.deleteFile', 'zend_picasa', 'controller', 'triggerFiledeleteFile', 'after'))	return true;
			
			//글 등록 시 피카사 앨범명 수정
			if(!$oModuleModel->getTrigger('document.insertDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after'))	return true;
			//글 수정 시 비교 후 피카사 앨범명 수정
			if(!$oModuleModel->getTrigger('document.updateDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after'))	return true;
			//글 삭제 시 피카사 앨범 삭제하기
		    if(!$oModuleModel->getTrigger('document.deleteDocument', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'before')) return true;
			//글 이동 시 파일의 upload_target_srl 과 module_srl 수정
		    if(!$oModuleModel->getTrigger('document.moveDocumentModule', 'zend_picasa', 'controller', 'triggerMoveDocument', 'before')) return true;
			//글 복사 시 파일의 upload_target_srl 과 module_srl 수정
		    if(!$oModuleModel->getTrigger('document.copyDocumentModule', 'zend_picasa', 'controller', 'triggerCopyDocument', 'before')) return true;
			//임시 저장글 삭제 시 피카사 앨범 삭제하기
		    if(!$oModuleModel->getTrigger('editor.deleteSavedDoc', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'after')) return true;

			//썸네일 주소 리턴
			if(!$oModuleModel->getTrigger('document.getThumbnail', 'zend_picasa', 'api', 'triggerGetThumbnail', 'before')) return true;

			return false;
		}

		/**
		 * @brief 업데이트 실행
		 **/
		function moduleUpdate() {
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');

			// 글쓰기 상태에서 uploader.js 의 defaultHandlers 변수값을 다시 정의하도록 변경된 js 파일을 html에 삽입합니다.
			if(!$oModuleModel->getTrigger('display', 'zend_picasa', 'controller', 'triggerDisplay', 'before'))
			{
				$oModuleController->insertTrigger('display', 'zend_picasa', 'controller', 'triggerDisplay', 'before');
			}

			//이미지 파일 등록시 피카사로 파일 전송
			if(!$oModuleModel->getTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFile', 'before'))
			{
				$oModuleController->insertTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFile', 'before');
			}
			/*if(!$oModuleModel->getTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFileafter', 'after'))
			{
				$oModuleController->insertTrigger('file.insertFile', 'zend_picasa', 'controller', 'triggerInsertFileafter', 'after');
			}*/

			//이미지 파일 삭제시 피카사도 파일 삭제
			if(!$oModuleModel->getTrigger('file.deleteFile', 'zend_picasa', 'controller', 'triggerFiledeleteFile', 'after'))
			{
				$oModuleController->insertTrigger('file.deleteFile', 'zend_picasa', 'controller', 'triggerFiledeleteFile', 'after');
			}


			//글 등록 시 피카사 앨범명 수정
			if(!$oModuleModel->getTrigger('document.insertDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after'))
			{
				$oModuleController->insertTrigger('document.insertDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after');
			}
			//글 수정 시 비교 후 피카사 앨범명 수정
			if(!$oModuleModel->getTrigger('document.updateDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after'))
			{
				$oModuleController->insertTrigger('document.updateDocument', 'zend_picasa', 'controller', 'triggerInsertDocument', 'after');
			}
		
			//글 삭제 시 피카사 앨범 삭제하기
		    if(!$oModuleModel->getTrigger('document.deleteDocument', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'before'))
		    {
        		$oModuleController->insertTrigger('document.deleteDocument', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'before');
		    }
			//글 이동 시 파일의 upload_target_srl 과 module_srl 수정
		    if(!$oModuleModel->getTrigger('document.moveDocumentModule', 'zend_picasa', 'controller', 'triggerMoveDocument', 'before'))
		    {
        		$oModuleController->insertTrigger('document.moveDocumentModule', 'zend_picasa', 'controller', 'triggerMoveDocument', 'before');
		    }
			//글 복사 시 파일의 upload_target_srl 과 module_srl 수정
		    if(!$oModuleModel->getTrigger('document.copyDocumentModule', 'zend_picasa', 'controller', 'triggerCopyDocument', 'before'))
		    {
        		$oModuleController->insertTrigger('document.copyDocumentModule', 'zend_picasa', 'controller', 'triggerCopyDocument', 'before');
		    }
			//임시 저장글 삭제 시 피카사 앨범 삭제하기
		    if(!$oModuleModel->getTrigger('editor.deleteSavedDoc', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'after'))
		    {
        		$oModuleController->insertTrigger('editor.deleteSavedDoc', 'zend_picasa', 'controller', 'triggerDeleteDocument', 'after');
		    }

			//썸네일 주소 리턴
		    if(!$oModuleModel->getTrigger('document.getThumbnail', 'zend_picasa', 'api', 'triggerGetThumbnail', 'before'))
		    {
        		$oModuleController->insertTrigger('document.getThumbnail', 'zend_picasa', 'api', 'triggerGetThumbnail', 'before');
		    }
			return new Object(0, 'success_updated');
		}

		/**
		 * @brief 캐시 파일 재생성
		 **/
		function recompileCache() {
			
		}
	}
