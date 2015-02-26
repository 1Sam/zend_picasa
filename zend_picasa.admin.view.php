<?php
	/**
	 * @class  zend_picasaAdminView
	 * @author  (csh@korea.com)
	 * @brief  zend_picasa 모듈의 AdminView class
	 **/

	class zend_picasaAdminView extends zend_picasa {

        /**
         * @brief 초기화
         **/
        function init() {
			$clientLibraryPath = realpath($this->module_path);
	        $oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
    	    require_once 'Zend/Loader.php';
        	Zend_Loader::loadClass('Zend_Gdata');
	        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    	    Zend_Loader::loadClass('Zend_Gdata_AuthSub');
        	Zend_Loader::loadClass('Zend_Gdata_Photos');
	        Zend_Loader::loadClass('Zend_Gdata_Photos_UserQuery');
    	    Zend_Loader::loadClass('Zend_Gdata_Photos_AlbumQuery');
        	Zend_Loader::loadClass('Zend_Gdata_Photos_PhotoQuery');
	        Zend_Loader::loadClass('Zend_Gdata_App_Extension_Category');

            $template_path = "./modules/zend_picasa/tpl/";
            $this->setTemplatePath($template_path);
			$args->module = 'zend_picasa';
			$oModuleModel = &getModel('module');
            $midlist=$oModuleModel->getMidList($args);
            if(count($midlist)) {
                foreach($midlist as $key=>$val) {
                    $module_info = $oModuleModel->getModuleInfoByModuleSrl($val->module_srl);
                }
            }
			$this->module_info=$module_info;
			$this->zend_picasa_config = $oModuleModel->getModuleConfig('zend_picasa');
        }

		//** 구글 웹앨범 설정
		//** 구글 로그인 정보 입력
		//** 썸네일 크기, 사용여부, 썸네일저장, 이미지 삭제 동기화 등
        function dispZend_picasaAdminConfig() {
			$oModuleModel = &getModel('module');

            Context::set('module_info',$this->module_info);
            Context::set('zend_picasa_config',$this->zend_picasa_config);

//			if(!$this->testSSLCapabilities() || $this->validatePHPExtensions()) $this->setTemplateFile('nouse');
			if(!$this->testSSLCapabilities() || !$this->validatePHPExtensions()) return new Object(-1,'nouse');
			$this->setTemplateFile('index');
        }

		// 호스팅 이미지를 피카사로 모두 전환
        function dispZend_picasaAdminMovetoPicasa() {

			$args = new stdClass;
			$args->comment = '';
			$args->isvalid = "Y";
			$args->direct_download = "Y";
			$args->file_size = 1;
			$args->source_filename1 = ".png";
			$args->source_filename2 = ".jpg";
			$args->source_filename3 = ".bmp";
			$args->source_filename4 = ".gif";
			

           	$output = executeQuery('zend_picasa.getFiles', $args);
			
			Context::set('files',$output);

			$this->setTemplateFile('move_to_picasa');
        }



		// 피카사 앨범 모듈 생성
		function dispZend_picasaAdminModule() {
			$oModuleModel = &getModel('module');
			Context::set('zend_picasa_config',$this->zend_picasa_config);
			Context::set('module_info',$this->module_info);

			$module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);
	
            // 스킨 목록을 구해옴
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

            // 레이아웃 목록을 구해옴
            $oLayoutMode = &getModel('layout');
            $layout_list = $oLayoutMode->getLayoutList();
            Context::set('layout_list', $layout_list);

            // 템플릿 파일 지정
            $this->setTemplateFile('module_insert');

		}

        function dispZend_picasaAdminGrantInfo() {
			$oModuleModel = &getModel('module');
            Context::set('zend_picasa_config',$this->zend_picasa_config);
			Context::set('module_info',$this->module_info);

            // 공통 모듈 권한 설정 페이지 호출
            $oModuleAdminModel = &getAdminModel('module');
            $grant_content = $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant);
            Context::set('grant_content', $grant_content);

            $this->setTemplateFile('grant_list');
        }

		// 피카사 앨범 모듈 삭제
		function dispZend_picasaAdminDeleteModule() {
            if(!Context::get('module_srl')) return $this->dispZend_picasaAdminModule();
            if(!in_array($this->module_info->module, array('admin', 'zend_picasa'))) {
                return $this->alertMessage('msg_invalid_request');
            }

			Context::set('zend_picasa_config',$this->zend_picasa_config);

            $module_info = $this->module_info;
            Context::set('module_info',$module_info);

            // 템플릿 파일 지정
            $this->setTemplateFile('module_delete');
        }

		 /**
         * @brief 스킨 정보 보여줌
         **/
        function dispZend_picasaAdminSkinInfo() {
			$oModuleModel = &getModel('module');
            Context::set('zend_picasa_config',$this->zend_picasa_config);
            Context::set('module_info',$this->module_info);

            // 공통 모듈 권한 설정 페이지 호출
            $oModuleAdminModel = &getAdminModel('module');
            $skin_content = $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl);
            Context::set('skin_content', $skin_content);

            $this->setTemplateFile('skin_info');
        }

		 /**
		 * Validate that SSL Capabilities are available.
		 *
		 * @return boolean False if there were errors.
		 */
		function testSSLCapabilities() {
			$sslCapabilitiesErrors = array();
			require_once 'Zend/Loader.php';
			Zend_Loader::loadClass('Zend_Http_Client');
	
			$httpClient = new Zend_Http_Client('https://www.google.com/accounts/AuthSubRequest');
			try{
				$response = $httpClient->request();
			} catch (Zend_Http_Client_Adapter_Exception $e) {
				return false;
			}
			return true;
		}
	
		function validatePHPExtensions() {
			$REQUIRED_EXTENSIONS = array('ctype', 'dom', 'libxml', 'spl', 'standard', 'openssl');
			foreach ($REQUIRED_EXTENSIONS as $requiredExtension) {
				if (!extension_loaded($requiredExtension))  return false;
			}
			return true;
		}

		/**
         * @brief board module용 메시지 출력
         **/
        function alertMessage($message) {
            $script =  sprintf('<script type="text/javascript"> xAddEventListener(window,"load", function() { alert("%s"); } );</script>', Context::getLang($message));
            Context::addHtmlHeader( $script );
        }


	}
?>
