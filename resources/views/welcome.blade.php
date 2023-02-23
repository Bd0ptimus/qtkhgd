<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
          integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="container">
        <div class="row m-0 mb-2">
            <div class="col-12">
                <a href="{{route('export.test1')}}" target="_blank">Export file docx using docx template (<span class="text-danger">public/exports/temp/temp1.docx</span>)</a> <br>
                <a href="{{route('export.test2')}}" target="_blank">Export file docx using HTML template (<span class="text-danger">resources/view/templates/exports/words/temp.blade.php</span>)</a> <br>
                <a href="{{route('export.view-template')}}" target="_blank">Export - View HTML template (<span class="text-danger">resources/view/templates/exports/words/temp.blade.php</span>)</a> <br>
                <a href="{{route('import.file-exist')}}" target="_blank">Export file html from <span class="text-danger">public/imports/inport-word.docx</span></a> <br>
            </div>
        </div>
        <div class="container">
            <div class="card card-primary">
                <div class="card-header">
                    <h2>Laravel File Upload Form</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <input type="file" name="file" class="form-control"/>
                                @if ($errors->has('file'))
                                    <p class="alert alert-danger mt-2">
                                        {{$errors->first('file')}}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
