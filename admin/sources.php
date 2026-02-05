<?php
require_once __DIR__ . '/../app/bootstrap.php';

$path = STORAGE_DIR . '/sources.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $raw = $_POST['json'] ?? '';
  $data = json_decode($raw, true);
  if (!is_array($data)) {
    $err = "Invalid JSON";
  } else {
    json_write($path, $data);
    $ok = "Saved!";
  }
}

$current = file_exists($path) ? file_get_contents($path) : "[]";
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="../assets/app.css" />
  <title>源管理</title>
</head>
<body>
  <div class="layout">
    <header class="header">
      <div class="header-content">
        <div class="header-top">
          <a class="brand" href="../home.php">VOD DEMO</a>
          <div class="header-actions">
            <a class="header-link" href="../home.php">首页</a>
            <a class="header-link" href="../search.php">搜索</a>
            <a class="header-link" href="sources.php">源管理</a>
          </div>
        </div>
        <nav class="nav">
          <a class="nav-item" href="../home.php">首页</a>
          <a class="nav-item" href="../search.php?type=电影片">电影片</a>
          <a class="nav-item" href="../search.php?type=连续剧">连续剧</a>
          <a class="nav-item" href="../search.php?type=动漫片">动漫片</a>
          <a class="nav-item" href="../search.php?type=综艺片">综艺片</a>
          <a class="nav-item" href="../search.php?type=短剧">短剧</a>
          <a class="nav-item active" href="sources.php">源管理</a>
        </nav>
      </div>
    </header>

    <main class="main">
      <h2 class="h2">编辑 sources.json</h2>
      <?php if (!empty($err)): ?><div class="alert bad"><?= h($err) ?></div><?php endif; ?>
      <?php if (!empty($ok)): ?><div class="alert ok"><?= h($ok) ?></div><?php endif; ?>

      <form method="post">
        <textarea name="json" class="textarea" spellcheck="false"><?= h($current) ?></textarea>
        <div class="actions">
          <button class="btn" type="submit">保存</button>
          <a class="btn ghost" href="../home.php">返回</a>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
