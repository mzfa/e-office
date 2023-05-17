<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error 404</title>
    <style>
        /*======================
            404 page
        =======================*/


        .page_404{ 
            padding:50px 0; background:#fff; font-family: 'Arvo', serif;
            text-align: center;
        }
        h1{
            font-size: 50px;
        }

        .four_zero_four_bg{
            margin-top: 10px;
            background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
            height: 400px;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body>
    <section class="page_404">
        <div class="container">
            <div class="row">	
            <div class="col-sm-12 ">
            <div class="col-sm-10 col-sm-offs
            et-1  text-center">
            <div class="four_zero_four_bg">
                <h1 class="text-center ">404</h1>
            </div>
            
            <div class="contant_box_404">
            <h3 class="h2">
                Mohon Maaf
            </h3>
            
            <p>URL yang anda cari tidak dapat ditemukan</p>
            
            <a href="{{ url('/') }}" class="link_404">Go to Home</a>
        </div>
            </div>
            </div>
            </div>
        </div>
    </section>
</body>
</html>