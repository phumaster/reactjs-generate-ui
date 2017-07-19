<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');
require __DIR__.'/vendor/autoload.php';

$formRaw = file_get_contents('schema/forms.json');
$forms = json_decode($formRaw, true);

foreach ($forms as $form) {
  $maker = new PhuMaster\Classes\Maker($form);
  $file = fopen(__DIR__.'/render/components/'.ucfirst($maker->getFormName()).'.jsx', 'w+');
  fwrite($file, $maker->form());
  fclose($file);
}
