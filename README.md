# FileDownload class

- Make downloads from file
- Make downloads from content
- IE 9+, Safari 5+, Android 4.4+, Chrome, Firefox, Edge support

## Usage

### From file

```PHP
$filePath = '/path/to/file';

$download = FileDownload::initWithFile($filePath);
$download->download('download-filename');
```

### From content

```PHP
$content = 'This is the content of the file';

$download = FileDownload::initWithContent($content);
$download->download('download-filename');
```

### Show PDF in browser with filename

```PHP
$filePath = '/path/to/pdf';

$download = FileDownload::initWithFile($filePath);
$download->showPDF('filename.pdf');
```
