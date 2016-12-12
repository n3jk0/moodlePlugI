/**
 * Created by Nejko on 26. 07. 2016.
 */
define(['jquery'], function($) {
    var test_code = function () {
        $(document).ready(function () {
            form = $("input[name = 'attachments']").val();
            $('#test_code').click(function () {
                var clickBtnValue = $(this).val();
                var ajaxurl = 'tester_ajax.php',
                    data = {'action': clickBtnValue, 'form' : form};
                $.post(ajaxurl, data, function (response) {
                    // Response div goes here.
                    // alert("action performed successfully");
                });
            });

        });
    };

    var hide = function () {
        $(document).ready(function () {
            $( "#hide" ).click(function() {
                $( "#sourceCode" ).toggle();
            });
        });
    };
    
    window.toggle = function (showHideDiv) {
        var ele = document.getElementById(showHideDiv);
        if (ele.style.display == "block") {
            ele.style.display = "none";
        }
        else {
            ele.style.display = "block";
        }
    };

    window.remove_file = function(file_name, course_id) {
        var ele = document.getElementById(file_name);
        ele.innerHTML = "";
        remove(file_name, course_id);
        
    };

    function remove(file_name, course_id) {
        var req = false;
        try{
            // most browsers
            req = new XMLHttpRequest();
        } catch (e){
            // IE
            try{
                req = new ActiveXObject("Msxml2.XMLHTTP");
            } catch(e) {
                // try an older version
                try{
                    req = new ActiveXObject("Microsoft.XMLHTTP");
                } catch(e) {
                    return false;
                }
            }
        }
        req.open("GET", "delete_file.php?id=" + course_id + "&file=" + file_name, true);
        req.send(null);
        return req;
    }

});