<?php
// Include database connection
include('../database/db.php');
include('../global.php');

// Prepare and execute the query to retrieve doctors' data
$query = "SELECT doctors.id, doctors.category, doctors.patients, doctors.experience, doctors.bio_data, doctors.status,doctors.time,doctors.display_image, users.name, users.email
          FROM doctors
          INNER JOIN users ON doctors.user_id = users.id";

$result = $conn->query($query);

// Check if there are any doctors found
if ($result->num_rows > 0) {
    $doctors = array();
    while ($row = $result->fetch_assoc()) {
        // Add doctor data to the array
        $doctor = array(
            'id' => $row['id'],
            'category' => $row['category'],
            'patients' => $row['patients'],
            'experience' => $row['experience'],
            'bio_data' => $row['bio_data'],
            'status' => $row['status'],
            'name' => $row['name'],
            'email' => $row['email'],
            'display_image' => $img_base . $row['display_image'],
            'time' => $row['time'],
        );
        $doctors[] = $doctor;
    }

    // Return doctors' data as JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $doctors
    ]);
} else {
    // No doctors found
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'No doctors found.'));
}

// Close the database connection
$conn->close();
