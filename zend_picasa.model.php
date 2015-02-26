<?php
/**
 * @class  zend_picasaModel
 * @author  (csh@korea.com)
 * @brief  zend_picasa 모듈의 Model class
 **/

class zend_picasaModel extends zend_picasa {

	/**
	 * @brief Initialization
	 **/
	function init() {
		//$clientLibraryPath = realpath('./modules/zend_picasa');
        //$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . realpath($this->module_path));

/*		$myFile = "testFilex.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = $clientLibraryPath.'//'.get_include_path().'//'.$oldPath;
		fwrite($fh, $stringData);
		fclose($fh);*/

/*		require_once 'Zend/Loader/Autoloader.php';
		Zend_Loader_Autoloader::getInstance();*/
        /*require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_Query'); // 보류
        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
        Zend_Loader::loadClass('Zend_Gdata_AuthSub');
        Zend_Loader::loadClass('Zend_Gdata_Photos');
        Zend_Loader::loadClass('Zend_Gdata_Photos_AlbumQuery');

		// get_albumid 필수
        Zend_Loader::loadClass('Zend_Gdata_Photos_UserQuery');
        Zend_Loader::loadClass('Zend_Gdata_Photos_PhotoQuery');
        Zend_Loader::loadClass('Zend_Gdata_App_Extension_Category'); //역할 모름*/

/*		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata_Photos');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_Query');
		Zend_Loader::loadClass('Zend_Gdata_Photos_UserQuery');
		Zend_Loader::loadClass('Zend_Gdata_Photos_AlbumQuery');
		Zend_Loader::loadClass('Zend_Gdata_Photos_PhotoQuery');*/
	}
	
	
	function getgp() {

		/*if(!$this->config->user) {
			$this->init();
		}*/
		//if($this->client) return $client;


		//HTTP認証オブジェクトの作成
		$service = Zend_Gdata_Photos::AUTH_SERVICE_NAME;// Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME; //

		//if(!$this->config->user || !$this->config->pass) return new Object(-1, 'msg_module_not_configured');
			//@set_time_limit(0);
		try {
			$client = Zend_Gdata_ClientLogin::getHttpClient($this->config->user, $this->config->pass , $service);
			//@set_time_limit(0);
		} catch (Zend_Gdata_App_AuthException $e) {
			$myFile = "testFilealbumnamez1.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($e,true);
			fwrite($fh, $stringData);
			fclose($fh);
        //echo "Error:0 " . $e->getResponse();
			return new Object(-1,'LoginFailed'.'hi');
		} catch (Zend_Gdata_App_HttpException $e) {
			$myFile = "testFilealbumnamez2.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($e,true);
			fwrite($fh, $stringData);
			fclose($fh);

        	$client = $this->getClient_onecemore($this->config->user, $this->config->pass , $service);

			//echo "Error:1 " . $e->getResponse();
			if(!$client) return new Object(-1,'NetworkError'.'hi1');
		}
		try {
		$this->gp = new Zend_Gdata_Photos($client);

		} catch (Zend_Gdata_App_HttpException $e) {
			return new Object(-1,'aaaa');//$this->dispZend_picasaMessage('msg_module_not_configured');
		} catch (Zend_Gdata_App_Exception $e) {
			return new Object(-1,'bbbb');//$this->dispZend_picasaMessage('msg_module_not_configured');
		}
        
		return $this->gp;
	}

	// readme_Zend_Gdata_HttpClient.txt 값으로 리턴되어 옴. 꼭 읽어보세요.

	function getClient() {
		

		if(!$this->config->user) {
			$oModuleModel = &getModel('module');
			$this->config = $oModuleModel->getModuleConfig('zend_picasa');
			//$sUser = $config->user; //GoogleApps OR GoogleAccountのメアド
			//$sPass = $config->pass; //"p@ssw0rd"; //上記メアドのログインパスワード
		}
		if($this->client) return $client;


		//HTTP認証オブジェクトの作成
		$sAuthServiceName = Zend_Gdata_Photos::AUTH_SERVICE_NAME;// Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME; //

		if(!$this->config->user || !$this->config->pass) return new Object(-1, 'msg_module_not_configured');
			//@set_time_limit(0);
		try {
			$client = Zend_Gdata_ClientLogin::getHttpClient($this->config->user, $this->config->pass , $sAuthServiceName);
			//@set_time_limit(0);
		} catch (Zend_Gdata_App_AuthException $e) {
			$myFile = "testFilealbumnamez1.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($e,true);
			fwrite($fh, $stringData);
			fclose($fh);
        //echo "Error:0 " . $e->getResponse();
			return new Object(-1,'LoginFailed'.'hi');
		} catch (Zend_Gdata_App_HttpException $e) {
			$myFile = "testFilealbumnamez2.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($e,true);
			fwrite($fh, $stringData);
			fclose($fh);

        	$client = $this->getClient_onecemore($this->config->user, $this->config->pass , $sAuthServiceName);

			//echo "Error:1 " . $e->getResponse();
			if(!$client) return new Object(-1,'NetworkError'.'hi1');
		}
		
		$this->client = $client;
		return $client;
	}
	
	function getClient_onecemore($user, $pass, $sAuthServiceName) {

		try {
			$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass , $sAuthServiceName);

		} catch (Zend_Gdata_App_AuthException $e) {
			$myFile = "testFilealbumnamez1.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($e,true);
			fwrite($fh, $stringData);
			fclose($fh);
        //echo "Error:0 " . $e->getResponse();
			return new Object(-1,'LoginFailed'.'hi');
		} catch (Zend_Gdata_App_HttpException $e) {
			$myFile = "testFilealbumnamez3.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($e,true);
			fwrite($fh, $stringData);
			fclose($fh);

        //echo "Error:1 " . $e->getResponse();
			return new Object(-1,'NetworkError'.'hi1');
		}

