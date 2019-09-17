<?php
use PHPUnit\Framework\TestCase;
use sharapeco\HTTP\FileDownload;

final class FileDownloadTest extends TestCase {

	/**
	 * @test
	 */
	public function testSanitizePath() {
		$inst = new FileDownload();
		$this->assertEquals(
			$inst->sanitizeFilename('remove/path\\delimiters:<redirects>|wild*cards?quotes".exe'),
			'removepathdelimitersredirectswildcardsquotes.exe'
		);
	}

	/**
	 * @test
	 */
	public function testSanitizeSpaces() {
		$ZWSP = "\xE2\x80\x8B"; // → ''
		$HairSP = "\xE2\x80\x8A"; // → '_'

		$inst = new FileDownload();
		$this->assertEquals(
			$inst->sanitizeFilename("the name　of${ZWSP}file${HairSP}has spaces"),
			'the_name_offile_has_spaces'
		);
	}

}
