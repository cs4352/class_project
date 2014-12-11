$(document).ready(function() {
    $('#create_account_open_modal_button').click(function() {
        var emailValue = $('#input_email').val();
        $('#input_email_create').val(emailValue);

        var passwordValue = $('#input_password').val();
        $('#input_password_create').val(passwordValue);

        // Account creation form submit handler.
        $('#input_password_confirm').change(function(e) {

            // Ensure that the password and verify password fields match.
            var $password = $('#input_password_create');
            var $passwordConfirm = $('#input_password_confirm');
            var passwordValue = $password.val();
            var passwordConfirmValue = $passwordConfirm.val();

            if (passwordValue !== passwordConfirmValue) {
                $password.get(0).setCustomValidity('Your passwords do not match.');
                return false;
            }
            $password.get(0).setCustomValidity('');
            return true;
        });
    });

    $('#create-account-form').submit(function(e) {
        var valid = e.target.checkValidity();

        if (!valid) {
            return false;
        }
        return true;
    });
});

loginUser = function(email, pass) {
    $('#input_email').val(email);
    $('#input_password').val(pass);
    $('#sign-in-submit').click();
};