		return $client;
	}

	function modifyAlbum($client, $user, $albumId,$name,$public) {
        $photos = new Zend_Gdata_Photos($client);

		$albumQuery = new Zend_Gdata_Photos_AlbumQuery;
        $albumQuery->setUser($user);
        $albumQuery->setAlbumId($albumId);
        $albumQuery->setType('entry');

		$entry = $photos->getAlbumEntry($albumQuery);

        $entry->setGphotoAccess($photos->newAccess($public));
        $entry->setTitle($photos->newTitle($name));
		$result = $entry->save();
		$cache_file = "./files/cache/zend_picasa/zend_picasa.*.cache.php";
        foreach (glob($cache_file) as $filename) {
           unlink($filename);
        }

/*	  	$myFile = "testFilealbumname2.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = 'Picasa AlbumName : '. print_r($this,true);
		fwrite($fh, $stringData);
		fclose($fh);*/

		return $result;
    }

    function addAlbum($client, $user, $name, $public) {
        $photos = new Zend_Gdata_Photos($client);

        $entry = new Zend_Gdata_Photos_AlbumEntry();
		$entry->setGphotoAccess($photos->newAccess($public));
        $entry->setTitle($photos->newTitle($name));

        $result = $photos->insertAlbumEntry($entry);
		$cache_file = "./files/cache/zend_picasa/zend_picasa.*.cache.php";
        foreach (glob($cache_file) as $filename) {
           unlink($filename);
        }
		//새앨범을 생성하고 앨범 아이디 리턴
		return $result->gphotoId->text;
    }

    function deleteAlbum($client, $user, $albumId)  {
        $photos = new Zend_Gdata_Photos($client);
		try { //
			$albumQuery = new Zend_Gdata_Photos_AlbumQuery;
			$albumQuery->setUser($user);
			$albumQuery->setAlbumId($albumId);
			$albumQuery->setType('entry');
	
			$entry = $photos->getAlbumEntry($albumQuery);
			/*$cache_file = "./files/cache/zend_picasa/zend_picasa.*.cache.php";
			foreach (glob($cache_file) as $filename) {
			   unlink($filename);
			}*/
			$photos->deleteAlbumEntry($entry, true);
		} catch (Zend_Gdata_App_HttpException $e) {
			// 피카사 앨범을 임의로 삭제한 경우, 블로그의 글을 삭제할 때 에러가 발생하기에
			// try로 오류 무시
			return new Object(-1,'NetworkError'.'앨범삭제시에 문제가 있습니다.'.$e.$entry);
		}
		return new Object(0, '앨범 삭제 성공');
    }

	function addPhoto($client, $user, $albumId, $title, $photo, $caption, $tags) {
        $photos = new Zend_Gdata_Photos($client);
		$filename = FileHandler::getRealPath($photo);

		$fd = $photos->newMediaFileSource($filename);
		$fd->setContentType("image/jpeg");

		// Create a PhotoEntry
		$photoEntry = $photos->newPhotoEntry();

		$photoEntry->setMediaSource($fd);	
		if($title) $photoEntry->setTitle($photos->newTitle($title));
		$photoEntry->setSummary($photos->newSummary($caption)); // 캡션, 이미지가 사용되는 블로그의 실제 페이지 링크주소

		// add some tags
		$keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
		$keywords->setText($tags); // 태그값 ','로 구분
		$photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
		$photoEntry->mediaGroup->keywords = $keywords;

		// We use the AlbumQuery class to generate the URL for the album
		$albumQuery = $photos->newAlbumQuery();

		$albumQuery->setUser($user);
		$albumQuery->setAlbumId($albumId);

		// 사진추가시 서버에 임시저장된 이미지 파일 제거
		/*$cache_file = sprintf("./files/cache/zend_picasa/%s.*.cache.php", $albumId);
		foreach (glob($cache_file) as $filename) {
           unlink($filename);
        }*/

// We insert the photo, and the server returns the entry representing
		// that photo after it is uploaded
		$result = $photos->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl()); 
        //$content = $result->mediaGroup->content;
        //$tmp = $result->mediaGroup->content[0];
        //$output->url =$tmp->url;
		$output->photoId = $result->gphotoId->text;
        $output->width = $result->gphotoWidth->text;//$tmp->width;
        $output->height = $result->gphotoHeight->text;//$tmp->height;
		$output->size = $result->gphotoSize->text;//, true);
		//$output->title = $result->title->text;
		//$output->urlencoded=urlencode( $output->title);
		$output->url = str_replace(basename($result->content->src),'s0/'.$result->title->text,$result->content->src);

		//$output->replcased= str_replace($output->urlencoded,'',$output->url);

		/*$myFile = "testFileaddPhoto8.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = 'Picasa AlbumName : '. print_r($result,true);
		fwrite($fh, $stringData);
		fclose($fh);*/
        
		/*$myFile = "testFileaddPhoto9.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData = 'Picasa AlbumName : '. print_r($output,true);
		fwrite($fh, $stringData);
		fclose($fh);*/
		
		
        return $output;
    }


	function deletePhoto($client, $user, $albumId, $photoId) {

		if(!$this->gp) $this->getgp();
        //$photos = new Zend_Gdata_Photos($client);
		$photos = $this->getgp();
		try {
			$photoQuery = new Zend_Gdata_Photos_PhotoQuery;
			$photoQuery->setUser($user);
			$photoQuery->setAlbumId($albumId);
			$photoQuery->setPhotoId($photoId);
			$photoQuery->setType('entry');
	
			$entry = $photos->getPhotoEntry($photoQuery);
	
			$result = $photos->deletePhotoEntry($entry, true);
		/*$cache_file = sprintf("./files/cache/zend_picasa/%s.*.cache.php", $albumId);
        foreach (glob($cache_file) as $filename) {
           unlink($filename);
        }*/

 		} catch (Zend_Gdata_App_HttpException $e) {
			/*$myFile = "testFileaddPhoto9.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($e,true).print_r($result,true);
			fwrite($fh, $stringData);
			fclose($fh);*/
			return new Object(-1, $e);
		}
		return new Object(0, $result->body);
    }


	//DB의 sid에는 albumId#photoId 로 저장되어 있음
	//2개의 값을 분리하여 앨범 아이디만 리턴
	function getAlbumidFromSid($upload_target_srl) {
		$args = new stdClass();
		$args->upload_target_srl = $upload_target_srl;

		$output = executeQueryArray('zend_picasa.getAlbumidFromSid', $args);

		foreach($output->data as $key => $item) {
			if(strlen($item->sid) != 32) $albumIdx = $item->sid;
		}
		$ids = explode('#',$albumIdx);
		$albumId = $ids[0];

		// DB에 저장된 이미지의 sid 값, 없으면 NULL
		return $albumId; //
	}

	//DB의 sid에는 albumId#photoId 로 저장되어 있음
	//2개의 값을 분리하여 앨범 아이디만 리턴
	function getAlbumidFromSid_($upload_target_srl) {
		$args = new stdClass();
		$args->upload_target_srl = $upload_target_srl;

		$output = executeQueryArray('zend_picasa.getAlbumidFromSid', $args);

		foreach($output->data as $item) {
			if(preg_match("/(\w+)#/i",$item->sid, $matches)) return $matches[1];
			//if(strpos($item->sid, '#') !== false) return explode('#',$item->sid);
		}
		return NULL; //
	}

	function getUploadTargetBySid($sid) {
		$args = new stdClass();
		$args->sid = $upload_target_srl;

		$output = executeQueryArray('zend_picasa.getUploadTargetBySid', $args);
		return $output->data[0];
	}


	//DB의 sid에는 albumId#photoId 로 저장되어 있음
	//2개의 값을 분리하여 배열로 리턴
	function getAlbumidFromSids($upload_target_srl) {
		$args = new stdClass();
		$args->upload_target_srl = $upload_target_srl;

		$output = executeQueryArray('zend_picasa.getAlbumidFromSid', $args);
		foreach($output->data as $item) {
			if(strlen($item->sid) != 32) $sid = $item->sid;
		}
		$sidz = explode('#',$sid);
		$sids = new stdClass();
		$sids->albumId = $sidz[0];
		$sids->photoId = $sidz[1];		
		// DB에 저장된 이미지의 sid 값, 없으면 NULL
		return $sids; // $albumId[0]은 앨범 아이디, $albumId[1]은 포토 아이디
	}




	// 피카사 이미지 게시글을 이동할 때는 files DB 내용만 수정
	// 이미지 파일은 피카사로, 일반 파일은 호스팅 서버로 업로드 ($output->error = 0)
	function zend_picasa_upload($file_info, $module_srl, $upload_target_srl) {

		// 이미지 파일은 피카사로 업로드한다.
		if(preg_match("/\.(jpe?g|gif|png|bmp)$/i", $file_info['name']))	{

			// 피카사 용량 무제한의 이미지 규격은 2048*2048
			// zend_picasa 모듈 옵션에서 최대치를 지정해 줄 수 있음
			if($this->config->maxsize) {
				include('SimpleImage.php');
				$image = new SimpleImage();
				$image->load($file_info['tmp_name']);
				$image->resizeToWidth($this->config->maxsize);
				$image->save($file_info['tmp_name']);			
			}
			//위의 내용은 파일이 저장될 장소와 이름만을 정의함
			//파일이 삽입되는 시점에 피카사에 같이 업로드 한다.
			//이미 파일은 템프로 서버에 저장된 상태임.

			$direct_download = 'Y';
			//$tmp_name = $file_info['tmp_name'];// 임시폴더에 업로드된 이미지 파일
			//$fname = $file_info['name']; // 파일의 이름

			$client = $this->getClient();
			
			//앨범 아이디가 없으면 새앨범생성후 그 값을 취함
			$albumId = $this->getAlbumidFromSid_($upload_target_srl);
			$album_title = $upload_target_srl;
			if(!$albumId) $albumId = $this->addAlbum($client, $this->config->user, $album_title, 'private');

			/*$myFile = "testFileaddPhoto1.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($this,true);
			fwrite($fh, $stringData);
			fclose($fh);*/

			$caption = getUrl().$upload_target_srl; 
			$tags = "XE, 1Samonline, 일쌤온라인";
			$result = $this->addPhoto($client, $this->config->user, $albumId, $file_info['name'], $file_info['tmp_name'], $caption, $tags);
			//@unlink($tmp_name);


			/*$myFile = "testFileaddPhoto2.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($result,true);
			fwrite($fh, $stringData);
			fclose($fh);*/

			// Get member information
           	$oMemberModel = &getModel('member');
           	$member_srl = $oMemberModel->getLoggedMemberSrl();
          	 	
			// BD 저장용
			//file_srl  upload_target_srl  upload_target_type  sid  module_srl  member_srl  download_count  direct_download  source_filename  uploaded_filename  file_size  comment  isvalid  regdate ipaddress  file_order  
			$args = new stdClass;
            $args->file_srl = getNextSequence();
           	$args->upload_target_srl = $upload_target_srl;
			$args->sid = $albumId.'#'.$result->photoId; //앨범아이디와 포토아이디를 같이 기록함
   	        $args->module_srl = $module_srl;
       	 	$args->member_srl = $member_srl;
         	$args->download_count = 0;
   	     	$args->direct_download = $direct_download;
   	     	$args->source_filename = $file_info['name'];
         	$args->uploaded_filename = $result->url;//$picasa_uploaded_info[0]; 
         	$args->file_size = $result->size;//$file_info['size'];
			$args->comment = $result->width.'x'.$result->height;//$picasa_uploaded_info[2]; //이미지 높이-후에 이값을 제거하고 s72 를 대입하여 썸네일 원본 파일로 쓰임 //NULL; //"link";

           	$output = executeQuery('file.insertFile', $args);

			/*$myFile = "testFileaddPhoto3.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($output,true);
			fwrite($fh, $stringData);
			fclose($fh);*/

           	if(!$output->toBool()) return $output;

			$_SESSION['__XE_UPLOADING_FILES_INFO__'][$args->file_srl] = true;

	        $output->add('file_srl', $args->file_srl);
    	    $output->add('file_size',  $args->file_size); //$picasa_uploaded_info[3]);//
            $output->add('sid', $args->sid);
           	$output->add('direct_download', $args->direct_download);
           	$output->add('source_filename', $args->source_filename);
           	$output->add('upload_target_srl', $upload_target_srl);
           	$output->add('uploaded_filename', $args->uploaded_filename);
			
			//템프 파일 자체를 지워버림?????????????????????
			FileHandler::removeFile($file_info['tmp_name']);
			/*$myFile = "testFileaddPhoto4.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = 'Picasa AlbumName : '. print_r($output,true);
			fwrite($fh, $stringData);
			fclose($fh);*/

           	return $output;
		}
		return new Object(-1, '이미지 파일이 아님');
	}


	function upload_picasa($tmp_name, $source_filename, $module_srl, $upload_target_srl) {
		//xe files/images/ 폴더에 저장되는 실제 폴더를 피카사에도 통일함
		//$album_title = $module_srl."/".getNumberingPath($upload_target_srl,3);
		//앨범명은 문서가 작성된 후에 trigger를 이용해 문서 제목으로 변경해줌
		$album_title ='temp';
		//피카사 업로드 시작

		$username = "default"; // 유저이름
		$photoName = $source_filename; // 실제 파일
		//피카사의 해당 이미지에 실제 이미지 올린 글을 링크하도록 캡션(제목)을 달아 줌
		$photoCaption = getUrl().$upload_target_srl; 
		$photoTags = "XE, 1Samonline, 일쌤온라인";
		//$albumId = "5836285696291714177";
		//$tmp_name = $_SERVER['DOCUMENT_ROOT']."/tmp/photo.jpg"; //성공
		//$tmp_name = $file_info['name'];
	
		$client = $this->getClient();
		$gp = new Zend_Gdata_Photos($client);

		// Find the album for the given accountId.
		$albumQuery = $gp->newAlbumQuery();
		$albumQuery->setUser($user); //계정 아이디 또는 email
		$albumQuery->setAlbumName(str_replace('/','',$album_title)); // No spaces.앨범 타이틀에 들어가 있는 슬래쉬(/)를 제거해야함
		$albumQuery->setMaxResults( 1 );
		

		// 이미지가 저장될 albumId를 구함
		// DB에서 해당 게시글의 이미지 파일을 검색하고 sid가 32자리가 아닌 것을 albumId로 취함

		$albumId = $this->getAlbumidFromSid_($upload_target_srl);


		if(!$albumId) {
			//앨범 아이디를 발견하지 못한 경우 새로운 앨범을 생성함
			//Add an Album
			$albumentry = new Zend_Gdata_Photos_AlbumEntry();
			$albumentry->setTitle($gp->newTitle($album_title));
			$albumentry->setSummary($gp->newSummary($albumId));
			//$albumentry->setGphotoAccess($gp->newAccess("public")); //공유가능
			$albumentry->setGphotoAccess($gp->newAccess("private")); //공유불능
				
			$createdEntry = $gp->insertAlbumEntry($albumentry);
			$albumId = str_replace('albumid/','',strstr($createdEntry->Id->text, 'albumid/'));
		}




/*		//예날 방식임
		//앨범 아이디를 구하기 위함임
		try {
			$albumFeed = $gp->getAlbumFeed( $albumQuery );
		  
			foreach( $albumFeed as $key => $entry ) {
				$albumId = $entry->getGphotoAlbumId();
			}
		} catch( Zend_Gdata_App_Exception $ex ) {

			//앨범 아이디를 발견하지 못한 경우 새로운 앨범을 생성함
			// Create the album because the album name could not be found.
			//$albumId = $this->createAlbum( $gp, $album_title );
			
			//Add an Album
			$albumentry = new Zend_Gdata_Photos_AlbumEntry();
			$albumentry->setTitle($gp->newTitle($album_title));
			$albumentry->setSummary($gp->newSummary($albumId));
			//$albumentry->setGphotoAccess($gp->newAccess("public")); //공유가능
			$albumentry->setGphotoAccess($gp->newAccess("private")); //공유불능
				
			$createdEntry = $gp->insertAlbumEntry($albumentry);
							
			// Get Albumid
			//$albumId = $createdEntry->albumId->getText;
			//$albumId = $createdEntry->getGphotoAlbumId()->text;
			//$photoCaption = $createdEntry->Id->text; //앨범의 주소 포함 https://picasaweb.google.com/data/entry/api/user/108515335692917089918/albumid/5836891111203540929
			$albumId = str_replace('albumid/','',strstr($createdEntry->Id->text, 'albumid/'));
		}
	
*/
		
		//$photoCaption = $albumId;
		
		/*	if ($albumId == NULL) {
			//Add an Album
			$albumentry = new Zend_Gdata_Photos_AlbumEntry();
			$albumentry->setTitle($gp->newTitle($album_title));
			$albumentry->setSummary($gp->newSummary($albumId));
			//$albumentry->setGphotoAccess($gp->newAccess("public")); //공유가능
			$albumentry->setGphotoAccess($gp->newAccess("private")); //공유불능
				
			$createdEntry = $gp->insertAlbumEntry($albumentry);
							
			// Get Albumid
			//$albumId = $createdEntry->albumId->getText;
			//$albumId = $createdEntry->getGphotoAlbumId()->text;
			//$photoCaption = $createdEntry->Id->text; //앨범의 주소 포함 https://picasaweb.google.com/data/entry/api/user/108515335692917089918/albumid/5836891111203540929
			$albumId = str_replace('albumid/','',strstr($createdEntry->Id->text, 'albumid/'));
		}	*/

		// We use the AlbumQuery class to generate the URL for the album
		//	$albumQuery = $gp->newAlbumQuery();
		//	$albumQuery->setUser($username);
			//$albumQuery->setTitle("Album Test Tile");//포토 올릴 때는 오류
			//$albumQuery->setSummary("test album");// 포토 올릴 때는 오류
		$albumQuery->setAlbumId($albumId);
							
		//$albumEntry = $gp->newAlbumEntry();
		//$albumentry->setTitle($gp->newTitle("New album"));
		//$albumentry->setSummary($gp->newSummary("This is an album."));
		//$insertedalbumEntry = $gp->insertAlbumEntry($albumEntry);
	
		$fd = $gp->newMediaFileSource($tmp_name);
		$fd->setContentType("image/jpeg");
	
		// Create a PhotoEntry
		$photoEntry = $gp->newPhotoEntry();
		//여기에서 파일 업로드함
		$photoEntry->setMediaSource($fd);
		$photoEntry->setTitle($gp->newTitle($photoName));
		$photoEntry->setSummary($gp->newSummary($photoCaption));
			
		// add some tags
		$keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
		$keywords->setText($photoTags);
		$photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
		$photoEntry->mediaGroup->keywords = $keywords;
				
		// We insert the photo, and the server returns the entry representing
		// that photo after it is uploaded
		$insertedEntry = $gp->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl());
	
		//가로가 크면 가로를 보냄
		/* ver 0.1
		if( $insertedEntry->getGphotoWidth()->text > $insertedEntry->getGphotoHeight()->text) {
			$val_s = "s".$insertedEntry->getGphotoWidth();
		} else {
			$val_s = "s".$insertedEntry->getGphotoHeight();
		}
		//리턴할 이미지의 주소, 앨범 아이디, 이미지 높이,사이즈 -  /s72/를 height로 수정
		$url = str_replace('/s72/', "/".$val_s."/", $insertedEntry->getMediaGroup()->Thumbnail[0]->url);
	
		$picasa_uploaded_info = array ($url, $albumId, $val_s, $size);
				
		$size = $insertedEntry->getGphotoSize();
		
		*/
	
	
		//ver 0.2
		//구글 이미지의 너비와 높이 모두를 기록한다.
		//썸네일 불러올 때 섬네일 크기값을 구하기 힘들어서 둘다 저장하기로 함.
		//split(replace$file->comment,'x')함수를 이용해서 너비와 높이를 구별해서 사용
		
		$val_s = $insertedEntry->getGphotoWidth().'x'.$insertedEntry->getGphotoHeight();
		//url
		
		$easy_url = split('/s72/',$insertedEntry->getMediaGroup()->Thumbnail[0]->url);

		//xe db 저장을 위한 리턴값
		//$picasa_uploaded_info = (빅사이즈 피카사 이미지 주소, 앨범아이디, 이미지 높이와 너비, 파일크기)
		$picasa_uploaded_info = array ($easy_url[0].'/s0/', $albumId, $val_s, $size);
		
		//https://lh6.googleusercontent.com/-_HMCILS8k2U/Uh66rrPlLvI/AAAAAAAAHDs/c4ra0aDVH_I/s2560/P8277230.JPG
		//피카사 업로드 끝
				
	  //return $insertedEntry;
		return $picasa_uploaded_info;
	}

