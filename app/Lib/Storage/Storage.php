<?php

/*

For S3 usage please refer to:
	https://github.com/tpyo/amazon-s3-php-class
	
*/

App::uses('Settings', 'Model');
App::import('Vendor/S3', 'S3');
App::uses('Me', 'Lib/User');


// TODO: Add ftp support
define('STORAGE_LOCAL', 0);
define('STORAGE_S3', 1);
define('STORAGE_FTP', 2); // Currently not being used

class Storage {

	public static function usedStorage() {
		$s = new Settings();
		$s3Enabled = $s->get('s3Enable');
		return $s3Enabled ? STORAGE_S3 : STORAGE_LOCAL;
	}
	
	private static function folder() {
		return md5(Configure::read('Security.salt').Configure::read('Security.cipherSeed'));
	}
		
	public static function isIconForAppWithId($id, $location) {
		if ($location == STORAGE_LOCAL) {
			$path = APP.'Userfiles'.DS.'Applications'.DS.$id.DS.'icon';
			return file_exists($path);
		}
		else {
			// TODO: Finish
			$path = self::folder().DS.'Applications'.DS.$id.DS.'icon';
			$s = new Settings();
			S3::setAuth($s->get('s3AccessKey'), $s->get('s3SecretKey'));
		}
	}
	
	public static function urlForIconForAppWithId($id, $location) {
		if (!(bool)$id) {
			return $path = Router::url('/').'Userfiles'.DS.'Settings'.DS.'Images'.DS.'Icon';
		}
		if ($location == STORAGE_LOCAL) {
			$path = Router::url('/').'Userfiles'.DS.'Applications'.DS.$id.DS.'icon';
			return $path;
		}
		else {
			$path = self::folder().DS.'Applications'.DS.$id.DS.'icon';
			$s = new Settings();
			return 'http://'.$s->get('s3Bucket').'.s3.amazonaws.com/'.$path;
		}
	}
	
	public static function saveFile($file, $section='General', $protected=true) {
		$ok = false;
		if (Storage::usedStorage() == STORAGE_LOCAL) {
			if ($protected) {
				$path = APP.'Userfiles'.DS.$section.DS;
			}
			else {
				$path = WWW_ROOT.'Userfiles'.DS.$section.DS;
			}
			$dir = new Folder();
			$dir->create($path);
			$path .= pathinfo($file, PATHINFO_BASENAME);
			
			$ok = copy($file, $path);
		}
		else {
			$s = new Settings();
			S3::setAuth($s->get('s3AccessKey'), $s->get('s3SecretKey'));
			$ok = @S3::putObject(S3::inputFile($file, false), $s->get('s3Bucket'), self::folder().DS.$section.DS.pathinfo($file, PATHINFO_BASENAME), ($protected ? S3::ACL_PRIVATE : S3::ACL_PUBLIC_READ));
		}
		return $ok;
	}
	
	public static function deleteFile($file, $section='General', $protected=true) {
		// TODO: Finish
		if (Storage::usedStorage() == STORAGE_LOCAL) {
			if ($protected) {
				$path = APP.'Userfiles'.DS.$section.DS.$file;
			}
			else {
				$path = WWW_ROOT.'Userfiles'.DS.$section.DS.$file;
			}
			if (is_dir($path)) {
				$dir = new Folder($path);
				$dir->delete();
			}
			else {
				$file = new File($path);
				$file->delete();
			}
		}
		else {
			S3::setAuth($s->get('s3AccessKey'), $s->get('s3SecretKey'));
			$file = self::folder().DS.$section.DS.$file;
			S3::deleteObject($s->get('s3Bucket'), $file);
		}
	}
	
}
