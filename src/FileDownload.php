<?php
namespace sharapeco\HTTP;

class FileDownloadException extends \ErrorException {}

class FileDownload {

	protected $file;
	protected $content;

	public function __construct($file = null, $content = null) {
		$this->file = $file;
		$this->content = $content;
	}

	/// ファイルパスを指定して初期化
	/// @param $file: string
	/// @return FileDownload
	public static function initWithFile($file) {
		return new FileDownload($file, null);
	}

	/// ファイルの内容を文字列として設定して初期化
	/// @param $content: string
	/// @return FileDownload
	public static function initWithContent($content) {
		return new FileDownload(null, $content);
	}

	/// $filename の名前でダウンロードさせる
	/// @param $filename: string
	public function download($filename) {

		if (!isset($this->file) && !isset($this->content)) {
			throw new FileDownloadException('FileDownload instance did not initialized.');
		}

		// Android (4.4?) でダウンロードした PDF が表示できないバグ対策
		if (preg_match('/\bAndroid\b/', getenv('HTTP_USER_AGENT')) && preg_match('/[.]pdf\\z/', $filename)) {
			return $this->showPDF($filename);
		}
	
		// ダウンロードファイル名
		$filename = $this->escapeFilename($filename);

		// 出力
		header('Accept-Ranges: bytes');
		header('Content-Type: application/octet-stream');
		if ($this->browserSupportsUnicode()) {
			header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($filename));
		} else {
			header('Content-Disposition: attachment; filename="' . mb_convert_encoding($filename, 'Shift_JIS', 'UTF-8') . '"');
		}
		
		if (isset($this->content)) {
			header('Content-Length: ' . strlen($this->content));
			echo $this->content;
		} else {
			$size = @filesize($this->file);
			if ($size === false) {
				throw new FileDownloadException('Could not get file size: ' . $this->file);
			}
			header('Content-Length: ' . $size);
			$readed = @readfile($this->file);
			if ($readed === false) {
				throw new FileDownloadException('Could not open file: ' . $this->file);
			}
		}
	}

	/// PDF ファイルを表示する
	/// @param $filename: string
	public function showPDF($filename) {

		if (!isset($this->file) && !isset($this->content)) {
			throw new FileDownloadException('FileDownload instance did not initialized.');
		}

		// ダウンロードファイル名
		$filename = $this->sanitizeFilename($filename);
		
		// 出力
		header('Accept-Ranges: bytes');
		if ($this->browserSupportsUnicode()) {
			header('Content-Type: application/pdf; name=' . rawurlencode($filename));
			header('Content-Disposition: inline; filename*=UTF-8\'\'' . rawurlencode($filename));
		} else {
			header('Content-Type: application/pdf; name="' . mb_convert_encoding($filename, 'Shift_JIS', 'UTF-8') . '"');
			header('Content-Disposition: inline; filename="' . mb_convert_encoding($filename, 'Shift_JIS', 'UTF-8') . '"');
		}
		header('Content-Transfer-Encoding: binary');
		
		if (isset($this->content)) {
			header('Content-Length: ' . strlen($this->content));
			echo $this->content;
		} else {
			if (!file_exists($this->file)) {
				throw new FileDownloadException('Download file does not exists: ' . $this->file);
			}
			header('Content-Length: ' . filesize($this->file));
			readfile($this->file);
		}
	}

	/// @return boolean
	public function browserSupportsUnicode() {
		return !preg_match('/\bMSIE\b|\bSafari [12345]\b/', getenv('HTTP_USER_AGENT'));
	}

	/// ファイル名として使えない文字・禁止する文字をエスケープする
	/// @param $filename: string
	/// @return string
	public function sanitizeFilename($filename) {

		// path delimiters, redirect marks, wildcards...
		$filename = preg_replace('{[\\\\/:*?"<>|]}', '', $filename);

		// spaces to underline
		$filename = preg_replace('/\\s/u', '_', $filename);

		// control characters
		$ascii = '[\\x00-\\x1F\\x7F]';
		$latin1 = '\\xC2[\\x80-\\x9F]';
		$punctuation = '\\xE2\\x80[\\x80-\\x8F\\xA7-\\xAF]|\\xE2\\x81[\\x9F-\\xAF]';
		$filename = preg_replace("/$ascii|$latin1|$punctuation/", '', $filename);

		return $filename;
	}
	
}
