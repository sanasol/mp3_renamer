<?php
	require_once('./getid3/getid3.php');
	$getID3 = new getID3;
	
	function directoryToArray($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '') {
        $arrayItems = array();
        $skipByExclude = false;
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
				preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
				if($exclude){
					preg_match($exclude, $file, $skipByExclude);
				}
				if (!$skip && !$skipByExclude) {
					if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
						if($recursive) {
							$arrayItems = array_merge($arrayItems, directoryToArray($directory. DIRECTORY_SEPARATOR . $file, $recursive, $listDirs, $listFiles, $exclude));
						}
						if($listDirs){
							$file = $directory . DIRECTORY_SEPARATOR . $file;
							$arrayItems[] = $file;
						}
						} else {
						if($listFiles){
							$file = $directory . DIRECTORY_SEPARATOR . $file;
							$arrayItems[] = $file;
						}
					}
				}
			}
			closedir($handle);
		}
        return $arrayItems;
	}
	//echo "<pre>";
	//print_r(directoryToArray("lib"));
	$i=0;
	foreach(directoryToArray("lib") as $file)
	{
		$array = explode("\\", $file);
		$name = end($array);
		$extt = explode(".", $name);
		$ext = end($extt);
		
		if($ext == "mp3")
		{
			$i++;
			$info = $getID3->analyze($file);
			if($info['tags']['id3v1']['artist'][0])
			{
				rename($file, implode("\\",array_slice($array, 0, -1))."\\".$info['tags']['id3v1']['artist'][0]." - ".$info['tags']['id3v1']['title'][0].".mp3");
				echo "<p>$i. Renamed ".$file." - ".implode("\\",array_slice($array, 0, -1))."\\".$info['tags']['id3v1']['artist'][0]." - ".$info['tags']['id3v1']['title'][0].".mp3</p>";
			}
		}
		
	}
?>