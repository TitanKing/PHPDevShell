<?php

function sdir( $path='.', $mask='*', $nocache=0 ){
    static $dir = array(); // cache result in memory
    if ( !isset($dir[$path]) || $nocache) {
        $dir[$path] = scandir($path, 1);
    }
    foreach ($dir[$path] as $i=>$entry) {
        if ($entry!='.' && $entry!='..' && fnmatch($mask, $entry) ) {
            $sdir[] = $entry;
        }
    }
    return ($sdir);
}

/**
 * File Log - Directory
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_fileLogOptions extends PHPDS_query
{

	public function invoke($parameters = null)
	{
		$logdir = $this->configuration['error']['file_log_dir'];

		$logfiles = sdir($logdir, '*.log');

		if (! empty($this->security->request['logfile'])) {
			$selected = $this->security->request['logfile'];
		} else {
			$selected = '';
		}

		if (! empty($logfiles)) {
			foreach($logfiles as $logfiles_) {
				if ($selected == $logfiles_)
					$logfiles__[] = array('file'=>$logfiles_, 'selected'=>'selected');
				else
					$logfiles__[] = array('file'=>$logfiles_, 'selected'=>'');
			}
			return $logfiles__;
		} else {
			$logfiles = array();
			return $logfiles;
		}
	}
}

/**
 * File Logs - Get All File Logs
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class PHPDS_getAllFileLogsQuery extends PHPDS_query
{

	public function invoke($parameters = null)
	{
		list($files) = $parameters;
		$logdir = $this->configuration['error']['file_log_dir'];

		if (! empty($files)) {
			if (! empty($this->security->request['logfile'])) {
				$readlogfile = $logdir . $this->security->request['logfile'];
			} else if (! empty($files[0]['file'])) {
				$readlogfile = $logdir . $files[0]['file'];
			} else {
				$this->template->warning(('Error opening log file'));
				$RESULTS = array();
				return $RESULTS;
			}

			if (file_exists($readlogfile)) {
				$fileopen = fopen($readlogfile ,'r');
				$filedata = fread($fileopen, filesize($readlogfile));
				fclose($fileopen);
			} else {
				$this->template->warning(sprintf(_('Cannot read file %s'), $readlogfile));
			}

			if (! empty($filedata)) {
				$datainarray = explode("----", $filedata);
			}

			$view = $this->template->icon('eye', _('View detailed HTML log'));

			// loop and clean
			if (! empty($datainarray)) {
				foreach ($datainarray as $row) {
					if (! empty($row)) {
						$row_ = explode("|", trim($row));
						list($detailed_log_path, $detailed_log_url, $error, $date, $name) = $row_;
						list($type, $message) = explode(":",$error, 2);
						$detailed_log_url = '<a href="' . $detailed_log_url . '" target="_blank" class="button">' . $view . '</a>';

						$RESULTS[] = array('detailed_log_path'=>$detailed_log_path, 'detailed_log_url'=>$detailed_log_url, 'type'=>$type, 'message'=>$message, 'date'=>$date, 'name'=>$name);
					}
				}
			}
		}

		if (! empty($RESULTS)) {
			$RESULTS[] = array('type'=>'<strong>EOF;</strong>', 'message'=>'', 'date'=>'', 'name'=>'', 'detailed_log_path'=>'', 'detailed_log_url'=>'');
			return $RESULTS;
		} else {
			$RESULTS = array();
			return $RESULTS;
		}
	}
}
