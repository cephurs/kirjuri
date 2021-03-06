<?php
require_once("./include_functions.php");
$confCrimes = file_get_contents('conf/crimes_autofill.conf');
$kirjuri_database = db('kirjuri-database');
$query = $kirjuri_database->prepare('SELECT * FROM exam_requests WHERE id=:id AND parent_id=:id LIMIT 1');
$query->execute(array(
    ':id' => $_GET['case']
));
$caserow = $query->fetchAll(PDO::FETCH_ASSOC);
$query = $kirjuri_database->prepare('SELECT id, case_id, case_suspect, case_name, case_devicecount FROM exam_requests WHERE case_file_number=:case_file_number AND id = parent_id AND is_removed = 0 AND case_id != :case_id');
$query->execute(array(
    ':case_file_number' => $caserow[0]['case_file_number'],
    ':case_id' => $caserow[0]['case_id']
));
$samerequest_file_number = $query->fetchAll(PDO::FETCH_ASSOC);
if ($_GET['j'] === "dev_owner")
  {
    $j = "device_owner";
  }
elseif ($_GET['j'] === "dev_manuf")
  {
    $j = "device_manuf";
  }
elseif ($_GET['j'] === "dev_model")
  {
    $j = "device_model";
  }
elseif ($_GET['j'] === "id")
  {
    $j = "id";
  }
elseif ($_GET['j'] === "device_action")
  {
    $j = "device_action";
  }
elseif ($_GET['j'] === "dev_location")
  {
    $j = "device_location";
  }
elseif ($_GET['j'] === "tvp")
  {
    $j = "device_document, device_item_number";
  }
else
  {
    $j = "device_type";
  }
;
$query = $kirjuri_database->prepare('SELECT * FROM exam_requests WHERE id != :id AND parent_id=:id AND is_removed != "1" ORDER BY ' . $j);
$query->execute(array(
    ':id' => $_GET['case']
));
$mediarow = $query->fetchAll(PDO::FETCH_ASSOC);
if (!file_exists("conf/instructions_" . str_replace(" ", "_", strtolower($caserow[0]['classification'])) . ".txt"))
  {
    $instructions_text = "File conf/instructions_" . str_replace(" ", "_", strtolower($caserow[0]['classification'])) . ".txt not found.";
  }
else
  {
    $instructions_text = file_get_contents("conf/instructions_" . str_replace(" ", "_", strtolower($caserow[0]['classification'])) . ".txt");
  }
echo $twig->render('edit_request.html', array(
    'samerequest_file_number' => $samerequest_file_number,
    'dev_owner' => urldecode($_GET['dev_owner']),
    'j' => $_GET['j'],
    'sort_order' => $j,
    'returntab' => $_GET['tab'],
    'showStatus' => $_GET['showStatus'],
    'caserow' => $caserow,
    'mediarow' => $mediarow,
    'settings' => $settings,
    'device_locations' => $device_locations,
    'device_actions' => $device_actions,
    'media_objs' => $media_objs,
    'devices' => $devices,
    'forensic_investigators' => $forensic_investigators,
    'phone_investigators' => $phone_investigators,
    'inv_units' => $inv_units,
    'classifications' => $classifications,
    'confCrimes' => $confCrimes,
    'instructions_text' => $instructions_text,
    'lang' => $_SESSION['lang']
));
?>
