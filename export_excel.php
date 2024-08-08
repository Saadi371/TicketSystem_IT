<?php
require 'vendor/autoload.php'; // Make sure this path is correct for PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column headers
$headers = ['Ticket No.', 'Name', 'Email', 'Query', 'Office', 'Priority', 'High_Priority_Description', 'Submit Date', 'Status', 'Attended By'];
$sheet->fromArray($headers, NULL, 'A1');

// Connect to the database
$mysqli = new mysqli('localhost', 'username', 'password', 'database');
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Fetch the data
$result = $mysqli->query('SELECT * FROM tickets');
$rowNum = 2; // Start from the second row

while ($row = $result->fetch_assoc()) {
    $sheet->fromArray(array_values($row), NULL, 'A' . $rowNum);
    $rowNum++;
}

$mysqli->close();

// Set headers to prompt download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="tickets.xlsx"');
header('Cache-Control: max-age=0');

// Write the file to the output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
