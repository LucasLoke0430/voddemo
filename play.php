<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once APP_ROOT . '/app/Services/ProviderRegistry.php';

$source = $_GET['source'] ?? '';
$id = $_GET['id'] ?? '';
$ep = (string)($_GET['ep'] ?? '1');

$p = ProviderRegistry::byKey($source);
if (!$p) die("Provider not found");

$play = $p->play($id, $ep);
$stream = $play['url'] ?? '';
if (!$stream) die("No stream URL returned");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Play</title>
  <style>body{margin:20px;font-family:system-ui}</style>
</head>
<body>
<h2>Playing</h2>
<video id="v" width="960" controls></video>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
const video = document.getElementById('v');
const src = <?=json_encode($stream)?>;

if (Hls.isSupported()) {
  const hls = new Hls();
  hls.loadSource(src);
  hls.attachMedia(video);
} else {
  video.src = src;
}
</script>
</body>
</html>
