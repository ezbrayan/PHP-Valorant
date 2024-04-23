<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
         .puntos {
            width: 100%;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;

        }

        .confort {
            width: 30%;
            height: 100%;
            padding: 5px;
        }

        .confort img {
            width: 100%;
            height: 100%;

        }

        .elementos {

            width: 25%;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0px;
            padding: 0px;


        }

        .elementos img {
            width: 35%;
            height: auto;
        }

        .elementos a {
            font-size: 10px;
            color: white;
            text-decoration: none;
        }

        .elementos a:hover {
            font-size: 10px;
            color: white;
            text-decoration: none;
        }


        .confi {
            width: 5%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: -45%;
        }

        .confi img {
            width: 60%;
            height: 40%;
            margin-left: 40%;
            /* Coloca la imagen a la derecha del contenedor */
        }
    </style>
</head>
<body>
<div class="puntos">
        <div class="confort">
            <img src="../img/confrontacion.png" alt="">

        </div>
        <div class="elementos">
            <a href=""><img src="../svg/radioactivo.svg" frameborder="0" scrolling="no"></img> 0/4</a>
            <a href=""><img src="../svg/valorant.svg" frameborder="0" scrolling="no"></img> 3000</a>
            <a href=""><img src="../svg/carta2.svg" frameborder="0" scrolling="no"></img></a>
            <a href=""><img src="../svg/radianita.svg" frameborder="0" scrolling="no"></img> 215</a>
            <a href=""><img src="../svg/kingdom.svg" frameborder="0" scrolling="no"></img> 8000</a>
        </div>
        <div class="confi">
            <a href=""><img src="../svg/confi.svg" frameborder="0" scrolling="no"></img></a>
        </div>
    </div>
</body>
</html>