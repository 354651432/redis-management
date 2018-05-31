<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css"/>
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>
    <script src="https://cdn.bootcss.com/axios/0.18.0/axios.min.js"></script>

    <style>
        dl {
            margin-left: 20px;
        }

        dt {
            margin-left: 20px;
        }
    </style>
</head>
<body>
<div class="">
    <br>
    <div class="col-sm-4">
        <div id="tree">tree loading....</div>
    </div>
    <div class="col-sm-8">
        <div class="form-group">
            <label class="pull-left control-label">key: <i style="display:inline-block;width:20px;"></i></label>
            <input id="key" class="col-sm-10">
        </div>
        <br>
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" id="cmd" placeholder="redis raw command">
                <span class="input-group-btn">
                    <button class="btn btn-primary" id="cmdBtn">run</button>
                </span>
            </div>
        </div>
        <br>
        <br>
        <pre id="json"></pre>
    </div>
</div>

<script>
    $.get("/redis/treeKeys/*", function (treeData) {
        $('#tree').treeview({data: treeData.nodes}).on("nodeSelected", function (event, data) {
            if (data.type == "l" && data.text) {
                $("#json").text("loading......");
                $("#key").val(data.key);

                $.get("/redis/get/" + data.key, function (data) {
                    $("#json").text(data)
                }, "text")
            }
        })
    });

    $('#cmdBtn').click(function () {
        var text = $("#cmd").val();
        $("#json").text("loading......");
        $.get("/redis/raw/" + text, function (data) {
            data = data.replace(/\\r/g, "\r")
                .replace(/\\n/g, "\n");
            $("#json").text(data)
        }, "text")
    });

</script>
</body>
</html>