<?php 
/*BY Jose Gregorio Ramos*/
class MQTTConection {
	private $_mqtt=NULL;
	private $_topic=NULL;
	

	function __construct($topic){
		$this->_mqtt= new SAMConnection();
		$this->_mqtt->connect(SAM_MQTT, array(SAM_HOST => XSAM_HOST,
                               SAM_PORT => XSAM_PORT));  
		$this->_topic=$topic;
	}
	
	public function send($str){
		$msgCpu = new SAMMessage($str);
		$data=$this->_mqtt->send('topic://'.$this->_topic, $msgCpu);
		$this->_mqtt->disconnect();  
	}
	public function send2($topic,$str){
		$msgCpu = new SAMMessage($str);
		$data=$this->_mqtt->send('topic://'.$topic, $msgCpu);
		//$this->_mqtt->disconnect();  
	}
	
	public function read($topic){
	  
	   $conn=$this->_mqtt;
	  
	   $subUp = $conn->subscribe('topic://'.$topic) OR die('could not subscribe');
 
	
		while($conn)
		{
			   //receive latest message on topic $subUp
			   $msgUp = $conn->receive($subUp);
		 
			   //if there is a message
			  if (!$msgUp->body=="")
			  {
				  //echo message to terminal
				  echo $msgUp->body;
				  $data= json_decode($msgUp->body);
				  
				  if (isset($data->deviceid)){
				//	if ($data->deviceid=="3"){
						print_r($data->data);	
			//		}
						  
				  }
			  }
		 
			  //wait 1s
			  sleep(1);
		}
	
	}
	

}



?>
