<?php

class Platforms {
	
	const iPhone = 0;
	const iPad = 1;
	const iOSUniversal = 2;
	const AndroidPhone = 3;
	const AndroidTablet = 4;
	const AndroidUniversal = 5;
	const Win8Phone = 6;
	const Win8Tablet = 7;
	const AppStoreLink = 9;
	const WebClip = 9;
	
	public static function count($data) {
		$arr = array();
		foreach ($data as $item) {
			if (!isset($item['platform'])) $item = $item['Application'];
			$icon = self::iconForPlatform($item['platform']);
			if (!isset($arr[$icon])) $arr[$icon] = self::nameForIconKey($icon);
		}
		return $arr;
	}
	
	public static function iOS($platform) {
		return ($platform <= self::iOSUniversal);
	}
	
	public static function Android($platform) {
		return ($platform == self::AndroidPhone || $platform == self::AndroidTablet || $platform == self::AndroidUniversal);
	}
	
	public static function Win8($platform) {
		return ($platform == self::Win8Phone || $platform == self::Win8Tablet);
	}
	
	public static function platformToString($platform) {
		switch($platform) {
			case 0:
				return __('iPhone or iPod');
				break;
			case 1:
				return __('iPad');
				break;
			case 2:
				return __('iOS Universal App');
				break;
			case 3:
				return __('Android');
				break;
			case 4:
				return __('Android Tablet');
				break;
			case 5:
				return __('Android All');
				break;
			case 6:
				return __('Windows 8 Phone');
				break;
			case 7:
				return __('Windoes 8 Table or Desktop');
				break;
			case 8:
				return __('iTunes or Google Play link');
				break;
			case 9:
				return __('Web Clip');
				break;
		}
	}
	
	public static function extensionForPlatform($platform) {
		if ($platform <= 2) {
			return 'ipa';
		}
		elseif ($platform > 2 || $platform <= 5) {
			return 'apk';
		}
		elseif ($platform == 6 || $platform == 7) {
			return 'appx';
		}
		else {
			return null;
		}
	}
	
	public static function iconForPlatform($platform) {
		if ($platform <= 2) {
			return 'apple';
		}
		elseif ($platform > 2 && $platform <= 5) {
			return 'android';
		}
		elseif ($platform == 6 || $platform == 7) {
			return 'windows';
		}
		else {
			return 'globe';
		}
	}
	
	public static function nameForIconKey($iconKey) {
		switch ($iconKey) {
			case 'apple':
				return 'Apple';
				break;
			case 'android':
				return 'Android';
				break;
			case 'windows':
				return 'Windows 8';
				break;
			case 'globe':
				return 'Web';
				break;
			default:
				return 'Other';
				break;
		}
	}
	
}