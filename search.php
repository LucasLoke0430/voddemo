<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once APP_ROOT . '/app/Services/SourceManager.php';
require_once APP_ROOT . '/app/Services/RouterService.php';

$q = trim((string)($_GET['q'] ?? ''));
$type = trim((string)($_GET['type'] ?? ''));

$items = [];
$bestKey = RouterService::bestProviderKey();

// Map Chinese category names to potential API type names
function mapCategoryToType(string $category): array {
  $map = [
    '电影片' => ['电影', '电影片', 'movie', 'movies'],
    '连续剧' => ['连续剧', '电视剧', 'tv', 'series', 'drama'],
    '动漫片' => ['动漫', '动漫片', '动画', 'anime', 'cartoon'],
    '综艺片' => ['综艺', '综艺片', 'variety', 'show'],
    '短剧' => ['短剧', '微剧', 'short'],
  ];
  return $map[$category] ?? [$category];
}

if ($q !== '' || $type !== '') {
  $providers = SourceManager::enabledProviders();

  // Put best provider first
  usort($providers, fn($a,$b)=>
    ($a->key()===$bestKey ? -1 : 0) <=> ($b->key()===$bestKey ? -1 : 0)
  );

  foreach ($providers as $p) {
    if ($q !== '') {
      // User provided a search query - use search() method
      $res = $p->search($q, 1);
      $items = $res['items'] ?? [];
      
      // If type filter is also provided, filter results by type
      if ($type !== '' && !empty($items)) {
        $typeMatches = mapCategoryToType($type);
        $items = array_filter($items, function($item) use ($typeMatches) {
          $itemType = strtolower((string)($item['type'] ?? ''));
          foreach ($typeMatches as $match) {
            if (stripos($itemType, strtolower($match)) !== false) {
              return true;
            }
          }
          return false;
        });
        $items = array_values($items); // Re-index array
      }
    } elseif ($type !== '') {
      // Only type selected - use latest() and filter by type
      $typeMatches = mapCategoryToType($type);
      
      // Try multiple pages to get more results
      $allItems = [];
      for ($page = 1; $page <= 3; $page++) {
        $res = method_exists($p, 'latest') ? $p->latest($page) : ['items' => []];
        $pageItems = $res['items'] ?? [];
        
        // Filter by type
        foreach ($pageItems as $item) {
          $itemType = strtolower((string)($item['type'] ?? ''));
          foreach ($typeMatches as $match) {
            if (stripos($itemType, strtolower($match)) !== false) {
              $allItems[] = $item;
              break;
            }
          }
        }
        
        // If we got enough results or no more items, stop
        if (count($allItems) >= 20 || empty($pageItems)) {
          break;
        }
      }
      $items = $allItems;
    }
    
    if (!empty($items)) break;
  }
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/app.css">
  <title>搜索 - 免费短视频分享大全</title>
</head>
<body>
<div class="layout">
  <header class="header">
    <div class="header-content">
      <div class="header-top">
        <a href="/home.php" class="brand">免费短视频分享大全</a>
        <form class="searchbar" action="/search.php" method="get">
          <input name="q" value="<?=h($q)?>" placeholder="搜索影片 / 剧集..." />
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
        <a class="nav-item <?=$type === '电影片' ? 'active' : ''?>" href="/search.php?type=电影片">电影片</a>
        <a class="nav-item <?=$type === '连续剧' ? 'active' : ''?>" href="/search.php?type=连续剧">连续剧</a>
        <a class="nav-item <?=$type === '动漫片' ? 'active' : ''?>" href="/search.php?type=动漫片">动漫片</a>
        <a class="nav-item <?=$type === '综艺片' ? 'active' : ''?>" href="/search.php?type=综艺片">综艺片</a>
        <a class="nav-item <?=$type === '短剧' ? 'active' : ''?>" href="/search.php?type=短剧">短剧</a>
      </nav>
    </div>
  </header>

  <main class="main">
    <div class="section-header">
      <h2 class="h2"><?=$type !== '' && $q === '' ? '分类浏览' : '搜索结果'?></h2>
      <?php if ($bestKey): ?>
        <span class="pill">最佳源: <?=h(SourceManager::nameByKey($bestKey))?></span>
      <?php endif; ?>
    </div>

    <?php if ($q !== ''): ?>
      <div class="search-results-header">
        搜索关键词: <b><?=h($q)?></b>
      </div>
    <?php elseif ($type !== ''): ?>
      <div class="search-results-header">
        分类: <b><?=h($type)?></b>
      </div>
    <?php endif; ?>

    <div class="grid">
      <?php foreach($items as $it): ?>
        <a class="card" href="/detail.php?source=<?=urlencode($it['sourceKey'] ?? '')?>&id=<?=urlencode($it['id'] ?? '')?>">
          <img class="poster" src="<?=h($it['poster'] ?? '')?>" alt="<?=h($it['title'] ?? '')?>" loading="lazy">
          <div class="card-info">
            <div class="t"><?=h($it['title'] ?? '')?></div>
            <div class="m"><?=h(trim(($it['year'] ?? '') . ' ' . ($it['type'] ?? '') . ' ' . ($it['remark'] ?? '')))?></div>
          </div>
        </a>
      <?php endforeach; ?>
      <?php if (empty($items) && ($q !== '' || $type !== '')): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-secondary);">
          未找到相关影片
        </div>
      <?php elseif (empty($items)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-secondary);">
          请输入搜索关键词或选择分类
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
