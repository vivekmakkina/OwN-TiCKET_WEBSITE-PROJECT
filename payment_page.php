<?php
session_start();
include('db_connection.php');

// Include PHPMailer classes
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

// Include Twilio SDK
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

// Check if the necessary data is passed
if (!isset($_POST['movieId'], $_POST['selectedSeats'], $_POST['email'], $_POST['mobile'])) {
    die("Invalid request.");
}

$movie_id = $_POST['movieId'];
$selectedSeats = explode(',', $_POST['selectedSeats']);
$email = $_POST['email'];
$mobile = $_POST['mobile'];

// Get movie details (corrected poster to poster_image)
$movie_query = "SELECT id, title, genre, show_time, poster_image FROM movies WHERE id = ?";
$stmt = $conn->prepare($movie_query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie_result = $stmt->get_result();
$movie = $movie_result->fetch_assoc();

if (!$movie) {
    echo "Movie not found!";
    exit();
}

// Process payment (simulate)
$payment_success = true;

if ($payment_success) {
    $ticket_file = generate_ticket($movie, $selectedSeats, $email, $mobile);

    send_email($email, $ticket_file);
    send_whatsapp($mobile);

    echo "Payment Successful! Ticket has been sent to your email and WhatsApp.";
} else {
    echo "Payment failed, please try again.";
}

// ==========================
// Generate PDF Ticket
// ==========================
function generate_ticket($movie, $seats, $email, $mobile) {
    require_once('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    // Set Colors
    $headerColor = [0, 102, 204];    // Nice Blue
    $textColor = [50, 50, 50];       // Dark Gray for text
    $borderColor = [220, 220, 220];  // Light Gray borders

    // Add Header
    $pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
    $pdf->Rect(0, 0, 210, 30, 'F');
    $pdf->SetTextColor(255, 255, 255); // White Text
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 30, "Movie Ticket", 0, 1, 'C');

    $pdf->Ln(5);

    // Reset text color
    $pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);

    // Movie poster (smaller size)
    if (!empty($movie['poster_image'])) {
        $posterPath = $movie['poster_image'];

        if (!file_exists($posterPath)) {
            $posterPath = __DIR__ . '/' . $posterPath;
        }

        if (file_exists($posterPath)) {
            $pdf->Image($posterPath, 55, 40, 100); // smaller size
            $pdf->Ln(60); // Added line break after the image to ensure space for text
        } else {
            $pdf->Cell(0, 10, "Poster image not found.", 0, 1, 'C');
            $pdf->Ln(10);
        }
    } else {
        $pdf->Cell(0, 10, "No poster available.", 0, 1, 'C');
        $pdf->Ln(10);
    }

    // Movie Details
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "Movie: " . $movie['title'], 0, 1, 'C');

    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 10, "Genre: " . $movie['genre'], 0, 1, 'C');
    $pdf->Cell(0, 10, "Show Date: " . date('d-m-Y'), 0, 1, 'C');
    $pdf->Cell(0, 10, "Show Time: " . $movie['show_time'], 0, 1, 'C');

    $pdf->Ln(5);

    // Your Seats
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "Your Seats", 0, 1, 'C');

    $pdf->SetFont('Arial', '', 14);
    $pdf->SetFillColor($borderColor[0], $borderColor[1], $borderColor[2]);
    $pdf->MultiCell(0, 10, implode(', ', $seats), 1, 'C', true);

    $pdf->Ln(10);

    // Payment Details
    $price_per_seat = 200;
    $seat_count = count($seats);
    $total_price = $price_per_seat * $seat_count;
    $gst = $total_price * 0.18;
    $final_amount = $total_price + $gst;

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Payment Details", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);

    $pdf->SetFillColor(240, 248, 255); // Light bluish background
    $pdf->Cell(0, 10, "Price per Seat: ₹" . number_format($price_per_seat, 2), 0, 1, 'C', true);
    $pdf->Cell(0, 10, "Subtotal: ₹" . number_format($total_price, 2), 0, 1, 'C', true);
    $pdf->Cell(0, 10, "GST (18%): ₹" . number_format($gst, 2), 0, 1, 'C', true);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Total Amount: ₹" . number_format($final_amount, 2), 0, 1, 'C', true);

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, "Thank you for booking with us!", 0, 1, 'C');

    // Save PDF
    if (!file_exists('tickets')) {
        mkdir('tickets', 0777, true);
    }

    $ticket_file = 'tickets/' . time() . '.pdf';
    $pdf->Output('F', $ticket_file);

    return $ticket_file;
}

// ==========================
// Send Email with Ticket
// ==========================
function send_email($email, $ticket_file) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vivekmakkina3@gmail.com';
        $mail->Password = 'tuleklxgazcdpbie';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('vivekmakkina3@gmail.com', 'Movie Ticket Booking');
        $mail->addAddress($email);

        $mail->Subject = 'Your Movie Ticket';
        $mail->Body    = 'Here is your movie ticket. Enjoy your show!';
        $mail->addAttachment($ticket_file);

        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}

// ==========================
// Send WhatsApp with Ticket
// ==========================
function send_whatsapp($mobile) {
    // Your Twilio Account SID and Auth Token
    $sid = 'ACeba2f414a6d92e78f30b07260e7e549e';
    $token = 'a37dceeed9114cab2c1afb419d5086d5';
    $client = new Client($sid, $token);

    // Send a WhatsApp message
    $message = $client->messages->create(
        'whatsapp:' . $mobile, // WhatsApp number to send to
        [
            'from' => 'whatsapp:+14155238886', // Your Twilio WhatsApp sandbox number (or your Twilio WhatsApp number if you've upgraded)
            'body' => 'Thank you for booking your ticket at OwN TiCKETS.com!'
        ]
    );

    echo "Ticket sent via WhatsApp!";
}
?>
