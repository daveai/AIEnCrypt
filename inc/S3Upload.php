<?php
use Aws\S3\S3Client;
use Aws\Common\Credentials\Credentials;
class S3Upload {
	var $credentials;
	var $client;
	var $bucket;
	public function __construct(){
		$this->credentials = new Credentials('AKIAJU657FYJXACVBIVA', '2QyzqNMZJ19OxvbnMO/uCVDl7NLZNPoCTMf4nc59');
		$this->client = S3Client::factory(array(
		    'credentials' => $this->credentials
		));
		$this->bucket = 'unrtmusics';		
	} 

	function upload($key, $path){
		$result = $this->client->putObject(array(
		    'Bucket'     => $this->bucket,
		    'Key'        => $key,
		    'SourceFile' => $path,
		    'ACL'    => 'public-read'		    
		));
		$this->client->waitUntil('ObjectExists', array(
		    'Bucket' => $this->bucket,
		    'Key'    => $key
		));
		return $this->client->getObjectUrl($this->bucket, $key);
	}
}