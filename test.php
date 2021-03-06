<?php

require_once 'src/core.php';

if (!isAuthorized() && !isQuest()) {
    location('admin.php');
}

$allTests = glob('tests/*.json');
$testNumber = $_GET['number'];
$requiredTest = $allTests[$testNumber];

if (!isset($requiredTest) || !isset($testNumber)) {
    header('HTTP/1.0 403 Not Found');
    die;
}

$test = json_decode(file_get_contents($requiredTest), true);

if (isset($_POST['check-test'])) {

    $testname = basename($requiredTest);
    if (isAuthorized()) {
        $username = $_SESSION['user']['name'];
    }

    if (isQuest()) {
        $username = $_SESSION['quest']['username'];
    }

    $date = date("d-m-Y H:i");
    $correctAnswers = answersCounter($test)['correct'];
    $totalAnswers = answersCounter($test)['total'];
    $variables = [
        'testname' => $testname,
        'username' => $username,
        'date' => $date,
        'correctAnswers' => $correctAnswers,
        'totalAnswers' => $totalAnswers
    ];

}

if (isset($_POST['generate-picture'])) {
    include_once 'src/create-picture.php';
}


//echo $_SESSION['counter']++;

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test</title>
    <link rel="stylesheet" href="styles/test.css">
</head>
<body>

<a href="<?php echo isset($_POST['check-test']) ? $_SERVER['HTTP_REFERER'] : 'list.php' ?>"><div>&lt; Назад</div></a><br>


<?php if (isset($_GET['number']) && !isset($_POST['check-test'])): ?>
    <form method="POST">
        <h1><?php echo basename($requiredTest); ?></h1>
        <?php foreach ($test as $key => $item): ?>
            <fieldset>
                <div class="on-hidden-radio"></div>
                <input type="radio" name="answer<?php echo $key ?>" id="hidden-radio" required>
                <legend><?php echo $item['question'] ?></legend>
                <label><input type="radio" name="answer<?php echo $key ?>" value="0"><?php echo $item['answers'][0] ?>
                </label><br>
                <label><input type="radio" name="answer<?php echo $key ?>" value="1"><?php echo $item['answers'][1] ?>
                </label><br>
                <label><input type="radio" name="answer<?php echo $key ?>" value="2"><?php echo $item['answers'][2] ?>
                </label><br>
                <label><input type="radio" name="answer<?php echo $key ?>" value="3"><?php echo $item['answers'][3] ?>
                </label>
            </fieldset>
        <?php endforeach; ?>
        <input type="submit" name="check-test" value="Проверить">
    </form>
<?php endif; ?>


<?php if (isset($_POST['check-test'])): ?>
    <div class="check-test">
        <?php checkTest($test) ?>
        <p style="font-weight: bold;">Итого правильных ответов: <?php echo "$correctAnswers из $totalAnswers" ?></p>
        <h2>Ваш сертификат, <span style="font-style: italic;"><?php echo $username ?></span>: </h2>
        <form method="POST">
            <input type="submit" name="generate-picture" value="Сгенерировать">
            <?php foreach ($variables as $key => $variable): ?>
                <input type="hidden" value="<?php echo $variable ?>" name="<?php echo $key ?>">
            <?php endforeach; ?>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
