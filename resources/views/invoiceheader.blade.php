<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8" />
  
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<style>


    
    
        .header-img {
            width: 100%;
            margin-bottom: 50px;
            height: auto;
        }

     
 
        .container {
            width: 100%;
            min-height: 100%;
            position: relative;
        }

    </style>
   
</head>

<body class="font-sans antialiased" style="border-top:0 !important">
    <div class="container">
        <div class="header">
            <img src="{{ public_path('storage/images/invoice-header.png') }}" class="header-img" />
            <h2 class="title">Invoice</h2>
        </div>
    
       
    </div>         
            
        
   
</body>

</html>