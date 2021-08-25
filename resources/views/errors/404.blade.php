<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title> Page not found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

</head>

<body>
    <div class="container mt-5 pt-5">
        <div class="alert alert-info text-center">
            <h1 class="display-1"><b>404</b></h1>
            <p class="display-5">Oops! Something is wrong.</p>
            <a class="btn btn-sm btn-info" href="{{route('dashboard')}}">Back To Home</a>
        </div>
    </div>
</body>

</html>
