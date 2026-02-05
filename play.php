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
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/app.css">
  <title>播放 - VOD DEMO</title>
</head>
<body>
<div class="layout">
  <header class="header">
    <div class="header-content">
      <div class="header-top">
        <a href="home.php" class="brand">VOD DEMO</a>
        <form class="searchbar" action="search.php" method="get">
          <input name="q" placeholder="搜索影片 / 剧集..." />
          <button type="submit">搜索</button>
        </form>
        <div class="header-actions">
          <a href="home.php" class="header-link">首页</a>
          <a href="search.php" class="header-link">搜索</a>
          <a href="admin/sources.php" class="header-link">源管理</a>
        </div>
      </div>
      <nav class="nav">
        <a class="nav-item" href="home.php">首页</a>
        <a class="nav-item" href="search.php?type=电影片">电影片</a>
        <a class="nav-item" href="search.php?type=连续剧">连续剧</a>
        <a class="nav-item" href="search.php?type=动漫片">动漫片</a>
        <a class="nav-item" href="search.php?type=综艺片">综艺片</a>
        <a class="nav-item" href="search.php?type=短剧">短剧</a>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="video-container">
      <div class="video-wrapper">
        <video id="v" controls autoplay></video>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
const video = document.getElementById('v');
const src = <?=json_encode($stream)?>;

if (Hls.isSupported()) {
  const hls = new Hls();
  hls.loadSource(src);
  hls.attachMedia(video);
  hls.on(Hls.Events.MANIFEST_PARSED, function() {
    video.play();
  });
} else if (video.canPlayType('application/vnd.apple.mpegurl')) {
  video.src = src;
  video.addEventListener('loadedmetadata', function() {
    video.play();
  });
} else {
  video.src = src;
}
</script>
</body>
</html>
