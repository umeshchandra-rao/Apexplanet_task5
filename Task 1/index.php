<?php
// Simple PHP check
echo "<!-- PHP is working -->";
date_default_timezone_set('Asia/Kolkata'); // Set your timezone if needed
$currentDateTime = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ApexPlanet Internship</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            background: #f9fafc;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .header {
            background: linear-gradient(90deg, #7f53ac 0%, #647dee 100%);
            color: #fff;
            padding: 48px 0 18px 0;
            text-align: center;
            box-shadow: 0 4px 24px rgba(100,125,222,0.10);
        }
        .header h1 {
            margin: 0;
            font-size: 2.7em;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .header h2 {
            margin: 22px 0 0 0;
            font-size: 1.35em;
            font-weight: 400;
            letter-spacing: 1px;
        }
        .content {
            text-align: center;
            margin: 60px 0 0 0;
        }
        .success {
            display: inline-block;
            background: #fff;
            border-radius: 18px;
            padding: 38px 48px;
            box-shadow: 0 6px 32px rgba(100,125,222,0.10);
            font-size: 1.18em;
            border: 2px solid #e0e7ff;
            transition: box-shadow 0.3s;
        }
        .success:hover {
            box-shadow: 0 12px 40px rgba(100,125,222,0.18);
        }
        .success .check {
            color: #ffb300;
            font-size: 2em;
            vertical-align: middle;
            margin-right: 8px;
        }
        .footer {
            background: linear-gradient(90deg, #647dee 0%, #7f53ac 100%);
            color: #fff;
            text-align: center;
            padding: 18px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
            font-size: 1.08em;
            letter-spacing: 1px;
            box-shadow: 0 -2px 16px rgba(100,125,222,0.08);
        }
        @media (max-width: 600px) {
            .success {
                padding: 24px 8px;
                font-size: 1em;
            }
            .header h1 {
                font-size: 1.5em;
            }
            .header h2 {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ApexPlanet Internship</h1>
        <h2>Task 1 : Setting Up the Development Environment</h2>
    </div>
    <div class="content">
        <div class="success">
            <span class="check">&#x2728;</span>
            Congratulations! Your PHP environment is working correctly.<br><br>
            <span style="color:#647dee;font-weight:500;">Current Server Date &amp; Time:</span>
            <span style="color:#7f53ac;"><?php echo $currentDateTime; ?></span>
        </div>
    </div>
    <div class="footer">
        &copy; 2025 ApexPlanet Software Pvt Ltd | Internship Program
    </div>
</body>
</html>