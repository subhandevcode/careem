<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Find Mosques</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      overflow: hidden; /* prevents scroll */
      background-color: #5e0f8f;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      padding: 20px;
    }

    .container {
      text-align: center;
      max-width: 400px;
      width: 100%;
    }

    .icon-box {
      margin-bottom: 10px;
    }

    .icon-box img {
      width: 300px;
      max-width: 100%;
      display: block;
      margin: 0 auto;
    }

    h1 {
      font-size: 24px;
      font-weight: bold;
      margin: 15px 0 10px;
      line-height: 1.4;
      color: white;
    }

    p {
      font-size: 14px;
      color: rgba(255, 255, 255, 0.8);
      margin-bottom: 20px;
      padding: 0 10px;
    }

    .dots {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-bottom: 20px;
    }

    .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.4);
    }

    .dot.active {
      background-color: white;
    }

    .btn {
      background-color: white;
      color: #6C2BD9;
      font-weight: bold;
      padding: 12px 35px;
      border-radius: 999px;
      border: none;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s ease;
       text-decoration: none;
    }

    .btn:hover {
      background-color: #f0f0f0;
    }

    @media (min-width: 600px) {
      h1 {
        font-size: 28px;
      }

      p {
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <!-- Icon Area -->
      <div class="icon-box">
        <img src="./images/logo.png" alt="Logo" />
      </div>

      <!-- Title and Description -->
      <h1>Find Friends<br>easier anywhere</h1>
      <p>Discover nearby Users with ease using location-based apps and websites, anytime, anywhere.</p>

      <!-- Dots Indicator -->
      <div class="dots">
        <div class="dot active"></div>  
        <div class="dot"></div>
        <div class="dot"></div>
      </div>

      <!-- Get Started Button -->
      <a href="{{route('register')}}" class="btn">Register Now</a>
      <a href="{{route('login')}}" class="btn">Login Now</a>
    </div>
  </div>
</body>
</html>