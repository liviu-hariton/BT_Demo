class BT_Demo {
    loadNewFile(val) {
        if(val === '0') {
            $("#file-upload").show();
        } else {
            $("#file-upload").hide();
        }
    }
}

let _bt_demo  = new BT_Demo;