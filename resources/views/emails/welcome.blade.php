<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BOXLEO COURIER SERVICES</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Hi {{ $name }},</h1>
    
    <p>You have been added as an administrator at BOXLEO COURIER SERVICES.</p>
    <p>Please use the following credentials to login:</p>
    
    <p><strong>Email address:</strong> {{ $email }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>
    
    <p><a href="http://boxleotransport.solssa.com" class="button">Go To Dashboard</a></p>
    
    <p>Good luck! We are glad to have you onboard.</p>
    
    <div class="footer">
        Boxleo Courier & Fulfillment Services Ltd.<br>
        Akshrap Godowns Gate A-2, JKIA Junction, Kenya
    </div>
</body>
</html>