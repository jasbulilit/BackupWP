<?php
/**
 * Backup WP files and MySQL data
 *
 * @author Jasmine
 */
class BackupWP {
	private $_wp_install_dir	= null;
	private $_exclude_path		= [];
	private $_wp_database		= null;
	private $_log_fileath		= 'backup.log';
	private $_archive_cmd		= 'tar cfz';
	private $_dump_cmd			= 'mysqldump';
	private $_db_conf_path		= 'db.ini';
	private $_dump_options		= [
		'--skip-extended-insert'
	];

	public function __construct($wp_install_dir, $wp_database) {
		$this->_wp_install_dir	= $wp_install_dir;
		$this->_wp_database		= $wp_database;
	}

	public function createArchive($archive_path) {
		$this->_log('Start create achive: ' . $archive_path);

		try {
			$cmd = sprintf(
				"%s %s %s %s",
				$this->_archive_cmd,
				$archive_path,
				$this->_getExcludeStr(),
				$this->_wp_install_dir
			);
			$this->_execCmd($cmd);
		} catch (Exception $e) {
			$this->_log($e->getMessage());
			throw $e;
		}

		$this->_log('End of create achive.');
	}

	public function dumpDatabase($dump_save_path) {
		$this->_log('Start dump database: ' . $dump_save_path);

		try {
			$cmd = sprintf(
				"%s --defaults-file=%s %s %s > %s",
				$this->_dump_cmd,
				$this->_db_conf_path,
				implode(' ', $this->_dump_options),
				$this->_wp_database,
				$dump_save_path
			);
			$this->_execCmd($cmd);
		} catch (Exception $e) {
			$this->_log($e->getMessage());
			throw $e;
		}

		$this->_log('End of dump database.');
		return $this;
	}

	public function setLogFilepath($log_filepath) {
		$this->_log_fileath = $log_filepath;
	}

	public function setArchiveCmd($archive_cmd) {
		$this->_archive_cmd = $archive_cmd;
	}

	public function setDumpCmd($dump_cmd) {
		$this->_dump_cmd = $dump_cmd;
	}

	public function setDbConfPath($db_conf_path) {
		$this->_db_conf_path = $db_conf_path;
	}

	public function addExcludePath($exclude_path) {
		$this->_exclude_path[] = $exclude_path;
	}

	public function clearExcludePath() {
		$this->_exclude_path = [];
	}

	public function addDumpOption($dump_option) {
		$this->_dump_options[] = $dump_option;
	}

	public function clearDumpOption() {
		$this->_dump_options = [];
	}

	private function _getExcludeStr() {
		$excludes = [];
		foreach ($this->_exclude_path as $path) {
			$excludes[] = '--exclude ' . $path;
		}
		return implode(' ', $excludes);
	}

	private function _execCmd($cmd) {
		exec($cmd, $output, $ret);
		if (! empty($output)) {
			$this->_log(print_r($output, true));
		}
		if ($ret !== 0) {
			throw new ErrorException('Failed to exec command: ' . $cmd);
		}
	}

	private function _log($message) {
		error_log(
			sprintf("[%s] %s\n", date('Y/m/d H:i:s'), $message),
			3,
			$this->_log_fileath
		);
	}
}
