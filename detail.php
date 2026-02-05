<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once APP_ROOT . '/app/Services/SourceManager.php';
require_once APP_ROOT . '/app/Services/RouterService.php';

$source = (string)($_GET['source'] ?? '');
$id = (string)($_GET['id'] ?? '');
if ($source === '' || $id === '') die("Missing source or id");

$providers = SourceManager::enabledProviders();
$p = null;
foreach ($providers as $pp) {
  if ($pp->key() === $source) { $p = $pp; break; }
}
if (!$p) die("Provider not found: " . h($source));

$sourceName = SourceManager::nameByKey($source);
$d = $p->detail($id);
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/app.css">
  <title><?=h($d['title'] ?? 'Detail')?> - 免费短视频分享大全</title>
</head>
<body>
<div class="layout">
  <header class="header">
    <div class="header-content">
      <div class="header-top">
        <a href="/home.php" class="brand">免费短视频分享大全</a>
        <form class="searchbar" action="/search.php" method="get">
          <input name="q" placeholder="搜索影片 / 剧集..." />
          <button type="submit">搜索</button>
        </form>
        <div class="header-actions">
          <a href="/home.php" class="header-link">首页</a>
          <a href="/search.php" class="header-link">搜索</a>
          <a href="/admin/sources.php" class="header-link">源管理</a>
        </div>
      </div>
      <nav class="nav">
        <a class="nav-item" href="/home.php">首页</a>
        <a class="nav-item" href="/search.php?type=电影片">电影片</a>
        <a class="nav-item" href="/search.php?type=连续剧">连续剧</a>
        <a class="nav-item" href="/search.php?type=动漫片">动漫片</a>
        <a class="nav-item" href="/search.php?type=综艺片">综艺片</a>
        <a class="nav-item" href="/search.php?type=短剧">短剧</a>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="detail-header">
      <?php if (!empty($d['poster'])): ?>
        <img src="<?=h($d['poster'])?>" alt="<?=h($d['title'] ?? '')?>" class="detail-poster">
      <?php endif; ?>
      <div class="detail-info">
        <h1 class="detail-title"><?=h($d['title'] ?? '')?></h1>
        <div class="detail-meta">
          <?php if (!empty($d['year'])): ?>
            <span><?=h($d['year'])?></span>
          <?php endif; ?>
          <?php if (!empty($d['type'])): ?>
            <span><?=h($d['type'])?></span>
          <?php endif; ?>
          <?php if (!empty($d['area'])): ?>
            <span><?=h($d['area'])?></span>
          <?php endif; ?>
          <span class="pill"><?=h($sourceName)?></span>
        </div>
        <?php if (!empty($d['desc'])): ?>
          <div class="detail-desc"><?=h($d['desc'])?></div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($d['episodes'])): ?>
      <div class="episodes-section">
        <h2 class="h2">播放列表</h2>
        <div class="episodes-grid">
          <?php foreach ($d['episodes'] as $ep): ?>
            <a class="episode-btn" href="/play.php?source=<?=urlencode($source)?>&id=<?=urlencode($id)?>&ep=<?=urlencode($ep['ep'] ?? '1')?>">
              <?=h($ep['name'] ?? $ep['ep'] ?? '播放')?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
