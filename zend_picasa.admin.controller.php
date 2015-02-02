<?php
/**
 * @class  zend_picasaAdminController
 * @author  (csh@korea.com)
 * @brief  zend_picasa 모듈의 AdminController class
 **/

class zend_picasaAdminController extends zend_picasa {

	/**
	 * @brief 초기화
	 **/
	function init() {
		$clientLibraryPath = realpath('./modules/zend_picasa');
		$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata_Photos');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	}

	function procZend_picasaAdminInsertModule($args = null) {
		$oModuleController = &getController('module');
		$oModuleModel = &getModel('module');

		$args = Context::getRequestVars();
		$args->module = 'zend_picasa';
		$args->mid = $args->board_name;
		unset($args->board_name);

		if($args->module_srl) {
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
			if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
		}

		if(!$args->module_srl) {
			$output = $oModuleController->insertModule($args);
			$msg_code = 'success_registed';
		} else {
			$output = $oModuleController->updateModule($args);
			$msg_code = 'success_updated';
		}

		if(!$output->toBool()) return $output;
		$this->setMessage($msg_code);
	}

	//구글 아이디와 패스워드 저장
	function procZend_picasaAdminInsertConfig() {
		// Get the basic information
		//$args = Context::getRequestVars();

		$oModuleModel = &getModel('module');
		$zend_picasa_config = $oModuleModel->getModuleConfig('zend_picasa');
		$oModuleController = &getController('module');

		$zend_picasa_config->use = Context::get('use');
		$zend_picasa_config->user = Context::get('user');
		$zend_picasa_config->pass = Context::get('pass');
		$zend_picasa_config->maxsize = Context::get('maxsize');
		$zend_picasa_config->thumbsize = Context::get('thumbsize');
		$zend_picasa_config->delete = Context::get('delete');

		// 개별 mid 는 2개 이상의 구글 계정이 필요할 경우에 생성하도록 할 예정임
		$zend_picasa_config->mid = Context::get('mid');

		// 구글 로그인이 제대로 되는지 확인
		$serviceName = Zend_Gdata_Photos::AUTH_SERVICE_NAME;

		if($zend_picasa_config->user && $zend_picasa_config->pass) {
			@set_time_limit(10);
			try {
				$client = Zend_Gdata_ClientLogin::getHttpClient($zend_picasa_config->user, $zend_picasa_config->pass, $serviceName);
			} catch (Zend_Gdata_App_AuthException $e) {
				return new Object(-1,'LoginFailed');
			} catch (Zend_Gdata_App_HttpException $e) {
				return new Object(-1,'serverFailed');
			} catch (Zend_Http_Client_Adapter_Exception $e) {
				return new Object(-1,'SSLError');
			}
		}

		$oModuleController->insertModuleConfig('zend_picasa', $zend_picasa_config);
		$this->setMessage('success_updated');	
		//return output;
	}

	function procZend_picasaAdminDeleteModule() {
		$module_srl = Context::get('module_srl');

		$oModuleController = &getController('module');
		$output = $oModuleController->deleteModule($module_srl);
		if(!$output->toBool()) return $output;

		$this->add('module','zend_picasa');
		$this->setMessage('success_deleted');
	}

}
?>
