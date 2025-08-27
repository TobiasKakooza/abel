<?php
// forms/contact.php

// Serve only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  header('Content-Type: text/plain; charset=utf-8');
  exit('Only POST allowed');
}

header('Content-Type: text/plain; charset=utf-8');

// Helper
function clean($v) {
  return trim((string)$v);
}

// Collect & validate input
$name    = clean($_POST['name'] ?? '');
$email   = clean($_POST['email'] ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = clean($_POST['message'] ?? '');

if ($name === '' || $subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(422);
  exit('Please fill in all fields correctly.');
}

// ---- CONFIGURE THIS ----
$to        = 'tobias.cc@isbatuniversity.com';      // recipient
$fromEmail = 'noreply@yourdomain.com';             // a mailbox on YOUR domain (improves deliverability)
$fromName  = 'Portfolio Website';
// ------------------------

// Build email
$ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$body = "From: {$name}\nEmail: {$email}\nIP: {$ip}\n\nMessage:\n{$message}\n";

$headers   = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=utf-8';
$headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
$headers[] = 'X-Mailer: PHP/' . phpversion();

// Try native mail(). If your host disables it, enable SMTP instead (see notes below).
$ok = @mail($to, ($subject ?: 'New website message'), $body, implode("\r\n", $headers));

if ($ok) {
  echo 'OK';
} else {
  http_response_code(500);
  // This exact text will appear in the red error box on the page:
  echo 'Email failed to send (mail() not available). Ask your host for SMTP settings or use a service like SendGrid/Mailgun.';
}
