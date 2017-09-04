<?php 
	class ServerUpload {		
		var $uniqueName;
		var $target_dir;
		var $uploadedPath;
		function boot(){						
			$this->handleUpload();			
		}
		function handleUpload(){
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			    echo "File upload initiated..." . "<br>";
			    $upload_dir   = wp_upload_dir();
			    $this->target_dir = $upload_dir['basedir']."/";
				$target_file = $this->target_dir . basename($_FILES["file"]["name"]);
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			    if($imageFileType != "mp3") {
				    echo "\nonly mp3 supported for now"  . "<br>";
				    $uploadOk = 0;
				}
				if ($_FILES["file"]["size"] > 50000000) {
				    echo "\nyour file is too large.";
				    $uploadOk = 0;
				}
				if ($uploadOk == 0) {
				    echo "\nyour file was not uploaded."  . "<br>";				
				} else {
					$fileName = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
					$this->uniqueName = $fileName.md5(uniqid(rand(), true)).".".$imageFileType;
					$target_file = $this->target_dir . $this->uniqueName;
					$this->uploadedPath = $target_file;
				    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
				        echo "\nThe file ". basename( $_FILES["file"]["name"]). " has been uploaded."   . "to ".$target_file . " " . "<br />";
				        echo "\nEncrytion initiated..."  . "<br />";				
				        $this->aiEncrypt($this->uniqueName);
				    } else {
				        echo "\nthere was an error uploading your file.";
				    }
				}

			}
		}
		function aiEncrypt($uniqueName){
			$ffmpeg = "/var/www/html/unrt/wp-content/plugins/AIEnCrypt/ffmpeg_sources/ffmpeg/ffmpeg -i /var/www/html/unrt/wp-content/uploads/water.mp3 -i $this->uploadedPath  -filter_complex amix=inputs=2:duration=first:dropout_transition=0 -codec:a libmp3lame -q:a 0 /var/www/html/unrt/wp-content/uploads/prev-$uniqueName 2>&1";				
			exec($ffmpeg, $output, $return);					
			if($return != 0){
				echo "Error while encryption. Please contact server admin.";				
			} else {
				echo "Encrytion done.<br />";
				echo "S3 upload initiaded...<br />";	
				$s3 = new S3Upload();	
				$resultMain = $s3->upload($this->uniqueName,$this->uploadedPath);		
				$resultPrev = $s3->upload("prev-".$uniqueName,"/var/www/html/unrt/wp-content/uploads/prev-$uniqueName");	
				echo "S3 upload complete <br />";				
				echo "The original file is : ".$resultMain."<br />";
				echo "The preview file is : ".$resultPrev."<br />";

			}
			/*echo "<pre>";
			var_dump($output);
			echo "</pre>";*/

		}
	}
?>
