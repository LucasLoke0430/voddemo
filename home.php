<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once APP_ROOT . '/app/Services/SourceManager.php';
require_once APP_ROOT . '/app/Services/RouterService.php';

$bestKey = RouterService::bestProviderKey();

$providers = SourceManager::enabledProviders();
$p = null;
foreach ($providers as $pp) {
  if ($bestKey && $pp->key() === $bestKey) { $p = $pp; break; }
}
if (!$p && $providers) $p = $providers[0];

$items = [];
if ($p) {
  $res = $p->latest(1);
  $items = $res['items'] ?? [];
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/app.css">
  <title>VOD DEMO</title>
</head>
<body>
<div class="layout">
  <header class="header">
    <div class="header-content">
      <div class="header-top">
        <a href="/home.php" class="brand">VOD DEMO</a>
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
        <a class="nav-item active" href="/home.php">首页</a>
        <a class="nav-item" href="/search.php?type=电影片">电影片</a>
        <a class="nav-item" href="/search.php?type=连续剧">连续剧</a>
        <a class="nav-item" href="/search.php?type=动漫片">动漫片</a>
        <a class="nav-item" href="/search.php?type=综艺片">综艺片</a>
        <a class="nav-item" href="/search.php?type=短剧">短剧</a>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="section-header">
      <h2 class="h2">最新影片</h2>
      <?php if ($bestKey): ?>
        <span class="pill">最佳源: <?=h(SourceManager::nameByKey($bestKey))?></span>
      <?php endif; ?>
    </div>

    <div class="grid">
      <?php foreach($items as $it): ?>
        <?php $srcName = SourceManager::nameByKey((string)($it['sourceKey'] ?? '')); ?>
        <a class="card" href="/detail.php?source=<?=urlencode($it['sourceKey'])?>&id=<?=urlencode($it['id'])?>">
          <img class="poster" src="<?=h($it['poster'] ?? '')?>" alt="<?=h($it['title'] ?? '')?>" loading="lazy">
          <div class="card-info">
            <div class="t"><?=h($it['title'] ?? '')?></div>
            <div class="src"><?=h($srcName)?></div>
          </div>
        </a>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-secondary);">
          暂无影片数据
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
