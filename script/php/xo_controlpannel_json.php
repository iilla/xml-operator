<?php
	$act_dir = $_POST['directory'];
	($_POST['specs']== "navigation")? $json['fromNavigation'] = true : $json['fromNavigation'] = false;
	$json['errors'] = false;
	
	if (is_dir($act_dir) && preg_match("((\/$)|(\\$))", $act_dir)) {
		if ($act_dir!="/" && !preg_match("/[a-z]\:\//",$act_dir)) {
			$parent_dir = explode("/",$act_dir);
			array_pop($parent_dir);array_pop($parent_dir);
			$parent_dir = implode("/",$parent_dir);
			$parent_dir .= "/";
		} else {
			$parent_dir = $act_dir;
		}
							
		if ($dh = opendir($act_dir)) {
			while ($file = readdir($dh)) {
				if (($file != ".") && ($file != "..")) {
					if (filetype($act_dir.$file) == "dir") {
						$directory_directories_content[] = "<a id='".$act_dir.$file."/' class='reflink' href='".$act_dir.$file."/'>".$file."</a>";
					} else {
						$directory_files_content[] = $file;
					}
				}
			}
			
			$json['filelist'] = "."."<br />";
			$json['filelist'] .= "<a id='".$parent_dir."' class='parentLink' href='".$parent_dir."'>..</a><br />";		
			
			$fill_list = false;
			if (!empty($directory_files_content) && !empty($directory_directories_content)) {
				asort($directory_files_content);
				asort($directory_directories_content);
				$directory_content = array_merge($directory_directories_content,$directory_files_content);	
				$fill_list = true;			
			} elseif (empty($directory_files_content) && !empty($directory_directories_content)) {
				asort($directory_directories_content);
				$directory_content = $directory_directories_content;
				$fill_list = true;
			} elseif (empty($directory_directories_content) && !empty($directory_files_content)) {
				asort($directory_files_content);
				$directory_content = $directory_files_content;
				$fill_list = true;
			}

			if($fill_list) {
				foreach ($directory_content as $file) {
					if (preg_match("/(.xml)$/",$file)) {
						$json['filelist'] .= "<span class='xmlfile'>".$file."</span><br />";
					} else {
						$json['filelist'] .= "<span class='file'>".$file."</span><br />";
					}
				}
			}
			closedir($dh);
		}
	} else {
		$json['filelist'] = "directorio incorrecto.";
		$json['errors'] = true;
	}
	
	echo json_encode($json);
?>