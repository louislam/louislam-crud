<!DOCTYPE html>
<html>
<head>
    <title><?=$this->e($title)?> | Louis CRUD</title>
    <?=$this->insert("raw::css") ?>

</head>
<body>

<?=$this->section('content')?>

<?=$this->insert('raw::scripts')?>
</body>
</html>