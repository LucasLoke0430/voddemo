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
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/app.css">
  <title>Home</title>
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="brand">VOD Demo</div>
    <a class="nav active" href="/home.php">首页</a>
    <a class="nav" href="/search.php">搜索</a>
    <a class="nav" href="/admin/sources.php">源管理</a>
  </aside>

  <main class="main">
    <div class="topbar">
      <form class="searchbar" action="/search.php" method="get">
        <input name="q" placeholder="搜索影片 / 剧集..." />
        <button>搜索</button>
      </form>

      <div class="pill">
        Best Source: <?=h($bestKey ? SourceManager::nameByKey($bestKey) : '-')?>
      </div>
    </div>

    <h2 class="h2">热门</h2>

    <div class="grid">
      <?php foreach($items as $it): ?>
        <?php $srcName = SourceManager::nameByKey((string)($it['sourceKey'] ?? '')); ?>
        <a class="card" href="/detail.php?source=<?=urlencode($it['sourceKey'])?>&id=<?=urlencode($it['id'])?>">
          <img class="poster" src="<?=h($it['poster'])?>" alt="">
          <div class="t"><?=h($it['title'])?></div>
          <div class="src"><?=h($srcName)?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </main>
</div>
</body>
</html>
