<?php
// Функция для загрузки переменных из .env файла
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Пропуск комментариев
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Разделение по знаку =
        list($name, $value) = explode('=', $line, 2);
        
        $name = trim($name);
        $value = trim($value);

        // Удаление кавычек, если они есть
        if (preg_match('/^(\'|").*(\'|")$/', $value)) {
            $value = substr($value, 1, -1);
        }

        // Установка переменной окружения
        if (!getenv($name)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}
// Загружаем файл из корня проекта
loadEnv(__DIR__ . '/.env');
//сертификаты безопасности
//$sslOptions = array(
//  'cafile' => realpath(__DIR__ . '/isrgrootx1.pem'),
//);
//сертификаты безопасности, пустышка
$sslOptions = ["verify_peer"=>true,"verify_peer_name"=>true];
//инициализация данных
//$platok_predstav=["Артикул","Название","Автор платка","Колорит 1","Колорит 2","Колорит 3",
//"Колорит 4","Колорит 5","Узор темени","Узор сердцевины","Узор сторон","Узор углов","Узор краёв",
//"Соотношение рисунка и орнамента","Нарисованный цветок 1","Нарисованный цветок 2","Нарисованный цветок 3",
//"Нарисованный цветок 4","Нарисованный цветок 5","Размер платка","Материал платка","Материал бахромы"];
//$soobshenije0="Феодосия сообщает, добавлен новый платок";

//$artikul=""; $nazvanije=""; $avtor=""; $kolorit_1=""; $kolorit_2=""; $kolorit_3=""; $kolorit_4=""; $kolorit_5="";
//$uzor_tenemi=""; $uzor_serdcevina=""; $uzor_storon=""; $uzor_uglov=""; $uzor_kraja=""; $uzor_cvety_ornament="";
//$cvetok_1=""; $cvetok_2=""; $cvetok_3=""; $cvetok_4=""; $cvetok_5=""; $razmer_platka=""; $material_platka="";
//$material_bahromi="";
// 1. Самодельный PSR-4 автозагрузчик взамен composer autoload.php
spl_autoload_register(function ($class) {
    // Префикс пространства имен библиотеки
    $prefix = 'PhpAmqpLib\\';
    
    // Базовая директория для этого префикса
    $base_dir = __DIR__ . '/PhpAmqpLib/';

    // Проверяем, использует ли класс этот префикс

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // Если нет, передаем управление следующему автозагрузчику
    }

    // Получаем относительное имя класса
    $relative_class = substr($class, $len);

    // Заменяем разделители пространства имен на разделители директорий
    // и добавляем расширение .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Если файл существует, подключаем его
    if (file_exists($file)) {
        require_once $file;
    }
});
//обход отсуствия mbstlengh
function mb_substr($str, $start, $length = null, $encoding = 'UTF-8') {
    // Разбиваем строку на массив символов (учитывая многобайтовые кодировки)
    $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    
    // Получаем нужный срез
    $sliced_chars = array_slice($chars, $start, $length);
    
    // Собираем обратно в строку
    return implode('', $sliced_chars);
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($str, $encoding = null) {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return iconv_strlen($str, $encoding);
    }
}
//новый заяц, работает!!!
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Wire\AMQPWriter;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;
$host_rabbit=getenv("STACKHERO_RABBITMQ_HOST");
$port_rabbit=getenv("STACKHERO_RABBITMQ_AMQP_PORT_TLS");
$password_rabbit=getenv("STACKHERO_RABBITMQ_PASSWORD");
$user_rabbit=getenv("STACKHERO_RABBITMQ_USER");
//$connection_rabbit_new = new AMQPSSLConnection($host_rabbit, $port_rabbit, $user_rabbit, $password_rabbit, $sslOptions);
$rabbit_host=getenv('RABBITHOST');
$rabbit_port=getenv('RABBITPORT');
$rabbit_username=getenv('RABBITUSERNAME');
$rabbit_password=getenv('RABBITPASSWORD');
$rabbit_virtual_engine=getenv('RABBITVIRTUALENGINE');
//подключение к брокеру
$connection_rabbit = new AMQPSSLConnection($rabbit_host, $rabbit_port,$rabbit_username, $rabbit_password,$rabbit_virtual_engine,$sslOptions);
//$rabbit_connect=new AMQPStreamConnection($rabbit_host,$rabbit_port,$rabbit_username,$rabbit_password,$rabbit_virtual_engine,$insist = false,
  //  $login_method = 'AMQPLAIN',
    //$locale = null,
    //$connection_timeout = 10.0,
    //$read_write_timeout = 10.0,
    //$context = null,
    //$keepalive = false, // <-- Включите этот параметр
    //$heartbeat = 0);
    //} 
    //catch (Exception $e) {
    //echo 'Caught broker exception: ',  $e->getMessage(), "\n";
    //}
$soobshenije="PRIVEEET!!!";
$channel = $connection_rabbit->channel();
//Объявление очереди (убеждаемся, что она существует)
$channel->queue_declare('platoky_queue', false, false, false, false);
//Создание сообщения
$data =$soobshenije;
$msg = new AMQPMessage($data);
//Отправка сообщения
$channel->basic_publish($msg, '', 'platoky_queue');
echo "Сообщение отправлено!";
//Закрытие соединения
$channel->close();
$connection_rabbit->close();
?>