<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Title</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    </head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light ">
        <a class="navbar-brand">Biblioteca</a>
        <button class="navbar-toggler" data-target="#my-nav" data-toggle="collapse" aria-controls="my-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="my-nav" class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="<?= base_url('listar') ?>">libros</span></a>
                </li>
                
            </ul>
        </div>
    </nav>
    
 <div class="container">
     <?php if (session('mensaje')): ?>
    <div class="alert alert-danger" role="alert">
        <?php
        echo session('mensaje');
        ?>
    </div>
    <?php
        endif;
    ?>