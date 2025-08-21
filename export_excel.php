 <?php
$conn = new mysqli("localhost", "root", "", "eas_db");

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=appraisals.csv');

$output = fopen("php://output", "w");

// Column headers
fputcsv($output, ['Employee', 'Evaluator', 'Date', 'Comments', 'KPI', 'Rating']);

// Get all appraisals
$appraisals = $conn->query("
    SELECT a.appraisal_id, a.appraisal_date, a.comments,
           eu.name AS employee_name,
           u.name AS evaluator_name
    FROM appraisals a
    JOIN employees e ON a.employee_id = e.employee_id
    JOIN users eu ON e.user_id = eu.user_id
    JOIN users u ON a.evaluator_id = u.user_id
");

while ($row = $appraisals->fetch_assoc()) {
    // Get scores for each appraisal
    $scores = $conn->query("
        SELECT s.rating, k.kpi_name
        FROM scores s
        JOIN kpis k ON s.kpi_id = k.kpi_id
        WHERE s.appraisal_id = {$row['appraisal_id']}
    ");

    while ($score = $scores->fetch_assoc()) {
        fputcsv($output, [
            $row['employee_name'],
            $row['evaluator_name'],
            $row['appraisal_date'],
            $row['comments'],
            $score['kpi_name'],
            $score['rating']
        ]);
    }
}

fclose($output);
?>
