<?php

/**
 * This class handles file uploads in its simplest form.
 */
class fileManager extends PHPDS_dependant
{
	/**
	 * Should file uploads be logged to the database.
	 * Default will load settings from database.
	 *
	 * @var boolean
	 */
	public $logUploads = 'default';
	/**
	 * The default upload relative path for files.
	 * Default will load settings from database.
	 * Example : myupload
	 *
	 * @var string
	 */
	public $defaultUploadDirectory = 'default';
	/**
	 * Set permission for newly uploaded files.
	 * Default will load settings from database.
	 * Example : 0777
	 *
	 * @var int
	 */
	public $cmod = 'default';
	/**
	 * The maximum allowed file upload size.
	 * Default will load settings from database.
	 * Example : 1000 (1kb)
	 *
	 * @var int
	 */
	public $maxFilesize = 'default';
	/**
	 * The maximum allowed image upload size.
	 * Default will load settings from database.
	 * Example : 1000 (1kb)
	 *
	 * @var int
	 */
	public $maxImagesize = 'default';
	/**
	 * The maximum amount of files allowed to be uploaded.
	 * 0 to disable.
	 * Example : 5
	 *
	 * @var int
	 */
	public $maxFileCount = 0;
	/**
	 * The allowed extentions for uploads.
	 * Default will load settings from database.
	 * Example : jpg,png,gif
	 *
	 * @var string
	 */
	public $allowedExt = 'default';
	/**
	 * Should thumbnails be created on image uploads.
	 * Default will load settings from database.
	 *
	 * @var boolean
	 */
	public $doCreateThumb = 'default';
	/**
	 * Image quality of converted images.
	 * Default will load settings from database.
	 * Example : 80
	 *
	 * @var int
	 */
	public $imageQuality = 'default';
	/**
	 * Typical resize type for thumbnails.
	 * Default will load settings from database.
	 * Options : resize | resizepercent | cropfromcenter | crop | adaptive
	 *
	 * @var string
	 */
	public $thumbnailType = 'default';
	/**
	 * Adaptive adjust resizing.
	 * Default will load settings from database.
	 * [Max Width, Max Height] example (resize image to no wider than 250 pixels wide and 250 pixels high thumbnails to be uniformly sized) : 250,250
	 *
	 * @var string
	 */
	public $resizeAdaptiveDimension = 'default';
	/**
	 * Resize by pixels.
	 * Default will load settings from database.
	 * [Max Width, Max Height] example (resize image to no wider than 250 pixels wide and 250 pixels high) : 250,250
	 *
	 * @var string
	 */
	public $resizeThumbDimension = 'default';
	/**
	 * Resize by percentage.
	 * Default will load settings from database.
	 * [Percentage] example (reduce the image by 50%) : 50
	 *
	 * @var string
	 */
	public $resizeThumbPercent = 'default';
	/**
	 * Crop from center.
	 * Default will load settings from database.
	 * [Crop Size] example (create a 100x100 pixel crop from the center of an image) : 100
	 *
	 * @var string
	 */
	public $cropThumbFromcenter = 'default';
	/**
	 * Crop by measure.
	 * Default will load settings from database.
	 * [startX, startY, width, height] example (create a 100x50 pixel crop from the top left corner of an image) : 0,0,100,50
	 *
	 * @var string
	 */
	public $cropThumbDimension = 'default';
	/**
	 * Add thumb reflections.
	 * Default will load settings from database.
	 *
	 * @var boolean
	 */
	public $doThumbReflect = 'default';
	/**
	 * Reflection options.
	 * Default will load settings from database.
	 * Data fields expected are [[Percentage of image], [Reflection percentage], [Transparency of reflection], [Set Border], [Border Color]. Example : 40,40,80,true,#a4a4a4
	 *
	 * @var string
	 */
	public $thumbReflectSettings = 'default';
	/**
	 * This option will shrink a large image to a smaller then original viewable image. This should be larger then a thumbnail in most cases as this is the image the user can see when clicking on a thumbnail.
	 * Default will load settings from database.
	 *
	 * @var boolean
	 */
	public $doCreateResizeImage = 'default';
	/**
	 * [Max Width, Max Height] example (resize image to no wider than 500 pixels wide and 500 pixels high) : 500,500
	 * Default will load settings from database.
	 *
	 * @var string
	 */
	public $resizeImageDimension = 'default';
	/**
	 * An alias to group the images with in a certain application.
	 * Will use active plugin name if left empty.
	 *
	 * @var string
	 */
	public $alias;
	/**
	 * Groups file upload by menu id.
	 * Will use active menu id if left empty.
	 *
	 * @var int
	 */
	public $menuId;
	/**
	 * Groups file uploads to a specific document.
	 * Will use active menu id if left empty.
	 *
	 * @var int
	 */
	public $subId;
	/**
	 * The group a file batch belongs to.
	 * Will use uploaders primary group id if left empty.
	 *
	 * @var int
	 */
	public $groupId;
	/**
	 * Convert pdf to image using convert in Linux.
	 * Please note imagemagick needs to be installed on Linux server.
	 *
	 * @var boolean
	 */
	public $convertPdf = false;
	/**
	 * Choose the density image will be converted to from pdf.
	 *
	 * @var integer
	 */
	public $convertDensity = '300';
	/**
	 * Graphics Engine.
	 * Supports gd, imagick
	 *
	 * @var string
	 */
	public $graphicsEngine = 'gd';
	/**
	 * When converting to pdf with multiple pages, image copies will be stored here.
	 *
	 * @var array
	 */
	public $imageCopies = array();
	/**
	 * Holds a record of recently uploaded files.
	 *
	 * @var array
	 */
	public $uploadHistory = array();
	/**
	 * Text Domain
	 *
	 * @var string
	 */
	public $d = 'core.lang';
	/**
	 * Contains images settings.
	 *
	 * @var array
	 */
	public $setting;

