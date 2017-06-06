/**
 * Check mail
 *
 * @param email_address
 * @return bool
 */
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    return pattern.test(emailAddress);
}

/**
 * Set a cookie
 *
 * @param val
 * @param name
 */
function setCookie(val, name) {

    var value = getCookie(name);
    if (value != null) {
        document.cookie = name + "=" + value + ", " + val;
    } else {
        document.cookie = name + "=" + val;
    }

}

/**
 * Get cookie content
 *
 * @param name
 */
function getCookie (name) {

    var value = null;
    document.cookie.split(";").forEach(function(e) {
        var cookie = e.split("=");
        if(name == cookie[0].trim()) {
            value = cookie[1].trim();
        }
    });
    return value;

}

/**
 * Update cookie value
 *
 * @param val
 * @param name
 */
function updateCookie(val, name) {

    var value = getCookie(name);
    if(value == null) {
        return;
    }

    var newValue = "";

    var ids = value.split(", ").forEach(function(e){
        if(e != val){
            if(newValue == ""){
                newValue = e;
            }else{
                newValue += ", " + e;
            }
        }
    });

    if(newValue == ""){
        document.cookie = name+"=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    }else {
        document.cookie = name+"=" + newValue;
    }

    return newValue;
}

/**
 * Delete a cookie
 *
 * @param name
 */
function cancelCookie(name){
    document.cookie = name+"=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
}

$(document).ready(function(){

    /* Check forms */
    $('#login_form').submit(function () {
        $check = true;
        if(!$('#username_l').val()) {
            $('#username_l').css('border-color', '#cc0000');
            $check = false;
        } else {
            $('#username_l').css('border-color', '#ddd');
        }

        if(!$('#password_l').val()) {
            $('#password_l').css('border-color', '#cc0000');
            $check = false;
        } else {
            $('#password_l').css('border-color', '#ddd');
        }
        return $check;
    });

    $('#register_form').submit(function () {
        $check = true;
        
        if(!$('#name_r').val()) {
            $('#name_r').css('border-color', '#cc0000');
            $check = false;
        } else {
            $('#name_r').css('border-color', '#ddd');
        }

        if(!$('#surname_r').val()) {
            $('#surname_r').css('border-color', '#cc0000');
            $check = false;
        } else {
            $('#surname_r').css('border-color', '#ddd');
        }

        if(!$('#username_r').val()) {
            $('#username_r').css('border-color', '#cc0000');
            $check = false;
        }else {
            $('#username_r').css('border-color', '#ddd');
        }

        if(!isValidEmailAddress($('#username_r').val())) {
            $('#username_r').css('border-color', '#cc0000');
            $check = false;
        }  else {
            $('#username_r').css('border-color', '#ddd');
        }

        if(!$('#password_r').val()) {
            $('#password_r').css('border-color', '#cc0000');
            $check = false;
        }else {
            $('#password_r').css('border-color', '#ddd');
        }

        if(!$('#check_password_r').val()) {
            $('#check_password_r').css('border-color', '#cc0000');
            $check = false;
        } else {
            $('#check_password_r').css('border-color', '#ddd');
        }

        if($('#password_r').val() != $('#check_password_r').val()) {
            $('#password_r').css('border-color', '#cc0000');
            $('#check_password_r').css('border-color', '#cc0000');
            $('#form_error_r').text("Passwords do not coincide.");
            $check = false;
        }
        return $check;
    });

    $('#new_order_form').submit(function () {
        $check = true;
        if(!$('#quantity').val()) {
            $('#quantity').css('border-color', '#cc0000');
            $check = false;
        } else {
            $('#quantity').css('border-color', '#ddd');
        }

        if(!$("input[name=operation]").is(":checked")) {
            $('#message').text('Select the operation!');
            $check = false;
        } else {
            $('#message').text('');
        }
        return $check;
    });

});