function get_albumidByTitle($client, $user, $pass, $album_title) {
		//$client = $this->getClient();
		$gp = new Zend_Gdata_Photos($client);

		// Find the album for the given accountId.
		$albumQuery = $gp->newAlbumQuery();
		$albumQuery->setUser($user); //계정 아이디 또는 email
		$albumQuery->setAlbumName($album_title);
		//$albumQuery->setAlbumName(str_replace('/','',$album_title)); // No spaces.앨범 타이틀에 들어가 있는 슬래쉬(/)를 제거해야함
		$albumQuery->setMaxResults( 1 );

		try {
			$albumFeed = $gp->getAlbumFeed( $albumQuery );
		  
			/*foreach( $albumFeed as $entry ) {
				$albumId = $entry->getGphotoAlbumId()->text;
			}*/
			$albumId = $albumFeed->getGphotoAlbumId()->text;
		} catch( Zend_Gdata_App_Exception $ex ) {
			//앨범 아이디 받기 실패
			return false;
		}
		return $albumId;

			/*$myFile = "testFile_get_albumid.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
		    $stringData = print_r($albumFeed, true).'/////////////////'.$albumId;
    		fwrite($fh, $stringData);
    		fclose($fh);*/		
}

//
//앨범의 '제목'으로 앨범 '아이디' 값을 구함
//
function get_albumid2($user, $pass, $album_title) {
	//$album_title = 'temp';
	/*ini_set("include_path", '/home2/samon:' . ini_get("include_path")  );
	require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata_Photos');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	        				
	$serviceName = Zend_Gdata_Photos::AUTH_SERVICE_NAME;
	$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $serviceName);

	// update the second argument to be CompanyName-ProductName-Version
	$gp = new Zend_Gdata_Photos($client, "Google-DevelopersGuide-1.0");*/
	
/*	ini_set("include_path", '/home2/samon:' . ini_get("include_path")  );
	require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata_Photos_UserQuery');
	Zend_Loader::loadClass('Zend_Gdata_Photos_AlbumQuery');
*/	
		$client = $this->getClient();
		$gp = new Zend_Gdata_Photos($client);

    $uquery = new Zend_Gdata_Photos_UserQuery();
	$aquery = new Zend_Gdata_Photos_AlbumQuery();
    $aquery->setUser("csh@korea.com");
    $aquery->setAlbumId("1");
    //$aquery->setType("entry");
	
				//텍스트파일로 저장하여 확인하는 과정 시작
			$myFile = "testFile_get_albumid.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
		    $stringData = print_r($aquery, true);
    		fwrite($fh, $stringData);
    		fclose($fh);
			//끝

    try {
        $albumFeed = $gp->getAlbumFeed($aquery);
		

    } catch (Zend_Gdata_App_Exception $e) {
        echo "Error: " . $e->getResponse();
    }
     

	try {
		$userFeed = $gp->getUserFeed(null, $uquery);
		//$userFeed = $gp->getUserFeed("default");
		//앨범 타이틀이 엔트리의 타이들에 포함되어 있을 경우, 해당 앨범ID를 리턴함 


		foreach ($userFeed as $userEntry) {
			if($album_title == $userEntry->title->text) {
				
			
				/* //같은 이름을 가진 파일 삭제하여 오버라이트함.
				try {
					//$photo = $gp->getPhotoEntry( 'http://picasaweb.google.com/data/entry/api/user/xxxxx/albumid/yyyyy/photoid/zzzzz');

					//$photos = $gp->getPhotoEntry();
					$myFile = "testFile.txt";
					$fh = fopen($myFile, 'w') or die("can't open file");
					$stringData = print_r($userEntry, true);
					fwrite($fh, $stringData);
					fclose($fh);
					
					foreach($photos as $photo)
						if($photo_fn == $photo->title-text) {
							$photo->delete();											
						}
  				} catch (Zend_Gdata_App_Exception $e) {
					
				echo "Error: " . $e->getResponse();
				$myFile = "testFilex.txt";
				$fh = fopen($myFile, 'w') or die("can't open file");
				$stringData = print_r($e, true);
				fwrite($fh, $stringData);
				fclose($fh);
				} */
			$myFile = "testFile_get_albumid2.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = print_r($userFeed, true);
		    //$stringData = print_r($userEntry->gphotoId->text, true);
    		fwrite($fh, $stringData);
    		fclose($fh);
						
				return str_replace('albumid/','',strstr($userEntry->id->text, 'albumid/')); // albumId 리턴
			} //end if						
		} //end foreach
	} catch (Zend_Gdata_App_Exception $e) {
        echo "Error: " . $e->getResponse();
    //}catch (Zend_Gdata_App_HttpException $e) {
	    //echo "Error: " . $e->getMessage() . "<br />\n";
	    //if ($e->getResponse() != null) {
		//	echo "Body: <br />\n" . $e->getResponse()->getBody() . "<br />\n"; 
		//}
				
	    // In new versions of Zend Framework, you also have the option
    	// to print out the request that was made.  As the request
	    // includes Auth credentials, it's not advised to print out
	    // this data unless doing debugging
	    // echo "Request: <br />\n" . $e->getRequest() . "<br />\n";
		
	} /*catch (Zend_Gdata_App_Exception $e) {
	    //echo "Error: " . $e->getMessage() . "<br />\n"; 
	}*/
	return NULL;
}



	function deleteThumbnail($album, $photoid) {
        $thumbnail_file = sprintf('files/cache/thumbnails/zend_picasa/%s/%s.jpg',$album,$photoid);
//        $tmp_file = sprintf('./files/cache/tmp/zend_picasa/%d', $photoid);
//        @unlink($tmp_file);
        @unlink($thumbnail_file);
        $thumbnail_url = 'files/cache/thumbnails/zend_picasa/nophoto.jpg';
       return $thumbnail_url;
	}

	function getThumbnail($source_file, $album, $photoid,$width = 96) {
		//캐시 파일을 생성하지 않고 바로 리턴함
		return $source_file;

        /*if(!$source_file) return false;

        $height = $width;
        $thumbnail_type = 'crop';

        $thumbnail_file = sprintf('files/cache/thumbnails/zend_picasa/%s/%s.jpg',$album,$photoid);
        $thumbnail_url  = Context::getRequestUri().$thumbnail_file;

        if(file_exists($thumbnail_file)) {
			list($w,$h) =@getimagesize($thumbnail_file);
            if(filesize($thumbnail_file)<1) return $source_file;
            elseif($w == $width) return $thumbnail_url;
			else unlink($thumbnail_file);
        }

		if(!preg_match('/^(http|https):\/\//i',$source_file)) $source_file = Context::getRequestUri().$source_file;
            $tmp_file = sprintf('./files/cache/tmp/zend_picasa/%d', $photoid);
            if(!is_dir('./files/cache/tmp/zend_picasa')) FileHandler::makeDir('./files/cache/tmp/zend_picasa');
			$this->delOldFiles('./files/cache/tmp/zend_picasa');

            FileHandler::getRemoteFile($source_file, $tmp_file);
            if(!file_exists($tmp_file)) return $source_file;
        else {
           	list($_w, $_h, $_t, $_a) = @getimagesize($tmp_file);
            if($_w<$width && $_h<$height) return $source_file;

            $source_file = $tmp_file;
          	$is_tmp_file = true;
		}

        $output = FileHandler::createImageFile($source_file, $thumbnail_file, $width, $height, 'jpg', $thumbnail_type);
		if($is_tmp_file) @unlink($tmp_file);
	
        // 썸네일 생성 성공시 경로 return
        if($output) return $thumbnail_url;

        return;*/
    }
	

	function addPhoto2($client, $user, $albumId,$title,$photo,$summary='',$tag='') {
        $photos = new Zend_Gdata_Photos($client);
		$filename = FileHandler::getRealPath($photo);

		$fd = $photos->newMediaFileSource($filename);
		$fd->setContentType("image/jpeg");

		// Create a PhotoEntry
		$photoEntry = $photos->newPhotoEntry();

		$photoEntry->setMediaSource($fd);	
		if($title) $photoEntry->setTitle($photos->newTitle($title));
		$photoEntry->setSummary($photos->newSummary($summary));

		// add some tags
		$keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
		$keywords->setText($tag);
		$photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
		$photoEntry->mediaGroup->keywords = $keywords;

		// We use the AlbumQuery class to generate the URL for the album
		$albumQuery = $photos->newAlbumQuery();

		$albumQuery->setUser($user);
		$albumQuery->setAlbumId($albumId);

		$cache_file = sprintf("./files/cache/zend_picasa/%s.*.cache.php", $albumId);
		foreach (glob($cache_file) as $filename) {
           unlink($filename);
        }

// We insert the photo, and the server returns the entry representing
		// that photo after it is uploaded
		$result = $photos->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl()); 
        $content = $result->mediaGroup->content;
        $temp=$content[0];
        $output->url =$temp->url;
        $output->width =$temp->width;
        $output->height=$temp->height;
        return $output;
    }


    function deleteComment($client, $user, $albumId, $photoId, $commentId) {
        $photos = new Zend_Gdata_Photos($client);

        $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
        $photoQuery->setUser($user);
        $photoQuery->setAlbumId($albumId);
        $photoQuery->setPhotoId($photoId);
        $photoQuery->setType('entry');

        $path = $photoQuery->getQueryUrl() . '/commentid/' . $commentId;

        $entry = $photos->getCommentEntry($path);

        $photos->deleteCommentEntry($entry, true);
    }

	function getRecentPhoto($client,$user,$limit) {
		if(!$limit) $limit=10;
//		$photos = new Zend_Gdata_Photos($client);

		try {
        	$photos = new Zend_Gdata_Photos($client);
        } catch (Zend_Gdata_App_HttpException $e) {
            return $this->dispZend_picasaMessage('msg_module_not_configured');
        } catch (Zend_Gdata_App_Exception $e) {
            return $this->dispZend_picasaMessage('msg_module_not_configured');
        }

		$query = $photos->newUserQuery();
		$query->setUser($user);
		$query->setKind("photo");
		$query->setMaxResults($limit);

    	$userFeed = $photos->getUserFeed(null,$query);

    	foreach ($userFeed as $photoEntry) {
			$data=null;
			$data->photoid = $photoEntry->gphotoId->text;
			$data->album = $photoEntry->gphotoAlbumId->text;
			$timestamp= $photoEntry->gphotoTimestamp->text;
			$data->timestamp=gmstrftime("%Y-%m-%d %H:%M:%S",substr($timestamp,0,-3));

			$data->commentcount = $photoEntry->gphotoCommentCount->text;
			$media = $photoEntry->getMediaGroup();
			$data->title = $media->title->text;
			$data->summary = $media->description->text;
			$thumb = $photoEntry->getMediaGroup()->getThumbnail();
            $data->thumb = $thumb[2]->getUrl();
            $data->thumb_url = $this->getThumbnail($data->thumb, $data->album,$data->photoid,100);

			$output[]=$data;
    	}
		return $output;
	}


	function makePublicAlbum($client, $user, $albumId) {
        $photos = new Zend_Gdata_Photos($client);

        $albumQuery = new Zend_Gdata_Photos_AlbumQuery;
        $albumQuery->setUser($user);
        $albumQuery->setAlbumId($albumId);
        $albumQuery->setType('entry');

        $entry = $photos->getAlbumEntry($albumQuery);

        $entry->setGphotoAccess($photos->newAccess('public'));
        $result = $entry->save();
		$cache_file = "./files/cache/zend_picasa/zend_picasa.*.cache.php";
        foreach (glob($cache_file) as $filename) {
           unlink($filename);
        }
		return $result;
    }


	function addComment($client, $user, $album, $photo, $comment) {
        $photos = new Zend_Gdata_Photos($client);

        $entry = new Zend_Gdata_Photos_CommentEntry();
        $entry->setContent($photos->newContent($comment));

        $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
        $photoQuery->setAlbumId($album);
        $photoQuery->setPhotoId($photo);
        $photoQuery->setType('entry');

        $photoEntry = $photos->getPhotoEntry($photoQuery);
        $result = $photos->insertCommentEntry($entry, $photoEntry);

        return $result;
    }

	function addTag($client, $user, $album, $photo, $tag) {
		$photos = new Zend_Gdata_Photos($client);

        $entry = new Zend_Gdata_Photos_TagEntry();
        $entry->setTitle($photos->newTitle($tag));
        if($summary) $entry->setSummary($photos->newSummary($summary));

        $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
        $photoQuery->setUser($user);
        $photoQuery->setAlbumId($album);
        $photoQuery->setPhotoId($photo);
        $photoQuery->setType('entry');

        $photoEntry = $photos->getPhotoEntry($photoQuery);

        $result = $photos->insertTagEntry($entry, $photoEntry);
		return $result;
	}

	function deleteTag($client, $user, $albumId, $photoId, $tagContent) {
	    $photos = new Zend_Gdata_Photos($client);

    	$photoQuery = new Zend_Gdata_Photos_PhotoQuery;
	    $photoQuery->setUser($user);
    	$photoQuery->setAlbumId($albumId);
	    $photoQuery->setPhotoId($photoId);
    	$query = $photoQuery->getQueryUrl() . "?kind=tag";

	    $photoFeed = $photos->getPhotoFeed($query);

    	foreach ($photoFeed as $entry) {
        	if ($entry instanceof Zend_Gdata_Photos_TagEntry) {
            	if ($entry->getContent() == $tagContent) {
                	$tagEntry = $entry;
		        }
    		}
	    }
    	$photos->deleteTagEntry($tagEntry, true);
	}

	function modifyPhoto($client, $user, $albumId,$photoId, $title, $tag, $summary){
		Zend_Loader::loadClass('Zend_Gdata_Media_Extension_MediaKeywords');
		Zend_Loader::loadClass('Zend_Gdata_Geo_Extension_GeoRssWhere');
		Zend_Loader::loadClass('Zend_Gdata_Geo_Extension_GmlPos');
		Zend_Loader::loadClass('Zend_Gdata_Geo_Extension_GmlPoint');

	 	$photos = new Zend_Gdata_Photos($client);

		$photoQuery = new Zend_Gdata_Photos_PhotoQuery;
        $photoQuery->setUser($user);
        $photoQuery->setAlbumId($albumId);
        $photoQuery->setPhotoId($photoId);
        $photoQuery->setType('entry');

		$insertedEntry = $photos->getPhotoEntry($photoQuery);

		$insertedEntry->title->text = $title;
		$insertedEntry->summary->text = $summary;

		$keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
		$keywords->setText($tag);
		$insertedEntry->mediaGroup->keywords = $keywords;
    
		$where = new Zend_Gdata_Geo_Extension_GeoRssWhere();
		$position = new Zend_Gdata_Geo_Extension_GmlPos('37.0 -122.0');
		$where->point = new Zend_Gdata_Geo_Extension_GmlPoint($position);
		$insertedEntry->setGeoRssWhere($where); 

		$updatedEntry = $insertedEntry->save();
		$cache_file = sprintf("./files/cache/zend_picasa/%s.*.cache.php", $albumId);
        foreach (glob($cache_file) as $filename) {
           unlink($filename);
        }
	}

	function getUserFeed($userFeed,$module_srl) {
		if(!count($userFeed)) return false;
		$zend_picasa->nickname = $userFeed->getGphotoNickname();
		$zend_picasa->title = $userFeed->getTitle();
		$zend_picasa->total_count = $userFeed->key();
		$extension = $userFeed->extensionElements;
		$quotalimit=intval($extension[0]->text);
		$quotacurrent = intval($extension[1]->text);
		if($quotalimit && $quotacurrent) $zend_picasa->quotaused = number_format(($quotacurrent/$quotalimit) * 100,2);
		$zend_picasa->quotalimit = $this->getMega($quotalimit);
		$zend_picasa->quotacurrent = $this->getMega($quotacurrent);
		foreach ($userFeed as $key=>$entry) {
                if ($entry instanceof Zend_Gdata_Photos_AlbumEntry) {
                    $data->albumid = $entry->getGphotoId()->getText();
                    $data->numphotos =  $entry->getGphotoNumPhotos()->getText();
                    $thumb = $entry->getMediaGroup()->getThumbnail();
                    $data->thumb = $thumb[0]->getUrl();
                    $data->albumtitle = $entry->getTitle();
                    $access = $entry->getGphotoAccess();
                    if($access=='public') $data->access=1;
                    else $data->access=0;
                    $tempdata[] = $data;
                    unset($data);
                }
			$zend_picasa->entry = $tempdata;
        }
		return $zend_picasa;
	}

	function getAlbumFeed($albumFeed) {
		$album = $albumFeed->getGphotoId()->getText();
		if(!count($albumFeed)) return false;
		$access = $albumFeed->getGphotoAccess();
		if($access=='public') $zend_picasa->access=1;
        else $zend_picasa->access=0;

		foreach ($albumFeed as $key=>$entry) {
            if ($entry instanceof Zend_Gdata_Photos_PhotoEntry) {
				$data=null;
                $data->photoid=$entry->getGphotoId()->getText();
                $data->regdate =$this->makegmtime($entry->published);
                $data->commentcount = $entry->gphotoCommentCount;
                $thumb = $entry->getMediaGroup()->getThumbnail();
                $data->thumb_url = $thumb[2]->url;
                $data->thumbnail = $this->getThumbnail($data->thumb_url, $album,$data->photoid,100);
                $data->title = $entry->getTitle();
				$data->link_url = $albumFeed->getLink('alternate')->getHref();
                $output[]=$data;
            }
			$zend_picasa->entry =$output;
        }
		return $zend_picasa;
	}

	function getPhotoFeed($photoFeed,$album,$photoid) {
		$url =$photoFeed->getMediaGroup()->content;
        $args->url=$url[0]->url;
        $args->title=$photoFeed->getTitle();

        $regtime = $photoFeed->getGphotoTimestamp();
        $args->regdate = gmstrftime("%Y%m%d%H%I%S",substr($regtime,0,-3));
        $args->summary=trim($photoFeed->getMediaGroup()->getDescription());
        $thumbs = $photoFeed->getMediaGroup()->getThumbnail();
        $args->thumburl = $thumbs[2]->url;
        $args->thumburl=$thumbs[2]->url;
		$args->link_url =$photoFeed->getLink('alternate')->getHref(); 
		$access = $photoFeed->extensionElements[0];
		$viewcount = $photoFeed->extensionElements[3];
		$exif = $photoFeed->extensionElements[4];
		$where = $photoFeed->extensionElements[5];
		$args->access=$access->text;
		$args->viewcount = $viewcount->text;
		$args->where = $where->text;
        foreach ($photoFeed as $entry) {
			$data=null;
            if ($entry instanceof Zend_Gdata_Photos_CommentEntry) {
                $data->content =  $entry->getContent();
                $data->gphotoid =$entry->getGphotoId();
                $data->regdate = $this->makegmtime($entry->getPublished());
                $zend_picasa[]=$data;
                unset($data);
            }
        }
		$args->zend_picasa=$zend_picasa;

        foreach ($photoFeed as $entry) {
			$data=null;
            if ($entry instanceof Zend_Gdata_Photos_TagEntry) {
                $data->title = $entry->getTitle();
                $data->content =  $entry->getContent();
                $tag[]=$data;
				$args->tags.=$data->title;
            }
        }
		$args->tag = $tag;
		return $args;
	}

	function getAlbumList() {
        $client = $this->getClient();

//        $photos = new Zend_Gdata_Photos($client);
		try {
            $photos = new Zend_Gdata_Photos($client);
        } catch (Zend_Gdata_App_HttpException $e) {
            return $this->dispZend_picasaMessage('msg_module_not_configured');
        } catch (Zend_Gdata_App_Exception $e) {
            return $this->dispZend_picasaMessage('msg_module_not_configured');
        }

        $query = new Zend_Gdata_Photos_UserQuery();
        $query->setUser('default');

        $userFeed = $photos->getUserFeed(null, $query);
		foreach ($userFeed as $entry) {
                if ($entry instanceof Zend_Gdata_Photos_AlbumEntry) {
					$access = $entry->getGphotoAccess();
					$access = $entry->getGphotoAccess();
                    if($access=='public') $data->access="공개";
                    else $data->access="비공개";
//					if($access=='public') $data->access=true;
//					else $data->access=false;
					$data->album = $entry->getGphotoId()->getText();
                    $data->albumtitle = $entry->getTitle()->getText();
					$tempdata[]=$data;
                    unset($data);
                }
        }
        return $tempdata;
    }

	function makegmtime($string,$format="YmdHis") {
		$temp = explode("T",$string);
		$date=explode("-",$temp[0]);
		$time = explode(":",$temp[1]);

        $datetime = gmmktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
        return date($format,$datetime);
    }

	function dispZend_picasaMessage($msg_code) {
        $msg = Context::getLang($msg_code);
        if(!$msg) $msg = $msg_code;
        Context::set('message', $msg);
        $this->setTemplateFile('message');
    }

	function delOldFiles($path,$duration=1) {
		$ltime = mktime(0,0,0,date(m),date(j)-1,date(Y));
		foreach (glob($path) as $file) { //configure path
		    $filetime=filectime($file);
			if($filetime <$ltime) unlink($file);
		} 
	}

	function getMega($value) {
		if($value==0) return 0;
		else return number_format($value/(1024*1024),1);
	}

}
