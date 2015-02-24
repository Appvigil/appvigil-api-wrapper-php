<?php
	
	/* Finally, A light, permissions-checking logging class. 
	 * 
	 * Author	: Kenneth Katzgrau < katzgrau@gmail.com >
	 * Date	: July 26, 2008
	 * Comments	: Originally written for use with wpSearch
	 * Website	: http://codefury.net
	 * Version	: 1.0
	 *
	 * Usage: 
	 *		$log = new KLogger ( "log.txt" , KLogger::INFO );
	 *		$log->LogInfo("Returned a million search results");	//Prints to the log file
	 *		$log->LogFATAL("Oh dear.");				//Prints to the log file
	 *		$log->LogDebug("x = 5");					//Prints nothing due to priority setting
	*/
	
	class KLogger
	{
		
		const DEBUG 	= 1;	// Most Verbose
		const INFO 		= 2;	// ...
		const WARN 		= 3;	// ...
		const ERROR 	= 4;	// ...
		const FATAL 	= 5;	// Least Verbose
		const OFF 		= 6;	// Nothing at all.
		
		const LOG_OPEN 		= 1;
		const OPEN_FAILED 	= 2;
		const LOG_CLOSED 	= 3;
		
		/* Public members: Not so much of an example of encapsulation, but that's okay. */
		public $Log_Status 	= KLogger::LOG_CLOSED;
		public $DateFormat	= "d M Y g:i:s A e";
		public $MessageQueue;
	
		private $log_file;
		private $priority = KLogger::DEBUG;
		
		public $file_handle;
		
		public function __construct($priority )
		{
			if ( $priority == KLogger::OFF ) return;
			
			//$this->log_file = $filepath;
			$this->MessageQueue = array();
			$this->priority = $priority;
		}
		
		public function __destruct()
		{

		}
		
		public function LogInfo($line)
		{
			$this->Log( $line , KLogger::INFO);
		}
		
		public function LogDebug($line)
		{
			//echo $this->file_handle;
			$this->Log( $line , KLogger::DEBUG);
		}
		
		public function LogWarn($line)
		{
			$this->Log( $line , KLogger::WARN);	
		}
		
		public function LogError($line)
		{
			$this->Log( $line , KLogger::ERROR);		
		}

		public function LogFatal($line)
		{
			$this->Log( $line , KLogger::FATAL );
		}
		
		public function Log($line, $priority)
		{
			if ( $this->priority <= $priority )
			{
				$status = $this->getTimeLine( $priority);
				//echo $this->file_handle;
				$this->WriteFreeFormLine ( "$status $line \n" );
			}
		}
		
		public function WriteFreeFormLine( $line )
		{
			if ($this->priority != KLogger::OFF )
			{
				echo $line;
			}
		}
		
		private function getTimeLine( $level ,$fromFile=null,$fromLine=null)
		{
			$time = date( $this->DateFormat );

			/*			switch( $level )
			{
				case KLogger::INFO:
					return "[ $time ] - INFO  --> ";//return "[ $time ] - INFO  --> [$logged_in_user_email in $fromFile:$fromLine]";
				case KLogger::WARN:
					return "[ $time ] - WARN  --> ";				
				case KLogger::DEBUG:
					return "[ $time ] - DEBUG --> ";				
				case KLogger::ERROR:
					return "[ $time ] - ERROR --> ";
				case KLogger::FATAL:
					return "[ $time ] - FATAL --> ";
				default:
					return "[ $time ] - LOG   --> ";
			}
			*/
			
			switch( $level )
			{
				case KLogger::INFO:
					return "[INFO] ";//return "[ $time ] - INFO  --> [$logged_in_user_email in $fromFile:$fromLine]";
				case KLogger::WARN:
					return "[WARN] ";				
				case KLogger::DEBUG:
					return "[DEBUG] ";				
				case KLogger::ERROR:
					return "[ $time ] - ERROR --> ";
				case KLogger::FATAL:
					return "[ $time ] - FATAL --> ";
				default:
					return "[ $time ] - LOG   --> ";
			}
		}
		
		public function logClose()
		{
		}
		
	}


?>