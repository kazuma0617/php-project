<?php
// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

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
    $date = $ym . '-' . $day;
    if($today == $date) {
        $week .= '<td class="today">' . $day;
    } else {
        $week .= '<td>' . $day;
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
    <title>PHPカレンダー</title>
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
        .today {
            background: orange !important;
        }
        th:nth-of-type(1), td:nth-of-type(1) {
            color: red;
        }
        th:nth-of-type(7), td:nth-of-type(7) {
            color: blue;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4"><a href="?ym=<?= $prev ?>">&lt;</a><span class="mx-3"><?= $html_title ?></span><a href="?ym=<?= $next ?>">&gt</a></h3>
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