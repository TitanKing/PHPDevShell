<?php

/**
 * Controller Class: Upload example.
 * Like always we start our node with the controller.
 * @author Jason Schoeman
 * @return string
 */
class uploadExample extends PHPDS_controller
{

	/**
	 * Execute Controller
	 * @author Jason Schoeman
	 */
	public function execute()
	{
		// Heading.
		$this->template->heading(_('Upload Example.'));
		$this->template->info(_('Shows how you can upload and list files.'));

		// Call filemanager.
		$filemanager = $this->factory('fileManager');

		// Deleting a file.
		if (! empty($this->security->get['df'])) {
			$deleted = $filemanager->deleteFiles($this->security->get['df']);
			if ($deleted) {
				// Note these messages gets logged automatically, you also have $this->template->ok, $this->template->warning, $this->template->notice, $this->template->critical, $this->template->message, $this->template->note.
				$this->template->ok(sprintf('Your file (%s) was deleted.', $deleted[$this->security->get['df']]['original_filename']));
			}
		}

		// Set some parameters, have a look at the API for more.
		$filemanager->allowedExt = 'default'; // Will load extentions as set in General Settings GUI
		$filemanager->alias = 'samplefiles'; // Group by this alias
		$filemanager->subId = '1'; // Can be set to an id accommodating a forms database id for instance so one could easily locate it when loading data.
		$filemanager->convertPdf = false; // This will convert PDF files to images, smart, I know.

		// Some exeption handling example.
		try {
			// Upload required files.
			if ($filename = $filemanager->autoUpload('file1')) {
				$this->template->ok(sprintf(_('File field 1 %s was uploaded'), $filename));
			}

			// Upload required files.
			if ($filename2 = $filemanager->autoUpload('file2')) {
				$this->template->ok(sprintf(_('File field 2 %s was uploaded'), $filename2));
			}

			// Upload required files.
			if ($filename3 = $filemanager->autoUpload('file3')) {
				$this->template->ok(sprintf(_('File field 3 %s was uploaded'), $filename3));
			}

		} catch (Exception $e) {
			// To make messages translatable, simply surround them with __('My message');
			$this->template->warning(sprintf(__('Oops, we had a slight problem uploading the file, %s in file %s on line %s'), $e->getMessage(), $e->getFile(), $e->getLine()));
		}

		// Lets load the files uploaded so far!
		// Use print_r to see all the data the filemanager returns :)
		$uploaded_files_array = $filemanager->loadFiles(false, 'samplefiles');

		$u_arr = array();
		$page_delete = $this->navigation->buildURL(false, 'df=');
		// Loop and assign files.
		if (! empty($uploaded_files_array)) {
			// Loop results of uploaded files.
			foreach ($uploaded_files_array as $files) {
				$u_arr[] = array(
					'download_file' => $files['download_file'],
					'file_id' => $files['file_id'],
					'thumbnail' => $files['thumbnail'],
					'resized' => $files['thumbnail'],
					'extention_img' => $files['extention_img'],
					'format_file_size' => $files['format_file_size'],
					'original_filename' => $files['original_filename'],
					'delete_file' => "<a href=\"{$page_delete}{$files['file_id']}\" {$this->core->confirmLink(sprintf(_('Are you sure you want to DELETE : %s'), $files['original_filename']))} class=\"button\">{$this->template->icon('broom--minus', _('Delete File'))}</a>"
				);
			}
		}

		// Call views plugin.
		$view = $this->factory('views');

		// Assign Variables.
		$view->set('self_url', $this->navigation->selfUrl());
		$view->set('u_arr', $u_arr);
		$view->set('developers_name', $this->configuration['user_display_name']);
		$view->set('absolute_url', $this->configuration['absolute_url'] . '/');

		// Show it.
		$view->show();
	}
}

return 'uploadExample';