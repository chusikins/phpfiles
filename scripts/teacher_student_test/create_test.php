<?php
require_once '../db/database_connection.php';
$upload_dir = "D:/xammp/htdocs/myPHP/" . "uploads/test_images/";
$upload_dir_temp = "D:/xammp/htdocs/myPHP/upload";

$php_error = array(1 => 'Превышен макс. размер файла, указанный в php.ini',
2 => 'Превышен макс. размер файла, указанный в форме HTML',
3 => 'Была отправлена только часть файла',
4 => 'Файл для отправки не был выбран.');

$image_number = trim($_REQUEST['images_number']);
$exercise_text = trim($_REQUEST['exercise_text']);
$sub_answers_amount = trim($_REQUEST['sub_answers_amount']);

$answer_key = array();
$sub_answer_text = array();
for ($i=0; $i<$sub_answers_amount; $i++){
  array_push($answer_key, $_REQUEST['member' . $i] );
  array_push($sub_answer_text, $_REQUEST['sub_text' . $i] );
}
$insert_exercise_sql = sprintf("INSERT INTO exercise ".
"(exercise_text, sub_answers_amount, image_amount)".
"VALUES ('%s', %d, %d);", $exercise_text, $sub_answers_amount, $image_number);
mysqli_query($link, $insert_exercise_sql) or die ("Not insert in DB " .mysqli_error($link));
$ex_id = mysqli_insert_id($link);

for ($i=0; $i<$sub_answers_amount; $i++){
  $update_answer_sql = sprintf("INSERT INTO sub_answers " .
  "(question_id, sub_answer_id, sub_answer, sub_answers_text)" . "VALUES(%d,%d,'%s','%s')", $ex_id, $i+1, $answer_key[$i], $sub_answer_text[$i]);
  mysqli_query($link,$update_answer_sql) or die ("Not insert in DD" .mysqli_error($link));
}

//проверка отсутвия ошибки при отправке изображения



for ($i=0; $i<$image_number; $i++){
  $image_fieldname="image".$i;

  ($_FILES[$image_fieldname]['error'] == 0)
   or handle_error("сервер не может получить выбранное вами изображение.",
  $php_error[$_FILES [$image_fieldname]['error']]);

  @getimagesize($_FILES[$image_fieldname]['tmp_name'])
  or handle_error("вы выбрали файл для своего фото," .
  "which is not an image.",
  "{$_FILES[$image_fieldname]['tmp_name']}" .
  " не явл файлом изображения");

  $now = time();
  while (file_exists($upload_filename = $upload_dir_temp . "/" . $now .
  '-' .
  $_FILES[$image_fieldname]['name'])){
    $now++;
  }

  $image = $_FILES[$image_fieldname];
  $image_filename = $image['name'];
  $image_info = getimagesize($image['tmp_name']);
  $image_mime_type = $image_info['mime'];
  $image_size = $image['size'];
  $image_data = file_get_contents($image['tmp_name']);
  $insert_image_sql = sprintf("INSERT INTO images " .
  "(ex_id, pic_ex_id, mime_type," .
  "filesize, imagedata, filename)" .
  "VALUES (%d, %d, '%s', %d, '%s','%s');",
  mysqli_real_escape_string($link, $ex_id),
  mysqli_real_escape_string($link, $i),
  mysqli_real_escape_string($link, $image_mime_type),
  mysqli_real_escape_string($link, $image_size),
  mysqli_real_escape_string($link, $image_data),
  mysqli_real_escape_string($link, $image_filename));
  mysqli_query($link, $insert_image_sql);

}
//
// for ($i=0; $i<$images_number, $i++){
//   $insert_image_sql = sprintf("INSERT INTO images " .
//   "(ex_id, pic_ex_id, mime_type," .
//   "filesize, imagedata, filename)" .
//   "VALUES (%d, %d, '%s', %d, '%s','%s');",
//   mysqli_real_escape_string($link, $ex_id),
//   mysqli_real_escape_string($link, $i),
//   mysqli_real_escape_string($link, $image_mime_type),
//   mysqli_real_escape_string($link, $image_size),
//   mysqli_real_escape_string($link, $image_data),
//   mysqli_real_escape_string($link, $image_filename));
//   mysqli_query($link, $insert_image_sql);
//
// }
// $insert_image_sql = sprintf("INSERT INTO images " .
// "(ex_id, pic_ex_id, mime_type," .
// "filesize, imagedata, filename)" .
// "VALUES (%d, %d, '%s', %d, '%s','%s');",
// mysqli_real_escape_string($link, $ex_id),
// mysqli_real_escape_string($link, $i),
// mysqli_real_escape_string($link, $image_mime_type),
// mysqli_real_escape_string($link, $image_size),
// mysqli_real_escape_string($link, $image_data),
// mysqli_real_escape_string($link, $image_filename));
// mysqli_query($link, $insert_image_sql);

 //
  header("Location: show_test.php?ex_id=" . $ex_id)
 ?>
