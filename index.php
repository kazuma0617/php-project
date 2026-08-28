<?php
// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

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

// ==========================================
// 2. データの追加処理 (CREATE)
// ==========================================
if(isset($_POST['add_schedule'])) {
    $target_date = $POST['target_date'];
    $plan = $POST['plan'];

    // 入力値が空でないか確認
    if(!empty($target_date) && !empty($plan)) {
        // SQL文を作成
        $sql = "INSERT INTO schedules (target_date, plan) VALUES (:target_date, :plan) ON DUPLICATE KEY UPDATE plan = :plan_update";

        // プリペアドステートメントを準備（SQLインジェクション対策）
        $stmt = $pdo->prepare($sql);

        // 値をバインドして実行
        $stmt->bindValue(':target_date', $target_date, PDO::PARAM_STR);
        $stmt->bindValue(':plan', $plan, PDO::PARAM_STR);
        $stmt->bindValue(':plan_update', $plan, PDO::PARAM_STR);
        $stmt->execute();

        // フォーム再送信（F5での二重投稿）を防ぐために自分自身にリダイレクト
        header('Location: ', $_SERVER['PHP_SELF']);
        exit;
    }
}

// 前月・次月リンクが押された場合は、GETパラメーターから年月を取得
if(isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    $ym = date('Y-m');
}

// タイムスタンプを作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// 今日の日付
$today = date('Y-m-j');

// カレンダーのタイトルを作成
$html_title = date('Y年n月', $timestamp);

// 前月・次月の年月を取得
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

// 該当月の日数を取得
$day_count = date('t', $timestamp);

// １日が何曜日か
$youbi = date('w', $timestamp);

// カレンダー作成の準備
$week = [];
$week = '';

// 第一週目：空のセルを追加
$week .= str_repeat('<td></td>', $youbi);

for($day = 1; $day <= $day_count; $day++, $youbi++) {
    $date = sprintf('%s-%02d', $ym, $day);

    // 今日の日付の場合、背景色をつける
    if($today == $date) {
        $week .= '<td class="today">';
    } else {
        $week .= '<td>';
    }

    // 予定の有無でリンク先を切り替える
    if(isset($schedules[$date])) {
        //　詳細画面へ遷移するリンク　＋　予定バッジ
        $plan_text = htmlspecialchars($schedules[$date], ENT_QUOTES, 'UTF-8');
        $week .= '<a href="detail.php?date=' . $date . '" class="day-bumber">' . $day . '</a>';
        $week .= '<a href="detail.php?date=' . $date . '">';
        $week .= '<div class="badge bg-primary d-block mt-1"' . $plan_text . '</dvi>';
        $week .= '</a>';
    } else {
        // 追加画面へ遷移するリンク
        $week .= '<a href="add.php?date=' . $date . '" class="day-number d-block h-100">' . $day . '</a>';  
    }

    $week .= '</td>';

    // 週替わり、または月終わりの場合
    if($youbi % 7 == 6 || $day == $day_count) {
        if($day == $day_count) {
            // 月の最終日の場合、空セルを追加
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
        }
        $weeks[] = '<tr>' . $week . '</tr>';
        $week = '';
    }
}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>来所予定表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        a {
            text-decoration: none;
        }
        th {
            height: 30px;
            text-align: center;
        }
        td {
            height: 100px;
        }
        td a {
            color: black;
        }
        .today {
            background: orange !important;
        }
        th:nth-of-type(1), td:nth-of-type(1) a {
            color: red;
        }
        th:nth-of-type(7), td:nth-of-type(7) a {
            color: blue;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4"><a href="?ym=<?= $prev ?>">&lt;</a><span class="mx-3"><?= $html_title ?></span><a href="?ym=<?= $next ?>">&gt;</a></h3>
        <table class="table table-bordered">
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
            <?php
            foreach($weeks as $week) {
                echo $week;
            }
            ?>
        </table>
    </div>
</body>
</html>