	/**
	 * This method simply renames the file to unix standards.
	 *
	 * @param string $filename
	 * @param string $replace Replace odd characters with what?
	 */
	public function safeFileName($filename, $replace = '_')
	{
		return $this->core->safeName($filename, $replace);
	}

	/**
	 * Get a files extension.
	 *
	 * @param string $filename
	 * @return string
	 */
	public function getFileExtension($filename)
	{
		$filename = (array) explode('.', $filename);
		return end($filename);
	}

	/**
	 * Get filename without extension.
	 *
	 * @param string $filename
	 * @return string
	 */
	public function getFileBase($filename)
	{
		$filename = (array) explode('.', $filename);
		return $filename[0];
	}

	/**
	 * Takes a filename and checks what icon needs to go with it.
	 *
	 * @author Adriaan Schoeman, Jason Schoeman
	 * @param string $extension_lookup
	 */
public function iconType($extension_lookup)
	{
		$template = $this->template;
		if (empty($extension_lookup)) return false;
		switch ($extension_lookup) {
			case 'zip':
				$icon = $template->icon('folder-zipper', $extension_lookup);
				break;
			case 'rar':
				$icon = $template->icon('folder-zipper', $extension_lookup);
				break;
			case 'jar':
				$icon = $template->icon('document-export', $extension_lookup);
				break;
			case 'ace':
				$icon = $template->icon('clipboard-invoice', $extension_lookup);
				break;
			case 'cab':
				$icon = $template->icon('clipboard-list', $extension_lookup);
				break;
			case 'bz2':
				$icon = $template->icon('document-node', $extension_lookup);
				break;
			case 'tar':
				$icon = $template->icon('document-zipper', $extension_lookup);
				break;
			case 'gzip':
				$icon = $template->icon('document-zipper', $extension_lookup);
				break;
			case 'doc':
				$icon = $template->icon('document-word-text', $extension_lookup);
				break;
			case 'docx':
				$icon = $template->icon('document-word-text', $extension_lookup);
				break;
			case 'xls':
				$icon = $template->icon('document-excel', $extension_lookup);
				break;
			case 'xlsx':
				$icon = $template->icon('document-excel', $extension_lookup);
				break;
			case 'eps':
				$icon = $template->icon('folder-medium', $extension_lookup);
				break;
			case 'exe':
				$icon = $template->icon('folder-share', $extension_lookup);
				break;
			case 'fla':
				$icon = $template->icon('folder-open-document', $extension_lookup);
				break;
			case 'gif':
				$icon = $template->icon('folder-open-image', $extension_lookup);
				break;
			case 'html':
				$icon = $template->icon('blog-blue', $extension_lookup);
				break;
			case 'php':
				$icon = $template->icon('layer--pencil', $extension_lookup);
				break;
			case 'jpg':
				$icon = $template->icon('image-reflection', $extension_lookup);
				break;
			case 'png':
				$icon = $template->icon('image-blur', $extension_lookup);
				break;
			case 'pdf':
				$icon = $template->icon('image-select', $extension_lookup);
				break;
			case 'psd':
				$icon = $template->icon('document-photoshop', $extension_lookup);
				break;
			case 'sig':
				$icon = $template->icon('image-vertical', $extension_lookup);
				break;
			case 'mp4':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'flv':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'avi':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'wmv':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'mov':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'swf':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'mpg':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'avi':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'wmp':
				$icon = $template->icon('folder-open-film', $extension_lookup);
				break;
			case 'bin':
				$icon = $template->icon('inbox-table', $extension_lookup);
				break;
			case 'txt':
				$icon = $template->icon('edit', $extension_lookup);
				break;
			case 'aac':
				$icon = $template->icon('speaker', $extension_lookup);
				break;
			case 'mid':
				$icon = $template->icon('speaker', $extension_lookup);
				break;
			case 'mp3':
				$icon = $template->icon('speaker-network', $extension_lookup);
				break;
			case 'mpa':
				$icon = $template->icon('speaker-network', $extension_lookup);
				break;
			case 'wav':
				$icon = $template->icon('media-player-black', $extension_lookup);
				break;
			case 'wma':
				$icon = $template->icon('media-player-black', $extension_lookup);
				break;
			case 'log':
				$icon = $template->icon('documents-stack', $extension_lookup);
				break;
			case 'msg':
				$icon = $template->icon('documents-text', $extension_lookup);
				break;
			case 'max':
				$icon = $template->icon('images-flickr', $extension_lookup);
				break;
			case '3dm':
				$icon = $template->icon('image--pencil', $extension_lookup);
				break;
			case 'bmp':
				$icon = $template->icon('folder', $extension_lookup);
				break;
			case 'ai':
				$icon = $template->icon('document-table', $extension_lookup);
				break;
			case 'app':
				$icon = $template->icon('television--arrow', $extension_lookup);
				break;
			case 'bat':
				$icon = $template->icon('terminal', $extension_lookup);
				break;
			case 'sys':
				$icon = $template->icon('terminal--exclamation', $extension_lookup);
				break;
			case 'css':
				$icon = $template->icon('table-split-column', $extension_lookup);
				break;
			default:
				$icon = $template->icon('application-list', $extension_lookup);
				break;
		}
		return $icon;
	}

