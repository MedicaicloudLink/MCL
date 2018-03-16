
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
    /**
     * .GatewayWorker..websocket...................
     * .....Gateway....start_gateway.php......
     * start_gateway.php .....websocket......
     * $gateway = new Gateway(websocket://0.0.0.0:7272);
     */

    ws = new WebSocket("ws://106.75.32.143:8282");
    var userid = sessionStorage.getItem("userid");
    console.log(ws);
    // ................onmessagea
    ws.onmessage = function(e){
        console.log(e);
        // json.....js..
        console.log(e.data);
        // var data = eval("("+e.data+")");
        var data = JSON.parse(e.data);
        console.log(data.type);
        var type = data.type || '';
        switch(type){

            // Events.php....init.......client_id......uid..
            case 'init':
                // ..jquery..ajax....client_id......uid..
                $.post('test/bind', {client_id: data.client_id,userid: userid}, function(data){}, 'json');
                console.log('okok');
                alert(e.data);

                break;
            // .mvc....GatewayClient......alert..
            default :
                alert(e.data);
        }
    };
</script>
