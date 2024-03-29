# FileDownload class

- Make downloads from file
- Make downloads from content
- IE 9+ (ja), Safari 5+ (ja), Android 4.4+, Chrome, Firefox, Edge support

## Usage

### From file

```PHP
$filePath = '/path/to/file';

$download = sharapeco\HTTP\FileDownload::initWithFile($filePath);
$download->download('download-filename');
```

### From content

```PHP
$content = 'This is the content of the file';

$download = sharapeco\HTTP\FileDownload::initWithContent($content);
$download->download('download-filename');
```

### Show PDF in browser with filename

```PHP
$filePath = '/path/to/pdf';

$download = sharapeco\HTTP\FileDownload::initWithFile($filePath);
$download->showPDF('filename.pdf');
```
