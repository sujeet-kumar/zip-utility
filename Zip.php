<?php
/**
 * Zip utility class -by Sujeet <sujeetkv90@gmail.com>
 * https://github.com/sujeet-kumar/zip-utility
 */

class Zip
{
	const ZIP_EXT = 'zip';
	const DIR_SEP = '/';
	
	public static function compress($source_path, $dest_path = '', $excludes = array()){
		if(is_array($source_path)){
			!empty($dest_path) or $dest_path = './compressed_' . time() . '.' . self::ZIP_EXT;
			
			$zip = new ZipArchive();
			if($zip->open($dest_path, ZipArchive::CREATE | ZipArchive::OVERWRITE)){
				foreach($source_path as $path){
					
					if(is_file($path)) $zip->addFile($path, basename($path));
					
				}
				$zip->close();
				return true;
			}else{
				return false;
			}
		}elseif($source_path = realpath($source_path) and $path_info = pathinfo($source_path)){
			$dir_path = $path_info['dirname'];
			$base_name = $path_info['basename'];
			
			!empty($dest_path) or $dest_path = './' . $path_info['basename'] . '.' . self::ZIP_EXT;
			
			$zip = new ZipArchive();
			if($zip->open($dest_path, ZipArchive::CREATE | ZipArchive::OVERWRITE)){
				if(is_file($source_path)){
					
					$zip->addFile($source_path, $base_name);
					
				}elseif(is_dir($source_path)){
					
					$zip->addEmptyDir($base_name);
					self::_processZip(rtrim($source_path, '/\\'), $zip, strlen("$dir_path/"), $excludes);
					
				}
				$zip->close();
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function extract($source_path, $dest_path = ''){
		$zip = new ZipArchive();
		if($source_path = realpath($source_path) and $zip->open($source_path)){
			!empty($dest_path) or $dest_path = './' . pathinfo($source_path, PATHINFO_FILENAME);
			$ex = $zip->extractTo($dest_path);
			$zip->close();
			return $ex;
		}else{
			return false;
		}
	}
	
	public static function getList($source_path, $html = false){
		if($html){
			return self::_htmlList(self::getTree($source_path));
		}else{
			$path_list = array();
			$zip = new ZipArchive();
			if($zip->open($source_path) === true){
				for($i = 0; $i < $zip->numFiles; $i++){
					if($stat = $zip->statIndex($i)){
						$path_list[] = $stat['name'];
					}
				}
				$zip->close();
			}
			return $path_list;
		}
	}
	
	public static function getTree($source_path, $include_dir_path = false){
		$path_list = self::getList($source_path);
		$returnArr = array();
		foreach($path_list as $path){
			$parts = preg_split('|'.self::DIR_SEP.'|', $path, -1, PREG_SPLIT_NO_EMPTY);
			$leaf_part = array_pop($parts);
			
			$parentArr = &$returnArr;
			foreach($parts as $part){
				if(! isset($parentArr[$part])){
					$parentArr[$part] = array();
				}elseif(! is_array($parentArr[$part])){
					$parentArr[$part] = ($include_dir_path) ? array('__dir' => $parentArr[$part]) : array();
				}
				$parentArr = &$parentArr[$part];
			}
			
			if(empty($parentArr[$leaf_part])){
				$parentArr[$leaf_part] = $path;
			}elseif($include_dir_path && is_array($parentArr[$leaf_part])){
				$parentArr[$leaf_part]['__dir'] = $path;
			}
		}
		return $returnArr;
	}
	
	private static function _processZip($path, &$zip, $path_pos, $excludes){
		$handle = opendir($path);
		while(false !== $f = readdir($handle)){
			if($f == '.' or $f == '..' or (is_array($excludes) and in_array($f, $excludes))) continue;
			
			$file_path = "$path/$f";
			$local_path = substr($file_path, $path_pos);
			
			if(is_file($file_path)){
				
				$zip->addFile($file_path, $local_path);
				
			}elseif(is_dir($file_path)){
				
				$zip->addEmptyDir($local_path);
				self::_processZip($file_path, $zip, $path_pos, $excludes);
				
			}
		}
		closedir($handle);
	}
	
	private static function _htmlList($path_tree){
		$html_list = '';
		if(! empty($path_tree)){
			$html_list .= '<ul class="zip-list">';
			foreach($path_tree as $name => $path){
				$html_list .= '<li class="'. (is_array($path) ? 'directory' : 'file') .'">';
				if(is_array($path)){$html_list .= '<span class="directory-name">'. $name .'</span>'. self::_htmlList($path);}
				else{$html_list .= '<span class="file-name">'. $name .'</span>';}
				$html_list .= '</li>';
			}
			$html_list .= '</ul>';
		}
		return $html_list;
	}
}

/* End of file Zip.php */