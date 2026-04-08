<?php
$data = ['id' => '5', 'observaciones' => 'Test'];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents('http://localhost/sigmade_php/CRUD/finalizarPrestamo.php', false, $context);
if ($result === FALSE) { /* Handle error */ echo "HTTP Request failed"; }
echo "Result:\n" . $result;
?>
