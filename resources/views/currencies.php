<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<table>
    <tbody>
    <?php
    $bg = 'background-color:#ffdead';
    $bg2 = 'background-color:#98fb98';

    echo '<table>';
    foreach ($currencies as $key=>$cur) {
        $style = $key%2 ? $bg : $bg2;
        echo "<tr style='" . $style . "'>";

        echo "<td>{$cur->name}</td>";
        echo "<td>{$cur->short_name}</td>";
        echo "<td>{$cur->value}</td>";
        echo "<td>{$cur->date}</td>";

        echo '</tr>';
    }
    echo '</table>';
    ?>
    </tbody>
</table>

</body>
</html>



