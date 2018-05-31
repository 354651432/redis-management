<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <br>
    <form class="form" method="POST">
        {{ csrf_field() }}
        <div class="form-group">
            <textarea type="text" name="data" class="form-control">{{ request("data") }}</textarea>
        </div>
        <div class="form-group">
            <label><input type="checkbox" {{ request("isJosonPretty") ? "checked" : "" }} name="isJosonPretty"
                          value="1"> json pretty</label>
        </div>
        <input type="submit" value="decrypt" name="method" class="btn btn-info">
        <input type="submit" value="encrypt" name="method" class="btn btn-danger">
    </form>
    <br>
    <pre class="well"><textarea class="form-control" rows="20" readonly>{{ $data }}</textarea></pre>
</div>
</body>
</html>