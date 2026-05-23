<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        *{
            padding:0;
            margin:0;
            box-sizing:border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        body{
            background-color: bisque;
            display:flex;
            justify-content: center;
            align-items: center;
            height:100vh;
            width:100%;
        }
        .form {
            box-shadow: 0 0 1px rgba(0,0,0,0.5);
            padding:10px;
            display:flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .form > div {
            display:flex;
            flex-direction: column;
            gap:5px;
            width:400px;
        }
        .form > div label {
            font-weight: 600;
            font-size:1rem;
        }
        .form button {
            width:100%;
            margin-top:10px;
            font-size:16px;
            background-color:blue;
            color:white;
            font-weight: bold;
            border:0;
            cursor: pointer;

            padding:7px 5px;
        }
        .form button:hover{
            background-color: rgb(5, 5, 218);
        }
        input {
            padding:5px;
            border:1px solid rgba(128, 128, 128, 0.514);
            border-radius: 5px;
        }
        .output {
            font-weight: 600;
            margin-top:15px;

        }
        ul {
           margin-top:10px;
        }
        ul li{
           list-style: none;
           color:red;
           font-weight: 300;
            font-style:italic;
            text-align: center;
        }
    </style>
</head>
<body>
    <div>
        <form class="form" action={{ url('/convert') }} method="POST">
            @csrf
            <div>
                <label>Amount In Peso</label>
                <input type="text" name="amount" placeholder="Input amount"/>
            </div>
            <div style="display: flex;flex-direction:row; align-items:center;gap:5px;width:100%; margin-top:5px;">
                <label>Convert to words</label>
                <input type="checkbox" name="toString" value="1"/>
            </div>
            <button type="submit">
                Convert
            </button>
            @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </form>
        @if(session('amount'))
            <p class="output" style="text-align: center;">
               USD {{ session('amount') }}
            </p>
        @endif
    </div>
</body>
</html>
