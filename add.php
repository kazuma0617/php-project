<?php
// タイムゾーンの設定
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
} catch(PDOException $e) {
    exit('データベース接続エラー：' . $e->getMessage());
}

// 「登録する」ボタンが押された時の処理
$error = '';
if(isset($_POST['save'])) {
    $plan = $_POST['plan'];

    if(!empty($plan)) {
        $sql = "INSERT INTO schedules (target_date, plan) VALUES (:target_date, :plan) ON DUPLICATE KEY UPDATE plan = :plan_update";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('target_date', $date, PDO::PARAM_STR);
        $stmt->bindValue('plan', $plan, PDO::PARAM_STR);
        $stmt->bindValue('plan_update', $plan, PDO::PARAM_STR);
        $stmt->execute();

        // 登録完了後、該当の月を開いた状態でカレンダーに戻る
        $ym = date('Y-m', strtotime($date));
        header("Location: index.php?ym={$ym}");
        exit;
    } else {
        $error = '予定内容を選択してください';
    }
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4">来所予定の追加</h3>
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">日付</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>
                <div class="mb-4">
                    <label class="form-label font-weght-bold">予定内容</label>
                    <select name="plan" class="form-select" required>
                        <option value="">選択してください</option>
                        <option value="A">A（ご飯あり１日）</option>
                        <option value="A">B（ご飯あり午前）</option>
                        <option value="A">C（ご飯あり午後）</option>
                        <option value="A">D（ご飯なし午前）</option>
                        <option value="A">E（ご飯なし午後）</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" name="save" class="btn btn-primary">登録する</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>