	/**
	 * Deletes a filename.
	 *
	 * @param string $filename
	 */
	public function deleteFilename($filename = false)
	{
		if (!empty($filename)) {
			if (file_exists($filename)) {
				if (unlink($filename)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	/**
	 * Creates directory according to month and year.
	 *
	 * @param string $directory Writable directory where new directories will be created and files stored.
	 * @param integer $cmod Example: 0777
	 * @param boolean $custome_dir
	 */
	public function createDateDirectory($directory = '', $cmod = '0777', $custom_dir = false)
	{
		try {
			$year = date('Y');
			$month = date('m');
			if (empty($custom_dir)) {
				// Create directory strings.
				$month_directory = str_ireplace('//', '/', $directory . '/' . $year . '/' . $month . '/');
				$year_directory = str_ireplace('//', '/', $directory . '/' . $year . '/');
			} else {
				// Create directory strings.
				$custom_directory = str_ireplace('//', '/', $directory);
			}
			if (empty($custom_dir)) {
				$thumb_directory = str_ireplace('//', '/', $directory . '/' . $year . '/' . $month . '/thumbs/');
				$resize_directory = str_ireplace('//', '/', $directory . '/' . $year . '/' . $month . '/resize/');

				// Create year directory.
				if (!file_exists($year_directory)) {
					mkdir($year_directory, octdec($cmod), true);
					chmod($year_directory, octdec($cmod));
					touch($year_directory . 'index.html');
				}

				// Create month directory.
				if (!file_exists($month_directory)) {
					mkdir($month_directory, octdec($cmod), true);
					chmod($month_directory, octdec($cmod));
					touch($month_directory . 'index.html');
				}

				// Check if thumbs folder exists.
				if (!file_exists($thumb_directory)) {
					mkdir($thumb_directory, octdec($cmod), true);
					chmod($thumb_directory, octdec($cmod));
					touch($thumb_directory . 'index.html');
				}
				// Check if thumbs folder exists.
				if (!file_exists($resize_directory)) {
					mkdir($resize_directory, octdec($cmod), true);
					chmod($resize_directory, octdec($cmod));
					touch($resize_directory . 'index.html');
				}
				// Return directory.
				return $month_directory;
			} else {
				// Check if full folder exists.
				if (!file_exists($custom_directory)) {
					mkdir($custom_directory, octdec($cmod), true);
					chmod($custom_directory, octdec($cmod));
					touch($custom_directory . 'index.html');
				}
				// Return directory.
				return $custom_directory;
			}
		} catch (Exception $e) {
			throw new PHPDS_exception($e->getMessage());
		}
	}

	/**
	 * Binary-safe file create, write and return filename path.
	 *
	 * @param string $filename
	 * @param string $data
	 * @param string $directory
	 * @return string
	 */
	public function writeFile ($filename, $data, $directory = false, $safename = true)
	{
		// Create file.
		try {
			$write = fopen($directory . $filename, 'w');
			fwrite($write, $data);
			fclose($write);
			return $directory . $filename;
		} catch (Exception $e) {
			throw new PHPDS_exception($e->getMessage());
		}
	}

	/**
	 * Move file from temp directory to specified directory.
	 *
	 * @param string $uploaded_filename
	 * @param string $directory
	 * @param string $new_filename
	 */
	public function uploadFile($uploaded_filename, $directory, $new_filename)
	{
		if (move_uploaded_file($uploaded_filename, $directory . basename($new_filename))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Creates unique and safe filename.
	 *
	 */
	public function renameUniqueFilename($filename, $extension = false)
	{
		if (empty($filename)) {
			return false;
		} else {
			if ($extension == false) {
				$extension_ = false;
			} else {
				$extension_ = '.' . $extension;
			}
			$random = rand(0, 999999999);
			$uniq = $this->safeFileName($this->getFileBase($filename)) . '_' . uniqid($random) . $extension_;
			return $uniq;
		}
	}

	/**
	 * Simplifies the whole process of uploading files with type=file.
	 *
	 * @param string $input_name The name of the <form type="file" name="???" />
	 * @param string $file_overwrite_id The id of the log that needs to be overwritten.
	 * @param string $file_description a Simple file description for uploaded item.
	 * @param string $file_explained Explaining in text what this uploaded file is about.
	 *
	 * @return string Will return the complete uploaded directory with its filename.
	 */
	public function autoUpload($input_name, $file_overwrite_id = false, $file_description = '', $file_explained = '')
	{
		// Set global variables.
		$template = $this->template;
		$db = $this->db;
		$configuration = $this->configuration;
		$core = $this->core;
		// Start Exception handling.
		try {
			// Get database settings.
			if (empty($this->setting)) {
				$this->setting = $db->getSettings(array('log_uploads', 'default_upload_directory', 'cmod', 'max_filesize', 'max_imagesize', 'allowed_ext', 'do_create_thumb', 'do_create_resize_image', 'image_quality', 'thumbnail_type', 'resize_thumb_dimension', 'resize_image_dimension', 'resize_thumb_percent', 'crop_thumb_fromcenter', 'crop_thumb_dimension', 'do_thumb_reflect', 'thumb_reflect_settings', 'graphics_engine', 'resize_adaptive_dimension'), 'PHPDevShell');
			}
			// Do we have files uploaded?
			if (!empty($_FILES[$input_name]['tmp_name']) && !empty($_FILES[$input_name]['name'])) {
				// Gain filenames.
				$tmp_name = $_FILES[$input_name]['tmp_name'];
				$original_name = $_FILES[$input_name]['name'];
				$uploaded_size = $_FILES[$input_name]['size'];
				$mime_type = $_FILES[$input_name]['type'];
				$extention_type = $this->getFileExtension($original_name);
				// Determine standard file association variables.
				(empty($this->subId)) ? $sub_id = $configuration['m'] : $sub_id = $this->subId;
				(empty($this->menuId)) ? $menu_id = $configuration['m'] : $menu_id = $this->menuId;
				(empty($this->alias)) ? $alias = $core->activePlugin() : $alias = $this->alias;
				(empty($file_description)) ? $file_description = $original_name : '';
				(empty($this->groupId)) ? $group_id = $configuration['user_group'] : $group_id = $this->groupId;
				// Determine correct file upload options.
				// graphics_engine
				$graphics_engine = $this->setting($this->graphicsEngine, $this->setting['graphics_engine'], 'gd');
				if (!defined('DEFAULT_THUMBLIB_IMPLEMENTATION')) define('DEFAULT_THUMBLIB_IMPLEMENTATION', $graphics_engine);
				// logUploads
				$log_uploads = $this->setting($this->logUploads, $this->setting['log_uploads'], true, true);
				// defaultUploadDirectory
				$default_upload_directory = $this->setting($this->defaultUploadDirectory, $this->setting['default_upload_directory'], 'upload');
				// cmod
				$cmod = $this->setting($this->cmod, $this->setting['cmod'], '0777');
				// max_filesize
				$max_filesize = $this->setting($this->maxFilesize, $this->setting['max_filesize'], false);
				// max_imagesize
				$max_imagesize = $this->setting($this->maxImagesize, $this->setting['max_imagesize'], false);
				// allowed_ext
				$allowed_ext = $this->setting($this->allowedExt, $this->setting['allowed_ext'], false);
				// do_create_thumb
				$do_create_thumb = $this->setting($this->doCreateThumb, $this->setting['do_create_thumb'], true, true);
				// do_create_resize_image
				$do_create_resize_image = $this->setting($this->doCreateResizeImage, $this->setting['do_create_resize_image'], true, true);
				// resize_image_dimension
				$resize_image_dimension = $this->setting($this->resizeImageDimension, $this->setting['resize_image_dimension'], '640,480');
				$resize_image_dimension_ = explode(',', $resize_image_dimension);
				// image_quality
				$image_quality = $this->setting($this->imageQuality, $this->setting['image_quality'], 80);
				// Set mime type.
				if (stripos($mime_type, 'image') !== false) {
					$mime_upload = 'image';
					$check_size = $max_imagesize;
				} else {
					$mime_upload = 'file';
					$check_size = $max_filesize;
				}
				// Setup thumbnail settings.
				$set_thumb = array('jpegQuality' => $image_quality);
				//////////////////////////////////////////////////////////////
				// Error checking.
				if (!empty($check_size) && ($uploaded_size > $check_size)) {
					throw new PHPDS_exception(sprintf(__('File %s is larger (%s) then the allowed size %s.', 'FileMan'), $original_name, $this->displayFilesize($uploaded_size), $this->displayFilesize($check_size)));
					$error[0] = true;
				}
				// Check extentions allowed.
				if (!empty($allowed_ext)) {
					// Check if we have the required extention.
					$extention_find = stripos($allowed_ext, $extention_type);
					// Check if extention is allowed.
					if ($extention_find === false) {
						throw new PHPDS_exception(sprintf(__('Extention type %s not allowed, allowed extention/s : %s', 'FileMan'), $extention_type, $allowed_ext));
						$error[1] = true;
					}
				}
				// Check if file count exceeds maximum.
				if (!empty($this->maxFileCount)) {
					// Lets count the files.
					if ($this->countFiles($alias, $sub_id, $menu_id) >= $this->maxFileCount) {
						throw new PHPDS_exception(sprintf(__('Too many files uploaded already, maximum allowed file count is : %s', 'FileMan'), $this->maxFileCount));
						$error[2] = true;
					}
				}
				//////////////////////////////////////////////////////////////
				// Check file size.
				if (empty($error)) {
					// Create date directory.
					$final_upload_path = $this->createDateDirectory($default_upload_directory, $cmod);
					// Create unique filename.
					$unique_filename = $this->renameUniqueFilename($original_name, $extention_type);
					// Move files to correct folder.
					if ($this->uploadFile($tmp_name, $final_upload_path, $unique_filename)) {
						// Check if we have a pdf and if we need to convert it.
						if ($this->convertPdf == true && $extention_type == 'pdf' && @exec("convert")) {
							$mime_upload = 'image';
							$pdf_path = $final_upload_path . $unique_filename;
							$jpg_file = str_replace(".pdf", ".jpg", "$unique_filename");
							$jpg_path = $final_upload_path . $jpg_file;
							exec("convert -density {$this->convertDensity} -resize $resize_image_dimension_[0] -quality $image_quality $pdf_path $jpg_path");
							$mime_type = 'image/jpg';
							$extention_type = 'jpg';
							// Check if system renamed it.
							if (file_exists($jpg_path)) {
								$unique_filename = $jpg_file;
							} else {
								$unique_filename = str_replace(".jpg", "-0.jpg", "$jpg_file");
								$this->returnImageCopies($unique_filename, 0, $final_upload_path);
							}
							if (! empty($this->imageCopies)) {
								$files_array = array_reverse($this->imageCopies);
							} else {
								$files_array = array($unique_filename);
							}
							// Finally delete old pdf.
							$this->deleteFilename($pdf_path);
						} else {
							$files_array = array($unique_filename);
						}
						// Loopit and do multiple.
						foreach ($files_array as $unique_filename) {
							// Initiate thumbnail.
							if ($mime_upload == 'image') {
								// Create thumbnail.
								$thumbnail_path = $final_upload_path . 'thumbs/' . $unique_filename;
								// Initiate thumb class.
								$imaging = $this->factory('imaging');
								$thumbnail = $imaging->create($final_upload_path . $unique_filename, $set_thumb);

								if (!empty($do_create_thumb)) {
									// Create thumbnail.
									$thumbnail_path = $final_upload_path . 'thumbs/' . $unique_filename;
									// Initiate thumb class.
									$resizeimg = $imaging->create($final_upload_path . $unique_filename, $set_thumb);

									// thumbnail_type
									$thumbnail_type = $this->setting($this->thumbnailType, $this->setting['thumbnail_type'], 'resize');
									// Set preferred settings.
									switch ($thumbnail_type) {
										case 'resize':
											// resize_thumb_dimension
											$resize_thumb_dimension = $this->setting($this->resizeThumbDimension, $this->setting['resize_thumb_dimension'], '150,150');
											$resize_thumb_dimension_ = explode(',', $resize_thumb_dimension);
											$thumbnail->resize($resize_thumb_dimension_[0], $resize_thumb_dimension_[1]);
											break;
										case 'resizepercent':
											// resize_thumb_percent
											$resize_thumb_percent = $this->setting($this->resizeThumbPercent, $this->setting['resize_thumb_percent'], '40');
											$thumbnail->resizePercent($resize_thumb_percent);
											break;
										case 'cropfromcenter':
											// crop_thumb_fromcenter
											$crop_thumb_fromcenter = $this->setting($this->cropThumbFromcenter, $this->setting['crop_thumb_fromcenter'], '150');
											$thumbnail->cropFromCenter($crop_thumb_fromcenter);
											break;
										case 'crop':
											// crop_thumb_dimension
											$crop_thumb_dimension = $this->setting($this->cropThumbDimension, $this->setting['crop_thumb_dimension'], '0,0,100,50');
											$crop_thumb_dimension_ = explode(',', $crop_thumb_dimension);
											$thumbnail->crop($crop_thumb_dimension_[0], $crop_thumb_dimension_[1], $crop_thumb_dimension_[2], $crop_thumb_dimension_[3]);
											break;
										case 'adaptive':
											// crop_thumb_dimension
											$resize_adaptive_dimension = $this->setting($this->resizeAdaptiveDimension, $this->setting['resize_adaptive_dimension'], '250,250');
											$resize_adaptive_dimension_ = explode(',', $resize_adaptive_dimension);
											$thumbnail->adaptiveResize($resize_adaptive_dimension_[0], $resize_adaptive_dimension_[1]);
											break;
										default:
											// resize_thumb_dimension
											$resize_thumb_dimension = $this->setting($this->resizeThumbDimension, $this->setting['resize_thumb_dimension'], '150,150');
											$resize_thumb_dimension_ = explode(',', $resize_thumb_dimension);
											$thumbnail->resize($resize_thumb_dimension_[0], $resize_thumb_dimension_[1]);
											break;
									}
									// do_thumb_reflect
									$do_thumb_reflect = $this->setting($this->doThumbReflect, $this->setting['do_thumb_reflect'], false, true);
									// thumb_reflect_settings
									if (!empty($do_thumb_reflect)) {
										// thumb_reflect_settings
										$thumb_reflect_settings = $this->setting($this->thumbReflectSettings, $this->setting['thumb_reflect_settings'], '40,40,80,false,#fff');
										$thumb_reflect_settings_ = explode(',', $thumb_reflect_settings);
										if ($thumb_reflect_settings_[3] == 'false') {
											$thumb_reflect_settings_[3] == false;
										} else if ($thumb_reflect_settings_[3] == 'true') {
											$thumb_reflect_settings_[3] == true;
										}
										$thumbnail->createReflection($thumb_reflect_settings_[0], $thumb_reflect_settings_[1], $thumb_reflect_settings_[2], $thumb_reflect_settings_[3], $thumb_reflect_settings_[4]);
									}
									//////////////////////////////////////////////
									// Continue saving thumb image well. /////////
									//////////////////////////////////////////////
									// Finaly save thumbnail!
									$thumbnail->save($thumbnail_path);
									// Set needed permissions.
									chmod($thumbnail_path, $cmod);
								}
								//////////////////////////////////////////////
								// Continue saving a resized image as well. //
								//////////////////////////////////////////////
								if (!empty($do_create_resize_image)) {
									// Create thumbnail.
									$resized_view_path = $final_upload_path . 'resize/' . $unique_filename;
									// Initiate thumb class.
									$resizeimg_ = $this->factory('imaging');
									$resizeimg = $imaging->create($final_upload_path . $unique_filename, $set_thumb);

									// Resize view image if we need it.
									$dimension = $resizeimg->getCurrentDimensions();
									if ($dimension['width'] > $resize_image_dimension_[0] || $dimension['height'] > $resize_image_dimension_[1])
											$resizeimg->resize($resize_image_dimension_[0], $resize_image_dimension_[1]);
									// Save resized image!
									$resizeimg->save($resized_view_path);
									// Set needed permissions.
									chmod($resized_view_path, $cmod);
								}
							} else {
								// We dont have an image so we wont have thumbs and resized images.
								$thumbnail_path = false;
								$resized_view_path = false;
							}
							// Should it be logged?
							if (!empty($log_uploads)) {
								// Now that all is good, lets log this entry to the database.
								$db->invokeQuery('FM_writeFilesLogsQuery',
										$file_overwrite_id, $sub_id, $menu_id,
										$alias, $original_name, $unique_filename,
										$final_upload_path, $thumbnail_path, $resized_view_path,
										$extention_type, $mime_type, $file_description, $group_id,
										$configuration['user_id'], $configuration['time'], $uploaded_size, $file_explained);
							}
							// Set needed permissions.
							chmod($final_upload_path . $unique_filename, $cmod);
						}
						// Return filename and relative path.
						return $final_upload_path . $unique_filename;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		} catch (Exception $e) {
			throw new PHPDS_exception($e->getMessage());
		}
	}

	/**
	 * Convert numeric into file size.
	 *
	 * @param numeric $filesize
	 * @return string
	 * @author info@levaravel.com
	 */
	public function displayFilesize($filesize)
	{
		// Set global.
		if (is_numeric($filesize)) {
			$decr = 1024;
			$step = 0;
			$prefix = array(__('Bytes', 'FileMan'), __('Kb', 'FileMan'), __('Mb', 'FileMan'), __('Gb', 'FileMan'), __('Tb', 'FileMan'), __('Pb', 'FileMan'));
			while (($filesize / $decr) > 0.9) {
				$filesize = $filesize / $decr;
				$step++;
			}
			return round($filesize, 2) . ' ' . $prefix[$step];
		} else {
			return false;
		}
	}

	/**
	 * Internal check to see what thumbnail what settings to return.
	 *
	 * @param mixed $preferred_setting First passed through settings.
	 * @param mixed $database_setting Database called settings.
	 * @param mixed $fail_setting If all else fail use this setting.
	 * @param boolean $boolean Is setting a boolean value?
	 * @return mixed
	 */
	private function setting($preferred_setting, $database_setting, $fail_setting, $boolean = false)
	{
		if (empty($boolean)) {
			if ($preferred_setting == 'default' && !empty($database_setting)) {
				return $database_setting;
			} else if (!empty($preferred_setting)) {
				return $preferred_setting;
			} else {
				return $fail_setting;
			}
		} else {
			if ($preferred_setting == 'default' && isset($database_setting)) {
				return $database_setting;
			} else if (isset($preferred_setting)) {
				return $preferred_setting;
			} else {
				return $fail_setting;
			}
		}
	}

	/**
	 * Build query for extracting stored files.
	 *
	 * @param integer $file_id Get only a single file with a file_id.
	 * @param string $alias Load only files by this alias.
	 * @param integer $sub_id Load only files by this sub_id.
	 * @param integer $menu_id Load only files by this menu_id.
	 * @return string
	 */
	private function buildFileQuery($file_id = 0, $alias = '', $sub_id = 0, $menu_id = 0)
	{
		return $this->db->invokeQuery('FM_buildFileQuery', $file_id, $alias, $sub_id, $menu_id);
	}

	/**
	 * Delete file logs from database.
	 *
	 * @param integer $file_id Get only a single file with a file_id.
	 * @param string $alias Load only files by this alias.
	 * @param integer $sub_id Load only files by this sub_id.
	 * @param integer $menu_id Load only files by this menu_id.
	 * @return boolean Will return true if deleted.
	 */
	public function deleteFiles($file_id = 0, $alias = '', $sub_id = 0, $menu_id = 0)
	{
		$db = $this->db;
		// Load deleted item.
		$files = $this->loadFiles($file_id, $alias, $sub_id, $menu_id);
		// Define.
		$query_grouped = $this->buildFileQuery($file_id, $alias, $sub_id, $menu_id);
		// Delete query.
		if ($db->invokeQuery('FM_deleteFilesLogsQuery', $query_grouped)) {
			// Delete files.
			foreach ($files as $fa) {
				// Delete Original.
				if (!empty($fa['download_file']))
						$this->deleteFilename($fa['download_file']);
				// Delete Resized.
				if (!empty($fa['resized'])) $this->deleteFilename($fa['resized']);
				// Delete Thumb.
				if (!empty($fa['thumbnail'])) $this->deleteFilename($fa['thumbnail']);
			}
			// Return array.
			return $files;
		} else {
			return false;
		} 
	}

	/**
	 * Count uploaded files from the log database.
	 *
	 * @param string $alias Load only files by this alias.
	 * @param integer $sub_id Load only files by this sub_id.
	 * @param integer $menu_id Load only files by this menu_id.
	 *
	 * @return int
	 */
	public function countFiles($alias = '', $sub_id = 0, $menu_id = 0)
	{
		$db = $this->db;
		// Define.
		$query_grouped = $this->buildFileQuery(0, $alias, $sub_id, $menu_id);
		// Load files from database.
		$count = $db->invokeQuery('FM_countFilesLogsQuery', $query_grouped);
		if (empty($count)) {
			return 0;
		} else {
			return (int) $count;
		}
	}

	/**
	 * Load uploaded files from the log database.
	 *
	 * @param integer $file_id Get only a single file with a file_id.
	 * @param string $alias Load only files by this alias.
	 * @param integer $sub_id Load only files by this sub_id.
	 * @param integer $menu_id Load only files by this menu_id.
	 * @param string $order What the query should be ordered by. allows you to order by any of these column values : file_id, sub_id, menu_id, alias, original_filename, new_filename, relative_path, thumbnail, resized, extention, mime_type, file_desc, group_id, user_id, date_stored
	 * @param string $limit how many files should be returned in the array.
	 * @return array
	 */
	public function loadFiles($file_id = 0, $alias = '', $sub_id = 0, $menu_id = 0, $order = 'file_id DESC', $limit = '0,5')
	{
		$db = $this->db;
		// Define.
		$query_grouped = $this->buildFileQuery($file_id, $alias, $sub_id, $menu_id);
		// Load files from database.
		$load_files_db = $db->invokeQuery('FM_readFilesLogsQuery', $query_grouped, $file_id, $order, $limit);
		
		if (! empty($load_files_db)) {
			// Loop and gather results.
			foreach ($load_files_db as $files_array) {
				// Create download filename.
				$files_array['download_file'] = $files_array['relative_path'] . $files_array['new_filename'];
				// Create human readable file size.
				$files_array['format_file_size'] = $this->displayFilesize($files_array['file_size']);
				// Create extention image.
				$files_array['extention_img'] = $this->iconType($files_array['extention']);
				// File array.
				$final_files_return[$files_array['file_id']] = $files_array;
				// Clear.
				unset($files_array);
			}
			if (!empty($final_files_return)) {
				// Return results.
				return $final_files_return;
			} else {
				// No results to return.
				return array();
			}
		} else {
			return array();
		}
	}

	/**
	 * Check if multiple images exists converted from a pdf with multiple pages.
	 *
	 * @param string $file
	 * @param int $nr The current file number in the loop.
	 * @param string Add directy if you wish for only file to be returned.
	 */
	public function returnImageCopies($file, $nr=0, $directory='')
	{
		// Multiple files should exist, recall and check.
		if (file_exists($directory . $file)) {
			$this->imageCopies[$nr] = $file;
			$nr_next = $nr + 1;
			$file_next = str_replace("-$nr.jpg", "-$nr_next.jpg", "$file");
			$this->returnImageCopies($file_next, $nr_next, $directory);
		}
	}

	/**
	 * Enables the caller to download a file to a local writable directory.
	 *
	 * @param $url The url to download from
	 * @param $copy_to The location to copy to add the name to the file too, example: /var/www/test.zip
	 * @return mixed
	 */
	public function downloadFile($url, $copy_to)
	{
		$template = $this->template;
		// Check if function exists.
		if (!function_exists('curl_init')) {
			throw new Exception(__('Oops cURL is not installed on your server, I cannot continue.', 'FileMan'));
			return false;
		}
		$err_msg = '';
		$out = fopen($copy_to, 'wb');
		if ($out == false) {
			throw new Exception(sprintf(__('File %s could not be written to %s, please check permission, directories and if file exists.', 'FileMan'), $url, $copy_to));
			return false;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FILE, $out);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);
		if (curl_error($ch)) {
			throw new Exception(sprintf(__('There was a cURL error : %s when I tried to copy to : %s', 'FileMan'), curl_error($ch), $copy_to));
			return false;
		}
		curl_close($ch);
		return $copy_to;
	}

	/**
	 * Extracts a ZIP archive to the specified extract path
	 *
	 * @param string $file The ZIP archive to extract (including the path)
	 * @param string $extractPath The path to extract the ZIP archive to
	 *
	 * @return boolean TURE if the ZIP archive is successfully extracted, FALSE if there was an errror
	 *
	 */
	public function zipExtract($file, $extractPath)
	{
		$zip = new ZipArchive();
		$res = $zip->open($file);
		if ($res === TRUE) {
			$zip->extractTo($extractPath);
			$zip->close();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Copy file or folder from source to destination, it can do
	 * recursive copy as well and is very smart
	 * It recursively creates the dest file or directory path if there weren't exists
	 * Situtaions :
	 * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
	 * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
	 * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest
	 * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
	 * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
	 * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
	 * @todo
	 * - Should have rollback technique so it can undo the copy when it wasn't successful
	 * - Auto destination technique should be possible to turn off
	 * - Supporting callback function
	 * - May prevent some issues on shared enviroments : http://us3.php.net/umask
	 * @param $source //file or folder
	 * @param $dest ///file or folder
	 * @param $options //folderPermission,filePermission
	 * @return boolean
	 */
	public function smartCopy($source, $dest, $options=array('folder_permission' => 0755, 'file_permission' => 0644))
	{
		$result = false;

		if (is_file($source)) {
			if ($dest[strlen($dest) - 1] == '/') {
				if (!file_exists($dest)) {
					cmfcDirectory::makeAll($dest, $options['folder_permission'], true);
				}
				$__dest = $dest . "/" . basename($source);
			} else {
				$__dest = $dest;
			}
			$result = copy($source, $__dest);
			chmod($__dest, $options['file_permission']);
		} elseif (is_dir($source)) {
			if ($dest[strlen($dest) - 1] == '/') {
				if ($source[strlen($source) - 1] == '/') {
					//Copy only contents
				} else {
					//Change parent itself and its contents
					$dest = $dest . basename($source);
					@mkdir($dest);
					chmod($dest, $options['file_permission']);
				}
			} else {
				if ($source[strlen($source) - 1] == '/') {
					//Copy parent directory with new name and all its content
					@mkdir($dest, $options['folder_permission']);
					chmod($dest, $options['file_permission']);
				} else {
					//Copy parent directory with new name and all its content
					@mkdir($dest, $options['folder_permission']);
					chmod($dest, $options['file_permission']);
				}
			}

			$dirHandle = opendir($source);
			while ($file = readdir($dirHandle)) {
				if ($file != "." && $file != "..") {
					if (!is_dir($source . "/" . $file)) {
						$__dest = $dest . "/" . $file;
					} else {
						$__dest = $dest . "/" . $file;
					}
					//echo "$source/$file ||| $__dest<br />";
					$result = $this->smartCopy($source . "/" . $file, $__dest, $options);
				}
			}
			closedir($dirHandle);
		} else {
			$result = false;
		}
		return $result;
	}

	/**
	 * Will delete a whole directory recursively (directory muste be writable).
	 *
	 * @param $dirname Directory to delete.
	 * @return boolean.
	 */
	public function deleteDir($dirname)
	{
		if (is_dir($dirname)) $dir_handle = opendir($dirname);
		if (!$dir_handle) return false;
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname . "/" . $file)) unlink($dirname . "/" . $file);
				else $this->deleteDir($dirname . '/' . $file);
			}
		}
		closedir($dir_handle);
		rmdir($dirname);
		return true;
	}

	/**
	 * Connects to a normal ftp or secure server.
	 * If values left empty system will use PHPDevShell configuration values.
	 *
	 * @param $username
	 * @param $password
	 * @param $host
	 * @param $port
	 * @param $ssl
	 * @param $timeout
	 * @param $chdir
	 * @return resource connection id.
	 */
	public function establishFtp($username='config', $password='config', $host='config', $port='config', $ssl='config', $timeout='config', $chdir='config')
	{
		$db = $this->db;
		$template = $this->template;

		// Load database settings.
		$conf = $db->getSettings(array('ftp_enable', 'ftp_username', 'ftp_password', 'ftp_host', 'ftp_port', 'ftp_ssl', 'ftp_timeout', 'ftp_root'));

		// Load defaults.
		if (!empty($conf['ftp_enable'])) {
			if ($username == 'config') $username = $conf['ftp_username'];
			if ($password == 'config') $password = $conf['ftp_password'];
			if ($host == 'config') $host = $conf['ftp_host'];
			if ($port == 'config') $port = $conf['ftp_port'];
			if ($ssl == 'config') $ssl = $conf['ftp_ssl'];
			if ($timeout == 'config') $timeout = $conf['ftp_timeout'];
			if ($chdir == 'config' && !empty($conf['ftp_root']))
				$chdir = $conf['ftp_root'];
		}
		// Establish connection.
		if ($ssl == false) {
			if (!$resource = ftp_connect($host, $port, $timeout)) {
				throw new Exception(sprintf(__('I could not connect to local ftp server %s, user %s.', 'FileMan'), $host, $username));
				return false;
			}
		} else {
			if (!$resource = ftp_ssl_connect($host, $port, $timeout)) {
				throw new Exception(sprintf(__('I could not connect using SSL to local ftp server %s, user %s.', 'FileMan'), $host, $username));
				return false;
			}
		}
		// Log in to server.
		if (ftp_login($resource, $username, $password)) {
			// Should we change the directory path.
			if (!empty($chdir) && $chdir != 'config') {
				ftp_chdir($resource, $chdir);
			}

			return $resource;
		} else {
			throw new Exception(sprintf(__('Incorrect login for local ftp server %s, user %s.', 'FileMan'), $host, $username));
			return false;
		}
	}

	/**
	 * The following is a fully tested function (based on a previous note) that recursively puts files from a source directory to a destination directory. See http://rufy.com/tech/archives/000026.html for more information.
	 *
	 * NOTE: Use full path name for the destination directory and the destination directory must already exist
	 *
	 * @param resource $conn_id
	 * @param string $src_dir
	 * @param string $dst_dir
	 * @return void
	 */
	public function ftpRcopy($conn_id, $src_dir, $dst_dir)
	{
		$template = $this->template;
		if ($dir = dir($src_dir)) {
			while ($file = $dir->read()) { // do this for each file in the directory
				if ($file != '.' && $file != '..') { // to prevent an infinite loop
					if (is_dir($src_dir . '/' . $file)) { // do the following if it is a directory
						if (!@ftp_nlist($conn_id, $dst_dir . '/' . $file)) {
							ftp_mkdir($conn_id, $dst_dir . '/' . $file); // create directories that do not yet exist
							ftp_chmod($conn_id, 0755, $dst_dir . '/' . $file);
						}
						$this->ftpRcopy($conn_id, $src_dir . '/' . $file, $dst_dir . '/' . $file); // recursive part
						$this->uploadHistory[] = array('from' => $src_dir . '/' . $file, 'to' => $dst_dir . '/' . $file);
					} else {
						if (!ftp_put($conn_id, $dst_dir . '/' . $file, $src_dir . '/' . $file, FTP_BINARY)) { // put the files
							throw new Exception(sprintf(__('There was a problem copying file %s', 'FileMan'), $dst_dir . '/' . $file));
						} else {
							$this->uploadHistory[] = array('from' => $src_dir . '/' . $file, 'to' => $dst_dir . '/' . $file);
							ftp_chmod($conn_id, 0644, $dst_dir . '/' . $file);
						}
					}
				}
			}
			$dir->close();
			if (!empty($this->uploadHistory)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Lists a directories sub directories. Skips files.
	 * @param string The absolute path to check. $path
	 * @param int How deep scanning should occur.
	 * @return array
	 */
	function getDirListing($path = '.', $level = 0)
	{
		if (is_dir($path)) {
			// Directories to ignore when listing output.
			$ignore = array('.', '..');
			// Open the directory to the handle $dh
			$dh = @opendir($path);
			$dir = array();
			// Loop through the directory
			while (false !== ( $file = readdir($dh) )) {
				// Check that this file is not to be ignored
				if (!in_array($file, $ignore)) {
					// Show directories only
					if (is_dir("$path/$file")) {
						// Re-call this same function but on a new directory.
						// this is what makes function recursive.
						$dir[] = array('path' => $path, 'folder' => $file);
						if (!empty($level)) $this->getDirListing("$path/$file", ($level + 1));
					}
				}
			}
			// Close the directory handle
			closedir($dh);
			return $dir;
		} else {
			return false;
		}
	}
}