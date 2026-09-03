<?php
// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

// URLパラメータから日付を取得する
if(isset($_GET['date'])) {
    $date = $_GET['date'];
} else {
    // header('Location: index.php');
    exit;
}

// ==========================================
// 1. データベース接続設定 (PDO)
// ==========================================
$db_host = 'localhost';
$db_name = 'schedule_db';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit('データベース接続エラー：' . $e->getMessage());
}

$messege = '';
$error = '';

// update
if(isset($_POST['update'])) {
    $plan = trim($_POST['plan']);

    if(!empty($plan)) {
        $sql = "UPDATE schedules SET plan = :plan WHERE target_date = :target_date";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':plan', $plan, PDO::PARAM_STR);
        $stmt->bindValue(':target_date', $date, PDO::PARM_STR);

        if(stmt->execute()) {
            // 更新成功後、カレンダー画面へ戻る
            header('Location: index.php?ym=' . substr($date, 0, 7));
            exit;
        } else {
            $error = '更新に失敗しました。';
        }
    } else {
        $error = '予定を入力してください。';
    }
}

// delete

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>詳細</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">予定の詳細</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <form medhod="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">対象日</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?>" readonlydisabled>
                    </div>
                    <div class="mb-4">

                    </div>
                    <div class="d-grid gap-2">
                        <button>変更を保存する</button>
                        <button>予定を削除する</button>
                        <a href="">戻る</a>
                    </div>
                </form>
            </div>
            
        </div>
        
    </div>
</body>
</html>

